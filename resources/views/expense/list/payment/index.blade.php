@extends('templates.main')
@section('title', $title)
@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{$title}}</h4>
            </div>
            <div class="card-body">
                <div class="row row-cols-2 row-cols-md-5 align-items-baseline">
                    <div class="col-md-2 mt-1">
                        <label for="expense_payment_id" class="form-label">Id</label>
                        <input name="expense_payment_id" id="expense_payment_id" value="BO.1" type="text" class="form-control" disabled required>
                    </div>
                    <div class="col-md-2 mt-1">
                        <label for="location" class="form-label">Lokasi</label>
                        <input name="location" id="location" value="Banten" type="text" class="form-control" disabled required>
                    </div>
                    <div class="col-md-2 mt-1">
                        <label for="category" class="form-label">Kategori</label>
                        <input name="category" id="category" value="Biaya Operational" type="text" class="form-control" disabled required>
                    </div>
                    <div class="col-md-2 mt-1">
                        <label for="nama_pengaju" class="form-label">Nama Pengaju</label>
                        <input name="nama_pengaju" id="nama_pengaju" value="Kamal Jamal" type="text" class="form-control" disabled required>
                    </div>
                    <div class="col-md-2 mt-1">
                        <label for="tanggal" class="form-label">Tanggal</label>
                        <input name="tanggal" id="tanggal" value="16-Des-2024" type="date" class="form-control" disabled required>
                    </div>
                </div>

                <div class="float-right mt-2 mb-2">
                    <button data-toggle="modal" type="button" class="btn btn-primary waves-effect waves-float waves-light">Tambah Pembayaran</button>
                </div>

                {{-- START :: table --}}
                <div class="card-datatable">
                    <div class="table-responsive mt-3">
                        <table id="datatable" class="table table-bordered table-striped w-100">
                            <thead class="text-center">
                                <th>Tanggal Pembayaran</th>
                                <th>Metode Pembayaran</th>
                                <th>Akun Bank</th>
                                <th>Nominal Pembayaran (Rp)</th>
                                <th>No Referensi</th>
                                <th>Verifikasi Pembayaran</th>
                                <th>Aksi</th>
                            </thead>
                            <tbody>
                                    <tr  class="text-center">
                                        <td>12-Dec-2024</td>
                                        <td>Transfer</td>
                                        <td>BCA - 01234567890 - Mitra Berlian Unggas</td>
                                        <td>8.020.000,00</td>
                                        <td>Ref001</td>
                                        <td>
                                            @php
                                                $verifikasiPembayaran = 0
                                            @endphp
                                            @switch($verifikasiPembayaran)
                                                @case(0)
                                                    <div class="badge badge-pill badge-warning">Diajukan</div>
                                                    @break
                                                @default
                                                    <div class="badge badge-pill badge-primary">Terverifikasi</div>
                                            @endswitch
                                        </td>
                                        <td>
                                            <div class="dropdown dropleft" style="position: static;">
                                                <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-toggle="dropdown">
                                                    <i data-feather="more-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" href="">
                                                        <i data-feather='edit-2' class="mr-50"></i>
                                                        <span>Edit</span>
                                                    </a>
                                                    <a class="dropdown-item item-delete-button text-danger" href="">
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
                {{-- END :: table --}}

                <div class="mt-3">
                    <div class="row justify-content-end mr-2">
                        <p class="col-6 col-md-2">Total Sudah Dibayar :</p>
                        <p class="col-6 col-md-2 numeral-mask font-weight-bolder text-right" style="font-size: 1.2em;"><span id="total-recap">0,00</span></p>
                    </div>
                    <div class="row justify-content-end mr-2">
                        <p class="col-6 col-md-2">Nominal Pembelian: </p>
                        <p class="col-6 col-md-2 numeral-mask font-weight-bolder text-right" style="font-size: 1.2em;"><span id="total-recap">0,00</span></p>
                    </div>
                    <div class="row justify-content-end mr-2 text-danger">
                        <p class="col-6 col-md-2">Sisa Belum Dibayar :</p>
                        <p class="col-6 col-md-2 numeral-mask font-weight-bolder text-right" style="font-size: 1.2em;"><span id="total-recap">0,00</span></p>
                    </div>
                </div>
        </div>
    </div>
</div>
@endsection
