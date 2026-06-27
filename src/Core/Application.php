<?php

declare(strict_types=1);

namespace App\Core;

use App\Middleware\AuthPelangganMiddleware;
use App\Middleware\AuthStaffMiddleware;
use App\Middleware\GuestMiddleware;
use App\Middleware\RequireOwnerMiddleware;

final class Application
{
    private Router $router;

    public function __construct()
    {
        require_once __DIR__ . '/helpers.php';

        $this->router = new Router();
        $this->registerMiddleware();
        $this->loadRoutes();
    }

    public static function loadEnv(string $path): void
    {
        if (!is_file($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if ($lines === false) {
            return;
        }

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            if (!str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value, " \t\"'");

            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }

    public function run(): void
    {
        Session::start();
        Csrf::ensureToken();

        $request = Request::capture();
        $response = $this->router->dispatch($request);
        $response->send();
    }

    private function registerMiddleware(): void
    {
        $this->router->registerMiddleware('guest', GuestMiddleware::class);
        $this->router->registerMiddleware('auth:pelanggan', AuthPelangganMiddleware::class);
        $this->router->registerMiddleware('auth:staff', AuthStaffMiddleware::class);
        $this->router->registerMiddleware('role:owner', RequireOwnerMiddleware::class);
    }

    private function loadRoutes(): void
    {
        $router = $this->router;
        require BASE_PATH . '/routes/web.php';
        require BASE_PATH . '/routes/admin.php';
    }

    public function router(): Router
    {
        return $this->router;
    }
}
