@extends('templates.main')
@section('title', $title)
@section('content')
@php
    use Carbon\Carbon;
    use Illuminate\Support\Collection;
@endphp
<style>
    .select2 {
        width: 17rem !important;
    }
    #tbl-summary th{
        padding-left: 10px;
        padding-right: 10px;
    }
</style>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ $title }}</h4>
            </div>
            <div class="card-body">
                <form method="post" action="{{ request()->fullUrl() }}">
                {{ csrf_field() }}
                <div class="col-12">
                    <div class="row" style="column-gap: 10px;">
                        <select class="form-control" name="area_id" id="area_id">
                            @php
                                $arrParamDownload = [
                                    'month' => $month, 
                                    'year' => $year
                                ];
                            @endphp
                            @if (request()->has('area_id'))
                                @php
                                    $areaId = request()->get('area_id');
                                    $areaDt = App\Models\DataMaster\Area::find($areaId);
                                    $arrParamDownload['area_id'] = $areaId;
                                @endphp
                                @if ($areaDt)
                                    <option value="{{ $areaId }}" selected>{{ $areaDt->name }}</option>
                                @else
                                    <option value="0" selected>Semua Area</option>
                                @endif
                            @endif
                        </select>
                        <div class="input-group-append" id="button-addon2">
                            <button type="submit" class="btn btn-outline-primary waves-effect" type="button"><i data-feather='search'></i></button>
                        </div>
                        <div class="col text-right pr-0">
                            <a href="{{ route('ph.performance.download', $arrParamDownload) }}" type="button" class="btn btn-outline-success waves-effect" target="_blank">Download</a>
                            <a href="{{ route('ph.performance.add') }}" type="button" class="btn btn-outline-primary waves-effect">Tambah Baru</a>
                            <a href="{{ route('ph.performance.index') }}" type="button" class="btn btn-outline-warning waves-effect">Kembali</a>
                        </div>
                    </div>
                </div>
                </form>
                <div class="card-datatable">
                    <div class="table-responsive mb-2">
                        <table id="datatable" class="table table-bordered table-striped w-100">
                            <thead>
                                <th>Area</th>
                                <th>Nama Farm</th>
                                <th>Tgl. Chick In</th>
                                <th>Populasi</th>
                                <th>DOC</th>
                                <th>Hatchery</th>
                                <th>Mati</th>
                                <th>Culling</th>
                                <th>Deplesi</th>
                                <th>Deplesi (%)</th>
                                <th>BW (gram)</th>
                                <th>Aksi</th>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
                                    <tr>
                                        <td>{{ $item->kandang->location->area->name }}</td>
                                        <td>{{ $item->kandang->name }}</td>
                                        <td>{{ date('d-m-Y', strtotime($item->chick_in_date)) }}</td>
                                        <td>{{ number_format($item->population, 0, ',', '.') }}</td>
                                        <td>{{ $item->supplier->name }}</td>
                                        <td>{{ $item->hatchery }}</td>
                                        <td>{{ number_format($item->death, 0, ',', '.') }}</td>
                                        <td>{{ number_format($item->culling, 0, ',', '.') }}</td>
                                        <td>{{ number_format($item->total_depletion, 0, ',', '.') }}</td>
                                        <td>{{ $item->percentage_depletion }} %</td>
                                        <td>{{ number_format($item->bw, 0, ',', '.') }}</td>
                                        <td>
                                            <div class="dropdown dropleft">
                                                <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-toggle="dropdown">
                                                    <i data-feather="more-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" href="{{ route('ph.performance.edit', $item->ph_performance_id) }}">
                                                        <i data-feather="edit-2" class="mr-50"></i>
                                                        <span>Edit</span>
                                                    </a>
                                                    <a class="dropdown-item text-danger" href="javascript:void(0);" data-id="{{ $item->ph_performance_id }}" data-toggle="modal" data-target="#delete">
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
                <div class="col-12">
                    @php
                        $grouped = collect($data)->groupBy(function ($item) {
                            $vendorName = $item['supplier']['alias']??$item['supplier']['name'];
                            return $vendorName. ' - ' . $item['hatchery'];
                        });

                        $summary = $grouped->map(function ($items, $key) {
                            $arrKey = explode(' - ', $key);
                            return [
                                'supplier_name' => $arrKey[0],
                                'hatchery' => $arrKey[1]??'',
                                'percentage_depletion' => $items->avg('percentage_depletion'),
                            ];
                        })->sortByDesc('percentage_depletion');
                        $arrSummary = [];
                    @endphp
                    <div class="text-center">
                        <h2>
                            POULTRY HEALTH DIVISION<br>
                            FIRST WEEK MORTALITY {{strtoupper(Carbon::parse($year.'-'.$month.'-01')->format('F'))}} {{$year}} <br>
                            {{ request()->has('area_id')&&request()->get('area_id')>0?'AREA '.strtoupper($areaDt->name):'' }}
                        </h2>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-8">
                            <div id="chart-container" style="overflow-x: auto;" >
                                <canvas id="myChart" style="width: 700px; !important;"></canvas>
                            </div>
                        </div>
                        <div class="col-md-4" style="margin-top: 7px;">
                            <div class="row">
                                <table class="table table-bordered" id="tbl-summary">
                                    <thead>
                                        <th>Asal Doc</th>
                                        <th>Hatchery</th>
                                        <th>Depletion (%)</th>
                                    </thead>
                                    <tbody>
                                        @foreach ($summary as $key => $item)
                                        @php
                                            $supplierName = $summary[$key]['alias']??$summary[$key]['supplier_name'];
                                            $hatcheryName = $summary[$key]['hatchery'];
                                            $percentDepletion = round($summary[$key]['percentage_depletion'], 2);
                                            $arrSummary[] = [
                                                'supplier_name' => $supplierName,
                                                'hatchery' => $hatcheryName,
                                                'percentage_depletion' => $percentDepletion
                                            ];
                                        @endphp
                                        <tr>
                                            <td>{{ $supplierName }}</td>
                                            <td>{{ $hatcheryName }}</td>
                                            <td>{{ $percentDepletion }} %</td>
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
                    <input type="hidden" name="month" value="{{ $month }}">
                    <input type="hidden" name="year" value="{{ $year }}">
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

@include('ph.performance.chartjs')

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
        $('#delete').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) 
            var id = button.data('id')
            var modal = $(this)
            modal.find('.modal-body #id').val(id)
        });

        $('#area_id').select2({
            placeholder: "Pilih Area",
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
                    data.unshift({
                        id: 0,
                        text: "Semua Area"
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