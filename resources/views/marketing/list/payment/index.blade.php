@extends('templates.main')
@section('title', $title)
@section('content')
@php
$statusMarketing = App\Constants::MARKETING_STATUS;
$statusPayment = App\Constants::MARKETING_VERIFY_PAYMENT_STATUS;
@endphp

<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/extensions/sweetalert2.min.css') }}" />

<script>
    function setDetail(e) {
        $('input[name="marketing_payment_id"]').val($(e).data('payment-id')).trigger('change');
    }
</script>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{$title}}</h4>
                <div>
                    <a href="{{ route('marketing.list.index') }}" class="btn btn-outline-secondary">
                        <i data-feather="arrow-left" class="mr-50"></i>
                        Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row row-cols-2 row-cols-md-4">
                    <!-- Nama Pelanggan -->
                    <div class="col-md-3 mt-1">
                        <label for="customer_name" class="form-label">Nama Pelanggan</label>
                        <input value="{{ $data->customer->name }}" name="customer_name" type="text" class="form-control" disabled>
                    </div>
                    <!-- Tanggal Penjualan -->
                    <div class="col-md-3 mt-1">
                        <label for="sold_at" class="form-label">Tanggal Penjualan</label>
                        <input value="{{ date('d-M-Y', strtotime($data->sold_at)) }}" name="sold_at" type="text" class="form-control" disabled>
                    </div>
                    <!-- Nomor DO -->
                    <div class="col-md-3 mt-1">
                        <label for="id_marketing" class="form-label">Nomor DO</label>
                        <input value="{{ $data->id_marketing }}" name="id_marketing" type="text" class="form-control" disabled>
                    </div>
                    <!-- Status -->
                    <div class="col-md-3 mt-1">
                        <label for="marketing_status" class="form-label">Status</label>
                        <input value="{{ $statusMarketing[$data->marketing_status] ?? '-' }}" name="marketing_status" type="text" class="form-control" disabled>
                    </div>
                </div>
                <div class="row row-cols-3 row-cols-md-4 justify-content-end">
                    <!-- Button Tambah Pembayaran -->
                    @if (auth()->user()->role->hasPermissionTo('marketing.list.payment.add'))
                    <div class="col-md-2 mt-2">
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#paymentAdd">
                            <i data-feather="plus"></i> Tambah Pembayaran
                        </button>
                    </div>
                    @endif
                </div>

                <!-- Modal -->
                @include('marketing.list.payment.add')
                @include('marketing.list.payment.detail')
                @include('marketing.list.payment.edit')

                <!-- BEGIN: Table-->
                <div class="card-datatable">
                    <div class="table-responsive mt-2">
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
                                                @switch($item->verify_status)
                                                    @case(0)
                                                        <div class="badge badge-pill badge-danger">{{ $statusPayment[$item->verify_status] }}</div>
                                                        @break
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
                                                        @php
                                                            $roleAccess = Auth::user()->role;
                                                        @endphp
                                                        @if ($roleAccess->hasPermissionTo('marketing.list.payment.edit'))
                                                            <a class="dropdown-item" role="button" onclick="return setDetail(this)"  data-bs-toggle="modal" data-bs-target="#paymentEdit" data-payment-id="{{ $item->marketing_payment_id }}">
                                                                <i data-feather="edit" class="mr-50"></i>
                                                                <span>Edit</span>
                                                            </a>
                                                        @endif
                                                        @if ($roleAccess->hasPermissionTo('marketing.list.payment.detail'))
                                                        <a class="dropdown-item" role="button" onclick="return setDetail(this)"  data-bs-toggle="modal" data-bs-target="#paymentDetail" data-payment-id="{{ $item->marketing_payment_id }}">
                                                            <i data-feather='eye' class="mr-50"></i>
                                                            <span>Lihat Detail</span>
                                                        </a>
                                                        @endif
                                                        <a class="dropdown-item" href="">
                                                            <i data-feather="download" class="mr-50"></i>
                                                            <span>Unduh Dokumen</span>
                                                        </a>
                                                        @if ($roleAccess->hasPermissionTo('marketing.list.payment.delete'))
                                                        <a class="dropdown-item item-delete-button text-danger" href="{{ route('marketing.list.payment.delete', $item->marketing_payment_id) }}">
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
                                <td class="text-center" colspan="8">Belum ada data pembayaran</td>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- END: Table-->

                <hr>

                <div class="row">
                    <!-- BEGIN: Total -->
                    <div class="col-md-6 offset-md-6 my-1">
                        <table class="table table-borderless">
                            <tbody class="text-right">
                                <tr>
                                    <td>Nominal Sebelum Pajak:</td>
                                    <td class="font-weight-bolder" style="font-size: 1.2em">Rp. {{ \App\Helpers\Parser::toLocale($data->sub_total) }}</td>
                                </tr>
                                <tr>
                                    <td>Nominal Biaya Lainnya:</td>
                                    <td class="font-weight-bolder" style="font-size: 1.2em">+ Rp. {{ \App\Helpers\Parser::toLocale($data->marketing_addit_prices->sum('price')) }}</td>
                                </tr>
                                <tr>
                                    <td>Nominal Diskon:</td>
                                    <td class="font-weight-bolder" style="font-size: 1.2em">- Rp. {{ \App\Helpers\Parser::toLocale($data->discount) }}</td>
                                </tr>
                                <tr>
                                    <td>Total Pajak:</td>
                                    <td class="font-weight-bolder" style="font-size: 1.2em">+ {{ \App\Helpers\Parser::toLocale($data->tax) }} %</td>
                                </tr>

                                <!-- Garis Horizontal -->
                                <tr>
                                    <td colspan="2">
                                        <hr>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-primary">Nominal Pembelian:</td>
                                    <td class="font-weight-bolder text-primary" style="font-size: 1.2em">Rp. {{ \App\Helpers\Parser::toLocale($data->grand_total) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-success">Total Sudah Dibayar:</td>
                                    <td class="font-weight-bolder text-success" style="font-size: 1.2em">Rp. {{ \App\Helpers\Parser::toLocale(
                                            $data->marketing_payments
                                            ->filter(fn($payment) => $payment->verify_status == 2)
                                            ->sum('payment_nominal') ?? 0
                                        )  }}</td>
                                </tr>
                                <tr>
                                    <td class="text-danger">Sisa Belum Dibayar:</td>
                                    <td class="text-danger font-weight-bolder" style="font-size: 1.2em">Rp. {{ \App\Helpers\Parser::toLocale(
                                            $data->grand_total - $data->marketing_payments
                                            ->filter(fn($payment) => $payment->verify_status == 2)
                                            ->sum('payment_nominal')
                                        ) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- END: Total -->
                </div>
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
