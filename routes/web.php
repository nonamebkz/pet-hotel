<?php

declare(strict_types=1);

use App\Controllers\Auth\PelangganAuthController;
use App\Controllers\Pelanggan\DashboardController;
use App\Controllers\Pelanggan\KucingController;
use App\Controllers\Pelanggan\ProfilController;
use App\Core\Request;
use App\Core\Response;
use App\Core\Router;
use App\Services\AuthService;

/** @var Router $router */

$router->get('/', function (Request $request): Response {
    $auth = new AuthService();

    if ($auth->isPelangganLoggedIn()) {
        return Response::redirect('/dashboard');
    }

    if ($auth->isStaffLoggedIn()) {
        return Response::redirect('/admin/dashboard');
    }

    return Response::redirect('/login');
});

$router->get('/register', [PelangganAuthController::class, 'showRegister'], ['guest']);
$router->post('/register', [PelangganAuthController::class, 'register'], ['guest']);
$router->get('/login', [PelangganAuthController::class, 'showLogin'], ['guest']);
$router->post('/login', [PelangganAuthController::class, 'login'], ['guest']);
$router->post('/logout', [PelangganAuthController::class, 'logout'], ['auth:pelanggan']);
$router->get('/forgot-password', [PelangganAuthController::class, 'showForgotPassword'], ['guest']);
$router->post('/forgot-password', [PelangganAuthController::class, 'forgotPassword'], ['guest']);
$router->get('/reset-password', [PelangganAuthController::class, 'showResetPassword'], ['guest']);
$router->post('/reset-password', [PelangganAuthController::class, 'resetPassword'], ['guest']);
$router->get('/change-password', [PelangganAuthController::class, 'showChangePassword'], ['auth:pelanggan']);
$router->post('/change-password', [PelangganAuthController::class, 'changePassword'], ['auth:pelanggan']);
$router->get('/dashboard', [DashboardController::class, 'index'], ['auth:pelanggan']);
$router->get('/profil', [ProfilController::class, 'index'], ['auth:pelanggan']);
$router->post('/profil', [ProfilController::class, 'update'], ['auth:pelanggan']);
$router->get('/profil/geocode', [ProfilController::class, 'geocode'], ['auth:pelanggan']);
$router->get('/kucing', [KucingController::class, 'index'], ['auth:pelanggan']);
$router->get('/kucing/tambah', [KucingController::class, 'create'], ['auth:pelanggan']);
$router->post('/kucing', [KucingController::class, 'store'], ['auth:pelanggan']);
$router->get('/kucing/edit', [KucingController::class, 'edit'], ['auth:pelanggan']);
$router->post('/kucing/update', [KucingController::class, 'update'], ['auth:pelanggan']);
$router->post('/kucing/hapus', [KucingController::class, 'destroy'], ['auth:pelanggan']);
