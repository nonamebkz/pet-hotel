<?php

declare(strict_types=1);

use App\Controllers\Auth\PelangganAuthController;
use App\Controllers\Pelanggan\BantuanController;
use App\Controllers\Pelanggan\DashboardController;
use App\Controllers\Pelanggan\GroomingController;
use App\Controllers\Pelanggan\KucingController;
use App\Controllers\Pelanggan\PetCareController;
use App\Controllers\Pelanggan\NotifikasiController;
use App\Controllers\Pelanggan\PenitipanController;
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
$router->get('/bantuan', [BantuanController::class, 'index'], ['auth:pelanggan']);
$router->get('/notifikasi', [NotifikasiController::class, 'index'], ['auth:pelanggan']);
$router->get('/profil', [ProfilController::class, 'index'], ['auth:pelanggan']);
$router->post('/profil', [ProfilController::class, 'update'], ['auth:pelanggan']);
$router->get('/profil/geocode', [ProfilController::class, 'geocode'], ['auth:pelanggan']);
$router->get('/kucing', [KucingController::class, 'index'], ['auth:pelanggan']);
$router->get('/kucing/tambah', [KucingController::class, 'create'], ['auth:pelanggan']);
$router->post('/kucing', [KucingController::class, 'store'], ['auth:pelanggan']);
$router->get('/kucing/edit', [KucingController::class, 'edit'], ['auth:pelanggan']);
$router->post('/kucing/update', [KucingController::class, 'update'], ['auth:pelanggan']);
$router->post('/kucing/hapus', [KucingController::class, 'destroy'], ['auth:pelanggan']);
$router->get('/pet-care', [PetCareController::class, 'index'], ['auth:pelanggan']);
$router->get('/pet-care/booking', [PetCareController::class, 'showBookingForm'], ['auth:pelanggan']);
$router->post('/pet-care/booking', [PetCareController::class, 'storeBooking'], ['auth:pelanggan']);
$router->get('/pet-care/riwayat', [PetCareController::class, 'riwayat'], ['auth:pelanggan']);
$router->post('/pet-care/booking/batalkan', [PetCareController::class, 'cancelBooking'], ['auth:pelanggan']);
$router->get('/grooming', [GroomingController::class, 'index'], ['auth:pelanggan']);
$router->get('/grooming/booking', [GroomingController::class, 'showBookingForm'], ['auth:pelanggan']);
$router->get('/grooming/estimasi-pickup', [GroomingController::class, 'estimasiPickup'], ['auth:pelanggan']);
$router->post('/grooming/booking', [GroomingController::class, 'storeBooking'], ['auth:pelanggan']);
$router->get('/grooming/riwayat', [GroomingController::class, 'riwayat'], ['auth:pelanggan']);
$router->get('/grooming/detail', [GroomingController::class, 'detail'], ['auth:pelanggan']);
$router->post('/grooming/booking/batalkan', [GroomingController::class, 'cancelBooking'], ['auth:pelanggan']);
$router->get('/grooming/pembayaran', [GroomingController::class, 'showPembayaran'], ['auth:pelanggan']);
$router->post('/grooming/pembayaran', [GroomingController::class, 'storePembayaran'], ['auth:pelanggan']);
$router->get('/grooming/invoice', [GroomingController::class, 'invoice'], ['auth:pelanggan']);
$router->get('/penitipan', [PenitipanController::class, 'index'], ['auth:pelanggan']);
$router->get('/penitipan/booking', [PenitipanController::class, 'showBookingForm'], ['auth:pelanggan']);
$router->get('/penitipan/estimasi-biaya', [PenitipanController::class, 'estimasiBiaya'], ['auth:pelanggan']);
$router->get('/penitipan/estimasi-pickup', [PenitipanController::class, 'estimasiPickup'], ['auth:pelanggan']);
$router->post('/penitipan/booking', [PenitipanController::class, 'storeBooking'], ['auth:pelanggan']);
$router->get('/penitipan/riwayat', [PenitipanController::class, 'riwayat'], ['auth:pelanggan']);
$router->get('/penitipan/detail', [PenitipanController::class, 'detail'], ['auth:pelanggan']);
$router->post('/penitipan/booking/batalkan', [PenitipanController::class, 'cancelBooking'], ['auth:pelanggan']);
$router->get('/penitipan/pembayaran', [PenitipanController::class, 'showPembayaran'], ['auth:pelanggan']);
$router->post('/penitipan/pembayaran', [PenitipanController::class, 'storePembayaran'], ['auth:pelanggan']);
$router->get('/penitipan/invoice', [PenitipanController::class, 'invoice'], ['auth:pelanggan']);
$router->get('/penitipan/perpanjangan/estimasi', [PenitipanController::class, 'estimasiPerpanjangan'], ['auth:pelanggan']);
$router->post('/penitipan/perpanjangan', [PenitipanController::class, 'storePerpanjangan'], ['auth:pelanggan']);
$router->get('/penitipan/perpanjangan/pembayaran', [PenitipanController::class, 'showPembayaranPerpanjangan'], ['auth:pelanggan']);
$router->post('/penitipan/perpanjangan/pembayaran', [PenitipanController::class, 'storePembayaranPerpanjangan'], ['auth:pelanggan']);
