<div class="table-responsive mt-2">
    <div class="row bg-primary d-flex justify-content-center align-items-center py-1 text-white px-2">
        <p class="col-md-6 mb-0">Biaya Lainnya</p>
        @if (@$data->expense_addit_prices)
        <p class="col-md-6 mb-0 text-right">Rp. <span id="total-biaya-lainnya">{{ \App\Helpers\Parser::toLocale(@$data->expense_addit_prices->sum('price')) }}</span></p>
        @else
        <p class="col-md-6 mb-0 text-right">Rp. <span id="total-biaya-lainnya">0,00</span></p>
        @endif
    </div>
    <table id="expense-repeater-2" class="table table-bordered">
        <thead>
            <tr class="bg-light text-center">
                <th>Nama Biaya</th>
                <th>Nominal Biaya (Rp)</th>
                <th>Catatan</th>
                <th class="col-1">
                    <button class="btn btn-sm btn-icon btn-primary" type="button" data-repeater-create title="Tambah Biaya Lainnya">
                        <i data-feather="plus"></i>
                    </button>
                </th>
            </tr>
        </thead>
        <tbody data-repeater-list="expense_addit_prices">
            @if (@$data->expense_addit_prices->count() > 0)
                @foreach ($data->expense_addit_prices as $ap)
                <tr data-repeater-item>
                    <td><input name="name" type="text" class="form-control" value="{{ $ap->name }}" placeholder="Masukkan nama biaya"></td>
                    <td><input name="price" type="text" class="form-control numeral-mask text-right total-amount-all-farms-2" value="{{ \App\Helpers\Parser::toLocale($ap->price) }}" placeholder="0"></td>
                    <td><input name="notes" type="text" class="form-control" value="{{ $ap->notes }}" placeholder="Masukkan catatan"></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-icon btn-danger" data-repeater-delete type="button" title="Hapus Produk">
                            <i data-feather="x"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            @else
            <tr data-repeater-item>
                <td><input name="name" type="text" class="form-control" placeholder="Masukkan nama biaya"></td>
                <td><input name="price" type="text" class="form-control numeral-mask text-right total-amount-all-farms-2" value="0" placeholder="0"></td>
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
        function calculateBiayaLainnya() {
            let total = 0;
            $('.total-amount-all-farms-2').each(function() {
                const value = $(this).val() || '0';
                total += parseLocaleToNum(value);
            });
            $('#total-biaya-lainnya').text(parseNumToLocale(total)).trigger('change');
        }

        function initializeRows($row) {
            const $numeralInput = $row.find('.numeral-mask');
            initNumeralMask($numeralInput);

            if (feather) {
                feather.replace({ width: 14, height: 14 });
            }
        }

        let is_edit = parseInt('{{ ! empty($data->expense_addit_prices) }}');

        const expenseRepeater2 = $("#expense-repeater-2").repeater({
            initEmpty: is_edit ? false : true,
            show: function() {
                const $row = $(this);
                $row.slideDown();

                initializeRows($row);
                console.log('new row');
            },
            hide: function(deleteElement) {
                confirmDelete($(this), () => {
                    deleteElement();
                    calculateBiayaLainnya();
                });
            }
        });

        $('#expense-repeater-2').on('input', '.numeral-mask', function() {
            calculateBiayaLainnya();
        });

        if (!is_edit) {
            $('#expense-repeater-2 button[data-repeater-create]').trigger('click');
        } else {
            $('#expense-repeater-2 [data-repeater-item]').each(function() {
                const $row = $(this);
                initializeRows($row);
            });
        }
    });
</script>
