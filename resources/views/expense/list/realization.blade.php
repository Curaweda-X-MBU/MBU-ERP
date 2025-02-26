@extends('templates.main')
@section('title', $title)
@section('content')

<style>
#transparentFileUpload {
    opacity: 0;
    position:absolute;
    inset: 0;
}
</style>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{$title}}</h4>
            </div>
            <div class="card-body">
                <form id="expense-realization-form" class="form-horizontal" method="post" action="{{ route('expense.list.realization', $data->expense_id) }}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    {{-- Top Section | Document Input --}}
                    <div class="row">
                        <div class="col-md-3 mt-1">
                            <label for="realization_docs">Dokumen Pembelian<i class="text-danger">*</i></label>
                            <div class="input-group">
                                    @php
                                        $path = $data->realization_docs ?? '';
                                        $parts = explode('/', $path);
                                        $filename = end($parts);
                                    @endphp
                                    <input type="text" id="fileName" placeholder="Upload" class="form-control" tabindex="-1" value="{{ @$data->realization_docs ? $filename : '' }}" {{ @$data->expense_status == array_key_last(\App\Constants::EXPENSE_STATUS) ? 'disabled' : (@$data->realization_docs && @$data->realization_docs !== '' ? '' : 'required') }}>
                                    <input type="file" id="transparentFileUpload" name="realization_docs" {{ @$data->expense_status == array_key_last(\App\Constants::EXPENSE_STATUS) ? 'disabled' : (@$data->realization_docs && @$data->realization_docs !== '' ? '' : 'required') }}>
                                <div class="input-group-append">
                                    <span class="input-group-text"> <i data-feather="upload"></i></span>
                                </div>
                            </div>
                            <span class="text-secondary" style="font-size: 0.9em">Max. 5 MB</span>
                        </div>
                    </div>
                    {{-- Mid Section | Expense Information --}}
                    <div class="table-responsive mt-2">
                        <div class="row bg-primary d-flex justify-content-center align-items-center py-1 text-white px-2">
                            <p class="col-md-6 mb-0">Informasi Pengajuan</p>
                            <p class="col-md-6 mb-0 text-right">{{ $data->id_expense }} | {{ $data->po_number }}</p>
                        </div>
                        <table class="table table-bordered">
                            <thead>
                                <tr class="bg-light">
                                    <th>Lokasi</th>
                                    <th>Kandang</th>
                                    <th class="text-center">Kategori</th>
                                    <th class="text-center">Tanggal Transaksi</th>
                                    <th class="text-center">Dokumen</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $data->location->name }}</td>
                                    <td>{{ $data->expense_kandang ? $data->expense_kandang->sortBy('kandang_id')->pluck('kandang.name')->join(', ') : '-' }}</td>
                                    <td class="text-center">{{ \App\Constants::EXPENSE_CATEGORY[$data->category] }}</td>
                                    <td class="text-center">{{ date('d-M-Y', strtotime($data->transaction_date)) }}</td>
                                    <td class="text-center">
                                        @if ($data->bill_docs)
                                            <a class="p-0" href="{{ route('file.show', ['filename' => $data->bill_docs]) }}" target="_blank">
                                                <i data-feather='file-text' class="mr-50"></i>
                                                <span>Lihat Dokumen Tagihan</span>
                                            </a>
                                        @else
                                            <span>-</span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    {{-- Main Section | Expense Main Prices --}}
                    <div class="table-responsive mt-2">
                        <div class="row bg-primary d-flex justify-content-center align-items-center py-1 text-white px-2">
                            <p class="col-md-6 mb-0">Biaya Utama</p>
                            <p class="col-md-6 mb-0 text-right">Diajukan | Rp {{ \App\Helpers\Parser::toLocale($data->expense_main_prices->sum('price')) }}</p>
                        </div>
                        <table class="table table-bordered" id="realization_main_prices_table">
                            <thead>
                                <tr class="bg-light">
                                    <th>#</th>
                                    <th>Non Stock</th>
                                    <th class="text-center">Total QTY</th>
                                    <th class="text-center">UOM</th>
                                    <th class="text-center">Total Harga (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $total_main_prices = 0;
                                @endphp
                                @foreach ($main as $index => $mp)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $mp->expense_main_price->nonstock->name }}</td>
                                    <td><input name="realization_main_prices[{{ $mp->expense_realization_id }}][qty]" type="text" class="form-control numeral-mask" placeholder="{{ \App\Helpers\Parser::trimLocale($mp->expense_main_price->qty) }}" value="{{ $mp->qty ?: '' }}"></td>
                                    <td>{{ $mp->expense_main_price->nonstock->uom->name }}</td>
                                    <td><input name="realization_main_prices[{{ $mp->expense_realization_id }}][price]" type="text" class="form-control numeral-mask" placeholder="{{ \App\Helpers\Parser::toLocale($mp->expense_main_price->price) }}" value="{{ $mp->price ?: '' }}"></td>
                                </tr>
                                @php
                                    $total_main_prices += $mp->price ?? 0;
                                @endphp
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="font-weight-bolder">
                                    <td>Total</td>
                                    <td class="text-right" id="total_main_prices" colspan="4">{{ \App\Helpers\Parser::toLocale($total_main_prices) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    {{-- Main Section | Expense Addit Prices --}}
                    <div class="table-responsive mt-2">
                        <div class="row bg-primary d-flex justify-content-center align-items-center py-1 text-white px-2">
                            <p class="col-md-6 mb-0">Biaya Lainnya</p>
                            <p class="col-md-6 mb-0 text-right">Diajukan | Rp {{ \App\Helpers\Parser::toLocale($data->expense_addit_prices->sum('price')) }}</p>
                        </div>
                        <table class="table table-bordered" id="realization_addit_prices_table">
                            <thead>
                                <tr class="bg-light">
                                    <th>#</th>
                                    <th>Nama Biaya</th>
                                    <th class="text-center">Total Harga (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $total_addit_prices = 0;
                                @endphp
                                @if (! empty($addit))
                                    @foreach ($addit as $index => $ap)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $ap->expense_addit_price->name }}</td>
                                        <td><input name="realization_addit_prices[{{ $ap->expense_realization_id }}][price]" type="text" class="form-control numeral-mask" placeholder="{{ \App\Helpers\Parser::toLocale($ap->expense_addit_price->price) }}" value="{{ $ap->price ?: '' }}"></td>
                                    </tr>
                                    @php
                                        $total_addit_prices += $ap->price ?? 0;
                                    @endphp
                                    @endforeach
                                @else
                                <tr>
                                    <td colspan="3">Tidak ada data</td>
                                </tr>
                                @endif
                            </tbody>
                            <tfoot>
                                <tr class="font-weight-bolder">
                                    <td>Total</td>
                                    <td class="text-right" id="total_addit_prices" colspan="2">{{ \App\Helpers\Parser::toLocale($total_addit_prices) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="col-12 mt-3">
                        <a href="{{ route('expense.list.detail', ['expense' => $data->expense_id, 'page' => 'realization']) }}" class="btn btn-outline-warning waves-effect">Batal</a>
                        <button id="submitForm" type="submit" class="btn btn-primary waves-effect waves-float waves-light">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

 <script src="{{asset('app-assets/vendors/js/forms/cleave/cleave.min.js')}}"></script>
<script>
    $(function() {
        $(document).on('change', '#transparentFileUpload', function() {
            $(this).siblings('#fileName').val($(this).val().split('\\').pop())
        });
        initNumeralMask('.numeral-mask');

        const $mainPricesTable = $('#realization_main_prices_table');
        $mainPricesTable.on('change', 'input[name*="price"]', function() {
            const total = $mainPricesTable.find('input[name$="price]"]').get().reduce((a, b) => intVal(a) + intVal($(b).val()), 0);
            $('#total_main_prices').text(parseNumToLocale(total));
        });
    });
</script>

@endsection
