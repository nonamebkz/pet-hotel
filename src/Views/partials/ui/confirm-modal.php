<div id="ui-confirm-modal"
     class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
     role="dialog"
     aria-modal="true"
     aria-labelledby="ui-confirm-title">
    <div class="<?= e(design_cn(design_surface('metric'), 'max-w-md w-full p-6 font-body')) ?>">
        <h3 id="ui-confirm-title" class="font-heading text-lg text-foreground mb-2">Konfirmasi</h3>
        <p id="ui-confirm-message" class="text-sm text-muted-foreground mb-6"></p>
        <div class="flex justify-end gap-3">
            <button type="button"
                    id="ui-confirm-cancel"
                    class="<?= e(ui_btn_secondary()) ?>">
                Batal
            </button>
            <button type="button"
                    id="ui-confirm-ok"
                    class="<?= e(ui_btn_destructive()) ?>">
                Ya, Lanjutkan
            </button>
        </div>
    </div>
</div>
