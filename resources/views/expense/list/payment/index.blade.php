@extends('templates.main')
@section('title', $title)
@section('content')

@php
    $category = $category = App\Constants::EXPENSE_CATEGORY;
    $statusPayment = App\Constants::MARKETING_VERIFY_PAYMENT_STATUS;
    $roleAccess = Auth::user()->role;
@endphp

<script>
    function setDetail(e) {
        $('input[name="expense_payment_id"]').val($(e).data('payment-id')).trigger('change');
    }
</script>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{$title}}</h4>
                <div>
                    <a href="{{ route('expense.list.index') }}" class="btn btn-outline-secondary">
                        <i data-feather="arrow-left" class="mr-50"></i>
                        Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row row-cols-2 row-cols-md-5 align-items-baseline">
                    <div class="col-md-2 mt-1">
                        <label for="id_expense" class="form-label">ID</label>
                        <input name="id_expense" id="id_expense" value="{{ $data->id_expense }}" type="text" class="form-control" disabled required>
                    </div>
                    <div class="col-md-2 mt-1">
                        <label for="location" class="form-label">Lokasi</label>
                        <input name="location" id="location" value="{{ $data->location->name }}" type="text" class="form-control" disabled required>
                    </div>
                    <div class="col-md-2 mt-1">
                        <label for="category" class="form-label">Kategori</label>
                        <input name="category" id="category" value="{{ $category[$data->category] }}" type="text" class="form-control" disabled required>
                    </div>
                    <div class="col-md-2 mt-1">
                        <label for="nama_pengaju" class="form-label">Nama Pengaju</label>
                        <input name="nama_pengaju" id="nama_pengaju" value="{{ $data->created_user->name }}" type="text" class="form-control" disabled required>
                    </div>
                    <div class="col-md-2 mt-1">
                        <label for="tanggal" class="form-label">Tanggal</label>
                        <input name="tanggal" id="tanggal" value="{{ date('d-M-Y', strtotime($data->created_at)) }}" type="text" class="form-control" disabled required>
                    </div>
                </div>
                <div class="row row-cols-3 row-cols-md-4 justify-content-end">
                    <!-- Button Tambah Pembayaran -->
                    @if (auth()->user()->role->hasPermissionTo('expense.list.payment.add'))
                    <div class="col-md-2 mt-2">
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#expensePaymentAdd">
                            <i data-feather="plus"></i> Tambah Pembayaran
                        </button>
                    </div>
                    @endif
                </div>

                <!-- Modal -->
                @include('expense.list.payment.add')
                @include('expense.list.payment.detail')
                @include('expense.list.payment.edit')

                {{-- START :: table --}}
                <div class="card-datatable">
                    <div class="table-responsive mt-2">
                        <table id="datatable" class="table table-bordered table-striped w-100">
                            <thead class="text-center">
                                <th>No</th>
                                <th>Tanggal Pembayaran</th>
                                <th>Metode Pembayaran</th>
                                <th>Akun Bank</th>
                                <th>Nominal Pembayaran (Rp)</th>
                                <th>No Referensi</th>
                                <th>Verifikasi Pembayaran</th>
                                <th>Aksi</th>
                            </thead>
                            <tbody>
                                @if (count($data->expense_payments) > 0)
                                    @foreach ($data->expense_payments as $index => $item)
                                        <tr class="text-center">
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
                                                        @if ($roleAccess->hasPermissionTo('expense.list.payment.edit'))
                                                        <a class="dropdown-item" role="button" onclick="return setDetail(this)"  data-toggle="modal" data-target="#paymentEdit" data-payment-id="{{ $item->expense_payment_id }}">
                                                            <i data-feather='edit-2' class="mr-50"></i>
                                                            <span>Edit</span>
                                                        </a>
                                                        @endif
                                                        @if ($roleAccess->hasPermissionTo('expense.list.payment.detail'))
                                                        <a class="dropdown-item" role="button" onclick="return setDetail(this)"  data-toggle="modal" data-target="#paymentDetail" data-payment-id="{{ $item->expense_payment_id }}">
                                                            <i data-feather='eye' class="mr-50"></i>
                                                            <span>Lihat Detail</span>
                                                        </a>
                                                        @endif
                                                        @if ($item->document_path)
                                                        <a class="dropdown-item" href="{{ route('file.show') . '?download=true&filename=' . $item->document_path }}">
                                                            <i data-feather="download" class="mr-50"></i>
                                                            <span>Unduh Dokumen</span>
                                                        </a>
                                                        @endif
                                                        @if ($roleAccess->hasPermissionTo('expense.list.payment.delete'))
                                                        <a class="dropdown-item item-delete-button text-danger" href="{{ route('expense.list.payment.delete', $item->expense_payment_id) }}">
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
                {{-- END :: table --}}

                <!-- Garis Horizontal -->
                <tr>
                    <td colspan="2">
                        <hr>
                    </td>
                </tr>

                <div class="row">
                <!-- BEGIN: Total -->
                    <div class="col-md-6 offset-md-6 my-1">
                        <table class="table table-borderless">
                            <tbody class="text-right">
                                <tr>
                                    <td>Nominal Biaya Utama:</td>
                                    <td class="font-weight-bolder" style="font-size: 1.2em">Rp. {{ \App\Helpers\Parser::toLocale($data->expense_main_prices->sum('total_price')) }}</td>
                                </tr>
                                <tr>
                                    <td>Nominal Biaya Lainnya:</td>
                                    <td class="font-weight-bolder" style="font-size: 1.2em">Rp. {{ \App\Helpers\Parser::toLocale($data->expense_addit_prices->sum('total_price')) }}</td>
                                </tr>

                                <!-- Garis Horizontal -->
                                <tr>
                                    <td colspan="2">
                                        <hr>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-primary">Nominal Biaya:</td>
                                    <td class="font-weight-bolder text-primary" style="font-size: 1.2em">Rp. {{ \App\Helpers\Parser::toLocale($data->grand_total) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-success">Total Sudah Dibayar:</td>
                                    <td class="font-weight-bolder text-success" style="font-size: 1.2em">Rp. {{ \App\Helpers\Parser::toLocale(
                                        $data->is_paid
                                    ) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-danger">Sisa Belum Dibayar:</td>
                                    <td class="text-danger font-weight-bolder" style="font-size: 1.2em">Rp. {{
                                        \App\Helpers\Parser::toLocale(
                                            $data->grand_total - $data->is_paid
                                        ) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <!-- END: Total -->
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('app-assets/vendors/js/extensions/sweetalert2.all.min.js') }}"></script>

<script>
    $('.item-delte-button').on('click', function(e) {
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
