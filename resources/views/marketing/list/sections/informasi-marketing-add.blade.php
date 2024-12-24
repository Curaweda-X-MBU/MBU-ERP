@php
    $dataMarketing = '';
    $dataCustomer = '';
    if (isset($data)) {
        $dataMarketing = $data;
        $dataCustomer = $data->customer;
    }
@endphp

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
        <select name="customer_id" id="customer_id" class="form-control" {{ @$is_realization || @$is_return ? 'disabled' : 'required' }}>
        </select>
    </div>
    <!-- Tanggal Penjualan -->
    <div class="col-md-2 mt-1">
        <label for="sold_at" class="form-label">Tanggal Penjualan<i class="text-danger">*</i></label>
        @if(@$is_return)
            <input name="sold_at" id="sold_at" class="form-control" readonly>
        @else
            <input name="sold_at" id="sold_at" class="form-control flatpickr-basic" aria-desribedby="sold_at" placeholder="Pilih Tanggal" value="{{ now()->format('d-M-Y') }}">
        @endif
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
            @if (@$is_realization || @$is_return || @$is_edit)
                <input type="text" id="fileName" placeholder="Upload" class="form-control" tabindex="-1" value="{{ $data->doc_reference }}">
            @else
                <input type="text" id="fileName" placeholder="Upload" class="form-control" tabindex="-1">
            @endif
                <input type="file" id="transparentFileUpload" name="doc_reference">
            <div class="input-group-append">
                <span class="input-group-text"> <i data-feather="upload"></i> </span>
            </div>
        </div>
    </div>
    <!-- Tanggal Realisasi -->
    @if (@$is_realization)
    <div class="col-md-2 mt-1">
        <label for="realized_at" class="form-label">Tanggal Realisasi<i class="text-danger">*</i></label>
        <input id="realized_at" name="realized_at" class="form-control flatpickr-basic" aria-desribedby="realized_at" placeholder="Pilih Tanggal" required>
    </div>
    @endif
    <!-- Tanggal Retur -->
    @if (@$is_return)
    <div class="col-md-2 mt-1">
        <label for="return_at" class="form-label">Tanggal Retur<i class="text-danger">*</i></label>
        <input id="return_at" name="return_at" class="form-control flatpickr-basic" aria-desribedby="realized_at" placeholder="Pilih Tanggal" required>
    </div>
    @endif
</div>

<script src="{{asset('app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script>
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

    // ? START :: EDIT VALUES
    if ('{{ $dataMarketing }}'.length && '{{ $dataCustomer }}'.length) {
        const marketing = @json($dataMarketing);
        const customer = @json($dataCustomer);
        const MARKETING_STATUS = @json(App\Constants::MARKETING_STATUS);

        // CUSTOMER
        $('#customer_id').append(`<option value="${customer.customer_id}" selected>${customer.name}</option>`).trigger('change');

        // SOLD AT
        const dateOpt = {
            day: '2-digit',
            year: 'numeric',
            month: 'short',
        };
        const soldAt = new Date(marketing.sold_at).toLocaleDateString('en-GB', dateOpt);
        $('#sold_at').val(soldAt.replace(/ /g, '-'));
        if (marketing.realized_at) {
            const realizedAt = new Date(marketing.realized_at).toLocaleDateString('en-GB', dateOpt);
            $('#realized_at').val(realizedAt.replace(/ /g, '-'));
        }

        if (marketing.marketing_return) {
            const returnAt = new Date(marketing.marketing_return.return_at).toLocaleDateString('en-GB', dateOpt);
            $('#return_at').val(returnAt.replace(/ /g, '-'));
        }

        // STATUS
        $('#marketing_status').val(MARKETING_STATUS[marketing.marketing_status]);
    }
    // ? END :: EDIT VALUES
</script>
