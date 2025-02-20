@extends('templates.main')
@section('title', $title)
@section('content')
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">{{ $title }}</h4>
                                    <div class="float-right">
                                        <a href="{{ route('inventory.movement.add') }}" type="button" class="btn btn-outline-primary waves-effect">Tambah</a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="card-datatable">
                                        <div class="table-responsive mb-2">
                                            <table id="datatable" class="table table-bordered table-striped w-100">
                                                <thead>
                                                    <th>ID</th>
                                                    <th>Gudang Asal</th>
                                                    <th>Gudang Tujuan</th>
                                                    <th>Nama Produk</th>
                                                    <th>Jumlah Transfer</th>
                                                    <th>Biaya Ekspedisi</th>
                                                    <th>Catatan</th>
                                                    <th>Oleh</th>
                                                    <th>Aksi</th>
                                                </thead>
                                                <tbody>
                                                    @foreach ($data as $item)
                                                    <tr>
                                                        <td>{{ $item->movement_number }}</td>
                                                        <td>{{ $item->origin->name??'' }}</td>
                                                        <td>{{ $item->destination->name??'' }}</td>
                                                        <td>{{ $item->product->name??'' }}</td>
                                                        <td>{{ number_format($item->transfer_qty, 0, ',', '.') }}</td>
                                                        <td>{{ number_format(collect($item->stock_movement_vehicle??[['transport_amount' => 0]])->sum('transport_amount'), 0, ',', '.') }}</td>
                                                        <td>{{ $item->notes }}</td>
                                                        <td>{{ $item->createdBy->name??'' }}</td>
                                                        <td>
                                                            <a href="{{ route('inventory.movement.detail', $item->stock_movement_id) }}">
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