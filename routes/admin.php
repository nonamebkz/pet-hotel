<?php

declare(strict_types=1);

use App\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Controllers\Auth\StaffAuthController;
use App\Core\Router;

/** @var Router $router */

$router->get('/admin/login', [StaffAuthController::class, 'showLogin'], ['guest']);
$router->post('/admin/login', [StaffAuthController::class, 'login'], ['guest']);
$router->post('/admin/logout', [StaffAuthController::class, 'logout'], ['auth:staff']);
$router->get('/admin/forgot-password', [StaffAuthController::class, 'showForgotPassword'], ['guest']);
$router->post('/admin/forgot-password', [StaffAuthController::class, 'forgotPassword'], ['guest']);
$router->get('/admin/reset-password', [StaffAuthController::class, 'showResetPassword'], ['guest']);
$router->post('/admin/reset-password', [StaffAuthController::class, 'resetPassword'], ['guest']);
$router->get('/admin/change-password', [StaffAuthController::class, 'showChangePassword'], ['auth:staff']);
$router->post('/admin/change-password', [StaffAuthController::class, 'changePassword'], ['auth:staff']);
$router->get('/admin/dashboard', [AdminDashboardController::class, 'index'], ['auth:staff']);
$router->get('/admin/staff', [AdminDashboardController::class, 'staffPlaceholder'], ['auth:staff', 'role:owner']);
