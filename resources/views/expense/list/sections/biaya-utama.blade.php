<div class="table-responsive mt-2">
    <div class="row bg-primary d-flex justify-content-center align-items-center py-1 text-white px-2">
        <p class="col-md-6 mb-0">Biaya Utama</p>
        @if (@$data->expense_main_prices)
        <p class="col-md-6 mb-0 text-right">Rp. <span id="total-biaya-utama">{{ \App\Helpers\Parser::toLocale(@$data->expense_main_prices->sum('price')) }}</span></p>
        @else
        <p class="col-md-6 mb-0 text-right">Rp. <span id="total-biaya-utama">0,00</span></p>
        @endif
    </div>
     <table id="expense-repeater-1" class="table table-bordered">
        <thead>
            <tr class="bg-light text-center">
                <th>Supplier</th>
                <th>Non Stock<i class="text-danger">*</i></th>
                <th>Total Qty<i class="text-danger">*</i></th>
                <th>QTY per Kandang</th>
                <th>UOM</th>
                <th>Total Biaya (Rp)<i class="text-danger">*</i></th>
                <th>Harga per Kandang (Rp)</th>
                <th>Catatan<i class="text-danger">*</i></th>
                <th class="col-1">
                    <button class="btn btn-sm btn-icon btn-primary" type="button" data-repeater-create title="Tambah Produk">
                        <i data-feather="plus"></i>
                    </button>
                </th>
            </tr>
        </thead>
         <tbody data-repeater-list="expense_main_prices">
            @if (!empty($data->expense_main_prices) && $data->expense_main_prices->count() > 0)
                @foreach ($data->expense_main_prices as $mp)
                <tr data-repeater-item>
                    <td>
                        <select name="supplier_id" class="form-control supplier-select">
                            <option value="{{ $mp->supplier_id ?? null }}" selected>{{ $mp->supplier->name ?? null }}</option>
                        </select>
                    </td>
                    <td>
                        <select name="nonstock_id" class="form-control nonstock-select" required>
                            <option value="{{ $mp->nonstock_id }}" selected>{{ $mp->nonstock->name }}</option>
                        </select>
                    </td>
                    <td><input name="qty" type="text" class="unit-qty form-control numeral-mask" value="{{ \App\Helpers\Parser::toLocale($mp->qty) }}" placeholder="0" required></td>
                    <td><input type="text" class="total-qty-all-farms form-control numeral-mask" value="{{ \App\Helpers\Parser::toLocale($mp->qty_per_kandang) }}" placeholder="0" disabled></td>
                    <td><span class="uom" readonly>{{ $mp->nonstock->uom->name }}</span></td>
                    <td><input name="price" type="text" class="unit-price form-control numeral-mask text-right" value="{{ \App\Helpers\Parser::toLocale($mp->price) }}" placeholder="0" required></td>
                    <td><input type="text" class="total-amount-all-farms form-control numeral-mask text-right" value="{{ \App\Helpers\Parser::toLocale($mp->price_per_kandang) }}" placeholder="0" disabled></td>
                    <td><input name="notes" type="text" class="form-control" value="{{ $mp->notes }}" placeholder="Masukkan catatan" required></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-icon btn-danger" data-repeater-delete type="button" title="Hapus Produk">
                            <i data-feather="x"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            @else
            <tr data-repeater-item>
                <td><select name="supplier_id" class="form-control supplier-select"></select></td>
                <td><select name="nonstock_id" class="form-control nonstock-select" required></select></td>
                <td><input name="qty"type="text" class="unit-qty form-control numeral-mask" value="0" placeholder="0" required></td>
                <td><input type="text" class="total-qty-all-farms form-control numeral-mask" value="0" placeholder="0" disabled></td>
                <td><span class="uom" readonly></span></td>
                <td><input name="price" type="text" class="unit-price form-control numeral-mask text-right" value="0" placeholder="0" required></td>
                <td><input type="text" class="total-amount-all-farms form-control numeral-mask text-right" value="0" placeholder="0" disabled></td>
                <td><input name="notes" type="text" class="form-control" placeholder="Masukkan catatan"></td>
                <td class="text-center">
                    <button class="btn btn-sm btn-icon btn-danger" data-repeater-delete type="button" title="Hapus Produk">
                        <i data-feather="x"></i>
                    </button>
                </td>
            </tr>
            @endif
        </tbody>
    </table>
</div>

<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
<script src="{{asset('app-assets/vendors/js/forms/repeater/jquery.repeater.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/forms/cleave/cleave.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/extensions/sweetalert2.all.min.js')}}"></script>
<script>
    $(function() {
        function calculateUnitPrice($row) {
            const qty = parseLocaleToNum($row.find('input.unit-qty').val() || '0');
            const price = parseLocaleToNum($row.find('input.unit-price').val() || '0');
            const countKandang = JSON.parse($('input[name="selected_kandangs"]').val()).length ? JSON.parse($('input[name="selected_kandangs"]').val()).length : 1;

            if (price > 0) {
                const totalAmount = price / countKandang;
                $row.find('input.total-amount-all-farms').val(parseNumToLocale(totalAmount));
            } else {
                $row.find('input.total-amount-all-farms').val('');
            }

            if (qty > 0) {
                const totalQty = qty / countKandang;
                $row.find('input.total-qty-all-farms').val(parseNumToLocale(totalQty));
            } else {
                $row.find('input.total-qty-all-farms').val('');
            }
        }

        function calculateBiayaUtama() {
            let total = 0;
            $('.unit-price').each(function() {
                const value = $(this).val() || '0';
                total += parseLocaleToNum(value);
            });
            $('#total-biaya-utama').text(parseNumToLocale(total)).trigger('change');
        }

        const nonstockIdRoute = '{{ route("data-master.nonstock.search") }}';

        $(document).on('change', '.supplier-select', function() {
            $(this).closest('tr').find('.nonstock-select').val('');
            $(this).closest('tr').find('.uom').text('');
            $(this).closest('tr').find('.nonstock-select').select2('destroy');
            if ($(this).val() && $(this).val() !== '') {
                initSelect2($(this).closest('tr').find('.nonstock-select'), 'Pilih Non Stock', nonstockIdRoute + '?supplier_id=' + $(this).val());
            } else {
                initSelect2($(this).closest('tr').find('.nonstock-select'), 'Pilih Non Stock', nonstockIdRoute);
            }
        });

        function initializeRows($row) {
            const $supplierSelect = $row.find('.supplier-select');
            const $nonstockSelect = $row.find('.nonstock-select');
            const supplierIdRoute = '{{ route("data-master.supplier.search") }}';
            initSelect2($supplierSelect, 'Pilih Supplier', supplierIdRoute, '', { allowClear: true });
            initSelect2($nonstockSelect, 'Pilih Non Stock', nonstockIdRoute);
            $nonstockSelect.on('select2:select', function(){
                const data = $(this).select2('data')[0];
                $row.find('.uom').text(data.uom_name);
            })

            const $numeralInput = $row.find('.numeral-mask');
            initNumeralMask($numeralInput);

            if (feather) {
                feather.replace({ width: 14, height: 14 });
            }
        }

        let is_edit = parseInt('{{ ! empty($data->expense_main_prices) }}');

        const expenseRepeater = $("#expense-repeater-1").repeater({
            initEmpty: is_edit ? false : true,
            show: function() {
                const $row = $(this);
                $row.slideDown();

                initializeRows($row);
            },
            hide: function(deleteElement) {
                confirmDelete($(this), () => {
                    deleteElement();
                    calculateBiayaUtama();
                });
            }
        });

        $('#expense-repeater-1').on('input', '.numeral-mask', function() {
            const $row = $(this).closest('tr');
            calculateUnitPrice($row);
            calculateBiayaUtama();
        });


        $('input[name="selected_kandangs"]').on('input', function() {
            const $unitPrices = $('input.unit-price');
            $unitPrices.each(function() {
                calculateUnitPrice($(this).closest('tr'));
                calculateBiayaUtama();
            });
        });

        if (!is_edit) {
            $('#expense-repeater-1 button[data-repeater-create]').trigger('click');
        } else {
            $('#expense-repeater-1 [data-repeater-item]').each(function() {
                const $row = $(this);
                initializeRows($row);
            });
        }
    });
</script>
