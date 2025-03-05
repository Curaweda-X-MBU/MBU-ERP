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
                                    <div></div>
                                    <div class="float-right">
                                        @if (Auth::user()->role->hasPermissionTo('project.recording.approve'))
                                        <a href="javascript:void(0)" type="button" class="btn btn-outline-success waves-effect" data-id="1" data-toggle="modal" data-target="#bulk-approve">Approve</a>
                                        @endif
                                        
                                        @if (Auth::user()->role->hasPermissionTo('project.recording.add'))
                                        <a href="{{ route('project.recording.add') }}" type="button" class="btn btn-outline-primary waves-effect">Tambah Baru</a>
                                        @endif
                                    </div>
                                </div>
                                @php
                                    $arrRows = [10,20,50,100];
                                    $statusRecording = App\Constants::RECORDING_STATUS;
                                @endphp
                                {{-- @dd($data->toArray()) --}}
                                <div class="card-body">
                                    <div class="card-datatable">
                                        <div class="row mb-1">
                                            <div class="col-12">
                                                <form action="{{ route('project.recording.index') }}">
                                                    <div class="row d-flex align-items-end">
                                                        <div class="col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label for="stock_type">Unit Bisnis</label>
                                                                <select name="project[kandang][company_id]" id="company_id" class="form-control" >
                                                                    @if (request()->has('project') && isset(request()->get('project')['kandang']['company_id']))
                                                                    @php
                                                                        $companyId = request()->get('project')['kandang']['company_id'];
                                                                    @endphp
                                                                    <option value="{{ $companyId }}" selected> {{ \App\Models\DataMaster\Company::find($companyId)->name }}</option>
                                                                    @endif
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label for="stock_type">Area</label>
                                                                <select name="project[kandang][location][area_id]" id="area_id" class="form-control" >
                                                                    @if (request()->has('project') && isset(request()->get('project')['kandang']['location']['area_id']))
                                                                    @php
                                                                        $areaId = request()->get('project')['kandang']['location']['area_id']
                                                                    @endphp
                                                                    <option value="{{ $areaId }}" selected> {{ \App\Models\DataMaster\Area::find($areaId)->name }}</option>
                                                                    @else
                                                                    <option selected disabled>Pilih unit bisnis terlebih dahulu</option>
                                                                    @endif
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label for="stock_type">Lokasi</label>
                                                                <select name="project[kandang][location_id]" id="location_id" class="form-control" >
                                                                    @if (request()->has('project') && isset(request()->get('project')['kandang']['location_id']))
                                                                    @php
                                                                        $locationiId = request()->get('project')['kandang']['location_id']
                                                                    @endphp
                                                                    <option value="{{ $locationiId }}" selected> {{ \App\Models\DataMaster\Location::find($locationiId)->name }}</option>
                                                                    @else
                                                                    <option selected disabled>Pilih area terlebih dahulu</option>
                                                                    @endif
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label for="stock_type">Project</label>
                                                                <select name="project_id" id="project_id" class="form-control" >
                                                                    @if (request()->get('project_id'))
                                                                    @php
                                                                        $projectId = request()->get('project_id')
                                                                    @endphp
                                                                    <option value="{{ $projectId }}" selected> {{ \App\Models\Project\Project::with('kandang')->find($projectId)->kandang->name }}</option>
                                                                    @else
                                                                    <option selected disabled>Pilih lokasi terlebih dahulu</option>
                                                                    @endif
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label for="stock_type">Periode</label>
                                                                <input type="number" name="project[period]" class="form-control" placeholder="Periode" value="{{ isset(request()->get('project')['period'])?request()->get('project')['period']:'' }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label for="stock_type">Status Recording</label>
                                                                <select name="status" class="form-control" >
                                                                    <option value="0" {{ !request()->has('status')||request()->get('status')==0?'selected':'' }}>Semua</option>
                                                                    @foreach ($statusRecording as $key => $item)
                                                                        <option value="{{$key}}" {{ request()->has('status')&&request()->get('status')==$key?'selected':'' }}>{{ $item }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label for="stock_type">Ketepatan Waktu</label>
                                                                <select name="on_time" class="form-control" >
                                                                    <option value="-1" {{ !request()->has('on_time')||request()->get('on_time')==99?'selected':'' }}>Semua</option>
                                                                    <option value="1" {{ request()->has('on_time')&&request()->get('on_time')==1?'selected':'' }}>Tepat Waktu</option>
                                                                    <option value="0" {{ request()->has('on_time')&&request()->get('on_time')==0?'selected':'' }}>Terlambat</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-1 col-12">
                                                            <div class="form-group">
                                                                <label for="stock_type">Baris</label>
                                                                <select name="rows" class="form-control" >
                                                                    @for ($i = 0; $i < count($arrRows); $i++)
                                                                    <option value="{{ $arrRows[$i] }}" {{ request()->has('rows')&&request()->get('rows')==$arrRows[$i]?'selected':'' }}>{{ $arrRows[$i] }}</option>
                                                                    @endfor
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2 col-12">
                                                            <div class="form-group float-right">
                                                                <label for="stock_type"></label>
                                                                <button type="submit" class="btn btn-primary">Cari</button>
                                                                <label for="stock_type"></label>
                                                                <a href="{{ route('project.recording.index') }}"  class="btn btn-warning">Reset</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <form method="post" action="{{ route('project.recording.approve', 'test') }}" id="form-approve">
                                            {{csrf_field()}}
                                            <table class="table table-bordered table-striped w-100" style="font-size: 10px;">
                                                <thead>
                                                    <tr>
                                                        @if (Auth::user()->role->hasPermissionTo('project.recording.approve'))
                                                        <th rowspan="2">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input" id="checkAll">
                                                                <label class="custom-control-label" for="checkAll"></label>
                                                            </div>
                                                        </th>
                                                        @endif
                                                        <th rowspan="2">ID</th>
                                                        <th rowspan="2">Nama Project</th>
                                                        <th rowspan="2">Periode</th>
                                                        <th rowspan="2">Umur<br>(hari)</th>
                                                        <th rowspan="2">Waktu Recording</th>
                                                        <th colspan="2" class="text-center">FCR</th>
                                                        <th colspan="4" class="text-center">Deplesi</th>
                                                        <th rowspan="2">Status Recording</th>
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
                                                            @if (Auth::user()->role->hasPermissionTo('project.recording.approve'))
                                                            <td>
                                                                <div class="custom-control custom-checkbox">
                                                                    <input type="checkbox" class="custom-control-input {{ $item->status === 1 ? 'select-row':''  }}" name="recording_ids[]" id="project-id-{{ $item->recording_id }}" value="{{ $item->recording_id }}" {{ $item->status === 1 ? '':'disabled'  }}>
                                                                    <label class="custom-control-label" for="project-id-{{ $item->recording_id }}"></label>
                                                                </div>
                                                            </td>
                                                            @endif
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
                                                                @if ($item->status === 2)
                                                                <div class="badge badge-glow badge-success">Disetujui</div>
                                                                @elseif ($item->status === 1)
                                                                <div class="badge badge-glow badge-warning">Menunggu Persetujuan</div>
                                                                @elseif ($item->status === 3)
                                                                <div class="badge badge-glow badge-danger">Ditolak</div>
                                                                @endif
                                                            </td>
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
                                            </form>
                                        </div>
                                    </div>
                                    <div class="mt-1">
                                        <span>Total : {{ number_format($data->total(), 0, ',', '.') }} data</span>
                                        <div class="float-right">
                                            {{ $data->links('vendor.pagination.bootstrap-4') }}
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

                    <div class="modal fade text-left" id="bulk-approve" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="myModalLabel1">Konfirmasi Approve Recording</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <p>Apakah kamu yakin ingin menyetujui recording ini ?</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" id="btn-bulk-approve" class="btn btn-danger">Ya</button>
                                    <button type="button" class="btn btn-primary" data-dismiss="modal">Tidak</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>

                    <script>
                        $(function () {
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

                            $('#company_id').select2({
                                placeholder: "Pilih Unit Bisnis",
                                allowClear: true,
                                ajax: {
                                    url: '{{ route("data-master.company.search") }}', 
                                    dataType: 'json',
                                    delay: 250, 
                                    data: function(params) {
                                        return {
                                            q: params.term 
                                        };
                                    },
                                    processResults: function(data) {
                                        return {
                                            results: data
                                        };
                                    },
                                    cache: true
                                }
                            });

                            $('#company_id').change(function (e) { 
                                if ($('#area_id').val()) {
                                    $('#area_id').trigger('change');
                                } else {
                                    $('#area_id').val(null).trigger('change');
                                }
                                const companyId = $(this).val();
                                $('#area_id').select2({
                                    placeholder: "Pilih Area",
                                    allowClear: true,
                                    ajax: {
                                        url: `{{ route("data-master.area.search") }}`, 
                                        dataType: 'json',
                                        delay: 250, 
                                        data: function(params) {
                                            return {
                                                q: params.term 
                                            };
                                        },
                                        processResults: function(data) {
                                            return {
                                                results: data
                                            };
                                        },
                                        cache: true
                                    }
                                });
                                $('#area_id').change(function (e) { 
                                    if ($('#location_id').val()) {
                                        $('#location_id').trigger('change');
                                    } else {
                                        $('#location_id').val(null).trigger('change');
                                    }
                                    const areaId = $(this).val();
                                    $('#location_id').select2({
                                        placeholder: "Pilih Lokasi",
                                        allowClear: true,
                                        ajax: {
                                            url: `{{ route("data-master.location.search") }}?area_id=${areaId}`, 
                                            dataType: 'json',
                                            delay: 250, 
                                            data: function(params) {
                                                return {
                                                    q: params.term 
                                                };
                                            },
                                            processResults: function(data) {
                                                return {
                                                    results: data
                                                };
                                            },
                                            cache: true
                                        }
                                    });
                                    $('#location_id').change(function (e) { 
                                        if ($('#project_id').val()) {
                                            $('#project_id').trigger('change');
                                        } else {
                                            $('#project_id').val(null).trigger('change');
                                        }
                                        const locationId = `&location_id=${$(this).val()}`;
                                        $('#project_id').select2({
                                            allowClear: true,
                                            placeholder: "Pilih Project",
                                            ajax: {
                                                url: `{{ route("project.list.search") }}?project_status_not=1&project_status_not=3&chickin_status=3${locationId}`, 
                                                dataType: 'json',
                                                delay: 250, 
                                                data: function(params) {
                                                    return {
                                                        q: params.term 
                                                    };
                                                },
                                                processResults: function(data) {
                                                    return {
                                                        results: data
                                                    };
                                                },
                                                cache: true
                                            }
                                        });
                                    });
                                });
                            });

                            $('select').trigger('change');
                        });
                    </script>

@endsection