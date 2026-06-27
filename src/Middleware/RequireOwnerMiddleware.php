<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\MiddlewareInterface;
use App\Core\Request;
use App\Core\Response;
use App\Services\AuthService;

final class RequireOwnerMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next): Response
    {
        $auth = new AuthService();

        if (!$auth->isOwner()) {
            return Response::forbidden('Halaman ini hanya dapat diakses oleh Owner.');
        }

        return $next($request);
    }
}
