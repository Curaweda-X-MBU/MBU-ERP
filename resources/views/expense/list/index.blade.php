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
                    {{-- TABLE FILTER::START --}}
                    <div class="dropdown d-inline">
                        <button class="btn btn-outline-secondary btn-icon waves-effect" type="button" data-toggle="dropdown">
                            <i data-feather="filter"></i>
                        </button>
                        <ul class="dropdown-menu" id="filterDropdown" aria-labelledby="filterDropdown">
                            <div class="dropdown-item dropleft autoclose">
                                <a class="stretched-link d-flex align-items-center"><i class="mr-2" data-feather="chevron-left"></i> Kategori</a>
                                <ul class="dropdown-menu" id="filterCategory">
                                    @foreach (\App\Constants::EXPENSE_CATEGORY as $category)
                                    <a class="dropdown-item">{{ $category }}</a>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="dropdown-item dropleft autoclose">
                                <a class="stretched-link d-flex align-items-center"><i class="mr-2" data-feather="chevron-left"></i> Status Pencairan</a>
                                <ul class="dropdown-menu" id="filterPaymentStatus">
                                    <a class="dropdown-item">Tempo</a>
                                    <a class="dropdown-item">Dibayar Sebagian</a>
                                    <a class="dropdown-item">Dibayar Penuh</a>
                                </ul>
                            </div>
                            <div class="dropdown-item dropleft autoclose">
                                <a class="stretched-link d-flex align-items-center"><i class="mr-2" data-feather="chevron-left"></i> Status</a>
                                <ul class="dropdown-menu" id="filterExpenseStatus">
                                    @foreach (\App\Constants::EXPENSE_STATUS as $status)
                                    <a class="dropdown-item">{{ $status }}</a>
                                    @endforeach
                                </ul>
                            </div>
                        </ul>
                    </div>
                    {{-- TABLE FILTER::END --}}
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
            <div class="card-body">
                <div class="card-datatable">
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
                                <th>expense_id</th>
                                <th>expense_status</th>
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
                                {{-- FILTER PURPOSES FIELDS::START --}}
                                <th>location_id</th>
                                <th>created_by</th>
                                <th>supplier_id</th>
                                {{-- FILTER PURPOSES FIELDS::END --}}
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
                                        <td>{{ $item->expense_id }}</td>
                                        <td>{{ $item->expense_status == 0 ? 0 : 1 }}</td>
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
                                        <td>{{ $item->location_id }}</td>
                                        <td>{{ $item->created_by }}</td>
                                        <td>{{ $item->supplier_id ?? null }}</td>
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
                                            Rp. <span id="grand_total">0,00</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-success">
                                            Total Sudah Dibayar:
                                        </td>
                                        <td class="font-weight-bolder text-success" style="font-size: 1.2em">
                                            Rp. <span id="is_paid">0,00</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-danger">
                                            Total Belum Dibayar:
                                        </td>
                                        <td class="font-weight-bolder text-danger" style="font-size: 1.2em">
                                            Rp. <span id="not_paid">0,00</span>
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
        const $table = $('#datatable').DataTable({
            dom: '<"#filter_wrapper"<"#filter_left"l>f>r<"custom-table-wrapper"t>ip',
            drawCallback: function(settings) {
                // NOTE: TABLE SELECT FILTER::START
                // ?: Location
                if (!$('#location_slice').length) {
                    $('#filter_wrapper')
                        .addClass('d-flex flex-wrap justify-content-between align-items-center')
                        .css('gap', '1rem');
                    $('#filter_left')
                        .addClass('d-flex flex-wrap justify-content-between align-items-center')
                        .css('gap', '1rem')
                        .prepend(
                            `
                                <div class="input-group">
                                    <div>
                                        <select id="location_slice" class="form-control" style="width: auto;"></select>
                                    </div>
                                    <div class="input-group-append">
                                        <button id="location_slice_clear" class="btn btn-icon btn-outline-secondary">
                                            <i data-feather="x"></i>
                                        </buton>
                                    </div>
                                </div>
                            `
                        );
                    var locationIdRoute = '{{ route("data-master.location.search") }}';
                    initSelect2($('#location_slice'), 'Filter Lokasi', locationIdRoute);

                    $('#location_slice_clear').on('click', function() {
                        $('#location_slice').val(null).trigger('change');
                    });

                    $('#location_slice').on('select2:select change', function() {
                        $table.columns(16).search('').draw();
                        $table.columns(16).search($(this).val() ?? '').draw();
                    });
                }

                // ?: Created By
                if (!$('#created_by_slice').length) {
                    $('#filter_wrapper')
                        .addClass('d-flex flex-wrap justify-content-between align-items-center')
                        .css('gap', '1rem');
                    $('#filter_left')
                        .addClass('d-flex flex-wrap justify-content-between align-items-center')
                        .css('gap', '1rem')
                        .append(
                            `
                                <div class="input-group">
                                    <div>
                                        <select id="created_by_slice" class="form-control" style="width: auto;"></select>
                                    </div>
                                    <div class="input-group-append">
                                        <button id="created_by_slice_clear" class="btn btn-icon btn-outline-secondary">
                                            <i data-feather="x"></i>
                                        </buton>
                                    </div>
                                </div>
                            `
                        );
                    var createdByIdRoute = '{{ route("user-management.user.search") }}';
                    initSelect2($('#created_by_slice'), 'Filter Pengaju', createdByIdRoute);

                    $('#created_by_slice_clear').on('click', function() {
                        $('#created_by_slice').val(null).trigger('change');
                    });

                    $('#created_by_slice').on('select2:select change', function() {
                        $table.columns(17).search('').draw();
                        $table.columns(17).search($(this).val() ?? '').draw();
                    });
                }

                // ?: Suppier
                if (!$('#supplier_slice').length) {
                    $('#filter_wrapper')
                        .addClass('d-flex flex-wrap justify-content-between align-items-center')
                        .css('gap', '1rem');
                    $('#filter_left')
                        .addClass('d-flex flex-wrap justify-content-between align-items-center')
                        .css('gap', '1rem')
                        .append(
                            `
                                <div class="input-group">
                                    <div>
                                        <select id="supplier_slice" class="form-control" style="width: auto;"></select>
                                    </div>
                                    <div class="input-group-append">
                                        <button id="supplier_slice_clear" class="btn btn-icon btn-outline-secondary">
                                            <i data-feather="x"></i>
                                        </buton>
                                    </div>
                                </div>
                            `
                        );
                    var supplierIdRoute = '{{ route("data-master.supplier.search") }}';
                    initSelect2($('#supplier_slice'), 'Filter Vendor', supplierIdRoute);

                    $('#supplier_slice_clear').on('click', function() {
                        $('#supplier_slice').val(null).trigger('change');
                    });

                    $('#supplier_slice').on('select2:select change', function() {
                        $table.columns(18).search('').draw();
                        $table.columns(18).search($(this).val() ?? '').draw();
                    });
                }

                // ?: css wrap for length filter
                $("#filter_left > div").css('flex', '1 0 40%').css('width', '100%');
                $("#filter_left > div > *:first-child").css('flex', '1 0 40%');
                // NOTE: TABLE SELECT FILTER::END

                let grandTotalSum = 0;
                let isPaidSum = 0;
                let notPaidSum = 0;
                // $table.rows({ filter: 'applied' }).every(function() {
                //     const data = this.data();
                //     const grandTotal = parseLocaleToNum(data[11]);
                //     const isPaid = parseLocaleToNum(data[12]);
                //     const notPaid = parseLocaleToNum(data[13]);

                //     grandTotalSum += grandTotal;
                //     isPaidSum += isPaid;
                //     notPaidSum += notPaid;
                // });

                const $grandTotal = $("#grand_total");
                const $isPaid = $('#is_paid');
                const $notPaid = $('#not_paid');

                $grandTotal.text(parseNumToLocale(grandTotalSum));
                $isPaid.text(parseNumToLocale(isPaidSum));
                $notPaid.text(parseNumToLocale(notPaidSum));

                feather.replace();
            },
            order: [[3, 'asc'], [2, 'desc']],
        });

        $table.columns([2, 3, 16, 17, 18]).visible(false);
        @if (! Auth::user()->role->hasPermissionTo('expense.list.approve.farm'))
        $table.columns(0).visible(false);
        @endif
        @if (! Auth::user()->role->hasPermissionTo('expense.list.approve.finance'))
        $table.columns(1).visible(false);
        @endif

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

        // NOTE: TABLE FILTER::START
        function setupDropdownFilter(selector, column, $table) {
            const resetClass = 'reset';
            const reset = `<a class="dropdown-item ${resetClass} active">Semua</a>`;
            $(selector).prepend(reset);
            $(selector).on('click', '.dropdown-item', function(e) {
                e.stopPropagation();
                $(this).siblings('.dropdown-item').removeClass('active');
                $(this).addClass('active')
                $table.columns(column).search('').draw();
                if (!$(this).hasClass(resetClass)) {
                    $table.columns(column).search($(this).text()).draw();
                }
            });

            $(selector).siblings('a').on('mouseover', function() {
                $(this).dropdown('show');
                $(this).parent('.autoclose').siblings('.autoclose').each(function() {
                    $(this).find('.dropdown-menu').dropdown('hide');
                });
            });
            $(selector).on('mouseleave', function() {
                $(this).dropdown('hide');
            });
        }
        setupDropdownFilter('#filterCategory', 6, $table);
        setupDropdownFilter('#filterPaymentStatus', 14, $table);
        setupDropdownFilter('#filterExpenseStatus', 15, $table);
        // NOTE: TABLE FILTER::END
    });
</script>

@endsection
