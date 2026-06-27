<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

final class MonitoringPenitipanRepository
{
    public function create(
        string $id,
        string $bookingId,
        string $staffId,
        string $tanggal,
        ?string $fotoUrl,
        ?string $catatanMakan,
        ?string $kondisi,
        ?string $aktivitasHarian,
    ): void {
        $stmt = Database::connection()->prepare(
            'INSERT INTO monitoring_penitipan (
                id, booking_penitipan_id, staff_id, tanggal,
                foto_url, catatan_makan, kondisi, aktivitas_harian
             ) VALUES (
                :id, :booking_id, :staff_id, :tanggal,
                :foto_url, :catatan_makan, :kondisi, :aktivitas_harian
             )'
        );
        $stmt->execute([
            'id' => $id,
            'booking_id' => $bookingId,
            'staff_id' => $staffId,
            'tanggal' => $tanggal,
            'foto_url' => $fotoUrl,
            'catatan_makan' => $catatanMakan,
            'kondisi' => $kondisi,
            'aktivitas_harian' => $aktivitasHarian,
        ]);
    }

    /** @return list<array<string, mixed>> */
    public function findByBookingId(string $bookingId): array
    {
        $stmt = Database::connection()->prepare(
            'SELECT m.*, s.nama AS staff_nama
             FROM monitoring_penitipan m
             INNER JOIN staff s ON s.id = m.staff_id
             WHERE m.booking_penitipan_id = :booking_id
             ORDER BY m.tanggal DESC, m.created_at DESC'
        );
        $stmt->execute(['booking_id' => $bookingId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
