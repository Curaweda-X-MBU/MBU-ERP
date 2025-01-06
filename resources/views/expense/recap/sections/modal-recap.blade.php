<div class="modal fade" id="recapExpense" tabindex="-1" role="dialog" aria-labelledby="returnPaymentLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header p-2">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position: absolute; top: 16px; right: 30px;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h5 class="modal-title text-primary" style="font-size: 1.5em;" id="konfirmasiModalLabel">Konfirmasi Rekap Biaya</h5>
                <p>Pilih untuk rekap biaya kedalam file apa</p>
                <div class="d-flex flex-column gap-3">
                    <div class="form-check mb-1">
                        <input class="form-check-input" type="radio" name="fileType" id="excelRadio" value="excel">
                        <label id="exportExcel" class="form-check-label" for="excelRadio">
                            File Excel
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="fileType" id="pdfRadio" value="pdf" checked>
                        <label id="exportPdf" class="form-check-label" for="pdfRadio">
                            File PDF
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="{{ route('expense.recap.index') }}" class="col-md-3 btn btn-outline-warning waves-effect">Batal</a>
                <button type="button" class="col-md-3 btn btn-primary d-flex justify-content-center">Download</button>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/jszip.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/pdfmake.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/vfs_fonts.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.html5.min.js') }}"></script>
