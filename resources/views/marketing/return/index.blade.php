@extends('templates.main')
@section('title', $title)
@section('content')
@php
    $statusReturPembayaran = 1;
    $statusRetur = 2;

    // dd($data);
@endphp
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
                        <a class="dropdown-item" href="link_to_excel_export">Excel</a>
                        <a class="dropdown-item" href="link_to_pdf_export">PDF</a>
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
                                <th class="col-2">Pelanggan</th>
                                <th>Unit Bisnis</th>
                                <th>Status Retur Pembayaran</th>
                                <th>Status Retur</th>
                                <th>Total Retur (Rp)</th>
                                <th>Aksi</th>
                            </thead>
                            <tbody>
                                @if (isset($data))
                                @foreach ($data as $item)
                                    <tr>
                                        <td>{{ $item->marketing->id_marketing }}</td>
                                        <td>{{ $item->invoice_number }}</td>
                                        <td>{{ date('d-M-Y', strtotime($item->return_at)) }}</td>
                                        <td>{{ $item->marketing->customer->name }}</td>
                                        <td>{{ $item->marketing->company->alias }}</td>
                                        <td class="text-center">
                                            @switch($item->payment_return_status)
                                                @case(1)
                                                    <div class="badge badge-pill badge-warning">Tempo</div>
                                                    @break
                                                @case(2)
                                                    <div class="badge badge-pill badge-success">Dibayar Sebagian</div>
                                                    @break
                                                @case(3)
                                                    <div class="badge badge-pill badge-info">Dibayar Penuh</div>
                                                    @break
                                                @default
                                                    <div class="badge badge-pill badge-secondary">N/A</div>
                                            @endswitch
                                        </td>
                                        <td class="text-center">
                                            @switch($item->return_status)
                                                @case(1)
                                                    <div class="badge badge-pill badge-warning">Diajukan</div>
                                                    @break
                                                @case(2)
                                                    <div class="badge badge-pill badge-info">Disetujui</div>
                                                    @break
                                                @default
                                                    <div class="badge badge-pill badge-secondary">N/A</div>
                                            @endswitch
                                        </td>
                                        <td>{{ \App\Helpers\Parser::toLocale($item->total_return) }}</td>
                                        <td>
                                            <div class="dropdown dropleft">
                                                <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-toggle="dropdown">
                                                    <i data-feather="more-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" href="{{ route('marketing.return.edit', $item->marketing_id) }}">
                                                        <i data-feather="edit" class="mr-50"></i>
                                                        <span>Edit</span>
                                                    </a>
                                                    <a class="dropdown-item" href="{{ route('marketing.return.delete', $item->marketing_id) }}">
                                                        <i data-feather='trash' class="mr-50"></i>
                                                        <span>Hapus</span>
                                                    </a>
                                                    <a class="dropdown-item" href="{{ route('marketing.return.detail', $item->marketing_id) }}">
                                                        <i data-feather='eye' class="mr-50"></i>
                                                        <span>Lihat Detail</span>
                                                    </a>
                                                    <a class="dropdown-item" href="">
                                                        <i data-feather="credit-card" class="mr-50"></i>
                                                        <span>Pembayaran Retur</span>
                                                    </a>
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
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{asset('app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js')}}"></script>

<script>
    $(function () {
        $('#datatable').DataTable({
            drawCallback: function( settings ) {
                feather.replace();
            },
            order: [[0, 'desc']],
        });
    });
</script>

@endsection
