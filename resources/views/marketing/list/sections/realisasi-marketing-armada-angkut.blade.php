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
                <th>No Polisi</th>
                <th>Product</th>
                <th>Jumlah</th>
                <th>UOM</th>
                <th>Waktu Keluar Kandang</th>
                <th>Nama Pengirim</th>
                <th>Nama Driver</th>
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
                <td class="py-2">
                    <select name="marketing_product_id" class="form-control vehicle_product_select" required>
                        <option value="">Pilih Produk</option>
                    </select>
                </td>
                <td class="py-2">
                    <input name="qty" type="text" class="form-control numeral-mask" placeholder="Jumlah" required>
                </td>
                <td class="py-2">
                    <input name="uom_id" type="hidden">
                    <input name="uom_name" class="form-control uom_select" placeholder="Unit" disabled>
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
        const dateOpt = { dateFormat: 'd-M-Y' };
        const dateTimeOpt = { dateFormat: 'd-M-Y H:i', enableTime: true };
        const optMarketingDeliveryVehicles = {
            initEmpty: true,
            show: function() {
                const $row = $(this);
                $row.slideDown();
                const $senderSelect = $row.find('.sender_select');
                const senderIdRoute = '{{ route("user-management.user.search") }}';
                initSelect2($senderSelect, 'Pilih Pengirim', senderIdRoute);

                const $productSelect = $row.find('.vehicle_product_select');
                initSelect2($productSelect, 'Pilih Produk');
                if ('{{ @$data->marketing_products }}') {
                    const marketingProducts = @json($data->marketing_products);
                    marketingProducts.forEach((marketingProduct, i) => {
                        $productSelect.append(`<option data-uom_id="${marketingProduct.uom.uom_id}" data-uom_name="${marketingProduct.uom.name}" value="${marketingProduct.marketing_product_id}">${marketingProduct.product.name}</option>`)
                    });
                }

                const $uomId = $row.find('input[name*="uom_id"]');
                const $uomName = $row.find('input[name*="uom_name"]');
                $productSelect.on('select2:select', function() {
                    const data = $(this).select2('data')[0];
                    const uom_id = data.element.dataset.uom_id;
                    const uom_name = data.element.dataset.uom_name;
                    $uomId.val(uom_id);
                    $uomName.val(uom_name);
                });

                initNumeralMask('.numeral-mask');
                $('.flatpickr-datetime').flatpickr(dateTimeOpt);
                $('.flatpickr-basic').flatpickr(dateOpt);
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

                $(`select[name="marketing_delivery_vehicles[${i}][marketing_product_id]"]`).append(`<option value="${vehicle.marketing_product_id}" selected>${vehicle.marketing_product.product.name}</option>`).trigger('change');

                $(`input[name="marketing_delivery_vehicles[${i}][qty]"]`).val(vehicle.qty);

                $(`input[name="marketing_delivery_vehicles[${i}][uom_id]"]`).val(vehicle.uom_id);
                $(`input[name="marketing_delivery_vehicles[${i}][uom_name]"]`).val(vehicle.uom.name);

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
