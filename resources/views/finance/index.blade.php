@extends('templates.main')
@section('title', $title)
@section('content')

<style>
    .budget-card {
      background: linear-gradient(118deg, #76A8D8, #A9D0F5);
      border-radius: 15px;
      color: white;
      padding: 20px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
</style>

<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/sweetalert2.min.css')}}" />

<script>

function searchFinance(isPrint = false, isBlank = false) {
        const locationId = $('#location_id').val();
        const companyId = $('#company_id').val();
        const kandangId = $('#kandang_id').val();
        const startDate = $('#start_date').val();
        const endDate = $('#end_date').val();

        if (!locationId || !companyId || !kandangId || locationId == '' || companyId == '' || kandangId == '') {
            Swal.fire({
                title: 'Filter',
                text: 'Isi filter wajib',
                icon: 'warning',
                showCancelButton: false,
                confirmButtonText: 'Ok',
                customClass: {
                    confirmButton: "btn mr-1 btn-warning",
                },
                buttonsStyling: false,
            }).then(function (result) {
                if (result.value) {
                    return;
                }
            });

            return;
        }

        const url = getUrl({
            locationId,
            companyId,
            kandangId,
            startDate,
            endDate,
            isPrint,
        });

        console.log(url)

        isBlank ? window.open(url, '_blank') : window.location.href = url;
    }

    function getUrl({
        locationId,
        companyId,
        kandangId,
        startDate,
        endDate,
        isPrint
    }) {
        const params = new URLSearchParams();
        if (locationId) params.append('location_id', locationId);
        if (companyId) params.append('company_id', companyId);
        if (kandangId) params.append('kandang_id', kandangId);
        if (startDate) params.append('date_start', startDate);
        if (endDate) params.append('date_end', endDate);
        if (isPrint) params.append('print', 'true');

        return `{{ route("finance.index") }}?${params.toString()}`;
    }</script>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title">{{$title}}</div>
                <div class="text-right mt-1">
                    <button class="btn btn-outline-secondary dropdown-toggle waves-effect" type="button" id="exportDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Export
                    </button>
                    <div class="dropdown-menu" aria-labelledby="exportDropdown">
                        <button id="exportExcel" class="dropdown-item w-100">Excel</button>
                        <button id="exportPdf" class="dropdown-item w-100">PDF</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    {{-- select --}}
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-3 mt-1">
                                <label for="company_id" class="form-label">Unit Bisnis<i class="text-danger">*</i></label>
                                <select name="company_id" id="company_id" class="form-control"></select>
                            </div>
                            <div class="col-md-3 mt-1">
                                <label for="location_id" class="form-label">Lokasi<i class="text-danger">*</i></label>
                                <select name="location_id" id="location_id" class="form-control"></select>
                            </div>
                            <div class="col-md-3 mt-1">
                                <label for="kandang_id" class="form-label">Kandang<i class="text-danger">*</i></label>
                                <select name="kandang_id" id="kandang_id" class="form-control"></select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mt-1">
                                <label for="start_date" class="form-label">Periode Tanggal</label>
                                <div class="row align-items-center">
                                    <div class="col-md-5">
                                        <input type="date" id="start_date" class="form-control flatpickr-basic" value="{{ old('date_start', $old['date_start'] ?? '') }}" name="start_date" placeholder="Pilih Tanggal Mulai">
                                    </div>
                                    <span style="font-size: 1.2rem;">-</span>
                                    <div class="col-md-5">
                                        <input type="date" id="end_date" class="form-control flatpickr-basic" value="{{ old('date_end', $old['date_end'] ?? '') }}" name="start_date" placeholder="Pilih Tanggal Selesai">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mt-1">
                                <label for="period" class="form-label">Periode Produksi</label>
                                <input type="text" name="period" id="period" class="form-control" placeholder="Masukan angka periode"></input>
                            </div>
                            <div class="col-12 mt-1">
                                <button class="btn btn-outline-primary" type="button" onclick="searchFinance()">Cari</button>
                            </div>
                        </div>
                    </div>
                    {{-- budget --}}
                    <div class="col-md-3 mt-1">
                        <div class="budget-card mt-1">
                            <h5 class="font-weight-bolder text-white">Budget</h5>
                            <p class="mt-1 font-weight-bolder fs-larger" style="font-size: 1.7rem;">Rp. <span id="budget">1.000.000.000,00</span></p>
                        </div>
                    </div>
                </div>

                {{-- START :: table --}}
                <div class="card-datatable mt-2">
                    <div class="table-responsive mb-2">
                        <table id="datatable" class="table table-bordered table-striped w-100">
                            <thead class="text-center">
                                <th>#</th>
                                <th>ID</th>
                                <th>Deskripsi</th>
                                <th>Jenis Transaksi</th>
                                <th>Dibuat Oleh</th>
                                <th>Tanggal Buat</th>
                                <th>Tanggal Bayar</th>
                                <th>Status Bayar</th>
                                <th>Pengeluaran</th>
                                <th>Pemasukan</th>
                                <th>Saldo</th>
                            </thead>
                            <tbody class="text-center">
                                <tr>
                                    <td>1</td>
                                    <td>PO.1</td>
                                    <td>Pembelian Pakan</td>
                                    <td>Pembelian</td>
                                    <td>Admin Farm</td>
                                    <td>05-01-2025</td>
                                    <td>05-01-2025</td>
                                    <td>
                                        @php
                                            $status = 2;
                                        @endphp
                                        @switch($status)
                                            @case(1)
                                                <div class="badge badge-pill badge-primary">Dibayar Sebagian</div>
                                                @break
                                            @case(2)
                                                <div class="badge badge-pill badge-success">Dibayar Penuh</div>
                                                @break
                                            @default
                                                <div class="badge badge-pill badge-secondary">N/A</div>
                                        @endswitch
                                    </td>
                                    <td>Rp <span class="pengeluaran">300.000.000,00</span></td>
                                    <td>Rp <span class="pemasukan">400.000.000,00</span></td>
                                    <td>Rp <span class="saldo">0,00</span></td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>BOP.1</td>
                                    <td>(BOP) Listrik</td>
                                    <td>Biaya</td>
                                    <td>Admin Farm</td>
                                    <td>06-01-2025</td>
                                    <td>06-01-2025</td>
                                    <td>
                                        @php
                                            $status = 1;
                                        @endphp
                                        @switch($status)
                                            @case(1)
                                                <div class="badge badge-pill badge-primary">Dibayar Sebagian</div>
                                                @break
                                            @case(2)
                                                <div class="badge badge-pill badge-success">Dibayar Penuh</div>
                                                @break
                                            @default
                                                <div class="badge badge-pill badge-secondary">N/A</div>
                                        @endswitch
                                    </td>
                                    <td>Rp <span class="pengeluaran">200.000.000,00</span></td>
                                    <td>Rp <span class="pemasukan">254.000.000,00</span></td>
                                    <td>Rp <span class="saldo">0,00</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- END :: table --}}

                <hr>

                <div class="row mx-1 my-2">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-4">Pengeluaran :</div>
                                    <div class="col-md-6 font-weight-bolder" style="font-size: 1.2em;">Rp <span id="total_pengeluaran">377.500.000,00</span></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">Pemasukan :</div>
                                    <div class="col-md-6 font-weight-bolder" style="font-size: 1.2em;">Rp <span id="total_pemasukan">50.000.000,00</span></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row text-right">
                                    <div class="col-md-8">EFISIENSI :</div>
                                    <div class="col-md-4 font-weight-bolder" style="font-size: 1.2em;"><span id="persentase_efisiensi">62,25</span>%</div>
                                </div>
                                <div class="row text-right">
                                    <div class="col-md-6"></div>
                                    <div class="col-md-6 font-weight-bolder" style="font-size: 1.2em;">Rp <span id="total_efisiensi">377.500.000,00</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{asset('app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js')}}"></script>
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>

<script src="{{asset('app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js')}}"></script>

<script src="{{ asset('app-assets/vendors/js/tables/datatable/datatables.buttons.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.html5.min.js') }}"></script>

<script src="{{ asset('app-assets/vendors/js/tables/datatable/jszip.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/pdfmake.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/vfs_fonts.js') }}"></script>
<script src="{{asset('app-assets/vendors/js/extensions/sweetalert2.all.min.js')}}"></script>

<script>
    // flatpickr
    initFlatpickrDate($('.flatpickr-basic'));

    // select2
    const $locationSelect = $('#location_id');
    const $companySelect = $('#company_id');
    const $kandangSelect = $('#kandang_id');
    const locationIdRoute = '{{ route("data-master.location.search") }}';
    const companyIdRoute = '{{ route("data-master.company.search") }}';
    const kandangIdRoute = '{{ route("data-master.kandang.search") }}';
    initSelect2($locationSelect, 'Pilih Lokasi', locationIdRoute, '');
    initSelect2($companySelect, 'Pilih Unit Bisnis', companyIdRoute, '');
    initSelect2($kandangSelect, 'Pilih Kandang', kandangIdRoute, '');

    $(function () {
        $('#datatable').DataTable({
            dom: '<"d-flex justify-content-between"B><"custom-table-wrapper"t>ip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    className: 'd-none datatable-hidden-excel-button',
                },
                {
                    extend: 'pdfHtml5',
                    className: 'd-none datatable-hidden-pdf-button',
                },
            ],
        });

        $('#exportExcel').on('click', function() {
            $('.datatable-hidden-excel-button').trigger('click');
        });

        $('#exportPdf').on('click', function() {
            $('.datatable-hidden-pdf-button').trigger('click');
        });

        // calculate total
        function calculateSaldo() {
            $('#datatable tbody tr').each(function() {
                const pengeluaran = parseLocaleToNum($(this).find('.pengeluaran').text());
                const pemasukan = parseLocaleToNum($(this).find('.pemasukan').text());
                const saldo = pemasukan - pengeluaran;
                $(this).find('.saldo').text(parseNumToLocale(saldo));
            });
        }

        function calculatePengeluaran() {
            let total = 0;
            $('.pengeluaran').each(function() {
                total += parseLocaleToNum($(this).text());
            });
            $('#total_pengeluaran').text(parseNumToLocale(total)).trigger('change');
        }

        function calculatePemasukan() {
            let total = 0;
            $('.pemasukan').each(function() {
                total += parseLocaleToNum($(this).text());
            });
            $('#total_pemasukan').text(parseNumToLocale(total)).trigger('change');
        }

        function calculateEfisiensi() {
            const budget = parseLocaleToNum($('#budget').text());
            const pengeluaran = parseLocaleToNum($('#total_pengeluaran').text());
            const persentaseEfisiensi = ((budget - pengeluaran) / budget) * 100;
            $('#persentase_efisiensi').text(parseNumToLocale(persentaseEfisiensi)).trigger('change');
            $('#total_efisiensi').text(parseNumToLocale(budget - pengeluaran)).trigger('change');
        }

        calculatePengeluaran();
        calculatePemasukan();
        calculateSaldo();
        calculateEfisiensi();
    });

</script>

@endsection
