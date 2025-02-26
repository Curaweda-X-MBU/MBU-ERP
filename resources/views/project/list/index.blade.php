@extends('templates.main')
@section('title', $title)
@section('content')
<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">{{ $title }}</h4>
                                    <div class="float-right">
                                        @if (Auth::user()->role->hasPermissionTo('project.list.approve'))
                                        <a class="btn btn-outline-success waves-effect" href="javascript:void(0);" data-id="1" data-toggle="modal" data-target="#bulk-approve">
                                            Approve
                                        </a>
                                        @endif
                                        <a href="{{ route('project.list.add') }}" type="button" class="btn btn-outline-primary waves-effect">Tambah Baru</a>
                                    </div>
                                </div>
                                @php
                                    $arrRows = [3, 10,20,50,100];
                                    $statusChickIn = App\Constants::PROJECT_CHICKIN_STATUS;
                                    $statusProject = App\Constants::PROJECT_STATUS;
                                @endphp
                                <div class="card-body">
                                    <div class="card-datatable">
                                        <div class="row mb-1">
                                            <div class="col-12">
                                                <form action="{{ route('project.list.index') }}">
                                                    <div class="row d-flex align-items-end">
                                                        <div class="col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label for="stock_type">Unit Bisnis</label>
                                                                <select name="kandang[company_id]" id="company_id" class="form-control" >
                                                                    @if (request()->has('kandang') && isset(request()->get('kandang')['company_id']))
                                                                    @php
                                                                        $companyId = request()->get('kandang')['company_id'];
                                                                    @endphp
                                                                    <option value="{{ $companyId }}" selected> {{ \App\Models\DataMaster\Company::find($companyId)->name }}</option>
                                                                    @endif
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label for="stock_type">Area</label>
                                                                <select name="kandang[location][area_id]" id="area_id" class="form-control" >
                                                                    @if (request()->has('kandang') && isset(request()->get('kandang')['location']['area_id']))
                                                                    @php
                                                                        $areaId = request()->get('kandang')['location']['area_id']
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
                                                                <select name="kandang[location_id]" id="location_id" class="form-control" >
                                                                    @if (request()->has('kandang') && isset(request()->get('kandang')['location_id']))
                                                                    @php
                                                                        $locationiId = request()->get('kandang')['location_id']
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
                                                                <label for="stock_type">Kandang</label>
                                                                <select name="kandang_id" id="kandang_id" class="form-control" >
                                                                    @if (request()->has('kandang') && request()->get('kandang_id'))
                                                                    @php
                                                                        $kandangId = request()->get('kandang_id')
                                                                    @endphp
                                                                    <option value="{{ $kandangId }}" selected> {{ \App\Models\DataMaster\Kandang::find($kandangId)->name }}</option>
                                                                    @else
                                                                    <option selected disabled>Pilih lokasi terlebih dahulu</option>
                                                                    @endif
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label for="stock_type">Periode</label>
                                                                <input type="number" name="period" class="form-control" placeholder="Periode" value="{{ request()->get('period') }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label for="stock_type">Status Chickin</label>
                                                                <select name="chickin_status" class="form-control" >
                                                                    <option value="0" {{ !request()->has('chickin_status')||request()->get('chickin_status')==0?'selected':'' }}>Semua</option>
                                                                    @foreach ($statusChickIn as $key => $item)
                                                                        <option value="{{$key}}" {{ request()->has('chickin_status')&&request()->get('chickin_status')==$key?'selected':'' }}>{{ $item }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label for="stock_type">Status Project</label>
                                                                <select name="project_status" class="form-control" >
                                                                    <option value="0" {{ !request()->has('project_status')||request()->get('project_status')==0?'selected':'' }}>Semua</option>
                                                                    @foreach ($statusProject as $key => $item)
                                                                        <option value="{{$key}}" {{ request()->has('project_status')&&request()->get('project_status')==$key?'selected':'' }}>{{ $item }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2 col-12">
                                                            <div class="form-group">
                                                                <label for="stock_type">Jumlah Baris</label>
                                                                <select name="rows" class="form-control" >
                                                                    @for ($i = 0; $i < count($arrRows); $i++)
                                                                    <option value="{{ $arrRows[$i] }}" {{ request()->has('rows')&&request()->get('rows')==$arrRows[$i]?'selected':'' }}>{{ $arrRows[$i] }}</option>
                                                                    @endfor
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-1 col-12">
                                                            <div class="form-group">
                                                                <label for="stock_type"></label>
                                                                <button type="submit" class="btn btn-primary">Cari</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <form method="post" action="{{ route('project.list.approve', 'test') }}" id="form-approve">
                                            {{csrf_field()}}
                                                <table class="table table-bordered table-striped w-100">
                                                    <thead>
                                                            @if (Auth::user()->role->hasPermissionTo('project.list.approve'))
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
                                                            <th>Status Project</th>
                                                            <th>Aksi</th>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($data as $item)
                                                            <tr>
                                                                @if (Auth::user()->role->hasPermissionTo('project.list.approve'))
                                                                <td>
                                                                    <div class="custom-control custom-checkbox">
                                                                        <input type="checkbox" class="custom-control-input {{ $item->project_status === 1 ? 'select-row':''  }}" name="project_ids[]" id="project-id-{{ $item->project_id }}" value="{{ $item->project_id }}" {{ $item->project_status === 1 ? '':'disabled'  }}>
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
                                                                    @switch($item->project_status)
                                                                        @case(1)
                                                                            <div class="badge badge-pill badge-warning">Pengajuan</div>
                                                                            @break
                                                                        @case(2)
                                                                            <div class="badge badge-pill badge-primary">Aktif</div>
                                                                            @break
                                                                        @case(3)
                                                                            <div class="badge badge-pill badge-info">Persiapan</div>
                                                                            @break
                                                                        @case(4)
                                                                            <div class="badge badge-pill badge-success">Selesai</div>
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
                                                                            <a class="dropdown-item" href="{{ route('project.list.detail', $item->project_id) }}">
                                                                                <i data-feather="info" class="mr-50"></i>
                                                                                <span>Detail</span>
                                                                            </a>
                                                                            @if (Auth::user()->role->hasPermissionTo('project.chick-in.detail'))
                                                                            <a class="dropdown-item" href="{{ route('project.chick-in.detail', $item->project_id) }}">
                                                                                <i data-feather='log-in' class="mr-50"></i>
                                                                                <span>Chick In</span>
                                                                            </a>
                                                                            @endif
                                                                            @if (Auth::user()->role->hasPermissionTo('project.recording.index'))
                                                                            <form action="{{ route('project.recording.index') }}" method="POST">
                                                                                @csrf
                                                                                <input type="hidden" name="project_id" value="{{$item->project_id}}">
                                                                                <input type="hidden" name="period" value="{{$item->period}}">
                                                                                <button class="dropdown-item">
                                                                                    <i data-feather='fast-forward' class="mr-50"></i>
                                                                                    <span>Recording</span>
                                                                                </button>
                                                                            </form>
                                                                            @endif
                                                                            <a class="dropdown-item" href="javascript:void(0)">
                                                                                <i data-feather='check-circle' class="mr-50"></i>
                                                                                <span>Persiapan</span>
                                                                            </a>
                                                                            @if (Auth::user()->role->hasPermissionTo('project.list.delete'))
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

                    <div class="modal fade text-left" id="delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable" role="document">
                            <div class="modal-content">
                                <form method="post" action="{{ route('project.list.delete', 'test') }}">
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
                                    <h4 class="modal-title" id="myModalLabel1">Konfirmasi Approve Project</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <p>Apakah kamu yakin ingin menyetujui project ini ?</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" id="btn-bulk-approve" class="btn btn-danger">Ya</button>
                                    <button type="button" class="btn btn-primary" data-dismiss="modal">Tidak</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script>
                        $(function () {
                            function getRecording() {
                                document.getElementById('getRecording').submit();
                            }

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
                                        if ($('#kandang_id').val()) {
                                            $('#kandang_id').trigger('change');
                                        } else {
                                            $('#kandang_id').val(null).trigger('change');
                                        }
                                        const locationId = $(this).val();
                                        $('#kandang_id').select2({
                                            placeholder: "Pilih Kandang",
                                            allowClear: true,
                                            ajax: {
                                                url: `{{ route("data-master.kandang.search") }}?location_id=${locationId}`, 
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

                            $('select').trigger('change');
                        });
                    </script>

@endsection