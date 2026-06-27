<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\JenisNotifikasi;
use App\Enums\TipePenerima;
use App\Repositories\NotifikasiRepository;
use PDO;
use function uuid;

final class NotifikasiService
{
    public function __construct(
        private readonly NotifikasiRepository $repo = new NotifikasiRepository(),
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
}
