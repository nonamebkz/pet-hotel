<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\StatusBookingGrooming;
use App\Enums\StatusBookingPetCare;
use App\Enums\StatusPenitipan;
use App\Repositories\LaporanRepository;
use App\Repositories\LayananPetCareRepository;

final class LaporanService
{
    public function __construct(
        private readonly LaporanRepository $repo = new LaporanRepository(),
        private readonly LayananPetCareRepository $layananRepo = new LayananPetCareRepository(),
    ) {}

    /**
     * @param array{mulai?: string, akhir?: string} $input
     * @return array{
     *     mulai: string,
     *     akhir: string,
     *     ringkasan: array{grooming: int, penitipan: int, pet_care: int}
     * }
     */
    public function getIndexReport(array $input): array
    {
        $periode = $this->resolvePeriode($input);

        return [
            'mulai' => $periode['mulai'],
            'akhir' => $periode['akhir'],
            'ringkasan' => $this->repo->countRingkasan($periode['mulai'], $periode['akhir']),
        ];
    }

    /**
     * @param array{mulai?: string, akhir?: string, status?: string} $input
     * @return array{
     *     mulai: string,
     *     akhir: string,
     *     filterStatus: string,
     *     metrics: array<string, mixed>,
     *     rows: list<array<string, mixed>>,
     *     statusLabels: array<string, string>
     * }
     */
    public function getGroomingReport(array $input): array
    {
        $periode = $this->resolvePeriode($input);
        $status = $this->normalizeStatus($input['status'] ?? '', StatusBookingGrooming::labels());

        return [
            'mulai' => $periode['mulai'],
            'akhir' => $periode['akhir'],
            'filterStatus' => $status ?? '',
            'metrics' => $this->repo->aggregateGroomingMetrics(
                $periode['mulai'],
                $periode['akhir'],
                $status,
            ),
            'rows' => $this->repo->findGroomingRows(
                $periode['mulai'],
                $periode['akhir'],
                $status,
            ),
            'statusLabels' => StatusBookingGrooming::labels(),
        ];
    }

    /**
     * @param array{mulai?: string, akhir?: string, status?: string} $input
     * @return array{
     *     mulai: string,
     *     akhir: string,
     *     filterStatus: string,
     *     metrics: array<string, mixed>,
     *     rows: list<array<string, mixed>>,
     *     statusLabels: array<string, string>
     * }
     */
    public function getPenitipanReport(array $input): array
    {
        $periode = $this->resolvePeriode($input);
        $status = $this->normalizeStatus($input['status'] ?? '', StatusPenitipan::labels());

        return [
            'mulai' => $periode['mulai'],
            'akhir' => $periode['akhir'],
            'filterStatus' => $status ?? '',
            'metrics' => $this->repo->aggregatePenitipanMetrics(
                $periode['mulai'],
                $periode['akhir'],
                $status,
            ),
            'rows' => $this->repo->findPenitipanRows(
                $periode['mulai'],
                $periode['akhir'],
                $status,
            ),
            'statusLabels' => StatusPenitipan::labels(),
        ];
    }

    /**
     * @param array{mulai?: string, akhir?: string, status?: string, layanan_id?: string} $input
     * @return array{
     *     mulai: string,
     *     akhir: string,
     *     filterStatus: string,
     *     filterLayananId: string,
     *     metrics: array<string, mixed>,
     *     rows: list<array<string, mixed>>,
     *     statusLabels: array<string, string>,
     *     layananList: list<array<string, mixed>>
     * }
     */
    public function getPetCareReport(array $input): array
    {
        $periode = $this->resolvePeriode($input);
        $status = $this->normalizeStatus($input['status'] ?? '', StatusBookingPetCare::labels());
        $layananId = trim((string) ($input['layanan_id'] ?? ''));

        $layananList = $this->layananRepo->findAllIncludingDeleted();
        $validLayananIds = array_column($layananList, 'id');
        if ($layananId !== '' && !in_array($layananId, $validLayananIds, true)) {
            $layananId = '';
        }

        return [
            'mulai' => $periode['mulai'],
            'akhir' => $periode['akhir'],
            'filterStatus' => $status ?? '',
            'filterLayananId' => $layananId,
            'metrics' => $this->repo->aggregatePetCareMetrics(
                $periode['mulai'],
                $periode['akhir'],
                $status,
                $layananId !== '' ? $layananId : null,
            ),
            'rows' => $this->repo->findPetCareRows(
                $periode['mulai'],
                $periode['akhir'],
                $status,
                $layananId !== '' ? $layananId : null,
            ),
            'statusLabels' => StatusBookingPetCare::labels(),
            'layananList' => $layananList,
        ];
    }

    /**
     * @param array{mulai?: string, akhir?: string} $input
     * @return array{mulai: string, akhir: string}
     */
    private function resolvePeriode(array $input): array
    {
        $defaultMulai = date('Y-m-01');
        $defaultAkhir = date('Y-m-t');

        $mulai = trim((string) ($input['mulai'] ?? ''));
        $akhir = trim((string) ($input['akhir'] ?? ''));

        if ($mulai === '' || !$this->isValidDate($mulai)) {
            $mulai = $defaultMulai;
        }

        if ($akhir === '' || !$this->isValidDate($akhir)) {
            $akhir = $defaultAkhir;
        }

        if ($mulai > $akhir) {
            [$mulai, $akhir] = [$akhir, $mulai];
        }

        return [
            'mulai' => $mulai,
            'akhir' => $akhir,
        ];
    }

    /**
     * @param array<string, string> $validLabels
     */
    private function normalizeStatus(string $status, array $validLabels): ?string
    {
        $status = trim($status);

        if ($status === '') {
            return null;
        }

        return array_key_exists($status, $validLabels) ? $status : null;
    }

    private function isValidDate(string $date): bool
    {
        $parsed = \DateTimeImmutable::createFromFormat('Y-m-d', $date);

        return $parsed !== false && $parsed->format('Y-m-d') === $date;
    }
}
