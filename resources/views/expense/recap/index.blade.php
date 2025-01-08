@extends('templates.main')
@section('title', $title)
@section('content')

@php
    $status = 2;
@endphp

<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">

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
                        <select name="location_id" id="location_id" class="form-control"></select>
                    </div>
                    <!-- Kategori -->
                    <div class="col-md-10 mt-1">
                        <label for="start_date" class="form-label">Tanggal Biaya<i class="text-danger">*</i></label>
                        <div class="row align-items-center">
                            <div class="col-md-2">
                                <input type="date" class="form-control flatpickr-basic" name="start_date" placeholder="Pilih Tanggal Mulai">
                            </div>
                            <span style="font-size: 1.2rem;">-</span>
                            <div class="col-md-2">
                                <input type="date" class="form-control flatpickr-basic" name="start_date" placeholder="Pilih Tanggal Selesai">
                            </div>
                        </div>
                    </div>
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
    const dateOpt = { dateFormat: 'd-M-Y' };
    $('.flatpickr-basic').flatpickr(dateOpt);

    const dataKandang = {
        "1": [
            { "id": 1, "name": "Kandang 1", "status": "Aktif" },
            { "id": 2, "name": "Kandang 2", "status": "Tidak Aktif" },
            { "id": 3, "name": "Kandang 3", "status": "Aktif" },
            { "id": 4, "name": "Kandang 4", "status": "Tidak Aktif" },
        ]
    };

    const locationIdRoute = '{{ route("data-master.location.search") }}';
    initSelect2($('#location_id'), 'Pilih Lokasi', locationIdRoute);

    $('#location_id').on('change', function () {
        const locationId = $(this).val();
        const container = $('#hatcheryButtonsContainer');
        container.empty();

        if (dataKandang[locationId]) {
            dataKandang[locationId].forEach(kandang => {
                const button = $('<button>', {
                    class: `btn mr-1 mt-1 rounded-pill ${kandang.status === "Aktif" ? "btn-outline-secondary" : "btn-outline-danger"}`,
                    text: kandang.name,
                    click: function (e) {
                        e.preventDefault();
                        if ($(this).hasClass('btn-outline-primary')) {
                            $(this)
                                .removeClass('btn-outline-primary')
                                .addClass(kandang.status === "Aktif" ? "btn-outline-secondary" : "btn-outline-danger");
                        } else {
                            $(this)
                                .removeClass('btn-outline-secondary btn-outline-danger')
                                .addClass('btn-outline-primary');
                        }
                    }
                });
                container.append(button);
            });
        }
    });

    // START :: calculate total
    $(function() {
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
