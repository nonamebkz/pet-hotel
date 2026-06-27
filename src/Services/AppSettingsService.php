<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\PengaturanPetshopRepository;

use function config;

final class AppSettingsService
{
    /** @var array<string, mixed>|null */
    private static ?array $cache = null;

    public function __construct(
        private readonly PengaturanPetshopRepository $repo = new PengaturanPetshopRepository(),
    ) {}

    /** @return array<string, mixed> */
    public function all(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }

        $defaults = $this->defaultsFromConfig();
        $row = $this->repo->find();

        if ($row === null) {
            self::$cache = $defaults;

            return self::$cache;
        }

        self::$cache = array_merge($defaults, [
            'petshop_lat' => (float) $row['petshop_lat'],
            'petshop_lng' => (float) $row['petshop_lng'],
            'pickup_free_radius_km' => (float) $row['pickup_free_radius_km'],
            'pickup_extra_fee_per_km' => (int) $row['pickup_extra_fee_per_km'],
            'payment_deadline_hours' => (int) $row['payment_deadline_hours'],
            'bank_name' => (string) $row['bank_name'],
            'bank_account_number' => (string) $row['bank_account_number'],
            'bank_account_name' => (string) $row['bank_account_name'],
            'promo_min_days' => (int) $row['promo_min_days'],
            'promo_discount_percent' => (int) $row['promo_discount_percent'],
            'min_vaccination_count' => (int) $row['min_vaccination_count'],
            'petshop_whatsapp' => (string) $row['petshop_whatsapp'],
        ]);

        return self::$cache;
    }

    public function get(string $key): mixed
    {
        return $this->all()[$key] ?? null;
    }

    public static function clearCache(): void
    {
        self::$cache = null;
    }

    /** @return array<string, mixed> */
    private function defaultsFromConfig(): array
    {
        $app = config('app');

        return [
            'petshop_lat' => (float) $app['petshop_lat'],
            'petshop_lng' => (float) $app['petshop_lng'],
            'pickup_free_radius_km' => (float) $app['pickup_free_radius_km'],
            'pickup_extra_fee_per_km' => (int) $app['pickup_extra_fee_per_km'],
            'payment_deadline_hours' => (int) $app['payment_deadline_hours'],
            'bank_name' => (string) $app['bank_name'],
            'bank_account_number' => (string) $app['bank_account_number'],
            'bank_account_name' => (string) $app['bank_account_name'],
            'promo_min_days' => (int) $app['promo_min_days'],
            'promo_discount_percent' => (int) $app['promo_discount_percent'],
            'min_vaccination_count' => (int) $app['min_vaccination_count'],
            'petshop_whatsapp' => (string) $app['petshop_whatsapp'],
        ];
    }
}
