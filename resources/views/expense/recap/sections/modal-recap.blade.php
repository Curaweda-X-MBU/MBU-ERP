<div class="modal fade" id="{{ $modal }}Recap" tabindex="-1" role="dialog" aria-labelledby="{{ $modal }}RecapLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header p-2">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position: absolute; top: 16px; right: 30px;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h5 class="modal-title text-primary" style="font-size: 1.5em;" id="{{ $modal }}RecapLabel">Konfirmasi Rekap Biaya</h5>
                <p>Pilih bentuk file</p>
                <div class="d-flex flex-column" style="gap: 1rem">
                    <div class="custom-control custom-radio">
                        <input type="radio" name="{{ $modal }}fileType" id="{{ $modal }}excelRadio" class="custom-control-input" value="excel">
                        <label for="{{ $modal }}excelRadio" class="custom-control-label">Excel</label>
                    </div>
                    <div class="custom-control custom-radio">
                        <input type="radio" name="{{ $modal }}fileType" id="{{ $modal }}pdfRadio" class="custom-control-input" value="pdf">
                        <label for="{{ $modal }}pdfRadio" class="custom-control-label">Pdf</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="col-md-3 btn btn-primary waves-effect">Download</button>
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
