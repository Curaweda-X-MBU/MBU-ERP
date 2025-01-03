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
                        Export
                    </button>
                    <div class="dropdown-menu" aria-labelledby="exportDropdown">
                        <button id="exportExcel" class="dropdown-item w-100">Excel</button>
                        <button id="exportPdf" class="dropdown-item w-100">PDF</button>
                    </div>
                    <a href="{{ route('expense.list.add') }}" type="button" class="btn btn-outline-primary waves-effect">Tambah</a>
                </div>
            </div>
            <div class="card-body">
                <div class="card-datatable">
                    <div class="table-responsive mb-2">
                        <table id="datatable" class="table table-bordered table-striped w-100">
                            <thead class="text-center">
                                <th>expense_id</th>
                                <th>expense_status</th>
                                <th>ID</th>
                                <th>Lokasi</th>
                                <th>Kategori</th>
                                <th>Tanggal</th>
                                <th>Nama Pengaju</th>
                                <th>Nominal Biaya (Rp)</th>
                                <th>Nominal Sudah Bayar (Rp)</th>
                                <th>Nominal Sisa Bayar (Rp)</th>
                                <th>Status Pembayaran</th>
                                <th>Status Biaya</th>
                                <th>Aksi</th>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
                                @php
                                    $nominalBiaya = $item->grand_total;
                                    $nominalSisaBayar = $item->expense_payments->sum('payment_nominal');
                                @endphp
                                    <tr>
                                        <td>{{ $item->expense_id }}</td>
                                        <td>{{ $item->expense_status == 0 ? 0 : 1 }}</td>
                                        <td>{{ $item->id_expense }}</td>
                                        <td>{{ $item->location->name }}</td>
                                        <td>
                                            @switch($item->category)
                                                @case(1)
                                                    <span>Biaya Operasional</span>
                                                    @break
                                                @case(2)
                                                    <span>Bukan BOP</span>
                                                    @break
                                                @default
                                                    <span>N/A</span>
                                            @endswitch
                                        </td>
                                        <td>{{ date('d-M-Y', strtotime($item->created_at)) }}</td>
                                        <td>{{ $item->created_user->name }}</td>
                                        <td class="text-right text-primary">{{ \App\Helpers\Parser::toLocale($nominalBiaya) }}</td>
                                        <td class="text-right text-success">{{ \App\Helpers\Parser::toLocale($nominalSisaBayar) }}</td>
                                        <td class="text-right text-danger">{{ \App\Helpers\Parser::toLocale($nominalBiaya - $nominalSisaBayar) }}</td>
                                        <td>
                                            @php
                                                $statusPayment = App\Constants::EXPENSE_PAYMENT_STATUS;
                                            @endphp
                                            @switch($item->payment_status)
                                                @case(0)
                                                    <div class="badge badge-pill badge-secondary">{{ $statusPayment[$item->payment_status] }}</div>
                                                    @break
                                                @case(1)
                                                    <div class="badge badge-pill badge-warning">{{ $statusPayment[$item->payment_status] }}</div>
                                                    @break
                                                @case(2)
                                                    <div class="badge badge-pill badge-success">{{ $statusPayment[$item->payment_status] }}</div>
                                                    @break
                                                @default
                                                    <div class="badge badge-pill badge-primary">{{ $statusPayment[$item->payment_status] }}</div>
                                            @endswitch
                                        </td>
                                        <td class="text-center">
                                            @php
                                                    $statusExpense = App\Constants::EXPENSE_STATUS;
                                            @endphp
                                            @switch($item->expense_status)
                                                @case(0)
                                                    <div class="badge badge-pill badge-secondary">{{ $statusExpense[$item->expense_status] }}</div>
                                                    @break
                                                @case(1)
                                                    <div class="badge badge-pill badge-warning">{{ $statusExpense[$item->expense_status] }}</div>
                                                    @break
                                                @case(2)
                                                    <div class="badge badge-pill badge-primary">{{ $statusExpense[$item->expense_status] }}</div>
                                                    @break
                                                @default
                                                    <div class="badge badge-pill badge-danger">{{ $statusExpense[$item->expense_status] }}</div>
                                            @endswitch
                                        </td>
                                        <td>
                                            <div class="dropdown dropleft">
                                                <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-toggle="dropdown">
                                                    <i data-feather="more-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" href="{{ route('expense.list.detail', $item->expense_id) }}">
                                                        <i data-feather='eye' class="mr-50"></i>
                                                        <span>Lihat Detail</span>
                                                    </a>
                                                    <a class="dropdown-item" href="{{ route('expense.list.payment.index', $item->expense_id) }}">
                                                        <i data-feather="credit-card" class="mr-50"></i>
                                                        <span>Tambah Pembayaran</span>
                                                    </a>
                                                    @if (@$item->approval_notes && @$item->is_approved === 0)
                                                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#notesModal" data-notes="{{ $item->approval_notes }}">
                                                            <i data-feather="message-square" class="mr-50"></i>
                                                            <span>Catatan Persetujuan</span>
                                                        </a>
                                                    @endif
                                                    <a class="dropdown-item item-delete-button text-danger" href="{{ route('expense.list.delete', $item->expense_id) }}">
                                                        <i data-feather='trash' class="mr-50"></i>
                                                        <span>Hapus</span>
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Catatan -->
<div class="modal fade" id="notesModal" tabindex="-1" role="dialog" aria-labelledby="notesModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notesModalLabel">Catatan Persetujuan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="notesContent">-</p>
            </div>
        </div>
    </div>
</div>

<script src="{{asset('app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js')}}"></script>

<script src="{{ asset('app-assets/vendors/js/tables/datatable/datatables.buttons.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.html5.min.js') }}"></script>

<script src="{{ asset('app-assets/vendors/js/tables/datatable/jszip.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/pdfmake.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/vfs_fonts.js') }}"></script>

<script src="{{ asset('app-assets/vendors/js/extensions/sweetalert2.all.min.js') }}"></script>


<script>
    $(function () {
        const $table = $('#datatable').DataTable({
            dom: 'B<"d-flex justify-content-between"lf>rtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    className: 'd-none datatable-hidden-excel-button',
                    exportOptions: {
                        columns: ':not(:last-child)',
                    },
                },
                {
                    extend: 'pdfHtml5',
                    className: 'd-none datatable-hidden-pdf-button',
                    exportOptions: {
                        columns: ':not(:last-child)',
                    },
                },
            ],
            drawCallback: function(settings) {
                feather.replace();
            },
            order: [[1, 'asc'], [0, 'desc']],
        });

        $table.columns([0, 1]).visible(false);

        $('#exportExcel').on('click', function() {
            $('.datatable-hidden-excel-button').trigger('click');
        });

        $('#exportPdf').on('click', function() {
            $('.datatable-hidden-pdf-button').trigger('click');
        });

        $('.item-delete-button').on('click', function(e) {
            e.preventDefailt();

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

        $(document).ready(function() {
            $('#notesModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var notes = button.data('notes') || '-';
                var modal = $(this);
                modal.find('#notesContent').text(notes);
            });
        });
    });
</script>

@endsection
