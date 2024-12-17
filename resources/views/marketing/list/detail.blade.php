@extends('templates.main')
@section('title', $title)
@section('content')
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

<div class="col-12">
    <div class="row">
        <div class="no-print pb-2">
            <h4 class="card-title">{{$title}}</h4>

                <a href="{{ route('marketing.list.index') }}" class="btn btn-outline-secondary">
                    <i data-feather="arrow-left" class="mr-50"></i>
                    Kembali
                </a>
                    <a href="{{ route('marketing.list.edit', $data->marketing_id) }}" class="btn btn-primary">
                        <i data-feather="edit-2" class="mr-50"></i>
                        Edit
                    </a>
                    <a class="btn btn-success" href="" data-toggle="modal" data-target="#approve">
                        <i data-feather="check" class="mr-50"></i>
                        Approve
                    </a>
        </div>
    </div>
</div>

<section id="collapsible">
    <div class="row">
        <div class="col-sm-12">
            <div class=" collapse-icon">
                <div class=" p-0">
                    <div class="collapse-default">

                        {{-- COLLAPSE TABLE INFORMASI PENJUALAN --}}
                        <div class="card mb-1">
                            <div id="headingCollapse1" class="card-header color-header collapsed" data-toggle="collapse" role="button" data-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                                <span class="lead collapse-title"> Informasi  Penjualan </span>
                            </div>
                            <div id="collapse1" role="tabpanel" aria-labelledby="headingCollapse1" class="collapse show" aria-expanded="true">
                                <div class="card-body p-2">
                                    <div class="col-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered w-100">
                                                <thead>
                                                    <tr>
                                                        <th>No. DO</th>
                                                        <th>Tanggal Penjualan</th>
                                                        <th>Tanggal Realisasi</th>
                                                        <th>Nama Pelanggan</th>
                                                        <th>Unit Bisnis</th>
                                                        <th>Referensi Dokumen</th>
                                                        <th>Nama Sales</th>
                                                        <th>Catatan</th>
                                                        <th>Total Piutang Penjualan (RP)</th>
                                                        <th>Status Pembayaran</th>
                                                        <th>Status Penjualan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>{{ $data->id_marketing }}</td>
                                                        <td>{{ date('d-M-Y', strtotime($data->sold_at)) }}</td>
                                                        <td>{{ $data->realized_at ?? '-' }}</td>
                                                        <td>{{ $data->customer->name }}</td>
                                                        <td>{{ $data->company->alias }}</td>
                                                        <td>
                                                            @if ($data->doc_reference)
                                                            <a class="dropdown-item" href="{{ route('file.show', ['filename' => $data->doc_reference]) }}" target="_blank">
                                                                <i data-feather='download' class="mr-50"></i>
                                                                <span>Download</span>
                                                            </a>
                                                            @else
                                                            <span>-</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $data->sales->name }}</td>
                                                        <td>
                                                            @if ($data->notes)
                                                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#notesModal">
                                                                Lihat Catatan
                                                            </button>
                                                            @else
                                                            <span>-</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ number_format($data->grand_total, 2, '.', ',') }}</td>
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
                                                </tbody>
                                            </table>

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
                                                                <td>{{ number_format($item->price, 2, '.', ',') }}</td>
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
                                                            <td>{{ number_format($item->price, 2, '.', ',') }}</td>
                                                            <td>{{ $item->weight_avg }}</td>
                                                            <td>{{ $item->uom->name }}</td>
                                                            <td>{{ number_format($item->qty, 2, '.', ',') }}</td>
                                                            <td>{{ number_format($item->weight_total, 2, '.', ',') }}</td>
                                                            <td>{{ number_format($item->total_price, 2, '.', ',') }}</td>
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
                                                                <td>{{ $item->qty }}</td>
                                                                <td>{{ $item->uom->name }}</td>
                                                                <td>{{ $item->exit_at }}</td>
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
            <form method="post" action="{{ route('project.chick-in.approve', 'test') }}">
            {{csrf_field()}}
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel1">Konfirmasi Approve Chick In</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="id" value="">
                    <input type="hidden" name="act" id="act" value="">
                    <input type="text" class="form-control flatpickr-inline" name="first_day_old_chick" placeholder="Pilih tanggal umur ayam 1 hari" required>
                    <br><p>Apakah kamu yakin ingin menyetujui data chick in ini ?</p>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Ya</button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Tidak</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
