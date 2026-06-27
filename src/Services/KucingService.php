<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Enums\JenisKelamin;
use App\Repositories\KucingRepository;
use App\Repositories\RiwayatVaksinRepository;
use function uuid;

final class KucingService
{
    private const MIN_VACCINATION_COUNT = 1;

    public function __construct(
        private readonly KucingRepository $kucingRepo = new KucingRepository(),
        private readonly RiwayatVaksinRepository $vaksinRepo = new RiwayatVaksinRepository(),
        private readonly FileUploadService $fileUpload = new FileUploadService(),
    ) {}

    /**
     * @param array<string, mixed> $input
     * @param array<string, mixed>|null $fotoFile
     * @param array<int, array<string, mixed>> $vaksinFiles
     * @return array{success: bool, errors?: array<string, string>, kucingId?: string}
     */
    public function create(string $pelangganId, array $input, ?array $fotoFile, array $vaksinFiles): array
    {
        $validated = $this->validateKucingInput($input);

        if ($validated['errors'] !== []) {
            return ['success' => false, 'errors' => $validated['errors']];
        }

        $vaksinResult = $this->parseVaksinInput($input, $vaksinFiles, []);

        if ($vaksinResult['errors'] !== []) {
            return ['success' => false, 'errors' => $vaksinResult['errors']];
        }

        $fotoUrl = null;

        if ($fotoFile !== null) {
            $upload = $this->fileUpload->upload($fotoFile, 'kucing');

            if (!$upload['success']) {
                return ['success' => false, 'errors' => ['foto' => $upload['error'] ?? 'Gagal mengunggah foto.']];
            }

            $fotoUrl = $upload['path'];
        }

        $kucingId = uuid();
        $pdo = Database::connection();

        try {
            $pdo->beginTransaction();

            $this->kucingRepo->create(
                $kucingId,
                $pelangganId,
                $validated['nama'],
                $validated['jenis_kelamin'],
                $validated['ras'],
                $validated['tanggal_lahir'],
                $validated['berat_badan'],
                $fotoUrl,
                $validated['catatan_kesehatan'],
            );

            $this->vaksinRepo->replaceForKucing($kucingId, $vaksinResult['entries']);

            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            $this->fileUpload->deletePublicPath($fotoUrl);

            foreach ($vaksinResult['entries'] as $entry) {
                $this->fileUpload->deletePublicPath($entry['sertifikat_url'] ?? null);
            }

            throw $e;
        }

        return ['success' => true, 'kucingId' => $kucingId];
    }

    /**
     * @param array<string, mixed> $input
     * @param array<string, mixed>|null $fotoFile
     * @param array<int, array<string, mixed>> $vaksinFiles
     * @return array{success: bool, errors?: array<string, string>}
     */
    public function update(
        string $kucingId,
        string $pelangganId,
        array $input,
        ?array $fotoFile,
        array $vaksinFiles,
    ): array {
        $kucing = $this->kucingRepo->findByIdAndPelanggan($kucingId, $pelangganId);

        if (!$kucing) {
            return ['success' => false, 'errors' => ['general' => 'Kucing tidak ditemukan.']];
        }

        $validated = $this->validateKucingInput($input);

        if ($validated['errors'] !== []) {
            return ['success' => false, 'errors' => $validated['errors']];
        }

        $existingVaksin = $this->vaksinRepo->findByKucingId($kucingId);
        $vaksinResult = $this->parseVaksinInput($input, $vaksinFiles, $existingVaksin);

        if ($vaksinResult['errors'] !== []) {
            return ['success' => false, 'errors' => $vaksinResult['errors']];
        }

        $fotoUrl = $kucing['foto_url'] ?? null;
        $oldFotoUrl = $fotoUrl;

        if ($fotoFile !== null) {
            $upload = $this->fileUpload->upload($fotoFile, 'kucing');

            if (!$upload['success']) {
                return ['success' => false, 'errors' => ['foto' => $upload['error'] ?? 'Gagal mengunggah foto.']];
            }

            $fotoUrl = $upload['path'];
        }

        $pdo = Database::connection();
        $oldSertifikatUrls = $this->vaksinRepo->findSertifikatUrlsByKucingId($kucingId);

        try {
            $pdo->beginTransaction();

            $this->kucingRepo->update(
                $kucingId,
                $pelangganId,
                $validated['nama'],
                $validated['jenis_kelamin'],
                $validated['ras'],
                $validated['tanggal_lahir'],
                $validated['berat_badan'],
                $fotoUrl,
                $validated['catatan_kesehatan'],
            );

            $this->vaksinRepo->replaceForKucing($kucingId, $vaksinResult['entries']);

            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();

            if ($fotoUrl !== $oldFotoUrl) {
                $this->fileUpload->deletePublicPath($fotoUrl);
            }

            foreach ($vaksinResult['entries'] as $entry) {
                if (!in_array($entry['sertifikat_url'] ?? null, $oldSertifikatUrls, true)) {
                    $this->fileUpload->deletePublicPath($entry['sertifikat_url'] ?? null);
                }
            }

            throw $e;
        }

        if ($fotoUrl !== $oldFotoUrl) {
            $this->fileUpload->deletePublicPath($oldFotoUrl);
        }

        $newSertifikatUrls = array_filter(array_column($vaksinResult['entries'], 'sertifikat_url'));

        foreach ($oldSertifikatUrls as $oldUrl) {
            if (!in_array($oldUrl, $newSertifikatUrls, true)) {
                $this->fileUpload->deletePublicPath($oldUrl);
            }
        }

        return ['success' => true];
    }

    /** @return array{success: bool, error?: string} */
    public function delete(string $kucingId, string $pelangganId): array
    {
        $kucing = $this->kucingRepo->findByIdAndPelanggan($kucingId, $pelangganId);

        if (!$kucing) {
            return ['success' => false, 'error' => 'Kucing tidak ditemukan.'];
        }

        if ($this->kucingRepo->hasActiveBooking($kucingId)) {
            return ['success' => false, 'error' => 'Penghapusan ditolak: masih ada booking aktif pada kucing ini.'];
        }

        $sertifikatUrls = $this->vaksinRepo->findSertifikatUrlsByKucingId($kucingId);
        $fotoUrl = $kucing['foto_url'] ?? null;

        if (!$this->kucingRepo->delete($kucingId, $pelangganId)) {
            return ['success' => false, 'error' => 'Gagal menghapus kucing.'];
        }

        $this->fileUpload->deletePublicPath($fotoUrl);

        foreach ($sertifikatUrls as $url) {
            $this->fileUpload->deletePublicPath($url);
        }

        return ['success' => true];
    }

    public function isEligiblePetHotel(string $kucingId): bool
    {
        return $this->vaksinRepo->countLengkapByKucingId($kucingId) >= self::MIN_VACCINATION_COUNT;
    }

    /**
     * @param array<string, mixed> $input
     * @return array{
     *   errors: array<string, string>,
     *   nama?: string,
     *   jenis_kelamin?: string,
     *   ras?: string|null,
     *   tanggal_lahir?: string|null,
     *   berat_badan?: float|null,
     *   catatan_kesehatan?: string|null
     * }
     */
    private function validateKucingInput(array $input): array
    {
        $errors = [];
        $nama = trim((string) ($input['nama'] ?? ''));

        if ($nama === '') {
            $errors['nama'] = 'Nama kucing wajib diisi.';
        }

        $jenisKelamin = (string) ($input['jenis_kelamin'] ?? '');

        if (!in_array($jenisKelamin, [JenisKelamin::JANTAN->value, JenisKelamin::BETINA->value], true)) {
            $errors['jenis_kelamin'] = 'Jenis kelamin wajib dipilih.';
        }

        $ras = trim((string) ($input['ras'] ?? ''));
        $tanggalLahir = trim((string) ($input['tanggal_lahir'] ?? ''));
        $beratBadanRaw = trim((string) ($input['berat_badan'] ?? ''));
        $catatan = trim((string) ($input['catatan_kesehatan'] ?? ''));

        $beratBadan = null;

        if ($beratBadanRaw !== '') {
            if (!is_numeric($beratBadanRaw) || (float) $beratBadanRaw <= 0) {
                $errors['berat_badan'] = 'Berat badan harus angka positif.';
            } else {
                $beratBadan = (float) $beratBadanRaw;
            }
        }

        if ($tanggalLahir !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggalLahir)) {
            $errors['tanggal_lahir'] = 'Format tanggal lahir tidak valid.';
        }

        return [
            'errors' => $errors,
            'nama' => $nama,
            'jenis_kelamin' => $jenisKelamin,
            'ras' => $ras !== '' ? $ras : null,
            'tanggal_lahir' => $tanggalLahir !== '' ? $tanggalLahir : null,
            'berat_badan' => $beratBadan,
            'catatan_kesehatan' => $catatan !== '' ? $catatan : null,
        ];
    }

    /**
     * @param array<string, mixed> $input
     * @param array<int, array<string, mixed>> $vaksinFiles
     * @param list<array<string, mixed>> $existingVaksin
     * @return array{
     *   errors: array<string, string>,
     *   entries: list<array{id: string, jenis_vaksin: string, tanggal_vaksin: string, sertifikat_url: string|null}>
     * }
     */
    private function parseVaksinInput(array $input, array $vaksinFiles, array $existingVaksin): array
    {
        $errors = [];
        $entries = [];

        $jenisList = $input['vaksin_jenis'] ?? [];
        $tanggalList = $input['vaksin_tanggal'] ?? [];
        $existingUrlList = $input['vaksin_sertifikat_existing'] ?? [];

        if (!is_array($jenisList)) {
            $jenisList = [];
        }

        if (!is_array($tanggalList)) {
            $tanggalList = [];
        }

        if (!is_array($existingUrlList)) {
            $existingUrlList = [];
        }

        $rowCount = max(count($jenisList), count($tanggalList), count($vaksinFiles), count($existingUrlList));

        for ($i = 0; $i < $rowCount; $i++) {
            $jenis = trim((string) ($jenisList[$i] ?? ''));
            $tanggal = trim((string) ($tanggalList[$i] ?? ''));
            $existingUrl = trim((string) ($existingUrlList[$i] ?? ''));
            $file = $vaksinFiles[$i] ?? null;

            if ($jenis === '' && $tanggal === '' && $file === null && $existingUrl === '') {
                continue;
            }

            if ($jenis === '' || $tanggal === '') {
                $errors["vaksin_$i"] = 'Jenis vaksin dan tanggal wajib diisi untuk setiap baris riwayat vaksin.';

                continue;
            }

            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
                $errors["vaksin_$i"] = 'Format tanggal vaksin tidak valid.';

                continue;
            }

            $sertifikatUrl = $existingUrl !== '' ? $existingUrl : null;

            if ($file !== null) {
                $upload = $this->fileUpload->upload($file, 'vaksin');

                if (!$upload['success']) {
                    $errors["vaksin_$i"] = $upload['error'] ?? 'Gagal mengunggah sertifikat.';

                    continue;
                }

                $sertifikatUrl = $upload['path'];
            }

            $entries[] = [
                'id' => uuid(),
                'jenis_vaksin' => $jenis,
                'tanggal_vaksin' => $tanggal,
                'sertifikat_url' => $sertifikatUrl,
            ];
        }

        return ['errors' => $errors, 'entries' => $entries];
    }
}
