@extends('templates.main')
@section('title', $title)
@section('content')
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">{{ $title }}</h4>
                                    <div class="pull-right">
                                        @if (Auth::user()->role->hasPermissionTo('project.chick-in.approve'))
                                        <a class="btn btn-success" href="javascript:void(0);" data-id="1" data-toggle="modal" data-target="#bulk-approve">
                                            Approve
                                        </a>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="card-datatable">
                                        <div class="table-responsive mb-2">
                                            <form method="post" action="{{ route('project.chick-in.approve', 'test') }}" id="form-approve">
                                            {{csrf_field()}}
                                            <table id="datatable" class="table table-bordered table-striped w-100">
                                                <thead>
                                                    @if (Auth::user()->role->hasPermissionTo('project.chick-in.approve'))
                                                    <th>
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input" id="checkAll">
                                                            <label class="custom-control-label" for="checkAll"></label>
                                                        </div>
                                                    </th>
                                                    @endif
                                                    <th>ID</th>
                                                    <th>Unit Bisnis</th>
                                                    <th>Kategori Produk</th>
                                                    <th>Area</th>
                                                    <th>Lokasi</th>
                                                    <th>Kandang</th>
                                                    <th>Kapasitas</th>
                                                    <th>Periode</th>
                                                    <th>Status Chick-in</th>
                                                    <th>Aksi</th>
                                                </thead>
                                                <tbody>
                                                    @foreach ($data as $item)
                                                        <tr>
                                                            @if (Auth::user()->role->hasPermissionTo('project.chick-in.approve'))
                                                            <td>
                                                                <div class="custom-control custom-checkbox">
                                                                    <input type="checkbox" class="custom-control-input {{ $item->chickin_status === 2 ? 'select-row':''  }}" name="project_ids[]" id="project-id-{{ $item->project_id }}" value="{{ $item->project_id }}" {{ $item->chickin_status === 2 ? '':'disabled'  }}>
                                                                    <label class="custom-control-label" for="project-id-{{ $item->project_id }}"></label>
                                                                </div>
                                                            </td>
                                                            @endif
                                                            <td>{{ $item->project_id }}</td>
                                                            <td>{{ $item->kandang->company->name??'' }}</td>
                                                            <td>{{ $item->product_category->name??'' }}</td>
                                                            <td>{{ $item->kandang->location->area->name??'' }}</td>
                                                            <td>{{ $item->kandang->location->name??'' }}</td>
                                                            <td>{{ $item->kandang->name??'' }}</td>
                                                            <td>{{ number_format($item->capacity, 0, ',', '.'   ) }}</td>
                                                            <td>{{ $item->period }}</td>
                                                            <td>
                                                                @php
                                                                    $statusChickIn = App\Constants::PROJECT_CHICKIN_STATUS;
                                                                @endphp
                                                                @switch($item->chickin_status)
                                                                    @case(1)
                                                                        <div class="badge badge-pill badge-warning">{{ $statusChickIn[$item->chickin_status] }}</div>
                                                                        @break
                                                                    @case(2)
                                                                        <div class="badge badge-pill badge-primary">{{ $statusChickIn[$item->chickin_status] }}</div>
                                                                        @break
                                                                    @case(3)
                                                                        <div class="badge badge-pill badge-success">{{ $statusChickIn[$item->chickin_status] }}</div>
                                                                        @break
                                                                    @default
                                                                        <div class="badge badge-pill badge-secondary">N/A</div>
                                                                @endswitch
                                                            </td>
                                                            <td>
                                                                <div class="dropdown dropleft">
                                                                    <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-toggle="dropdown">
                                                                        <i data-feather="more-vertical"></i>
                                                                    </button>
                                                                    <div class="dropdown-menu">
                                                                        <a class="dropdown-item" href="{{ route('project.chick-in.detail', $item->project_id) }}">
                                                                            <i data-feather="info" class="mr-50"></i>
                                                                            <span>Detail</span>
                                                                        </a>
                                                                        @if (Auth::user()->role->hasPermissionTo('project.chick-in.delete'))
                                                                        <a class="dropdown-item text-danger" href="javascript:void(0);" data-id="{{ $item->project_id }}" data-toggle="modal" data-target="#delete">
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
                                <form method="post" action="{{ route('project.chick-in.delete', 'test') }}">
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

                    <div class="modal fade text-left" id="bulk-approve" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable" role="document">
                            <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="myModalLabel1">Konfirmasi Approve Chick In</h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <br><p>Apakah kamu yakin ingin menyetujui data chick in ini ?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" id="btn-bulk-approve">Ya</button>
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
                                scrollX: true,
                                drawCallback: function( settings ) {
                                    feather.replace();
                                },
                                order: [[1, 'desc']],
                                columnDefs: [
                                    { orderable: false, targets: [0] } 
                                ]
                            });
                            $('#delete').on('show.bs.modal', function (event) {
                                var button = $(event.relatedTarget) 
                                var id = button.data('id')
                                var modal = $(this)
                                modal.find('.modal-body #id').val(id)
                            });

                            $('#bulk-approve').on('show.bs.modal', function (event) {
                                $('#btn-bulk-approve').click(function (e) { 
                                    $('#form-approve').submit();
                                });
                            });

                            $('#checkAll').change(function (e) { 
                                e.preventDefault();
                                if ($(this).is(':checked')) {
                                    $('.select-row').prop('checked',true);
                                } else {
                                    $('.select-row').prop('checked',false);
                                }
                            });
                        });
                    </script>

@endsection