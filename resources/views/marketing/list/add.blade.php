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
                    <div class="row row-cols-2 row-cols-md-4">
                        <!-- Nama Pelanggan -->
                        <div class="col-md-2 mt-1">
                            <label for="customer_id" class="form-label">Nama Pelanggan</label>
                            <select name="customer_id" id="customer_id" class="form-control">
                                @if(old('customer_id') && old('customer_id'))
                                    <option value="{{ old('customer_id') }}" selected="selected">{{ old('customer_name') }}</option>
                                @endif
                            </select>
                            @if ($errors->has('customer_id'))
                                <span class="text-danger small">{{ $errors->first('customer_id') }}</span>
                            @endif
                        </div>
                        <!-- Tanggal Penjualan -->
                        <div class="col-md-2 mt-1">
                            <label for="sold_at" class="form-label">Tanggal Penjualan</label>
                            <input id="sold_at" name="sold_at" class="form-control flatpickr-basic" aria-desribedby="sold_at" placeholder="Pilih Tanggal" required>
                            @if ($errors->has('sold_at'))
                                <span class="text-danger small">{{ $errors->first('sold_at') }}</span>
                            @endif
                        </div>
                        <!-- Status -->
                        <div class="col-md-2 mt-1">
                            <label for="marketing_status" class="form-label">Status</label>
                            <input id="marketing_status" value="Diajukan" name="status" type="text" class="form-control" disabled>
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

                    <!-- BEGIN: Table-->
                    <div class="table-responsive mt-3">
                        <table id="marketing-product-repeater-1" class="table w-100">
                            <thead>
                                <tr class="text-center">
                                    <th>Kandang/Hatchery*</th>
                                    <th class="col-2">Nama Produk*</th>
                                    <th>Harga Satuan (Rp)*</th>
                                    <th>Bobot Avg (Kg)*</th>
                                    <th>Qty*</th>
                                    <th>Total Bobot (Kg)*</th>
                                    <th>Total Penjualan (Rp)*</th>
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
                                        <select name="marketing_kandang_id" class="form-control marketing_kandang_select">
                                            @if(old('marketing_kandang_id') && old('marketing_kandang_id'))
                                                <option value="{{ old('marketing_kandang_id') }}" selected="selected">{{ old('marketing_kandang_name') }}</option>
                                            @endif
                                        </select>
                                        @if ($errors->has('marketing_kandang_id'))
                                            <span class="text-danger small">{{ $errors->first('marketing_kandang_id') }}</span>
                                        @endif
                                    </td>
                                    <td class="pt-2 pb-3 position-relative">
                                        <select name="marketing_product_id" class="form-control marketing_product_select">
                                            @if(old('marketing_product_id') && old('marketing_product_id'))
                                                <option value="{{ old('marketing_product_id') }}" selected="selected">{{ old('marketing_product_name') }}</option>
                                            @endif
                                        </select>
                                        <small class="form-text text-muted text-right position-absolute pr-1" style="right: 0; font-size: 65%;">Current Stock: 0000</small>
                                        @if ($errors->has('marketing_product_id'))
                                            <span class="text-danger small">{{ $errors->first('marketing_product_id') }}</span>
                                        @endif
                                    </td>
                                    <td class="pt-2 pb-3 position-relative">
                                        <input type="text" name="price" class="form-control numeral-mask" placeholder="Harga Satuan (Rp)">
                                        <small class="form-text text-muted text-right position-absolute pr-1" style="right: 0; font-size: 65%;">Actual Price: 0000</small>
                                    </td>
                                    <td class="pt-2 pb-3">
                                        <input type="text" name="weight_avg" class="form-control numeral-mask" placeholder="Bobot Avg (Kg)">
                                    </td>
                                    <td class="pt-2 pb-3">
                                        <input type="text" name="qty" class="form-control numeral-mask" placeholder="Qty">
                                    </td>
                                    <td class="pt-2 pb-3">
                                        <input type="text" name="weight_total" class="form-control text-center" value="" disabled>
                                    </td>
                                    <td class="pt-2 pb-3">
                                        <input type="text" name="price_total" class="form-control text-right" value="" disabled>
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
                                        <textarea id="catatan" class="form-control" rows="3"></textarea>
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
                                    <div class="col-5"> <span>Rp. 70,000,000.00</span> </div>
                                    <div class="col-5"> <span>Pajak:</span> </div>
                                    <div class="col-5 input-group">
                                        <input type="number" min="0" max="100" class="form-control" value="0">
                                        <div class="input-group-append"><span class="input-group-text">%</span></div>
                                    </div>
                                    <div class="col-5"> <span>Diskon:</span> </div>
                                    <div class="col-5 input-group">
                                        <div class="input-group-prepend"><span class="input-group-text">Rp.</span></div>
                                        <input type="text" class="form-control numeral-mask" value="0">
                                    </div>
                                    <div class="offset-5 col-5"> <hr class="border-bottom"> </div>
                                    <div class="col-5"> <span>Total Setelah Pajak dan Diskon:</span> </div>
                                    <div class="col-5"> <span class="font-weight-bolder">Rp. 70,000,000.00</span> </div>
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
                                        <div class="col-5"> <input type="text" class="form-control" placeholder="Item"> </div>
                                        <div class="col-5 input-group">
                                            <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                                            <input type="text" class="form-control numeral-mask" placeholder="Harga">
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
                                    <div class="col-5"> <span class="font-weight-bolder" style="font-size: 1.2em;">Rp. 120,000,000.00</span> </div>
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
    })

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
    initNumeralMask();
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
                        results: data
                    };
                },
                cache: true
            }
        });
    }
    // ? END :: SELECT2 :: INITIALIZE

    $(function() {
        // ? START :: CALCULATION ::  PRODUCT ROWS
        $('[data-repeater-list]').on('input', '[data-repeater-item] input[name*="qty"], [data-repeater-item] input[name*="weight_avg"], [data-repeater-item] input[name*="price"]', function () {
            // Get the current row
            const $row = $(this).closest('tr');

            // Get the input fields within the current row
            const $qtyInput = $row.find('input[name*="qty"]');
            const $weightAvgInput = $row.find('input[name*="weight_avg"]');
            const $priceInput = $row.find('input[name*="price"]');
            const $weightTotalInput = $row.find('input[name*="weight_total"]');
            const $priceTotalInput = $row.find('input[name*="price_total"]');

            // Parse the values (strip commas or formatting if necessary)
            function parseValue(value) { return parseFloat(value.replace(/\./g, '').replace(',', '.') || 0); }
            const qty = parseValue($qtyInput.val());
            const weightAvg = parseValue($weightAvgInput.val());
            const price = parseValue($priceInput.val());

            // Perform calculations
            const weightTotal = qty * weightAvg;
            const priceTotal = weightTotal * price;

            // Update the disabled input fields with localized formatting
            $weightTotalInput.val(weightTotal.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            $priceTotalInput.val(priceTotal.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        });
        // ? END :: CALCULATION ::  PRODUCT ROWS

        /*
        ----- FORM INPUT DATAS -----
        */
        // ? START :: SELECT2 :: CUSTOMER ID
        const customerIdRoute = '{{ route("user-management.user.search") }}';
        initSelect2($('#customer_id'), 'Pilih Pelanggan', customerIdRoute);

        var oldValueCustomer = "{{ old('customer_id') }}";
        if (oldValueCustomer) {
            var oldNameCustomer = "{{ old('customer_name') }}";
            if (oldNameCustomer) {
                var newOption = new Option(oldNameCustomer, oldValueCustomer, true, true);
                $('#customer_id').append(newOption).trigger('change');
            }
        }

        @if ($errors->has('customer_id'))
            $('#customer_id').next('.select2-container').find('.select2-selection').addClass('is-invalid');
        @endif
        // ? END :: SELECT2 :: CUSTOMER ID

        // ? START :: SELECT2 :: SALES ID
        const salesIdRoute = '{{ route("user-management.user.search") }}';
        initSelect2($('#sales_id'), 'Pilih Sales', salesIdRoute);

        var oldValueSales = "{{ old('sales_id') }}";
        if (oldValueSales) {
            var oldNameSales = "{{ old('sales_name') }}";
            if (oldNameSales) {
                var newOption = new Option(oldNameSales, oldValueSales, true, true);
                $('#sales_id').append(newOption).trigger('change');
            }
        }

        @if ($errors->has('sales_id'))
            $('#sales_id').next('.select2-container').find('.select2-selection').addClass('is-invalid');
        @endif
        // ? END :: SELECT2 :: SALES ID

        // ? START :: FLATPICKR ::  SOLD AT
        const dateOpt = { dateFormat: 'd-M-Y' };
        $('.flatpickr-basic').flatpickr(dateOpt);
        // ? END :: FLATPICKR ::  SOLD AT

        /*
        ----- PRODUCTS INITIALIZE -----
        */

        // ? START :: SELECT2 ::  MARKETING KANDANG
        var kandangIdRoute = '{{ route("data-master.kandang.search") }}';
        initSelect2($('.marketing_kandang_select'), 'Pilih Kandang', kandangIdRoute);
        // ? END :: SELECT2 ::  MARKETING KANDANG

        // ? START :: SELECT2 :: MARKETING PRODUCT
        $('.marketing_kandang_select').on('change', function() {
            const marketingKandangId = $(this).val();
            const qryParam = marketingKandangId ? `?product_category_id=${marketingKandangId}` : '';

            const productIdRoute = '{{ route("data-master.product.search") }}';
            initSelect2($('.marketing_product_select'), 'Pilih Produk', productIdRoute + qryParam);
        });
        // ? END :: SELECT2 :: MARKETING PRODUCT

        // ? START :: REPEATER :: PRODUCTS
        const optMarketingProduct = {
            show: function() {
                const $rowScope = $(this);
                $rowScope.slideDown();

                // ? START :: SELECT2 :: MARKETING KANDANG
                $rowScope.find('.select2-container').remove();
                initSelect2($('.marketing_kandang_select'), 'Pilih Kandang', kandangIdRoute);
                    // ? START :: SELECT2 :: MARKETING PRODUCT
                    $(this).find('.marketing_kandang_select').on('change', function() {
                        const marketingKandangId = $(this).val();
                        const qryParam = marketingKandangId ? `?product_category_id=${marketingKandangId}` : '';

                        const productIdRoute = '{{ route("data-master.product.search") }}';
                        initSelect2($rowScope.find('.marketing_product_select'), 'Pilih Produk', productIdRoute + qryParam);
                    });
                    // ? END :: SELECT2 :: MARKETING PRODUCT
                // ? END :: SELECT2 :: MARKETING KANDANG

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

        const $marketingProductRepeater = $('#marketing-product-repeater-1').repeater(optMarketingProduct);
        const oldMarketingProducts = @json(old('marketing_products'));
        if (oldMarketingProducts) {
            $marketingProductRepeater.setList(oldMarketingProduct);

            $.each(oldMarketingProducts, function(index, product) {
                const $select = $(`.marketing_kandang_select:eq(${index})`);
                const newOption = new Option(product.marketing_kandang_name, product.marketing_kandang_id, true, true);
                $select.append(newOption).trigger('change');
            })
        }
        // ? END :: REPEATER :: PRODUCTS

        // ? START :: REPEATER :: MARKETING ADDIT PRICES
        const optMarketingAdditPrices = {
            show: function() {
                $(this).slideDown();

                initNumeralMask();

                // FEATHER ICONS
                if (feather) {
                    feather.replace({ width: 14, height: 14 });
                }
            },
            hide: function(deleteElement) {
                confirmDelete(this, deleteElement);
            },
        };

        const $marketingAdditPricesRepeater = $('#marketing-addit-prices-repeater-1').repeater(optMarketingAdditPrices);
        // ? END :: REPEATER :: MARKETING ADDIT PRICES
    });
</script>

@endsection
