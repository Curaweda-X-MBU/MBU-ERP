<div class="card mb-1">
    <div id="headingCollapse4" class="card-header color-header collapsed" data-toggle="collapse" role="button" data-target="#collapse4" aria-expanded="true" aria-controls="collapse4">
        <span class="lead collapse-title"> Estimasi Anggaran </span>
    </div>
    <div id="collapse4" role="tabpanel" aria-labelledby="headingCollapse4" class="collapse show" aria-expanded="true">
        <div class="card-body p-2">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered w-100">
                        <thead>
                            <th>Nama Produk</th>
                            <th class="text-right">QTY</th>
                            <th class="text-right">Harga Satuan (Rp)</th>
                            <th class="text-right">Total Anggaran (Rp)</th>
                        </thead>
                        <tbody>
                            @if ($data->project_budget)
                                @foreach ($data->project_budget as $item)
                                    <tr>
                                        <td>{{ $item->product->name??$item->nonstock->name??'' }}</td>
                                        <td class="text-right">{{ number_format($item->qty, '0', ',', '.') }}</td>
                                        <td class="text-right">{{ number_format($item->price, '0', ',', '.') }}</td>
                                        <td class="text-right">{{ number_format($item->qty*$item->price, '0', ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-right">
                                    Grand Total Anggaran
                                </th>
                                <td class="text-right" style="background-color: #f3f2f7;">
                                    {{ number_format($data->total_budget, '0', ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function () {

    });
</script>
