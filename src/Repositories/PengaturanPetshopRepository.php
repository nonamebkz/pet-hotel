<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

final class PengaturanPetshopRepository
{
    private const DEFAULT_ID = '00000000-0000-4000-8000-000000000001';

    /** @return array<string, mixed>|null */
    public function find(): ?array
    {
        $stmt = Database::connection()->query(
            'SELECT * FROM pengaturan_petshop ORDER BY updated_at DESC LIMIT 1'
        );
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /**
     * @param array{
     *     petshop_lat: float,
     *     petshop_lng: float,
     *     pickup_free_radius_km: float,
     *     pickup_extra_fee_per_km: int,
     *     payment_deadline_hours: int,
     *     bank_name: string,
     *     bank_account_number: string,
     *     bank_account_name: string,
     *     promo_min_days: int,
     *     promo_discount_percent: int,
     *     min_vaccination_count: int,
     *     petshop_whatsapp: string,
     * } $data
     */
    public function upsert(array $data, string $staffId): void
    {
        $existing = $this->find();
        $id = $existing['id'] ?? self::DEFAULT_ID;

        $stmt = Database::connection()->prepare(
            'INSERT INTO pengaturan_petshop (
                id, petshop_lat, petshop_lng, pickup_free_radius_km, pickup_extra_fee_per_km,
                payment_deadline_hours, bank_name, bank_account_number, bank_account_name,
                promo_min_days, promo_discount_percent, min_vaccination_count, petshop_whatsapp,
                updated_by_staff_id
            ) VALUES (
                :id, :petshop_lat, :petshop_lng, :pickup_free_radius_km, :pickup_extra_fee_per_km,
                :payment_deadline_hours, :bank_name, :bank_account_number, :bank_account_name,
                :promo_min_days, :promo_discount_percent, :min_vaccination_count, :petshop_whatsapp,
                :updated_by_staff_id
            ) ON DUPLICATE KEY UPDATE
                petshop_lat = VALUES(petshop_lat),
                petshop_lng = VALUES(petshop_lng),
                pickup_free_radius_km = VALUES(pickup_free_radius_km),
                pickup_extra_fee_per_km = VALUES(pickup_extra_fee_per_km),
                payment_deadline_hours = VALUES(payment_deadline_hours),
                bank_name = VALUES(bank_name),
                bank_account_number = VALUES(bank_account_number),
                bank_account_name = VALUES(bank_account_name),
                promo_min_days = VALUES(promo_min_days),
                promo_discount_percent = VALUES(promo_discount_percent),
                min_vaccination_count = VALUES(min_vaccination_count),
                petshop_whatsapp = VALUES(petshop_whatsapp),
                updated_by_staff_id = VALUES(updated_by_staff_id)'
        );

        $stmt->execute([
            'id' => $id,
            'petshop_lat' => $data['petshop_lat'],
            'petshop_lng' => $data['petshop_lng'],
            'pickup_free_radius_km' => $data['pickup_free_radius_km'],
            'pickup_extra_fee_per_km' => $data['pickup_extra_fee_per_km'],
            'payment_deadline_hours' => $data['payment_deadline_hours'],
            'bank_name' => $data['bank_name'],
            'bank_account_number' => $data['bank_account_number'],
            'bank_account_name' => $data['bank_account_name'],
            'promo_min_days' => $data['promo_min_days'],
            'promo_discount_percent' => $data['promo_discount_percent'],
            'min_vaccination_count' => $data['min_vaccination_count'],
            'petshop_whatsapp' => $data['petshop_whatsapp'],
            'updated_by_staff_id' => $staffId,
        ]);
    }
}
