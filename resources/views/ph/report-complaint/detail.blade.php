@extends('templates.main')
@section('title', $title)
@section('content')
<style>
    #tbl-chick-in td, #tbl-mortality td {
        text-align: center;
        padding: 5px !important;
    }

    #tbl-chick-in th, #tbl-mortality th {
        vertical-align: middle;
        text-align: center;
        padding: 5px;
    }

    .tbl-info td {
        vertical-align: top;
    }

    #tbl-signature {
        border-collapse: collapse; /* Collapse borders */
        width: 91rem; /* Set table width */
        margin: auto;
    }
    #tbl-signature td {
        border: 1px solid black; /* Border for each cell */
        padding: 10px; /* Padding for better spacing */
        text-align: center; /* Center align text */
    }
</style>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">{{$title}}</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="float-right">
                                                <a href="{{ route('ph.report-complaint.download', $data->ph_complaint_id) }}" target="_blank" class="btn btn-outline-success waves-effect">
                                                    <span>Download</span>
                                                </a>
                                                <a href="{{ route('ph.report-complaint.index') }}" class="btn btn-outline-warning waves-effect">
                                                    <span>Kembali</span>
                                                </a>
                                            </div>
                                            <h3>Complaint Report</h3>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <table class="table table-striped tbl-info mb-2">
                                                        <tr>
                                                            <td style="width: 50%">Kandang</td>
                                                            <td style="width: 5%">:</td>
                                                            <td>{{ $data->kandang->name??'' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Produk</td>
                                                            <td>:</td>
                                                            <td>{{ $data->product->name??'' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Vendor</td>
                                                            <td>:</td>
                                                            <td>{{ $data->supplier->name??'' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Tipe ternak/Jenis peternakan</td>
                                                            <td>:</td>
                                                            <td>{{ $type[$data->type]??'' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Populasi</td>
                                                            <td>:</td>
                                                            <td>{{ $data->population }}</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div class="col-md-6">
                                                    <table class="table table-striped tbl-info">
                                                        <tr>
                                                            <td style="width: 50%">Tanggal investigasi/umur ayam/jam</td>
                                                            <td style="width: 5%">:</td>
                                                            <td>{{ date('d F Y', strtotime($data->investigation_date)) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Lokasi kandang</td>
                                                            <td>:</td>
                                                            <td>{{ $data->kandang->location->name??'' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Komplain</td>
                                                            <td>:</td>
                                                            <td>{{ $data->description }}</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-12">
                                            <span><li><b>Chick In Date and Time</b></li></span>
                                            <div class="table-responsive">
                                                <table class="table table-bordered" id="tbl-chick-in">
                                                    <thead>
                                                        <tr>
                                                            <th rowspan="2">Tanggal</th>
                                                            <th rowspan="2">No. Surat Jalan</th>
                                                            <th rowspan="2">Jam Kirim</th>
                                                            <th rowspan="2">Jam Terima</th>
                                                            <th rowspan="2">Lama Perjalan</th>
                                                            <th rowspan="2">Hatchery</th>
                                                            <th rowspan="2">Grade</th>
                                                            <th colspan="2">Jumlah</th>
                                                        </tr>
                                                        <tr>
                                                            <th>Box</th>
                                                            <th>Ekor</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($data->ph_chick_in as $item)
                                                            <tr>
                                                                <td>{{ date('d/m/Y', strtotime($item->date)) }}</td>
                                                                <td>{{ $item->travel_letter_number }}</td>
                                                                <td>{{ date('H:i', strtotime($item->delivery_time)) }}</td>
                                                                <td>{{ date('H:i', strtotime($item->reception_time)) }}</td>
                                                                <td>{{ date('H:i', strtotime($item->duration)) }}</td>
                                                                <td>{{ $item->hatchery }}</td>
                                                                <td>{{ $item->grade }}</td>
                                                                <td>{{ number_format($item->total_box, 0, ',', '.') }}</td>
                                                                <td>{{ number_format($item->total_heads, 0, ',', '.') }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-4">
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div id="chart-container" style="overflow-x: auto;">
                                                        <canvas id="myChart" style="width: 700px; !important; margin: auto !important;"></canvas>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mt-1">
                                                    <h4>HASIL INVESTIGAS<br>
                                                        Data Mortality</h4>
        
                                                    @php
                                                        $oneWeek = $data->total_deaths + $data->total_culling;
                                                        $threeDays = 0;
                                                        for ($i=1; $i <= 3; $i++) { 
                                                            $threeDayCollect = collect($data->ph_mortality)->firstWhere('day', $i);
                                                            $threeDays += $threeDayCollect['death'] + $threeDayCollect['culling'];
                                                        }
        
                                                        $threeDayPercentage = ( $threeDays/$data->population ) * 100;
                                                        $oneWeekPercentage = ( $oneWeek/$data->population ) * 100;
        
                                                        $arrSymptom = json_decode($data->symptoms);
                                                    @endphp    
        
                                                    <p>Total Deplesi 3 hari = {{ $threeDays }} ekor ( {{number_format($threeDayPercentage, 2)}}% )<br>
                                                        Total Deplesi 7 hari = {{ $oneWeek }} ekor ( {{number_format($oneWeekPercentage, 2)}}% )
                                                    </p>
                                                    <p> <b>Gejala Klinis : </b> 
                                                        @foreach ($arrSymptom as $symptomId)
                                                        <li>{{  \App\Models\Ph\PhSymptom::find($symptomId)->name ?? '' }}</li> 
                                                        @endforeach
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-4">
                                        <div class="col-12">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped w-100" id="tbl-mortality">
                                                    <thead>
                                                    <tr>
                                                        <th style="width: 15%">Umur<br>(Hari)</th>
                                                        <th>1</th>
                                                        <th>2</th>
                                                        <th>3</th>
                                                        <th>4</th>
                                                        <th>5</th>
                                                        <th>6</th>
                                                        <th>7</th>
                                                        <th style="width: 15%">Jumlah</th>
                                                    </tr>
                                                    <tr>
                                                        <th>Jumlah Mati</th>
                                                        @for ($i = 1; $i <= 7; $i++)
                                                            @if (isset($data) && isset($data->ph_mortality))
                                                                @php
                                                                    $currentData = collect($data->ph_mortality)->firstWhere('day', $i);
                                                                @endphp
                                                            @endif
                                                            <td>{{ $currentData['death'] }}</td>
                                                        @endfor
                                                        <td>{{ $data->total_deaths }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Culling</th>
                                                        @for ($i = 1; $i <= 7; $i++)
                                                            @if (isset($data) && isset($data->ph_mortality))
                                                                @php
                                                                    $currentData = collect($data->ph_mortality)->firstWhere('day', $i);
                                                                @endphp
                                                            @endif
                                                            <td>{{ $currentData['culling'] }}</td>
                                                        @endfor
                                                        <td>{{ $data->total_culling }}</td>
                                                    </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-4">
                                        <div class="col-12">
                                            <h4>FOTO-FOTO BUKTI</h4><br>
                                            <div class="table-responsive">
                                                <table class="w-100">
                                                    @foreach (json_decode($data->images) as $index => $item)
                                                        @if ($index % 3 == 0)
                                                        <tr> 
                                                        @endif
                                                            <td class="text-center" style="padding-top: 1.5rem !important; width: 33%" {{ $index + 1 }}>
                                                                <img width="250" style="border-radius: 10px;" src="{{ route('file.show', ['filename' => $item->file]) }}" alt="ph-complaint-image">
                                                                <span><p>{{ $item->description }}</p></span>
                                                            </td>
                                                        @if ($index % 3 == 2 || $loop->last)
                                                        </tr>
                                                        @endif
                                                    @endforeach
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @include('ph.report-complaint.chartjs')
@endsection