@extends('templates.main')
@section('title', $title)
@section('content')
@php
    use Carbon\Carbon;
@endphp
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">{{ $title }}</h4>
                                    {{-- @if (in_array(session('users')->role, [1,7])) --}}
                                    <div class="float-right">
                                        <a href="{{ route('ph.performance.add') }}" type="button" class="btn btn-outline-primary waves-effect">Tambah Baru</a>
                                    </div>
                                    {{-- @endif --}}
                                </div>
                                <div class="card-body">
                                    <div class="card-datatable">
                                        <div class="table-responsive mb-2">
                                            <table id="datatable" class="table table-bordered table-striped w-100">
                                                <thead>
                                                    <th>Tahun</th>
                                                    <th>Bulan</th>
                                                    <th>Total Populasi</th>
                                                    <th>Total Mati</th>
                                                    <th>Total Culling</th>
                                                    <th>Total Deplesi</th>
                                                    <th>Total Deplesi (%)</th>
                                                    <th>Rata-rata BW</th>
                                                    <th>Aksi</th>
                                                </thead>
                                                <tbody>
                                                    @foreach ($data as $item)
                                                        <tr>
                                                            <td>{{ $item->year }}</td>
                                                            <td>{{ Carbon::parse($item->year.'-'.$item->month.'-01')->format('F') }}</td>
                                                            <td>{{ number_format($item->total_population, 0, ',', '.') }}</td>
                                                            <td>{{ number_format($item->total_death, 0, ',', '.') }}</td>
                                                            <td>{{ number_format($item->total_culling, 0, ',', '.') }}</td>
                                                            <td>{{ number_format($item->total_depletion, 0, ',', '.') }}</td>
                                                            <td>{{ $item->percentage_depletion }} %</td>
                                                            <td>{{ number_format($item->average_bw, 0, ',', '.') }}</td>
                                                            <td>
                                                                <div class="dropdown dropleft">
                                                                    <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-toggle="dropdown">
                                                                        <i data-feather="more-vertical"></i>
                                                                    </button>
                                                                    <div class="dropdown-menu">
                                                                        <a class="dropdown-item" href="{{ route('ph.performance.detail', ["month" => $item->month, "year" => $item->year]) }}">
                                                                            <i data-feather='info' class="mr-50"></i>
                                                                            <span>Detail</span>
                                                                        </a>
                                                                    </div>
                                                                </div>
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

                    <div class="modal fade text-left" id="delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable" role="document">
                            <div class="modal-content">
                                <form method="post" action="{{ route('ph.performance.delete', 'test') }}">
                                {{csrf_field()}}
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="myModalLabel1">Konfirmasi hapus data</h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="id" id="id" value="">
                                        <input type="hidden" name="act" id="act" value="">
                                        <p>Apakah kamu yakin ingin menghapus data ?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-danger">Ya</button>
                                        <button type="button" class="btn btn-primary" data-dismiss="modal">Tidak</button>
                                    </div>
                                </form>
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
                            $('#delete').on('show.bs.modal', function (event) {
                                var button = $(event.relatedTarget) 
                                var id = button.data('id')
                                var modal = $(this)
                                modal.find('.modal-body #id').val(id)
                            });
                        });
                    </script>

@endsection