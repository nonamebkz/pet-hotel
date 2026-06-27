<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

final class KuotaPenitipanRepository
{
    /** @return list<string> */
    public function datesInRange(string $checkIn, string $checkOut): array
    {
        $dates = [];
        $current = new \DateTimeImmutable($checkIn);
        $end = new \DateTimeImmutable($checkOut);

        while ($current < $end) {
            $dates[] = $current->format('Y-m-d');
            $current = $current->modify('+1 day');
        }

        return $dates;
    }

    public function findById(string $id): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM kuota_penitipan WHERE id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function findByKamarAndDate(string $kamarId, string $tanggal): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM kuota_penitipan
             WHERE kamar_penitipan_id = :kamar_id AND tanggal = :tanggal
             LIMIT 1'
        );
        $stmt->execute([
            'kamar_id' => $kamarId,
            'tanggal' => $tanggal,
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function findByKamarAndDateForUpdate(
        string $kamarId,
        string $tanggal,
        PDO $pdo,
    ): ?array {
        $stmt = $pdo->prepare(
            'SELECT * FROM kuota_penitipan
             WHERE kamar_penitipan_id = :kamar_id AND tanggal = :tanggal
             LIMIT 1 FOR UPDATE'
        );
        $stmt->execute([
            'kamar_id' => $kamarId,
            'tanggal' => $tanggal,
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function findByKamarFromToday(string $kamarId, int $daysAhead = 60): array
    {
        $today = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime("+{$daysAhead} days"));

        $stmt = Database::connection()->prepare(
            'SELECT kp.*, k.nama_kamar
             FROM kuota_penitipan kp
             INNER JOIN kamar_penitipan k ON k.id = kp.kamar_penitipan_id
             WHERE kp.kamar_penitipan_id = :kamar_id
               AND kp.tanggal >= :today
               AND kp.tanggal <= :end_date
             ORDER BY kp.tanggal ASC'
        );
        $stmt->execute([
            'kamar_id' => $kamarId,
            'today' => $today,
            'end_date' => $endDate,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param array{status?: string, kamar_id?: string, tanggal?: string} $filters
     * @return list<array<string, mixed>>
     */
    public function findAllForAdmin(array $filters = []): array
    {
        $sql = 'SELECT kp.*, k.nama_kamar
                FROM kuota_penitipan kp
                INNER JOIN kamar_penitipan k ON k.id = kp.kamar_penitipan_id
                WHERE kp.tanggal >= CURDATE()';
        $params = [];

        if (!empty($filters['kamar_id'])) {
            $sql .= ' AND kp.kamar_penitipan_id = :kamar_id';
            $params['kamar_id'] = $filters['kamar_id'];
        }

        if (!empty($filters['tanggal'])) {
            $sql .= ' AND kp.tanggal = :tanggal';
            $params['tanggal'] = $filters['tanggal'];
        }

        $sql .= ' ORDER BY kp.tanggal ASC, k.nama_kamar ASC';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(
        string $id,
        string $kamarId,
        string $tanggal,
        int $slotMaksimal,
    ): void {
        $stmt = Database::connection()->prepare(
            'INSERT INTO kuota_penitipan (id, kamar_penitipan_id, tanggal, slot_maksimal, slot_terisi)
             VALUES (:id, :kamar_id, :tanggal, :slot_maksimal, 0)'
        );
        $stmt->execute([
            'id' => $id,
            'kamar_id' => $kamarId,
            'tanggal' => $tanggal,
            'slot_maksimal' => $slotMaksimal,
        ]);
    }

    public function updateSlotMaksimal(string $id, int $slotMaksimal): bool
    {
        $stmt = Database::connection()->prepare(
            'UPDATE kuota_penitipan SET slot_maksimal = :slot_maksimal WHERE id = :id'
        );
        $stmt->execute([
            'id' => $id,
            'slot_maksimal' => $slotMaksimal,
        ]);

        return $stmt->rowCount() > 0;
    }

    public function existsByKamarAndDate(string $kamarId, string $tanggal): bool
    {
        $stmt = Database::connection()->prepare(
            'SELECT COUNT(*) FROM kuota_penitipan
             WHERE kamar_penitipan_id = :kamar_id AND tanggal = :tanggal'
        );
        $stmt->execute([
            'kamar_id' => $kamarId,
            'tanggal' => $tanggal,
        ]);

        return (int) $stmt->fetchColumn() > 0;
    }

    public function incrementTerisi(string $id, PDO $pdo): void
    {
        $stmt = $pdo->prepare(
            'UPDATE kuota_penitipan SET slot_terisi = slot_terisi + 1 WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
    }

    public function decrementTerisi(string $id, PDO $pdo): void
    {
        $stmt = $pdo->prepare(
            'UPDATE kuota_penitipan SET slot_terisi = GREATEST(slot_terisi - 1, 0) WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
    }

    public function delete(string $id): bool
    {
        $stmt = Database::connection()->prepare(
            'DELETE FROM kuota_penitipan WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);

        return $stmt->rowCount() > 0;
    }
}
