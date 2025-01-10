<div class="table-responsive mt-2" style="overflow-x: hidden;">
    <div class="row bg-primary d-flex justify-content-center align-items-center py-1 text-white px-2">
        <p class="col-md-6 mb-0">Bukan Biaya Operasional (NBOP)</p>
        <p class="col-md-6 mb-0 text-right">Total: Rp <span id="total-biaya-non-bop">{{
            \App\Helpers\Parser::toLocale(
                old('farms', $old['farms'] ?? null)
                    ? ($non_bop->sum('price') ?? 0)
                    : ($non_bop->sum('total_price') ?? 0)
            )
        }}</span></p>
    </div>
    <table id="datatableNonBOP" class="table table-bordered" style="margin: 0 0 !important;">
        <thead>
            <tr class="bg-light text-center">
                <th>No</th>
                <th>ID Biaya</th>
                <th>Lokasi</th>
                <th>Tanggal</th>
                <th>Sub Kategori</th>
                <th>Catatan</th>
                <th>QTY</th>
                <th>UOM</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($non_bop as $index => $item)
            <tr class="text-center">
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->id_expense }}</td>
                <td>{{ $item->location_name }}</td>
                <td>{{ date('d-M-Y', strtotime($item->created_at)) }}</td>
                <td>{{ $item->sub_category ?? $item->name }}</td>
                <td>{{ $item->notes ?? '-' }}</td>
                <td>{{ \App\Helpers\Parser::toLocale($item->total_qty) ?? '-' }}</td>
                <td>{{ $item->uom ?? '-' }}</td>
                <td>
                    @switch($item->status)
                        @case(1)
                            <div class="badge badge-pill badge-warning">{{ $statusPayment[$item->status] }}</div>
                            @break
                        @case(2)
                            <div class="badge badge-pill badge-success">{{ $statusPayment[$item->status] }}</div>
                            @break
                        @case(3)
                            <div class="badge badge-pill badge-primary">{{ $statusPayment[$item->status] }}</div>
                            @break
                        @default
                            <div class="badge badge-pill badge-danger">{{ $statusPayment[$item->status] }}</div>
                    @endswitch
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
