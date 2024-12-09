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
                                            Eksport
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="exportDropdown">
                                            <a class="dropdown-item" href="link_to_excel_export">Excel</a>
                                            <a class="dropdown-item" href="link_to_pdf_export">PDF</a>
                                        </div>
                                        <a href="" type="button" class="btn btn-outline-primary waves-effect">Tambah</a>
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
                                                    <th>Lokasi</th>
                                                    <th>Kandang/Hatchery</th>
                                                    <th>Status Pembayaran</th>
                                                    <th>Status</th>
                                                    <th>Aksi</th>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>DO.MBU.091224</td>
                                                        <td>12-Dec-2024</td>
                                                        <td></td>
                                                        <td>Abdul Aziz</td>
                                                        <td>MANBU</td>
                                                        <td>Ciamis</td>
                                                        <td>Landeuh</td>
                                                        <td>
                                                            <div class="badge badge-pill badge-primary">Dibayar Penuh</div>
                                                        </td>
                                                        <td>
                                                            <div class="badge badge-pill badge-success">Final</div>
                                                        </td>
                                                        <td>
                                                            <div class="dropdown dropleft">
                                                                <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-toggle="dropdown">
                                                                    <i data-feather="more-vertical"></i>
                                                                </button>
                                                                <div class="dropdown-menu">
                                                                    <a class="dropdown-item" href="">
                                                                        <i data-feather="edit" class="mr-50"></i>
                                                                        <span>Edit</span>
                                                                    </a>
                                                                    <a class="dropdown-item" href="">
                                                                        <i data-feather='trash' class="mr-50"></i>
                                                                        <span>Hapus</span>
                                                                    </a>
                                                                    <a class="dropdown-item" href="">
                                                                        <i data-feather='eye' class="mr-50"></i>
                                                                        <span>Lihat Detail</span>
                                                                    </a>
                                                                    <a class="dropdown-item" href="">
                                                                        <i data-feather='package' class="mr-50"></i>
                                                                        <span>Tambah Realisasi</span>
                                                                    </a>
                                                                    <a class="dropdown-item" href="">
                                                                        <i data-feather="credit-card" class="mr-50"></i>
                                                                        <span>Tambah Pembayaran</span>
                                                                    </a>
                                                                    <a class="dropdown-item" href="">
                                                                        <i data-feather="corner-down-left" class="mr-50"></i>
                                                                        <span>Retur</span>
                                                                    </a>
                                                                </div>
                                                            </div>
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

<script src="{{asset('app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js')}}"></script>

<script>
    $(function () {
        $('#datatable').DataTable({
            // scrollX: true,
            drawCallback: function( settings ) {
                feather.replace();
            },
            order: [[0, 'desc']],
        });
    });
</script>


@endsection
