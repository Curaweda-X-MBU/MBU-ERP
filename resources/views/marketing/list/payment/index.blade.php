@extends('templates.main')
@section('title', $title)
@section('content')
@php
$statusMarketing = App\Constants::MARKETING_STATUS;
@endphp

<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/extensions/sweetalert2.min.css') }}" />

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{$title}}</h4>
            </div>
            <div class="card-body">
                <form class="form-horizontal" method="post" action="{{ route('marketing.list.payment.add', $data->marketing_id) }}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="row row-cols-2 row-cols-md-4">
                        <!-- Nama Pelanggan -->
                        <div class="col-md-3 mt-1">
                            <label for="NamaPelanggan" class="form-label">Nama Pelanggan</label>
                            <input id="status" value="{{ $data->customer->name }}" name="status" type="text" class="form-control" disabled>
                        </div>
                        <!-- Tanggal Penjualan -->
                        <div class="col-md-3 mt-1">
                            <label for="tanggalPenjualan" class="form-label">Tanggal Penjualan</label>
                            <input id="status" value="{{ date('d-M-Y', strtotime($data->sold_at)) }}" name="status" type="text" class="form-control" disabled>
                        </div>
                        <!-- Nomor DO -->
                        <div class="col-md-3 mt-1">
                            <label for="NomorDo" class="form-label">Nomor DO</label>
                            <input id="status" value="{{ $data->id_marketing }}" name="status" type="text" class="form-control" disabled>
                        </div>
                        <!-- Status -->
                        <div class="col-md-3 mt-1">
                            <label for="NamaPelanggan" class="form-label">Status</label>
                            <input id="status" value="{{ $statusMarketing[$data->marketing_status] ?? '-' }}" name="status" type="text" class="form-control" disabled>
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
                    @include('marketing.list.payment.add')

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
                                            <td>{{ date('d-M-Y', strtotime($item->payment_at)) }}</td>
                                            <td>{{ $item->payment_method }}</td>
                                            <td>{{ isset($item->bank) ? $item->bank->alias.' - '.$item->bank->account_number.' - '.$item->bank->owner : '-'}}</td>
                                            <td>{{ \App\Helpers\Parser::toLocale($item->payment_nominal) }}</td>
                                            <td>{{ $item->payment_reference ?? '-' }}</td>
                                            <td>
                                                @php
                                                    $statusPayment = App\Constants::MARKETING_VERIFY_PAYMENT_STATUS;
                                                @endphp
                                                @switch($item->verify_status)
                                                    @case(1)
                                                        <div class="badge badge-pill badge-warning">{{ $statusPayment[$item->verify_status] }}</div>
                                                        @break
                                                    @default
                                                        <div class="badge badge-pill badge-primary">{{ $statusPayment[$item->verify_status] }}</div>
                                                @endswitch
                                            </td>
                                            <td>
                                                <div class="dropdown dropleft" style="position: static;">
                                                    <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-toggle="dropdown">
                                                        <i data-feather="more-vertical"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item" href="">
                                                            <i data-feather="edit" class="mr-50"></i>
                                                            <span>Edit</span>
                                                        </a>
                                                        <a class="dropdown-item item-delete-button" href="{{ route('marketing.list.payment.delete', $item->marketing_payment_id) }}">
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
                                        <td class="font-weight-bolder" style="font-size: 1.2em">Rp. {{ \App\Helpers\Parser::toLocale($data->marketing_payments->sum('payment_nominal') ?? 0)  }}</td>
                                    </tr>
                                    <tr>
                                        <td>Nominal Pembelian:</td>
                                        <td class="font-weight-bolder" style="font-size: 1.2em">Rp. {{ \App\Helpers\Parser::toLocale($data->grand_total) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-danger">Sisa Belum Dibayar:</td>
                                        <td class="text-danger font-weight-bolder" style="font-size: 1.2em">Rp. {{ \App\Helpers\Parser::toLocale($data->grand_total - $data->marketing_payments->sum('payment_nominal')) }}</td>
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
<script src="{{ asset('app-assets/vendors/js/extensions/sweetalert2.all.min.js') }}"></script>

<script>
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
</script>

@endsection
