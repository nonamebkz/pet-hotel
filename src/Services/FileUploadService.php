<?php

declare(strict_types=1);

namespace App\Services;

final class FileUploadService
{
    /** @var array<string, list<string>> */
    private const MIME_BY_CATEGORY = [
        'profil' => ['image/jpeg', 'image/png', 'image/webp'],
        'kucing' => ['image/jpeg', 'image/png', 'image/webp'],
        'vaksin' => ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'],
    ];

    /**
     * @param array<string, mixed> $file
     * @return array{success: bool, path?: string, error?: string}
     */
    public function upload(array $file, string $category): array
    {
        if (!isset(self::MIME_BY_CATEGORY[$category])) {
            return ['success' => false, 'error' => 'Kategori upload tidak valid.'];
        }

        $error = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);

        if ($error === UPLOAD_ERR_NO_FILE) {
            return ['success' => false, 'error' => 'Tidak ada file yang diunggah.'];
        }

        if ($error !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => 'Gagal mengunggah file.'];
        }

        $tmpPath = (string) ($file['tmp_name'] ?? '');

        if ($tmpPath === '' || !is_uploaded_file($tmpPath)) {
            return ['success' => false, 'error' => 'File upload tidak valid.'];
        }

        $maxBytes = (int) config('app')['upload_max_bytes'];

        if ((int) ($file['size'] ?? 0) > $maxBytes) {
            return ['success' => false, 'error' => 'Ukuran file melebihi batas maksimum (2 MB).'];
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($tmpPath) ?: '';

        if (!in_array($mime, self::MIME_BY_CATEGORY[$category], true)) {
            return ['success' => false, 'error' => 'Tipe file tidak diizinkan.'];
        }

        $extension = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'application/pdf' => 'pdf',
            default => 'bin',
        };

        $uploadDir = BASE_PATH . '/public/uploads/' . $category;

        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
            return ['success' => false, 'error' => 'Folder upload tidak tersedia.'];
        }

        $filename = bin2hex(random_bytes(16)) . '.' . $extension;
        $destination = $uploadDir . '/' . $filename;

        if (!move_uploaded_file($tmpPath, $destination)) {
            return ['success' => false, 'error' => 'Gagal menyimpan file.'];
        }

        return [
            'success' => true,
            'path' => '/uploads/' . $category . '/' . $filename,
        ];
    }

    public function deletePublicPath(?string $publicPath): void
    {
        if ($publicPath === null || $publicPath === '') {
            return;
        }

        if (!str_starts_with($publicPath, '/uploads/')) {
            return;
        }

        $fullPath = BASE_PATH . '/public' . $publicPath;

        if (is_file($fullPath)) {
            unlink($fullPath);
        }
    }
}
