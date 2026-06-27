<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Session;
use function config;

final class LoginThrottleService
{
    /** @return array{attempts: int, locked_until: ?int} */
    private function getState(string $key): array
    {
        $state = Session::get("_login_throttle.$key", ['attempts' => 0, 'locked_until' => null]);

        if (!is_array($state)) {
            return ['attempts' => 0, 'locked_until' => null];
        }

        return [
            'attempts' => (int) ($state['attempts'] ?? 0),
            'locked_until' => isset($state['locked_until']) ? (int) $state['locked_until'] : null,
        ];
    }

    public function isLocked(string $key): bool
    {
        $state = $this->getState($key);

        if ($state['locked_until'] === null) {
            return false;
        }

        if (time() >= $state['locked_until']) {
            $this->clear($key);

            return false;
        }

        return true;
    }

    public function recordFailure(string $key): void
    {
        $state = $this->getState($key);
        $state['attempts']++;

        $maxAttempts = config('app')['login_max_attempts'];

        if ($state['attempts'] >= $maxAttempts) {
            $lockoutMinutes = config('app')['login_lockout_minutes'];
            $state['locked_until'] = time() + ($lockoutMinutes * 60);
            $state['attempts'] = 0;
        }

        Session::set("_login_throttle.$key", $state);
    }

    public function clear(string $key): void
    {
        Session::forget("_login_throttle.$key");
    }
}
