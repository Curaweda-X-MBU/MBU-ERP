@extends('templates.main')
@section('title', $title)
@section('content')

@php
    $statusPayment = App\Constants::MARKETING_PAYMENT_STATUS;
    $statusMarketing = App\Constants::MARKETING_STATUS;
@endphp

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
                    <div class="dropdown d-inline">
                        <button class="btn btn-outline-secondary btn-icon waves-effect" type="button" data-toggle="dropdown">
                            <i data-feather="filter"></i>
                        </button>
                        <ul class="dropdown-menu" id="filterDropdown" aria-labelledby="filterDropdown">
                            <div class="dropdown-item dropleft autoclose">
                                <a class="stretched-link d-flex align-items-center"><i class="mr-2" data-feather="chevron-left"></i> Unit Bisnis</a>
                                <ul class="dropdown-menu" id="filterCompany">
                                </ul>
                            </div>
                            <div class="dropdown-item dropleft autoclose">
                                <a class="stretched-link d-flex align-items-center"><i class="mr-2" data-feather="chevron-left"></i> Status Pembayaran</a>
                                <ul class="dropdown-menu" id="filterPaymentStatus">
                                    <a class="dropdown-item">Tempo</a>
                                    <a class="dropdown-item">Dibayar Sebagian</a>
                                    <a class="dropdown-item">Dibayar Penuh</a>
                                </ul>
                            </div>
                            <div class="dropdown-item dropleft autoclose">
                                <a class="stretched-link d-flex align-items-center"><i class="mr-2" data-feather="chevron-left"></i> Status</a>
                                <ul class="dropdown-menu" id="filterMarketingStatus">
                                    <a class="dropdown-item">Diajukan</a>
                                    <a class="dropdown-item">Penawaran</a>
                                    <a class="dropdown-item">Final</a>
                                    <a class="dropdown-item">Realisasi</a>
                                </ul>
                            </div>
                        </ul>
                    </div>
                    <div class="dropdown d-inline">
                        <button class="btn btn-outline-secondary dropdown-toggle waves-effect" type="button" id="exportDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Export
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                            <button id="exportExcel" class="dropdown-item w-100">Excel</button>
                            <button id="exportPdf" class="dropdown-item w-100">PDF</button>
                        </ul>
                    </div>
                    @if (auth()->user()->role->hasPermissionTo('marketing.list.add'))
                    <a href="{{ route('marketing.list.add') }}" type="button" class="btn btn-outline-primary waves-effect">Tambah</a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="card-datatable">
                    <div class="table-responsive mb-2">
                        <table id="datatable" class="table table-bordered table-striped w-100">
                            <thead>
                                <th>No.DO</th>
                                <th>Tanggal Penjualan</th>
                                <th>Tanggal Realisasi</th>
                                <th>Aging</th>
                                <th>customer_id</th>
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
                                        <td>{{ $item->customer_id }}</td>
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
                                                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#notesModal" data-notes="{{ $item->approval_notes }}">
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

<script src="{{ asset('app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js') }}"></script>

<script src="{{ asset('app-assets/vendors/js/tables/datatable/datatables.buttons.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.html5.min.js') }}"></script>

<script src="{{ asset('app-assets/vendors/js/tables/datatable/jszip.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/pdfmake.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/vfs_fonts.js') }}"></script>

<script src="{{ asset('app-assets/vendors/js/extensions/sweetalert2.all.min.js') }}"></script>

<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>

<script>
    $(function () {
        var $table = $('#datatable').DataTable({
            // scrollX: true,
            dom: 'B<"#filter_wrapper"<"#filter_left"l>f>r<"custom-table-wrapper"t>ip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    className: 'd-none datatable-hidden-excel-button',
                    exportOptions: {
                        columns: ':not(:last-child)',
                    },
                },
                {
                    extend: 'pdfHtml5',
                    className: 'd-none datatable-hidden-pdf-button',
                    exportOptions: {
                        columns: ':not(:last-child)',
                    },
                },
            ],
            drawCallback: function(settings) {
                if (!$('#customer_slice').length) {
                    $('#filter_wrapper')
                        .addClass('d-flex flex-wrap justify-content-between align-items-center')
                        .css('gap', '1rem');
                    $('#filter_left')
                        .addClass('d-flex flex-wrap justify-content-between align-items-center')
                        .css('gap', '1rem')
                        .append(
                            `
                                <div class="input-group" style="width: auto;">
                                    <div>
                                        <select id="customer_slice" class="form-control" style="width: auto;"></select>
                                    </div>
                                    <div class="input-group-append">
                                        <button id="customer_slice_clear" class="btn btn-icon btn-outline-secondary">
                                            <i data-feather="x"></i>
                                        </buton>
                                    </div>
                                </div>
                            `
                        );

                    var customerIdRoute = '{{ route("data-master.customer.search") }}';
                    initSelect2($('#customer_slice'), 'Filter Pelanggan', customerIdRoute);

                    $('#customer_slice_clear').on('click', function() {
                        $('#customer_slice').val(null).trigger('change');
                    });

                    $('#customer_slice').on('select2:select change', function() {
                        $table.columns(3).search('').draw();
                        $table.columns(3).search($(this).val() ?? '').draw();
                    });
                }


                let grandTotalSum = 0;
                let isPaidSum = 0;
                let notPaidSum = 0;

                $table.rows({ filter: 'applied' }).every(function() {
                    const data = this.data();
                    const grandTotal = parseLocaleToNum(data[7]);
                    const isPaid = parseLocaleToNum(data[8]);
                    const notPaid = parseLocaleToNum(data[9]);

                    grandTotalSum += grandTotal;
                    isPaidSum += isPaid;
                    notPaidSum += notPaid;
                });

                const $grandTotal = $("#grand_total");
                const $isPaid = $('#is_paid');
                const $notPaid = $('#not_paid');

                $grandTotal.text(parseNumToLocale(grandTotalSum));
                $isPaid.text(parseNumToLocale(isPaidSum));
                $notPaid.text(parseNumToLocale(notPaidSum));

                feather.replace();
            },
            order: [[0, 'desc']],
        });

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

        $table.columns(4).visible(false);

        $.ajax({
            method: 'get',
            url: '{{ route("data-master.company.search") }}',
        }).done(function(result) {
            result.forEach(function(company) {
                $('#filterCompany').append(`<a class="dropdown-item">${company.alias}</a>`);
            });
            setupDropdownFilter('#filterCompany', 5, $table);
        });
        setupDropdownFilter('#filterPaymentStatus', 9, $table);
        setupDropdownFilter('#filterMarketingStatus', 10, $table);

        $('#exportExcel').on('click', function() {
            $('.datatable-hidden-excel-button').trigger('click');
        });

        $('#exportPdf').on('click', function() {
            $('.datatable-hidden-pdf-button').trigger('click');
        });

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
        });
    });
</script>


@endsection
