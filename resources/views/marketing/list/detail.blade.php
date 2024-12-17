@extends('templates.main')
@section('title', $title)
@section('content')
<style>
    .color-header {
        color: white;
        background: linear-gradient(118deg, #76A8D8, #c9e5ff);
    }
    th {
        padding-left: 10px !important;
        padding-right: 10px !important;
    }
</style>

<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">

<div class="col-12">
    <div class="row">
        <div class="no-print pb-2">
            <h4 class="card-title">{{$title}}</h4>

                <a href="" class="btn btn-outline-secondary">
                    <i data-feather="arrow-left" class="mr-50"></i>
                    Kembali
                </a>
                    <a href="" class="btn btn-primary">
                        <i data-feather="edit-2" class="mr-50"></i>
                        Edit
                    </a>
                    <a class="btn btn-success" href="" data-toggle="modal" data-target="#approve">
                        <i data-feather="check" class="mr-50"></i>
                        Approve
                    </a>
        </div>
    </div>
</div>


<section id="collapsible">
    <div class="row">
        <div class="col-sm-12">
            <div class=" collapse-icon">
                <div class=" p-0">
                    <div class="collapse-default">

                        {{-- COLLAPSE TABLE INFORMASI PENJUALAN --}}
                        <div class="card mb-1">
                            <div id="headingCollapse1" class="card-header color-header collapsed" data-toggle="collapse" role="button" data-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                                <span class="lead collapse-title"> Informasi  Penjualan </span>
                            </div>
                            <div id="collapse1" role="tabpanel" aria-labelledby="headingCollapse1" class="collapse show" aria-expanded="true">
                                <div class="card-body p-2">
                                    <div class="col-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered w-100">
                                                <thead>
                                                    <th>No. DO</th>
                                                    <th>Nama Pelanggan</th>
                                                    <th>Unit Bisnis</th>
                                                    <th>Referensi Dokumen</th>
                                                    <th>Nama Sales</th>
                                                    <th>Catatan</th>
                                                    <th>Total Piutang Penjualan</th>
                                                    <th>Status Pembayaran</th>
                                                    <th>Status Penjualan</th>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>DO.MBU.1</td>
                                                        <td>Agus</td>
                                                        <td>MBU</td>
                                                        <td>
                                                            <a class="dropdown-item" href="" target="_blank">
                                                                <i data-feather='download' class="mr-50"></i>
                                                                <span>Download</span>
                                                            </a>
                                                        </td>
                                                        <td>Abdullah</td>
                                                        <td>
                                                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#notesModal">
                                                                Lihat Catatan
                                                            </button>
                                                        </td>
                                                        <td>100.000.000</td>
                                                        <td><div class="badge badge-pill badge-success">Lunas</div></td>
                                                        <td><div class="badge badge-pill badge-success">Selesai</div></td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <!-- Modal -->
                                            <div class="modal fade" id="notesModal" tabindex="-1" role="dialog" aria-labelledby="notesModalLabel" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="notesModalLabel">Catatan Penjualan</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Sudah dibayar full Catatan tambahan: Pembayaran dilakukan melalui transfer bank pada tanggal 1 Januari 2023. Jika ada pertanyaan lebih lanjut, silakan hubungi sales.</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- COLLAPSE TABLE BIAYA LAINNYA --}}
                        <div class="card mb-1">
                            <div id="headingCollapse2" class="card-header color-header collapsed" data-toggle="collapse" role="button" data-target="#collapse2" aria-expanded="true" aria-controls="collapse2">
                                <span class="lead collapse-title"> Biaya Lainnya </span>
                            </div>
                            <div id="collapse2" role="tabpanel" aria-labelledby="headingCollapse2" class="collapse show" aria-expanded="true">
                                <div class="card-body p-2">
                                    <div class="col-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered w-100">
                                                <thead>
                                                    <th>No</th>
                                                    <th>Item</th>
                                                    <th>Price</th>
                                                </thead>
                                                <tbody>
                                                        <tr>
                                                            <td>1</td>
                                                            <td>Ayan Potong</td>
                                                            <td>2.000.000.000</td>
                                                        </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- COLLAPSE TABLE PRODUK PENJUALAN --}}
                        <div class="card mb-1">
                            <div id="headingCollapse1" class="card-header color-header collapsed" data-toggle="collapse" role="button" data-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                                <span class="lead collapse-title"> Produk  Penjualan </span>
                            </div>
                            <div id="collapse1" role="tabpanel" aria-labelledby="headingCollapse1" class="collapse show" aria-expanded="true">
                                <div class="card-body p-2">
                                    <div class="col-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered w-100">
                                                <thead>
                                                    <th>No</th>
                                                    <th>Kandang/Hatcher</th>
                                                    <th>Nama Produk</th>
                                                    <th>Harga Satuan (Rp)</th>
                                                    <th>Bobot AVG</th>
                                                    <th>UOM</th>
                                                    <th>QTY</th>
                                                    <th>Total Bobot</th>
                                                    <th>Total Penjualan (Rp)</th>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>1</td>
                                                        <td>Singaparna 1</td>
                                                        <td>Broiler</td>
                                                        <td>20.000.000</td>
                                                        <td>12</td>
                                                        <td>KG</td>
                                                        <td>12</td>
                                                        <td>120000</td>
                                                        <td>120.000.000</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        {{-- COLLAPSE TABLE ARMADA ANGKUT --}}
                        <div class="card mb-1">
                            <div id="headingCollapse2" class="card-header color-header collapsed" data-toggle="collapse" role="button" data-target="#collapse2" aria-expanded="true" aria-controls="collapse2">
                                <span class="lead collapse-title"> Armada Angkut </span>
                            </div>
                            <div id="collapse2" role="tabpanel" aria-labelledby="headingCollapse2" class="collapse show" aria-expanded="true">
                                <div class="card-body p-2">
                                    <div class="col-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered w-100">
                                                <thead>
                                                    <th>No</th>
                                                    <th>No. Polisi</th>
                                                    <th>Jumlah (Ekor)</th>
                                                    <th>Waktu Keluar Kandang</th>
                                                    <th>Nama Pengirim</th>
                                                    <th>Nama Driver</th>
                                                </thead>
                                                <tbody>
                                                        <tr>
                                                            <td>1</td>
                                                            <td>JSAFGIA</td>
                                                            <td>12</td>
                                                            <td>12-12-2024</td>
                                                            <td>Abdullah</td>
                                                            <td>Septian</td>
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
            </div>
        </div>
    </div>
</section>

<div class="modal fade text-left" id="approve" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <form method="post" action="{{ route('project.chick-in.approve', 'test') }}">
            {{csrf_field()}}
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel1">Konfirmasi Approve Chick In</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="id" value="">
                    <input type="hidden" name="act" id="act" value="">
                    <input type="text" class="form-control flatpickr-inline" name="first_day_old_chick" placeholder="Pilih tanggal umur ayam 1 hari" required>
                    <br><p>Apakah kamu yakin ingin menyetujui data chick in ini ?</p>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Ya</button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Tidak</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
