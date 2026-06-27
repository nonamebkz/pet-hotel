<?php

declare(strict_types=1);

namespace App\Core;

final class Request
{
    public function __construct(
        private readonly string $method,
        private readonly string $path,
        private readonly array $query,
        private readonly array $post,
        private readonly array $server,
        private readonly array $files = [],
    ) {}

    public static function capture(): self
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $path = rtrim($uri, '/') ?: '/';

        return new self($method, $path, $_GET, $_POST, $_SERVER, $_FILES);
    }

    public function method(): string
    {
        return $this->method;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->post[$key] ?? $this->query[$key] ?? $default;
    }

    public function all(): array
    {
        return array_merge($this->query, $this->post);
    }

    public function ip(): string
    {
        return $this->server['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    public function isPost(): bool
    {
        return $this->method === 'POST';
    }

    /** @return array<string, mixed>|null */
    public function file(string $key): ?array
    {
        if (!isset($this->files[$key]) || !is_array($this->files[$key])) {
            return null;
        }

        $file = $this->files[$key];

        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        return $file;
    }

    /** @return list<array<string, mixed>|null> */
    public function files(string $key): array
    {
        if (!isset($this->files[$key]) || !is_array($this->files[$key])) {
            return [];
        }

        $group = $this->files[$key];

        if (!is_array($group['name'] ?? null)) {
            $single = $this->file($key);

            return $single ? [$single] : [];
        }

        $result = [];
        $count = count($group['name']);

        for ($i = 0; $i < $count; $i++) {
            if (($group['error'][$i] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                $result[] = null;

                continue;
            }

            $result[] = [
                'name' => $group['name'][$i],
                'type' => $group['type'][$i],
                'tmp_name' => $group['tmp_name'][$i],
                'error' => $group['error'][$i],
                'size' => $group['size'][$i],
            ];
        }

        return $result;
    }
}
