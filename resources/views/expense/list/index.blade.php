@php
    $title = 'Biaya > List';
    $data = [
        [
            'location' => 'Singaparna',
            'category' => 1,
            'created_at' => '25-Dec-2024',
            'created_by' => 'Agus Saripudin',
            'grand_total' => 8020000,
            'expense_status' => 2,
        ],
        [
            'location' => 'Pangandaran',
            'category' => 2,
            'created_at' => '25-Dec-2024',
            'created_by' => 'Zaenaludin',
            'grand_total' => 8020000,
            'expense_status' => 1,
        ],
        [
            'location' => 'Pangandaran',
            'category' => 2,
            'created_at' => '25-Dec-2024',
            'created_by' => 'Agus Abdul Jalil',
            'grand_total' => 8020000,
            'expense_status' => 0,
        ],
    ];
@endphp

@extends('templates.main')
@section('title', $title)
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ $title }}</h4>
                <div class="float-right">
                    <button class="btn btn-outline-secondary dropdown-toggle waves-effect" type="button" id="exportDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Export
                    </button>
                    <div class="dropdown-menu" aria-labelledby="exportDropdown">
                        <button id="exportExcel" class="dropdown-item w-100">Excel</button>
                        <button id="exportPdf" class="dropdown-item w-100">PDF</button>
                    </div>
                    <a href="{{ route('expense.list.add') }}" type="button" class="btn btn-outline-primary waves-effect">Tambah</a>
                </div>
            </div>
            <div class="card-body">
                <div class="card-datatable">
                    <div class="table-responsive mb-2">
                        <table id="datatable" class="table table-bordered table-striped w-100">
                            <thead class="text-center">
                                <th>#</th>
                                <th>Lokasi</th>
                                <th>Kategori</th>
                                <th>Tanggal</th>
                                <th>Nama Pengaju</th>
                                <th>Nominal</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item['location'] }}</td>
                                        <td>
                                            @switch($item['category'])
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
                                        <td>{{ date('d-M-Y', strtotime($item['created_at'])) }}</td>
                                        <td>{{ $item['created_by'] }}</td>
                                        <td>Rp. {{ \App\Helpers\Parser::toLocale($item['grand_total']) }}</td>
                                        <td class="text-center">
                                            @switch($item['expense_status'])
                                                @case(0)
                                                    <div class="badge badge-pill badge-secondary">Draft</div>
                                                    @break
                                                @case(1)
                                                    <div class="badge badge-pill badge-warning">Pengajuan</div>
                                                    @break
                                                @case(2)
                                                    <div class="badge badge-pill badge-primary">Disetujui</div>
                                                    @break
                                                @case(3)
                                                    <div class="badge badge-pill badge-danger">Ditolak</div>
                                                    @break
                                                @default
                                                    <div class="badge badge-pill badge-secondary">N/A</div>
                                            @endswitch
                                        </td>
                                        <td>
                                            <div class="dropdown dropleft">
                                                <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-toggle="dropdown">
                                                    <i data-feather="more-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" href="">
                                                        <i data-feather='eye' class="mr-50"></i>
                                                        <span>Lihat Detail</span>
                                                    </a>
                                                    <a class="dropdown-item" href="">
                                                        <i data-feather="message-square" class="mr-50"></i>
                                                        <span>Catatan</span>
                                                    </a>
                                                    <a class="dropdown-item item-delete-button text-danger" href="">
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
        $('#datatable').DataTable({
            dom: 'B<"d-flex justify-content-between"lf>rtip',
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
            order: [[3, 'desc']],
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
                window.location.href = e.target.href;
            });
        });
    });
</script>

@endsection
