<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    /** @var array<string, array<int, array{path: string, handler: callable|array, middleware: array<string>}>> */
    private array $routes = [];

    /** @var array<string, class-string<MiddlewareInterface>> */
    private array $middlewareMap = [];

    public function registerMiddleware(string $alias, string $class): void
    {
        $this->middlewareMap[$alias] = $class;
    }

    public function get(string $path, callable|array $handler, array $middleware = []): void
    {
        $this->addRoute('GET', $path, $handler, $middleware);
    }

    public function post(string $path, callable|array $handler, array $middleware = []): void
    {
        $this->addRoute('POST', $path, $handler, $middleware);
    }

    private function addRoute(string $method, string $path, callable|array $handler, array $middleware): void
    {
        $path = rtrim($path, '/') ?: '/';
        $this->routes[$method][] = [
            'path' => $path,
            'handler' => $handler,
            'middleware' => $middleware,
        ];
    }

    public function dispatch(Request $request): Response
    {
        $method = $request->method();
        $path = $request->path();

        foreach ($this->routes[$method] ?? [] as $route) {
            if ($route['path'] !== $path) {
                continue;
            }

            $handler = $this->resolveHandler($route['handler']);
            $pipeline = $this->buildPipeline($route['middleware'], $handler);

            return $pipeline($request);
        }

        return Response::notFound();
    }

    /** @param callable|array{class-string, string} $handler */
    private function resolveHandler(callable|array $handler): callable
    {
        if (is_callable($handler)) {
            return $handler;
        }

        [$class, $method] = $handler;

        return fn (Request $request) => (new $class())->$method($request);
    }

    /** @param array<string> $middlewareAliases */
    private function buildPipeline(array $middlewareAliases, callable $handler): callable
    {
        $next = $handler;

        foreach (array_reverse($middlewareAliases) as $alias) {
            if (!isset($this->middlewareMap[$alias])) {
                throw new \RuntimeException("Middleware not registered: $alias");
            }

            $class = $this->middlewareMap[$alias];
            $middleware = new $class();
            $inner = $next;
            $next = fn (Request $request) => $middleware->handle($request, $inner);
        }

        return $next;
    }
}
