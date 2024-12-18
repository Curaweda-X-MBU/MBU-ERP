@extends('templates.main')
@section('title', $title)
@section('content')

<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/extensions/sweetalert2.min.css') }}" />

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ $title }}</h4>
                <div class="float-right">
                    <button class="btn btn-outline-secondary dropdown-toggle waves-effect" type="button" id="exportDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Eksport
                    </button>
                    <div class="dropdown-menu" aria-labelledby="exportDropdown">
                        <button id="exportExcel" class="dropdown-item w-100" href="link_to_excel_export">Excel</button>
                        <button id="exportPdf" class="dropdown-item w-100" href="link_to_pdf_export">PDF</button>
                    </div>
                    <a href="{{ route('marketing.list.add') }}" type="button" class="btn btn-outline-primary waves-effect">Tambah</a>
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
                                <th>Pelanggan</th>
                                <th>Unit Bisnis</th>
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
                                        <td>{{ $item->customer->name }}</td>
                                        <td>{{ $item->company->alias }}</td>
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
                                            <div class="dropdown dropleft">
                                                <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-toggle="dropdown">
                                                    <i data-feather="more-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item item-delete-button"
                                                        href="{{ route('marketing.list.delete', $item->marketing_id) }}"
                                                    >
                                                        <i data-feather='trash' class="mr-50"></i>
                                                        <span>Hapus</span>
                                                    </a>
                                                    <a class="dropdown-item" href="{{ route('marketing.list.detail', $item->marketing_id) }}">
                                                        <i data-feather='eye' class="mr-50"></i>
                                                        <span>Lihat Detail</span>
                                                    </a>
                                                    <a class="dropdown-item" href="{{ route('marketing.list.realization', $item->marketing_id) }}">
                                                        <i data-feather='package' class="mr-50"></i>
                                                        <span>Tambah Realisasi</span>
                                                    </a>
                                                    <a class="dropdown-item" href="{{ route('marketing.payment.index', $item->marketing_id) }}">
                                                        <i data-feather="credit-card" class="mr-50"></i>
                                                        <span>Tambah Pembayaran</span>
                                                    </a>
                                                    <a class="dropdown-item" href="{{ route('marketing.return.add', $item->marketing_id) }}">
                                                        <i data-feather="corner-down-left" class="mr-50"></i>
                                                        <span>Retur</span>
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
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

<script>
    $(function () {
        $('#datatable').DataTable({
            // scrollX: true,
            dom: 'Bfrtip',
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
            drawCallback: function( settings ) {
                feather.replace();
            },
            order: [[0, 'desc']],
        });

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
