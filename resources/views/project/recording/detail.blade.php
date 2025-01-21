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
                                                        <label for="location_id" class="float-right">Lokasi Farm</label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <input type="text" class="form-control" readonly value="{{ $data->project->kandang->location->name??'' }}">
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
                                                <div class="row mt-1">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="project_id" class="float-right">Standar Fcr</label>
                                                    </div>
                                                    <div class="col-sm-9" id="show-fcr">
                                                        <button type="button" class="btn btn-sm btn-outline-primary" data-id="{{ $data->project->fcr_id }}" data-toggle="modal" data-target="#showFcr">
                                                            {{ $data->project->fcr->name ?? 'N/A' }}
                                                        </button>
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
                                                <div class="row mt-1">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="record_datetime" class="float-right">Tanggal Record</label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <input type="text" class="form-control flatpickr-basic" value="{{ date('d-M-Y H:i', strtotime($data->record_datetime))}}" readonly>
                                                    </div>
                                                </div>
                                                <div class="row mt-1">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="day" class="float-right">Umur<br>(Hari)</label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <input type="text" class="form-control" value="{{ $data->day }}" readonly>
                                                    </div>
                                                </div>
                                                @if ($data->document_revision)
                                                <div class="row">
                                                    <div class="col-md-12 mt-1">
                                                        <div class="text-right">
                                                            Dokumen perubahan data recording<br>
                                                            <a class="btn btn-success btn-sm" href="{{ route('file.show', ['filename' => $data->document_revision]) }}" target="_blank">Download</a>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
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
                                                        <th>Berat (Gram)</th>
                                                        <th>Jumlah</th>
                                                        <th>Total Rataan</th>
                                                        <th>Satuan</th>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($data->recording_bw??[] as $item)
                                                            @foreach ($item->recordingBwList??[] as $val)
                                                            <tr>
                                                                <td>{{ number_format($val->weight,2,',','.') }}</td>
                                                                <td>{{ number_format($val->total,0,',','.') }}</td>
                                                                <td>{{ number_format($val->weight_calc,2,',','.') }}</td>
                                                                <td>Ekor</td>
                                                            </tr>
                                                            @endforeach
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot>
                                                        @foreach ($data->recording_bw as $item)
                                                        <tr>
                                                            <td colspan="3" class="text-right">Rata-rata Berat</td>
                                                            <td>{{ number_format($item->avg_weight, 2, ',', '.') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3" class="text-right">Jumlah Ekor</td>
                                                            <td>{{ number_format($item->total_chick, 0, ',', '.') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3" class="text-right">Jumlah Berat</td>
                                                            <td>{{ number_format($item->total_calc, 2, ',', '.') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3" class="text-right">Bobot Rataan</td>
                                                            <td>{{ number_format($item->value, 2, ',', '.') }}</td>
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

                    <div class="modal fade text-left" id="showFcr" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="myModalLabel1">Detail Standar FCR <span id="fcr-name"></span></h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="table-responsive">
                                                <table class="table table-bordered w-100 no-wrap text-center">
                                                    <thead>
                                                        <tr>
                                                            <th rowspan="2">Umur<br>(Hari)</th>
                                                            <th rowspan="2">Bobot</th>
                                                            <th colspan="2">Peningkatan</th>
                                                            <th colspan="2">Asupan</th>
                                                            <th rowspan="2">FCR</th>
                                                        </tr>
                                                        <tr>
                                                            <th>Harian</th>
                                                            <th>Rata - rata</th>
                                                            <th>Harian</th>
                                                            <th>Kumulatif</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script>
                        $(document).ready(function() {
                            $('#showFcr').on('show.bs.modal', function (event) {
                                var button = $(event.relatedTarget) 
                                var id = button.data('id')
                                var modal = $(this)

                                $.ajax({
                                    type: "get",
                                    url: `{{ route('data-master.fcr.search-standard') }}?fcr_id=${id}`,
                                    beforeSend: function () {
                                        modal.find('table tbody').html('');
                                    },
                                    success: function (response) {
                                        let html = '';
                                        modal.find('#fcr-name').html(`- ${response.name}`);
                                        const arrFcr = response.fcr_standard;
                                        
                                        arrFcr.forEach(val => {
                                            html += `<tr>
                                                        <td>${formatMoney(val.day)}</td>
                                                        <td>${formatMoney(val.weight)}</td>
                                                        <td>${formatMoney(val.daily_gain)}</td>
                                                        <td>${formatMoney(val.avg_daily_gain)}</td>
                                                        <td>${formatMoney(val.daily_intake)}</td>
                                                        <td>${formatMoney(val.cum_intake)}</td>
                                                        <td>${formatMoney(val.fcr)}</td>
                                                    </tr>`;
                                        });
                                        modal.find('table tbody').html(html);

                                    }
                                });
                            });

                            function formatMoney(amount) {
                                const number = parseFloat(amount);
                                const formatted = number.toFixed(2) 
                                    .replace('.', ',')   
                                    .replace(/\B(?=(\d{3})+(?!\d))/g, '.'); 
                                return formatted.replace(/,00$/, '');
                            }
                        });
                    </script>
@endsection