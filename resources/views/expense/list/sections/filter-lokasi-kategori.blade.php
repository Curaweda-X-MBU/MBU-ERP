@php
$category = \App\Constants::EXPENSE_CATEGORY;
$kandangs = '';
$selected_kandang_id = [];
if (isset($data->expense_kandang) && !$data->expense_kandang->isEmpty()) {
    $kandangs = \App\Models\DataMaster\Kandang::where('location_id', $data->location_id)
        ->get()
        ->sortByDesc('project_status');
    $selected_kandang_id = $data->expense_kandang->map(fn($k) => $k->kandang_id)->toArray();
}
@endphp

<style>
.color-information {
    visibility: hidden;
}
.circle {
    display: inline-block;
    border-radius: 100%;
    height: 1em;
    width: 1em;
    margin: auto 0.5rem;
}
#transparentFileUpload {
    opacity: 0;
    position:absolute;
    inset: 0;
}
</style>

<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">

<div class="row">
    <!-- Lokasi -->
    <div class="col-md-2 mt-1">
        <label for="location_id" class="form-label">Lokasi<i class="text-danger">*</i></label>
        <select name="location_id" id="location_id" class="form-control" {{ @$data->expense_status == 1 ? 'disabled' : 'required' }}>
            @if (@$data->location_id)
            <option value="{{ @$data->location_id }}" selected>{{ @$data->location->name }}</option>
            @endif
        </select>
    </div>
    <!-- Kategori -->
    <div class="col-md-2 mt-1">
        <label for="category_id" class="form-label">Kategori<i class="text-danger">*</i></label>
        <select name="category" id="category_id" class="form-control" required {{ @$data->expense_status == 1 ? 'disabled' : '' }}>
            <option value="">Pilih Kategori</option>
            <option value="1" {{ @$data->category == array_search('Biaya Operasional', $category) ? 'selected' : '' }}>Biaya Operasional</option>
            <option value="2" {{ @$data->category == array_search('Bukan BOP', $category) ? 'selected' : '' }}>Bukan BOP</option>
        </select>
    </div>
    <div class="col-md-2 mt-1">
        <label for="category_id" class="form-label">Tanggal Transaksi<i class="text-danger">*</i></label>
        <input name="transaction_date" id="transaction_date" class="form-control flatpickr-basic" aria-desribedby="transaction_date" placeholder="Pilih Tanggal Transaksi" value="{{ now() }}" required {{ @$data->expense_status == 1 ? 'disabled' : '' }}>
    </div>
    <div class="col-md-2 mt-1">
        <label for="bill_docs">Dokumen Tagihan</label>
        <div class="input-group">
                <input type="text" id="fileName" placeholder="Upload" class="form-control" tabindex="-1" value="{{ @$data->bill_docs }}" {{ @$data->expense_status == 1 ? 'disabled' : '' }}>
                <input type="file" id="transparentFileUpload" name="bill_docs" {{ @$data->expense_status == 1 ? 'disabled' : '' }}>
            <div class="input-group-append">
                <span class="input-group-text"> <i data-feather="upload"></i></span>
            </div>
        </div>
        <span class="text-secondary" style="font-size: 0.9em">Max. 5 MB</span>
    </div>
    <!-- Color information -->
    <div class="color-information col mt-1">
        <h5 class="text-right">Keterangan Kandang</h5>
        <div class="row justify-content-end">
            <div class="m-1"><i class="circle bg-primary"></i>Dipilih</div>
            <div class="m-1"><i class="circle bg-danger"></i>Kandang Non-Aktif</div>
            <div class="m-1"><i class="circle bg-secondary"></i>Kandang Aktif</div>
        </div>
    </div>
    <!-- Kandang hidden array -->
    <input type="hidden" name="selected_kandangs" value="{{ @$data->expense_kandang ? $data->expense_kandang->map(fn($k) => $k->kandang_id) : '[]' }}">
</div>

<!-- Vendor -->
<div class="row col-md-6">
    <label for="supplier_id" class="form-label">Nama Vendor</label>
    <select name="supplier_id" id="supplier_id" class="form-control" {{ @$data->expense_status >= 1 ? 'disabled' : '' }}>
        @if (@$data->supplier_id)
        <option value="{{ @$data->supplier_id }}" selected>{{ @$data->supplier->name }}</option>
        @endif
    </select>
</div>

<!-- container kandang -->
<div id="hatcheryButtonsContainer" class="mt-1">
    @if (! empty($kandangs))
        @foreach ($kandangs as $kandang)
        @php
        $is_active = $kandang->project_status;
        @endphp

        <button
            type="button"
            class="kandang_select btn mr-1 mt-1 rounded-pill waves-effect {{ in_array($kandang->kandang_id, $selected_kandang_id) ? 'btn-outline-primary' : ($is_active ? 'btn-outline-secondary' : 'btn-outline-danger') }}"
            data-active={{ $is_active }}
            data-kandang-id={{ $kandang->kandang_id }}
        >
            {{ $kandang->name }}
        </button>
        @endforeach
    @endif
</div>

<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
<script src="{{asset('app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js')}}"></script>
<script>
    $(function() {
        $(document).on('change', '#transparentFileUpload', function() {
            $(this).siblings('#fileName').val($(this).val().split('\\').pop())
        });

        // Initialize Select2
        const locationIdRoute = '{{ route("data-master.location.search") }}';
        const kandangIdRoute = `{{ route('data-master.kandang.search') }}?location_id=:id`;
        const $locationSelect = $('#location_id');
        const $categorySelect = $('#category_id');
        const $container = $('#hatcheryButtonsContainer');
        const $kandangInput = $('input[name="selected_kandangs"]');

        initSelect2($locationSelect, 'Pilih Lokasi', locationIdRoute);
        initSelect2($categorySelect, 'Pilih Kategori');
        initFlatpickrDate($('.flatpickr-basic'));
        $locationSelect.trigger('select2:select');
        $categorySelect.trigger('select2:select');

        // -----------------------------------------------
        // change nonstock options according to supplier
        const nonstockIdRoute = '{{ route("data-master.nonstock.search") }}';
        const supplierIdRoute = '{{ route("data-master.supplier.search") }}';
        const $supplierSelect = $('#supplier_id');
        initSelect2($supplierSelect, 'Pilih Supplier', supplierIdRoute, '', { allowClear: true });
        $supplierSelect.on('change', function() {
            $supplier = $(this);
            $('.nonstock-select').val('').trigger('change');
            $('.uom').text('').trigger('change');
            $('.nonstock-select').select2('destroy');
            if ($supplier.val() && $supplier.val() !== '') {
                $('.nonstock-select').each(function() {
                    initSelect2($(this), 'Pilih Non Stock', nonstockIdRoute + '?supplier_id=' + $supplier.val());
                });
            } else {
                $('.nonstock-select').each(function() {
                    initSelect2($(this), 'Pilih Non Stock', nonstockIdRoute);
                });
            }
        });
        // -----------------------------------------------

        let selectedKandangs = JSON.parse($kandangInput.val()); // array

        function renderHatcheryButtons(data) {
            $container.empty();
            $kandangInput.val('[]').trigger('input');
            selectedKandangs = [];
            $('.color-information').css('visibility', 'hidden');

            if (data) {
                $('.color-information').css('visibility', 'visible');
                const kandangs = data.map(kandang => kandang.data);

                const buttons = [];
                kandangs.forEach(kandang => {
                    const status = !!kandang.project_status ? 1 : 0;

                    const button = $('<button>', {
                        class: `kandang_select btn mr-1 mt-1 rounded-pill waves-effect ${status ? "btn-outline-secondary" : "btn-outline-danger"}`,
                        text: kandang.name,
                    })
                    .attr('data-kandang-id', kandang.kandang_id)
                    .attr('data-active', status);
                    buttons.push(button);
                });

                // Sort button by project_status
                buttons.sort(function(a, b) {
                    const aActive = $(a).data('active');
                    const bActive = $(b).data('active');
                    return bActive - aActive;
                });

                // Append after sort so active kandang would be shown first
                buttons.forEach((button) => $container.append(button));
            }
        }

        $container.on('click', 'button', function(e) {
            e.preventDefault();
            const status = $(this).data('active');
            if ($(this).hasClass('btn-outline-primary')) {
                // Unselected
                $(this)
                    .removeClass('btn-outline-primary')
                    .addClass(status ? "btn-outline-secondary" : "btn-outline-danger");

                selectedKandangs = selectedKandangs.filter((id) => id !== $(this).data('kandang-id'));
            } else {
                // Selected
                $(this)
                    .removeClass('btn-outline-secondary btn-outline-danger')
                    .addClass('btn-outline-primary');

                selectedKandangs.push($(this).data('kandang-id'));
            }

            $kandangInput.val(JSON.stringify(selectedKandangs)).trigger('input');
        });

        $(document).on('select2:select', '#category_id, #location_id', function() {
            const category_id = $categorySelect.val();
            const location_id = $locationSelect.val();
            if (category_id.toLowerCase() == 1 && location_id) {
                // Fetch kandangs
                $.getJSON(kandangIdRoute.replace(':id', location_id), function(data) {
                    if (data.length) {
                        renderHatcheryButtons(data);
                    }
                });
            } else {
                // Delete kandangs
                renderHatcheryButtons(null);
            }
        });

        function validationFile() {
            $('input[type="file"]').on('change', function() {
                const file = this.files[0];
                if (file) {
                    const fileType = file.type;
                    const maxSize = 5 * 1024 * 1024;
                    const fileSize = file.size;
                    const allowedTypes = /^(application\/pdf|image\/(jpeg|jpg))$/;
                    if (!allowedTypes.test(fileType)) {
                        alert('Mohon upload file berformat PDF atau JPEG/JPG.');
                        $(this).val('');
                    } else if (fileSize > maxSize) {
                        alert('Ukuran file harus kurang dari 5 MB');
                        $(this).val('');
                    }
                }
            });
        }
    });
</script>
