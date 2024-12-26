<div class="table-responsive mt-2">
    <table id="expense-repeater-1" class="table table-bordered">
        <thead>
            <tr class="bg-light text-center">
                <th class="col-6">Sub Kategori</th>
                <th class="col-6">Nominal Seluruh Kandang</th>
                <th class="col-1">
                    <button class="btn btn-sm btn-icon btn-primary" type="button" data-repeater-create title="Tambah Produk">
                        <i data-feather="plus"></i>
                    </button>
                </th>
            </tr>
        </thead>
        <tbody data-repeater-list="expense">
            <tr data-repeater-item>
                <td>
                    <select id="sub_category_id" class="form-control sub-category-select">
                        <option value="">Pilih Sub Kategori</option>
                        <option value="Air">Air</option>
                        <option value="Listrik">Listrik</option>
                        <option value="Gas">Gas</option>
                    </select>
                </td>
                <td><input type="text" id="total-amount-all-farms" class="form-control numeral-mask text-right" value="0"></td>
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
        $('input[id="total-amount-all-farms"]').each(function() {
            const value = $(this).val() || '0';
            total += parseLocaleToNum(value);
        });
        $('#total-expense').text(parseNumToLocale(total));
    }

    const expenseRepeater = $("#expense-repeater-1").repeater({
        initEmpty: false,
        defaultValues: {
            'total-amount-all-farms': '0'
        },
        show: function() {
            const $row = $(this);
            $row.slideDown();

            const $subCategorySelect = $row.find('.sub-category-select');
            initSelect2($subCategorySelect, 'Pilih Sub Kategori');

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

    // Initialize existing components
    $('.sub-category-select').each(function() {
        initSelect2($(this), 'Pilih Sub Kategori');
    });

    // Initialize numeral mask for existing inputs
    initNumeralMask('.numeral-mask');

    // Add input event listener to existing inputs
    $(document).on('input', 'input[id="total-amount-all-farms"]', function() {
        updateTotal();
    });

    // Initial total calculation
    updateTotal();
</script>
