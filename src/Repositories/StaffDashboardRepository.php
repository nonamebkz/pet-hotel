<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Enums\JenisLayanan;
use App\Enums\StatusPembayaran;
use App\Enums\StatusPenitipan;
use PDO;

final class StaffDashboardRepository
{
    /**
     * @return array{grooming: int, penitipan: int, pet_care: int, total: int}
     */
    public function countBookingsToday(string $today): array
    {
        $pdo = Database::connection();

        $stmtGrooming = $pdo->prepare(
            'SELECT COUNT(*) FROM booking_grooming WHERE tanggal = :today'
        );
        $stmtGrooming->execute(['today' => $today]);
        $grooming = (int) $stmtGrooming->fetchColumn();

        $stmtPenitipan = $pdo->prepare(
            'SELECT COUNT(*) FROM booking_penitipan WHERE check_in = :today'
        );
        $stmtPenitipan->execute(['today' => $today]);
        $penitipan = (int) $stmtPenitipan->fetchColumn();

        $stmtPetCare = $pdo->prepare(
            'SELECT COUNT(*) FROM booking_pet_care WHERE tanggal = :today'
        );
        $stmtPetCare->execute(['today' => $today]);
        $petCare = (int) $stmtPetCare->fetchColumn();

        return [
            'grooming' => $grooming,
            'penitipan' => $penitipan,
            'pet_care' => $petCare,
            'total' => $grooming + $penitipan + $petCare,
        ];
    }

    public function countPenitipanAktif(): int
    {
        $stmt = Database::connection()->prepare(
            'SELECT COUNT(*) FROM booking_penitipan
             WHERE status IN (:check_in, :sedang_dititipkan)'
        );
        $stmt->execute([
            'check_in' => StatusPenitipan::CHECK_IN->value,
            'sedang_dititipkan' => StatusPenitipan::SEDANG_DITITIPKAN->value,
        ]);

        return (int) $stmt->fetchColumn();
    }

    public function sumVerifiedRevenue(string $mulai, string $akhir): float
    {
        $stmt = Database::connection()->prepare(
            'SELECT COALESCE(SUM(total_bayar), 0)
             FROM transaksi
             WHERE status_pembayaran = :lunas
               AND jenis_layanan IN (:grooming, :penitipan)
               AND dibayar_at >= :mulai
               AND dibayar_at < :akhir'
        );
        $stmt->execute([
            'lunas' => StatusPembayaran::LUNAS->value,
            'grooming' => JenisLayanan::GROOMING->value,
            'penitipan' => JenisLayanan::PENITIPAN->value,
            'mulai' => $mulai,
            'akhir' => $akhir,
        ]);

        return (float) $stmt->fetchColumn();
    }
}
