@extends('templates.main')
@section('title', $title)
@section('content')
@php
    $nominalBiaya = $data->grand_total;
    $nominalSisaBayar = $data->expense_payments->sum('payment_nominal');
    $roleAccess = Auth::user()->role;
@endphp

<style>
    .color-header {
        color: white;
        background: linear-gradient(118deg, #76A8D8, #c9e5ff);
    }
    th {
        padding-left: 10px !important;
        padding-right: 10px !important;
    }
</style>

<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">

<script>
    function setApproval(value) {
        $('#is_approved').val(value);
        $('#approveForm').trigger('submit');
    }
</script>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ $title }}</h4>
                <div>
                    <a href="{{ route('expense.list.edit', $data->expense_id) }}" class="btn btn-primary">
                        <i data-feather="edit-2" class="mr-50"></i>
                        Edit
                    </a>
                    <a class="btn btn-success" href="{{ route('expense.list.approve', $data->expense_id) }}" data-toggle="modal" data-target="#approve">
                        <i data-feather="check" class="mr-50"></i>
                        Approve
                    </a>
                    <a href="{{ route('expense.list.index') }}" class="btn btn-outline-secondary">
                        <i data-feather="arrow-left" class="mr-50"></i>
                        Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="card-datatable">
                    <div class="table-responsive mb-2">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-striped w-100">
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
                                            <td style="width: 25%"><b>Tanggal</b></td>
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
                                            <td style="width: 25%"><b>Status Biaya</b></td>
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
                                                @php
                                                    $statusExpense = App\Constants::EXPENSE_STATUS;
                                                @endphp
                                                @switch($data->expense_status)
                                                    @case(0)
                                                        <div class="badge badge-pill badge-secondary">{{ $statusExpense[$data->expense_status] }}</div>
                                                        @break
                                                    @case(1)
                                                        <div class="badge badge-pill badge-warning">{{ $statusExpense[$data->expense_status] }}</div>
                                                        @break
                                                    @case(2)
                                                        <div class="badge badge-pill badge-primary">{{ $statusExpense[$data->expense_status] }}</div>
                                                        @break
                                                    @default
                                                        <div class="badge badge-pill badge-danger">{{ $statusExpense[$data->expense_status] }}</div>
                                                @endswitch
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
    </div>
</div>

<section id="collapsible">
    <div class="row">
        <div class="col-sm-12">
            <div class=" collapse-icon">
                <div class=" p-0">
                    <div class="collapse-default">
                        {{-- COLLAPSE TABLE BIAYA UTAMA --}}
                        <div class="card mb-1">
                            <div id="headingCollapse1" class="card-header color-header collapsed" data-toggle="collapse" role="button" data-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                                <span class="lead collapse-title"> Biaya Utama</span>
                            </div>
                            <div id="collapse1" role="tabpanel" aria-labelledby="headingCollapse1" class="collapse show" aria-expanded="true">
                                <div class="card-body p-2">
                                    <div class="col-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered w-100">
                                                <thead>
                                                    <th>No</th>
                                                    <th>Sub Categori</th>
                                                    <th>Qty</th>
                                                    <th>UOM</th>
                                                    <th>Harga Satuan</th>
                                                    <th>Nominal Biaya</th>
                                                    <th>Catatan</th>
                                                </thead>
                                                <tbody>
                                                    @if (count($data->expense_main_prices) > 0)
                                                        @foreach ($data->expense_main_prices as $index => $item)
                                                            <tr>
                                                                <td>{{  $index + 1 }}</td>
                                                                <td>{{ $item->sub_category }}</td>
                                                                <td>{{ \App\Helpers\Parser::toLocale($item->qty) }}</td>
                                                                <td>{{ $item->uom }}</td>
                                                                <td>{{ \App\Helpers\Parser::toLocale($item->total_price / $item->qty) }}</td>
                                                                <td>{{ \App\Helpers\Parser::toLocale($item->total_price) }}</td>
                                                                <td>
                                                                    @if ($item->notes)
                                                                        <button type="button" class="btn btn-link p-0 m-0" data-toggle="modal" data-target="#notesModal" data-notes="{{ $item->notes }}" data-title="Catatan Biaya Utama">
                                                                            Lihat Catatan
                                                                        </button>
                                                                    @else
                                                                        <span>-</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                    <tr>
                                                        <td class="text-center" colspan="7">Tidak ada data biaya Utama</td>
                                                    </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- COLLAPSE TABLE BIAYA LAINNYA --}}
                        <div class="card mb-1">
                            <div id="headingCollapse2" class="card-header color-header collapsed" data-toggle="collapse" role="button" data-target="#collapse2" aria-expanded="true" aria-controls="collapse2">
                                <span class="lead collapse-title"> Biaya Lainnya </span>
                            </div>
                            <div id="collapse2" role="tabpanel" aria-labelledby="headingCollapse2" class="collapse show" aria-expanded="true">
                                <div class="card-body p-2">
                                    <div class="col-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered w-100">
                                                <thead>
                                                    <th>No</th>
                                                    <th>Nama Biaya</th>
                                                    <th>Nominla Biaya</th>
                                                    <th>Catatan</th>
                                                </thead>
                                                <tbody>
                                                    @if (count($data->expense_addit_prices) > 0)
                                                        @foreach ($data->expense_addit_prices as $index => $item)
                                                            <tr>
                                                                <td>{{  $index + 1 }}</td>
                                                                <td>{{ $item->name }}</td>
                                                                <td>{{ \App\Helpers\Parser::toLocale($item->price) }}</td>
                                                                <td>
                                                                    @if ($item->notes)
                                                                        <button type="button" class="btn btn-link p-0 m-0" data-toggle="modal" data-target="#notesModal" data-notes="{{ $item->notes }}" data-title="Catatan Biaya Lainnya">
                                                                            Lihat Catatan
                                                                        </button>
                                                                    @else
                                                                        <span>-</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                    <tr>
                                                        <td class="text-center" colspan="4">Tidak ada data biaya lainnya</td>
                                                    </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Catatan -->
<div class="modal fade" id="notesModal" tabindex="-1" role="dialog" aria-labelledby="notesModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notesModalLabel">-</h5>
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

<!-- Modal Approval -->
<div class="modal fade text-left" id="approve" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <form id="approveForm" method="post" action="">
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

<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script>
    $(function() {
        initSelect2($('#marketing_status'), 'Pilih Status');
    });

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

@endsection
