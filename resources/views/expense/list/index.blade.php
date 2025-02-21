@extends('templates.main')
@section('title', $title)
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ $title }}</h4>
                <div class="pull-right">
                    @php
                        $role = Auth::user()->role;
                    @endphp
                    @if ($role->hasPermissionTo('expense.list.approve.farm'))
                    <a href="javascript:void(0)" type="button" class="btn btn-outline-success waves-effect">
                        Approve Mgr. Farm
                    </a>
                    @endif
                    @if ($role->hasPermissionTo('expense.list.approve.finance'))
                    <a href="javascript:void(0)" type="button" class="btn btn-outline-success waves-effect">
                        Approve Mgr. Finance
                    </a>
                    @endif
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
                                <th>Biaya (Rp)</th>
                                <th>Sudah Bayar (Rp)</th>
                                <th>Sisa Bayar (Rp)</th>
                                <th>Status Pencairan</th>
                                <th>Status Biaya</th>
                                <th>Aksi</th>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
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
                                        <td class="text-right text-primary">{{ \App\Helpers\Parser::toLocale($item->grand_total) }}</td>
                                        <td class="text-right text-success">{{ \App\Helpers\Parser::toLocale($item->is_paid) }}</td>
                                        <td class="text-right text-danger">{{ \App\Helpers\Parser::toLocale($item->not_paid) }}</td>
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
                                                    <div class="badge badge-pill" style="background-color: #b8654e">{{ $statusExpense[$item->expense_status] }}</div>
                                                    @break
                                                @case(3)
                                                    <div class="badge badge-pill" style="background-color: #c0b408">{{ $statusExpense[$item->expense_status] }}</div>
                                                    @break
                                                @case(4)
                                                    <div class="badge badge-pill" style="background-color: #0bd3a8">{{ $statusExpense[$item->expense_status] }}</div>
                                                    @break
                                                @case(5)
                                                    <div class="badge badge-pill badge-success">{{ $statusExpense[$item->expense_status] }}</div>
                                                    @break
                                                @case(6)
                                                    <div class="badge badge-pill badge-danger">{{ $statusExpense[$item->expense_status] }}</div>
                                                    @break
                                                @default
                                                    <div class="badge badge-pill badge-primary">{{ $statusExpense[$item->expense_status] }}</div>
                                            @endswitch
                                        </td>
                                        <td>
                                            <div class="dropdown dropleft" style="position: static;">
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
                                                        <span>Pencairan</span>
                                                    </a>
                                                    <a class="dropdown-item" href="{{ route('expense.list.realization', $item->expense_id) }}">
                                                        <i data-feather='package' class="mr-50"></i>
                                                            <span>Realisasi</span>
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
                    <hr>
                    <div class="row">
                        <div class="col-12 col-md-6 my-1">
                            @if (auth()->user()->role->hasPermissionTo('expense.list.payment.add') && auth()->user()->role->hasPermissionTo('expense.list.payment.approve'))
                            @include('expense.list.sections.batch-upload-modal')
                            @endif
                        </div>
                        <div class="col-12 col-md-6 my-1">
                            <table class="table table-borderless">
                                <tbody class="text-right">
                                    <tr>
                                        <td class="text-primary">
                                            Total Biaya:
                                        </td>
                                        <td class="font-weight-bolder text-primary" style="font-size: 1.2em">
                                            Rp. <span id="grand_total">0,00</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-success">
                                            Total Sudah Dibayar:
                                        </td>
                                        <td class="font-weight-bolder text-success" style="font-size: 1.2em">
                                            Rp. <span id="is_paid">0,00</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-danger">
                                            Total Belum Dibayar:
                                        </td>
                                        <td class="font-weight-bolder text-danger" style="font-size: 1.2em">
                                            Rp. <span id="not_paid">0,00</span>
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
            dom: '<"d-flex justify-content-between"lf>r<"custom-table-wrapper"t>ip',
            drawCallback: function(settings) {
                let grandTotalSum = 0;
                let isPaidSum = 0;
                $table.rows({ filter: 'applied' }).every(function() {
                    const data = this.data();
                    const grandTotal = parseLocaleToNum(data[7]);
                    const isPaid = parseLocaleToNum(data[8]);

                    grandTotalSum += grandTotal;
                    isPaidSum += isPaid;
                });

                const $grandTotal = $("#grand_total");
                const $isPaid = $('#is_paid');
                const $notPaid = $('#not_paid');

                $grandTotal.text(parseNumToLocale(grandTotalSum));
                $isPaid.text(parseNumToLocale(isPaidSum));
                $notPaid.text(parseNumToLocale(grandTotalSum - isPaidSum));

                feather.replace();
            },
            order: [[1, 'asc'], [0, 'desc']],
        });

        $table.columns([0, 1]).visible(false);

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
