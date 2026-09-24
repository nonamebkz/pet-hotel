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
?>
<div class="font-body space-y-6">
    <section class="<?= e(design_cn(design_surface('panel'), 'relative overflow-hidden p-6 sm:p-8')) ?>">
        <div class="pointer-events-none absolute -right-12 -top-12 h-40 w-40 rounded-full bg-primary/10 blur-2xl" aria-hidden="true"></div>
        <div class="relative flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-content-secondary">Owner only</p>
                <h1 class="mt-1 font-heading text-2xl sm:text-3xl text-content-primary">Manajemen Staff</h1>
                <p class="mt-2 max-w-xl text-sm text-content-secondary">
                    Tambah akun pegawai, atur kredensial, lalu aktifkan setelah password diserahkan.
                </p>
                <?php if ($totalCount > 0): ?>
                    <p class="mt-3 text-xs font-medium text-content-secondary">
                        <span class="text-admin font-semibold"><?= e((string) $aktifCount) ?></span> aktif
                        · <?= e((string) $totalCount) ?> total
                    </p>
                <?php endif; ?>
            </div>
            <a href="/admin/staff/tambah"
               class="cursor-pointer inline-flex items-center gap-2 rounded-xl bg-admin px-4 py-2.5 text-sm font-semibold text-white transition duration-soft hover:bg-admin-hover focus:outline-none focus-visible:ring-2 focus-visible:ring-admin focus-visible:ring-offset-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Tambah Staff
            </a>
        </div>
    </section>

    <?php if ($staffList === []): ?>
        <?php
        $variant = 'empty';
        $title = 'Belum ada akun staff';
        $description = 'Buat akun pertama agar tim dapat masuk ke dashboard operasional.';
        $ctaLabel = 'Tambah Staff';
        $ctaHref = '/admin/staff/tambah';
        $ctaClass = 'cursor-pointer inline-flex items-center justify-center rounded-xl bg-admin px-4 py-2.5 text-sm font-semibold text-white transition duration-soft hover:bg-admin-hover focus:outline-none focus-visible:ring-2 focus-visible:ring-admin focus-visible:ring-offset-2';
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
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-admin-soft font-heading text-sm font-semibold text-admin" aria-hidden="true">
                            <?= e($initials) ?>
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="font-heading text-lg text-content-primary truncate"><?= e($nama) ?></h2>
                                <span class="inline-flex items-center rounded-lg bg-admin-soft px-2 py-0.5 text-xs font-semibold text-admin">
                                    <?= e($roleLabels[$staff['role'] ?? ''] ?? (string) ($staff['role'] ?? '—')) ?>
                                </span>
                                <span class="inline-flex items-center rounded-lg px-2 py-0.5 text-xs font-semibold <?= $isActive ? 'bg-success-bg text-success' : 'bg-page text-content-secondary' ?>">
                                    <?= e($statusLabels[$staff['status']] ?? (string) $staff['status']) ?>
                                </span>
                            </div>
                            <dl class="mt-2 grid gap-1 text-sm text-content-secondary sm:grid-cols-2 lg:grid-cols-3">
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
                                   class="cursor-pointer inline-flex items-center rounded-xl border border-border bg-page/60 px-3 py-2 text-xs font-semibold text-admin transition duration-soft hover:bg-admin-soft focus:outline-none focus-visible:ring-2 focus-visible:ring-admin">
                                    Edit
                                </a>
                                <a href="/admin/staff/reset-password?id=<?= e(urlencode((string) $staff['id'])) ?>"
                                   class="cursor-pointer inline-flex items-center rounded-xl border border-border bg-page/60 px-3 py-2 text-xs font-semibold text-admin transition duration-soft hover:bg-admin-soft focus:outline-none focus-visible:ring-2 focus-visible:ring-admin">
                                    Reset Password
                                </a>
                                <form method="POST" action="/admin/staff/status"
                                      data-confirm="<?= e(($isActive ? 'Nonaktifkan' : 'Aktifkan') . ' akun staff "' . $nama . '"?') ?>">
                                    <?= \App\Core\Csrf::field() ?>
                                    <input type="hidden" name="id" value="<?= e((string) $staff['id']) ?>">
                                    <button type="submit"
                                            class="cursor-pointer inline-flex items-center rounded-xl border px-3 py-2 text-xs font-semibold transition duration-soft focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 <?= $isActive ? 'border-red-200 bg-red-50 text-red-700 hover:bg-red-100 focus-visible:ring-red-400' : 'border-success/30 bg-success-bg text-success hover:bg-success/10 focus-visible:ring-success' ?>">
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

        <p class="text-xs text-content-secondary px-1">
            Tip: buat akun sebagai <strong class="font-semibold text-content-primary">Nonaktif</strong>, serahkan password ke staff, lalu aktifkan dari menu aksi.
        </p>
    <?php endif; ?>
</div>
