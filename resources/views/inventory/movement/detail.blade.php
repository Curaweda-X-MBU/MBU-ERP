@extends('templates.main')
@section('title', $title)
@section('content')
<script src="{{asset('app-assets/vendors/js/forms/repeater/jquery.repeater.min.js')}}"></script>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{$title}} ( {{ $data->movement_number??'PND-'.$data->origin->location->company->alias.'-'.str_pad($data->stock_movement_id, 5, '0', STR_PAD_LEFT) }} )</h4>
                <div class="float-right">
                    <a href="{{ route('inventory.movement.index') }}" class="btn btn-outline-warning">Kembali</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="card-title">Gudang Asal</h4>
                                        <div class="row">
                                            <table class="table table-striped w-100">
                                                <tr>
                                                    <td>Unit Bisnis</td>
                                                    <td>:</td>
                                                    <td>{{ $data->origin->location->company->name ?? '' }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Area</td>
                                                    <td>:</td>
                                                    <td>{{ $data->origin->location->area->name ?? '' }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Lokasi</td>
                                                    <td>:</td>
                                                    <td>{{ $data->origin->location->name ?? '' }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Gudang</td>
                                                    <td>:</td>
                                                    <td>{{ $data->origin->name ?? '' }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Produk</td>
                                                    <td>:</td>
                                                    <td>{{ $data->product->name ?? '' }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="card-title">Gudang Tujuan</h4>
                                        <div class="row">
                                            <table class="table table-striped w-100">
                                                <tr>
                                                    <td>Unit Bisnis</td>
                                                    <td>:</td>
                                                    <td>{{ $data->destination->location->company->name ?? '' }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Area</td>
                                                    <td>:</td>
                                                    <td>{{ $data->destination->location->area->name ?? '' }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Lokasi</td>
                                                    <td>:</td>
                                                    <td>{{ $data->destination->location->name ?? '' }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Gudang</td>
                                                    <td>:</td>
                                                    <td>{{ $data->destination->name ?? '' }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Jumlah Transfer</td>
                                                    <td>:</td>
                                                    <td>{{ number_format($data->transfer_qty, 0, ',', '.') ?? '' }} {{ $data->product->uom->name ?? '' }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="alert alert-secondary" role="alert">
                                    <h4 class="alert-heading">Alasan Transfer</h4>
                                    <div class="alert-body">
                                        {{ $data->notes }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 mt-2">
                        <h4 class="card-title"><li>Armada Angkut</li></h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped w-100">
                                <thead>
                                    <th>Vendor</th>
                                    <th>Plat Nomor</th>
                                    <th>Nomor Surat Jalan</th>
                                    <th>Dokumen</th>
                                    <th>Biaya Transport /Item</th>
                                    <th>Biaya Transport</th>
                                    <th>Nama Sopir</th>
                                </thead>
                                <tbody>
                                    @foreach ($data->stock_movement_vehicle as $item)
                                        <tr>
                                            <td>{{ $item->supplier->name ?? '' }}</td>
                                            <td>{{ $item->vehicle_number }}</td>
                                            <td>{{ $item->travel_document_number }}</td>
                                            <td>
                                                @if ($item->travel_document)
                                                <a class="dropdown-item" href="{{ route('file.show', ['filename' => $item->travel_document]) }}" target="_blank">
                                                    <i data-feather='download' class="mr-50"></i>
                                                    <span>Download</span>
                                                </a>
                                                @endif
                                            </td>
                                            <td>{{ number_format($item->transport_amount_item>0?$item->transport_amount_item:$item->transport_amount/$data->transfer_qty, 0, ',', '.') }}</td>
                                            <td>{{ number_format($item->transport_amount, 0, ',', '.') }}</td>
                                            <td>{{ $item->driver_name }}</td>
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
</div>
@endsection