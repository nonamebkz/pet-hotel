<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\JenisNotifikasi;
use App\Enums\TipePenerima;
use App\Repositories\NotifikasiRepository;
use App\Repositories\StaffRepository;
use PDO;
use function uuid;

final class NotifikasiService
{
    public function __construct(
        private readonly NotifikasiRepository $repo = new NotifikasiRepository(),
        private readonly StaffRepository $staffRepo = new StaffRepository(),
    ) {}

    public function notifyPelanggan(
        string $pelangganId,
        JenisNotifikasi $jenis,
        string $judul,
        string $pesan,
        ?string $referensiId = null,
        ?string $referensiTipe = null,
        ?PDO $pdo = null,
    ): void {
        try {
            $this->repo->create(
                uuid(),
                $pelangganId,
                TipePenerima::PELANGGAN->value,
                $jenis->value,
                $judul,
                $pesan,
                $referensiId,
                $referensiTipe,
                $pdo,
            );
        } catch (\Throwable) {
            // Notifikasi tidak boleh menggagalkan alur bisnis utama.
        }
    }

    public function notifyStaff(
        string $staffId,
        JenisNotifikasi $jenis,
        string $judul,
        string $pesan,
        ?string $referensiId = null,
        ?string $referensiTipe = null,
        ?PDO $pdo = null,
    ): void {
        try {
            $this->repo->create(
                uuid(),
                $staffId,
                TipePenerima::STAFF->value,
                $jenis->value,
                $judul,
                $pesan,
                $referensiId,
                $referensiTipe,
                $pdo,
            );
        } catch (\Throwable) {
            // Notifikasi tidak boleh menggagalkan alur bisnis utama.
        }
    }

    public function notifyAllActiveStaff(
        JenisNotifikasi $jenis,
        string $judul,
        string $pesan,
        ?string $referensiId = null,
        ?string $referensiTipe = null,
        ?PDO $pdo = null,
    ): void {
        foreach ($this->staffRepo->findAllActiveInternal() as $staff) {
            $this->notifyStaff(
                (string) $staff['id'],
                $jenis,
                $judul,
                $pesan,
                $referensiId,
                $referensiTipe,
                $pdo,
            );
        }
    }

    public function hasNotifikasiForReferensi(
        string $penerimaId,
        TipePenerima $tipePenerima,
        JenisNotifikasi $jenis,
        string $referensiId,
    ): bool {
        return $this->repo->existsByReferensi(
            $penerimaId,
            $tipePenerima->value,
            $jenis->value,
            $referensiId,
        );
    }
}
