@extends('templates.main')
@section('title', $title)
@section('content')

<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/extensions/sweetalert2.min.css') }}" />

<style>
#filter_wrapper label, #filter_wrapper input {
    margin: 0;
}
</style>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ $title }}</h4>
                <div class="float-right">
                    @if (auth()->user()->role->hasPermissionTo('marketing.list.add'))
                    <a href="{{ route('marketing.list.add') }}" type="button" class="btn btn-outline-primary waves-effect">Tambah</a>
                    @endif
                </div>
            </div>
            @php
                $arrRows = [10,20,50,100];
                $statusPayment = App\Constants::MARKETING_PAYMENT_STATUS;
                $statusMarketing = App\Constants::MARKETING_STATUS;
            @endphp
            <div class="card-body">
                <div class="card-datatable">
                    <div class="row mb-1">
                        <div class="col-12">
                            <form action="{{ route('marketing.list.index') }}">
                                <div class="row d-flex align-items-end">
                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label for="stock_type">Unit Bisnis</label>
                                            <select name="company_id" id="company_id" class="form-control" >
                                                @if (request()->has('company_id') && request()->get('company_id'))
                                                @php
                                                    $companyId = request()->get('company_id');
                                                @endphp
                                                <option value="{{ $companyId }}" selected> {{ \App\Models\DataMaster\Company::find($companyId)->name }}</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label for="stock_type">Area</label>
                                            <select name="marketing_products[warehouse][location][area_id]" id="area_id" class="form-control" >
                                                @if (request()->has('marketing_products') && isset(request()->get('marketing_products')['warehouse']['location']['area_id']))
                                                @php
                                                    $areaId = request()->get('marketing_products')['warehouse']['location']['area_id'];
                                                @endphp
                                                <option value="{{ $areaId }}" selected> {{ \App\Models\DataMaster\Area::find($areaId)->name }}</option>
                                                @else
                                                <option selected disabled>Pilih unit bisnis terlebih dahulu</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label for="stock_type">Lokasi</label>
                                            <select name="marketing_products[warehouse][location_id]" id="location_id" class="form-control" >
                                                @if (request()->has('marketing_products') && isset(request()->get('marketing_products')['warehouse']['location_id']))
                                                @php
                                                    $locationiId = request()->get('marketing_products')['warehouse']['location_id'];
                                                @endphp
                                                <option value="{{ $locationiId }}" selected> {{ \App\Models\DataMaster\Location::find($locationiId)->name }}</option>
                                                @else
                                                <option selected disabled>Pilih area terlebih dahulu</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label for="stock_type">Pelanggan</label>
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
                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label for="payment_status">Status Pembayaran</label>
                                            <select name="payment_status" id="payment_status" class="form-control">
                                                <option value="-all" {{ ! request()->has('payment_status') || request()->get('payment_status') == '-all' ? 'selected' : '' }}>Semua</option>
                                                @foreach ($statusPayment as $key => $item)
                                                    <option value="{{$key}}" {{ request()->has('payment_status') && request()->get('payment_status') == $key ? 'selected' : '' }}>{{ $item }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label for="marketing_status">Status Penjualan</label>
                                            <select name="marketing_status" id="marketing_status" class="form-control">
                                                <option value="-all" {{ ! request()->has('marketing_status') || request()->get('marketing_status') == '-all' ? 'selected' : '' }}>Semua</option>
                                                @foreach ($statusMarketing as $key => $item)
                                                    <option value="{{$key}}" {{ request()->has('marketing_status') && request()->get('marketing_status') == $key ? 'selected' : '' }}>{{ $item }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-12">
                                    </div>
                                    <div class="col-md-1 col-12">
                                        <div class="form-group">
                                            <label for="rows">Baris</label>
                                            <select name="rows" class="form-control" >
                                                @for ($i = 0; $i < count($arrRows); $i++)
                                                <option value="{{ $arrRows[$i] }}" {{ request()->has('rows') && request()->get('rows') == $arrRows[$i] ? 'selected' : '' }}>{{ $arrRows[$i] }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-12">
                                        <div class="form-group float-right">
                                            <button type="submit" class="btn btn-primary">Cari</button>
                                            <a href="{{ route('marketing.list.index') }}"  class="btn btn-warning">Reset</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="table-responsive mb-2">
                        <table id="datatable" class="table table-bordered table-striped w-100">
                            <thead>
                                <th>No.DO</th>
                                <th>Tanggal Penjualan</th>
                                <th>Tanggal Realisasi</th>
                                <th>Aging</th>
                                <th>Pelanggan</th>
                                <th>Unit Bisnis</th>
                                <th>Nominal Penjualan (Rp)</th>
                                <th>Nominal Sudah Bayar (Rp)</th>
                                <th>Nominal Sisa Bayar (Rp)</th>
                                <th>Status Pembayaran</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
                                    <tr>
                                        <td>{{ $item->id_marketing }}</td>
                                        <td>{{ date('d-M-Y', strtotime($item->sold_at)) }}</td>
                                        <td>{{ isset($item->realized_at) ? date('d-M-Y', strtotime($item->realized_at)) : '-' }}</td>
                                        <td>{{ isset($item->realized_at) ? \Carbon\Carbon::parse($item->realized_at)->diffInDays(now()) . ' hari' : '-' }}</td>
                                        <td>{{ $item->customer->name }}</td>
                                        <td>{{ $item->company->alias }}</td>
                                        <td class="text-right text-primary">{{ \App\Helpers\Parser::toLocale($item->grand_total) }}</td>
                                        <td class="text-right text-success">{{ \App\Helpers\Parser::toLocale($item->is_paid) }}</td>
                                        <td class="text-right text-danger">{{ \App\Helpers\Parser::toLocale($item->not_paid) }}</td>
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
                                        <td>
                                            @switch($item->marketing_status)
                                                @case(0)
                                                    <div class="badge badge-pill badge-danger">{{ $statusMarketing[$item->marketing_status] }}</div>
                                                    @break
                                                @case(1)
                                                    <div class="badge badge-pill badge-warning">{{ $statusMarketing[$item->marketing_status] }}</div>
                                                    @break
                                                @case(2)
                                                    <div class="badge badge-pill badge-info">{{ $statusMarketing[$item->marketing_status] }}</div>
                                                    @break
                                                @case(3)
                                                    <div class="badge badge-pill badge-success">{{ $statusMarketing[$item->marketing_status] }}</div>
                                                    @break
                                                @default
                                                    <div class="badge badge-pill badge-primary">{{ $statusMarketing[$item->marketing_status] }}</div>
                                            @endswitch
                                        </td>
                                        <td>
                                            <div class="dropdown dropleft" style="position: static;">
                                                <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-toggle="dropdown">
                                                    <i data-feather="more-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" href="{{ route('marketing.list.detail', $item->marketing_id) }}">
                                                        <i data-feather='eye' class="mr-50"></i>
                                                        <span>Detail</span>
                                                    </a>
                                                    @if ($item->marketing_status != 4 && auth()->user()->role->hasPermissionTo('marketing.list.realization'))
                                                    <a class="dropdown-item" href="{{ route('marketing.list.realization', $item->marketing_id) }}">
                                                        <i data-feather='package' class="mr-50"></i>
                                                        <span>Realisasi</span>
                                                    </a>
                                                    @endif
                                                    @if ($item->marketing_status == 4 && auth()->user()->role->hasPermissionTo('marketing.list.edit'))
                                                    <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#discountModal" data-id="{{ $item->marketing_id }}" data-do-number="{{ $item->id_marketing }}" data-current-discount="{{ $item->discount ?? null }}" data-current-discount-notes="{{ $item->discount_notes ?? null }}">
                                                        <i data-feather="percent" class="mr-50"></i>
                                                        <span>Tambah Diskon</span>
                                                    </a>
                                                    @endif
                                                    <a class="dropdown-item" href="{{ route('marketing.list.payment.index', $item->marketing_id) }}">
                                                        <i data-feather="credit-card" class="mr-50"></i>
                                                        <span>Pembayaran</span>
                                                    </a>
                                                    @if ($item->marketing_status == 4 && auth()->user()->role->hasPermissionTo('marketing.return.add'))
                                                    <a class="dropdown-item" href="{{ route('marketing.return.add', $item->marketing_id) }}">
                                                        <i data-feather="corner-down-left" class="mr-50"></i>
                                                        <span>Retur</span>
                                                    </a>
                                                    @endif
                                                    @if (@$item->approval_notes && @$item->is_approved === 0)
                                                    <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#notesModal" data-notes="{{ $item->approval_notes }}">
                                                        <i data-feather="message-square" class="mr-50"></i>
                                                        <span>Catatan Penolakan</span>
                                                    </a>
                                                    @endif
                                                    @if ($item->doc_reference)
                                                    <a class="dropdown-item" href="{{ route('file.show') . '?download=true&filename=' . $item->doc_reference }}">
                                                        <i data-feather="download" class="mr-50"></i>
                                                        <span>Unduh Dokumen</span>
                                                    </a>
                                                    @endif
                                                    @if (auth()->user()->role->hasPermissionTo('marketing.list.delete'))
                                                    <a class="dropdown-item item-delete-button text-danger"
                                                        href="{{ route('marketing.list.delete', $item->marketing_id) }}">
                                                        <i data-feather='trash' class="mr-50"></i>
                                                        <span>Hapus</span>
                                                    </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-1">
                        <span>Total : {{ number_format($data->total(), 0, ',', '.') }} data</span>
                        <div class="float-right">
                            {{ $data->links('vendor.pagination.bootstrap-4') }}
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12 col-md-6 my-1">
                            @if (auth()->user()->role->hasPermissionTo('marketing.list.payment.add') && auth()->user()->role->hasPermissionTo('marketing.list.payment.approve'))
                            @include('marketing.list.sections.batch-upload-modal')
                            @endif
                        </div>
                        <div class="col-12 col-md-6 my-1">
                            <table class="table table-borderless">
                                <tbody class="text-right">
                                    <tr>
                                        <td class="text-primary">
                                            Total Penjualan:
                                        </td>
                                        <td class="font-weight-bolder text-primary" style="font-size: 1.2em">
                                            Rp. <span id="grand_total">{{ \App\Helpers\Parser::toLocale($data->sum('grand_total')) }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-success">
                                            Total Sudah Dibayar:
                                        </td>
                                        <td class="font-weight-bolder text-success" style="font-size: 1.2em">
                                            Rp. <span id="is_paid">{{ \App\Helpers\Parser::toLocale($data->sum('is_paid')) }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-danger">
                                            Total Belum Dibayar:
                                        </td>
                                        <td class="font-weight-bolder text-danger" style="font-size: 1.2em">
                                            Rp. <span id="not_paid">{{ \App\Helpers\Parser::toLocale($data->sum('not_paid')) }}</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Catatan -->
<div class="modal fade" id="notesModal" tabindex="-1" role="dialog" aria-labelledby="notesModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notesModalLabel">Catatan Persetujuan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="notesContent">-</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal Diskon -->
<div class="modal fade" id="discountModal" tabindex="-1" role="dialog" aria-labelledby="discountModalLabel" aria-hidden="true">
    <form action="#" method="post">
    {{ csrf_field() }}
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="discountModalLabel">Tambah Diskon Pada: <span id="discountDoNumber"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-md-4 col-form-label">Total Diskon: </label>
                        <div class="col-md-8">
                            <input type="text" name="discount" class="form-control numeral-mask" placeholder="00,0">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-4 col-form-label">Catatan Diskon: </label>
                        <div class="col-md-8">
                            <textarea name="discount_notes" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary waves-effect waves-float waves-light" data-dismiss="modal" aria-label="Close">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary waves-effect waves-float waves-light">Simpan</button>
                </div>
            </div>
        </div>
    </form>
</div>

<script src="{{ asset('app-assets/vendors/js/extensions/sweetalert2.all.min.js') }}"></script>

<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>

<script src="{{asset('app-assets/vendors/js/forms/cleave/cleave.min.js')}}"></script>

<script>
    $(function () {
        const companyIdRoute = '{{ route("data-master.company.search") }}';
        const areaIdRoute = '{{ route("data-master.area.search") }}';
        const locationIdRoute = '{{ route("data-master.location.search") }}';
        const customerIdRoute = '{{ route("data-master.customer.search") }}';
        initSelect2($('select#customer_id'), 'Pilih Pelanggan', customerIdRoute, '', { allowClear: true });
        initSelect2($('select#payment_status'), 'Pilih Status Pembayaran');
        initSelect2($('select#marketing_status'), 'Pilih Status Penjualan');

        initSelect2($('select#company_id'), 'Pilih Unit Bisnis', companyIdRoute, '', { allowClear: true });

        $('select#company_id').on('change', function () {
            if ($('select#area_id').val()) {
                $('select#area_id').trigger('change');
            } else {
                $('select#area_id').val(null).trigger('change');
            }

            const companyId = $(this).val();

            initSelect2($('select#area_id'), 'Pilih Area', areaIdRoute, '', { allowClear: true });

            $('select#area_id').on('change', function() {
                if ($('select#location_id').val()) {
                    $('select#location_id').trigger('change');
                } else {
                    $('#location_id').val(null).trigger('change');
                }

                const areaId = $(this).val();

                initSelect2($('select#location_id'), 'Pilih Lokasi', locationIdRoute + `?area_id=${areaId}`, '', { allowClear: true });
            });
        });

        $('select').trigger('change');

        $('.item-delete-button').on('click', function(e) {
            e.preventDefault();

            confirmCallback({
                title: 'Hapus',
                text: 'Data tidak bisa dikembalikan!',
                icon: 'warning',
                confirmText: 'Hapus',
                confirmClass: 'btn-danger',
            }, function() {
                window.location.href = e.currentTarget.href;
            });
        });

        $(document).ready(function() {
            $('#notesModal').on('show.bs.modal', function(event) {
                var $button = $(event.relatedTarget);
                var notes = $button.data('notes') || '-';
                var $modal = $(this);
                $modal.find('#notesContent').text(notes);
            });

            $('#discountModal').on('show.bs.modal', function(event) {
                var $button = $(event.relatedTarget);
                var id = $button.data('id') || null;
                var doNumber = $button.data('do-number') || '-';
                var currentDiscount = $button.data('current-discount');
                var currentDiscountNotes = $button.data('current-discount-notes');
                var $modal = $(this);
                const discountRoute = @js(route('marketing.list.edit', ['marketing' => ':id', 'discount' => 'true']));
                $modal.find('#discountDoNumber').text(doNumber);
                $modal.find('form').attr('action', discountRoute.replace(':id', id));

                if (!!currentDiscount) {
                    $modal.find('input[name="discount"]').val(parseNumToLocale(currentDiscount));
                }
                if (!!currentDiscountNotes) {
                    $modal.find('textarea[name="discount_notes"]').text(currentDiscountNotes);
                }
            });
        });
        initNumeralMask('.numeral-mask');
    });
</script>


@endsection
