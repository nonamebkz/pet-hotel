<?php

declare(strict_types=1);

use App\Core\Csrf;

$kuotaList = $kuotaList ?? [];
?>
<div>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Kuota Grooming</h1>
        <a href="/admin/grooming/kuota/tambah"
           class="bg-slate-800 text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-slate-700">
            + Tambah Kuota
        </a>
    </div>

    <?php
    $activeTab = 'kuota';
    require __DIR__ . '/../_subnav.php';
    ?>

    <?php if ($kuotaList === []): ?>
        <?php
        $variant = 'empty';
        $title = 'Belum ada kuota grooming';
        $description = 'Atur kapasitas harian agar pelanggan dapat melakukan booking grooming.';
        $ctaLabel = 'Tambah Kuota';
        $ctaHref = '/admin/grooming/kuota/tambah';
        require __DIR__ . '/../../../partials/ui/empty-state.php';
        ?>
    <?php else: ?>
        <form method="POST" action="/admin/grooming/kuota/hapus-massal" id="kuota-bulk-form"
              data-confirm="Hapus kuota terpilih yang belum terisi?">
            <?= Csrf::field() ?>

            <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                <label class="inline-flex items-center gap-2 text-sm text-gray-600">
                    <input type="checkbox" id="kuota-select-all" class="rounded border-gray-300">
                    Pilih semua yang dapat dihapus
                </label>
                <button type="submit" id="kuota-bulk-delete" disabled
                        class="text-sm border border-red-300 text-red-600 rounded-lg px-4 py-2 hover:bg-red-50 disabled:opacity-40 disabled:cursor-not-allowed">
                    Hapus Terpilih
                </button>
            </div>

            <div class="bg-white rounded-xl border overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="w-10 px-4 py-3"></th>
                            <th class="text-left px-4 py-3 font-medium text-gray-600">Tanggal</th>
                            <th class="text-left px-4 py-3 font-medium text-gray-600">Okupansi</th>
                            <th class="text-left px-4 py-3 font-medium text-gray-600">Terisi / Maks</th>
                            <th class="text-left px-4 py-3 font-medium text-gray-600">Sisa</th>
                            <th class="text-right px-4 py-3 font-medium text-gray-600 w-16">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php foreach ($kuotaList as $kuota): ?>
                            <?php
                            $maks = (int) $kuota['slot_maksimal'];
                            $terisi = (int) $kuota['slot_terisi'];
                            $sisa = $maks - $terisi;
                            $pct = $maks > 0 ? min(100, (int) round(($terisi / $maks) * 100)) : 0;
                            $barColor = $pct >= 90 ? 'bg-red-500' : ($pct >= 70 ? 'bg-amber-500' : 'bg-green-500');
                            $canDelete = $terisi === 0;
                            ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <?php if ($canDelete): ?>
                                        <input type="checkbox" name="ids[]" value="<?= e((string) $kuota['id']) ?>"
                                               class="kuota-row-check rounded border-gray-300">
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 font-medium text-gray-800">
                                    <?= e(date('d/m/Y', strtotime((string) $kuota['tanggal']))) ?>
                                </td>
                                <td class="px-4 py-3 min-w-[140px]">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                                            <div class="h-full rounded-full <?= e($barColor) ?>" style="width: <?= e((string) $pct) ?>%"></div>
                                        </div>
                                        <span class="text-xs text-gray-500 w-8 text-right"><?= e((string) $pct) ?>%</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-gray-700"><?= $terisi ?> / <?= $maks ?></td>
                                <td class="px-4 py-3">
                                    <span class="<?= $sisa === 0 ? 'text-red-600 font-medium' : 'text-gray-700' ?>">
                                        <?= $sisa ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <?php
                                    $items = [
                                        [
                                            'type' => 'link',
                                            'label' => 'Edit',
                                            'href' => '/admin/grooming/kuota/edit?id=' . urlencode((string) $kuota['id']),
                                        ],
                                    ];
                                    if ($canDelete) {
                                        $items[] = [
                                            'type' => 'form',
                                            'label' => 'Hapus',
                                            'formAction' => '/admin/grooming/kuota/hapus',
                                            'formFields' => ['id' => (string) $kuota['id']],
                                            'confirm' => 'Hapus kuota tanggal ' . date('d/m/Y', strtotime((string) $kuota['tanggal'])) . '?',
                                            'class' => 'text-red-600',
                                        ];
                                    }
                                    require __DIR__ . '/../../../partials/ui/action-menu.php';
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </form>

        <script>
        (function () {
            var selectAll = document.getElementById('kuota-select-all');
            var bulkBtn = document.getElementById('kuota-bulk-delete');
            var checks = document.querySelectorAll('.kuota-row-check');

            function updateBulkState() {
                var checked = document.querySelectorAll('.kuota-row-check:checked').length;
                if (bulkBtn) {
                    bulkBtn.disabled = checked === 0;
                }
            }

            if (selectAll) {
                selectAll.addEventListener('change', function () {
                    checks.forEach(function (cb) {
                        cb.checked = selectAll.checked;
                    });
                    updateBulkState();
                });
            }

            checks.forEach(function (cb) {
                cb.addEventListener('change', updateBulkState);
            });
        })();
        </script>
    <?php endif; ?>
</div>
