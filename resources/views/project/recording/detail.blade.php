@extends('templates.main')
@section('title', $title)
@section('content')
<style>
    table#bw-repeater tfoot tr td {
        padding: 0px 10px 0px 10px !important;
    }
</style>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">{{$title}}</h4>
                                </div>
                                <div class="card-body">
                                    {{ csrf_field() }}
                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-md-6 mt-1">
                                                <div class="row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="company_id" class="float-right">Unit Bisnis</label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <input type="text" class="form-control" readonly value="{{ $data->project->kandang->company->name??'' }}">
                                                    </div>
                                                </div>
                                                <div class="row mt-1">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="project_id" class="float-right">Project</label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <input type="text" class="form-control" readonly value="{{ $data->project->kandang->name??'' }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mt-1">
                                                <div class="row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="product_category_id" class="float-right">Kategori Produk</label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <input type="text" class="form-control" readonly value="{{ $data->project->product_category->name??'' }}">
                                                    </div>
                                                </div>
                                                <div class="row mt-1">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="warehouse_id" class="float-right">Gudang</label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        @foreach ($data->project->kandang->warehouse??[] as $item)
                                                            @if ($item->type === 2)
                                                            <input type="text" class="form-control" readonly value="{{ $item->name }}">
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mt-1">
                                                <div class="row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="record_datetime" class="float-right">Tanggal Record</label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <input type="text" class="form-control flatpickr-basic" value="{{ date('d-M-Y H:i', strtotime($data->record_datetime))}}" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 mt-3">
                                        <li class="mb-2">
                                            <span style="font-size: 15px;"><b>Persediaan</b></span>
                                            <div class="table-responsive">
                                                <table class="table table-bordered w-100 no-wrap text-center" id="stock-repeater">
                                                    <thead>
                                                        <th>Persediaan</th>
                                                        <th>Jumlah Stock Digunakan</th>
                                                        <th>Satuan</th>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($data->recording_stock as $item)
                                                            <tr>
                                                                <td>{{ $item->product_warehouse->product->name??'' }}</td>
                                                                <td>{{ number_format($item->decrease, 0, ',', '.') }}</td>
                                                                <td>{{ $item->product_warehouse->product->uom->name??'' }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <hr>
                                        </li>
                                        <li class="mb-2">
                                            <span style="font-size: 15px;"><b>Non Persediaan</b></span>
                                            <div class="table-responsive">
                                                <table class="table table-bordered w-100 no-wrap text-center" id="nonstock-repeater">
                                                    <thead>
                                                        <th>Jenis Recording</th>
                                                        <th>Jumlah</th>
                                                        <th>Satuan</th>
                                                    </thead>
                                                    <tbody >
                                                       @foreach ($data->recording_nonstock as $item)
                                                            <tr>
                                                                <td>{{ $item->nonstock->name??'' }}</td>
                                                                <td>{{ number_format($item->value, 0, ',', '.') }}</td>
                                                                <td>{{ $item->nonstock->uom->name??'' }}</td>
                                                            </tr>
                                                       @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <hr>
                                        </li>
                                        <li class="mb-2">
                                            <span style="font-size: 15px;"><b>Body Weight</b></span>
                                            <div class="table-responsive">
                                                <table class="table table-bordered w-100 no-wrap text-center" id="bw-repeater">
                                                    <thead>
                                                        <th>Berat (Kg)</th>
                                                        <th>Jumlah</th>
                                                        <th>Total Rataan</th>
                                                        <th>Satuan</th>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($data->recording_bw??[] as $item)
                                                            @foreach ($item->recordingBwList??[] as $val)
                                                            <tr>
                                                                <td>{{ $val->weight }}</td>
                                                                <td>{{ $val->total }}</td>
                                                                <td>{{ $val->weight_calc }}</td>
                                                                <td>Ekor</td>
                                                            </tr>
                                                            @endforeach
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot>
                                                        @foreach ($data->recording_bw as $item)
                                                        <tr>
                                                            <td colspan="3" class="text-right">Rata-rata Berat</td>
                                                            <td>{{ $item->avg_weight }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3" class="text-right">Jumlah Ekor</td>
                                                            <td>{{ $item->total_chick }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3" class="text-right">Jumlah Berat</td>
                                                            <td>{{ $item->total_calc }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3" class="text-right">Bobot Rataan</td>
                                                            <td>{{ $item->value }}</td>
                                                        </tr>
                                                        @endforeach
                                                    </tfoot>
                                                </table>
                                            </div>
                                            <hr>
                                        </li>
                                        <li class="mb-2">
                                            <span style="font-size: 15px;"><b>Deplesi</b></span>
                                            <div class="table-responsive">
                                                <table class="table table-bordered w-100 no-wrap text-center" id="depletion">
                                                    <thead>
                                                        <th>Kondisi</th>
                                                        <th>Jumlah</th>
                                                        <th>Satuan</th>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($data->recording_depletion as $item)
                                                            <tr>
                                                                <td>{{ $item->product_warehouse->product->name??'' }}</td>
                                                                <td>{{ number_format($item->total,0,',','.' ) }}</td>
                                                                <td>Ekor</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </li>
                                        <li class="mb-2">
                                            <span style="font-size: 15px;"><b>Telur</b></span>
                                            <div class="table-responsive">
                                                <table class="table table-bordered w-100 no-wrap text-center" id="egg">
                                                    <thead>
                                                        <th>Kondisi</th>
                                                        <th>Jumlah</th>
                                                        <th>Satuan</th>
                                                    </thead>
                                                    <tbody data-repeater-list="eggs">
                                                        @foreach ($data->recording_egg as $item)
                                                            <tr>
                                                                <td>{{ $item->product_warehouse->product->name??'' }}</td>
                                                                <td>{{ number_format($item->total,0,',','.' ) }}</td>
                                                                <td>Kg</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </li>
                                    </div>
                                    <div class="col-12 mt-4">
                                        <div class="text-right">
                                            <a href="{{ route('project.recording.index') }}" class="btn btn-outline-warning waves-effect">Kembali</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script>
                        $(document).ready(function() {
                        });
                    </script>
@endsection