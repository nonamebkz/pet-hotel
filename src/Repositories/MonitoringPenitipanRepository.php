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

    /**
     * @param list<string> $bookingIds
     * @return array<string, array{count: int, has_today: bool, last_tanggal: ?string}>
     */
    public function findSummaryByBookingIds(array $bookingIds): array
    {
        if ($bookingIds === []) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($bookingIds), '?'));
        $stmt = Database::connection()->prepare(
            "SELECT booking_penitipan_id,
                    COUNT(*) AS monitoring_count,
                    MAX(tanggal) AS last_tanggal,
                    SUM(CASE WHEN tanggal = CURDATE() THEN 1 ELSE 0 END) AS today_count
             FROM monitoring_penitipan
             WHERE booking_penitipan_id IN ({$placeholders})
             GROUP BY booking_penitipan_id"
        );
        $stmt->execute(array_values($bookingIds));

        $summary = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $bookingId = (string) $row['booking_penitipan_id'];
            $summary[$bookingId] = [
                'count' => (int) $row['monitoring_count'],
                'has_today' => (int) $row['today_count'] > 0,
                'last_tanggal' => $row['last_tanggal'] !== null ? (string) $row['last_tanggal'] : null,
            ];
        }

        return $summary;
    }

    public function existsByBookingAndDate(string $bookingId, string $tanggal): bool
    {
        $stmt = Database::connection()->prepare(
            'SELECT 1 FROM monitoring_penitipan
             WHERE booking_penitipan_id = :booking_id AND tanggal = :tanggal
             LIMIT 1'
        );
        $stmt->execute([
            'booking_id' => $bookingId,
            'tanggal' => $tanggal,
        ]);

        return (bool) $stmt->fetchColumn();
    }

    public function countBookingsMissingTodayMonitoring(): int
    {
        $stmt = Database::connection()->query(
            "SELECT COUNT(*)
             FROM booking_penitipan b
             WHERE b.status = 'SEDANG_DITITIPKAN'
               AND NOT EXISTS (
                   SELECT 1 FROM monitoring_penitipan m
                   WHERE m.booking_penitipan_id = b.id
                     AND m.tanggal = CURDATE()
               )"
        );

        return (int) $stmt->fetchColumn();
    }

    /** @return list<array<string, mixed>> */
    public function findBookingsMissingTodayMonitoringPreview(int $limit = 5): array
    {
        $stmt = Database::connection()->prepare(
            "SELECT b.id AS booking_id,
                    b.check_out,
                    k.nama AS kucing_nama,
                    pl.nama AS pelanggan_nama
             FROM booking_penitipan b
             INNER JOIN kucing k ON k.id = b.kucing_id
             INNER JOIN pelanggan pl ON pl.id = b.pelanggan_id
             WHERE b.status = 'SEDANG_DITITIPKAN'
               AND NOT EXISTS (
                   SELECT 1 FROM monitoring_penitipan m
                   WHERE m.booking_penitipan_id = b.id
                     AND m.tanggal = CURDATE()
               )
             ORDER BY b.check_out ASC, b.check_in ASC
             LIMIT :limit"
        );
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
