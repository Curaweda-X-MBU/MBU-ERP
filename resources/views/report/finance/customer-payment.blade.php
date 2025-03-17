@extends('templates.main')
@section('title', $title)
@section('content')

<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">

<style>
    .color-header {
        color: white;
        background: linear-gradient(118deg, #76A8D8, #c9e5ff);
    }
</style>

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
                            <form action="{{ route('report.finance.customer-payment') }}">
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
                                                @if (request()->has('marketings') && @request()->get('customer_id'))
                                                @php
                                                    $customerId = request()->get('customer_id');
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
                                                    $arrRows = [5,10,15,20];
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
                                            <a href="{{ route('report.finance.customer-payment') }}"  class="btn btn-warning">Reset</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!--/ Filter -->
                    <hr>
                    @if (count($paginated))
                    @foreach ($paginated as $item)
                    <!-- Collapse -->
                    <div class="card-body">
                        <section id="collapsible">
                            <div class="row">
                                <div class="col-12">
                                    <div class="collapse-icon">
                                        <div class="p-0">
                                            <div class="collapse-default">
                                                <div class="card mb-1">
                                                    <!-- Collapse Header -->
                                                    @php
                                                        $customer = $item->customer;
                                                        $id = strtolower(str_replace(' ', '-', $customer->name)) . '-' . $customer->npwp;
                                                    @endphp
                                                    <div id="heading-collapse-{{ $id }}" class="card-header color-header collapsed" data-toggle="collapse" role="button" data-target="#collapse-{{ $id }}" aria-expanded="true" aria-controls="collapse-{{ $id }}">
                                                        <span class="lead collapse-title">{{ $customer->name }} | {{ $customer->address }}</span>
                                                        <span class="text-dark">NIK: {{ $customer->nik }} | NPWP: {{ $customer->npwp }}</span>
                                                    </div>
                                                    <!--/ Collapse Header -->
                                                    <!-- Table -->
                                                    <div id="collapse-{{ $id }}" role="tabpanel" aria-labelledby="heading-collapse-{{ $id }}" class="collapse show" aria-expanded="true">
                                                        <div class="card-body p-0">
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered w-100" style="margin: 0 0 !important;">
                                                                    <thead class="text-center">
                                                                        <tr>
                                                                            <th>No</th>
                                                                            <th>Tanggal</th>
                                                                            <th>Referensi</th>
                                                                            <th>CPA</th>
                                                                            <th>Nomor Polisi</th>
                                                                            <th>Ekor/Qty</th>
                                                                            <th>Berat (Kg)</th>
                                                                            <th>AVG</th>
                                                                            <th>Harga</th>
                                                                            <th>Diskon</th>
                                                                            <th>Tabungan</th>
                                                                            <th>PPN (%)</th>
                                                                            <th>Total</th>
                                                                            <th>Pembayaran</th>
                                                                            <th>Saldo Piutang</th>
                                                                            <th>Keterangan</th>
                                                                            <th>Pengambilan</th>
                                                                            <th>Sales/Marketing</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach ($item->products as $index => $values)
                                                                        @php
                                                                        $cpa = (int) filter_var($values->referensi, FILTER_SANITIZE_NUMBER_INT);
                                                                        @endphp
                                                                        <tr>
                                                                            <td class="text-right">{{ $index + 1 }}</td>
                                                                            <td>{{ $values->tanggal }}</td>
                                                                            <td>{{ $values->referensi }}</td>
                                                                            <td>{{ $cpa }}</td>
                                                                            <td>{{ $values->nopol }}</td>
                                                                            <td>{{ $values->qty }}</td>
                                                                            <td>{{ $values->berat }}</td>
                                                                            <td>{{ $values->avg }}</td>
                                                                            <td class="text-right">{{ $values->harga }}</td>
                                                                            <td class="text-right">{{ $values->diskon }}</td>
                                                                            <td class="text-right">{{ $values->tabungan }}</td>
                                                                            <td>{{ $values->pajak }}</td>
                                                                            <td class="text-right">{{ $values->total }}</td>
                                                                            <td class="text-right">{{ $values->pembayaran }}</td>
                                                                            <td class="text-right{{ \App\Helpers\Parser::parseLocale($values->saldo) > 0 ? ' text-danger' : '' }} ">{{ $values->saldo }}</td>
                                                                            <td class="text-center font-weight-bolder">{{ $values->keterangan }}</td>
                                                                            <td>{{ $values->pengambilan }}</td>
                                                                            <td>{{ $values->sales }}</td>
                                                                        </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!--/ Table -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                    <!--/ Collapse -->
                    @endforeach
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
