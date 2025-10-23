@extends('templates.main')
@section('title', $title)
@section('content')

@php
    function jsonMainPrices($arr) {
        $parsed = $arr->map(fn($mp) => $mp->nonstock->name ?? '-')->toArray();
        return json_encode($parsed);
    }
@endphp

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
                <div class="pull-right">
                    @php
                        $role = Auth::user()->role;
                    @endphp
                    @if ($role->hasPermissionTo('expense.list.approve.farm'))
                    <a href="javascript:void(0)" type="button" class="btn btn-outline-success waves-effect" data-toggle="modal" data-target="#bulk-approve" data-role="Manager Farm">
                        Approve Mgr. Farm
                    </a>
                    @endif
                    @if ($role->hasPermissionTo('expense.list.approve.finance'))
                    <a href="javascript:void(0)" type="button" class="btn btn-outline-success waves-effect" data-toggle="modal" data-target="#bulk-approve" data-role="Manager Finance">
                        Approve Mgr. Finance
                    </a>
                    @endif
                    <a href="{{ route('expense.list.add') }}" type="button" class="btn btn-outline-primary waves-effect">Tambah</a>
                </div>
            </div>
            @php
                $arrRows = [10,20,50,100];
                $categoryExpense = App\Constants::EXPENSE_CATEGORY;
                $statusPayment = App\Constants::EXPENSE_PAYMENT_STATUS;
                $statusExpense = App\Constants::EXPENSE_STATUS;
            @endphp
            <div class="card-body">
                <div class="card-datatable">
                    <div class="row mb-1">
                        <div class="col-12">
                            <form action="{{ route('expense.list.index') }}">
                                <div class="row d-flex align-items-end">
                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label for="stock_type">Area</label>
                                            <select name="location[area_id]" id="area_id" class="form-control">
                                                @if (request()->has('location') && isset(request()->get('location')['area_id']))
                                                @php
                                                    $areaId = request()->get('location')['area_id'];
                                                @endphp
                                                <option value="{{ $areaId }}" selected> {{ \App\Models\DataMaster\Area::find($areaId)->name }}</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label for="stock_type">Lokasi</label>
                                            <select name="location_id" id="location_id" class="form-control" >
                                                @if (request()->has('location_id') && @request()->get('location_id'))
                                                @php
                                                    $locationiId = request()->get('location_id');
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
                                            <label for="stock_type">Pengaju</label>
                                            <select name="created_by" id="created_by" class="form-control">
                                                @if (request()->has('created_by') && @request()->get('created_by'))
                                                @php
                                                    $createdBy = request()->get('created_by')
                                                @endphp
                                                <option value="{{ $createdBy }}" selected> {{ \App\Models\UserManagement\User::find($createdBy)->name }}</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label for="stock_type">Vendor</label>
                                            <select name="supplier_id" id="supplier_id" class="form-control">
                                                @if (request()->has('supplier_id') && @request()->get('supplier_id'))
                                                @php
                                                    $supplierId = request()->get('supplier_id')
                                                @endphp
                                                <option value="{{ $supplierId }}" selected> {{ \App\Models\DataMaster\Supplier::find($supplierId)->name }}</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label for="category">Kategori</label>
                                            <select name="category" id="category" class="form-control">
                                                <option value="-all" {{ ! request()->has('category') || request()->get('category') == '-all' ? 'selected' : '' }}>Semua</option>
                                                @foreach ($categoryExpense as $key => $item)
                                                    <option value="{{$key}}" {{ request()->has('category') && request()->get('category') == $key ? 'selected' : '' }}>{{ $item }}</option>
                                                @endforeach
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
                                            <label for="expense_status">Status Biaya</label>
                                            <select name="expense_status" id="expense_status" class="form-control">
                                                <option value="-all" {{ ! request()->has('expense_status') || request()->get('expense_status') == '-all' ? 'selected' : '' }}>Semua</option>
                                                @foreach ($statusExpense as $key => $item)
                                                    <option value="{{$key}}" {{ request()->has('expense_status') && request()->get('expense_status') == $key ? 'selected' : '' }}>{{ $item }}</option>
                                                @endforeach
                                            </select>
                                        </div>
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
                                            <a href="{{ route('expense.list.index') }}"  class="btn btn-warning">Reset</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="table-responsive mb-2">
                        <form method="post" action="{{ route('expense.list.approve.bulk') }}" id="form-approve">
                        {{csrf_field()}}
                        <table id="datatable" class="table table-bordered table-striped w-100">
                            <thead class="text-center">
                                <th>
                                    @if (Auth::user()->role->hasPermissionTo('expense.list.approve.farm'))
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="checkAllFarm">
                                        <label class="custom-control-label" for="checkAllFarm"></label>
                                    </div>
                                    @endif
                                </th>
                                <th>
                                    @if (Auth::user()->role->hasPermissionTo('expense.list.approve.finance'))
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="checkAllFinance">
                                        <label class="custom-control-label" for="checkAllFinance"></label>
                                    </div>
                                    @endif
                                </th>
                                <th>ID</th>
                                <th>Lokasi</th>
                                <th>Kategori</th>
                                <th>Tanggal</th>
                                <th>Nama Pengaju</th>
                                <th>Vendor</th>
                                <th>Biaya Utama</th>
                                <th>Nominal (Rp)</th>
                                <th>Sudah Bayar (Rp)</th>
                                <th>Sisa Bayar (Rp)</th>
                                <th>Status Pencairan</th>
                                <th>Status Biaya</th>
                                <th>Aksi</th>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
                                    <tr>
                                        <td>
                                            @if (Auth::user()->role->hasPermissionTo('expense.list.approve.farm'))
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input {{ $item->expense_status === array_search('Approval Manager', \App\Constants::EXPENSE_STATUS) && $item->is_approved !== 0 ? 'select-row-farm' : ''  }}" name="farm_expense_ids[]" id="farm-expense-id-{{ $item->expense_id }}" value="{{ $item->expense_id }}" {{ $item->expense_status === array_search('Approval Manager', \App\Constants::EXPENSE_STATUS) && $item->is_approved !== 0 ? '' : 'disabled' }}>
                                                <label class="custom-control-label" for="farm-expense-id-{{ $item->expense_id }}"></label>
                                            </div>
                                            @endif
                                        </td>
                                        <td>
                                            @if (Auth::user()->role->hasPermissionTo('expense.list.approve.finance'))
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input {{ $item->expense_status === array_search('Approval Finance', \App\Constants::EXPENSE_STATUS) && $item->is_approved !== 0 ? 'select-row-finance' : ''  }}" name="finance_expense_ids[]" id="finance-expense-id-{{ $item->expense_id }}" value="{{ $item->expense_id }}" {{ $item->expense_status === array_search('Approval Finance', \App\Constants::EXPENSE_STATUS) && $item->is_approved !== 0 ? '' : 'disabled' }}>
                                                <label class="custom-control-label" for="finance-expense-id-{{ $item->expense_id }}"></label>
                                            </div>
                                            @endif
                                        </td>
                                        <td>{{ $item->id_expense }}</td>
                                        <td>{{ $item->location->name }}</td>
                                        <td>
                                            @switch($item->category)
                                                @case(1)
                                                    <span>Biaya Operasional</span>
                                                    @break
                                                @case(2)
                                                    <span>Bukan BOP</span>
                                                    @break
                                                @default
                                                    <span>N/A</span>
                                            @endswitch
                                        </td>
                                        <td>{{ date('d-M-Y', strtotime($item->created_at)) }}</td>
                                        <td>{{ $item->created_user->name }}</td>
                                        <td>{{ $item->supplier->name ?? '-' }}</td>
                                        @if (count($item->expense_main_prices) > 1)
                                            <td>
                                                <a href="javascript:void(0)"
                                                    data-toggle="modal"
                                                    data-target="#itemModal"
                                                    data-item="{{ jsonMainPrices($item->expense_main_prices) }}" >
                                                    Lihat {{ count($item->expense_main_prices) }} Biaya
                                                </a>
                                            </td>
                                        @else
                                            <td>{{ $item->expense_main_prices->first()->nonstock->name ?? '-' }}</td>
                                        @endif
                                        <td class="text-right text-primary">{{ \App\Helpers\Parser::toLocale($item->grand_total) }}</td>
                                        <td class="text-right text-success">{{ \App\Helpers\Parser::toLocale($item->is_paid) }}</td>
                                        <td class="text-right text-danger">{{ \App\Helpers\Parser::toLocale($item->not_paid) }}</td>
                                        <td>
                                            @php
                                                $statusPayment = App\Constants::EXPENSE_PAYMENT_STATUS;
                                            @endphp
                                            @switch($item->payment_status)
                                                @case(0)
                                                    <div class="badge badge-pill badge-secondary">{{ $statusPayment[$item->payment_status] }}</div>
                                                    @break
                                                @case(1)
                                                    <div class="badge badge-pill badge-warning">{{ $statusPayment[$item->payment_status] }}</div>
                                                    @break
                                                @case(2)
                                                    <div class="badge badge-pill badge-success">{{ $statusPayment[$item->payment_status] }}</div>
                                                    @break
                                                @default
                                                    <div class="badge badge-pill badge-primary">{{ $statusPayment[$item->payment_status] }}</div>
                                            @endswitch
                                        </td>
                                        <td class="text-center">
                                            @php
                                                    $statusExpense = App\Constants::EXPENSE_STATUS;

                                                    $show_status = $item->is_rejected ? 2 : $item->expense_status;
                                            @endphp
                                            @switch($show_status)
                                                @case(0)
                                                    <div class="badge badge-pill badge-secondary">{{ $statusExpense[$show_status] }}</div>
                                                    @break
                                                @case(1)
                                                    <div class="badge badge-pill badge-warning">{{ $statusExpense[$show_status] }}</div>
                                                    @break
                                                @case(2)
                                                    <div class="badge badge-pill badge-danger">{{ $statusExpense[$show_status] }}</div>
                                                    @break
                                                @case(3)
                                                    <div class="badge badge-pill" style="background-color: #b8654e">{{ $statusExpense[$show_status] }}</div>
                                                    @break
                                                @case(4)
                                                    <div class="badge badge-pill" style="background-color: #c0b408">{{ $statusExpense[$show_status] }}</div>
                                                    @break
                                                @case(5)
                                                    <div class="badge badge-pill" style="background-color: #0bd3a8">{{ $statusExpense[$show_status] }}</div>
                                                    @break
                                                @case(6)
                                                    <div class="badge badge-pill badge-success">{{ $statusExpense[$show_status] }}</div>
                                                    @break
                                                @default
                                                    <div class="badge badge-pill badge-primary">{{ $statusExpense[$show_status] }}</div>
                                            @endswitch
                                        </td>
                                        <td>
                                            <div class="dropdown dropleft" style="position: static;">
                                                <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-toggle="dropdown">
                                                    <i data-feather="more-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" href="{{ route('expense.list.detail', $item->expense_id) }}">
                                                        <i data-feather='eye' class="mr-50"></i>
                                                        <span>Lihat Detail</span>
                                                    </a>
                                                    @if (@$item->expense_status >= array_search('Pencairan', \App\Constants::EXPENSE_STATUS))
                                                    <a class="dropdown-item" href="{{ route('expense.list.disburse.index', $item->expense_id) }}">
                                                        <i data-feather="credit-card" class="mr-50"></i>
                                                        <span>Pencairan</span>
                                                    </a>
                                                    @endif
                                                    @if(@$item->expense_status === array_search('Realisasi', \App\Constants::EXPENSE_STATUS))
                                                    <a class="dropdown-item" href="{{ route('expense.list.realization', $item->expense_id) }}">
                                                        <i data-feather='package' class="mr-50"></i>
                                                        <span>Realisasi</span>
                                                    </a>
                                                    @endif
                                                    @php
                                                    $approvalNotes = optional(collect(json_decode($item->approval_line))->where('is_approved', 0)->last())->notes ?? null;
                                                    @endphp
                                                    @if ($approvalNotes && @$item->is_approved === 0)
                                                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#notesModal" data-notes="{{ $approvalNotes }}">
                                                            <i data-feather="message-square" class="mr-50"></i>
                                                            <span>Catatan Penolakan</span>
                                                        </a>
                                                    @endif
                                                    <a class="dropdown-item item-delete-button text-danger" href="{{ route('expense.list.delete', ['expense' => $item->expense_id]) }}">
                                                        <i data-feather='trash' class="mr-50"></i>
                                                        <span>Hapus</span>
                                                    </a>
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
                            @if (auth()->user()->role->hasPermissionTo('expense.list.payment.add') && auth()->user()->role->hasPermissionTo('expense.list.payment.approve'))
                            @include('expense.list.sections.batch-upload-modal')
                            @endif
                        </div>
                        <div class="col-12 col-md-6 my-1">
                            <table class="table table-borderless">
                                <tbody class="text-right">
                                    <tr>
                                        <td class="text-primary">
                                            Total Biaya:
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

<!-- Modal Main Prices -->
<div class="modal fade" id="itemModal" tabindex="-1" role="dialog" aria-labelledby="itemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="itemModalLabel">List Biaya Utama</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered w-100">
                                <thead>
                                    <th>No</th>
                                    <th>Non Stock</th>
                                </thead>
                                <tbody id="itemContent">
                                    <tr>
                                        <td colspan="2" class="text-center"></td>
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

<!-- Modal Bulk Approval -->
<div class="modal fade text-left" id="bulk-approve" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel1">Konfirmasi Approve Biaya (<span class="bulk-approve-role"></span>)</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <br><p>Apakah kamu yakin ingin menyetujui data biaya ini sebagai <span class="bulk-approve-role"></span> ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="btn-bulk-approve">Ya</button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Tidak</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script src="{{asset('app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js')}}"></script>

<script src="{{ asset('app-assets/vendors/js/tables/datatable/datatables.buttons.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.html5.min.js') }}"></script>

<script src="{{ asset('app-assets/vendors/js/tables/datatable/jszip.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/pdfmake.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/vfs_fonts.js') }}"></script>

<script src="{{ asset('app-assets/vendors/js/extensions/sweetalert2.all.min.js') }}"></script>

<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>

<script>
    $(function () {
        const areaIdRoute = '{{ route("data-master.area.search") }}';
        const locationIdRoute = '{{ route("data-master.location.search") }}';
        const userIdRoute = '{{ route("user-management.user.search") }}';
        const supplierIdRoute = '{{ route("data-master.supplier.search") }}';

        initSelect2($('select#created_by'), 'Pilih Pengaju', userIdRoute, '', { allowClear: true });
        initSelect2($('select#supplier_id'), 'Pilih Vendor', supplierIdRoute, '', { allowClear: true });

        initSelect2($('select#payment_status'), 'Pilih Status Pembayaran');
        initSelect2($('select#expense_status'), 'Pilih Status Biaya');
        initSelect2($('select#category'), 'Pilih Kategori');

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
                var button = $(event.relatedTarget);
                var notes = button.data('notes') || '-';
                var modal = $(this);
                modal.find('#notesContent').text(notes);
            });

            $('#itemModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var mainPrice = button.data('item');

                var modal = $(this);

                // Cek apakah data ada
                var detailHtml = '';
                $.each(mainPrice, function(index, item) {
                    detailHtml += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${item ?? '-'}</td>
                        </tr>
                    `;
                });
                // Update konten modal
                modal.find('#itemContent').html(detailHtml);
            });

            $('#bulk-approve').on('show.bs.modal', function (event) {
                const role = $(event.relatedTarget).data('role');

                $(this).find('.bulk-approve-role').text(role);
                $('#btn-bulk-approve').click(function (e) {
                    $('#form-approve').submit();
                });
            });

            $('#checkAllFarm').change(function (e) {
                e.preventDefault();
                if ($(this).is(':checked')) {
                    $('.select-row-farm').prop('checked', true);
                } else {
                    $('.select-row-farm').prop('checked', false);
                }
            });

            $('#checkAllFinance').change(function (e) {
                e.preventDefault();
                if ($(this).is(':checked')) {
                    $('.select-row-finance').prop('checked', true);
                } else {
                    $('.select-row-finance').prop('checked', false);
                }
            });
        });
    });
</script>

@endsection
