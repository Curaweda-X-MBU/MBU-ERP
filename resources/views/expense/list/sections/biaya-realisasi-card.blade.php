<style>
    #biaya-realisasi-card {
        display: none;
    }

    #biaya-realisasi-card.show {
        display: flex;
    }
</style>

<div class="card" id="biaya-realisasi-card">
    <div class="card-header">
        <div style="display: flex; width: 100%; align-items: center; justify-content: space-between;">
            <div class="card-title">Rincian Realisasi</div>
            <div>
                @if($data->realization_docs)
                <a href="{{ route('file.show') . '?filename=' . $data->realization_docs }}" target="_blank" class="btn btn-primary">
                    <i data-feather="file-text" class="mr-50"></i>
                    Dokumen
                </a>
                @else
                <button class="btn btn-secondary" disabled>
                    <i data-feather="x-circle" class="mr-50"></i>
                    Dokumen
                </button>
                @endif
            </div>
        </div>
    </div>
    <div class="card-body">
        <section id="collapsible">
            <div class="row">
                <div class="col-sm-12">
                    <div class=" collapse-icon">
                        <div class=" p-0">
                            <div class="collapse-default">
                                {{-- COLLAPSE TABLE BIAYA UTAMA --}}
                                <div class="card mb-1">
                                    <div id="heading-collapse-realization-biaya-utama" class="card-header color-header success collapsed" data-toggle="collapse" role="button" data-target="#collapse-realization-biaya-utama" aria-expanded="true" aria-controls="collapse-realization-biaya-utama">
                                        <span class="lead collapse-title">Biaya Utama</span>
                                    </div>
                                    <div id="collapse-realization-biaya-utama" role="tabpanel" aria-labelledby="heading-collapse-realization-biaya-utama" class="collapse show" aria-expanded="true">
                                        <div class="card-body p-2">
                                            <div class="col-12">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered w-100">
                                                        <thead>
                                                            <th>No</th>
                                                            <th>Supplier</th>
                                                            <th>Non Stock</th>
                                                            <th>Qty Per Kandang</th>
                                                            <th>Total Qty</th>
                                                            <th>UOM</th>
                                                            <th>Harga Per Kandang</th>
                                                            <th>Total Biaya</th>
                                                            <th>Catatan</th>
                                                        </thead>
                                                        <tbody>
                                                            @if (count($data->expense_main_prices) > 0)
                                                                @foreach ($data->expense_main_prices as $index => $item)
                                                                    <tr>
                                                                        <td>{{  $index + 1 }}</td>
                                                                        <td>{{ $item->supplier->name ?? '-' }}</td>
                                                                        <td>{{ $item->nonstock->name ?? '-' }}</td>
                                                                        <td>{{ \App\Helpers\Parser::trimLocale($item->expense_realization->qty_per_kandang) }}</td>
                                                                        <td>{{ \App\Helpers\Parser::trimLocale($item->expense_realization->qty) }}</td>
                                                                        <td>{{ $item->nonstock->uom->name ?? '-' }}</td>
                                                                        <td>{{ \App\Helpers\Parser::toLocale($item->expense_realization->price_per_kandang) }}</td>
                                                                        <td>{{ \App\Helpers\Parser::toLocale($item->expense_realization->price) }}</td>
                                                                        <td>{{ $item->notes }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            @else
                                                            <tr>
                                                                <td class="text-center" colspan="7">Tidak ada data biaya Utama</td>
                                                            </tr>
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- COLLAPSE TABLE BIAYA LAINNYA --}}
                                <div class="card mb-1">
                                    <div id="heading-coallpse-realization-biaya-lainnya" class="card-header color-header success collapsed" data-toggle="collapse" role="button" data-target="#collapse-realizaiton-biaya-lainnya" aria-expanded="true" aria-controls="collapse-realization-biaya-lainnya">
                                        <span class="lead collapse-title">Biaya Lainnya</span>
                                    </div>
                                    <div id="collapse-realization-biaya-lainnya" role="tabpanel" aria-labelledby="heading-collapse-realization-biaya-lainnya" class="collapse show" aria-expanded="true">
                                        <div class="card-body p-2">
                                            <div class="col-12">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered w-100">
                                                        <thead>
                                                            <th>No</th>
                                                            <th>Nama Biaya</th>
                                                            <th>Nominal Biaya</th>
                                                            <th>Catatan</th>
                                                        </thead>
                                                        <tbody>
                                                            @if (count($data->expense_addit_prices) > 0)
                                                                @foreach ($data->expense_addit_prices as $index => $item)
                                                                    <tr>
                                                                        <td>{{  $index + 1 }}</td>
                                                                        <td>{{ $item->name }}</td>
                                                                        <td>{{ \App\Helpers\Parser::toLocale($item->expense_realization->price) }}</td>
                                                                        <td>{{ $item->notes }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            @else
                                                            <tr>
                                                                <td class="text-center" colspan="4">Tidak ada data biaya lainnya</td>
                                                            </tr>
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
