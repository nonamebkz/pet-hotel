<div id="ui-confirm-modal"
     class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
     role="dialog"
     aria-modal="true"
     aria-labelledby="ui-confirm-title">
    <div class="rounded-2xl border border-white/80 bg-card shadow-soft-lg max-w-md w-full p-6 font-body">
        <h3 id="ui-confirm-title" class="font-heading text-lg text-content-primary mb-2">Konfirmasi</h3>
        <p id="ui-confirm-message" class="text-sm text-content-secondary mb-6"></p>
        <div class="flex justify-end gap-3">
            <button type="button"
                    id="ui-confirm-cancel"
                    class="cursor-pointer rounded-xl border border-border bg-page/60 px-4 py-2.5 text-sm font-semibold text-content-secondary transition duration-soft hover:bg-admin-soft hover:text-admin focus:outline-none focus-visible:ring-2 focus-visible:ring-admin">
                Batal
            </button>
            <button type="button"
                    id="ui-confirm-ok"
                    class="cursor-pointer rounded-xl bg-danger px-4 py-2.5 text-sm font-semibold text-white shadow-soft transition duration-soft hover:opacity-90 focus:outline-none focus-visible:ring-2 focus-visible:ring-danger">
                Ya, Lanjutkan
            </button>
        </div>
    </div>
</div>
