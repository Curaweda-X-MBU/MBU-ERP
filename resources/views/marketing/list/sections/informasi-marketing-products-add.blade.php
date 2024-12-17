@php
    $dataProducts = '';
    if (isset($data)) {
        $dataProducts = $data->marketing_products;
    }
@endphp

<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/animate/animate.css')}}" />
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/sweetalert2.min.css')}}" />

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
                    <button class="btn btn-sm btn-icon btn-primary {{ isset($is_realization) ? 'd-none' : '' }}" type="button" data-repeater-create title="Tambah Produk">
                        <i data-feather="plus"></i>
                    </button>
                </th>
            </tr>
        </thead>
        <tbody data-repeater-list="marketing_products">
            <tr class="text-center" data-repeater-item>
                <td class="pt-2 pb-3">
                    <select name="kandang_id" class="form-control marketing_kandang_select" {{ (isset($is_realization) && $is_realization) ? 'readonly' : '' }} required>
                    </select>
                </td>
                <td class="pt-2 pb-3 position-relative">
                    <select name="product_id" class="form-control marketing_product_select" {{ (isset($is_realization) && $is_realization) ? 'readonly' : '' }} required>
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
                @if (!isset($is_realization))
                <td class="pt-2 pb-3">
                    <button class="btn btn-sm btn-icon btn-danger" data-repeater-delete type="button" title="Hapus Produk">
                        <i data-feather="x"></i>
                    </button>
                </td>
                @endif
            </tr>
        </tbody>
    </table>
</div>

<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/forms/repeater/jquery.repeater.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/forms/cleave/cleave.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/extensions/sweetalert2.all.min.js')}}"></script>
<script>
    // ? START :: SET VALUE :: QTY & CURRENT STOCK
    const localeOpts = { minimumFractionDigits: 2, maximumFractionDigits: 2 };
    function setQtyStock($this, reset) {
        let qty;
        if (reset) {
            qty = 0;
        } else {
            const data = $this.select2('data')[0];
            qty = data && data.qty ? data.qty : 0;
        }
        const value = qty.toLocaleString('en-GB');
        const $rowScope = $this.closest('tr');

        $rowScope.find('#current_stock').text(value);
        $rowScope.find('#qty').attr('max', qty);
    }
    // ? END :: SET VALUE :: QTY & CURRENT STOCK

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

            console.log($qtyInput.val());
            const qty = parseLocale($qtyInput.val());
            const weightAvg = parseLocale($weightAvgInput.val());
            const price = parseLocale($priceInput.val());

            const weightTotal = qty * weightAvg;
            const priceTotal = weightTotal * price;

            $weightTotalInput.val(weightTotal.toLocaleString('en-GB'));
            $priceTotalInput.val(priceTotal.toLocaleString('en-GB'));

            setTimeout(function(){
                const priceAllRow = $('#marketing-product-repeater-1 #price_total').get().reduce(function(acc, elem) {
                    const value = parseLocale($(elem).val());
                    return acc + value;
                }, 0);
                $totalSebelumPajak.text(priceAllRow.toLocaleString('en-GB')).trigger('change');
            }, 0);
        });
    }
    // ? END :: CALCULATION ::  PRODUCT ROWS

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
                const val = parseLocale($(this).val());
                const stock = parseLocale($row.find('#current_stock').text());
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
            initNumeralMask('.numeral-mask');
        },
        hide: function(deleteElement) {
            confirmDelete($(this), deleteElement);
        },
    };

    // ? END :: REPEATER OPTS :: PRODUCTS
    const $repeaterProduct = $('#marketing-product-repeater-1').repeater(optMarketingProduct);
    calculateTotalPerRow();

    // ? START :: EDIT VALUES
    if ('{{ $dataProducts }}'.length) {
        const products = @json($dataProducts);

        products.forEach((product, i) => {
            $('#marketing-product-repeater-1').find('button[data-repeater-create]').trigger('click');
            $(`select[name="marketing_products[${i}][kandang_id]"]`).append(`<option value="${product.kandang.kandang_id}" selected>${product.kandang.name}</option>`).trigger('change');
            $(`select[name="marketing_products[${i}][product_id]"]`).append(`<option value="${product.product.product_id}" selected>${product.product.name}</option>`).trigger('change');
            $(`input[name="marketing_products[${i}][price]"]`).val(product.price);
            $(`select[name="marketing_products[${i}][uom_id]"]`).append(`<option value="${product.uom_id}" selected>${product.uom.name}</option>`).trigger('change');
            $(`input[name="marketing_products[${i}][weight_avg]"]`).val(product.weight_avg);
            let productIdRoute = '{{ route("marketing.list.search-product", ['id' => ':id']) }}';
            productIdRoute = productIdRoute.replace(':id', product.kandang.kandang_id);
            const ajaxProduct = $.get({
                url: productIdRoute,
                dataType: 'json',
            });
            ajaxProduct.done(function(res) {
                const chose = res.filter((a) => a.data.product_id = product.product.product_id)[0];
                $(`select[name="marketing_products[${i}][product_id]"]`).closest('tr').find('#qty').prop('max', chose.qty);
                $(`select[name="marketing_products[${i}][product_id]"]`).closest('tr').find('#current_stock').text(chose.qty);
                $(`input[name="marketing_products[${i}][qty]"]`).siblings('#qty_mask').val(product.qty).trigger('input');

            initNumeralMask('.numeral-mask');
            });
        });
    } else {
        $('#marketing-product-repeater-1').find('button[data-repeater-create]').trigger('click');
    }
    // ? END :: EDIT VALUES
</script>
