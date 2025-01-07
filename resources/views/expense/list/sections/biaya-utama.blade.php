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
                <th>Sub Kategori<i class="text-danger">*</i></th>
                <th>QTY<i class="text-danger">*</i></th>
                <th>UOM</th>
                <th>Harga Satuan (Rp)</th>
                <th>Total Biaya (Rp)<i class="text-danger">*</i></th>
                <th>Catatan</th>
                <th class="col-1">
                    <button class="btn btn-sm btn-icon btn-primary" type="button" data-repeater-create title="Tambah Produk">
                        <i data-feather="plus"></i>
                    </button>
                </th>
            </tr>
        </thead>
        <tbody data-repeater-list="expense_main_prices">
            @if (@$data->expense_main_prices)
                @foreach ($data->expense_main_prices as $mp)
                @php
                $uom = \App\Models\DataMaster\Nonstock::where('name', $mp->sub_category)->first()->uom;
                $countKandang = count($data->expense_kandang) ?: 1;
                @endphp
                <tr data-repeater-item>
                    <td>
                        <select name="sub_category" class="form-control sub-category-select" required>
                            <option value="{{ $mp->sub_category }}" selected>{{ $mp->sub_category }}</option>
                        </select>
                    </td>
                    <td><input name="qty" type="text" class="form-control numeral-mask" value="{{ \App\Helpers\Parser::toLocale($mp->qty) }}" placeholder="0" required></td>
                    <td>
                        <input name="uom" class="form-control uom" value="{{ $uom->name }}" readonly>
                    </td>
                    <td><input type="text" class="unit-price form-control numeral-mask text-right" value="{{ \App\Helpers\Parser::toLocale($mp->price / $countKandang) }}" disabled></td>
                    <td><input name="total_price" type="text" class="total-amount-all-farms form-control numeral-mask text-right" value="{{ \App\Helpers\Parser::toLocale($mp->price) }}" placeholder="0" required></td>
                    <td><input name="notes" type="text" class="form-control" value="{{ $mp->notes }}" placeholder="Masukkan catatan"></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-icon btn-danger" data-repeater-delete type="button" title="Hapus Produk">
                            <i data-feather="x"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            @else
            <tr data-repeater-item>
                <td>
                    <select name="sub_category" class="form-control sub-category-select" required></select>
                </td>
                <td><input name="qty" type="text" class="form-control numeral-mask" value="0" placeholder="0" required></td>
                <td>
                    <input name="uom" class="form-control uom" readonly></input>
                </td>
                <td><input type="text" class="unit-price form-control numeral-mask text-right" value="0" disabled></td>
                <td><input name="total_price" type="text" class="total-amount-all-farms form-control numeral-mask text-right" value="0" placeholder="0" required></td>
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
            const qty = parseLocaleToNum($row.find('input.numeral-mask').eq(0).val() || '0');
            const totalAmount = parseLocaleToNum($row.find('input.total-amount-all-farms').val() || '0');

            if (qty > 0) {
                const unitPrice = totalAmount / qty;
                $row.find('input.unit-price').val(parseNumToLocale(unitPrice));
            } else {
                $row.find('input.unit-price').val('');
            }
        }

        function calculateBiayaUtama() {
            let total = 0;
            $('.total-amount-all-farms').each(function() {
                const value = $(this).val() || '0';
                total += parseLocaleToNum(value);
            });
            $('#total-biaya-utama').text(parseNumToLocale(total)).trigger('change');
        }

        function initializeRows($row) {
            const $subCategorySelect = $row.find('.sub-category-select');
            const subCategoryIdRoute = '{{ route("data-master.nonstock.search") }}';
            initSelect2($subCategorySelect, 'Pilih Sub Kategori', subCategoryIdRoute, 'nonStock');
            $subCategorySelect.on('select2:select', function(){
                const data = $(this).select2('data')[0];
                $row.find('input.uom').val(data.uom_name);
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
