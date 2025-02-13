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
            <td rowspan="2" style="width: 75%">
                <h1 class="font-weight-bolder">
                    EXPENSE ITEMS
                </h1>
            </td>
            <td>PO Number</td>
            <td class="pl-2">:</td>
            <td>{{ $data->po_number }}</td>
        </tr>
        <tr>
            <td>Date</td>
            <td class="pl-2">:</td>
            <td>{{ date('d-M-Y', strtotime($data->approved_at)) }}</td>
        </tr>
        <tr>
            <td>Location</td>
            <td class="pl-2">:</td>
            <td>{{ $data->location->name }}</td>
        </tr>
        <tr>
            <td>Selected Kandang</td>
            <td class="pl-2">:</td>
            <td>{!! $data->expense_kandang?->map(fn($kandang) => $kandang->kandang->name ?? '')->join('<br>') ?: '-' !!}</td>
        </tr>
    </table>
    <br>
    <h3>MAIN PRICES</h3>
    <table class="table table-bordered w-100">
        <thead class="font-weight-bolder">
            <th>No</th>
            <th>Supplier</th>
            <th>Item</th>
            <th>Qty/Kandang</th>
            <th>Total Qty</th>
            <th>UOM</th>
            <th>Price/Kandang</th>
            <th>Total Price</th>
            <th>Notes</th>
        </thead>
        <tbody>
            @foreach ($data->expense_main_prices as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->supplier->name }}</td>
                    <td>{{ $item->nonstock->name }}</td>
                    <td>{{ \App\Helpers\Parser::trimLocale($item->total_qty) }}</td>
                    <td>{{ \App\Helpers\Parser::trimLocale($item->qty) }}</td>
                    <td>{{ $item->nonstock->uom->name ?? '-' }}</td>
                    <td>Rp<span class="float-right">{{ \App\Helpers\Parser::toLocale($item->total_price) }}</span></td>
                    <td>Rp<span class="float-right">{{ \App\Helpers\Parser::toLocale($item->price) }}</span></td>
                    <td>{{ $item->notes ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <br>
    <h3>ADDITIONAL PRICES</h3>
    <table class="table table-bordered w-100">
        <thead class="font-weight-bolder">
            <th>No</th>
            <th>Item</th>
            <th>Price</th>
            <th>Notes</th>
        </thead>
        <tbody>
            @if (count($data->expense_addit_prices) > 0)
                @foreach ($data->expense_addit_prices as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->name }}</td>
                    <td class="text-right">Rp{{ \App\Helpers\Parser::toLocale($item->price) }}</td>
                    <td>{{ $item->notes ?? '-' }}</td>
                </tr>
                @endforeach
            @else
            <tr>
                <td class="text-center" colspan="4">No data Additional Prices</td>
            </tr>
            @endif
        </tbody>
    </table>
    <br>
    <h3>ACCUMULATION</h3>
    <table class="table table-bordered w-100">
        <thead class="font-weight-bolder">
            <th>Transaction</th>
            <th>Total Price</th>
        </thead>
        <tbody>
            <tr>
                <td>MAIN PRICES</td>
                <td>+ Rp<span class="float-right">{{ \App\Helpers\Parser::toLocale($data->expense_main_prices->sum('price')) }}</span></td>
            </tr>
            <tr>
                <td>ADDITIONAL PRICES</td>
                <td>+ Rp<span class="float-right">{{ \App\Helpers\Parser::toLocale($data->expense_addit_prices->sum('price')) }}</span></td>
            </tr>
        </tbody>
        <tfoot class="font-weight-bolder">
            <td colspan="1" class="text-right">Grand Total</td>
            <td>Rp<span class="float-right">{{ \App\Helpers\Parser::toLocale($data->grand_total) }}</span></td>
        </tfoot>
    </table>
    <br>
    <span class="float-right"><h2>PT MITRA BERLIAN UNGGAS</h2></span>
</body>
<script>
    window.print();
</script>
</html>
