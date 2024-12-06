<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/vendors.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/bootstrap.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/bootstrap-extended.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/colors.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/components.css')}}">
    <style>
        html {
            font-size: calc(100% - 4px); /* Decrease by 2 pixels */
        }

        body {
            background-color: black;
            font-family: 'Poppins';
        }

        #tbl-chart td {
            vertical-align: top;
        }

        .table th, .table td {
            padding: 7px !important;
        }

        @media print {
            @page {
                size: A4;
            }
        }
    </style>
    <script src="{{asset('app-assets/vendors/js/vendors.min.js')}}"></script>
    <script src="{{asset('app-assets/js/core/app-menu.js')}}"></script>
    <script src="{{asset('app-assets/js/core/app.js')}}"></script>
    <title>Poultry Health - Mitra Berlian Unggas</title>
</head>
<body>
    @php
        use Carbon\Carbon;
        use Illuminate\Support\Collection;

        if (request()->has('area_id')) {
            $areaId = request()->get('area_id');
            $areaDt = App\Models\DataMaster\Area::find($areaId);
        }

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
    <div id="header-mbu" class="header" style="display: flex; flex-wrap: wrap; margin-right: -1rem; margin-left: -1rem;">
        <div style="flex: 0 0 100%; max-width: 100%; position: relative; width: 100%; padding-right: 1rem; padding-left: 1rem;">
            <img src="{{asset('app-assets/images/logo/mbu_logo_light.png')}}" width="200" alt="" srcset=""><br><br>
            <span><b>PT MITRA BERLIAN UNGGAS</b></span><br>
            <span style="font-size: 8px;">SOHO Building Lt.3 (Paris Van Java), Jalan Karang Tinggal, Kel. Cipedes, Kec. Sukajadi, Kota Bandung 40162</span>
            <hr style="border: 1.3px, solid, black"/>
        </div>
    </div>
    <div class="text-center mt-2 mb-3">
        <h2>
            POULTRY HEALTH DIVISION<br>
            FIRST WEEK MORTALITY {{strtoupper(Carbon::parse($year.'-'.$month.'-01')->format('F'))}} {{$year}} <br>
            {{ request()->has('area_id')&&request()->get('area_id')>0?'AREA '.strtoupper($areaDt->name):'' }}
        </h2>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="table-responsive mb-2">
                <table class="table table-bordered table-striped w-100">
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
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <table id="tbl-chart" class="w-100 mt-3">
                    <td style="width: 70%">
                        <div id="chart-container">
                            <canvas id="myChart" width="650"></canvas>
                        </div>
                    </td>
                    <td>
                        <table class="table table-bordered" style="margin-top: 6.5px;">
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
                    </td>
                </table>
                
                
            </div>
        </div>
    </div>
    @include('ph.performance.chartjs')
    <script>
        $(function () {
            var printInterval = setInterval(function() {
                window.print();
                clearInterval(printInterval); // Stop the interval after the first print
            }, 1000);
        });
    </script>
</body>
</html>