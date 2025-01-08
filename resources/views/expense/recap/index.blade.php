@extends('templates.main')
@section('title', $title)
@section('content')

@php
    $status = 2;
    if (! empty($data)) {
        dump($data);
    }
    dump($old);
@endphp

<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">

<script>
    function searchRecap() {
        const locationId = $('#location_id').val();
        const startDate = $('#start_date').val();
        const endDate = $('#end_date').val();

        const selectedKandangs = JSON.parse($('input[name="selected_kandangs"]').val());

        // Query parameters
        const params = new URLSearchParams();
        if (locationId) params.append('location_id', locationId);
        if (startDate) params.append('date_start', startDate);
        if (endDate) params.append('date_end', endDate);
        if (selectedKandangs.length > 0) params.append('farms', JSON.stringify(selectedKandangs));

        // Url with params
        const url = `{{ route("expense.recap.index") }}?${params.toString()}`;

        window.location.href = url;
    }
</script>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title">{{$title}}</div>
                <div class="float-right">
                    <button data-toggle="modal" data-target="#recapExpense" id="submitForm" type="submit" class="btn btn-primary waves-effect waves-float waves-light">Rekap BOP</button>
                    @include('expense.recap.sections.modal-recap')
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Lokasi -->
                    <div class="col-md-2 mt-1">
                        <label for="location_id" class="form-label">Lokasi</label>
                        <select name="location_id" id="location_id" class="form-control">
                            @if(old('location_id', $old['location_id'] ?? null))
                                <option value="{{ old('location_id', $old['location_id']) }}" selected="selected">{{ old('location_name', $old['location_name']) }}</option>
                            @endif
                        </select>
                    </div>
                    <!-- Kategori -->
                    <div class="col-md-10 mt-1">
                        <label for="start_date" class="form-label">Tanggal Biaya<i class="text-danger">*</i></label>
                        <div class="row align-items-center">
                            <div class="col-md-2">
                                <input type="date" id="start_date" class="form-control flatpickr-basic" value="{{ old('date_start', $old['date_start'] ?? '') }}" name="start_date" placeholder="Pilih Tanggal Mulai">
                            </div>
                            <span style="font-size: 1.2rem;">-</span>
                            <div class="col-md-2">
                                <input type="date" id="end_date" class="form-control flatpickr-basic" value="{{ old('date_end', $old['date_end'] ?? '') }}" name="start_date" placeholder="Pilih Tanggal Selesai">
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mt-1">
                        <button type="button" class="btn btn-outline-primary waves-effect" onclick="searchRecap()">Cari</button>
                    </div>
                    <input type="hidden" name="selected_kandangs" value="[]">
                </div>

                <!-- container kandang -->
                <div id="hatcheryButtonsContainer" class="mt-1"></div>

                {{-- table--}}
                @include('expense.recap.sections.bop-table')
                @include('expense.recap.sections.non-bop-table')

                <div class="row justify-content-end mr-2 mt-3">
                    <p class="col-6 col-md-2">Total Keseluruhan Biaya</p>
                    <p class="col-6 col-md-2 numeral-mask font-weight-bolder text-right" style="font-size: 1.2em;"><span id="total-recap">0,00</span></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{asset('app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js')}}"></script>
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
<script>
    // flatpickr
    initFlatpickrDate($('.flatpickr-basic'));

    $(function() {
        const locationIdRoute = '{{ route("data-master.location.search") }}';
        const kandangIdRoute = `{{ route('data-master.kandang.search') }}?location_id=:id`;
        const $locationSelect = $('#location_id');
        const $categorySelect = $('#category_id');
        const $container = $('#hatcheryButtonsContainer');
        const $kandangInput = $('input[name="selected_kandangs"]');
        initSelect2($locationSelect, 'Pilih Lokasi', locationIdRoute);
        initSelect2($categorySelect, 'Pilih Kategori');

        let selectedKandangs = JSON.parse($kandangInput.val());

        function renderHatcheryButtons(data) {
            $container.empty();
            $kandangInput.val('[]').trigger('input');
            selectedKandangs = [];

            if (data) {
                const kandangs = data.map(kandang => kandang.data);

                const buttons = [];
                kandangs.forEach(kandang => {
                    const status = !!kandang.project_status;

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
                    const aActive = $(a).data('active') ? 1 : 0;
                    const bActive = $(b).data('active') ? 1 : 0;
                    return bActive - aActive;
                });

                // Append after sort so active kandang would be shown first
                buttons.forEach((button) => $container.append(button));
            }
        }

        $container.on('click', 'button', function(e) {
            e.preventDefault();
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

        $(document).on('select2:select', '#location_id', function() {
            const location_id = $locationSelect.val();
            if (location_id) {
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

        // START :: calculate total
        function calculateBOP() {
            let total = 0;
            $('.nominal-bop').each(function() {
                total += parseLocaleToNum($(this).text());
            });
            $('#total-biaya-bop').text(parseNumToLocale(total)).trigger('change');
        }

        function calculateNonBOP() {
            let total = 0;
            $('.nominal-non-bop').each(function() {
                total += parseLocaleToNum($(this).text());
            });
            $('#total-biaya-non-bop').text(parseNumToLocale(total)).trigger('change');
        }

        function calculateTotal() {
            const totalBop = parseLocaleToNum($('#total-biaya-bop').text());
            const totalNonBop = parseLocaleToNum($('#total-biaya-non-bop').text());
            const total = totalBop + totalNonBop;
            $('#total-recap').text(parseNumToLocale(total));
        }

        calculateBOP();
        calculateNonBOP();
        calculateTotal();
    });
    // END :: calculate total

    // START :: export
    let tableBOP = $('#datatableBOP').DataTable({
        dom: '<"custom-table-wrapper"t>',
        // buttons: [
        //     {
        //         extend: 'excelHtml5',
        //         className: 'd-none',
        //         title: 'Rekap Biaya',
        //         footer: true
        //     },
        //     {
        //         extend: 'pdfHtml5',
        //         className: 'd-none',
        //         title: 'Rekap Biaya Operasional',
        //         customize: function(doc) {
        //             doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
        //         },
        //         footer: true
        //     }
        // ]
    });

    let tableNonBOP = $('#datatableNonBOP').DataTable({
        dom: '<"custom-table-wrapper"t>',
    });

    // Event handler untuk tombol export
    $('.modal-footer .btn-primary').on('click', function() {
        const selectedFormat = $('input[name="fileType"]:checked').val();

        if (selectedFormat === 'excel') {
            tableBOP.button(0).trigger(); // Trigger Excel export
        } else if (selectedFormat === 'pdf') {
            tableBOP.button(1).trigger(); // Trigger PDF export
        }

        $('#recapExpense').modal('hide');
    });

    // Radio button click handlers
    $('#exportExcel, #excelRadio').on('click', function() {
        $('#excelRadio').prop('checked', true);
    });

    $('#exportPdf, #pdfRadio').on('click', function() {
        $('#pdfRadio').prop('checked', true);
    });
    // END :: export
</script>

@endsection
