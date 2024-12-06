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
                                        <th colspan="6" class="text-left">{{ $item->product->name }}</th>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Penerimaan</th>
                                        <th>No. Surat Jalan</th>
                                        <th>Dokumen Surat Jalan</th>
                                        <th>No. Armada Pengangkut</th>
                                        <th>Jumlah Diterima</th>
                                        <th>Jumlah Retur</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (count($item->purchase_item_reception)>0)
                                        @foreach ($item->purchase_item_reception as $val)
                                            <tr>
                                                <td>{{ date('d-M-Y H:i', strtotime($val->received_date)) }}</td>
                                                <td>{{ $val->travel_number }}</td>
                                                <td>
                                                    @if ($val->travel_number_document)
                                                    <a href="{{ route('file.show', ['filename' => $val->travel_number_document]) }}" target="_blank">
                                                        <i data-feather='download' class="mr-50"></i>
                                                        <span>Download</span>
                                                    </a>
                                                    @endif
                                                </td>
                                                <td>{{ $val->vehicle_number }}</td>
                                                <td class="text-right">{{ number_format($val->total_received, '0', ',', '.') }}</td>
                                                <td class="text-right">{{ number_format($val->total_retur, '0', ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" class="text-center">Belum ada data</td>
                                        </tr>
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td class="text-right" colspan="4">Jumlah Produk</td>
                                        <td colspan="2" class="text-right">{{ number_format($item->qty, '0', ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-right" colspan="4">Jumlah Produk Diterima</td>
                                        <td colspan="2" class="text-right">{{ number_format($item->total_received, '0', ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-right" colspan="4">Jumlah Produk Belum Diterima</td>
                                        <td colspan="2" class="text-right">{{ number_format($item->total_not_received, '0', ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-right" colspan="4">Nominal Produk Diterima</td>
                                        <td colspan="2">Rp. <span class="pl-2 float-right"> {{ number_format($item->amount_received, '0', ',', '.') }}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="text-right" colspan="4">Nominal Produk Belum Diterima</td>
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