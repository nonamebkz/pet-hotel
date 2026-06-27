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

final class StaffAuthController
{
    public function __construct(
        private readonly AuthService $auth = new AuthService(),
        private readonly PasswordResetService $passwordReset = new PasswordResetService(),
    ) {}

    public function showLogin(Request $request): Response
    {
        return $this->guestView('auth/admin/login', 'Login Staff / Owner');
    }

    public function login(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            return $this->guestView('auth/admin/login', 'Login Staff / Owner', [
                'error' => 'Token CSRF tidak valid.',
            ]);
        }

        $result = $this->auth->loginStaff(
            (string) $request->input('identifier', ''),
            (string) $request->input('password', ''),
            $request->ip(),
        );

        if (!$result['success']) {
            Session::pullOld($request->all());

            return $this->guestView('auth/admin/login', 'Login Staff / Owner', [
                'error' => $result['error'] ?? 'Login gagal.',
            ]);
        }

        Session::flash('success', 'Login berhasil.');

        return Response::redirect('/admin/dashboard');
    }

    public function logout(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            return Response::redirect('/admin/login');
        }

        $this->auth->logoutStaff();
        Session::regenerate();
        Session::flash('success', 'Anda telah logout.');

        return Response::redirect('/admin/login');
    }

    public function showForgotPassword(Request $request): Response
    {
        return $this->guestView('auth/admin/forgot-password', 'Lupa Password');
    }

    public function forgotPassword(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            return $this->guestView('auth/admin/forgot-password', 'Lupa Password', [
                'error' => 'Token CSRF tidak valid.',
            ]);
        }

        $result = $this->passwordReset->requestReset(
            (string) $request->input('email', ''),
            UserType::STAFF,
        );

        Session::flash('success', $result['message']);

        return $this->guestView('auth/admin/forgot-password', 'Lupa Password', [
            'reset_url' => $result['reset_url'] ?? null,
        ]);
    }

    public function showResetPassword(Request $request): Response
    {
        $token = (string) ($request->input('token') ?? '');

        return $this->guestView('auth/admin/reset-password', 'Reset Password', [
            'token' => $token,
        ]);
    }

    public function resetPassword(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            return $this->guestView('auth/admin/reset-password', 'Reset Password', [
                'token' => (string) $request->input('token', ''),
                'error' => 'Token CSRF tidak valid.',
            ]);
        }

        $token = (string) $request->input('token', '');

        $result = $this->passwordReset->resetPassword(
            $token,
            (string) $request->input('password', ''),
            (string) $request->input('password_confirmation', ''),
            UserType::STAFF,
        );

        if (!$result['success']) {
            return $this->guestView('auth/admin/reset-password', 'Reset Password', [
                'token' => $token,
                'error' => $result['error'] ?? 'Reset gagal.',
            ]);
        }

        Session::flash('success', 'Password berhasil diperbarui. Silakan login.');

        return Response::redirect('/admin/login');
    }

    public function showChangePassword(Request $request): Response
    {
        return Response::html(View::render('auth/admin/change-password', [
            'title' => 'Ubah Password',
            'layout' => 'admin',
        ]));
    }

    public function changePassword(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            Session::flash('error', 'Token CSRF tidak valid.');

            return Response::redirect('/admin/change-password');
        }

        $result = $this->auth->changePasswordStaff(
            (string) $this->auth->currentStaffId(),
            (string) $request->input('current_password', ''),
            (string) $request->input('password', ''),
            (string) $request->input('password_confirmation', ''),
        );

        if (!$result['success']) {
            return Response::html(View::render('auth/admin/change-password', [
                'title' => 'Ubah Password',
                'layout' => 'admin',
                'error' => $result['error'] ?? 'Gagal mengubah password.',
            ]));
        }

        Session::flash('success', 'Password berhasil diubah.');

        return Response::redirect('/admin/change-password');
    }

    /** @param array<string, mixed> $data */
    private function guestView(string $view, string $title, array $data = []): Response
    {
        $html = View::render($view, array_merge([
            'title' => $title,
            'layout' => 'guest-admin',
        ], $data));

        return Response::html($html);
    }
}
