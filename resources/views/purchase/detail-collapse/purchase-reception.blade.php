<div class="card mb-1">
    <div id="headingCollapse5" class="card-header color-header collapsed" data-toggle="collapse" role="button" data-target="#collapse5" aria-expanded="true" aria-controls="collapse5">
        <span class="lead collapse-title">Penerimaan Barang</span>
    </div>
    <div id="collapse5" role="tabpanel" aria-labelledby="headingCollapse5" class="collapse show" aria-expanded="true">
        <div class="card-body p-2">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="w-100">
                    @foreach ($data->purchase_item as $key => $item)
                    <tr>
                        <td style="vertical-align: top; padding: 0px !important;">{{ $key+1 }}.</td>
                        <td>
                            <table class="table table-bordered table-striped w-100">
                                <thead>
                                    <tr>
                                        <th colspan="10" class="text-left">{{ $item->product->name }} {{ $item->product->product_sub_category->name==="DOC"?" (DOC)":"" }}</th>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Penerimaan</th>
                                        <th>Gudang Tujuan</th>
                                        <th>No. Surat Jalan</th>
                                        <th>Dokumen Surat Jalan</th>
                                        <th>No. Armada Pengangkut</th>
                                        <th>Jumlah Diterima</th>
                                        <th>Jumlah Retur</th>
                                        <th>Ekspedisi</th>
                                        <th>Transport /Item</th>
                                        <th>Transport Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (count($item->purchase_item_alocation) > 0)
                                        @foreach ($item->purchase_item_alocation as $key => $val)
                                            @php
                                                $receivedDate = '';
                                                $travelNumber = '';
                                                $travelNumberDoc = false;
                                                $vehicleNumber = '';
                                                $totalRetur = 0;
                                                $supplierName = '';
                                                $transItem = 0;
                                                $transTotal = 0;
                                                if (count($item->purchase_item_reception) > 0) {
                                                    foreach ($item->purchase_item_reception as $k => $v) {
                                                        if ($v->warehouse_id === $val->warehouse_id) {
                                                            $receivedDate = date('d-M-Y H:i', strtotime($v->received_date));
                                                            $travelNumber = $v->travel_number;
                                                            $travelNumberDoc = $v->travel_number_document;
                                                            $vehicleNumber = $v->vehicle_number;
                                                            $totalRetur = $v->total_retur;
                                                            $supplierName = $v->supplier->name??'';
                                                            $transItem = $v->transport_per_item;
                                                            $transTotal = $v->transport_total;
                                                        }
                                                    }
                                                }
                                            @endphp
                                            <tr>
                                                <td>{{ $receivedDate }}</td>
                                                <td>{{ $val->warehouse->name ??'' }}</td>
                                                <td>{{ $travelNumber }}</td>
                                                <td>
                                                    @if ($travelNumberDoc)
                                                    <a href="{{ route('file.show', ['filename' => $travelNumberDoc]) }}" target="_blank">
                                                        <i data-feather='download' class="mr-50"></i>
                                                        <span>Download</span>
                                                    </a>
                                                    @endif
                                                </td>
                                                <td>{{ $vehicleNumber }}</td>
                                                <td class="text-right">{{ number_format($val->alocation_qty, '0', ',', '.') }}</td>
                                                <td class="text-right">{{ number_format($totalRetur??0, '0', ',', '.') }}</td>
                                                <td>{{ $supplierName }}</td>
                                                <td class="text-right">{{ number_format($transItem??0, '0', ',', '.') }}</td>
                                                <td class="text-right">{{ number_format($transTotal??0, '0', ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="10" class="text-center">Belum ada data</td>
                                        </tr>
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td class="text-right" colspan="8">Jumlah Produk</td>
                                        <td colspan="2" class="text-right">{{ number_format($item->qty, '0', ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-right" colspan="8">Jumlah Produk Diterima</td>
                                        <td colspan="2" class="text-right">{{ number_format($item->total_received, '0', ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-right" colspan="8">Jumlah Produk Belum Diterima</td>
                                        <td colspan="2" class="text-right">{{ number_format($item->total_not_received, '0', ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-right" colspan="8">Nominal Produk Diterima</td>
                                        <td colspan="2">Rp. <span class="pl-2 float-right"> {{ number_format($item->amount_received, '0', ',', '.') }}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="text-right" colspan="8">Nominal Produk Belum Diterima</td>
                                        <td colspan="2">Rp. <span class="pl-2 float-right"> {{ number_format($item->amount_not_received, '0', ',', '.') }}</span></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" style="padding-left: 0px !important;"><hr></td>
                    </tr>
                    @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>