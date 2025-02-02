@extends('templates.main')
@section('title', $title)
@section('content')
@php
    $farmType = App\Constants::KANDANG_TYPE;
    dump($data)
@endphp

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{$title}}</h4>
            </div>
            <div class="card-body">
                {{-- informasi --}}
                <div class="row row-cols-2 row-cols-md-4 align-items-baseline">
                    <div class="col-md-2 mt-1">
                        <label for="location" class="form-label">Lokasi</label>
                        <select name="location" id="location" class="form-control"></select>
                    </div>
                    <div class="col-md-2 mt-1">
                        <label for="status_project" class="form-label">Status Project</label>
                        <select name="status_project" id="status_project" class="form-control">
                            <option value="" selected>Semua</option>
                            @foreach (App\Constants::PROJECT_STATUS as $index => $status)
                            <option value="{{ $index }}">{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- table --}}
                <div class="card-datatable mt-2">
                    <div class="table-responsive mb-2">
                        <table id="datatable" class="table table-bordered table-striped w-100">
                            <thead class="text-center">
                                <th>#</th>
                                <th class="d-none">location_id</th>
                                <th>Lokasi</th>
                                <th>Periode</th>
                                <th>Tanggal Closing</th>
                                <th>Jenis Project</th>
                                <th>Kandang</th>
                                <th class="d-none">project_status</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </thead>
                            <tbody class="text-center">
                                @foreach ($data as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="d-none">{{ $item->location_id }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->period }}</td>
                                    <td>-</td>
                                    <td>{{ $farmType[$item->farm_type] }}</td>
                                    <td>{{ $item->count_kandang }} Kandang</td>
                                    <td class="d-none">{{ $item->project_status }}</td>
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
                                        <div class="dropdown dropleft" style="position: static;">
                                            <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-toggle="dropdown">
                                                <i data-feather="more-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="{{ route('report.detail.location', $item->location_id).'?company=mbu'}}">
                                                    <i data-feather='eye' class="mr-50"></i>
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

<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js')}}"></script>
<script>
    // select2
    const $locationSelect = $('#location');
    const $statusProjectSelect = $('#status_project');
    const locationIdRoute = '{{ route("data-master.location.search") }}';
    initSelect2($locationSelect, 'Pilih Lokasi', locationIdRoute, '', { allowClear: true });
    initSelect2($statusProjectSelect, 'Pilih Status', null, '', { allowClear: true });

    $(function () {
        const $table = $('#datatable').DataTable({
            dom: '<"d-flex justify-content-between"B><"custom-table-wrapper"t>ip',
        });

        // Location ID
        $table.columns(1).visible(false);
        $locationSelect.on('change', function() {
            $table.columns(1).search('').draw();
            $table.columns(1).search($(this).val() ?? '').draw();
        });

        // Project Status Enum
        $table.columns(7).visible(false);
        $statusProjectSelect.on('change', function() {
            $table.columns(7).search('').draw();
            $table.columns(7).search($(this).val() ?? '').draw();
        });
    });
</script>

@endsection
