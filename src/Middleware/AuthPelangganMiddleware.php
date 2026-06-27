<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\MiddlewareInterface;
use App\Core\Request;
use App\Core\Response;
use App\Services\AuthService;

final class AuthPelangganMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next): Response
    {
        $auth = new AuthService();

        if ($auth->isStaffLoggedIn()) {
            return Response::redirect('/admin/dashboard');
        }

        if (!$auth->isPelangganLoggedIn()) {
            return Response::redirect('/login');
        }

        return $next($request);
    }
}
