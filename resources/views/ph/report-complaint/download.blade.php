<!DOCTYPE html>
<html lang="en" style="-webkit-text-size-adjust: 100%; -webkit-tap-highlight-color: rgba(34, 41, 47, 0);box-sizing: border-box; ">
<head>
    <title>Poultry Health - Mitra Berlian Unggas</title>
    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        @media print {
            .page-break {
                page-break-after: always; 
            }
            #header-mbu {
                position: absolute; 
            }

            @page {
                size: A4 landscape;
            }
        }

    </style>
    <script src="{{asset('app-assets/vendors/js/vendors.min.js')}}"></script>
</head>
<body style='font-family: "Poppins"; font-size: 10px;; font-weight: 400; line-height: 1.45; color: #6e6b7b; background-color: #f8f8f8;'>
    <div id="header-mbu" class="header" style="display: flex; flex-wrap: wrap; margin-right: -1rem; margin-left: -1rem;">
        <div style="flex: 0 0 100%; max-width: 100%; position: relative; width: 100%; padding-right: 1rem; padding-left: 1rem;">
            <img src="{{asset('app-assets/images/logo/mbu_logo_light.png')}}" width="200" alt="" srcset=""><br><br>
            <span><b>PT MITRA BERLIAN UNGGAS</b></span><br>
            <span style="font-size: 8px;">SOHO Building Lt.3 (Paris Van Java), Jalan Karang Tinggal, Kel. Cipedes, Kec. Sukajadi, Kota Bandung 40162</span>
            <hr style="border: 1.3px, solid, black"/>
        </div>
    </div>
    {{-- header --}}
    <div class="page-break">
        <div style="display: flex; flex-wrap: wrap; margin-right: -1rem; margin-left: -1rem;">
            <div style="flex: 0 0 100%; max-width: 100%; position: relative; width: 100%; padding-right: 1rem; padding-left: 1rem;">
                <h2>Complaint Report</h2>
            </div>
        </div>
        <div style="display: flex; flex-wrap: wrap; margin-right: -1rem; margin-left: -1rem;">
            <div style="flex: 0 0 100%; max-width: 100%; position: relative; width: 100%; padding-right: 1rem; padding-left: 1rem;">
                <div style="display: block; width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;">
                    <table style="width: 100%">
                        <td style="width: 23rem; vertical-align: top;">
                            <table class="tbl-info">
                                <tr>
                                    <td style="padding: 0 10px 0 10px !important; padding-left: unset !important; vertical-align: top;">Kandang</td>
                                    <td style="padding: 0 10px 0 10px !important; padding-left: unset !important; vertical-align: top;">:</td>
                                    <td style="padding: 0 10px 0 10px !important; padding-left: unset !important; vertical-align: top;">{{ $data->kandang->name ?? '' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 0 10px 0 10px !important; padding-left: unset !important; vertical-align: top;">Produk</td>
                                    <td style="padding: 0 10px 0 10px !important; padding-left: unset !important; vertical-align: top;">:</td>
                                    <td style="padding: 0 10px 0 10px !important; padding-left: unset !important; vertical-align: top;">{{ $data->product->name ?? '' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 0 10px 0 10px !important; padding-left: unset !important; vertical-align: top;">Vendor</td>
                                    <td style="padding: 0 10px 0 10px !important; padding-left: unset !important; vertical-align: top;">:</td>
                                    <td style="padding: 0 10px 0 10px !important; padding-left: unset !important; vertical-align: top;">{{ $data->supplier->name }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 0 10px 0 10px !important; padding-left: unset !important; vertical-align: top;">Tipe ternak/Jenis peternakan</td>
                                    <td style="padding: 0 10px 0 10px !important; padding-left: unset !important; vertical-align: top;">:</td>
                                    <td style="padding: 0 10px 0 10px !important; padding-left: unset !important; vertical-align: top;">{{ $type[$data->type]??'' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 0 10px 0 10px !important; padding-left: unset !important; vertical-align: top;">Populasi</td>
                                    <td style="padding: 0 10px 0 10px !important; padding-left: unset !important; vertical-align: top;">:</td>
                                    <td style="padding: 0 10px 0 10px !important; padding-left: unset !important; vertical-align: top;">{{ $data->population }}</td>
                                </tr>
                            </table>
                        </td>
                        {{-- <td style="width: 33%;"></td> --}}
                        <td style="vertical-align: top;">
                            <table>
                                <tr>
                                    <td style="padding: 0 10px 0 10px !important; padding-left: unset !important; vertical-align: top; width: 12.5rem;">Tanggal investigasi/umur ayam/jam</td>
                                    <td style="padding: 0 10px 0 10px !important; padding-left: unset !important; vertical-align: top;">:</td>
                                    <td style="padding: 0 10px 0 10px !important; padding-left: unset !important; vertical-align: top;">{{ date('d F Y', strtotime($data->investigation_date)) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 0 10px 0 10px !important; padding-left: unset !important; vertical-align: top;">Lokasi kandang</td>
                                    <td style="padding: 0 10px 0 10px !important; padding-left: unset !important; vertical-align: top;">:</td>
                                    <td style="padding: 0 10px 0 10px !important; padding-left: unset !important; vertical-align: top;">{{ $data->kandang->location->name }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 0 10px 0 10px !important; padding-left: unset !important; vertical-align: top;">Komplain</td>
                                    <td style="padding: 0 10px 0 10px !important; padding-left: unset !important; vertical-align: top;">:</td>
                                    <td style="padding: 0 10px 0 10px !important; padding-left: unset !important; vertical-align: top;">{{ $data->description }}</td>
                                </tr>
                            </table>
                        </td>
                    </table>
                    
                </div>
            </div>
        </div>
        <div style="display: flex; flex-wrap: wrap; margin-right: -1rem; margin-left: -1rem; margin-top: 10px;">
            <div style="flex: 0 0 100%; max-width: 100%; position: relative; width: 100%; padding-right: 1rem; padding-left: 1rem;">
                <span><b>Chick In Date and Time</b></span>
                <div style="display: block; width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;">
                    <table style="width: 100%; border-collapse: collapse;" id="tbl-chick-in">
                        <thead>
                            <tr>
                                <th style="vertical-align: middle; text-align: center; padding: 5px; border: 1px solid black;" rowspan="2">Tanggal</th>
                                <th style="vertical-align: middle; text-align: center; padding: 5px; border: 1px solid black;" rowspan="2">No. Surat Jalan</th>
                                <th style="vertical-align: middle; text-align: center; padding: 5px; border: 1px solid black;" rowspan="2">Jam Kirim</th>
                                <th style="vertical-align: middle; text-align: center; padding: 5px; border: 1px solid black;" rowspan="2">Jam Terima</th>
                                <th style="vertical-align: middle; text-align: center; padding: 5px; border: 1px solid black;" rowspan="2">Lama Perjalan</th>
                                <th style="vertical-align: middle; text-align: center; padding: 5px; border: 1px solid black;" rowspan="2">Hatchery</th>
                                <th style="vertical-align: middle; text-align: center; padding: 5px; border: 1px solid black;" rowspan="2">Grade</th>
                                <th style="vertical-align: middle; text-align: center; padding: 5px; border: 1px solid black;" colspan="2">Jumlah</th>
                            </tr>
                            <tr>
                                <th style="vertical-align: middle; text-align: center; padding: 5px; border: 1px solid black;">Box</th>
                                <th style="vertical-align: middle; text-align: center; padding: 5px; border: 1px solid black;">Ekor</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data->ph_chick_in as $item)
                                <tr>
                                    <td style="text-align: center; padding: 5px !important; border: 1px solid black;">{{ date('d/m/Y', strtotime($item->date)) }}</td>
                                    <td style="text-align: center; padding: 5px !important; border: 1px solid black;">{{ $item->travel_letter_number }}</td>
                                    <td style="text-align: center; padding: 5px !important; border: 1px solid black;">{{ date('H:i', strtotime($item->delivery_time)) }}</td>
                                    <td style="text-align: center; padding: 5px !important; border: 1px solid black;">{{ date('H:i', strtotime($item->reception_time)) }}</td>
                                    <td style="text-align: center; padding: 5px !important; border: 1px solid black;">{{ date('H:i', strtotime($item->duration)) }}</td>
                                    <td style="text-align: center; padding: 5px !important; border: 1px solid black;">{{ $item->hatchery }}</td>
                                    <td style="text-align: center; padding: 5px !important; border: 1px solid black;">{{ $item->grade }}</td>
                                    <td style="text-align: center; padding: 5px !important; border: 1px solid black;">{{ number_format($item->total_box, 0, ',', '.') }}</td>
                                    <td style="text-align: center; padding: 5px !important; border: 1px solid black;">{{ number_format($item->total_heads, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div style="flex: 0 0 100%; max-width: 100%; position: relative; width: 100%; padding-right: 1rem; padding-left: 1rem; padding-top: 10px;">
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
                @endphp    
                <p>Total Deplesi 3 hari = {{ $threeDays }} ekor ( {{number_format($threeDayPercentage, 2)}}% )<br>
                    Total Deplesi 7 hari = {{ $oneWeek }} ekor ( {{number_format($oneWeekPercentage, 2)}}% )
                </p>
                
                <div style="display: block; width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;">
                    <table style="width: 100%; border-collapse: collapse;" id="tbl-mortality">
                        <thead>
                        <tr>
                            <th style="width: 15%; vertical-align: middle; text-align: center; padding: 5px; border: 1px solid black;">Umur<br>(Hari)</th>
                            <th style="vertical-align: middle; text-align: center; padding: 5px; border: 1px solid black;">1</th>
                            <th style="vertical-align: middle; text-align: center; padding: 5px; border: 1px solid black;">2</th>
                            <th style="vertical-align: middle; text-align: center; padding: 5px; border: 1px solid black;">3</th>
                            <th style="vertical-align: middle; text-align: center; padding: 5px; border: 1px solid black;">4</th>
                            <th style="vertical-align: middle; text-align: center; padding: 5px; border: 1px solid black;">5</th>
                            <th style="vertical-align: middle; text-align: center; padding: 5px; border: 1px solid black;">6</th>
                            <th style="vertical-align: middle; text-align: center; padding: 5px; border: 1px solid black;">7</th>
                            <th style="width: 15%; vertical-align: middle; text-align: center; padding: 5px; border: 1px solid black;">Jumlah</th>
                        </tr>
                        <tr>
                            <th style="border: 1px solid black;">Jumlah Mati</th>
                            @for ($i = 1; $i <= 7; $i++)
                                @if (isset($data) && isset($data->ph_mortality))
                                    @php
                                        $currentData = collect($data->ph_mortality)->firstWhere('day', $i);
                                    @endphp
                                @endif
                                <td style="text-align: center; padding: 5px !important; border: 1px solid black;">{{ $currentData['death'] }}</td>
                            @endfor
                            <td style="text-align: center; padding: 5px !important; border: 1px solid black;">{{ $data->total_deaths }}</td>
                        </tr>
                        <tr>
                            <th style="border: 1px solid black;">Culling</th>
                            @for ($i = 1; $i <= 7; $i++)
                                @if (isset($data) && isset($data->ph_mortality))
                                    @php
                                        $currentData = collect($data->ph_mortality)->firstWhere('day', $i);
                                    @endphp
                                @endif
                                <td style="text-align: center; padding: 5px !important; border: 1px solid black;">{{ $currentData['culling'] }}</td>
                            @endfor
                            <td style="text-align: center; padding: 5px !important; border: 1px solid black;">{{ $data->total_culling }}</td>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="page-break">
        <div style="display: flex; flex-wrap: wrap; margin-right: -1rem; margin-left: -1rem; margin-top: 20px;">
            <div style="flex: 0 0 100%; max-width: 100%; position: relative; width: 100%; padding-right: 1rem; padding-left: 1rem;">
                <div id="chart-container">
                    <canvas id="myChart" style="width: 900px; !important; margin: auto !important;"></canvas>
                </div>
            </div>
        </div>
        <div style="display: flex; flex-wrap: wrap; margin-right: -1rem; margin-left: -1rem; margin-top: 20px;">
            <div style="flex: 0 0 100%; max-width: 100%; position: relative; width: 100%; padding-right: 1rem; padding-left: 1rem;">
                <p> <b>Gejala Klinis : </b> </p>
                <table>
                    @php
                        $arrSymptoms = json_decode($data->symptoms);
                        $rows = [];
                
                        foreach($arrSymptoms as $index => $item) {
                            $symptomName = \App\Models\Ph\PhSymptom::find($item)->name ?? '';
                            if ($index < 3) {
                                $rows[$index] = [$symptomName];
                            } else {
                                $rowIndex = $index % 3;
                                $rows[$rowIndex][] = $symptomName;
                            }
                        }
                    @endphp
                    
                    @foreach($rows as $row)
                        <tr>
                            @foreach($row as $cell)
                                <td style="padding-right: 20px;"><li>{{  $cell }}</li></td> 
                            @endforeach
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
    @php
        $arrImages = json_decode($data->images);
        $chunkedData = array_chunk($arrImages, 3);
    @endphp
    @foreach ($chunkedData as $chunk)
    <div class="page-break">
        <div style="display: flex; flex-wrap: wrap; margin-right: -1rem; margin-left: -1rem; margin-top: 20px;">
            <div style="flex: 0 0 100%; max-width: 100%; position: relative; width: 100%; padding-right: 1rem; padding-left: 1rem;">
                <span style="font-size: 10px; font-weight: bold;">FOTO-FOTO BUKTI</span><br><br><br>
                <div style="display: block; width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;">
                    <table style="width: {{(count($chunk) < 3) ? (33 * count($chunk)) . '%' : '100%'}}; border-collapse: collapse;">
                        <tr>
                            @foreach ($chunk as $item)
                                <td style="text-align:center; padding-top: 1.5rem !important; padding-left: 1.5rem !important; padding-right: 1.5rem !important; width:33%" {{ $index + 1 }}>
                                    <img width="270" style="border-radius: 10px;" src="{{ route('file.show', ['filename' => $item->file]) }}" alt="ph-complaint-image">
                                    <span><p>{{ $item->description }}</p></span>
                                </td>
                            @endforeach
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    <div class="page-break">
        <div style="display: flex; flex-wrap: wrap; margin-right: -1rem; margin-left: -1rem; margin-top: 20px;">
            <div style="flex: 0 0 100%; max-width: 100%; position: relative; width: 100%; padding-right: 1rem; padding-left: 1rem;">
                <p>Note: Foto bukti kematian tidak dilampirkan semua</p>
                <div style="display: block; width: 98%; overflow-x: auto; -webkit-overflow-scrolling: touch; margin: auto;">
                    <table id="tbl-signature" style="border-collapse: collapse; margin: auto;">
                        <tr>
                            <td style="padding: 0 10px 0 10px !important; border: 1px solid black; padding: 10px; text-align: center;">Dibuat Oleh</td>
                            <td style="padding: 0 10px 0 10px !important; border: 1px solid black; padding: 10px; text-align: center;">Pengaju Culling</td>
                            @foreach ($sign as $key => $item)
                            @if (count($item) > 1)
                            <td style="padding: 0 10px 0 10px !important; border: 1px solid black; padding: 10px; text-align: center;">{{$key}}</td>
                            @endif
                            <td style="padding: 0 10px 0 10px !important; border: 1px solid black; padding: 10px; text-align: center;">{{$key}}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td style="height: 10rem; border: 1px solid black; padding: 10px; text-align: center;"></td>
                            <td style="height: 10rem; border: 1px solid black; padding: 10px; text-align: center;"></td>
                            @foreach ($sign as $key => $item)
                            @if (count($item) > 1)
                            <td style="height: 10rem; border: 1px solid black; padding: 10px; text-align: center;"></td>
                            @endif
                            <td style="height: 10rem; border: 1px solid black; padding: 10px; text-align: center;"></td>
                            @endforeach
                        </tr>
                        <tr>
                            <td style="padding: 0 10px 0 10px !important; border: 1px solid black; padding: 10px; text-align: center;"><b>( {{$data->createdby->name}} )</b></td>
                            <td style="padding: 0 10px 0 10px !important; border: 1px solid black; padding: 10px; text-align: center;"><b>( {{ $data->cullingpic->name??'N/A' }} )</b></td>
                            @foreach ($sign as $key => $item)
                            @foreach ($sign[$key] as $i => $v)
                            <td style="padding: 0 10px 0 10px !important; border: 1px solid black; padding: 10px; text-align: center;"><b>( {{$v}} )</b></td>
                            @endforeach
                            @endforeach
                        </tr>
                        <tr>
                            <td style="padding: 0 10px 0 10px !important; border: 1px solid black; padding: 10px; text-align: center;">( {{$data->createdby->role->name}} )</td>
                            <td style="padding: 0 10px 0 10px !important; border: 1px solid black; padding: 10px; text-align: center;">( {{$data->cullingpic->role->name??'N/A'}} )</td>
                            @foreach ($sign as $key => $item)
                            @foreach ($sign[$key] as $i => $v)
                            <td style="padding: 0 10px 0 10px !important; border: 1px solid black; padding: 10px; text-align: center;">( {{$i}} )</td>
                            @endforeach
                            @endforeach
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @include('ph.report-complaint.chartjs')
    
    <script>
        $(function () {
            let pages = $('.page-break');
            pages.each(function(index) {
                $(this).before(`<div class="header" style="display: flex; flex-wrap: wrap; margin-right: -1rem; margin-left: -1rem;">
                    <div style="flex: 0 0 100%; max-width: 100%; position: relative; width: 100%; padding-right: 1rem; padding-left: 1rem;">
                        <img src="{{asset('app-assets/images/logo/mbu_logo_light.png')}}" width="200" alt="" srcset=""><br><br>
                        <span><b>PT MITRA BERLIAN UNGGAS</b></span><br>
                        <span style="font-size: 8px;">SOHO Building Lt.3 (Paris Van Java), Jalan Karang Tinggal, Kel. Cipedes, Kec. Sukajadi, Kota Bandung 40162</span>
                        <hr style="border: 1.3px, solid, black"/>
                    </div>
                </div>`);
            });
            var printInterval = setInterval(function() {
                window.print();
                clearInterval(printInterval); // Stop the interval after the first print
            }, 1000);
            $('#header-mbu').remove();
        });
    </script>
</body>
</html>