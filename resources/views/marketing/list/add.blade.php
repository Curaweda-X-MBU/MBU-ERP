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
                <form class="form-horizontal" method="post" action="{{ route('marketing.list.add') }}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="row row-cols-2 row-cols-md-4 align-items-baseline">
                        <!-- Nama Pelanggan -->
                        <div class="col-md-2 mt-1">
                            <label for="customer_id" class="form-label">Nama Pelanggan<i class="text-danger">*</i></label>
                            <select name="customer_id" id="customer_id" class="form-control" required>
                            </select>
                        </div>
                        <!-- Tanggal Penjualan -->
                        <div class="col-md-2 mt-1">
                            <label for="sold_at" class="form-label">Tanggal Penjualan<i class="text-danger">*</i></label>
                            <input name="sold_at" id="sold_at" class="form-control flatpickr-basic" aria-desribedby="sold_at" placeholder="Pilih Tanggal" value="{{ now()->format('d-M-Y') }}" required>
                        </div>
                        <!-- Status -->
                        <div class="col-md-2 mt-1">
                            <label for="marketing_status" class="form-label">Status</label>
                            <input id="marketing_status" value="Diajukan" name="marketing_status" type="text" class="form-control" disabled required>
                        </div>
                        <!-- Referensi Dokumen -->
                        <div class="col-md-2 mt-1">
                            <label for="doc_reference" class="form-label">Referensi Dokumen</label>
                            <div class="input-group">
                                <input type="text" id="fileName" placeholder="Upload" class="form-control" tabindex="-1">
                                <input type="file" id="transparentFileUpload" name="doc_reference">
                                <div class="input-group-append">
                                    <span class="input-group-text"> <i data-feather="upload"></i> </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- BEGIN: Table-->
                    <div class="table-responsive mt-3">
                        <table id="marketing-product-repeater-1" class="table w-100">
                            <thead>
                                <tr class="text-center">
                                    <th>Kandang/Hatchery<i class="text-danger">*</i></th>
                                    <th class="col-2">Nama Produk<i class="text-danger">*</i></th>
                                    <th>Harga Satuan (Rp)<i class="text-danger">*</i></th>
                                    <th>Bobot Avg<i class="text-danger">*</i></th>
                                    <th>UOM<i class="text-danger">*</i></th>
                                    <th>Qty<i class="text-danger">*</i></th>
                                    <th>Total Bobot</th>
                                    <th>Total Penjualan (Rp)</th>
                                    <th>
                                        <button class="btn btn-sm btn-icon btn-primary" type="button" data-repeater-create title="Tambah Produk">
                                            <i data-feather="plus"></i>
                                        </button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody data-repeater-list="marketing_products">
                                <tr class="text-center" data-repeater-item>
                                    <td class="pt-2 pb-3">
                                        <select name="kandang_id" class="form-control marketing_kandang_select" required>
                                        </select>
                                    </td>
                                    <td class="pt-2 pb-3 position-relative">
                                        <select name="product_id" class="form-control marketing_product_select" required>
                                            <option disabled selected>Pilih Kandang terlebih dahulu</option>
                                        </select>
                                        <small class="form-text text-muted text-right position-absolute pr-1" style="right: 0; font-size: 80%;">Current Stock: <span id="current_stock">0</span></small>
                                    </td>
                                    <td class="pt-2 pb-3 position-relative">
                                        <input name="price" type="text" class="form-control numeral-mask" placeholder="Harga Satuan (Rp)" required>
                                    </td>
                                    <td class="pt-2 pb-3">
                                        <input name="weight_avg" type="text" class="form-control numeral-mask" placeholder="Bobot Avg (Kg)" required>
                                    </td>
                                    <td class="pt-2 pb-3">
                                        <select name="uom_id" class="form-control uom_select" required>
                                        </select>
                                    </td>
                                    <td class="pt-2 pb-3 position-relative">
                                        <input type="number" name="qty" id="qty" max="0" class="position-absolute" style="opacity: 0; pointer-events: none;" tabindex="-1">
                                        <input type="text" id="qty_mask" class="form-control numeral-mask" placeholder="Qty" required>
                                        <span id="invalid_qty" class="text-danger text-right small position-absolute pr-1" style="right: 0; font-size: 80%; opacity: 0;">Melebihi stock</span>
                                    </td>
                                    <td class="pt-2 pb-3">
                                        <input type="text" id="weight_total" class="form-control" value="0,00" disabled>
                                    </td>
                                    <td class="pt-2 pb-3">
                                        <input type="text" id="price_total" class="form-control" value="0,00" disabled>
                                    </td>
                                    <td class="pt-2 pb-3">
                                        <button class="btn btn-sm btn-icon btn-danger" data-repeater-delete type="button" title="Hapus Produk">
                                            <i data-feather="x"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- END: Table-->

                    <hr class="border-bottom">

                        <div class="row">
                            <!-- BEGIN: Catatan dan Nama Sales -->
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-8 mt-1">
                                        <label for="catatan" class="form-label">Catatan :</label>
                                        <textarea id="catatan" name="notes" class="form-control" rows="3"></textarea>
                                    </div>

                                    <div class="col-md-4 mt-1">
                                        <label for="sales_id" class="form-label">Nama Sales</label>
                                        <select name="sales_id" id="sales_id" class="form-control">
                                            @if(old('sales_id') && old('sales_id'))
                                                <option value="{{ old('sales_id') }}" selected="selected">{{ old('sales_name') }}</option>
                                            @endif
                                        </select>
                                        @if ($errors->has('sales_id'))
                                            <span class="text-danger small">{{ $errors->first('sales_id') }}</span>
                                        @endif
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

                            {{-- button --}}
                        </div>
                    <div class="col-12 mt-1">
                        <a href="{{ route('marketing.list.index') }}" class="btn btn-outline-warning waves-effect">Batal</a>
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
    });

    /* NOTE :
    ----- HELPER FUNCTIONS -----
    */
    function parseValue(value) { return parseFloat(value.replace(/\./g, '').replace(',', '.') || 0); }

    // ? START :: SWAL2 ::  DELETE CONFIRMATION
    function confirmDelete($this, deleteElement) {
        Swal.fire({
            title: 'Hapus data ini?',
            text: 'Data tidak bisa dikembalikan.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Hapus',
            customClass: {
                confirmButton: 'btn btn-danger mr-1',
                cancelButton: 'btn btn-secondary',
            },
            buttonsStyling: false,
        }).then(function(result) {
            if (result.value) {
                $(this).slideUp(deleteElement);
            }
        });
    }
    // ? END :: SWAL2 ::  DELETE CONFIRMATION

    // ? START :: NUMERAL MASK :: INITIALIZE
    function initNumeralMask() {
        $('.numeral-mask').each(function() {
            new Cleave(this, {
                numeral: true,
                numeralDecimalMark: ',', delimiter: '.',
            });
        });
    }
    // ? END :: NUMERAL MASK :: INITIALIZE

    // ? START :: SELECT2 :: INITIALIZE
    function initSelect2($component, placeholder, routePath) {
        $component.select2({
            placeholder: placeholder,
            ajax: {
                url: routePath,
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.map((item) => ({
                            id: item.id,
                            text: item.text,
                            qty: item.qty ? item.qty : 0,
                        }))
                    };
                },
                cache: true,
            },
       });
    }
    // ? END :: SELECT2 :: INITIALIZE

    // ? START :: SET VALUE :: QTY & CURRENT STOCK
    function setQtyStock($this, reset) {
        let qty;
        if (reset) {
            qty = 0;
        } else {
            const data = $this.select2('data')[0];
            qty = data && data.qty ? data.qty : 0;
        }
        const value = qty.toLocaleString('id-ID');
        const $rowScope = $this.closest('tr');

        $rowScope.find('#current_stock').text(value);
        $rowScope.find('#qty').attr('max', qty);
    }
    // ? END :: SET VALUE :: QTY & CURRENT STOCK

    $(function() {
        /* NOTE :
        ----- CALCULATIONS -----
        */
        // ? START :: CALCULATION ::  PRODUCT ROWS
        function calculateTotalPerRow() {
            $('#marketing-product-repeater-1').on('input', '[data-repeater-item] #qty_mask, [data-repeater-item] input[name*="weight_avg"], [data-repeater-item] input[name*="price"]', function () {
                const $row = $(this).closest('tr');

                const $qtyInput = $row.find('#qty_mask');
                const $weightAvgInput = $row.find('input[name*="weight_avg"]');
                const $priceInput = $row.find('input[name*="price"]');
                const $weightTotalInput = $row.find('#weight_total');
                const $priceTotalInput = $row.find('#price_total');
                const $totalSebelumPajak = $('#total_sebelum_pajak');
                const $totalSebelumPajakInput = $('input[name="total_sebelum_pajak"]');

                const qty = parseValue($qtyInput.val());
                const weightAvg = parseValue($weightAvgInput.val());
                const price = parseValue($priceInput.val());

                const weightTotal = qty * weightAvg;
                const priceTotal = weightTotal * price;

                $weightTotalInput.val(weightTotal.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                $priceTotalInput.val(priceTotal.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

                setTimeout(function(){
                    const priceAllRow = $('#marketing-product-repeater-1 #price_total').get().reduce(function(acc, elem) {
                        const value = parseValue($(elem).val());
                        return acc + value;
                    }, 0);
                    $totalSebelumPajak.text(priceAllRow.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })).trigger('change');
                }, 0);
            });
        }
        // ? END :: CALCULATION ::  PRODUCT ROWS

        // ? START :: CALCULATION ::  GRAND TOTAL
        function calculateGrandTotal() {
            $(document).on('change input', '#addit_price, #total_setelah_pajak', function () {
                const totalSetelahPajak = parseValue($('#total_setelah_pajak').text());
                const $totalPiutang = $('#total_piutang');
                setTimeout(function(){
                    const priceAllRow = $('#addit_price').get().reduce(function(acc, elem) {
                        const value = parseValue($(elem).val());
                        return acc + value;
                    }, 0);
                    $totalPiutang.text((totalSetelahPajak + priceAllRow).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                }, 0);
            });
        }
        // ? END :: CALCULATION ::  GRAND TOTAL

        // ? START :: CALCULATION ::  BEFORE TAX
        function calculateBeforeTax() {
            $(document).on('change input', 'input[name="discount"], input[name="tax"], #total_sebelum_pajak', function () {
                const $totalSetelahPajak = $('#total_setelah_pajak');
                const totalSebelumPajak = parseValue($('#total_sebelum_pajak').text());
                const tax = $('input[name="tax"]').val();
                const discount = parseValue($('input[name="discount"]').val());
                let total;
                if (tax > 0) {
                    total = totalSebelumPajak + (totalSebelumPajak * (tax / 100)) - discount;
                } else {
                    total = totalSebelumPajak - discount;
                }
                $totalSetelahPajak.text(total.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })).trigger('change');
            });
        }
        // ? END :: CALCULATION ::  BEFORE TAX

        /* NOTE :
        ----- INITIALIZATIONS -----
        */
        // ? START :: FLATPICKR ::  SOLD AT
        const dateOpt = { dateFormat: 'd-M-Y' };
        $('.flatpickr-basic').flatpickr(dateOpt);
        // ? END :: FLATPICKR ::  SOLD AT

        // ? START :: SELECT2
        const customerIdRoute = '{{ route("data-master.customer.search") }}';
        const salesIdRoute = '{{ route("user-management.user.search") }}';
        initSelect2($('#customer_id'), 'Pilih Pelanggan', customerIdRoute);
        initSelect2($('#sales_id'), 'Pilih Sales', salesIdRoute);
        // ? END :: SELECT2

        // ? START :: REPEATER OPTS :: PRODUCTS
        const optMarketingProduct = {
            initEmpty: true,
            show: function() {
                const $row = $(this);
                $row.slideDown();
                const $uomSelect = $row.find('.uom_select');
                const $marketingKandangSelect = $row.find('.marketing_kandang_select');
                const $marketingProductSelect = $row.find('.marketing_product_select');
                const kandangIdRoute = '{{ route("data-master.kandang.search") }}';
                const uomIdRoute = '{{ route("data-master.uom.search") }}';
                // ? START :: SELECT2 :: REINITIALIZE
                initSelect2($marketingKandangSelect, 'Pilih Kandang', kandangIdRoute);
                initSelect2($uomSelect, 'Pilih Satuan', uomIdRoute);
                // START :: MARKETING PRODUCT + STOCK UPDATE
                $marketingProductSelect.html('<option disabled selected>Pilih Kandang terlebih dahulu</option>');
                $marketingKandangSelect.on('change', function(e) {
                    e.preventDefault();
                    const marketingKandangId = $(this).val();
                    $marketingProductSelect.val(null).trigger('change').html('');

                    if (marketingKandangId) {
                        let productIdRoute = '{{ route("marketing.list.search-product", ['id' => ':id']) }}';
                        productIdRoute = productIdRoute.replace(':id', marketingKandangId);
                        initSelect2($marketingProductSelect, 'Pilih Produk', productIdRoute);
                        setQtyStock($marketingProductSelect, true);
                    } else {
                        $marketingProductSelect.html('<option disabled selected>Pilih Kandang terlebih dahulu</option>');
                        setQtyStock($(this), true);
                    }
                });
                $marketingProductSelect.on('select2:select', function() {
                    setQtyStock($(this));
                });
                // END :: MARKETING PRODUCT + STOCK UPDATE

                // ? START :: VALIDATION :: QTY
                $row.find('#qty_mask').on('input', function() {
                    const val = parseValue($(this).val());
                    const stock = parseValue($row.find('#current_stock').text());
                    $(this).siblings('#qty').val(val);
                    if (val > stock) {
                        $(this).siblings('#invalid_qty').css('opacity', 1);
                    } else {
                        $(this).siblings('#invalid_qty').css('opacity', 0);
                    }
                });
                // ? END :: VALIDATION :: QTY
                // ? END :: SELECT2 :: REINITIALIZE

                // FEATHER ICON
                if (feather) {
                    feather.replace({ width: 14, height: 14 });
                }

                initNumeralMask();
            },
            hide: function(deleteElement) {
                confirmDelete(this, deleteElement);
            },
        };

        // ? END :: REPEATER OPTS :: PRODUCTS

        // ? START :: REPEATER OPTS :: MARKETING ADDIT PRICES
        const optMarketingAdditPrices = {
            initEmpty: true,
            show: function() {
                $(this).slideDown();
                // FEATHER ICONS
                if (feather) {
                    feather.replace({ width: 14, height: 14 });
                }
                initNumeralMask();
            },
            hide: function(deleteElement) {
                confirmDelete(this, deleteElement);
            },
        };
        // ? END :: REPEATER OPTS :: MARKETING ADDIT PRICES

        // ? START :: REPEATER :: INITIALIZE
        $('#marketing-addit-prices-repeater-1').repeater(optMarketingAdditPrices);
        $('#marketing-addit-prices-repeater-1').find('button[data-repeater-create]').trigger('click');
        $('#marketing-product-repeater-1').repeater(optMarketingProduct);
        $('#marketing-product-repeater-1').find('button[data-repeater-create]').trigger('click');
        calculateTotalPerRow();
        calculateBeforeTax();
        calculateGrandTotal();
        // ? END :: REPEATER :: INITIALIZE
    });
</script>

@endsection
