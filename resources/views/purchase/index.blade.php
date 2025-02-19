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
                                    @if (Auth::user()->role->hasPermissionTo('purchase.add'))
                                    <div class="float-right">
                                        <a href="{{ route('purchase.add') }}" type="button" class="btn btn-outline-primary waves-effect">Tambah Baru</a>
                                    </div>
                                    @endif
                                    {{-- @endif --}}
                                </div>
                                <div class="card-body">
                                    <div class="card-datatable">
                                        <div class="table-responsive mb-2">
                                            <table id="datatable" class="table table-bordered table-striped w-100" style="font-size: 10px">
                                                <thead>
                                                        <th>No. PR</th>
                                                        <th>Vendor</th>
                                                        <th>Nama Pengaju</th>
                                                        <th>Departemen</th>
                                                        <th>Tgl. Dibutuhkan</th>
                                                        <th>Umur Invoice</th>
                                                        <th>Total Dibayar</th>
                                                        <th>Belum Dibayar</th>
                                                        <th>Total (Rp.)</th>
                                                        <th>Status</th>
                                                        <th>Aksi</th>
                                                </thead>
                                                <tbody>
                                                    @foreach ($data as $item)
                                                        @php
                                                            $poDate = Carbon::parse($item->po_date);
                                                            $today = Carbon::today();
                                                            $invAge = $poDate->diffInDays($today);
                                                        @endphp
                                                        <tr>
                                                            <td>{{ $item->pr_number }}</td>
                                                            <td>{{ $item->supplier->name??'' }}</td>
                                                            <td>{{ $item->createdBy->name??'' }}</td>
                                                            <td>{{ $item->createdBy->department->name??'' }} - {{ $item->createdBy->department->company->name??'' }}</td>
                                                            <td>{{ date('d-M-Y', strtotime($item->require_date)) }}</td>
                                                            <td>{{$invAge}} hari</td>
                                                            <td>{{ number_format($item->total_payment??0, 0, ',', '.'   ) }}</td>
                                                            <td>{{ number_format($item->total_remaining_payment??0, 0, ',', '.'   ) }}</td>
                                                            
                                                            <td class="text-right">{{ number_format($item->grand_total==0?$item->total_before_tax:$item->grand_total, 0, ',', '.'   ) }}</td>
                                                            <td>
                                                                @if ($item->rejected)
                                                                    <div class="badge badge-pill badge-danger">Ditolak</div>
                                                                @else
                                                                    @switch($item->status)
                                                                        @case(0)
                                                                            <div class="badge badge-pill badge-outline-secondary">Draft</div>
                                                                            @break
                                                                        @case(1)
                                                                            <div class="badge badge-pill badge-warning">Approval Manager</div>
                                                                            @break
                                                                        @case(2)
                                                                            <div class="badge badge-pill badge-warning">Approval Poultry Health</div>
                                                                            @break
                                                                        @case(3)
                                                                            <div class="badge badge-pill badge-warning">Approval Purchasing</div>
                                                                            @break
                                                                        @case(4)
                                                                            <div class="badge badge-pill badge-warning">Approval Finance</div>
                                                                            @break
                                                                        @case(5)
                                                                            <div class="badge badge-pill badge-warning">Approval Dir. Finance</div>
                                                                            @break
                                                                        @case(6)
                                                                            <div class="badge badge-pill badge-warning">Produk Diterima</div>
                                                                            @break
                                                                        @case(7)
                                                                            <div class="badge badge-pill badge-warning">Pelunasan</div>
                                                                            @break
                                                                        @case(8)
                                                                            <div class="badge badge-pill badge-success">Lunas</div>
                                                                            @break
                                                                        @default
                                                                            <div class="badge badge-pill badge-secondary">N/A</div>
                                                                    @endswitch
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <div class="dropdown dropleft">
                                                                    <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-toggle="dropdown">
                                                                        <i data-feather="more-vertical"></i>
                                                                    </button>
                                                                    <div class="dropdown-menu">
                                                                        @if (Auth::user()->role->hasPermissionTo('purchase.detail'))
                                                                        <a class="dropdown-item" href="{{ route('purchase.detail', $item->purchase_id) }}">
                                                                            <i data-feather="info" class="mr-50"></i>
                                                                            <span>Detail</span>
                                                                        </a>
                                                                        @endif
                                                                        @if (Auth::user()->role->hasPermissionTo('purchase.delete'))
                                                                        <a class="dropdown-item text-danger" href="javascript:void(0);" data-id="{{ $item->purchase_id }}" data-toggle="modal" data-target="#delete">
                                                                            <i data-feather="trash" class="mr-50"></i>
                                                                            <span>Hapus</span>
                                                                        </a>
                                                                        @endif
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
                                <form method="post" action="{{ route('purchase.delete', 'test') }}">
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