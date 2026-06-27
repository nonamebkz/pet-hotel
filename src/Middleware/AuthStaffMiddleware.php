<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\MiddlewareInterface;
use App\Core\Request;
use App\Core\Response;
use App\Services\AuthService;

final class AuthStaffMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next): Response
    {
        $auth = new AuthService();

        if ($auth->isPelangganLoggedIn()) {
            return Response::redirect('/dashboard');
        }

        if (!$auth->isStaffLoggedIn()) {
            return Response::redirect('/admin/login');
        }

        return $next($request);
    }
}
