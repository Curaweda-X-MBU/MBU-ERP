@extends('templates.main')
@section('title', $title)
@section('content')
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">{{ $title }}</h4>
                                    <div class="float-right">
                                        <a href="{{ route('inventory.adjustment.add') }}" type="button" class="btn btn-outline-primary waves-effect">Tambah</a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="card-datatable">
                                        <div class="table-responsive mb-2">
                                            <table id="datatable" class="table table-bordered table-striped w-100">
                                                <thead>
                                                    <th>ID</th>
                                                    <th>Nama Produk</th>
                                                    <th>Gudang</th>
                                                    <th>Tanggal</th>
                                                    <th>Peningkatan</th>
                                                    <th>Penurunan</th>
                                                    <th>Saldo</th>
                                                    <th>Jenis Transaksi</th>
                                                    <th>Catatan</th>
                                                    <th>Oleh</th>
                                                </thead>
                                                <tbody>
                                                    @foreach ($data as $item)
                                                    <tr>
                                                        <td>{{ $item->stock_log_id }}</td>
                                                        <td>{{ $item->stock->product->name??'' }}</td>
                                                        <td>{{ $item->stock->warehouse->name??'' }}</td>
                                                        <td>{{ date('d-M-Y', strtotime($item->created_at)) }}</td>
                                                        <td>{{ number_format($item->increase, 0, ',', '.') }}</td>
                                                        <td>{{ number_format($item->decrease, 0, ',', '.') }}</td>
                                                        <td>{{ number_format($item->remaining_total, 0, ',', '.') }}</td>
                                                        <td>{{ $item->stocked_by }}</td>
                                                        <td>{{ $item->notes }}</td>
                                                        <td>{{ $item->createdBy->name??'' }}</td>
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