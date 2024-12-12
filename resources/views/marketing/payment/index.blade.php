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
                <form class="form-horizontal" method="post" action="{{ route('marketing.list.add') }}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="row row-cols-2 row-cols-md-4">
                        <!-- Nama Pelanggan -->
                        <div class="col-md-3 mt-1">
                            <label for="NamaPelanggan" class="form-label">Nama Pelanggan</label>
                            <input id="status" value="Abd Muis" name="status" type="text" class="form-control" readonly>
                        </div>
                        <!-- Tanggal Penjualan -->
                        <div class="col-md-3 mt-1">
                            <label for="tanggalPenjualan" class="form-label">Tanggal Penjualan</label>
                            <input id="status" value="16-Des-2024" name="status" type="text" class="form-control" readonly>
                        </div>
                        <!-- Nomor DO -->
                        <div class="col-md-3 mt-1">
                            <label for="NomorDo" class="form-label">Nomor DO</label>
                            <input id="status" value="DO.MBU.19289" name="status" type="text" class="form-control" readonly>
                        </div>
                        <!-- Status -->
                        <div class="col-md-3 mt-1">
                            <label for="NamaPelanggan" class="form-label">Status</label>
                            <input id="status" value="Final" name="status" type="text" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="row row-cols-3 row-cols-md-4 justify-content-end">
                        <!-- Button Tambah Pembayaran -->
                        <div class="col-md-2 mt-2">
                            <button type="button" class="btn btn-icon btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#paymentDetail">
                                Tambah Pembayaran <i data-feather="plus"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Modal -->
                    <div class="modal fade text-left" id="paymentDetail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document" style="max-width: 90%;">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="myModalLabel1">Form Pembayaran</h4>
                                    <button type="button" class="btn" style="border: none; background: none; padding: 0;" data-bs-dismiss="modal" aria-label="Close">
                                        <span style="font-size: 24px; color: #000;">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div id="modal-spinner" class="text-center" style="display: none;">
                                        <div class="spinner-grow" role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                    </div>
                                    <div id="modal-content">
                                        @include('marketing.payment.add')
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Batal</button>
                                    <button type="button" class="btn btn-primary" id="savePaymentButton">Submit</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

                    <!-- BEGIN: Table-->
                    <div class="table-responsive mt-3">
                        <table id="datatable" class="table table-bordered table-striped w-100">
                            <thead>
                                <th>No</th>
                                <th>Tanggal Pembayaran</th>
                                <th>Metode Pembayaran</th>
                                <th>Akun Bank</th>
                                <th>Nominal Pembayaran (Rp)</th>
                                <th>No. Referensi</th>
                                <th>Verifikasi Pembayaran</th>
                                <th>Aksi</th>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>12-Dec-2024</td>
                                    <td>Transfer</td>
                                    <td>BCA - 01234567890 - Mitra Berlian Unggas</td>
                                    <td>50,000,000.00</td>
                                    <td>Ref001</td>
                                    <td>
                                        <div class="badge badge-pill badge-primary">Terverifikasi</div>
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

                                <tr>
                                    <td>2</td>
                                    <td>12-Dec-2024</td>
                                    <td>Transfer</td>
                                    <td>BCA - 01234567890 - Mitra Berlian Unggas</td>
                                    <td>50,000,000.00</td>
                                    <td>Ref001</td>
                                    <td>
                                        <div class="badge badge-pill badge-primary">Terverifikasi</div>
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
                    <!-- END: Table-->

                    <hr>

                        <div class="row">
                            <!-- BEGIN: kosong -->
                            <div class="col-md-6">
                                <div class="row">
                                </div>
                            </div>
                            {{-- END: kosong--}}

                            <!-- BEGIN: Total -->
                            <div class="col-md-6 my-1">
                                <table class="table table-borderless">
                                    <tbody class="text-right">
                                        <tr>
                                            <td>Total Sudah Dibayar:</td>
                                            <td>Rp 50,000,000.00</td>
                                        </tr>
                                        <tr>
                                            <td>Nominal Pembelian:</td>
                                            <td>Rp 87,700,000.00</td>
                                        </tr>
                                        <tr>
                                            <td style="color: red;">Sisa Belum Dibayar:</td>
                                            <td style="color: red;">37,000,000.00</td>
                                        </tr>

                                    </tbody>
                                </table>
                        </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
