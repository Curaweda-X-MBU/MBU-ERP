<div class="card mb-1">
    <div id="headingCollapse3" class="card-header color-header collapsed" data-toggle="collapse" role="button" data-target="#collapse3" aria-expanded="true" aria-controls="collapse3">
        <span class="lead collapse-title"> Item Pembelian </span>
    </div>
    <div id="collapse3" role="tabpanel" aria-labelledby="headingCollapse3" class="collapse show" aria-expanded="true">
        <div class="card-body p-2">
            <div class="col-12">
                <table>
                    <td style="width: 53%; vertical-align: top;">
                        <table class="mb-2">
                            <tr>
                                <td><b>Unit Bisnis</b></td>
                                <td>:</td>
                                <td>{{ $data->warehouse->location->company->name??'' }}</td>
                            </tr>
                            <tr>
                                <td><b>Area</b></td>
                                <td>:</td>
                                <td>{{ $data->warehouse->location->area->name??'' }}</td>
                            </tr>
                            <tr>
                                <td><b>Lokasi</b></td>
                                <td>:</td>
                                <td>{{ $data->warehouse->location->name??'' }}</td>
                            </tr>
                            <tr>
                                <td><b>Gudang Penyimpanan</b></td>
                                <td>:</td>
                                <td>{{ $data->warehouse->name??'' }}</td>
                            </tr>
                        </table>
                    </td>
                    <td style="vertical-align: top;">
                        <table class="mb-2">
                            <tr>
                                <td><b>Nama Vendor</b></td>
                                <td>:</td>
                                <td>{{ $data->supplier->name??'' }}</td>
                            </tr>
                            <tr>
                                <td><b>Alamat Vendor</b></td>
                                <td>:</td>
                                <td>{{ $data->supplier->address??'' }}</td>
                            </tr>
                            <tr>
                                <td><b>Tgl. Dibutuhkan</b></td>
                                <td>:</td>
                                <td>{{ date('d-M-Y', strtotime($data->require_date)) }}</td>
                            </tr>
                            <tr>
                                <td><b>Nomor</b></td>
                                <td>:</td>
                                <td>{{ $data->pr_number }}</td>
                            </tr>
                            <tr>
                                <td><b>Nomor PO</b></td>
                                <td>:</td>
                                <td>
                                    @if ($data->po_number)
                                        <a class="btn btn-sm btn-primary" target="_blank" href="{{ route('purchase.detail', ['id' => $data->purchase_id, 'po_number' => $data->po_number]) }}">
                                            {{ $data->po_number }}
                                        </a>
                                    @else
                                        <i class="text-muted">Belum dibuat</i>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                </table>
            </div>
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped w-100 no-wrap text-center" id="purchase-repeater">
                        <thead>
                            <th>Produk</th>
                            <th>Jenis Produk</th>
                            {{-- <th>Project Aktif</th> --}}
                            {{-- <th>Gudang/Tempat<br>Pengiriman</th> --}}
                            <th width="30">Jumlah</th>
                            <th>Satuan</th>
                            <th>Harga Satuan</th>
                            <th>Pajak (%)</th>
                            <th>Discount (%)</th>
                            <th>Total (Rp.)</th>
                        </thead>
                        <tbody>
                            @foreach ($data->purchase_item as $item)
                            <tr>
                                <td>{{ $item->product->name??'' }}</td>
                                <td>{{ $item->product->product_category->name??'' }}</td>
                                {{-- <td>{{ $item->project->kandang->name??'' }}</td> --}}
                                {{-- <td>{{ $item->warehouse->name }}</td> --}}
                                <td class="text-right">{{ number_format($item->qty, '0', ',', '.') }}</td>
                                <td>{{ $item->product->uom->name??'' }}</td>
                                <td class="text-right">{{ number_format($item->price, '0', ',', '.') }}</td>
                                <td class="text-right">{{ $item->tax }}</td>
                                <td class="text-right">{{ $item->discount }}</td>
                                <td class="text-right">{{ number_format($item->price*$item->qty, '0', ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="6" rowspan="4" class="text-left" style="vertical-align: top">
                                    Catatan : <br>{{ $data->notes }} <br>
                                </td>
                                <td colspan="1" class="text-right">
                                    Total Sebelum Pajak
                                </td>
                                <td style="padding: 0 10px 0 10px;" class="text-right">
                                    {{ number_format($data->total_before_tax, '0', ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="1" class="text-right">
                                    Pajak
                                </td>
                                <td style="padding: 0 10px 0 10px;" class="text-right">
                                    {{ number_format($data->total_tax, '0', ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="1" class="text-right">
                                    Discount
                                </td>
                                <td style="padding: 0 10px 0 10px;" class="text-right">
                                    {{ number_format($data->total_discount, '0', ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="1" class="text-right">
                                    <b>Total</b>
                                </td>
                                <td style="padding: 0 10px 0 10px;" class="text-right">
                                    <b>{{ number_format($data->total_after_tax==0?$data->total_before_tax:$data->total_after_tax, '0', ',', '.') }}</b>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>