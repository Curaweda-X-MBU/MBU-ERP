@extends('templates.main')
@section('title', $title)
@section('content')

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
                                            <select name="area_id" id="area_id" class="form-control">
                                                @if (request()->has('area_id') && @request()->get('area_id')))
                                                @php
                                                    $areaId = request()->get('area_id');
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
                                            <select name="sales_id" id="sales_id" class="form-control">
                                                @if (request()->has('sales_id') && @request()->get('sales_id'))
                                                @php
                                                    $salesId = request()->get('sales_id')
                                                @endphp
                                                <option value="{{ $salesId }}" selected> {{ \App\Models\UserManagement\User::find($salesId)->name }}</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-12">
                                        <div class="form-group">
                                            <label for="balance_type">Tampil</label>
                                            <select name="balance_type" id="balance_type" class="form-control">
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
                                            <input id="start_date" name="start_date" type="text" class="form-control flatpickr-basic">
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-12">
                                        <div class="form-group">
                                            <label for="end_date">Tanggal Akhir</label>
                                            <input id="end_date" name="end_date" type="text" class="form-control flatpickr-basic">
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
                                            <a href="{{ route('expense.list.index') }}"  class="btn btn-warning">Reset</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!--/ Filter -->
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
                                <tr>
                                    <td class="text-right">No</td>
                                    <td>Customer</td>
                                    <td class="text-right">Saldo Awal</td>
                                    <td>Ekor</td>
                                    <td>Kg</td>
                                    <td class="text-right">Nominal</td>
                                    <td class="text-right">Trading</td>
                                    <td class="text-right">Pembayaran</td>
                                    <td class="text-right">Hutang</td>
                                    <td>Aging</td>
                                    <td>AVG Aging</td>
                                    <td class="text-right">Saldo Akhir</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!--/ Table -->
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
