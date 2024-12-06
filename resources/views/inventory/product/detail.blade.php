@extends('templates.main')
@section('title', $title)
@section('content')
<style>
    .color-header {
        color: white;
        background: linear-gradient(118deg, #76A8D8, #c9e5ff);
    }
</style>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ $title }}</h4>
                <a href="{{ route('inventory.product.index') }}" class="btn btn-outline-secondary">
                    <i data-feather="arrow-left" class="mr-50"></i>
                    Kembali
                </a>
            </div>
            <div class="card-body">
                <div class="card-datatable">
                    <div class="table-responsive mb-2">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-striped w-100">
                                        <tr>
                                            <td style="width: 25%"><b>Unit Bisnis</b></td>
                                            <td style="width: 5%">:</td>
                                            <td>{{ $data[0]->product->company->name??'-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><b>Nama Produk</b></td>
                                            <td>:</td>
                                            <td>{{ $data[0]->product->name??'-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><b>Kategory</b></td>
                                            <td>:</td>
                                            <td>{{ $data[0]->product->product_category->name??'-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><b>Sub Kategory</b></td>
                                            <td>:</td>
                                            <td>{{ $data[0]->product->product_sub_category->name??'-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><b>Merek</b></td>
                                            <td>:</td>
                                            <td>{{ $data[0]->product->brand??'-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><b>Satuan</b></td>
                                            <td>:</td>
                                            <td>{{ $data[0]->product->uom->name??'-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-striped w-100">
                                        <tr>
                                            <td><b>Harga Beli</b></td>
                                            <td>:</td>
                                            <td>Rp. {{ number_format($data[0]->product->product_price??0, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td><b>Harga Jual</b></td>
                                            <td>:</td>
                                            <td>Rp. {{ number_format($data[0]->product->selling_price??0, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td style="width: 25%"><b>Untuk Dijual</b></td>
                                            <td style="width: 5%">:</td>
                                            <td>{{ $data[0]->product->can_be_sold?'Ya':'Tidak' }}</td>
                                        </tr>
                                        <tr>
                                            <td><b>Untuk Dibeli</b></td>
                                            <td>:</td>
                                            <td>{{ $data[0]->product->can_be_purchased?'Ya':'Tidak' }}</td>
                                        </tr>
                                        <tr>
                                            <td><b>Pajak</b></td>
                                            <td>:</td>
                                            <td>{{ $data[0]->product->tax??'-' }}%</td>
                                        </tr>
                                        <tr>
                                            <td><b>Total Stok</b></td>
                                            <td>:</td>
                                            <td>{{ number_format($total_quantity??0, 0,',','.') }}</td>
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
                        <div class="card mb-1">
                            <div id="headingCollapse1" class="card-header color-header collapsed" data-toggle="collapse" role="button" data-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                                <span class="lead collapse-title"> Informasi Gudang / Tempat Penyimpanan </span>
                            </div>
                            <div id="collapse1" role="tabpanel" aria-labelledby="headingCollapse1" class="collapse show" aria-expanded="true">
                                <div class="card-body p-2">
                                    <div class="col-12">
                                        <table class="datatable table table-bordered table-striped w-100">
                                            <thead>
                                                <th>Nama Gudang</th>
                                                <th>Lokasi</th>
                                                <th>Jumlah Stok</th>
                                            </thead>
                                            <tbody>
                                                @foreach ($data as $item)
                                                <tr>
                                                    <td>{{ $item->warehouse->name??'' }}</td>
                                                    <td>{{ $item->warehouse->location->name??'' }}</td>
                                                    <td>{{ number_format($item->quantity, 0, ',', '.') }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-1">
                            <div id="headingCollapse3" class="card-header color-header collapsed" data-toggle="collapse" role="button" data-target="#collapse3" aria-expanded="true" aria-controls="collapse3">
                                <span class="lead collapse-title"> Buku Stock Produk </span>
                            </div>
                            <div id="collapse3" role="tabpanel" aria-labelledby="headingCollapse3" class="collapse show" aria-expanded="true">
                                <div class="card-body p-2">
                                    <div class="col-12">
                                        <table class="datatable table table-bordered table-striped w-100">
                                            <thead>
                                                <th>ID</th>
                                                <th>Tanggal</th>
                                                <th>Peningkatan</th>
                                                <th>Penurunan</th>
                                                <th>Saldo</th>
                                                <th>Jenis Transaksi</th>
                                                <th>Catatan</th>
                                                <th>Oleh</th>
                                            </thead>
                                            <tbody>
                                                @if (count($stock_log)>0)
                                                    @foreach ($stock_log as $item)
                                                    <tr>
                                                        <td>{{ $item->stock_log_id }}</td>
                                                        <td>{{ date('d-M-Y', strtotime($item->stock_date)) }}</td>
                                                        <td>{{ number_format($item->increase, 0, ',', '.') }}</td>
                                                        <td>{{ number_format($item->decrease, 0, ',', '.') }}</td>
                                                        <td>{{ number_format($item->remaining_total, 0, ',', '.') }}</td>
                                                        <td>{{ $item->stocked_by }}</td>
                                                        <td>{{ $item->notes }}</td>
                                                        <td>{{ $item->createdBy->name??'' }}</td>
                                                    </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>


                        {{-- @include('purchase.detail-collapse.purchase-other') --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="{{asset('app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js')}}"></script>

<script>
    $(function () {
        $('.datatable').DataTable({
            drawCallback: function( settings ) {
                feather.replace();
            },
            order: [[0, 'desc']],
        });
    });
</script>
@endsection