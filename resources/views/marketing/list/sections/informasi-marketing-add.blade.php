<style>
    #transparentFileUpload {
        opacity: 0;
        position:absolute;
        inset: 0;
    }
</style>
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">

<div class="row row-cols-2 row-cols-md-4 align-items-baseline">
    <!-- Nama Pelanggan -->
    <div class="col-md-2 mt-1">
        <label for="customer_id" class="form-label">Nama Pelanggan<i class="text-danger">*</i></label>
        <select name="customer_id" id="customer_id" class="form-control" required>
        </select>
    </div>
    <!-- Tanggal Penjualan -->
    <div class="col-md-2 mt-1">
        <label for="sold_at" class="form-label">Tanggal Penjualan<i class="text-danger">*</i></label>
        <input name="sold_at" id="sold_at" class="form-control flatpickr-basic" aria-desribedby="sold_at" placeholder="Pilih Tanggal" value="{{ now()->format('d-M-Y') }}" required>
    </div>
    <!-- Status -->
    <div class="col-md-2 mt-1">
        <label for="marketing_status" class="form-label">Status</label>
        <input id="marketing_status" value="Diajukan" name="marketing_status" type="text" class="form-control" disabled required>
    </div>
    <!-- Referensi Dokumen -->
    <div class="col-md-2 mt-1">
        <label for="doc_reference" class="form-label">Referensi Dokumen</label>
        <div class="input-group">
            <input type="text" id="fileName" placeholder="Upload" class="form-control" tabindex="-1">
            <input type="file" id="transparentFileUpload" name="doc_reference">
            <div class="input-group-append">
                <span class="input-group-text"> <i data-feather="upload"></i> </span>
            </div>
        </div>
    </div>
</div>

<script src="{{asset('app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script>
    /* NOTE :
    ----- INITIALIZATIONS -----
    */

    $(document).ready(function() {
        $('#transparentFileUpload').on('change', function() {
            $('#fileName').val($('#transparentFileUpload').val().split('\\').pop())
        })
    });

    // ? START :: FLATPICKR ::  SOLD AT
    const dateOpt = { dateFormat: 'd-M-Y' };
    $('.flatpickr-basic').flatpickr(dateOpt);
    // ? END :: FLATPICKR ::  SOLD AT

    // ? START :: SELECT2
    const customerIdRoute = '{{ route("data-master.customer.search") }}';
    initSelect2($('#customer_id'), 'Pilih Pelanggan', customerIdRoute);
    // ? END :: SELECT2
</script>
