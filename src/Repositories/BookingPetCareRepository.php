<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

final class BookingPetCareRepository
{
    public function create(
        string $id,
        string $pelangganId,
        string $kucingId,
        string $layananPetCareId,
        string $kuotaPetCareId,
        string $tanggal,
        string $slotWaktu,
        float $hargaLayanan,
        ?string $catatan,
        PDO $pdo,
    ): void {
        $stmt = $pdo->prepare(
            "INSERT INTO booking_pet_care (
                id, pelanggan_id, kucing_id, layanan_pet_care_id, kuota_pet_care_id,
                tanggal, slot_waktu, harga_layanan, status, catatan
             ) VALUES (
                :id, :pelanggan_id, :kucing_id, :layanan_pet_care_id, :kuota_pet_care_id,
                :tanggal, :slot_waktu, :harga_layanan, 'TERKONFIRMASI', :catatan
             )"
        );
        $stmt->execute([
            'id' => $id,
            'pelanggan_id' => $pelangganId,
            'kucing_id' => $kucingId,
            'layanan_pet_care_id' => $layananPetCareId,
            'kuota_pet_care_id' => $kuotaPetCareId,
            'tanggal' => $tanggal,
            'slot_waktu' => $slotWaktu,
            'harga_layanan' => $hargaLayanan,
            'catatan' => $catatan,
        ]);
    }

    public function findById(string $id): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM booking_pet_care WHERE id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function findByIdAndPelanggan(string $id, string $pelangganId): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM booking_pet_care WHERE id = :id AND pelanggan_id = :pelanggan_id LIMIT 1'
        );
        $stmt->execute([
            'id' => $id,
            'pelanggan_id' => $pelangganId,
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /** @return list<array<string, mixed>> */
    public function findAllByPelanggan(string $pelangganId): array
    {
        $stmt = Database::connection()->prepare(
            'SELECT b.*,
                    l.nama AS layanan_nama,
                    k.nama AS kucing_nama
             FROM booking_pet_care b
             INNER JOIN layanan_pet_care l ON l.id = b.layanan_pet_care_id
             INNER JOIN kucing k ON k.id = b.kucing_id
             WHERE b.pelanggan_id = :pelanggan_id
             ORDER BY b.tanggal DESC, b.slot_waktu DESC'
        );
        $stmt->execute(['pelanggan_id' => $pelangganId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param array{status?: string, tanggal?: string} $filters
     * @return list<array<string, mixed>>
     */
    public function findAllForAdmin(array $filters = []): array
    {
        $sql = 'SELECT b.*,
                       l.nama AS layanan_nama,
                       k.nama AS kucing_nama,
                       p.nama AS pelanggan_nama,
                       p.email AS pelanggan_email
                FROM booking_pet_care b
                INNER JOIN layanan_pet_care l ON l.id = b.layanan_pet_care_id
                INNER JOIN kucing k ON k.id = b.kucing_id
                INNER JOIN pelanggan p ON p.id = b.pelanggan_id
                WHERE 1=1';
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= ' AND b.status = :status';
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['tanggal'])) {
            $sql .= ' AND b.tanggal = :tanggal';
            $params['tanggal'] = $filters['tanggal'];
        }

        $sql .= ' ORDER BY b.tanggal DESC, b.slot_waktu DESC';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function hasActiveBookingForSlot(string $kuotaId): bool
    {
        $stmt = Database::connection()->prepare(
            "SELECT COUNT(*) FROM booking_pet_care
             WHERE kuota_pet_care_id = :kuota_id
               AND status NOT IN ('DIBATALKAN', 'SELESAI')"
        );
        $stmt->execute(['kuota_id' => $kuotaId]);

        return (int) $stmt->fetchColumn() > 0;
    }

    public function updateStatus(string $id, string $status, PDO $pdo): bool
    {
        $stmt = $pdo->prepare(
            'UPDATE booking_pet_care SET status = :status WHERE id = :id'
        );
        $stmt->execute([
            'id' => $id,
            'status' => $status,
        ]);

        return $stmt->rowCount() > 0;
    }

    public function cancel(
        string $id,
        string $dibatalkanOleh,
        ?string $staffId,
        ?string $alasan,
        PDO $pdo,
    ): bool {
        $stmt = $pdo->prepare(
            "UPDATE booking_pet_care SET
                status = 'DIBATALKAN',
                dibatalkan_oleh = :dibatalkan_oleh,
                dibatalkan_oleh_staff_id = :staff_id,
                alasan_pembatalan = :alasan,
                waktu_dibatalkan = NOW()
             WHERE id = :id"
        );
        $stmt->execute([
            'id' => $id,
            'dibatalkan_oleh' => $dibatalkanOleh,
            'staff_id' => $staffId,
            'alasan' => $alasan,
        ]);

        return $stmt->rowCount() > 0;
    }
}
