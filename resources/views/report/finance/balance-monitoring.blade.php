@extends('templates.main')
@section('title', $title)
@section('content')

@php
dump($paginated);
@endphp

<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">

<div class="row">
    <div class="col-12">
        @include('report.finance.tabs')
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ $title }}</h4>
            </div>
            <div class="card-body">
                <div class="card-datatable">
                    <!-- Filter -->
                    <div class="row mb-1">
                        <div class="col-12">
                            <form action="{{ route('report.finance.balance-monitoring') }}">
                                <div class="row d-flex align-items-end">
                                    <div class="col-md-4 col-12">
                                        <div class="form-group">
                                            <label for="area_id">Area</label>
                                            <select name="marketings[marketing_products][warehouse][location][area_id]" id="area_id" class="form-control">
                                                @if (request()->has('marketings') && isset(request()->get('marketings')['marketing_products']['warehouse']['location']['area_id']))
                                                @php
                                                    $areaId = request()->get('marketings')['marketing_products']['warehouse']['location']['area_id'];
                                                @endphp
                                                <option value="{{ $areaId }}" selected> {{ \App\Models\DataMaster\Area::find($areaId)->name }}</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-12">
                                        <div class="form-group">
                                            <label for="customer_id">Customer</label>
                                            <select name="customer_id" id="customer_id" class="form-control">
                                                @if (request()->has('customer_id') && @request()->get('customer_id'))
                                                @php
                                                    $customerId = request()->get('customer_id')
                                                @endphp
                                                <option value="{{ $customerId }}" selected> {{ \App\Models\DataMaster\Customer::find($customerId)->name }}</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-12">
                                        <div class="form-group">
                                            <label for="sales_id">Sales</label>
                                            <select name="marketings[sales_id]" id="sales_id" class="form-control">
                                                @if (request()->has('marketings') && isset(request()->get('marketings')['sales_id']))
                                                @php
                                                    $salesId = request()->get('marketings')['sales_id'];
                                                @endphp
                                                <option value="{{ $salesId }}" selected> {{ \App\Models\UserManagement\User::find($salesId)->name }}</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-12">
                                        <div class="form-group">
                                            <label for="balance_type">Tampil</label>
                                            <select id="balance_type" class="form-control">
                                                <option value="-all" {{ ! request()->has('balance_type') || request()->get('balance_type') == '-all' ? 'selected' : '' }}>Semua</option>
                                                @php
                                                $balanceType = ['Penjualan', 'Pembelian', 'Contoh 1', 'Contoh 2'];
                                                @endphp
                                                @foreach ($balanceType as $key => $item)
                                                    <option value="{{$key}}" {{ request()->has('balance_type') && request()->get('balance_type') == $key ? 'selected' : '' }}>{{ $item }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-12">
                                        <div class="form-group">
                                            <label for="start_date">Tanggal Awal</label>
                                            <input
                                                name="marketings[created_at][start]"
                                                id="start_date"
                                                type="text"
                                                class="form-control flatpickr-basic"
                                                value="{{ request()->has('marketings') && isset(request()->get('marketings')['created_at']['start']) ? request()->get('marketings')['created_at']['start'] : '' }}"
                                            >
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-12">
                                        <div class="form-group">
                                            <label for="end_date">Tanggal Akhir</label>
                                            <input
                                                name="marketings[created_at][end]"
                                                id="end_date"
                                                type="text"
                                                class="form-control flatpickr-basic"
                                                value="{{ request()->has('marketings') && isset(request()->get('marketings')['created_at']['end']) ? request()->get('marketings')['created_at']['end'] : '' }}"
                                            >
                                        </div>
                                    </div>
                                    <div class="col-md-1 col-12">
                                        <div class="form-group">
                                            <label for="rows">Baris</label>
                                            <select name="rows" class="form-control" >
                                                @php
                                                    $arrRows = [10,20,50,100];
                                                @endphp
                                                @for ($i = 0; $i < count($arrRows); $i++)
                                                <option value="{{ $arrRows[$i] }}" {{ request()->has('rows') && request()->get('rows') == $arrRows[$i] ? 'selected' : '' }}>{{ $arrRows[$i] }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-12">
                                        <div class="form-group float-right">
                                            <button type="submit" class="btn btn-primary">Cari</button>
                                            <a href="{{ route('report.finance.balance-monitoring') }}"  class="btn btn-warning">Reset</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!--/ Filter -->
                    <hr>
                    @if (isset($paginated) && count($paginated))
                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered w-100" style="margin: 0 0 !important;">
                            <thead class="text-center">
                                <tr>
                                    <th rowspan="2">No</th>
                                    <th rowspan="2">Customer</th>
                                    <th rowspan="2">Saldo Awal</th>
                                    <th colspan="3">Penjualan Ayam Besar</th>
                                    <th rowspan="2">Penjualan Trading</th>
                                    <th rowspan="2">Pembayaran</th>
                                    <th rowspan="2">Hutang Customer</th>
                                    <th rowspan="2">Aging</th>
                                    <th rowspan="2">Aging Rata-Rata</th>
                                    <th rowspan="2">Saldo Akhir</th>
                                </tr>
                                <tr>
                                    <th>Ekor</th>
                                    <th>Kg</th>
                                    <th>Nominal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($paginated as $index => $item)
                                <tr>
                                    <td class="text-right">{{ $index + 1 }}</td>
                                    <td>{{ $item->customer }}</td>
                                    <td class="text-right">{{ $item->saldo_awal }}</td>
                                    <td>{{ $item->ayam_ekor }}</td>
                                    <td>{{ $item->ayam_kg }}</td>
                                    <td class="text-right">{{ $item->ayam_nominal }}</td>
                                    <td class="text-right">{{ $item->trading }}</td>
                                    <td class="text-right">{{ $item->pembayaran }}</td>
                                    <td class="text-right{{ $item->raw_hutang > 0 ? ' text-danger' : '' }}">{{ $item->hutang }}</td>
                                    <td>{{ $item->aging }} Hari</td>
                                    <td>{{ $item->aging_avg }} Hari</td>
                                    <td class="text-right{{ $item->raw_saldo_akhir > 0 ? ' text-danger' : '' }}">{{ $item->saldo_akhir }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                @php
                                    $parser = \App\Helpers\Parser::class;
                                @endphp
                                <tr class="font-weight-bolder">
                                    <td colspan="2" class="text-center">Total</td>
                                    <td class="text-right">{{ $parser::toLocale($paginated->sum('raw_saldo_awal')) }}</td>
                                    <td>{{ $parser::trimLocale($paginated->sum('raw_ayam_ekor')) }}</td>
                                    <td>{{ $parser::trimLocale($paginated->sum('raw_ayam_kg')) }}</td>
                                    <td class="text-right">{{ $parser::toLocale($paginated->sum('raw_ayam_nominal')) }}</td>
                                    <td class="text-right">{{ $parser::toLocale($paginated->sum('raw_trading')) }}</td>
                                    <td class="text-right">{{ $parser::toLocale($paginated->sum('raw_pembayaran')) }}</td>
                                    <td class="text-right text-danger">{{ $parser::toLocale($paginated->sum('raw_hutang')) }}</td>
                                    <td>{{ $parser::trimLocale($paginated->sum('raw_aging')) }}</td>
                                    <td>{{ $parser::trimLocale($paginated->sum('raw_aging_avg')) }}</td>
                                    <td class="text-right text-danger">{{ $parser::toLocale($paginated->sum('raw_saldo_akhir')) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <!--/ Table -->
                    @else
                    <div class="text-center">Tidak ada data.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>

<script src="{{asset('app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js')}}"></script>

<script>
    $(function () {
        const areaIdRoute = '{{ route("data-master.area.search") }}';
        const customerIdRoute = '{{ route("data-master.customer.search") }}';
        const userIdRoute = '{{ route("user-management.user.search") }}';

        initSelect2($('select#area_id'), 'Pilih Area', areaIdRoute, '', { allowClear: true });
        initSelect2($('select#customer_id'), 'Pilih Customer', customerIdRoute, '', { allowClear: true });
        initSelect2($('select#sales_id'), 'Pilih Sales', userIdRoute, '', { allowClear: true });
        initSelect2($('select#balance_type'), 'Pilih Tipe Saldo');

        $('.flatpickr-basic').each(function() {
            initFlatpickrDate($(this));
        });
    });
</script>

@endsection
