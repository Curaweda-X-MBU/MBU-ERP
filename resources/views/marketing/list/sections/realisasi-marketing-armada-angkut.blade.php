@php
    $dataVehicles = '';
    if (isset($data->marketing_delivery_vehicles[0])) {
        $dataVehicles = $data->marketing_delivery_vehicles;
    }
@endphp

<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/animate/animate.css')}}" />
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/sweetalert2.min.css')}}" />

<div class="table-responsive mt-3">
    <table id="marketing-delivery-vehicles-repeater-1" class="table w-100">
        <thead>
            <tr class="text-center">
                <th>No Polisi<i class="text-danger">*</i></th>
                <th>Supplier<i class="text-danger">*</i></th>
                <th>Product<i class="text-danger">*</i></th>
                <th>Jumlah<i class="text-danger">*</i></th>
                <th>UOM<i class="text-danger">*</i></th>
                <th>Biaya Antar (Rp)<i class="text-danger">*</i></th>
                <th>Waktu Keluar Kandang<i class="text-danger">*</i></th>
                <th>Nama Pengirim<i class="text-danger">*</i></th>
                <th>Nama Driver<i class="text-danger">*</i></th>
                <th>
                    <button class="btn btn-sm btn-icon btn-primary" type="button" data-repeater-create title="Tambah Armada">
                        <i data-feather="plus"></i>
                    </button>
                </th>
            </tr>
        </thead>
        <tbody data-repeater-list="marketing_delivery_vehicles">
            <tr class="text-center" data-repeater-item>
                <td class="py-2">
                    <input name="plat_number" type="text" class="form-control" placeholder="No Polisi" required>
                </td>
                <td>
                    <select name="supplier_id" class="form-control supplier_select" required>
                        <option value="">Pilih Vendor</option>
                    </select>
                </td>
                <td class="py-2 position-relative">
                    <select name="marketing_product_id" class="form-control vehicle_product_select" required>
                        <option value="">Pilih Produk</option>
                    </select>
                    <small class="form-text text-muted text-right position-absolute pr-1" style="right: 0; font-size: 80%;">Sisa :<span class="current_stock">0</span></small>
                </td>
                <td class="py-2 position-relative">
                    <input type="number" name="qty" id="qty" max="0" class="position-absolute" style="opacity: 0; pointer-events: none;" tabindex="-1">
                    <input type="text" class="form-control numeral-mask qty_mask" placeholder="Jumlah" required>
                    <span class="text-danger text-right small position-absolute pr-1 invalid" style="right: 0; font-size: 80%; opacity: 0;">Melebihi stock</span>
                </td>
                <td class="py-2">
                    <input name="uom_id" type="hidden">
                    <input name="uom_name" class="form-control uom_select" placeholder="Unit" disabled>
                </td>
                <td class="py-2">
                    <input name="delivery_fee" type="text" class="form-control numeral-mask delivery_fee_mask" placeholder="Biaya" required>
                </td>
                <td class="py-2">
                    <input id="exit_at" name="exit_at" class="form-control flatpickr-datetime" placeholder="Waktu Keluar Kandang" required>
                </td>
                <td class="py-2">
                    <select name="sender_id" class="form-control sender_select" required>
                    </select>
                </td>
                <td class="py-2">
                    <input name="driver_name" type="text" class="form-control" placeholder="Nama Driver" required>
                </td>
                <td class="py-2">
                    <button class="btn btn-sm btn-icon btn-danger" data-repeater-delete type="button" title="Hapus Armada">
                        <i data-feather="x"></i>
                    </button>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<script src="{{asset('app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/forms/repeater/jquery.repeater.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/forms/cleave/cleave.min.js')}}"></script>

<script>
    $(function() {
        function setField($this) {
            const data = $this.select2('data')[0];
            const uom_id = data.element.dataset.uom_id;
            const uom_name = data.element.dataset.uom_name;
            const qty = data.element.dataset.qty;

            const $rowScope = $this.closest('tr');

            const $uomId = $rowScope.find('input[name*="uom_id"]');
            const $uomName = $rowScope.find('input[name*="uom_name"]');
            const $currentStock = $rowScope.find('.current_stock');
            const $qty = $rowScope.find('input[name*="qty"]');

            $uomId.val(uom_id);
            $uomName.val(uom_name);
            $qty.attr('max', qty);
            $currentStock.text(parseNumToLocale(parseInt(qty - $qty.val())).split(',')[0]);

        }

        function updateMax($this) {
            const updatedProductId = $this.closest('tr').find('.marketing_product_select').val();
            const $allTargetOptions = $(`#marketing-delivery-vehicles-repeater-1 option[data-product-id="${updatedProductId}"]`);
            $allTargetOptions.attr('data-qty', parseLocaleToNum($this.val()));

            $allTargetOptions.filter(':selected').each(function() {
                updateCurrentStock($(this));
            });
        }

        function updateCurrentStock($option) {
            const $select = $option.parent();
            const $rowScope = $select.closest('tr');

            const $currentStock = $rowScope.find('.current_stock');
            const $qty = $rowScope.find('input[name*="qty"]');
            const qty = $option.attr('data-qty');

            if ($qty.length) {
                $qty.attr('max', qty);
                const currentVal = parseInt($qty.val() || 0);
                const remaining = parseInt(qty) - currentVal;
                $currentStock.text(trimLocale(remaining));
            }

            updateQty($rowScope.find('.qty_mask'));
        }

        function updateQty($this) {
            const $rowScope = $this.closest('tr');
            const $qty = $rowScope.find('input[name*="qty"]');
            const $currentStock = $rowScope.find('.current_stock');
            const max = $qty.attr('max');

            const val = parseLocaleToNum($this.val());

            $qty.val(val);

            // cross check
            const $currentProduct = $rowScope.find('select[name*="marketing_product_id"]');
            const currentProductId = $currentProduct.val();
            const $products = $('#marketing-delivery-vehicles-repeater-1 select[name*="marketing_product_id"]');
            const $filteredProducts = $products.filter(function() {
                return $(this).val() === currentProductId;
            }).filter(function() {
                return $(this).data('select2Id') !== $currentProduct.data('select2Id');
            });
            const otherQty = $filteredProducts.map(function() {
                return $(this).closest('tr').find('input[name*="qty"]').val();
            }).get().reduce((cur, arr) => cur + parseInt(arr), 0);

            const currentStock = parseNumToLocale(parseInt(max - val - otherQty)).split(',')[0];

            $currentStock.text(currentStock);
            $filteredProducts.map(function() {
                return $(this).closest('tr').find('.current_stock');
            }).each(function() {
                $(this).text(currentStock);
            });

            const stock = parseLocaleToNum($currentStock.text());
            if (stock < 0) {
                $qty.siblings('.invalid').css('opacity', 1);
            } else {
                $qty.siblings('.invalid').css('opacity', 0);
            }
        }

        $('#marketing-product-repeater-1 #qty_mask').on('change', function() {
            updateMax($(this));
        });

        const dateTimeOpt = { dateFormat: 'd-M-Y H:i', enableTime: true };
        const optMarketingDeliveryVehicles = {
            initEmpty: true,
            show: function() {
                const $row = $(this);
                $row.slideDown();
                const $senderSelect = $row.find('.sender_select');
                const senderIdRoute = '{{ route("user-management.user.search") }}';
                initSelect2($senderSelect, 'Pilih Pengirim', senderIdRoute);

                const $supplierSelect = $row.find('.supplier_select');
                const supplierIdRoute = '{{ route("data-master.supplier.search") }}';
                initSelect2($supplierSelect, 'Pilih Vendor', supplierIdRoute);

                const $productSelect = $row.find('.vehicle_product_select');
                initSelect2($productSelect, 'Pilih Produk');
                if ('{{ @$data->marketing_products }}') {
                    const marketingProducts = @json($data->marketing_products);
                    marketingProducts.forEach((marketingProduct, i) => {
                        $productSelect.append(`<option data-qty="${marketingProduct.qty}" data-product-id="${marketingProduct.product.product_id}" data-uom_id="${marketingProduct.uom.uom_id}" data-uom_name="${marketingProduct.uom.name}" value="${marketingProduct.marketing_product_id}">${marketingProduct.product.name}</option>`)
                    });
                }

                $productSelect.on('select2:select', function() {
                    setField($(this));
                });

                // set default value to prevent NaN error when calculating combined qty
                $row.find('input[name*="qty"]').val(0);

                $row.find('.qty_mask').on('input', function() {
                    updateQty($(this));
                });

                initNumeralMask('.numeral-mask');
                $('.flatpickr-datetime').flatpickr(dateTimeOpt);
                if (feather) {
                    feather.replace({ width: 14, height: 14 });
                }
            },
            hide: function(deleteElement) {
                confirmDelete($(this), deleteElement);
            },
        }

        const $repeaterVehicle = $('#marketing-delivery-vehicles-repeater-1').repeater(optMarketingDeliveryVehicles);

        if ('{{ $dataVehicles }}'.length) {
            const vehicles = @json($dataVehicles);

            vehicles.forEach((vehicle, i) => {
                $('#marketing-delivery-vehicles-repeater-1').find('button[data-repeater-create]').trigger('click');

                $(`input[name="marketing_delivery_vehicles[${i}][plat_number]"]`).val(vehicle.plat_number);

                if (vehicle.supplier) {
                    $(`select[name="marketing_delivery_vehicles[${i}][supplier_id]"]`).append(`<option value="${vehicle.supplier_id}" selected>${vehicle.supplier.name}</option>`).trigger('change');
                }

                if (vehicle.marketing_product) {
                    $(`select[name="marketing_delivery_vehicles[${i}][marketing_product_id]"]`).append(`<option value="${vehicle.marketing_product_id}" selected>${vehicle.marketing_product.product.name}</option>`).trigger('change');
                }

                $(`input[name="marketing_delivery_vehicles[${i}][qty]"]`).val(vehicle.qty);
                $(`input[name="marketing_delivery_vehicles[${i}][qty]"]`).siblings('.qty_mask').val(parseNumToLocale(vehicle.qty));

                $(`input[name="marketing_delivery_vehicles[${i}][uom_id]"]`).val(vehicle.uom_id);
                $(`input[name="marketing_delivery_vehicles[${i}][uom_name]"]`).val(vehicle.uom.name);

                $(`input[name="marketing_delivery_vehicles[${i}][delivery_fee]"]`).val(parseNumToLocale(vehicle.delivery_fee));

                const date = new Date(vehicle.exit_at);
                const dateOptions = { day: '2-digit', year: 'numeric', month: 'short' };
                const timeOptions = { hour: '2-digit', minute: '2-digit', hour12: true };
                const formattedDate = date.toLocaleDateString('en-GB', dateOptions).replace(/ /g, '-');
                const formattedTime = date.toLocaleTimeString('en-GB', timeOptions);
                $(`input[name="marketing_delivery_vehicles[${i}][exit_at]"]`).val(`${formattedDate} ${formattedTime}`);

                $(`select[name="marketing_delivery_vehicles[${i}][sender_id]"]`).append(`<option value="${vehicle.sender_id}" selected>${vehicle.sender.name}</option>`).trigger('change');

                $(`input[name="marketing_delivery_vehicles[${i}][driver_name]"]`).val(vehicle.driver_name);
            });
        } else {
            $('#marketing-delivery-vehicles-repeater-1').find('button[data-repeater-create]').trigger('click');
        }
    });
</script>
