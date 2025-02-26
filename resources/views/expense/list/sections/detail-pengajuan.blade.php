@php
    $roleAccess = Auth::user()->role;
    $expenseStatus = \App\Constants::EXPENSE_STATUS;

    $to_approve_by_farm = array_search('Approval Manager', $expenseStatus);
    $to_approve_by_finance = array_search('Approval Finance', $expenseStatus);

    $can_approve_by_farm = $roleAccess->hasPermissionTo('expense.list.approve.farm');
    $can_approve_by_finance = $roleAccess->hasPermissionTo('expense.list.approve.finance');
@endphp

<div class="card">
    <div class="card-header pb-0">
        <h4 class="card-title">{{ $title }} | Pengajuan</h4>
    </div>
    <div class="card-header">
        <div>
            <a href="{{ route('expense.list.index') }}" class="btn btn-outline-secondary">
                <i data-feather="arrow-left" class="mr-50"></i>
                Kembali
            </a>
            @if ($data->expense_status < 4 && $data->expense_status !== 2)
            <a href="{{ route('expense.list.edit', $data->expense_id) }}" class="btn btn-primary">
                <i data-feather="edit-2" class="mr-50"></i>
                Edit
            </a>
            @endif

            @php
                $button_text = ($can_approve_by_farm && $data->expense_status === $to_approve_by_farm)
                    ? 'Approve Manager Farm'
                    : (
                        ($can_approve_by_finance && $data->expense_status === $to_approve_by_finance)
                            ? 'Approve Finance'
                            : false
                    );
                $show_button = ($data->expense_status === $to_approve_by_farm || $data->expense_status === $to_approve_by_finance) ?? false;
            @endphp
            @if ($show_button && $button_text)
            <a class="btn btn-success" href="#" data-toggle="modal" data-target="#approve">
                <i data-feather="check" class="mr-50"></i>
                {{ $button_text }}
            </a>
            @endif
        </div>
    </div>
    <div class="card-body">
        @include('expense.stepper')
        <div class="card-datatable">
            <div class="table-responsive mb-2">
                <div class="col-12">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-striped w-100">
                                <tr>
                                    <td style="width: 25%"><b>Nomor PO</b></td>
                                    <td style="width: 5%">:</td>
                                    <td>
                                        @if (isset($data->po_number))
                                        <a class="btn btn-sm btn-primary" target="_blank" href="{{ route('expense.list.detail', $data->expense_id) . '?po_number=' . $data->po_number }}">
                                            {{ $data->po_number }}
                                        </a>
                                        @else
                                        <i class="text-muted">Belum dibuat</i>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 25%"><b>ID</b></td>
                                    <td style="width: 5%">:</td>
                                    <td>{{ $data->id_expense }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 25%"><b>Lokasi</b></td>
                                    <td style="width: 5%">:</td>
                                    <td>{{ $data->location->name }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 25%"><b>Kategori</b></td>
                                    <td style="width: 5%">:</td>
                                    <td>
                                        @php
                                            $category = App\Constants::EXPENSE_CATEGORY;
                                        @endphp
                                        @switch($data->category)
                                            @case(1)
                                                <span>{{ $category[$data->category] }}</span>
                                                @break
                                            @case(2)
                                                <span>{{ $category[$data->category] }}</span>
                                                @break
                                            @default
                                                <span>N/A</span>
                                        @endswitch
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 25%"><b>Kandang yang dipilih</b></td>
                                    <td style="width: 5%">:</td>
                                    <td>{{ $data->expense_kandang?->sortBy('kandang_id')->map(fn($kandang) => $kandang->kandang->name ?? '')->join(', ') ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 25%"><b>Tanggal Transaksi</b></td>
                                    <td style="width: 5%">:</td>
                                    <td>{{ date('d-M-Y', strtotime($data->transaction_date)) ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 25%"><b>Tanggal Dibuat</b></td>
                                    <td style="width: 5%">:</td>
                                    <td>{{ date('d-M-Y', strtotime($data->created_at)) }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 25%"><b>Nama Pengaju</b></td>
                                    <td style="width: 5%">:</td>
                                    <td>{{ $data->created_user->name }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 25%"><b>Nominal Biaya</b></td>
                                    <td style="width: 5%">:</td>
                                    <td class="text-primary">Rp. {{ \App\Helpers\Parser::toLocale($nominalBiaya) }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 25%"><b>Nominal Sudah Bayar</b></td>
                                    <td style="width: 5%">:</td>
                                    <td class="text-success">Rp. {{ \App\Helpers\Parser::toLocale($nominalSisaBayar) }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 25%"><b>Nominal Sisa Bayar</b></td>
                                    <td style="width: 5%">:</td>
                                    <td class="text-danger">Rp. {{ \App\Helpers\Parser::toLocale($nominalBiaya - $nominalSisaBayar) }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 25%"><b>Status Pencairan</b></td>
                                    <td style="width: 5%">:</td>
                                    <td>
                                        @php
                                            $statusPayment = App\Constants::EXPENSE_PAYMENT_STATUS;
                                        @endphp
                                        @switch($data->payment_status)
                                            @case(0)
                                                <div class="badge badge-pill badge-secondary">{{ $statusPayment[$data->payment_status] }}</div>
                                                @break
                                            @case(1)
                                                <div class="badge badge-pill badge-warning">{{ $statusPayment[$data->payment_status] }}</div>
                                                @break
                                            @case(2)
                                                <div class="badge badge-pill badge-success">{{ $statusPayment[$data->payment_status] }}</div>
                                                @break
                                            @default
                                                <div class="badge badge-pill badge-primary">{{ $statusPayment[$data->payment_status] }}</div>
                                        @endswitch
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 25%"><b>Status Biaya</b></td>
                                    <td style="width: 5%">:</td>
                                    <td>
                                        @switch($data->expense_status)
                                            @case(0)
                                                <div class="badge badge-pill badge-secondary">{{ $expenseStatus[$data->expense_status] }}</div>
                                                @break
                                            @case(1)
                                                <div class="badge badge-pill badge-warning">{{ $expenseStatus[$data->expense_status] }}</div>
                                                @break
                                            @case(2)
                                                <div class="badge badge-pill badge-danger">{{ $expenseStatus[$data->expense_status] }}</div>
                                                @break
                                            @case(3)
                                                <div class="badge badge-pill" style="background-color: #b8654e">{{ $expenseStatus[$data->expense_status] }}</div>
                                                @break
                                            @case(4)
                                                <div class="badge badge-pill" style="background-color: #c0b408">{{ $expenseStatus[$data->expense_status] }}</div>
                                                @break
                                            @case(5)
                                                <div class="badge badge-pill" style="background-color: #0bd3a8">{{ $expenseStatus[$data->expense_status] }}</div>
                                                @break
                                            @case(6)
                                                <div class="badge badge-pill badge-success">{{ $expenseStatus[$data->expense_status] }}</div>
                                                @break
                                            @default
                                                <div class="badge badge-pill badge-primary">{{ $expenseStatus[$data->expense_status] }}</div>
                                        @endswitch
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 25%"><b>Dokumen Tagihan</b></td>
                                    <td style="width: 5%">:</td>
                                    <td>
                                        @if ($data->bill_docs)
                                            <a class="p-0" href="{{ route('file.show', ['filename' => $data->bill_docs]) }}" target="_blank">
                                                <i data-feather='download' class="mr-50"></i>
                                                <span>Lihat Dokumen Tagihan</span>
                                            </a>
                                        @else
                                            <span>-</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Approval -->
<div class="modal fade text-left" id="approve" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            @php
                $approve_route = $data->expense_status === $to_approve_by_farm
                    ? route('expense.list.approve.farm', ['expense' => $data->expense_id ])
                    : (
                        $data->expense_status === $to_approve_by_finance
                            ? route('expense.list.approve.finance', ['expense' => $data->expense_id ])
                            : '#'
                    );
            @endphp
            <form id="approveForm" method="post" action="{{ $approve_route }}">
            {{csrf_field()}}
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel1">Konfirmasi Approve Biaya </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="is_approved" id="is_approved" value="">
                    <div class="form-group">
                        <label for="notes" class="form-label">Catatan</label>
                        <textarea name="approval_notes" id="notes" class="form-control"></textarea>
                    </div>
                    <br><p>Apakah anda yakin ingin menyetujui data biaya ini?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="setApproval(1)" class="btn btn-success">Setuju</button>
                    <button type="button" onclick="setApproval(0)" class="btn btn-danger">Tidak</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // MODAL CATATAN
    $(document).ready(function() {
        $('#notesModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var notes = button.data('notes');
            var title = button.data('title');
            var modal = $(this);
            modal.find('#notesContent').text(notes || 'Catatan tidak tersedia.');
            modal.find('#notesModalLabel').text(title || 'Catatan');
        });
    });
</script>

