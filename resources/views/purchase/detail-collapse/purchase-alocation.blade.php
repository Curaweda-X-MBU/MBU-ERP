<div class="card mb-1">
    <div id="headingCollapse4" class="card-header color-header collapsed" data-toggle="collapse" role="button" data-target="#collapse4" aria-expanded="true" aria-controls="collapse4">
        <span class="lead collapse-title">Alokasi Produk </span>
    </div>
    <div id="collapse4" role="tabpanel" aria-labelledby="headingCollapse4" class="collapse show" aria-expanded="true">
        <div class="card-body p-2">
            <div class="col-12">
                @foreach ($data->purchase_item as $item)
                <h5><li>{{$item->product->name ?? 'N/A'}}</li></h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped w-100 no-wrap text-center mb-2" id="purchase-repeater">
                        <thead>
                            <th>Gudang</th>
                            <th>Jumlah Alokasi</th>
                        </thead>
                        <tbody>
                            @if (count($item->purchase_item_alocation) > 0)
                                @foreach ($item->purchase_item_alocation as $val)
                                <tr>
                                    <td class="text-left">{{ $val->warehouse->name }}</td>
                                    <td class="text-right">{{ number_format($val->alocation_qty, '0', ',', '.') }}</td>
                                </tr>
                                @endforeach
                            @else
                            <tr>
                                <td class="text-center" colspan="6">Belum ada data</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>