<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\PengaturanPetshopRepository;

final class PengaturanPetshopService
{
    public function __construct(
        private readonly PengaturanPetshopRepository $repo = new PengaturanPetshopRepository(),
    ) {}

    /**
     * @param array<string, mixed> $input
     * @return array{success: bool, errors?: array<string, string>}
     */
    public function update(array $input, string $staffId): array
    {
        $validated = $this->validateInput($input);

        if ($validated['errors'] !== []) {
            return ['success' => false, 'errors' => $validated['errors']];
        }

        $this->repo->upsert($validated['data'], $staffId);
        AppSettingsService::clearCache();

        return ['success' => true];
    }

    /**
     * @param array<string, mixed> $input
     * @return array{errors: array<string, string>, data: array<string, mixed>}
     */
    private function validateInput(array $input): array
    {
        $errors = [];

        $lat = $this->parseFloat($input['petshop_lat'] ?? null);
        $lng = $this->parseFloat($input['petshop_lng'] ?? null);

        if ($lat === null || $lat < -90 || $lat > 90) {
            $errors['petshop_lat'] = 'Latitude petshop tidak valid (-90 s/d 90).';
        }

        if ($lng === null || $lng < -180 || $lng > 180) {
            $errors['petshop_lng'] = 'Longitude petshop tidak valid (-180 s/d 180).';
        }

        $freeRadius = $this->parseFloat($input['pickup_free_radius_km'] ?? null);
        if ($freeRadius === null || $freeRadius <= 0 || $freeRadius > 50) {
            $errors['pickup_free_radius_km'] = 'Radius gratis harus antara 0.1 dan 50 km.';
        }

        $feePerKm = filter_var($input['pickup_extra_fee_per_km'] ?? null, FILTER_VALIDATE_INT);
        if ($feePerKm === false || $feePerKm < 0) {
            $errors['pickup_extra_fee_per_km'] = 'Biaya per km tambahan tidak valid.';
        }

        $deadlineHours = filter_var($input['payment_deadline_hours'] ?? null, FILTER_VALIDATE_INT);
        if ($deadlineHours === false || $deadlineHours < 1 || $deadlineHours > 168) {
            $errors['payment_deadline_hours'] = 'Batas waktu pembayaran harus 1–168 jam.';
        }

        $bankName = trim((string) ($input['bank_name'] ?? ''));
        if ($bankName === '' || mb_strlen($bankName) > 50) {
            $errors['bank_name'] = 'Nama bank wajib diisi (maks. 50 karakter).';
        }

        $bankAccount = trim((string) ($input['bank_account_number'] ?? ''));
        if ($bankAccount === '' || mb_strlen($bankAccount) > 30) {
            $errors['bank_account_number'] = 'Nomor rekening wajib diisi (maks. 30 karakter).';
        }

        $bankAccountName = trim((string) ($input['bank_account_name'] ?? ''));
        if ($bankAccountName === '' || mb_strlen($bankAccountName) > 100) {
            $errors['bank_account_name'] = 'Atas nama rekening wajib diisi (maks. 100 karakter).';
        }

        $promoMinDays = filter_var($input['promo_min_days'] ?? null, FILTER_VALIDATE_INT);
        if ($promoMinDays === false || $promoMinDays < 1) {
            $errors['promo_min_days'] = 'Minimal hari promo harus ≥ 1.';
        }

        $promoDiscount = filter_var($input['promo_discount_percent'] ?? null, FILTER_VALIDATE_INT);
        if ($promoDiscount === false || $promoDiscount < 1 || $promoDiscount > 100) {
            $errors['promo_discount_percent'] = 'Diskon promo harus 1–100%.';
        }

        $minVaksin = filter_var($input['min_vaccination_count'] ?? null, FILTER_VALIDATE_INT);
        if ($minVaksin === false || $minVaksin < 0) {
            $errors['min_vaccination_count'] = 'Minimal vaksin tidak valid.';
        }

        $whatsapp = preg_replace('/\D+/', '', (string) ($input['petshop_whatsapp'] ?? '')) ?? '';
        if ($whatsapp === '' || strlen($whatsapp) < 10 || strlen($whatsapp) > 15) {
            $errors['petshop_whatsapp'] = 'Nomor WhatsApp tidak valid (10–15 digit, format 62…).';
        }

        if ($errors !== []) {
            return ['errors' => $errors, 'data' => []];
        }

        return [
            'errors' => [],
            'data' => [
                'petshop_lat' => $lat,
                'petshop_lng' => $lng,
                'pickup_free_radius_km' => $freeRadius,
                'pickup_extra_fee_per_km' => (int) $feePerKm,
                'payment_deadline_hours' => (int) $deadlineHours,
                'bank_name' => $bankName,
                'bank_account_number' => $bankAccount,
                'bank_account_name' => $bankAccountName,
                'promo_min_days' => (int) $promoMinDays,
                'promo_discount_percent' => (int) $promoDiscount,
                'min_vaccination_count' => (int) $minVaksin,
                'petshop_whatsapp' => $whatsapp,
            ],
        ];
    }

    private function parseFloat(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!is_numeric($value)) {
            return null;
        }

        return (float) $value;
    }
}
