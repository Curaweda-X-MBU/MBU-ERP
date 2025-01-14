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
        body {
            background-color: black;
            font-family: 'Poppins';
        }

        td {
            vertical-align: top;
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
    <title>{{ $title }} - Mitra Berlian Unggas</title>
</head>

@php
$bop = $data['bop'] ?? collect();
$non_bop = $data['non_bop'] ?? collect();
$statusPayment = App\Constants::MARKETING_PAYMENT_STATUS;
@endphp

<body>
    <div id="header-mbu" class="header" style="display: flex; flex-wrap: wrap; margin-right: -1rem; margin-left: -1rem;">
        <div style="flex: 0 0 100%; max-width: 100%; position: relative; width: 100%; padding-right: 1rem; padding-left: 1rem;">
            <img src="{{asset('app-assets/images/logo/mbu_logo_light.png')}}" width="200" alt="" srcset=""><br><br>
            <span><b>PT MITRA BERLIAN UNGGAS</b></span><br>
            <span style="font-size: 8px;">SOHO Building Lt.3 (Paris Van Java), Jalan Karang Tinggal, Kel. Cipedes, Kec. Sukajadi, Kota Bandung 40162</span>
            <hr style="border: 1.3px, solid, black"/>
        </div>
    </div>
    <br>
    <table class="tabel w-100">
        <tr>
            <td rowspan="3" style="width: 75%">
                <h1>
                    REKAP BIAYA
                </h1>
            </td>
            <td>Date Period</td>
            <td class="pl-2">:</td>
            <td>{{
                date('d-M-Y',
                    strtotime(old('date_start', $old['date_start']))
                )
                }}
                -
                {{
                date('d-M-Y',
                    strtotime(old('date_end', $old['date_end'] ?? now()))
                )
                }}</td>
        </tr>
        <tr>
            <td>Location</td>
            <td class="pl-2">:</td>
            <td>{{ old('location_name', $old['location_name'] ?? '-') }}</td>
        </tr>
        <tr>
            <td>Farms</td>
            <td class="pl-2">:</td>
            <td>
                @if (old('farms', $old['farms'] ?? null))
                    @foreach(old('available_kandangs', $old['available_kandangs']) as $kandang)
                    <div>{{ $kandang }}</div>
                    @endforeach
                @else
                <div>-</div>
                @endif
            </td>
        </tr>
    </table>
    <br>
    <div>
        <div class="d-flex justify-content-between mt-1">
            <h3>Biaya Operasional (BOP)</h3>
            <h3>Rp
            {{ \App\Helpers\Parser::toLocale(
                old('farms', $old['farms'] ?? null)
                    ? ($bop->sum('price') ?? 0)
                    : ($bop->sum('total_price') ?? 0)
            ) }}
            </h3>
        </div>
        <table class="table table-bordered w-100">
        <thead>
            <tr class="bg-light text-center">
                <th>No</th>
                <th>ID Biaya</th>
                <th>Lokasi</th>
                <th>Tanggal</th>
                <th>Sub Kategori</th>
                <th>QTY</th>
                <th>Status</th>
                <th>Kandang</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($bop as $index => $item)
            @php
            $kandang_length = count($item->kandangs);
            @endphp
            <tr class="text-center">
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->id_expense }}</td>
                <td>{{ $item->location_name }}</td>
                <td>{{ date('d-M-Y', strtotime($item->created_at)) }}</td>
                <td>
                    <div>{{ $item->sub_category ?? $item->name }}</div>
                    @if (isset($item->notes))
                    <div>({{ $item->notes }})</div>
                    @endif
                </td>
                <td>{{ $item->qty ? \App\Helpers\Parser::toLocale($kandang_length > 1 ? $item->total_qty : $item->qty) : '-' }} {{ $item->uom ?? '' }}</td>
                <td>
                    <div>{{ $statusPayment[$item->status] }}</div>
                </td>
                <td>
                    @foreach ($item->kandangs as $kandang)
                    <div>{{ $kandang }}</div>
                    @endforeach
                </td>
            </tr>
            @endforeach
        </tbody>
        </table>
    </div>
    <br>
    <div>
        <div class="d-flex justify-content-between mt-1">
            <h3>Bukan Biaya Operasional (NBOP)</h3>
            <h3>Rp
            {{ \App\Helpers\Parser::toLocale(
                old('farms', $old['farms'] ?? null)
                    ? ($non_bop->sum('price') ?? 0)
                    : ($non_bop->sum('total_price') ?? 0)
            ) }}
            </h3>
        </div>
        <table class="table table-bordered w-100">
            <thead>
                <tr class="bg-light text-center">
                    <th>No</th>
                    <th>ID Biaya</th>
                    <th>Lokasi</th>
                    <th>Tanggal</th>
                    <th>Sub Kategori</th>
                    <th>QTY</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @if (count($non_bop))
                @foreach ($non_bop as $index => $item)
                <tr class="text-center">
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->id_expense }}</td>
                    <td>{{ $item->location_name }}</td>
                    <td>{{ date('d-M-Y', strtotime($item->created_at)) }}</td>
                    <td>
                        <div>{{ $item->sub_category ?? $item->name }}</div>
                        @if (isset($item->notes))
                        <div>({{ $item->notes }})</div>
                        @endif
                    </td>
                    <td>{{ $item->qty ? \App\Helpers\Parser::toLocale($kandang_length > 1 ? $item->total_qty : $item->qty) : '-' }} {{ $item->uom ?? '' }}</td>
                    <td>
                        <div>{{ $statusPayment[$item->status] }}</div>
                    </td>
                </tr>
                @endforeach
                @else
                <tr class="text-center">
                    <td colspan="7">Tidak ada data</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
    <br>
    <div class="row justify-content-end mt-1">
        <h3 class="col-6">Total Keseluruhan Biaya</h3>
        <h3 class="col-6 text-right"><span id="total-recap">Rp {{
            \App\Helpers\Parser::toLocale(
                old('farms', $old['farms'] ?? null)
                    ? ($bop->sum('price') ?? 0) + ($non_bop->sum('price') ?? 0)
                    : ($bop->sum('total_price') ?? 0) + ($non_bop->sum('total_price') ?? 0)
            )
        }}</span></h3>
    </div>
    <br>
    <span class="float-right"><h2>PT MITRA BERLIAN UNGGAS</h2></span>
</body>
<script>
    window.print();
</script>
</html>
