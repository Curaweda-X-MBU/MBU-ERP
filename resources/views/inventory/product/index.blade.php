@extends('templates.main')
@section('title', $title)
@section('content')
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">{{ $title }}</h4>
                                </div>
                                <div class="card-body">
                                    <div class="card-datatable">
                                        <div class="table-responsive mb-2">
                                            <table id="datatable" class="table table-bordered table-striped w-100">
                                                <thead>
                                                        <th>ID Produk</th>
                                                        <th>Nama</th>
                                                        <th>Harga Beli (Rp)</th>
                                                        <th>Harga Jual (Rp)</th>
                                                        <th>Kategori</th>
                                                        <th>Total Stok</th>
                                                        <th>Satuan</th>
                                                        <th>Aksi</th>
                                                </thead>
                                                <tbody>
                                                    @foreach ($data as $item)
                                                        <tr>
                                                            <td>{{ $item->product_id }}</td>
                                                            <td>{{ $item->product->name??'' }}</td>
                                                            <td class="text-right">{{ number_format($item->product->product_price??0, 0, ',', '.') }}</td>
                                                            <td class="text-right">{{ number_format($item->product->selling_price??0, 0, ',', '.') }}</td>
                                                            <td>{{ $item->product->product_category->name??'' }}</td>
                                                            <td class="text-right">{{ number_format($item->total_quantity, 0, ',', '.'   ) }}</td>
                                                            <td>{{ $item->product->uom->name??'' }}</td>
                                                            <td>
                                                                <a href="{{ route('inventory.product.detail', $item->product_id) }}">
                                                                    <i data-feather="info" class="mr-50"></i>
                                                                    <span>Detail</span>
                                                                </a>
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

                    <script src="{{asset('app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js')}}"></script>
                    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js')}}"></script>

                    <script>
                        $(function () {
                            $('#datatable').DataTable({
                                // scrollX: true,
                                drawCallback: function( settings ) {
                                    feather.replace();
                                },
                                order: [[0, 'desc']],
                            });
                        });
                    </script>

@endsection