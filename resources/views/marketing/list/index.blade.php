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
                    <a href="{{ route('marketing.list.add') }}" type="button" class="btn btn-outline-primary waves-effect">Tambah</a>
                </div>
            </div>
            <div class="card-body">
                <div class="card-datatable">
                    <div class="table-responsive mb-2">
                        <table id="datatable" class="table table-bordered table-striped w-100">
                            <thead>
                                <th>Grand Total</th>
                                <th>Is Paid</th>
                                <th>No.DO</th>
                                <th>Tanggal Penjualan</th>
                                <th>Tanggal Realisasi</th>
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
                                @php
                                    $nominalPenjualan = $item->grand_total;
                                    $nominalSisaBayar = $item->marketing_payments->sum('payment_nominal');
                                @endphp
                                    <tr>
                                        <td>{{ $item->grand_total }}</td>
                                        <td>{{ $item->is_paid }}</td>
                                        <td>{{ $item->id_marketing }}</td>
                                        <td>{{ date('d-M-Y', strtotime($item->sold_at)) }}</td>
                                        <td>{{ isset($item->realized_at) ? date('d-M-Y', strtotime($item->realized_at)) : '-' }}</td>
                                        <td>{{ $item->customer_id }}</td>
                                        <td>{{ $item->customer->name }}</td>
                                        <td>{{ $item->company->alias }}</td>
                                        <td class="text-right text-primary">{{ \App\Helpers\Parser::toLocale($nominalPenjualan) }}</td>
                                        <td class="text-right text-success">{{ \App\Helpers\Parser::toLocale($nominalSisaBayar) }}</td>
                                        <td class="text-right text-danger">{{ \App\Helpers\Parser::toLocale($nominalPenjualan - $nominalSisaBayar) }}</td>
                                        <td>
                                            @php
                                                $statusPayment = App\Constants::MARKETING_PAYMENT_STATUS;
                                            @endphp
                                            @switch($item->payment_status)
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
                                        <td>
                                            @php
                                                $statusMarketing = App\Constants::MARKETING_STATUS;
                                            @endphp
                                            @switch($item->marketing_status)
                                                @case(1)
                                                    <div class="badge badge-pill badge-warning">{{ $statusMarketing[$item->marketing_status] }}</div>
                                                    @break
                                                @case(2)
                                                    <div class="badge badge-pill badge-danger">{{ $statusMarketing[$item->marketing_status] }}</div>
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
                                                        <span>Lihat Detail</span>
                                                    </a>
                                                    @if ($item->marketing_status != 4)
                                                    <a class="dropdown-item" href="{{ route('marketing.list.realization', $item->marketing_id) }}">
                                                        <i data-feather='package' class="mr-50"></i>
                                                            <span>Tambah Realisasi</span>
                                                        </a>
                                                    @endif
                                                    <a class="dropdown-item" href="{{ route('marketing.list.payment.index', $item->marketing_id) }}">
                                                        <i data-feather="credit-card" class="mr-50"></i>
                                                        <span>Tambah Pembayaran</span>
                                                    </a>
                                                    <a class="dropdown-item" href="{{ route('marketing.return.add', $item->marketing_id) }}">
                                                        <i data-feather="corner-down-left" class="mr-50"></i>
                                                        <span>Retur</span>
                                                    </a>
                                                    <a class="dropdown-item item-delete-button text-danger"
                                                        href="{{ route('marketing.list.delete', $item->marketing_id) }}">
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
                        <div class="col-12 col-md-6 offset-md-6 my-1">
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

<script src="{{ asset('app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js') }}"></script>

<script src="{{ asset('app-assets/vendors/js/tables/datatable/datatables.buttons.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.html5.min.js') }}"></script>

<script src="{{ asset('app-assets/vendors/js/tables/datatable/jszip.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/pdfmake.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/vfs_fonts.js') }}"></script>

<script src="{{ asset('app-assets/vendors/js/extensions/sweetalert2.all.min.js') }}"></script>

<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>

<script>
    $(function () {
        var $table = $('#datatable').DataTable({
            // scrollX: true,
            dom: 'B<"#filter_wrapper"<"#filter_left"l>f>rtip',
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
                        $table.columns(5).search('').draw();
                        $table.columns(5).search($(this).val() ?? '').draw();
                    });
                }


                let grandTotalSum = 0;
                let isPaidSum = 0;

                $table.rows({ filter: 'applied' }).every(function() {
                    const data = this.data();
                    const grandTotal = parseFloat(data[0]) || 0;
                    const isPaid = parseFloat(data[1]) || 0;

                    grandTotalSum += grandTotal;
                    isPaidSum += isPaid;
                });

                const $grandTotal = $("#grand_total");
                const $isPaid = $('#is_paid');
                const $notPaid = $('#not_paid');

                $grandTotal.text(parseNumToLocale(grandTotalSum));
                $isPaid.text(parseNumToLocale(isPaidSum));
                $notPaid.text(parseNumToLocale(grandTotalSum - isPaidSum));

                feather.replace();
            },
            order: [[2, 'desc']],
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

        $table.columns(0).visible(false);
        $table.columns(1).visible(false);
        $table.columns(5).visible(false);

        $.ajax({
            method: 'get',
            url: '{{ route("data-master.company.search") }}',
        }).done(function(result) {
            result.forEach(function(company) {
                $('#filterCompany').append(`<a class="dropdown-item">${company.alias}</a>`);
            });
            setupDropdownFilter('#filterCompany', 7, $table);
        });
        setupDropdownFilter('#filterPaymentStatus', 11, $table);
        setupDropdownFilter('#filterMarketingStatus', 12, $table);

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
                window.location.href = e.target.href;
            });
        });
    });
</script>


@endsection
