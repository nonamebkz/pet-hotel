<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

final class InvoiceRepository
{
    public function create(string $id, string $transaksiId, string $nomorInvoice, PDO $pdo): void
    {
        $stmt = $pdo->prepare(
            'INSERT INTO invoice (id, transaksi_id, nomor_invoice) VALUES (:id, :transaksi_id, :nomor_invoice)'
        );
        $stmt->execute([
            'id' => $id,
            'transaksi_id' => $transaksiId,
            'nomor_invoice' => $nomorInvoice,
        ]);
    }

    public function findByTransaksiId(string $transaksiId): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM invoice WHERE transaksi_id = :transaksi_id LIMIT 1'
        );
        $stmt->execute(['transaksi_id' => $transaksiId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function findById(string $id): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM invoice WHERE id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function generateNomor(): string
    {
        $prefix = 'INV-' . date('Ymd') . '-';
        $stmt = Database::connection()->prepare(
            'SELECT COUNT(*) FROM invoice WHERE nomor_invoice LIKE :prefix'
        );
        $stmt->execute(['prefix' => $prefix . '%']);
        $count = (int) $stmt->fetchColumn() + 1;

        return $prefix . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }
}
