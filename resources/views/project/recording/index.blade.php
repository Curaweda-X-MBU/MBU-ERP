@extends('templates.main')
@section('title', $title)
@section('content')
@php
    use Carbon\Carbon;
    use App\Constants;

    if (!function_exists('formatnumber')) {
        function formatnumber($number) {
            return rtrim(rtrim(number_format(floatval($number), 2, ',', '.'), '0'), ',');
        }
    }
@endphp
                    <div class="row">
                        <div class="col-12">
                            <h4 class="card-title">{{ $title }}</h4>
                            <div class="card">
                                <div class="card-header">
                                    <form method="post" action="{{ request()->fullUrl() }}" style="display: inline-flex; column-gap: 10px; width: 40%;">
                                        @csrf
                                        <select name="project_id" class="form-control project_id" required>
                                            @if(isset($param) && isset($param['project']))
                                                <option value="{{ $param['project_id'] }}" selected>{{ $param['project_id']==0?"Semua Project":($param['project']->kandang->name??'') }}</option>
                                            @endif
                                        </select>
                                        <select name="period" class="form-control period" required>
                                            @if(isset($param) && isset($param['period']))
                                                <option value="{{ $param['period'] }}" selected>{{ $param['period']==0?"Semua Periode":$param['period'] }}</option>
                                            @endif
                                        </select>
                                        <div class="input-group-append" id="button-addon2">
                                            <button type="submit" class="btn btn-outline-primary waves-effect" type="button"><i data-feather='search'></i></button>
                                        </div>
                                    </form>
                                    @if (Auth::user()->role->hasPermissionTo('project.recording.add'))
                                    <div class="float-right">
                                        <a href="{{ route('project.recording.add') }}" type="button" class="btn btn-outline-primary waves-effect">Tambah Baru</a>
                                    </div>
                                    @endif
                                </div>
                                <div class="card-body">
                                    <div class="card-datatable">
                                        <div class="table-responsive">
                                            <table id="datatable" class="table table-bordered table-striped w-100" style="font-size: 10px;">
                                                <thead>
                                                    <tr>
                                                        <th rowspan="2">ID</th>
                                                        <th rowspan="2">Nama Project</th>
                                                        <th rowspan="2">Periode</th>
                                                        <th rowspan="2">Umur<br>(hari)</th>
                                                        <th rowspan="2">Waktu Recording</th>
                                                        <th colspan="2" class="text-center">FCR</th>
                                                        <th colspan="4" class="text-center">Deplesi</th>
                                                        <th rowspan="2">Ketepatan Waktu</th>
                                                        <th rowspan="2">Status Perubahan</th>
                                                        <th rowspan="2">Tanggal Submit</th>
                                                        <th rowspan="2">Aksi</th>
                                                    </tr>
                                                    <tr>
                                                        <th>Aktual</th>
                                                        <th>Standar</th>
                                                        <th>Culling</th>
                                                        <th>Mati</th>
                                                        <th>Afkir</th>
                                                        <th>Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($data as $item)
                                                        @php
                                                            $fcrStd = collect($item->project->fcr->fcr_standard);
                                                            $dailyFcrStd = $fcrStd->where('day', $item->day)->first();
                                                            $depletions = $item->recording_depletion;
                                                            $culling = 0;
                                                            $death = 0;
                                                            $afkir = 0;
                                                            foreach ($depletions??[] as $key => $value) {
                                                                $conditionName = $value->product_warehouse->product->name??'n/a';
                                                                if (preg_match('/culling/i', $conditionName)) {
                                                                    $culling += $value->total;
                                                                } elseif (preg_match('/mati/i', $conditionName)) {
                                                                    $death += $value->total;
                                                                } elseif (preg_match('/afkir/i', $conditionName)) {
                                                                    $afkir += $value->total;
                                                                }
                                                            }
                                                            
                                                        @endphp
                                                        <tr>
                                                            <td>{{ $item->recording_id }}</td>
                                                            <td>{{ $item->project->kandang->name??'' }}</td>
                                                            <td>{{ $item->project->period??'' }}</td>
                                                            <td>{{ $item->day }}</td>
                                                            <td>{{ date('d-M-Y', strtotime($item->record_datetime)) }}</td>
                                                            <td>{{ formatnumber($item->fcr_value) }}</td>
                                                            <td>{{ formatnumber($dailyFcrStd->fcr??'N/A') }}</td>
                                                            <td>{{ formatnumber($culling) }}</td>
                                                            <td>{{ formatnumber($death) }}</td>
                                                            <td>{{ formatnumber($afkir) }}</td>
                                                            <td>{{ formatnumber($item->total_depletion) }}</td>
                                                            <td>
                                                                @if ($item->on_time)
                                                                <div class="badge badge-glow badge-success">Tepat Waktu</div>
                                                                @else
                                                                <div class="badge badge-glow badge-warning">Terlambat</div>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if (isset(Constants::REVISION_STATUS[$item->revision_status]))
                                                                    <div class="badge badge-light-secondary">{{ Constants::REVISION_STATUS[$item->revision_status] }}</div>
                                                                @endif
                                                            </td>
                                                            <td>{{ date('d-M-Y H:i', strtotime($item->created_at)) }}</td>
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
                                                                        @if (Auth::user()->role->hasPermissionTo('project.recording.revision-submission'))
                                                                            @if (in_array($item->revision_status, [0, 4]))
                                                                            <a class="dropdown-item text-warning" href="javascript:void(0);" data-id="{{ $item->recording_id }}" data-toggle="modal" data-target="#revision-submission">
                                                                                <i data-feather="edit-2" class="mr-50"></i>
                                                                                <span>Ajukan Perubahan</span>
                                                                            </a>
                                                                            @endif
                                                                        @endif
                                                                        @if (Auth::user()->role->hasPermissionTo('project.recording.edit'))
                                                                            @if ($item->revision_status == 2)
                                                                            <a class="dropdown-item text-info" href="{{ route('project.recording.edit', $item->recording_id) }}">
                                                                                <i data-feather="edit-2" class="mr-50"></i>
                                                                                <span>Ubah</span>
                                                                            </a>
                                                                            @endif
                                                                        @endif
                                                                        @if (Auth::user()->role->hasPermissionTo('project.recording.revision-approval'))
                                                                            @if ($item->revision_status === 1)
                                                                            <a class="dropdown-item text-success" href="javascript:void(0);" data-id="{{ $item->recording_id }}#approve" data-toggle="modal" data-target="#revision-approval">
                                                                                <i data-feather="check" class="mr-50"></i>
                                                                                <span>Setujui</span>
                                                                            </a>
                                                                            <a class="dropdown-item text-danger" href="javascript:void(0);" data-id="{{ $item->recording_id }}#reject" data-toggle="modal" data-target="#revision-approval">
                                                                                <i data-feather="x" class="mr-50"></i>
                                                                                <span>Tolak</span>
                                                                            </a>
                                                                            @endif
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

                    <div class="modal fade text-left" id="revision-submission" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable" role="document">
                            <div class="modal-content">
                                <form method="post" action="{{route('project.recording.revision-submission','test')}}" enctype="multipart/form-data">
                                {{csrf_field()}}
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="myModalLabel1">Konfirmasi perubahan data recording</h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="id" id="id" value="">
                                        <input type="hidden" id="act">
                                        <p>Perubahan data recording dapat dilakukan setalah mendapatkan persetujuan.</p>
                                        Upload dokumen pendukung<br>
                                        <input type="file" class="form-control" name="document_revision" required>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-danger">Kirim</button>
                                        <button type="button" class="btn btn-primary" data-dismiss="modal">Tidak</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade text-left" id="revision-approval" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable" role="document">
                            <div class="modal-content">
                                <form method="post" action="{{route('project.recording.revision-approval','test')}}" enctype="multipart/form-data">
                                {{csrf_field()}}
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="myModalLabel1">Konfirmasi approve perubahan</h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="id" id="id" value="">
                                        <input type="hidden" name="revision_status" id="act" value="">
                                        <div id="approval-message"></div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-danger">Kirim</button>
                                        <button type="button" class="btn btn-primary" data-dismiss="modal">Tidak</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <script src="{{asset('app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js')}}"></script>
                    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js')}}"></script>
                    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>


                    <script>
                        $(function () {
                            $('#datatable').DataTable({
                                // scrollX: true,
                                drawCallback: function( settings ) {
                                    feather.replace();
                                },
                                order: [[0, 'desc']],
                            });

                            $('#revision-submission').on('show.bs.modal', function (event) {
                                var button = $(event.relatedTarget) 
                                var id = button.data('id')
                                var modal = $(this)
                                modal.find('.modal-body #id').val(id)
                                modal.find('.modal-body input[type="file"]').on('change', function() {
                                    const file = this.files[0];
                                    if (file) {
                                        const fileType = file.type;
                                        const maxSize = 2 * 1024 * 1024;
                                        const fileSize = file.size;
                                        const allowedTypes = /^(application\/pdf|image\/(jpeg|jpg))$/;
                                        if (!allowedTypes.test(fileType)) {
                                            alert('Mohon upload file berformat PDF atau JPEG/JPG.');
                                            $(this).val('');
                                        } else if (fileSize > maxSize) {
                                            alert('Ukuran file harus kurang dari 2 MB');
                                            $(this).val('');
                                        } 
                                    }
                                });
                            });

                            $('#revision-approval').on('show.bs.modal', function (event) {
                                var button = $(event.relatedTarget) 
                                var dataBtn = button.data('id')
                                var id = dataBtn.split('#')[0]
                                var status = dataBtn.split('#')[1]
                                var modal = $(this)
                                modal.find('.modal-body #id').val(id)
                                let msg = "Apakah kamu yakin ingin menyetujui perubahan ini";
                                let revisionStatus = 2;
                                if (status === 'reject') {
                                    msg = "Apakah kamu yakin ingin menolak perubahan ini";
                                    revisionStatus = 4;
                                } 
                                modal.find('.modal-body #act').val(revisionStatus)
                                modal.find('.modal-body #approval-message').html(msg);
                            });

                            $('.project_id').select2({
                                placeholder: "Pilih Project",
                                ajax: {
                                    url: '{{ route("project.list.search") }}?project_status_not=1&project_status_not=3&chickin_status=3', 
                                    dataType: 'json',
                                    delay: 250, 
                                    data: function(params) {
                                        return {
                                            q: params.term 
                                        };
                                    },
                                    processResults: function(data) {
                                        data.unshift({
                                            id: 0,
                                            text: "Semua Project"
                                        })                    
                                        return {
                                            results: data
                                        };
                                    },
                                    cache: true
                                }
                            });

                            $('.period').select2({
                                placeholder: "Pilih Periode",
                                ajax: {
                                    url: '{{ route("project.list.search-period") }}?project_status_not=1&project_status_not=3&chickin_status=3', 
                                    dataType: 'json',
                                    delay: 250, 
                                    data: function(params) {
                                        return {
                                            q: params.term 
                                        };
                                    },
                                    processResults: function(data) {
                                        data.unshift({
                                            id: 0,
                                            text: "Semua Periode"
                                        })                    
                                        return {
                                            results: data
                                        };
                                    },
                                    cache: true
                                }
                            });
                        });
                    </script>

@endsection