@extends('templates.main')
@section('title', $title)
@section('content')
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">{{ $title }}</h4>
                                    {{-- @if (in_array(session('users')->role, [1,7])) --}}
                                    <div class="float-right">
                                        <a href="{{ route('audit.add') }}" type="button" class="btn btn-outline-primary waves-effect">Tambah Baru</a>
                                    </div>
                                    {{-- @endif --}}
                                </div>
                                <div class="card-body">
                                    <div class="card-datatable">
                                        <div class="table-responsive mb-2">
                                            <table id="datatable" class="table table-bordered table-striped w-100">
                                                <thead>
                                                        <th>ID</th>
                                                        <th>Judul</th>
                                                        <th>Kategori</th>
                                                        <th>Prioritas</th>
                                                        <th>Unit Bisnis</th>
                                                        <th>Lokasi</th>
                                                        <th>Departemen</th>
                                                        <th>Tanggal Buat</th>
                                                        <th>Aksi</th>
                                                </thead>
                                                <tbody>
                                                    @foreach ($data as $item)
                                                        <tr>
                                                            <td>{{ $item->audit_id }}</td>
                                                            <td>{{ $item->title }}</td>
                                                            <td>{{ $category[$item->category] ?? 'N/A' }}</td>
                                                            <td>
                                                                <div class="badge badge-pill badge-{{$priority_color[$item->priority]??'secondary'}}">
                                                                    {{ $priority[$item->priority]??'N/A' }}
                                                                </div>
                                                            </td>
                                                            <td>{{ $item->department->company->name ?? '' }}</td>
                                                            <td>{{ $item->department->location->name ?? '' }}</td>
                                                            <td>{{ $item->department->name ?? '' }}</td>
                                                            <td>{{ date('d-m-Y', strtotime($item->created_at)) }}</td>
                                                            <td>
                                                                <div class="dropdown dropleft">
                                                                    <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-toggle="dropdown">
                                                                        <i data-feather="more-vertical"></i>
                                                                    </button>
                                                                    <div class="dropdown-menu">
                                                                        @if ($item->document)
                                                                        <a class="dropdown-item" href="{{ route('file.show', ['filename' => $item->document]) }}" target="_blank">
                                                                            <i data-feather='download' class="mr-50"></i>
                                                                            <span>Download</span>
                                                                        </a>
                                                                        @endif
                                                                        <a class="dropdown-item" href="{{ route('audit.edit', $item->audit_id) }}">
                                                                            <i data-feather="edit-2" class="mr-50"></i>
                                                                            <span>Edit</span>
                                                                        </a>
                                                                        <a class="dropdown-item text-danger" href="javascript:void(0);" data-id="{{ $item->audit_id }}" data-toggle="modal" data-target="#delete">
                                                                            <i data-feather="trash" class="mr-50"></i>
                                                                            <span>Hapus</span>
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
                                <form method="post" action="{{ route('audit.delete', 'test') }}">
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