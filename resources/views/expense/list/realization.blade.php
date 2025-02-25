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
                            <label for="realization_docs">Dokumen Pembelian</label>
                            <div class="input-group">
                                    <input type="text" id="fileName" placeholder="Upload" class="form-control" tabindex="-1" value="{{ @$data->realization_docs }}" {{ @$data->expense_status == array_key_last(\App\Constants::EXPENSE_STATUS) ? 'disabled' : 'required' }}>
                                    <input type="file" id="transparentFileUpload" name="realization_docs" {{ @$data->expense_status == array_key_last(\App\Constants::EXPENSE_STATUS) ? 'disabled' : 'required' }}>
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
                        <table class="table table-bordered">
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
                                @foreach ($main as $index => $mp)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $mp->expense_main_price->nonstock->name }}</td>
                                    <td><input type="text" class="form-control numeral-mask" placeholder="{{ $mp->expense_main_price->qty }}"></td>
                                    <td>{{ $mp->expense_main_price->nonstock->uom->name }}</td>
                                    <td><input type="text" class="form-control numeral-mask" placeholder="{{ $mp->expense_main_price->price }}"></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{-- Main Section | Expense Addit Prices --}}
                    <div class="table-responsive mt-2">
                        <div class="row bg-primary d-flex justify-content-center align-items-center py-1 text-white px-2">
                            <p class="col-md-6 mb-0">Biaya Lainnya</p>
                            <p class="col-md-6 mb-0 text-right">Diajukan | Rp {{ \App\Helpers\Parser::toLocale($data->expense_addit_prices->sum('price')) }}</p>
                        </div>
                        <table class="table table-bordered">
                            <thead>
                                <tr class="bg-light">
                                    <th>#</th>
                                    <th>Nama Biaya</th>
                                    <th class="text-center">Total Harga (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (! empty($addit))
                                    @foreach ($addit as $index => $ap)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $ap->expense_addit_price->name }}</td>
                                        <td><input type="text" class="form-control numeral-mask" placeholder="{{ $ap->expense_addit_price->price }}"></td>
                                    </tr>
                                    @endforeach
                                @else
                                <tr>
                                    <td colspan="3">Tidak ada data</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        $(document).on('change', '#transparentFileUpload', function() {
            $(this).siblings('#fileName').val($(this).val().split('\\').pop())
        });
    });
</script>

@endsection
