<?php

declare(strict_types=1);

$staffList = $staffList ?? [];
$statusLabels = $statusLabels ?? [];

$roleLabels = [
    'STAFF' => 'Staff',
    'OWNER' => 'Owner',
];

$aktifCount = 0;
foreach ($staffList as $row) {
    if (($row['status'] ?? '') === 'AKTIF') {
        $aktifCount++;
    }
}
$totalCount = count($staffList);
$btnPrimary = design_cn(ui_btn_primary(), 'gap-2');
$metaHtml = '';
if ($totalCount > 0) {
    $metaHtml = '<span class="' . e(design_status_badge('success')) . '">' . e((string) $aktifCount) . ' aktif</span>'
        . '<span class="' . e(design_status_badge('muted')) . '">' . e((string) $totalCount) . ' total</span>';
}
?>
<div class="<?= e(ui_page_content_shell_classes()) ?>">
    <?php
    ob_start();
    ?>
    <a href="/admin/staff/tambah" class="<?= e($btnPrimary) ?>">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        Tambah Staff
    </a>
    <?php
    $actionsHtml = (string) ob_get_clean();
    ui_page_header(
        'Manajemen Staff',
        'Owner only · Administrasi',
        'Tambah akun pegawai, atur kredensial, lalu aktifkan setelah password diserahkan.',
        $actionsHtml,
        $metaHtml !== '' ? $metaHtml : null,
    );
    ?>

    <?php if ($staffList === []): ?>
        <?php
        $variant = 'empty';
        $title = 'Belum ada akun staff';
        $description = 'Buat akun pertama agar tim dapat masuk ke dashboard operasional.';
        $ctaLabel = 'Tambah Staff';
        $ctaHref = '/admin/staff/tambah';
        $ctaClass = $btnPrimary;
        require __DIR__ . '/../../partials/ui/empty-state.php';
        ?>
    <?php else: ?>
        <div class="space-y-3">
            <?php foreach ($staffList as $staff): ?>
                <?php
                $isActive = ($staff['status'] ?? '') === 'AKTIF';
                $nama = (string) ($staff['nama'] ?? '');
                $initials = '';
                foreach (preg_split('/\s+/', trim($nama)) ?: [] as $part) {
                    if ($part !== '' && mb_strlen($initials) < 2) {
                        $initials .= mb_strtoupper(mb_substr($part, 0, 1));
                    }
                }
                if ($initials === '') {
                    $initials = '?';
                }
                ?>
                <article class="<?= e(design_cn(design_interactive('listArticle'), design_interactive('listArticleHover'))) ?>">
                    <div class="flex flex-wrap items-start gap-4">
                        <div class="<?= e(design_cn(design_icon_badge('default'), 'flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl font-heading text-sm font-semibold')) ?>" aria-hidden="true">
                            <?= e($initials) ?>
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="font-heading text-lg text-foreground truncate"><?= e($nama) ?></h2>
                                <span class="<?= e(design_status_badge('muted')) ?> !bg-primary/10 !text-primary">
                                    <?= e($roleLabels[$staff['role'] ?? ''] ?? (string) ($staff['role'] ?? '—')) ?>
                                </span>
                                <span class="inline-flex items-center rounded-lg px-2 py-0.5 text-xs font-semibold <?= $isActive ? design_status_badge('success') : design_status_badge('muted') ?>">
                                    <?= e($statusLabels[$staff['status']] ?? (string) $staff['status']) ?>
                                </span>
                            </div>
                            <dl class="mt-2 grid gap-1 text-sm text-muted-foreground sm:grid-cols-2 lg:grid-cols-3">
                                <div class="truncate">
                                    <dt class="sr-only">Email</dt>
                                    <dd><?= e((string) $staff['email']) ?></dd>
                                </div>
                                <div class="truncate">
                                    <dt class="sr-only">Username</dt>
                                    <dd>@<?= e((string) ($staff['username'] ?: '—')) ?></dd>
                                </div>
                                <div class="truncate">
                                    <dt class="sr-only">Dibuat</dt>
                                    <dd>Dibuat <?= e(date('d M Y', strtotime((string) $staff['created_at']))) ?></dd>
                                </div>
                            </dl>
                        </div>

                        <div class="flex w-full flex-wrap items-center justify-end gap-2 sm:w-auto sm:ml-auto">
                            <div class="hidden sm:flex flex-wrap items-center gap-2">
                                <a href="/admin/staff/edit?id=<?= e(urlencode((string) $staff['id'])) ?>"
                                   class="<?= e(design_cn(ui_btn_secondary(), 'px-3 py-2 text-xs')) ?>">
                                    Edit
                                </a>
                                <a href="/admin/staff/reset-password?id=<?= e(urlencode((string) $staff['id'])) ?>"
                                   class="<?= e(design_cn(ui_btn_secondary(), 'px-3 py-2 text-xs')) ?>">
                                    Reset Password
                                </a>
                                <form method="POST" action="/admin/staff/status"
                                      data-confirm="<?= e(($isActive ? 'Nonaktifkan' : 'Aktifkan') . ' akun staff "' . $nama . '"?') ?>">
                                    <?= \App\Core\Csrf::field() ?>
                                    <input type="hidden" name="id" value="<?= e((string) $staff['id']) ?>">
                                    <button type="submit"
                                            class="cursor-pointer inline-flex items-center rounded-xl border px-3 py-2 text-xs font-semibold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 <?= $isActive ? 'border-red-200 bg-red-50 text-red-700 hover:bg-red-100 focus-visible:ring-red-400' : 'border-success/30 bg-success-bg text-success hover:bg-success/10 focus-visible:ring-success' ?>">
                                        <?= $isActive ? 'Nonaktifkan' : 'Aktifkan' ?>
                                    </button>
                                </form>
                            </div>
                            <div class="sm:hidden">
                                <?php
                                $items = [
                                    [
                                        'type' => 'link',
                                        'label' => 'Edit',
                                        'href' => '/admin/staff/edit?id=' . urlencode((string) $staff['id']),
                                    ],
                                    [
                                        'type' => 'link',
                                        'label' => 'Reset Password',
                                        'href' => '/admin/staff/reset-password?id=' . urlencode((string) $staff['id']),
                                    ],
                                    [
                                        'type' => 'form',
                                        'label' => $isActive ? 'Nonaktifkan' : 'Aktifkan',
                                        'formAction' => '/admin/staff/status',
                                        'formFields' => ['id' => (string) $staff['id']],
                                        'confirm' => ($isActive ? 'Nonaktifkan' : 'Aktifkan') . ' akun staff "' . $nama . '"?',
                                        'class' => $isActive ? 'text-red-600' : 'text-success',
                                    ],
                                ];
                                require __DIR__ . '/../../partials/ui/action-menu.php';
                                ?>
                            </div>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <p class="text-xs text-muted-foreground px-1">
            Tip: buat akun sebagai <strong class="font-semibold text-foreground">Nonaktif</strong>, serahkan password ke staff, lalu aktifkan dari menu aksi.
        </p>
    <?php endif; ?>
</div>
