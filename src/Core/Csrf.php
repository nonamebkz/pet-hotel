<?php

declare(strict_types=1);

namespace App\Core;

final class Csrf
{
    private const TOKEN_KEY = '_csrf_token';

    public static function ensureToken(): void
    {
        if (!Session::get(self::TOKEN_KEY)) {
            Session::set(self::TOKEN_KEY, bin2hex(random_bytes(32)));
        }
    }

    public static function token(): string
    {
        return (string) Session::get(self::TOKEN_KEY, '');
    }

    public static function field(): string
    {
        $token = e(self::token());

        return '<input type="hidden" name="_token" value="' . $token . '">';
    }

    public static function validate(?string $token): bool
    {
        $sessionToken = Session::get(self::TOKEN_KEY);

        if (!is_string($sessionToken) || !is_string($token)) {
            return false;
        }

        return hash_equals($sessionToken, $token);
    }

    public static function verifyRequest(): bool
    {
        return self::validate($_POST['_token'] ?? null);
    }
}
