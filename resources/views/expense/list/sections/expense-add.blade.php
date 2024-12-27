<div class="table-responsive mt-2">
    <table id="expense-repeater-1" class="table table-bordered">
        <thead>
            <tr class="bg-light text-center">
                <th>Sub Kategori<i class="text-danger">*</i></th>
                <th>QTY<i class="text-danger">*</i></th>
                <th>UOM</th>
                <th>Harga Satuan (Rp)</th>
                <th>Nominal Seluruh Kandang (Rp)<i class="text-danger">*</i></th>
                <th class="col-1">
                    <button class="btn btn-sm btn-icon btn-primary" type="button" data-repeater-create title="Tambah Produk">
                        <i data-feather="plus"></i>
                    </button>
                </th>
            </tr>
        </thead>
        <tbody data-repeater-list="expense_items">
            <tr data-repeater-item>
                <td>
                    <select class="form-control sub-category-select"></select>
                </td>
                <td><input type="text" class="form-control numeral-mask" value="0"></td>
                <td>
                    <input class="form-control uom" disabled></input>
                </td>
                <td><input type="text" class="form-control numeral-mask text-right" value="0" disabled></td>
                <td><input type="text" class="form-control numeral-mask text-right total-amount-all-farms" value="0"></td>
                <td class="text-center">
                    <button class="btn btn-sm btn-icon btn-danger" data-repeater-delete type="button" title="Hapus Produk">
                        <i data-feather="x"></i>
                    </button>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
<script src="{{asset('app-assets/vendors/js/forms/repeater/jquery.repeater.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/forms/cleave/cleave.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/extensions/sweetalert2.all.min.js')}}"></script>
<script>
    function updateTotal() {
        let total = 0;
        $('.total-amount-all-farms').each(function() {
            const value = $(this).val() || '0';
            total += parseLocaleToNum(value);
        });
        $('#total-expense').text(parseNumToLocale(total));
    }

    const expenseRepeater = $("#expense-repeater-1").repeater({
        initEmpty: true,
        defaultValues: {
            'total-amount-all-farms': '0'
        },
        show: function() {
            const $row = $(this);
            $row.slideDown();

            const $subCategorySelect = $row.find('.sub-category-select');
            const subCategoryIdRoute = '{{ route("data-master.nonstock.search") }}';
            initSelect2($subCategorySelect, 'Pilih Sub Kategori', subCategoryIdRoute, 'nonStock');
            $subCategorySelect.on('select2:select', function(){
                const data = $(this).select2('data')[0];
                $row.find('input.uom').val(data.uom_name);
            })

            const $numeralInput = $row.find('.numeral-mask');
            initNumeralMask($numeralInput);

            $numeralInput.on('input', function() {
                updateTotal();
            });

            if (feather) {
            feather.replace({ width: 14, height: 14 });
            }
        },
        hide: function(deleteElement) {
            confirmDelete($(this), deleteElement);
        }
    });

    $('button[data-repeater-create]').trigger('click');

    // Initialize numeral mask for existing inputs
    initNumeralMask('.numeral-mask');

    // Add input event listener to existing inputs
    $(document).on('input', '.total-amount-all-farms', function() {
        updateTotal();
    });

    // Initial total calculation
    updateTotal();
</script>
