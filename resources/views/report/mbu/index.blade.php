@extends('templates.main')
@section('title', $title)
@section('content')

@php
$data = collect([
    [
        'location' => 'Singaparna',
        'period' => 4,
        'closed_at' => '2024-12-11',
        'farm_type' => 'Own Farm',
        'active_farms_count' => 7,
        'project_status' => 4,
    ],
    [
        'location' => 'Pandeglang',
        'period' => 3,
        'closed_at' => '2023-10-08',
        'farm_type' => 'Own Farm',
        'active_farms_count' => 4,
        'project_status' => 4,
    ],
]);
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
                        <label for="location" class="form-label">Lokasi<i class="text-danger">*</i></label>
                        <select name="location" id="location" class="form-control"></select>
                    </div>
                    <div class="col-md-2 mt-1">
                        <label for="kandang" class="form-label">Kandang<i class="text-danger">*</i></label>
                        <select name="kandang" id="kandang" class="form-control"></select>
                    </div>
                    <div class="col-md-2 mt-1">
                        <label for="status_project" class="form-label">Status Project<i class="text-danger">*</i></label>
                        <select name="status_project" id="status_project" class="form-control"></select>
                    </div>
                </div>

                {{-- table --}}
                <div class="card-datatable mt-2">
                    <div class="table-responsive mb-2">
                        <table id="datatable" class="table table-bordered table-striped w-100">
                            <thead class="text-center">
                                <th>#</th>
                                <th>Lokasi</th>
                                <th>Periode</th>
                                <th>Tanggal Closing</th>
                                <th>Jenis Project</th>
                                <th>Kandang AKtif</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </thead>
                            <tbody class="text-center">
                                @foreach ($data as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item['location'] }}</td>
                                    <td>{{ $item['period'] }}</td>
                                    <td>{{ date('d-M-Y', strtotime($item['closed_at'])) }}</td>
                                    <td>{{ $item['farm_type'] }}</td>
                                    <td>{{ $item['active_farms_count'] }} Kandang</td>
                                    <td>
                                        @switch($item['project_status'])
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
                                                <a class="dropdown-item" href="{{ route('report.mbu.detail')}}">
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
    const $kandangSelect = $('#kandang');
    const $statusProjectSelect = $('#status_project');
    const locationIdRoute = '{{ route("data-master.location.search") }}';
    const kandangIdRoute = '{{ route("data-master.kandang.search") }}';
    initSelect2($locationSelect, 'Pilih Lokasi', locationIdRoute, '');
    initSelect2($kandangSelect, 'Pilih Kandang', kandangIdRoute, '');
    initSelect2($statusProjectSelect, 'Pilih Status');

    $(function () {
        $('#datatable').DataTable({
            dom: '<"d-flex justify-content-between"B><"custom-table-wrapper"t>ip',
        });
    });
</script>

@endsection
