@extends('templates.main')
@section('title', $title)
@section('content')

@php
$statusPayment = App\Constants::MARKETING_PAYMENT_STATUS;
$budget = 1_000_000_000;
$efisiensi_nominal = $budget - $data->sum('pengeluaran');
$efisiensi_persen = $efisiensi_nominal / $budget * 100;
@endphp

<style>
    .budget-card {
      background: linear-gradient(118deg, #76A8D8, #A9D0F5);
      cursor: pointer;
      border-radius: 15px;
      color: white;
      padding: 20px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    #budget {
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
    }
</style>

<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/sweetalert2.min.css')}}" />

<script>
    function resetFilters() {
        const $locationId = $('#location_id');
        const $companyId = $('#company_id');
        const $kandangId = $('#kandang_id');
        const $startDate = $('#start_date');
        const $endDate = $('#end_date');
        const $period = $('#period');

        $locationId.val('').trigger('change').trigger('select2:select');
        $companyId.val('').trigger('change');
        $kandangId.val('').trigger('change');
        $startDate.val('').trigger('input');
        $startDate.siblings('.input.flatpickr-basic').val('').trigger('input');
        $endDate.val('').trigger('input');
        $endDate.siblings('.input.flatpickr-basic').val('').trigger('input');
        $period.val('').trigger('input');

        $kandangId.html('<option disabled selected>Pilih Lokasi terlebih dahulu</option>');
        $kandangId.select2('destroy');
    }

    function searchFinance(isPrint = false, isBlank = false) {
        const locationId = $('#location_id').val();
        const companyId = $('#company_id').val();
        const kandangId = $('#kandang_id').val();
        const startDate = $('#start_date').val();
        const endDate = $('#end_date').val();
        const period = $('#period').val();

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
            period,
            isPrint,
        });

        isBlank ? window.open(url, '_blank') : window.location.href = url;
    }

    function getUrl({
        locationId,
        companyId,
        kandangId,
        startDate,
        endDate,
        period,
        isPrint
    }) {
        const params = new URLSearchParams();
        if (locationId) params.append('location_id', locationId);
        if (companyId) params.append('company_id', companyId);
        if (kandangId) params.append('kandang_id', kandangId);
        if (startDate) params.append('date_start', startDate);
        if (endDate) params.append('date_end', endDate);
        if (period) params.append('period', period);
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
                                <select name="company_id" id="company_id" class="form-control">
                                    @if(old('company_id', $old['company_id'] ?? null))
                                        <option value="{{ old('company_id', $old['company_id']) }}" selected="selected">{{ old('company_name', $old['company_name']) }}</option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-3 mt-1">
                                <label for="location_id" class="form-label">Lokasi<i class="text-danger">*</i></label>
                                <select name="location_id" id="location_id" class="form-control">
                                    @if(old('location_id', $old['location_id'] ?? null))
                                        <option value="{{ old('location_id', $old['location_id']) }}" selected="selected">{{ old('location_name', $old['location_name']) }}</option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-3 mt-1">
                                <label for="kandang_id" class="form-label">Kandang<i class="text-danger">*</i></label>
                                <select name="kandang_id" id="kandang_id" class="form-control">
                                    @if(old('kandang_id', $old['kandang_id'] ?? null))
                                        <option value="{{ old('kandang_id', $old['kandang_id']) }}" selected="selected">{{ old('kandang_name', $old['kandang_name']) }}</option>
                                    @else
                                        <option value="" selected disabled>Pilih Lokasi terlebih dahulu</option>
                                    @endif
                                </select>
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
                                <input type="text" name="period" id="period" class="form-control" value="{{ old('period', $old['period'] ?? '') }}" placeholder="Masukan angka periode"></input>
                            </div>
                            <div class="col-12 mt-1">
                                <button type="button" class="btn btn-outline-primary waves-effect" onclick="searchFinance()">Cari</button>
                                <button type="button" class="btn btn-outline-warning waves-effect" onclick="resetFilters()">Reset</button>
                            </div>
                        </div>
                    </div>
                    {{-- budget --}}
                    <div class="col-md-3 mt-1">
                        <div class="budget-popover d-none">
                            <p class="font-bolder">{{ \App\Helpers\Parser::toLocale($budget) }}</p>
                        </div>
                        <div class="budget-card mt-1" tabindex="0" data-container="body" data-toggle="popover" data-trigger="focus" data-placement="left" data-html="true">
                            <h5 class="font-weight-bolder text-white">Budget</h5>
                            <p id="budget" class="mt-1 font-weight-bolder fs-larger" data-visible="false" style="font-size: 1.7rem;">Rp.&nbsp;<span>{{ \App\Helpers\Parser::toLocale($budget) }}</span></p>
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
                                @foreach ($data as $index => $item)
                                @php
                                $budget -= $item->pengeluaran;
                                $budget += $item->pemasukan;
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->id_item }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->transaction_type }}</td>
                                    <td>{{ $item->created_by }}</td>
                                    <td>{{ date('d-M-Y', strtotime($item->created_at)) }}</td>
                                    <td>{{ date('d-M-Y', strtotime($item->payment_at)) }}</td>
                                    <td>
                                    @switch($item->payment_status)
                                        @case(1)
                                            <div class="badge badge-pill badge-warning">{{ $statusPayment[$item->payment_status] }}</div>
                                            @break
                                        @case(2)
                                            <div class="badge badge-pill badge-success">{{ $statusPayment[$item->payment_status] }}</div>
                                            @break
                                        @case(3)
                                            <div class="badge badge-pill badge-primary">{{ $statusPayment[$item->payment_status] }}</div>
                                            @break
                                        @default
                                            <div class="badge badge-pill badge-danger">{{ $statusPayment[$item->payment_status] }}</div>
                                    @endswitch
                                    </td>
                                    <td class="text-right">Rp&nbsp;<span class="pengeluaran">{{ \App\Helpers\Parser::toLocale($item->pengeluaran) }}</span></td>
                                    <td class="text-right">Rp&nbsp;<span class="pemasukan">{{ \App\Helpers\Parser::toLocale($item->pemasukan) }}</span></td>
                                    <td class="text-right">Rp&nbsp;<span class="saldo">{{ \App\Helpers\Parser::toLocale($budget) }}</span></td>
                                </tr>
                                @endforeach
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
                                    <div class="col-md-6 font-weight-bolder" style="font-size: 1.2em;">Rp&nbsp;<span id="total_pengeluaran">{{ \App\Helpers\Parser::toLocale($data->sum('pengeluaran')) }}</span></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">Pemasukan :</div>
                                    <div class="col-md-6 font-weight-bolder" style="font-size: 1.2em;">Rp <span id="total_pemasukan">{{ \App\Helpers\Parser::toLocale($data->sum('pemasukan')) }}</span></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row text-right">
                                    <div class="col-md-8">EFISIENSI :</div>
                                    <div class="col-md-4 font-weight-bolder" style="font-size: 1.2em;"><span id="persentase_efisiensi">{{ $efisiensi_persen }}</span>%</div>
                                </div>
                                <div class="row text-right">
                                    <div class="col-md-6"></div>
                                    <div class="col-md-6 font-weight-bolder" style="font-size: 1.2em;">Rp <span id="total_efisiensi">{{ \App\Helpers\Parser::toLocale($efisiensi_nominal) }}</span></div>
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
    $(function () {
        // flatpickr
        initFlatpickrDate($('.flatpickr-basic'));

        // select2
        const $locationSelect = $('#location_id');
        const $companySelect = $('#company_id');
        const $kandangSelect = $('#kandang_id');
        const locationIdRoute = '{{ route("data-master.location.search") }}';
        const companyIdRoute = '{{ route("data-master.company.search") }}';
        const kandangIdRoute = `{{ route('data-master.kandang.search') }}?location_id=:id`;
        initSelect2($locationSelect, 'Pilih Lokasi', locationIdRoute, '');
        initSelect2($companySelect, 'Pilih Unit Bisnis', companyIdRoute, '');
        // initSelect2($kandangSelect, 'Pilih Kandang', kandangIdRoute, '');

        $locationSelect.on('select2:select', function() {
            const locationId = $(this).val();
            $kandangSelect.val(null).trigger('change').empty();

            if (locationId) {
                let kandangRoute = kandangIdRoute.replace(':id', locationId);
                initSelect2($kandangSelect, 'Pilih Kandang', kandangRoute);
            } else {
                $kandangSelect.html('<option disabled selected>Pilih Lokasi terlebih dahulu</option>');
            }
        });

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

        $('.budget-card').on('click', function() {
            const $this = $(this).find('#budget');
            const visible = $this.attr('data-visible') == 'true' ? true : false;
            $this.attr('data-visible', !visible);
        });

        $('.budget-card').popover({
            content: function() {
                return $(this).siblings('.budget-popover').html();
            }
        });
    });

</script>

@endsection
