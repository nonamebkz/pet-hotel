<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

final class KuotaGroomingRepository
{
    /** @return list<string> */
    public function findDatesWithAvailableSlots(int $daysAhead = 30): array
    {
        $today = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime("+{$daysAhead} days"));

        $stmt = Database::connection()->prepare(
            'SELECT tanggal FROM kuota_grooming
             WHERE tanggal >= :today
               AND tanggal <= :end_date
               AND slot_terisi < slot_maksimal
             ORDER BY tanggal ASC'
        );
        $stmt->execute([
            'today' => $today,
            'end_date' => $endDate,
        ]);

        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'tanggal');
    }

    public function findByDate(string $tanggal): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM kuota_grooming WHERE tanggal = :tanggal LIMIT 1'
        );
        $stmt->execute(['tanggal' => $tanggal]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function findById(string $id): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM kuota_grooming WHERE id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function findByIdForUpdate(string $id, PDO $pdo): ?array
    {
        $stmt = $pdo->prepare(
            'SELECT * FROM kuota_grooming WHERE id = :id LIMIT 1 FOR UPDATE'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /** @return list<array<string, mixed>> */
    public function findAllFromToday(int $daysAhead = 60): array
    {
        $today = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime("+{$daysAhead} days"));

        $stmt = Database::connection()->prepare(
            'SELECT * FROM kuota_grooming
             WHERE tanggal >= :today AND tanggal <= :end_date
             ORDER BY tanggal ASC'
        );
        $stmt->execute([
            'today' => $today,
            'end_date' => $endDate,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(string $id, string $tanggal, int $slotMaksimal): void
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO kuota_grooming (id, tanggal, slot_maksimal, slot_terisi)
             VALUES (:id, :tanggal, :slot_maksimal, 0)'
        );
        $stmt->execute([
            'id' => $id,
            'tanggal' => $tanggal,
            'slot_maksimal' => $slotMaksimal,
        ]);
    }

    public function updateSlotMaksimal(string $id, int $slotMaksimal): bool
    {
        $stmt = Database::connection()->prepare(
            'UPDATE kuota_grooming SET slot_maksimal = :slot_maksimal WHERE id = :id'
        );
        $stmt->execute([
            'id' => $id,
            'slot_maksimal' => $slotMaksimal,
        ]);

        return $stmt->rowCount() > 0;
    }

    public function existsByDate(string $tanggal): bool
    {
        $stmt = Database::connection()->prepare(
            'SELECT COUNT(*) FROM kuota_grooming WHERE tanggal = :tanggal'
        );
        $stmt->execute(['tanggal' => $tanggal]);

        return (int) $stmt->fetchColumn() > 0;
    }

    public function incrementTerisi(string $id, PDO $pdo): void
    {
        $stmt = $pdo->prepare(
            'UPDATE kuota_grooming SET slot_terisi = slot_terisi + 1 WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
    }

    public function decrementTerisi(string $id, PDO $pdo): void
    {
        $stmt = $pdo->prepare(
            'UPDATE kuota_grooming SET slot_terisi = GREATEST(slot_terisi - 1, 0) WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
    }

    public function delete(string $id): bool
    {
        $stmt = Database::connection()->prepare(
            'DELETE FROM kuota_grooming WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);

        return $stmt->rowCount() > 0;
    }
}
