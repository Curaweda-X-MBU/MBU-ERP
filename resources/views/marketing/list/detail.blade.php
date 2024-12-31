@extends('templates.main')
@section('title', $title)
@section('content')
@php
    $nominalPenjualan = $data->grand_total;
    $nominalSisaBayar = $data->marketing_payments->sum('payment_nominal');
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
                    @if ($data->marketing_status != 4)
                    <a href="{{ $data->marketing_status == 4 ? route('marketing.list.realization', $data->marketing_id) : route('marketing.list.edit', $data->marketing_id) }}" class="btn btn-primary">
                        <i data-feather="edit-2" class="mr-50"></i>
                        Edit
                    </a>
                    @endif
                    @php
                        $roleAccess = Auth::user()->role;
                    @endphp
                    @if ($roleAccess->hasPermissionTo('marketing.list.approve') && $data->marketing_status != 3 && $data->marketing_status != 4)
                        <a class="btn btn-success" href="" data-toggle="modal" data-target="#approve">
                            <i data-feather="check" class="mr-50"></i>
                            Approve
                        </a>
                    @endif
                    <a href="{{ route('marketing.list.index') }}" class="btn btn-outline-secondary">
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
                                <div class="col-md-6">
                                    <table class="table table-striped w-100">
                                        <tr>
                                            <td style="width: 25%"><b>No. DO</b></td>
                                            <td style="width: 5%">:</td>
                                            <td>{{ $data->id_marketing }}</td>
                                        </tr>
                                        <tr>
                                            <td style="width: 25%"><b>Tanggal Penjualan</b></td>
                                            <td style="width: 5%">:</td>
                                            <td>{{ date('d-M-Y', strtotime($data->sold_at)) }}</td>
                                        </tr>
                                        <tr>
                                            <td style="width: 25%"><b>Tanggal Realisasi</b></td>
                                            <td style="width: 5%">:</td>
                                            <td>{{ @$data->realized_at ? date('d-M-Y', strtotime($data->realized_at)) : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td style="width: 25%"><b>Nama Pelanggan</b></td>
                                            <td style="width: 5%">:</td>
                                            <td>{{ $data->customer->name }}</td>
                                        </tr>
                                        <tr>
                                            <td style="width: 25%"><b>Unit Bisnis</b></td>
                                            <td style="width: 5%">:</td>
                                            <td>{{ $data->company->name }}</td>
                                        </tr>
                                        <tr>
                                            <td style="width: 25%"><b>Referensi Dokumen</b></td>
                                            <td style="width: 5%">:</td>
                                            <td>
                                                @if ($data->doc_reference)
                                                    <a class="p-0" href="{{ route('file.show', ['filename' => $data->doc_reference]) }}" target="_blank">
                                                        <i data-feather='download' class="mr-50"></i>
                                                        <span>Download</span>
                                                    </a>
                                                @else
                                                    <span>-</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width: 25%"><b>Nama Sales</b></td>
                                            <td style="width: 5%">:</td>
                                            <td>{{ @$data->sales->name ? $data->sales->name : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td style="width: 25%"><b>Catatan</b></td>
                                            <td style="width: 5%">:</td>
                                            <td>
                                                @if ($data->notes)
                                                    <button type="button" class="btn btn-link p-0 m-0" data-toggle="modal" data-target="#notesModal">
                                                        Lihat Catatan
                                                    </button>
                                                @else
                                                    <span>-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div>
                                    <table class="table table-striped w-100">
                                        <tr>
                                            <td style="width: 25%"><b>Pajak</b></td>
                                            <td style="width: 5%">:</td>
                                            <td>{{ \App\Helpers\Parser::toLocale($data->tax) }} %</td>
                                        </tr>
                                        <tr>
                                            <td style="width: 25%"><b>Diskon</b></td>
                                            <td style="width: 5%">:</td>
                                            <td>Rp. {{ \App\Helpers\Parser::toLocale($data->discount) }}</td>
                                        </tr>
                                        <tr>
                                            <td style="width: 25%"><b>Nominal Penjualan</b></td>
                                            <td style="width: 5%">:</td>
                                            <td>Rp. {{ \App\Helpers\Parser::toLocale($nominalPenjualan) }}</td>
                                        </tr>
                                        <tr>
                                            <td style="width: 25%"><b>Nominal Sudah Bayar</b></td>
                                            <td style="width: 5%">:</td>
                                            <td class="text-success">Rp. {{ \App\Helpers\Parser::toLocale($nominalSisaBayar) }}</td>
                                        </tr>
                                        <tr>
                                            <td style="width: 25%"><b>Nominal Sisa Bayar</b></td>
                                            <td style="width: 5%">:</td>
                                            <td class="text-danger">Rp. {{ \App\Helpers\Parser::toLocale($nominalPenjualan - $nominalSisaBayar) }}</td>
                                        </tr>
                                        <tr>
                                            <td style="width: 25%"><b>Status Pembayaran</b></td>
                                            <td style="width: 5%">:</td>
                                            <td>
                                                @php
                                                    $statusPayment = App\Constants::MARKETING_PAYMENT_STATUS;
                                                @endphp
                                                @switch($data->payment_status)
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
                                            <td style="width: 25%"><b>Status Penjualan</b></td>
                                            <td style="width: 5%">:</td>
                                            <td>
                                                @php
                                                    $statusMarketing = App\Constants::MARKETING_STATUS;
                                                @endphp
                                                @switch($data->marketing_status)
                                                    @case(1)
                                                        <div class="badge badge-pill badge-warning">{{ $statusMarketing[$data->marketing_status] }}</div>
                                                        @break
                                                    @case(2)
                                                        <div class="badge badge-pill badge-danger">{{ $statusMarketing[$data->marketing_status] }}</div>
                                                        @break
                                                    @case(3)
                                                        <div class="badge badge-pill badge-success">{{ $statusMarketing[$data->marketing_status] }}</div>
                                                        @break
                                                    @default
                                                        <div class="badge badge-pill badge-primary">{{ $statusMarketing[$data->marketing_status] }}</div>
                                                @endswitch
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <!-- Modal -->
                                <div class="modal fade" id="notesModal" tabindex="-1" role="dialog" aria-labelledby="notesModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="notesModalLabel">Catatan Penjualan</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <p>{{ $data->notes }}</p>
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
    </div>
</div>

<section id="collapsible">
    <div class="row">
        <div class="col-sm-12">
            <div class=" collapse-icon">
                <div class=" p-0">
                    <div class="collapse-default">
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
                                                    <th>Item</th>
                                                    <th>Price</th>
                                                </thead>
                                                <tbody>
                                                    @if (count($data->marketing_addit_prices) > 0)
                                                        @foreach ($data->marketing_addit_prices as $index => $item)
                                                            <tr>
                                                                <td>{{  $index + 1 }}</td>
                                                                <td>{{ $item->item }}</td>
                                                                <td>{{ \App\Helpers\Parser::toLocale($item->price) }}</td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                    <tr>
                                                        <td class="text-center" colspan="3">Tidak ada data biaya lainnya</td>
                                                    </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- COLLAPSE TABLE PRODUK PENJUALAN --}}
                        <div class="card mb-1">
                            <div id="headingCollapse3" class="card-header color-header collapsed" data-toggle="collapse" role="button" data-target="#collapse3" aria-expanded="true" aria-controls="collapse3">
                                <span class="lead collapse-title"> Produk  Penjualan </span>
                            </div>
                            <div id="collapse3" role="tabpanel" aria-labelledby="headingCollapse3" class="collapse show" aria-expanded="true">
                                <div class="card-body p-2">
                                    <div class="col-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered w-100">
                                                <thead>
                                                    <th>No</th>
                                                    <th>Kandang/Hatcher</th>
                                                    <th>Nama Produk</th>
                                                    <th>Harga Satuan (Rp)</th>
                                                    <th>Bobot AVG</th>
                                                    <th>UOM</th>
                                                    <th>QTY</th>
                                                    <th>Total Bobot</th>
                                                    <th>Total Penjualan (Rp)</th>
                                                </thead>
                                                <tbody>
                                                    @foreach ($data->marketing_products as $index => $item)
                                                        <tr>
                                                            <td>{{ $index + 1 }}</td>
                                                            <td>{{ $item->warehouse->name }}</td>
                                                            <td>{{ $item->product->name }}</td>
                                                            <td>{{ \App\Helpers\Parser::toLocale($item->price) }}</td>
                                                            <td>{{ \App\Helpers\Parser::toLocale($item->weight_avg) }}</td>
                                                            <td>{{ $item->uom->name }}</td>
                                                            <td>{{ \App\Helpers\Parser::toLocale($item->qty) }}</td>
                                                            <td>{{ \App\Helpers\Parser::toLocale($item->weight_total) }}</td>
                                                            <td>{{ \App\Helpers\Parser::toLocale($item->total_price) }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- COLLAPSE TABLE ARMADA ANGKUT --}}
                        <div class="card mb-1">
                            <div id="headingCollapse4" class="card-header color-header collapsed" data-toggle="collapse" role="button" data-target="#collapse4" aria-expanded="true" aria-controls="collapse4">
                                <span class="lead collapse-title"> Armada Angkut </span>
                            </div>
                            <div id="collapse4" role="tabpanel" aria-labelledby="headingCollapse4" class="collapse show" aria-expanded="true">
                                <div class="card-body p-2">
                                    <div class="col-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered w-100">
                                                <thead>
                                                    <th>No</th>
                                                    <th>No. Polisi</th>
                                                    <th>Jumlah</th>
                                                    <th>uom</th>
                                                    <th>Waktu Keluar Kandang</th>
                                                    <th>Nama Pengirim</th>
                                                    <th>Nama Driver</th>
                                                </thead>
                                                <tbody>
                                                    @if (count($data->marketing_delivery_vehicles) > 0)
                                                        @foreach ($data->marketing_delivery_vehicles as $index => $item)
                                                            <tr>
                                                                <td>{{  $index + 1 }}</td>
                                                                <td>{{ $item->plat_number }}</td>
                                                                <td>{{ \App\Helpers\Parser::toLocale($item->qty) }}</td>
                                                                <td>{{ $item->uom->name }}</td>
                                                                <td>{{ date('d-M-Y H:i', strtotime($item->exit_at)) }}</td>
                                                                <td>{{ $item->sender->name }}</td>
                                                                <td>{{ $item->driver_name }}</td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                    <tr>
                                                        <td class="text-center" colspan="7">Belum ada data armada angkut</td>
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

<div class="modal fade text-left" id="approve" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <form id="approveForm" method="post" action="{{ route('marketing.list.approve', $data->marketing_id) }}">
            {{csrf_field()}}
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel1">Konfirmasi Approve Penjualan</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="is_approved" id="is_approved" value="">
                    <div class="form-group">
                        <label for="marketing_status" class="form-label">Status Penjualan<i class="text-danger">*</i></label>
                        <select name="marketing_status" id="marketing_status" class="form-control" required>
                            <option value="2" selected>Penawaran</option>
                            <option value="3">Final</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="notes" class="form-label">Catatan</label>
                        <textarea name="approval_notes" id="notes" class="form-control"></textarea>
                    </div>
                    <br><p>Apakah anda yakin ingin menyetujui data penjualan ini ?</p>
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
</script>

@endsection
