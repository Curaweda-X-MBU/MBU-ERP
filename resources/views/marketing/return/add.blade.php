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

<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/animate/animate.css')}}" />
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/sweetalert2.min.css')}}" />

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{$title}}</h4>
            </div>
            <div class="card-body">
                <form class="form-horizontal" method="post" action="" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="row row-cols-2 row-cols-md-4">
                        <!-- Nama Pelanggan -->
                        <div class="col-md-2 mt-1">
                            <label for="customer_id" class="form-label">Nama Pelanggan<i class="text-danger">*</i></label>
                            <select name="customer_id" id="customer_id" class="form-control" disabled>
                                <option value="1" selected="selected">Abd. Muis</option>
                            </select>
                        </div>
                        <!-- Tanggal Penjualan -->
                        <div class="col-md-2 mt-1">
                            <label for="sold_at" class="form-label">Tanggal Penjualan<i class="text-danger">*</i></label>
                            <input id="sold_at" name="sold_at" type="text" class="form-control" value="16-Des-2024" disabled>
                        </div>
                        <!-- Tanggal Retur -->
                        <div class="col-md-2 mt-1">
                            <label for="return_at" class="form-label">Tanggal Retur<i class="text-danger">*</i></label>
                            <input id="return_at" name="return_at" type="date" class="form-control flatpickr-basic" placeholder="Pilih Tanggal" required>
                        </div>
                        <!-- Status -->
                        <div class="col-md-2 mt-1">
                            <label for="marketing_status" class="form-label">Status<i class="text-danger">*</i></label>
                            <input id="marketing_status" value="Diajukan" name="marketing_status" type="text" class="form-control" disabled>
                        </div>
                        <!-- Referensi Dokumen -->
                        <div class="col-md-2 mt-1">
                            <label for="doc_reference" class="form-label">Referensi Dokumen</label>
                            <div class="input-group">
                                <input type="text" id="fileName" placeholder="Upload" class="form-control">
                                <input type="file" id="transparentFileUpload" name="doc_reference">
                                <div class="input-group-append">
                                    <span class="input-group-text"> <i data-feather="upload"></i> </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- START: Table-->
                    <div class="table-responsive mt-3">
                        <table id="marketing-return-repeater-1" class="table w-100">
                            <thead>
                                <tr class="text-center">
                                    <th>Kandang/Hatchery<i class="text-danger">*</i></th>
                                    <th class="col-2">Nama Produk<i class="text-danger">*</i></th>
                                    <th>Harga Satuan (Rp)<i class="text-danger">*</i></th>
                                    <th>Bobot Avg (Kg)<i class="text-danger">*</i></th>
                                    <th>Qty<i class="text-danger">*</i></th>
                                    <th>Total Bobot (Kg)</th>
                                    <th>Total Penjualan (Rp)</th>
                                    <th>
                                        <button class="btn btn-sm btn-icon btn-primary" type="button" data-repeater-create title="Tambah Kandang">
                                            <i data-feather="plus"></i>
                                        </button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody data-repeater-list="marketing_returns">
                                <tr class="text-center" data-repeater-item>
                                    <td class="pt-2 pb-3">
                                        <select name="kandang_id" class="form-control marketing_kandang_select" disabled>
                                            @if(old('marketing_kandang_id') && old('marketing_kandang_id'))
                                                <option value="{{ old('marketing_kandang_id') }}" selected="selected">{{ old('marketing_kandang_name') }}</option>
                                            @endif
                                        </select>
                                        @if ($errors->has('marketing_kandang_id'))
                                            <span class="text-danger small">{{ $errors->first('marketing_kandang_id') }}</span>
                                        @endif
                                    </td>
                                    <td class="pt-2 pb-3 position-relative">
                                        <select name="marketing_product_id" class="form-control marketing_product_select" disabled>
                                            @if(old('marketing_product_id') && old('marketing_product_id'))
                                                <option value="{{ old('marketing_product_id') }}" selected="selected">{{ old('marketing_product_name') }}</option>
                                            @endif
                                        </select>
                                        <small class="form-text text-muted text-right position-absolute pr-1" style="right: 0; font-size: 80%;">Current Stock: <span id="current_stock">0000</span></small>
                                        @if ($errors->has('marketing_product_id'))
                                            <span class="text-danger small">{{ $errors->first('marketing_product_id') }}</span>
                                        @endif
                                    </td>
                                    <td class="pt-2 pb-3">
                                        <input type="text" id="price" class="form-control numeral-mask" disabled>
                                    </td>
                                    <td class="pt-2 pb-3">
                                        <input type="text" id="weight_avg" class="form-control numeral-mask" disabled>
                                    </td>
                                    <td class="pt-2 pb-3 position-relative">
                                        <input type="number" name="qty" id="qty" max="0" hidden>
                                        <input type="text" name="qty_mask" id="qty_mask" class="form-control numeral-mask" placeholder="Qty" required>
                                        <span id="invalid_qty" class="text-danger text-right small position-absolute pr-1" style="right: 0; font-size: 80%; opacity: 0;">Melebihi stock</span>
                                    </td>
                                    <td class="pt-2 pb-3">
                                        <input type="text" id="weight_total" class="form-control" value="0,00" disabled>
                                    </td>
                                    <td class="pt-2 pb-3">
                                        <input type="text" id="price_total" class="form-control" value="0,00" disabled>
                                    </td>
                                    <td class="pt-2 pb-3">
                                        <button class="btn btn-sm btn-icon btn-danger" data-repeater-delete type="button" title="Hapus Kandang">
                                            <i data-feather="x"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- END: Table-->

                    <hr>

                        <div class="row">
                            <!-- BEGIN: Catatan dan Nama Sales -->
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-8 mt-1">
                                        <label for="catatan" class="form-label">Catatan :</label>
                                        <textarea id="catatan" class="form-control" rows="3"></textarea>
                                    </div>

                                    <div class="col-md-4 mt-1">
                                        <label for="namaSales" class="form-label">Nama Sales</label>
                                        <select name="namaSales" id="namaSales" class="form-control" disabled>
                                            <option value="1" selected="selected">Ulfah</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            {{-- END: Catatan dan Nama Sales --}}

                            <!-- BEGIN: Total -->
                            <div class="row col-md-6 my-1" id="marketing-addit-prices-repeater-1" style="row-gap: 1em;">
                                <div class="row col-12 text-right align-items-center" style="row-gap: 0.5em;">
                                    <div class="col-5"> <span>Total Sebelum Pajak:</span> </div>
                                    <div class="col-5"> Rp. <span id="total_sebelum_pajak">0,00</span> </div>
                                    <div class="col-5"> <span>Pajak:</span> </div>
                                    <div class="col-5 input-group">
                                        <input name="tax" type="number" min="0" max="100" class="form-control" value="0">
                                        <div class="input-group-append"><span class="input-group-text">%</span></div>
                                    </div>
                                    <div class="col-5"> <span>Diskon:</span> </div>
                                    <div class="col-5 input-group">
                                        <div class="input-group-prepend"><span class="input-group-text">Rp.</span></div>
                                        <input name="discount" type="text" class="form-control numeral-mask" value="0">
                                    </div>
                                    <div class="offset-5 col-5"> <hr class="border-bottom"> </div>
                                    <div class="col-5"> <span>Total Setelah Pajak dan Diskon:</span> </div>
                                    <div class="col-5"> Rp. <span id="total_setelah_pajak" class="font-weight-bolder">0,00</span> </div>
                                </div>
                                {{-- BEGIN: Tambah biaya lainnya --}}
                                <div class="row col-12 text-right align-items-center" data-repeater-list="marketing_addit_prices" style="row-gap: 0.5em;">
                                    <div class="col-12 text-left">
                                        <button type="button" class="btn btn-primary btn-sm" data-repeater-create>
                                            <span>Tambah Biaya Lainnya</span>
                                            <span><i data-feather="plus"></i></span>
                                        </button>
                                    </div>
                                    <div class="row align-items-center" data-repeater-item>
                                        <div class="col-5"> <input name="item" type="text" class="form-control" placeholder="Item"> </div>
                                        <div class="col-5 input-group">
                                            <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                                            <input type="text" name="price" id ="addit_price" class="form-control numeral-mask" placeholder="Harga">
                                        </div>
                                        <div class="col-2 text-left">
                                            <button class="btn btn-sm btn-icon btn-danger" data-repeater-delete type="button" title="Hapus Produk">
                                                <i data-feather="x"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="row col-12 text-right align-items-center" data-repeater-list="marketing_addit_prices" style="row-gap: 0.5em;">
                                    <div class="offset-5 col-5"> <hr class="border-bottom" style="border-color: black;"> </div>
                                    <div class="col-5"> <span>Total Piutang Penjualan:</span> </div>
                                    <div class="col-5"> Rp. <span id="total_piutang" class="font-weight-bolder" style="font-size: 1.2em;">0,00</span> </div>
                                </div>
                                {{-- START: Grand Total --}}
                                {{-- END: Grand Total --}}
                                <div class="row col-12 text-right align-items-center" data-repeater-list="marketing_addit_prices" style="row-gap: 0.5em;">
                                {{-- END: Tambah biaya lainnya --}}
                            </div>
                            {{-- END: Total --}}
                        </div>
                        {{-- button --}}
                        <div class="col-12 mt-1">
                            <a href="{{ route('marketing.return.index') }}" class="btn btn-outline-warning waves-effect">Batal</a>
                            <button id="submitForm" type="submit" class="btn btn-primary mr-1 waves-effect waves-float waves-light">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="{{asset('app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/forms/repeater/jquery.repeater.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/forms/cleave/cleave.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/extensions/sweetalert2.all.min.js')}}"></script>
<script>
    $(document).ready(function() {
        $('#transparentFileUpload').on('change', function() {
            $('#fileName').val($('#transparentFileUpload').val().split('\\').pop())
        })
    })

    // ? START :: SET VALUE :: QTY & CURRENT STOCK
    function setQtyStock($this, reset) {
        let qty;
        if (reset) {
            qty = 0;
        } else {
            const data = $this.select2('data')[0];
            qty = data && data.qty ? data.qty : 0;
        }
        const value = parseNumToLocale(qty);
        const $rowScope = $this.closest('tr');

        $rowScope.find('#current_stock').text(value);
        $rowScope.find('#qty').attr('max', qty);
    }
    // ? END :: SET VALUE :: QTY & CURRENT STOCK


    // ? START :: FLATPICKR
    const dateOpt = { dateFormat: 'd-M-Y' };
        $('.flatpickr-basic').flatpickr(dateOpt);
    // ? END :: FLATPICKR

    // ? START :: SET VALUE :: QTY & CURRENT STOCK
    function setQtyStock($this, reset) {
        let qty;
        if (reset) {
            qty = 0;
        } else {
            const data = $this.select2('data')[0];
            qty = data && data.qty ? data.qty : 0;
        }
        const value = parseNumToLocale(qty);
        const $rowScope = $this.closest('tr');

        $rowScope.find('#current_stock').text(value);
        $rowScope.find('#qty').attr('max', qty);
    }
    // ? END :: SET VALUE :: QTY & CURRENT STOCK

    $(function () {
        function calculateTotalPerRow() {
            $('#marketing-return-repeater-1').on('input', '[data-repeater-item] input[name*="qty_mask"]', function () {
                const $row = $(this).closest('tr');

                const $qtyInput = $row.find('input[name*="qty_mask"]');
                const $price = $row.find('#price');
                const $weightAvg = $row.find('#weight_avg');
                const $weightTotalInput = $row.find('#weight_total');
                const $priceTotalInput = $row.find('#price_total');
                const $totalSebelumPajak = $('#total_sebelum_pajak');

                const qty = parseLocaleToNum($qtyInput.val());
                const weightAvg = parseLocaleToNum($weightAvg.val());
                const price = parseLocaleToNum($price.val());

                const weightTotal = qty * weightAvg;
                const priceTotal = weightTotal * price;

                $weightTotalInput.val(parseNumToLocale(weightTotal));
                $priceTotalInput.val(parseNumToLocale(priceTotal));

                setTimeout(function(){
                    const priceAllRow = $('#marketing-return-repeater-1 #price_total').get().reduce(function(acc, elem) {
                        const value = parseLocaleToNum($(elem).val());
                        return acc + value;
                    }, 0);
                    $totalSebelumPajak.text(parseNumToLocale(priceAllRow)).trigger('change');
                }, 0);
            })
        }

        // ? START :: REPEATER :: MARKETING RETURN
        const OptMarketingReturn = {
            initEmpty: true,
            show: function() {
                const $rowScope = $(this);
                $rowScope.slideDown();
                // FEATHER ICON
                if (feather) {
                    feather.replace({ width: 14, height: 14 });
                }
                initNumeralMask('.numeral-mask');
                calculateTotalPerRow();
            },
            hide: function(deleteElement) {
                confirmDelete($(this), deleteElement);
            },
        };
        // ? END :: REPEATER :: MARKETING RETURN

        // ? START :: REPEATER :: MARKETING ADDIT PRICES
        const optMarketingAdditPrices = {
            show: function() {
                $(this).slideDown();
                // FEATHER ICONS
                if (feather) {
                    feather.replace({ width: 14, height: 14 });
                }
                initNumeralMask('.numeral-mask');
            },
            hide: function(deleteElement) {
                confirmDelete($(this), deleteElement);
            },
        };
        // ? END :: REPEATER :: MARKETING ADDIT PRICES

        // ? START :: REPEATER :: INITIALIZE
        $('#marketing-return-repeater-1').repeater(OptMarketingReturn);
        $('#marketing-addit-prices-repeater-1').repeater(optMarketingAdditPrices);
        calculateTotalPerRow();
        // ? END :: REPEATER :: INITIALIZE
    });
</script>

@endsection
