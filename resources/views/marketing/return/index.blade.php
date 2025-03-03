@extends('templates.main')
@section('title', $title)
@section('content')

@php
    $statusPayment = App\Constants::MARKETING_PAYMENT_STATUS;
    $statusReturn = App\Constants::MARKETING_RETURN_STATUS;
@endphp

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ $title }}</h4>
                <div class="float-right">
                    <button class="btn btn-outline-secondary dropdown-toggle waves-effect"
                        type="button"
                        id="exportDropdown"
                        data-toggle="dropdown"
                        aria-haspopup="true"
                        aria-expanded="false"
                    >
                        Export
                    </button>
                    <div class="dropdown-menu" aria-labelledby="exportDropdown">
                        <button id="exportExcel" class="dropdown-item w-100">Excel</button>
                        <button id="exportPdf" class="dropdown-item w-100">PDF</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="card-datatable">
                    <div class="table-responsive mb-2">
                        <table id="datatable" class="table table-bordered table-striped w-100">
                            <thead class="text-center">
                                <th>No. DO</th>
                                <th>No Faktur Retur</th>
                                <th>Tanggal Retur</th>
                                <th>Pelanggan</th>
                                <th>Unit Bisnis</th>
                                <th>Nominal Pembayaran (Rp)</th>
                                <th>Nominal Retur (Rp)</th>
                                <th>Nominal Sudah Retur (Rp)</th>
                                <th>Nominal Sisa Retur (Rp)</th>
                                <th>Status Retur Pembayaran</th>
                                <th>Status Retur</th>
                                <th>Aksi</th>
                            </thead>
                            <tbody>
                                @if (isset($data))
                                    @foreach ($data as $item)
                                        <tr>
                                            <td>{{ $item->marketing->id_marketing }}</td>
                                            <td>{{ $item->invoice_number ?? '-' }}</td>
                                            <td>{{ date('d-M-Y', strtotime($item->return_at)) }}</td>
                                            <td>{{ $item->marketing->customer->name }}</td>
                                            <td>{{ $item->marketing->company->alias }}</td>
                                            <td class="text-warning">{{ \App\Helpers\Parser::toLocale($item->marketing->is_paid) }}</td>
                                            <td class="text-primary">{{ \App\Helpers\Parser::toLocale($item->total_return) }}</td>
                                            <td class="text-success">{{ \App\Helpers\Parser::toLocale($item->is_returned) }}</td>
                                            <td class="text-danger" >{{ \App\Helpers\Parser::toLocale($item->total_return - $item->is_returned) }}</td>
                                            <td class="text-center">
                                                @switch($item->payment_return_status)
                                                    @case(1)
                                                        <div class="badge badge-pill badge-warning">{{ $statusPayment[$item->payment_return_status] }}</div>
                                                        @break
                                                    @case(2)
                                                        <div class="badge badge-pill badge-success">{{ $statusPayment[$item->payment_return_status] }}</div>
                                                        @break
                                                    @case(3)
                                                        <div class="badge badge-pill badge-primary">{{ $statusPayment[$item->payment_return_status] }}</div>
                                                        @break
                                                    @default
                                                        <div class="badge badge-pill badge-danger">{{ $statusPayment[$item->payment_return_status] }}</div>
                                                @endswitch
                                            </td>
                                            <td class="text-center">
                                                @switch($item->return_status)
                                                    @case(0)
                                                        <div class="badge badge-pill badge-danger">{{ $statusReturn[$item->return_status] }}</div>
                                                        @break
                                                    @case(1)
                                                        <div class="badge badge-pill badge-warning">{{ $statusReturn[$item->return_status] }}</div>
                                                        @break
                                                    @default
                                                        <div class="badge badge-pill badge-primary">{{ $statusReturn[$item->return_status] }}</div>
                                                @endswitch
                                            </td>
                                            <td>
                                                <div class="dropdown dropleft">
                                                    <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-toggle="dropdown">
                                                        <i data-feather="more-vertical"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        @if (auth()->user()->role->hasPermissionTo('marketing.return.detail'))
                                                        <a class="dropdown-item" href="{{ route('marketing.return.detail', $item->marketing_id) }}">
                                                            <i data-feather='eye' class="mr-50"></i>
                                                            <span>Lihat Detail</span>
                                                        </a>
                                                        @endif
                                                        @if (
                                                            auth()->user()->role->hasPermissionTo('marketing.return.payment.index')
                                                            && $item->return_status == array_search('Disetujui', \App\Constants::MARKETING_RETURN_STATUS)
                                                            )
                                                        <a class="dropdown-item" href="{{ route('marketing.return.payment.index', $item->marketing_id) }}">
                                                            <i data-feather="credit-card" class="mr-50"></i>
                                                            <span>Pembayaran Retur</span>
                                                        </a>
                                                        @endif
                                                        @if (auth()->user()->role->hasPermissionTo('marketing.return.delete'))
                                                        <a class="dropdown-item item-delete-button text-danger"
                                                            href="{{ route('marketing.return.delete', $item->marketing_return_id) }}"
                                                        >
                                                            <i data-feather='trash' class="mr-50"></i>
                                                            <span>Hapus</span>
                                                        </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                <tr>
                                    <td class="text-center" colspan="9">Belum ada data retur</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12 col-md-6 offset-md-6">
                            <table class="table table-borderless">
                                <tbody class="text-right">
                                    <tr>
                                        <td class="text-warning">
                                            Total Terbayar:
                                        </td>
                                        <td class="font-weight-bolder text-warning" style="font-size: 1.2em">
                                            Rp. <span id="is_paid">0,00</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Total Retur:
                                        </td>
                                        <td class="font-weight-bolder" style="font-size: 1.2em">
                                            Rp. <span id="total_return">0,00</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-success">
                                            Total Sudah Diretur:
                                        </td>
                                        <td class="font-weight-bolder text-success" style="font-size: 1.2em">
                                            Rp. <span id="is_returned">0,00</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-danger">
                                            Total Belum Diretur:
                                        </td>
                                        <td class="font-weight-bolder text-danger" style="font-size: 1.2em">
                                            Rp. <span id="not_returned">0,00</span>
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

<script src="{{asset('app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js')}}"></script>

<script src="{{ asset('app-assets/vendors/js/tables/datatable/datatables.buttons.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.html5.min.js') }}"></script>

<script src="{{ asset('app-assets/vendors/js/tables/datatable/jszip.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/pdfmake.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/vfs_fonts.js') }}"></script>

<script src="{{ asset('app-assets/vendors/js/extensions/sweetalert2.all.min.js') }}"></script>


<script>
    $(function () {
        var $table = $('#datatable').DataTable({
            dom: 'B<"d-flex justify-content-between"lf>r<"custom-table-wrapper"t>ip',
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
                let isPaidSum = 0;
                let totalReturnSum = 0;
                let isReturnedSum = 0;

                $table.rows({ filter: 'applied' }).every(function() {
                    const data = this.data();
                    const isPaid = parseLocaleToNum(data[5]);
                    const totalReturn = parseLocaleToNum(data[6]);
                    const isReturned = parseLocaleToNum(data[7]);

                    isPaidSum += isPaid;
                    totalReturnSum += totalReturn;
                    isReturnedSum += isReturned;
                });

                const $isPaid = $('#is_paid');
                const $totalReturn = $('#total_return');
                const $isReturned = $('#is_returned');
                const $notReturned = $('#not_returned');

                $isPaid.text(parseNumToLocale(isPaidSum));
                $totalReturn.text(parseNumToLocale(totalReturnSum));
                $isReturned.text(parseNumToLocale(isReturnedSum));
                $notReturned.text(parseNumToLocale(totalReturnSum - isReturnedSum));

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
            e.preventDefailt();

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
    });
</script>

@endsection
