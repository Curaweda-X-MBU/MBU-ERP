@extends('templates.main')
@section('title', $title)
@section('content')
@php
 $VerifikasiPembayaran = 2;
@endphp
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{$title}}</h4>
            </div>
            <div class="card-body">
                <div class="row row-cols-2 row-cols-md-4">
                    <!-- Nama Pelanggan -->
                    <div class="col-md-2 mt-1">
                        <label for="namaPelanggan" class="form-label">Nama Pelanggan*</label>
                        <select name="namaPelanggan" id="namaPelanggan" class="form-control" disabled>
                            <option value="1" selected="selected">Abd. Muis</option>
                        </select>
                    </div>
                    <!-- Tanggal Penjualan -->
                    <div class="col-md-2 mt-1">
                        <label for="tanggalPenjualan" class="form-label">Tanggal Penjualan</label>
                        <input id="tanggalPenjualan" name="tanggalPenjualan" type="text" value="16-Des-2024" class="form-control" disabled>
                    </div>
                    <!-- Tanggal Retur -->
                    <div class="col-md-2 mt-1">
                        <label for="tanggalRetur" class="form-label">Tanggal Retur</label>
                        <input id="tanggalRetur" name="tanggalRetur" type="text" value="25-Jan-2025" class="form-control" disabled>
                    </div>
                    <!-- No DO -->
                    <div class="col-md-2 mt-1">
                        <label for="status" class="form-label">Nomor DO</label>
                        <input id="status" value="DO.MBU.19289" name="status" type="text" class="form-control" disabled>
                    </div>
                </div>

                {{-- add payment button --}}
                <div class="mt-2 text-right">
                    <button type="button" class="btn btn-icon btn-primary" data-toggle="modal" data-target="#returnPayment">
                        <i data-feather="plus"></i> Tambah Pembayaran
                    </button>
                </div>

                {{-- Include Modal --}}
                @include('marketing.return.add-payment')

                <!-- BEGIN: Table-->
                <div class="card-datatable">
                    <div class="table-responsive mt-2">
                        <table id="datatable" class="table table-bordered table-striped w-100">
                            <thead>
                                <tr class="text-center">
                                    <th>No</th>
                                    <th>Tanggal Pembayaran</th>
                                    <th class="col-2">Metode Pembayaran</th>
                                    <th>Akun Bank Penerima</th>
                                    <th>Nominal Pembayaran (Rp)</th>
                                    <th>No. Referensi</th>
                                    <th>Verifikasi Pembayaran</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody data-repeater-list="marketing_returns_payment">
                                <tr class="text-center" data-repeater-item>
                                    <td>1</td>
                                    <td>25-Dec-2024</td>
                                    <td>Transfer</td>
                                    <td>BCA - 012345677890 - Mitra Berlian Unggas</td>
                                    <td>75,020,000.00</td>
                                    <td>Ref001</td>
                                    <td>
                                        @switch($VerifikasiPembayaran)
                                            @case(1)
                                                <div class="badge badge-pill badge-warning">Diajukan</div>
                                                @break
                                            @case(2)
                                                <div class="badge badge-pill badge-info">Terverifikasi</div>
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
                                                    <i data-feather="download" class="mr-50"></i>
                                                    <span>Unduh Dokumen</span>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- END: Table-->

                {{-- total --}}
                <div class="table-responsive mt-2" style="width: auto; float: right;">
                    <table class="table table-borderless">
                        <tbody class="text-right">
                            <tr>
                                <td>Total Sudah Dibayar:</td>
                                <td class="font-weight-bolder">Rp 75.000.000,00</td>
                            </tr>
                            <tr>
                                <td>Nominal Retur:</td>
                                <td class="font-weight-bolder">Rp 8.020.000,00</td>
                            </tr>
                            <tr>
                                <td class="text-danger">Sisa Belum Dibayar:</td>
                                <td class="text-danger font-weight-bolder">Rp 5.000.000,00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
