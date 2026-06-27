<?php

declare(strict_types=1);

namespace App\Core;

use function url;

final class Response
{
    public function __construct(
        private readonly string $body,
        private readonly int $status = 200,
        private readonly array $headers = [],
    ) {}

    public static function html(string $body, int $status = 200): self
    {
        return new self($body, $status, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    public static function redirect(string $path, int $status = 302): self
    {
        return new self('', $status, ['Location' => url($path)]);
    }

    public static function forbidden(string $message = 'Akses ditolak.'): self
    {
        $body = View::render('errors/403', ['message' => $message]);

        return new self($body, 403, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    public static function notFound(): self
    {
        $body = View::render('errors/404');

        return new self($body, 404, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    /** @param array<string, mixed> $data */
    public static function json(array $data, int $status = 200): self
    {
        return new self(
            json_encode($data, JSON_UNESCAPED_UNICODE) ?: '{}',
            $status,
            ['Content-Type' => 'application/json; charset=UTF-8'],
        );
    }

    public function send(): void
    {
        http_response_code($this->status);

        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }

        echo $this->body;
    }
}
