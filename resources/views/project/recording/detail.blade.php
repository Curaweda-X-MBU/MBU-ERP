@extends('templates.main')
@section('title', $title)
@section('content')
<style>
    table#bw-repeater tfoot tr td {
        padding: 0px 10px 0px 10px !important;
    }
</style>

@php
    $fcrStandar = collect($data->project->fcr->fcr_standard??[])->where('day', $data->day)->first();
    if (!function_exists('formatnumber')) {
        function formatnumber($number) {
            return rtrim(rtrim(number_format(floatval($number), 2, ',', '.'), '0'), ',');
        }
    }

    // $pakanRecord = collect($data->recording_stock)->filter(function ($recordingStock) {
    //             return optional($recordingStock->product_warehouse)
    //                 ->product
    //                 ->name === 'Pakan';
    //         })->first();

    $pakanRecord = collect($data->recording_stock)->filter(function($recordingStock) {
            return Str::contains(strtolower($recordingStock->product_warehouse?->product?->product_sub_category?->name ?? ''), 'pakan');
        })->first();
    // dd($pakaRecord);

@endphp

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">{{$title}}</h4>
                                        <div class="text-right">
                                            <a href="{{ route('project.recording.index') }}" class="btn btn-outline-warning waves-effect">Kembali</a>
                                            @if (Auth::user()->role->hasPermissionTo('project.recording.approve'))
                                                @if ($data->status === 1)
                                                <a class="btn btn-outline-success" href="javascript:void(0);" data-id="{{ $data->recording_id }}#approve" data-toggle="modal" data-target="#approve">
                                                    Approve
                                                </a>
                                                <a class="btn btn-outline-danger" href="javascript:void(0);" data-id="{{ $data->recording_id }}#reject" data-toggle="modal" data-target="#approve">
                                                    Reject
                                                </a>
                                                @endif
                                            @endif
                                        </div>
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
                                                <div class="row mt-1">
                                                    <div class="col-sm-3">
                                                        <label for="standard_mortality" class="float-right">Standar Mortalitas</label>
                                                    </div>
                                                    <div class="col-sm-9" id="show-fcr">
                                                        <h4>{{ $data->project->standard_mortality }} %</h4>
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
                                                        <label for="day" class="float-right">Umur (Hari)</label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <input type="text" class="form-control" value="{{ $data->day }}" readonly>
                                                    </div>
                                                </div>
                                                <div class="row mt-1">
                                                    <div class="col-sm-3 ">
                                                        <label for="day" class="float-right">Status Recording</label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        @if ($data->status === 2)
                                                        <div class="badge badge-glow badge-success">Disetujui</div>
                                                        @elseif ($data->status === 1)
                                                        <div class="badge badge-glow badge-warning">Menunggu Persetujuan</div>
                                                        @elseif ($data->status === 3)
                                                        <div class="badge badge-glow badge-danger">Ditolak</div>
                                                        @endif
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
                                        <div class="row">
                                            <div class="col-md-5">
                                                <li>
                                                    <span style="font-size: 15px;"><b>FCR</b></span>
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered w-100 no-wrap">
                                                            <thead>
                                                                <th></th>
                                                                <th class="text-center">Aktual</th>
                                                                <th class="text-center">Standar</th>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td>Bobot</td>
                                                                    <td class="text-center">{{ formatnumber($data->recording_bw[0]->value) }}</td>
                                                                    <td class="text-center">{{ formatnumber($fcrStandar?->weight)??'' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Peningkatan Harian</td>
                                                                    <td class="text-center">{{ formatnumber($data->daily_gain) }}</td>
                                                                    <td class="text-center">{{ formatnumber($fcrStandar?->daily_gain)??'' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Peningkatan Rata - rata</td>
                                                                    <td class="text-center">{{ formatnumber($data->avg_daily_gain) }}</td>
                                                                    <td class="text-center">{{ formatnumber($fcrStandar?->avg_daily_gain)??'' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Asupan Harian</td>
                                                                    <td class="text-center">{{ formatnumber(($pakanRecord->decrease??0*1000/($data->total_chick ?: 1))) ?? '' }}</td>
                                                                    <td class="text-center">{{ formatnumber($fcrStandar?->daily_intake)??'' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Asupan Kumulatif</td>
                                                                    <td class="text-center">{{ formatnumber($data->cum_intake) }}</td>
                                                                    <td class="text-center">{{ formatnumber($fcrStandar?->cum_intake)??'' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>FCR</td>
                                                                    <td class="text-center">{{ formatnumber($data->fcr_value) }}</td>
                                                                    <td class="text-center">{{ formatnumber($fcrStandar?->fcr)??'' }}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </li>
                                            </div>
                                            <div class="col-md-7">
                                                <li>
                                                    <span style="font-size: 15px;"><b>Mortalitas</b></span>
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered w-100 no-wrap text-center">
                                                            <thead>
                                                                <tr>
                                                                    <th rowspan="2">Jumlah Ayam</th>
                                                                    <th colspan="2">Deplesi Harian</th>
                                                                    <th colspan="2">Deplesi Kumulatif</th>
                                                                </tr>
                                                                <tr>
                                                                    <th>Total</th>
                                                                    <th>(%)</th>
                                                                    <th>Total</th>
                                                                    <th>(%)</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td>{{ formatnumber($data->total_chick) }}</td>
                                                                    <td>{{ formatnumber($data->total_depletion) }}</td>
                                                                    <td>{{ formatnumber($data->daily_depletion_rate) }}</td>
                                                                    <td>{{ formatnumber($data->cum_depletion) }}</td>
                                                                    <td>{{ formatnumber($data->cum_depletion_rate) }}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </li>
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
                                                                <td>{{ formatnumber($val->weight) }}</td>
                                                                <td>{{ formatnumber($val->total) }}</td>
                                                                <td>{{ formatnumber($val->weight_calc) }}</td>
                                                                <td>Ekor</td>
                                                            </tr>
                                                            @endforeach
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot>
                                                        @foreach ($data->recording_bw as $item)
                                                        <tr>
                                                            <td colspan="3" class="text-right">Rata-rata Berat</td>
                                                            <td>{{ formatnumber($item->avg_weight) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3" class="text-right">Jumlah Ekor</td>
                                                            <td>{{ formatnumber($item->total_chick) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3" class="text-right">Jumlah Berat</td>
                                                            <td>{{ formatnumber($item->total_calc) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3" class="text-right">Bobot Rataan</td>
                                                            <td>{{ formatnumber($item->value) }}</td>
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

                    <div class="modal fade text-left" id="approve" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable" role="document">
                            <div class="modal-content">
                                <form method="post" action="{{ route('project.recording.approve', 'test') }}">
                                {{csrf_field()}}
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="myModalLabel1">Konfirmasi <span class="action"></span> Recording</h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="recording_ids[]" id="id" value="">
                                        <input type="hidden" name="act" id="act" value="">
                                        <p>Apakah kamu yakin ingin <span class="action"></span> data recording ini ?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-danger">Ya</button>
                                        <button type="button" class="btn btn-primary" data-dismiss="modal">Tidak</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <script>
                        $(document).ready(function() {
                            $('#approve').on('show.bs.modal', function (event) {
                                var button = $(event.relatedTarget)
                                var strId = button.data('id');
                                var id = strId.split('#')[0];
                                var action = strId.split('#')[1];
                                var modal = $(this)
                                modal.find('#id').val(id);
                                if (action === 'approve') {
                                    modal.find('.action').html('Approve');
                                } else  {
                                modal.find('#act').val(action);
                                    modal.find('.action').html('Reject');
                                }
                            });

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
