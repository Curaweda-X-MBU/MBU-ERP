@extends('templates.main')
@section('title', $title)
@section('content')
@php
$statusMarketing = App\Constants::MARKETING_STATUS;
@endphp

<style>
    #transparentFileUpload {
        opacity: 0;
        position:absolute;
        inset: 0;
    }
</style>

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
                            <input id="status" value="{{ $data->customer->name }}" name="status" type="text" class="form-control" readonly>
                        </div>
                        <!-- Tanggal Penjualan -->
                        <div class="col-md-3 mt-1">
                            <label for="tanggalPenjualan" class="form-label">Tanggal Penjualan</label>
                            <input id="status" value="{{ $data->sold_at }}" name="status" type="text" class="form-control" readonly>
                        </div>
                        <!-- Nomor DO -->
                        <div class="col-md-3 mt-1">
                            <label for="NomorDo" class="form-label">Nomor DO</label>
                            <input id="status" value="{{ $data->id_marketing }}" name="status" type="text" class="form-control" readonly>
                        </div>
                        <!-- Status -->
                        <div class="col-md-3 mt-1">
                            <label for="NamaPelanggan" class="form-label">Status</label>
                            <input id="status" value="{{ $statusMarketing[$data->marketing_status] ?? '-' }}" name="status" type="text" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="row row-cols-3 row-cols-md-4 justify-content-end">
                        <!-- Button Tambah Pembayaran -->
                        <div class="col-md-2 mt-2">
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#paymentDetail">
                                <i data-feather="plus"></i> Tambah Pembayaran
                            </button>
                        </div>
                    </div>

                    <!-- Modal -->
                    <div class="modal fade" id="paymentDetail" tabindex="-1" role="dialog" aria-labelledby="paymentDetailLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title text-primary" id="paymentDetailLabel">Form Pembayaran</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position: absolute; top: 16px; right: 30px;">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form class="form-horizontal" method="post" action="{{ route('marketing.return.payment') }}" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            {{-- Table kiri --}}
                                            <div class="col-md-6">
                                                <table class="table table-borderless">
                                                    <tbody>
                                                        <tr>
                                                            <td><label for="do_number">No. DO</label></td>
                                                            <td><input type="text" class="form-control" id="do_number" value="{{ $data->id_marketing }}" disabled></td>
                                                        </tr>
                                                        <tr>
                                                            <td><label for="customer_name">Nama Customer</label></td>
                                                            <td><input type="text" class="form-control" id="customer_name" value="{{ $data->customer->name }}" disabled></td>
                                                        </tr>
                                                        <tr>
                                                            <td><label for="return_nominal">Nominal Penjualan</label></td>
                                                            <td><input type="text" class="form-control" id="return_nominal" value="Rp. {{ number_format($data->grand_total, 2, '.', ',') }}" disabled></td>
                                                        </tr>
                                                        <tr>
                                                            <td><label for="payment_method">Metode Pembayaran*</label></td>
                                                            <td>
                                                                <select class="form-control" id="payment_method">
                                                                    <option value="">Pilih Metode Pembayaran</option>
                                                                    <option value="transfer">Transfer</option>
                                                                    <option value="cash">Cash</option>
                                                                    <option value="credit_card">Kartu Kredit</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><label for="own_bank_id">Akun Bank*</label></td>
                                                            <td>
                                                                <select class="form-control" id="own_bank_id">
                                                                    <option value="">Pilih Akun Bank</option>
                                                                    <option value="bank1">Mandiri - 012345678 - Mitra Berlian</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                            {{-- Table kanan --}}
                                            <div class="col-md-6">
                                                <table class="table table-borderless">
                                                    <tbody>
                                                        <tr>
                                                            <td><label for="ref_number">Referensi Pembayaran</label></td>
                                                            <td><input type="text" class="form-control" id="ref_number" placeholder="Masukkan Referensi"></td>
                                                        </tr>
                                                        <tr>
                                                            <td><label for="transaction_number">Nomor Transaksi</label></td>
                                                            <td><input type="text" class="form-control" id="transaction_number" placeholder="Masukkan No. Transaksi"></td>
                                                        </tr>
                                                        <tr>
                                                            <td><label for="payment_amount">Nominal Pembayaran*</label></td>
                                                            <td><input type="text" class="form-control numeral-mask" id="payment_amount" value="0"></td>
                                                        </tr>
                                                        <tr>
                                                            <td><label for="payment_at">Tanggal Bayar*</label></td>
                                                            <td><input type="date" class="form-control flatpickr-basic" id="payment_at" value="0"></td>
                                                        </tr>
                                                        <tr>
                                                            <td><label for="doc_reference">Upload Dokumen</label></td>
                                                            <td>
                                                                <div class="input-group">
                                                                    <input type="text" id="fileName" placeholder="Upload" class="form-control">
                                                                    <input type="file" id="transparentFileUpload" name="doc_reference">
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text"> <i data-feather="upload"></i> </span>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><label for="notes">Catatan</label></td>
                                                            <td colspan="3"><textarea class="form-control" id="notes"></textarea></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button id="submitForm" type="submit" data-repeater-create class="btn btn-primary mr-1 waves-effect waves-float waves-light">Submit</button>
                                </div>
                            </div>
                        </div>
                    </div>

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
                                @if (count($data->marketing_payments) > 0)
                                    @foreach ($data->marketing_payments as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item->payment_at }}</td>
                                            <td>{{ $item->payment_method }}</td>
                                            <td>{{ $item->bank->alias }} - {{ $item->bank->account_number }} - {{ $item->bank->owner }}</td>
                                            <td>{{ number_format($item->payment_nominal, 2, '.', ',') }}</td>
                                            <td>{{ $item->payment_reference }}</td>
                                            <td>
                                                @php
                                                    $statusPayment = App\Constants::MARKETING_VERIFY_PAYMENT_STATUS;
                                                @endphp
                                                @switch($data->verify_status)
                                                    @case(1)
                                                        <div class="badge badge-pill badge-warning">{{ $statusPayment[$data->marketing_status] }}</div>
                                                        @break
                                                    @default
                                                        <div class="badge badge-pill badge-primary">{{ $statusPayment[$data->marketing_status] }}</div>
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
                                    @endforeach
                                @else
                                <td class="text-center" colspan="8">Belum ada data pembayaran</td>
                                @endif
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
                                        <td class="font-weight-bolder" style="font-size: 1.2em">Rp. {{ $data->marketing_payments->sum('payment_nominal') ?? 0 }}</td>
                                    </tr>
                                    <tr>
                                        <td>Nominal Pembelian:</td>
                                        <td class="font-weight-bolder" style="font-size: 1.2em">Rp. {{ number_format($data->grand_total, 2, '.', ',') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-danger">Sisa Belum Dibayar:</td>
                                        <td class="text-danger font-weight-bolder" style="font-size: 1.2em">Rp. {{ number_format($data->grand_total - $data->marketing_payments->sum('payment_nominal') ?? 0, 2, '.', ',') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
{{-- <script src="{{asset('app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js')}}"></script> --}}
<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/forms/repeater/jquery.repeater.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/forms/cleave/cleave.min.js')}}"></script>
<script>
    $(document).ready(function() {
        $('#transparentFileUpload').on('change', function() {
            $('#fileName').val($('#transparentFileUpload').val().split('\\').pop())
        })
    })

    initNumeralMask('.numeral-mask');

    const dateOpt = { dateFormat: 'd-M-Y' };
        $('.flatpickr-basic').flatpickr(dateOpt);

    initSelect2($('#payment_method'), 'Pilih Metode Pembayaran');
    initSelect2($('#own_bank_id'), 'Pilih Bank');
    initSelect2($('#recipient_bank_id'), 'Pilih Bank');

</script>

@endsection
