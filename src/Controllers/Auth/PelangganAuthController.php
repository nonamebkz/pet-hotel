<?php

declare(strict_types=1);

namespace App\Controllers\Auth;

use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Enums\UserType;
use App\Services\AuthService;
use App\Services\PasswordResetService;

final class PelangganAuthController
{
    public function __construct(
        private readonly AuthService $auth = new AuthService(),
        private readonly PasswordResetService $passwordReset = new PasswordResetService(),
    ) {}

    public function showRegister(Request $request): Response
    {
        return $this->view('auth/pelanggan/register', 'Daftar Akun');
    }

    public function register(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            return $this->backWithError('register', 'Token CSRF tidak valid.', $request);
        }

        $result = $this->auth->register(
            (string) $request->input('nama', ''),
            (string) $request->input('email', ''),
            (string) $request->input('password', ''),
            (string) $request->input('password_confirmation', ''),
        );

        if (!$result['success']) {
            Session::pullOld($request->all());

            return $this->view('auth/pelanggan/register', 'Daftar Akun', [
                'errors' => $result['errors'] ?? [],
            ]);
        }

        $this->auth->loginPelanggan(
            (string) $request->input('email', ''),
            (string) $request->input('password', ''),
            $request->ip(),
        );

        Session::flash('success', 'Akun berhasil dibuat. Selamat datang!');

        return Response::redirect('/dashboard');
    }

    public function showLogin(Request $request): Response
    {
        return $this->view('auth/pelanggan/login', 'Login Pelanggan');
    }

    public function login(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            return $this->backWithError('login', 'Token CSRF tidak valid.', $request);
        }

        $result = $this->auth->loginPelanggan(
            (string) $request->input('email', ''),
            (string) $request->input('password', ''),
            $request->ip(),
        );

        if (!$result['success']) {
            Session::pullOld($request->all());

            return $this->view('auth/pelanggan/login', 'Login Pelanggan', [
                'error' => $result['error'] ?? 'Login gagal.',
            ]);
        }

        Session::flash('success', 'Login berhasil.');

        return Response::redirect('/dashboard');
    }

    public function logout(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            return Response::redirect('/login');
        }

        $this->auth->logoutPelanggan();
        Session::regenerate();
        Session::flash('success', 'Anda telah logout.');

        return Response::redirect('/login');
    }

    public function showForgotPassword(Request $request): Response
    {
        return $this->view('auth/pelanggan/forgot-password', 'Lupa Password');
    }

    public function forgotPassword(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            return $this->backWithError('forgot-password', 'Token CSRF tidak valid.', $request);
        }

        $result = $this->passwordReset->requestReset(
            (string) $request->input('email', ''),
            UserType::PELANGGAN,
        );

        Session::flash('success', $result['message']);

        return $this->view('auth/pelanggan/forgot-password', 'Lupa Password', [
            'reset_url' => $result['reset_url'] ?? null,
        ]);
    }

    public function showResetPassword(Request $request): Response
    {
        $token = (string) ($request->input('token') ?? '');

        return $this->view('auth/pelanggan/reset-password', 'Reset Password', [
            'token' => $token,
        ]);
    }

    public function resetPassword(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            return $this->backWithError('reset-password', 'Token CSRF tidak valid.', $request);
        }

        $token = (string) $request->input('token', '');

        $result = $this->passwordReset->resetPassword(
            $token,
            (string) $request->input('password', ''),
            (string) $request->input('password_confirmation', ''),
            UserType::PELANGGAN,
        );

        if (!$result['success']) {
            return $this->view('auth/pelanggan/reset-password', 'Reset Password', [
                'token' => $token,
                'error' => $result['error'] ?? 'Reset gagal.',
            ]);
        }

        Session::flash('success', 'Password berhasil diperbarui. Silakan login.');

        return Response::redirect('/login');
    }

    public function showChangePassword(Request $request): Response
    {
        return Response::html(View::render('auth/pelanggan/change-password', [
            'title' => 'Ubah Password',
            'layout' => 'pelanggan',
        ]));
    }

    public function changePassword(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            Session::flash('error', 'Token CSRF tidak valid.');

            return Response::redirect('/change-password');
        }

        $result = $this->auth->changePasswordPelanggan(
            (string) $this->auth->currentPelangganId(),
            (string) $request->input('current_password', ''),
            (string) $request->input('password', ''),
            (string) $request->input('password_confirmation', ''),
        );

        if (!$result['success']) {
            return Response::html(View::render('auth/pelanggan/change-password', [
                'title' => 'Ubah Password',
                'layout' => 'pelanggan',
                'error' => $result['error'] ?? 'Gagal mengubah password.',
            ]));
        }

        Session::flash('success', 'Password berhasil diubah.');

        return Response::redirect('/change-password');
    }

    private function backWithError(string $view, string $message, Request $request): Response
    {
        Session::pullOld($request->all());

        return $this->view("auth/pelanggan/$view", ucfirst(str_replace('-', ' ', $view)), [
            'error' => $message,
        ]);
    }

    /** @param array<string, mixed> $data */
    private function view(string $view, string $title, array $data = []): Response
    {
        $html = View::render($view, array_merge([
            'title' => $title,
            'layout' => 'guest',
        ], $data));

        return Response::html($html);
    }
}
