<div class="table-responsive mt-2" style="overflow-x: hidden;">
    <div class="row bg-primary d-flex justify-content-center align-items-center py-1 text-white px-2">
        <p class="col-md-6 mb-0">Biaya Operasional (BOP)</p>
        <p class="col-md-6 mb-0 text-right">Total: Rp <span id="total-biaya-bop">{{
            \App\Helpers\Parser::toLocale(
                old('farms', $old['farms'] ?? null)
                    ? ($bop->sum('price') ?? 0)
                    : ($bop->sum('total_price') ?? 0)
            )
        }}</span></p>
    </div>
    <table id="datatableBOP" class="table table-bordered" style="margin: 0 0 !important;">
        <thead>
            <tr class="bg-light text-center">
                <th>No</th>
                <th>ID Biaya</th>
                <th>Tanggal</th>
                <th>Sub Kategori</th>
                <th>Catatan</th>
                <th>QTY</th>
                <th>UOM</th>
                <th>Nominal</th>
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
                <td>{{ date('d-M-Y', strtotime($item->created_at)) }}</td>
                <td>{{ $item->sub_category ?? $item->name }}</td>
                <td>{{ $item->notes ?? '-' }}</td>
                <td>{{ $item->qty ? \App\Helpers\Parser::toLocale($kandang_length > 1 ? $item->total_qty : $item->qty) : '-' }}</td>
                <td>{{ $item->uom ?? '-' }}</td>
                <td>{{ \App\Helpers\Parser::toLocale($kandang_length > 1 ? $item->total_price : $item->price) }}</td>
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
                <td>
                    @if ($kandang_length > 1)
                        <div class="kandang-popover d-none">
                            @foreach ($item->kandangs as $kandang)
                            <div class="badge badge-rounded badge-secondary">{{ $kandang }}</div>
                            @endforeach
                        </div>
                        <button type="button" class="btn badge badge-rounded badge-secondary font-weight-bolder" data-container="body" data-toggle="popover" data-trigger="focus" data-placement="left" data-html="true">
                          Lihat {{ $kandang_length }} Kandang
                        </button>
                    @else
                        <div class="badge badge-rounded badge-secondary">{{ $item->kandangs[0] }}</div>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    $(function() {
        $('[data-toggle="popover"]').popover({
            content: function() {
                return $(this).siblings('.kandang-popover').html();
            }
        });
    });
</script>
