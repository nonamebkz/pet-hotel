<?php

declare(strict_types=1);

use App\Core\Csrf;
use App\Enums\StaffRole;
use App\Enums\StatusAkun;

$staff = $staff ?? null;
$errors = $errors ?? [];
$statusLabels = $statusLabels ?? StatusAkun::labels();
$action = $action ?? '';
$submitLabel = $submitLabel ?? 'Simpan';
$isEdit = $staff !== null && !empty($staff['id']);
$defaultStatus = (string) ($staff['status'] ?? old('status', StatusAkun::NONAKTIF->value));

$inputClass = static function (string $field, array $errors): string {
    $base = design_cn(ui_form_input_class(), 'py-3');
    if (!empty($errors[$field])) {
        return $base . ' border-destructive focus:border-destructive focus:ring-destructive/25';
    }

    return $base;
};

$startStep = 0;
if (!$isEdit && $errors !== []) {
    if (!empty($errors['password']) || !empty($errors['password_confirmation'])) {
        $startStep = 1;
    } elseif (!empty($errors['status']) || !empty($errors['role'])) {
        $startStep = 2;
    }
}
?>
<div class="<?= e(design_cn(ui_page_content_shell_classes(), design_page_layout('formSm'))) ?>">
    <?php
    ui_breadcrumb([
        ['label' => 'Manajemen Staff', 'href' => '/admin/staff'],
        ['label' => $isEdit ? 'Edit Staff' : 'Tambah Staff'],
    ]);
    ui_page_header(
        $isEdit ? 'Edit data staff' : 'Tambah akun staff',
        'Staff · Administrasi',
        $isEdit ? 'Perbarui profil dan hak akses pegawai.' : 'Buat akun baru; disarankan status Nonaktif sampai password diserahkan.',
    );
    ?>

    <?php if (!empty($errors['general'])): ?>
        <div class="<?= e(ui_form_error_alert_class()) ?>" role="alert">
            <svg class="mt-0.5 h-5 w-5 shrink-0 text-red-500" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
            </svg>
            <span><?= e((string) $errors['general']) ?></span>
        </div>
    <?php endif; ?>

    <?php if ($isEdit): ?>
        <form method="POST" action="<?= e($action) ?>" class="space-y-5" data-loading-submit>
            <?= Csrf::field() ?>
            <input type="hidden" name="id" value="<?= e((string) ($staff['id'] ?? '')) ?>">

            <section class="<?= e(design_cn(design_surface('panel'), 'p-5 sm:p-6 space-y-5')) ?>">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary/10 text-primary">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                        </svg>
                    </span>
                    <div>
                        <h2 class="font-heading text-lg text-foreground">Identitas</h2>
                        <p class="text-xs text-muted-foreground">Nama, email, dan username login</p>
                    </div>
                </div>

                <div>
                    <label for="nama" class="mb-1.5 block text-sm font-semibold text-foreground">Nama lengkap</label>
                    <input type="text" id="nama" name="nama" required
                           value="<?= e((string) ($staff['nama'] ?? old('nama', ''))) ?>"
                           class="<?= e($inputClass('nama', $errors)) ?>">
                    <?php if (!empty($errors['nama'])): ?>
                        <p class="<?= e(ui_field_error_class()) ?>"><?= e((string) $errors['nama']) ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="email" class="mb-1.5 block text-sm font-semibold text-foreground">Email</label>
                    <input type="email" id="email" name="email" required autocomplete="email"
                           value="<?= e((string) ($staff['email'] ?? old('email', ''))) ?>"
                           class="<?= e($inputClass('email', $errors)) ?>">
                    <?php if (!empty($errors['email'])): ?>
                        <p class="<?= e(ui_field_error_class()) ?>"><?= e((string) $errors['email']) ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="username" class="mb-1.5 block text-sm font-semibold text-foreground">Username <span class="font-normal text-muted-foreground">(opsional)</span></label>
                    <input type="text" id="username" name="username" autocomplete="username"
                           value="<?= e((string) ($staff['username'] ?? old('username', ''))) ?>"
                           placeholder="Minimal 3 karakter jika diisi"
                           class="<?= e($inputClass('username', $errors)) ?>">
                    <?php if (!empty($errors['username'])): ?>
                        <p class="<?= e(ui_field_error_class()) ?>"><?= e((string) $errors['username']) ?></p>
                    <?php endif; ?>
                </div>
            </section>

            <aside class="rounded-2xl border border-border bg-primary/5 px-4 py-3.5 text-sm text-muted-foreground">
                Status akun &amp; password tidak diubah di sini.
                <a href="/admin/staff/reset-password?id=<?= e(urlencode((string) ($staff['id'] ?? ''))) ?>"
                   class="ml-1 cursor-pointer font-semibold text-primary hover:underline focus:outline-none focus-visible:underline">Reset password</a>
                atau gunakan menu aksi di daftar staff untuk aktif/nonaktif.
            </aside>

            <div class="flex flex-wrap items-center gap-3">
                <button type="submit"
                        class="cursor-pointer inline-flex items-center justify-center rounded-xl bg-primary px-5 py-3 text-sm font-semibold text-white transition transition hover:opacity-90 focus:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60">
                    <?= e($submitLabel) ?>
                </button>
                <a href="/admin/staff"
                   class="cursor-pointer inline-flex items-center justify-center rounded-xl px-4 py-3 text-sm font-semibold text-muted-foreground transition transition hover:text-primary focus:outline-none focus-visible:underline">
                    Batal
                </a>
            </div>
        </form>
    <?php else: ?>
        <form method="POST" action="<?= e($action) ?>" class="space-y-5" data-loading-submit data-stepper data-step-start="<?= e((string) $startStep) ?>">
            <?= Csrf::field() ?>
            <input type="hidden" name="role" value="<?= e(StaffRole::STAFF->value) ?>">

            <ol class="grid grid-cols-3 gap-2 rounded-2xl border bg-card p-3 sm:p-4" aria-label="Langkah penambahan staff">
                <?php
                $stepsMeta = [
                    ['Identitas', 'Nama & kontak'],
                    ['Kredensial', 'Password awal'],
                    ['Akses', 'Status akun'],
                ];
                foreach ($stepsMeta as $i => [$title, $sub]):
                ?>
                    <li class="flex flex-col items-center gap-1.5 text-center sm:flex-row sm:gap-3 sm:text-left"
                        data-step-indicator>
                        <span data-step-dot class="flex h-8 w-8 items-center justify-center rounded-xl bg-primary/10 text-primary text-sm font-semibold" aria-hidden="true">
                            <?= e((string) ($i + 1)) ?>
                        </span>
                        <span class="min-w-0">
                            <span data-step-label class="block text-xs sm:text-sm text-muted-foreground"><?= e($title) ?></span>
                            <span class="hidden sm:block text-[11px] text-muted-foreground/80"><?= e($sub) ?></span>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ol>

            <section data-step-panel class="<?= e(design_cn(design_surface('panel'), 'p-5 sm:p-6 space-y-5')) ?>">
                <div>
                    <h2 class="font-heading text-lg text-foreground">Identitas staff</h2>
                    <p class="mt-1 text-sm text-muted-foreground">Data yang dipakai untuk login dan ditampilkan di sistem.</p>
                </div>

                <div>
                    <label for="nama" class="mb-1.5 block text-sm font-semibold text-foreground">Nama lengkap</label>
                    <input type="text" id="nama" name="nama" required
                           value="<?= e((string) ($staff['nama'] ?? old('nama', ''))) ?>"
                           placeholder="Contoh: Siti Aminah"
                           class="<?= e($inputClass('nama', $errors)) ?>">
                    <?php if (!empty($errors['nama'])): ?>
                        <p class="<?= e(ui_field_error_class()) ?>"><?= e((string) $errors['nama']) ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="email" class="mb-1.5 block text-sm font-semibold text-foreground">Email</label>
                    <input type="email" id="email" name="email" required autocomplete="email"
                           value="<?= e((string) ($staff['email'] ?? old('email', ''))) ?>"
                           placeholder="staff@petshop.com"
                           class="<?= e($inputClass('email', $errors)) ?>">
                    <?php if (!empty($errors['email'])): ?>
                        <p class="<?= e(ui_field_error_class()) ?>"><?= e((string) $errors['email']) ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="username" class="mb-1.5 block text-sm font-semibold text-foreground">Username <span class="font-normal text-muted-foreground">(opsional)</span></label>
                    <input type="text" id="username" name="username" autocomplete="username"
                           value="<?= e((string) ($staff['username'] ?? old('username', ''))) ?>"
                           placeholder="Login alternatif selain email"
                           class="<?= e($inputClass('username', $errors)) ?>">
                    <?php if (!empty($errors['username'])): ?>
                        <p class="<?= e(ui_field_error_class()) ?>"><?= e((string) $errors['username']) ?></p>
                    <?php endif; ?>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
                    <a href="/admin/staff" class="cursor-pointer text-sm font-semibold text-muted-foreground hover:text-primary focus:outline-none focus-visible:underline">Batal</a>
                    <button type="button" data-step-next
                            class="cursor-pointer inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white transition transition hover:opacity-90 focus:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                        Lanjut
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                    </button>
                </div>
            </section>

            <section data-step-panel class="hidden rounded-2xl border bg-card p-5 sm:p-6 space-y-5">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="font-heading text-lg text-foreground">Password awal</h2>
                        <p class="mt-1 text-sm text-muted-foreground">Minimal 8 karakter. Bisa digenerate otomatis.</p>
                    </div>
                    <button type="button"
                            data-generate-password="staff-password"
                            data-generate-password-confirm="staff-password-confirm"
                            class="cursor-pointer inline-flex items-center gap-1.5 rounded-xl border border-border bg-background px-3 py-2 text-xs font-semibold text-primary transition transition hover:bg-muted focus:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182"/>
                        </svg>
                        Generate
                    </button>
                </div>

                <div data-password-field>
                    <label for="staff-password" class="mb-1.5 block text-sm font-semibold text-foreground">Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="staff-password" required minlength="8"
                               autocomplete="new-password"
                               data-password-strength="staff-password-meter"
                               class="<?= e($inputClass('password', $errors)) ?> pr-24">
                        <button type="button" data-password-toggle="staff-password"
                                class="absolute right-2 top-1/2 -translate-y-1/2 cursor-pointer rounded-lg px-2.5 py-1.5 text-xs font-medium text-muted-foreground transition transition hover:bg-muted hover:text-primary focus:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                            Tampilkan
                        </button>
                    </div>
                    <div id="staff-password-meter" class="mt-2.5">
                        <div class="h-1.5 overflow-hidden rounded-full bg-primary/10">
                            <div data-strength-bar class="h-full rounded-full transition-all transition" style="width: 0%"></div>
                        </div>
                        <p data-strength-label class="mt-1 text-xs text-muted-foreground"></p>
                    </div>
                    <?php if (!empty($errors['password'])): ?>
                        <p class="<?= e(ui_field_error_class()) ?>"><?= e((string) $errors['password']) ?></p>
                    <?php endif; ?>
                </div>

                <div data-password-field>
                    <label for="staff-password-confirm" class="mb-1.5 block text-sm font-semibold text-foreground">Konfirmasi password</label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="staff-password-confirm" required minlength="8"
                               autocomplete="new-password"
                               data-password-match="staff-password"
                               class="<?= e($inputClass('password_confirmation', $errors)) ?> pr-24">
                        <button type="button" data-password-toggle="staff-password-confirm"
                                class="absolute right-2 top-1/2 -translate-y-1/2 cursor-pointer rounded-lg px-2.5 py-1.5 text-xs font-medium text-muted-foreground transition transition hover:bg-muted hover:text-primary focus:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                            Tampilkan
                        </button>
                    </div>
                    <p data-match-hint class="mt-1.5 text-xs text-muted-foreground"></p>
                    <?php if (!empty($errors['password_confirmation'])): ?>
                        <p class="<?= e(ui_field_error_class()) ?>"><?= e((string) $errors['password_confirmation']) ?></p>
                    <?php endif; ?>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
                    <button type="button" data-step-prev
                            class="cursor-pointer inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold text-muted-foreground transition transition hover:text-primary focus:outline-none focus-visible:underline">
                        ← Kembali
                    </button>
                    <button type="button" data-step-next
                            class="cursor-pointer inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white transition transition hover:opacity-90 focus:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                        Lanjut
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                    </button>
                </div>
            </section>

            <section data-step-panel class="hidden rounded-2xl border bg-card p-5 sm:p-6 space-y-5">
                <div>
                    <h2 class="font-heading text-lg text-foreground">Status akses</h2>
                    <p class="mt-1 text-sm text-muted-foreground">Role tetap Staff. Pilih kapan akun boleh login.</p>
                </div>

                <div class="rounded-xl border border-border bg-muted/50 px-4 py-3">
                    <p class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Role</p>
                    <p class="mt-1 text-sm font-semibold text-primary">Staff</p>
                    <p class="mt-0.5 text-xs text-muted-foreground">Owner tidak dapat dibuat dari halaman ini.</p>
                    <?php if (!empty($errors['role'])): ?>
                        <p class="<?= e(ui_field_error_class()) ?>"><?= e((string) $errors['role']) ?></p>
                    <?php endif; ?>
                </div>

                <fieldset>
                    <legend class="mb-2.5 text-sm font-semibold text-foreground">Status akun</legend>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <?php
                        $statusOptions = [
                            StatusAkun::NONAKTIF->value => [
                                'label' => $statusLabels[StatusAkun::NONAKTIF->value] ?? 'Nonaktif',
                                'desc' => 'Direkomendasikan. Staff belum bisa login sampai diaktifkan.',
                                'badge' => 'Disarankan',
                            ],
                            StatusAkun::AKTIF->value => [
                                'label' => $statusLabels[StatusAkun::AKTIF->value] ?? 'Aktif',
                                'desc' => 'Langsung bisa login ke dashboard setelah disimpan.',
                                'badge' => null,
                            ],
                        ];
                        foreach ($statusOptions as $value => $meta):
                            $checked = $defaultStatus === $value;
                        ?>
                            <label class="relative cursor-pointer rounded-xl border p-4 transition transition has-[:checked]:border-primary has-[:checked]:bg-primary/10 <?= $checked ? 'border-primary bg-primary/10' : 'border-border bg-muted/30 hover:border-primary/30' ?>">
                                <input type="radio" name="status" value="<?= e($value) ?>" class="sr-only" <?= $checked ? 'checked' : '' ?> required>
                                <div class="flex items-start justify-between gap-2">
                                    <span class="font-semibold text-foreground"><?= e($meta['label']) ?></span>
                                    <?php if ($meta['badge']): ?>
                                        <span class="rounded-lg bg-warning-bg px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-amber-800"><?= e($meta['badge']) ?></span>
                                    <?php endif; ?>
                                </div>
                                <p class="mt-1.5 text-xs leading-relaxed text-muted-foreground"><?= e($meta['desc']) ?></p>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <?php if (!empty($errors['status'])): ?>
                        <p class="<?= e(ui_field_error_class()) ?>"><?= e((string) $errors['status']) ?></p>
                    <?php endif; ?>
                </fieldset>

                <div class="rounded-xl border border-success/20 bg-success-bg/60 px-4 py-3 text-sm text-foreground">
                    <p class="font-semibold text-success">Siap disimpan</p>
                    <p class="mt-1 text-xs text-muted-foreground">Pastikan password sudah disalin sebelum menyerahkan ke staff.</p>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
                    <button type="button" data-step-prev
                            class="cursor-pointer inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold text-muted-foreground transition transition hover:text-primary focus:outline-none focus-visible:underline">
                        ← Kembali
                    </button>
                    <button type="submit"
                            class="cursor-pointer inline-flex items-center justify-center rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white transition transition hover:opacity-90 focus:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60">
                        <?= e($submitLabel) ?>
                    </button>
                </div>
            </section>
        </form>
    <?php endif; ?>
</div>
