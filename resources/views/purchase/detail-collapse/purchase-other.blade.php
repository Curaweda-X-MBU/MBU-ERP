<div class="card mb-1">
    <div id="headingCollapse4" class="card-header color-header collapsed" data-toggle="collapse" role="button" data-target="#collapse4" aria-expanded="true" aria-controls="collapse4">
        <span class="lead collapse-title">Rincian Biaya Lainnya </span>
    </div>
    <div id="collapse4" role="tabpanel" aria-labelledby="headingCollapse4" class="collapse show" aria-expanded="true">
        <div class="card-body p-2">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped w-100 no-wrap text-center" id="purchase-repeater">
                        <thead>
                            <th>Nama Biaya</th>
                            <th>Nominal</th>
                        </thead>
                        <tbody>
                            @if (count($data->purchase_other) > 0)
                                @foreach ($data->purchase_other as $item)
                                <tr>
                                    <td class="text-left">{{ $item->name }}</td>
                                    <td class="text-right">{{ number_format($item->amount, '0', ',', '.') }}</td>
                                </tr>
                                @endforeach
                            @else
                            <tr>
                                <td class="text-center" colspan="6">Belum ada data</td>
                            </tr>
                            @endif
                        </tbody>
                        <tfoot>
                            <td class="text-right">
                                Total Biaya Lainnya
                            </td>
                            <td style="padding: 0 10px 0 10px;" class="text-right">
                                {{ number_format($data->total_other_amount, '0', ',', '.') }}
                            </td>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>