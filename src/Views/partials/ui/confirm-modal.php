<div id="ui-confirm-modal"
     class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
     role="dialog"
     aria-modal="true"
     aria-labelledby="ui-confirm-title">
    <div class="bg-white rounded-xl border shadow-xl max-w-md w-full p-6">
        <h3 id="ui-confirm-title" class="text-lg font-semibold text-gray-800 mb-2">Konfirmasi</h3>
        <p id="ui-confirm-message" class="text-sm text-gray-600 mb-6"></p>
        <div class="flex justify-end gap-3">
            <button type="button"
                    id="ui-confirm-cancel"
                    class="border border-gray-300 rounded-lg px-4 py-2 text-sm hover:bg-gray-50">
                Batal
            </button>
            <button type="button"
                    id="ui-confirm-ok"
                    class="bg-red-600 text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-red-700">
                Ya, Lanjutkan
            </button>
        </div>
    </div>
</div>
