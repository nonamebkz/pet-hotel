<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

final class KuotaPetCareRepository
{
    /** @return list<array<string, mixed>> */
    public function findAvailableByDate(string $tanggal): array
    {
        $today = date('Y-m-d');
        $now = date('H:i:s');

        if ($tanggal < $today) {
            return [];
        }

        $sql = "SELECT * FROM kuota_pet_care
                WHERE tanggal = :tanggal
                  AND status_slot = 'TERSEDIA'
                  AND slot_terisi < slot_maksimal";

        if ($tanggal === $today) {
            $sql .= ' AND slot_waktu > :now';
        }

        $sql .= ' ORDER BY slot_waktu ASC';

        $stmt = Database::connection()->prepare($sql);
        $params = ['tanggal' => $tanggal];

        if ($tanggal === $today) {
            $params['now'] = $now;
        }

        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** @return list<string> */
    public function findDatesWithAvailableSlots(int $daysAhead = 30): array
    {
        $today = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime("+{$daysAhead} days"));
        $now = date('H:i:s');

        $stmt = Database::connection()->prepare(
            "SELECT DISTINCT tanggal FROM kuota_pet_care
             WHERE tanggal >= :today
               AND tanggal <= :end_date
               AND status_slot = 'TERSEDIA'
               AND slot_terisi < slot_maksimal
               AND (tanggal > :today2 OR slot_waktu > :now)
             ORDER BY tanggal ASC"
        );
        $stmt->execute([
            'today' => $today,
            'end_date' => $endDate,
            'today2' => $today,
            'now' => $now,
        ]);

        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'tanggal');
    }

    /** @return list<array<string, mixed>> */
    public function findByDate(string $tanggal): array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM kuota_pet_care WHERE tanggal = :tanggal ORDER BY slot_waktu ASC'
        );
        $stmt->execute(['tanggal' => $tanggal]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(string $id): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM kuota_pet_care WHERE id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function findByIdForUpdate(string $id, PDO $pdo): ?array
    {
        $stmt = $pdo->prepare(
            'SELECT * FROM kuota_pet_care WHERE id = :id LIMIT 1 FOR UPDATE'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function create(string $id, string $tanggal, string $slotWaktu): void
    {
        $stmt = Database::connection()->prepare(
            "INSERT INTO kuota_pet_care (id, tanggal, slot_waktu, slot_maksimal, slot_terisi, status_slot)
             VALUES (:id, :tanggal, :slot_waktu, 1, 0, 'TERSEDIA')"
        );
        $stmt->execute([
            'id' => $id,
            'tanggal' => $tanggal,
            'slot_waktu' => $slotWaktu,
        ]);
    }

    public function existsByDateAndTime(string $tanggal, string $slotWaktu): bool
    {
        $stmt = Database::connection()->prepare(
            'SELECT COUNT(*) FROM kuota_pet_care WHERE tanggal = :tanggal AND slot_waktu = :slot_waktu'
        );
        $stmt->execute([
            'tanggal' => $tanggal,
            'slot_waktu' => $slotWaktu,
        ]);

        return (int) $stmt->fetchColumn() > 0;
    }

    public function setStatus(string $id, string $statusSlot): bool
    {
        $stmt = Database::connection()->prepare(
            'UPDATE kuota_pet_care SET status_slot = :status_slot WHERE id = :id'
        );
        $stmt->execute([
            'id' => $id,
            'status_slot' => $statusSlot,
        ]);

        return $stmt->rowCount() > 0;
    }

    public function incrementTerisi(string $id, PDO $pdo): void
    {
        $stmt = $pdo->prepare(
            'UPDATE kuota_pet_care SET slot_terisi = slot_terisi + 1 WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
    }

    public function decrementTerisi(string $id, PDO $pdo): void
    {
        $stmt = $pdo->prepare(
            'UPDATE kuota_pet_care SET slot_terisi = GREATEST(slot_terisi - 1, 0) WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
    }

    public function delete(string $id): bool
    {
        $stmt = Database::connection()->prepare(
            'DELETE FROM kuota_pet_care WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);

        return $stmt->rowCount() > 0;
    }
}
