<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\KucingRepository;
use App\Repositories\PelangganRepository;
use App\Repositories\RiwayatVaksinRepository;

final class AdminPelangganService
{
    public function __construct(
        private readonly PelangganRepository $pelangganRepo = new PelangganRepository(),
        private readonly KucingRepository $kucingRepo = new KucingRepository(),
        private readonly RiwayatVaksinRepository $vaksinRepo = new RiwayatVaksinRepository(),
        private readonly PelangganProfileService $profileService = new PelangganProfileService(),
    ) {}

    /**
     * @return array{pelangganList: list<array<string, mixed>>}
     */
    public function list(?string $search): array
    {
        return [
            'pelangganList' => $this->pelangganRepo->findAllForAdmin($search),
        ];
    }

    /**
     * @return array{
     *   pelanggan: array<string, mixed>,
     *   kucingList: list<array<string, mixed>>,
     *   minVaksin: int,
     *   addressComplete: bool
     * }|null
     */
    public function detail(string $pelangganId): ?array
    {
        $pelanggan = $this->pelangganRepo->findByIdForAdmin($pelangganId);

        if ($pelanggan === null) {
            return null;
        }

        $minVaksin = (int) app_settings('min_vaccination_count');
        $kucingList = $this->kucingRepo->findAllByPelanggan($pelangganId);

        foreach ($kucingList as &$kucing) {
            $kucing['vaksin_list'] = $this->vaksinRepo->findByKucingId((string) $kucing['id']);
            $kucing['vaksin_count'] = $this->vaksinRepo->countLengkapByKucingId((string) $kucing['id']);
            $kucing['eligible_pet_hotel'] = $kucing['vaksin_count'] >= $minVaksin;
        }
        unset($kucing);

        return [
            'pelanggan' => $pelanggan,
            'kucingList' => $kucingList,
            'minVaksin' => $minVaksin,
            'addressComplete' => $this->profileService->isAddressComplete($pelanggan),
        ];
    }
}
