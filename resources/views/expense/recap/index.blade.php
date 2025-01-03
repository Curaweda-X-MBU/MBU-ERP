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
                    <button data-toggle="modal" data-target="#recapExpense" id="submitForm" type="submit" class="btn btn-primary waves-effect waves-float waves-light">Rekap</button>
                    {{-- Include Modal --}}
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
                        <label for="start_date" class="form-label">Tanggal Penjualan<i class="text-danger">*</i></label>
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

                {{-- START :: table --}}
                <div class="table-responsive mt-2">
                    <table id="datatable" class="table table-bordered">
                        <thead>
                            <tr class="bg-light text-center">
                                <th>Kategori</th>
                                <th>Sub Kategori</th>
                                <th>Status</th>
                                <th>Nominal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="text-center">
                                <td>Biaya Operational</td>
                                <td>Listrik</td>
                                <td>
                                    @switch($status)
                                        @case(1)
                                            <div class="badge badge-pill badge-primary">Dibayar</div>
                                            @break
                                        @case(2)
                                            <div class="badge badge-pill badge-danger">Belum Dibayar</div>
                                            @break
                                        @default
                                            <div class="badge badge-pill badge-secondary">N/A</div>
                                    @endswitch
                                </td>
                                <td class="total-nominal">15.000,00</td>
                            </tr>
                            <tr class="text-center">
                                <td>Biaya Operational</td>
                                <td>Listrik</td>
                                <td>
                                    @switch($status)
                                        @case(1)
                                            <div id="status" class="badge badge-pill badge-primary">Dibayar</div>
                                            @break
                                        @case(2)
                                            <div id="status" class="badge badge-pill badge-danger">Belum Dibayar</div>
                                            @break
                                        @default
                                            <div id="status" class="badge badge-pill badge-secondary">N/A</div>
                                    @endswitch
                                </td>
                                <td class="total-nominal">177.000,00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                {{-- END :: table  --}}

                <div class="row justify-content-end mr-2 mt-3">
                    <p class="col-6 col-md-2">Total Sebelum Pajak</p>
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

    function calculateTotal() {
        let total = 0;
        $('.total-nominal').each(function() {
            const nominal = $(this).text() || '0';
        total += parseLocaleToNum(nominal);
        });
        $('#total-recap').text(parseNumToLocale(total));
    }

    $(document).ready(function () {
        calculateTotal();
    });
</script>

@endsection
