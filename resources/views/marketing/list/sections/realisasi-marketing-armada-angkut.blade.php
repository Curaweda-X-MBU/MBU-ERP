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
                    <input name="qty" type="text" class="form-control numeral-mask" placeholder="Jumlah" required>
                </td>
                <td class="py-2">
                    <select name="uom_id" class="form-control uom_select" required>
                    </select>
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
                const $uomSelect = $row.find('.uom_select');
                const $senderSelect = $row.find('.sender_select');
                const uomIdRoute = '{{ route("data-master.uom.search") }}';
                const senderIdRoute = '{{ route("user-management.user.search") }}';
                initSelect2($uomSelect, 'Pilih Satuan', uomIdRoute);
                initSelect2($senderSelect, 'Pilih Pengirim', senderIdRoute);
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
                $(`input[name="marketing_delivery_vehicles[${i}][qty]"]`).val(vehicle.qty);
                $(`select[name="marketing_delivery_vehicles[${i}][uom_id]"]`).append(`<option value="${vehicle.uom_id}" selected>${vehicle.uom.name}</option>`).trigger('change');
                $(`input[name="marketing_delivery_vehicles[${i}][exit_at]"]`).val(vehicle.exit_at);
                $(`select[name="marketing_delivery_vehicles[${i}][sender_id]"]`).append(`<option value="${vehicle.sender_id}" selected>${vehicle.sender.name}</option>`).trigger('change');
                $(`input[name="marketing_delivery_vehicles[${i}][driver_name]"]`).val(vehicle.driver_name);
            });
        } else {
            $('#marketing-delivery-vehicles-repeater-1').find('button[data-repeater-create]').trigger('click');
        }
    });
</script>
