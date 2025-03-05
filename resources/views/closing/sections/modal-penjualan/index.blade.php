<div class="modal fade" id="penjualanModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header p-2">
                <h5 class="modal-title text-primary" id="penjualanModalLabel" style="font-size: 1.5rem">
                    Detail Penjualan Ayam Besar | ###
                </h5>
                <button type="button" class="close mr-0" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @include('closing.sections.modal-penjualan.produk-penjualan-collapse')
                @include('closing.sections.modal-penjualan.biaya-lainnya-collapse')
            </div>
        </div>
    </div>
</div>
