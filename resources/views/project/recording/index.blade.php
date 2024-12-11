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
                                    @if (Auth::user()->role->hasPermissionTo('project.recording.add'))
                                    <div class="float-right">
                                        <a href="{{ route('project.recording.add') }}" type="button" class="btn btn-outline-primary waves-effect">Tambah Baru</a>
                                    </div>
                                    @endif
                                </div>
                                <div class="card-body">
                                    <div class="card-datatable">
                                        <div class="table-responsive">
                                            <table id="datatable" class="table table-bordered table-striped w-100">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Nama Project</th>
                                                        <th>Periode</th>
                                                        <th>Waktu Recording</th>
                                                        <th>Ketepatan Waktu</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($data as $item)
                                                        <tr>
                                                            <td>{{ $item->recording_id }}</td>
                                                            <td>{{ $item->project->kandang->name??'' }}</td>
                                                            <td>{{ $item->project->period??'' }}</td>
                                                            <td>{{ date('d-M-Y', strtotime($item->created_at)) }}</td>
                                                            <td>
                                                                @php
                                                                    $createdAt = Carbon::parse($item->created_at);
                                                                    $recordDate = Carbon::parse($item->record_datetime);
                                                                    $onTime = $createdAt->isSameDay($recordDate)?true:false;
                                                                @endphp
                                                                @if ($onTime)
                                                                <div class="badge badge-glow badge-success">Tepat Waktu</div>
                                                                @else
                                                                <div class="badge badge-glow badge-warning">Terlambat</div>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <div class="dropdown dropleft">
                                                                    <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-toggle="dropdown">
                                                                        <i data-feather="more-vertical"></i>
                                                                    </button>
                                                                    <div class="dropdown-menu">
                                                                        @if (Auth::user()->role->hasPermissionTo('project.recording.detail'))
                                                                        <a class="dropdown-item" href="{{ route('project.recording.detail', $item->recording_id) }}">
                                                                            <i data-feather="info" class="mr-50"></i>
                                                                            <span>Detail</span>
                                                                        </a>
                                                                        @endif
                                                                        {{-- <a class="dropdown-item text-danger" href="javascript:void(0);" data-id="{{ $item->recording_id }}" data-toggle="modal" data-target="#delete">
                                                                            <i data-feather="trash" class="mr-50"></i>
                                                                            <span>Hapus</span>
                                                                        </a> --}}
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
                                <form method="post" action="{{route('project.recording.delete','test')}}">
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