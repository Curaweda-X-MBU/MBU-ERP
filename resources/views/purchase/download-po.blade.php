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
            font-size: calc(100% - 3px);
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
                <h1>
                    PURCHASE ORDER
                </h1>
            </td>
            <td>PO Number</td>
            <td class="pl-2">:</td>
            <td>{{ $data->po_number }}</td>
        </tr>
        <tr>
            <td>Date</td>
            <td class="pl-2">:</td>
            <td>{{ date('d-M-Y', strtotime($data->po_date)) }}</td>
        </tr>
    </table>
    <br>
    <table class="table table-bordered w-100">
        <thead>
            <th style="width: 50%">Vendor</th>
            <th style="width: 50%">Ship To</th>
        </thead>
        <tbody>
            <tr>
                <td>
                    {{ $data->supplier->name }}<br>
                    {{ $data->supplier->pic_name }}<br>
                    {{ $data->supplier->phone??''}}
                    {{ $data->supplier->email ? ' / ' .$data->supplier->email : '' }}<br>
                    {{ $data->supplier->address }}
                </td>
                <td style="vertical-align: top">
                    {{ $data->createdBy->department->company->name}}<br>
                    @php
                        $warehouseIds = $data->warehouse_ids;
                        $warehouses = App\Models\DataMaster\Warehouse::with(['location', 'kandang'])
                            ->whereIn('warehouse_id', $warehouseIds)->get();
                        $locationNames = [];
                        foreach ($warehouses as $key => $value) {
                            $locationNames[] = $value->location->name ?? 'N/A';
                        }
                        $shipTo = array_unique($locationNames);
                    @endphp
                    {{ implode(', ', $shipTo) }}
                </td>
            </tr>
        </tbody>
    </table>
    <br>
    {{-- <table class="table table-bordered w-100">
        <thead>
            <th>Requestioner</th>
            <th>Ship VIA</th>
            <th>F.O.B</th>
            <th>Shipping Terms</th>
        </thead>
        <tbody>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table> --}}
    <br>
    <table class="table table-bordered w-100">
        <thead>
            <th>Item Description</th>
            <th>Unit Price</th>
            <th>Total Quantity</th>
            <th>Total Amount</th>
        </thead>
        <tbody>
            @foreach ($data->purchase_item as $item)
                <tr>
                    <td>{{ $item->product->name??'' }}</td>
                    <td>Rp. <span class="float-right">{{ number_format($item->price, '0', ',', '.') }}</span></td>
                    <td class="text-right">{{ number_format($item->qty, '0', ',', '.') }}</td>
                    <td>Rp. <span class="float-right">{{ number_format($item->price*$item->qty, '0', ',', '.') }}</span></td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <td colspan="3" class="text-right">Grand Total</td>
            <td>Rp. <span class="float-right">{{ number_format($data->total_before_tax, '0', ',', '.') }}</span></td>
        </tfoot>
    </table>
    <br>
    <h4><li>Product Alocation</li></h4>
    <table class="table table-bordered w-100">
        <thead>
            <th>Warehouse Name</th>
            <th>PIC</th>
            <th>Address Detail</th>
            <th>Product Alocation</th>
        </thead>
        <tbody>
            @php
                $arrProductNames = collect($data->purchase_item);
                $collection = collect($data->purchase_item)
                    ->flatMap(function ($item) {
                        return collect($item['purchase_item_alocation'])->map(function ($alocation) use ($item) {
                            return [
                                'warehouse_id' => $alocation['warehouse_id'],
                                'warehouse' => $alocation['warehouse']['name'],
                                'address' => $alocation['warehouse']['location']['address'],
                                'pic' => $alocation['warehouse']['kandang']['user']['name'] ?? '',
                                'phone' => $alocation['warehouse']['kandang']['user']['phone'] ?? '',
                                'product_id' => $item['product']['name'],
                                'alocation_qty' => $alocation['alocation_qty']
                            ];
                        });
                    })
                    ->groupBy('warehouse_id')
                    ->map(function ($group) {
                        return [
                            'warehouse' => $group->first()['warehouse'],
                            'address' => $group->first()['address'],
                            'pic' => $group->first()['pic'],
                            'phone' => $group->first()['phone'],
                            'alocation_product' => $group->map(function ($alocation) {
                                return [
                                    'product_name' => $alocation['product_id'],
                                    'alocation_qty' => $alocation['alocation_qty']
                                ];
                            })->toArray()
                        ];
                    })
                    ->values(); 
            @endphp
            @foreach ($collection->toArray() as $item)
                <tr>
                    <td style="vertical-align: top">{{ $item['warehouse'] }}</td>
                    <td style="vertical-align: top">{{ $item['pic'] }}<br>{{ $item['phone'] }}</td>
                    <td style="vertical-align: top">{{ $item['address'] }}</td>
                    <td>
                        <table class="w-100">
                            @foreach ($item['alocation_product'] as $val)
                                <tr>
                                    <td>{{ $val['product_name'] }}</td>
                                    <td class="text-right">{{ number_format($val['alocation_qty'], '0', ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <br>
    <span class="float-right"><h2>PT MITRA BERLIAN UNGGAS</h2></span>
    <table class="table table-bordered w-50">
        <thead>
            <th>Special Instruction</th>
        </thead>
        <tbody>
            <tr>
                <td></td>
            </tr>
        </tbody>
    </table>
</body>
<script>
    window.print();
</script>
</html>