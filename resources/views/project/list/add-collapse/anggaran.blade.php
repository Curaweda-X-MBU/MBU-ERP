@php
    $fcr_id = old('fcr_id');
    $fcr_name = old('fcr_name');
    $target_depletion = old('target_depletion');
    $dataBudget = '';
    if (isset($data) && isset($data->project_budget)) {
        $dataBudget = $data->project_budget;
    }
@endphp

<div class="card mb-1">
    <div id="headingCollapse4" class="card-header color-header collapsed" data-toggle="collapse" role="button" data-target="#collapse4" aria-expanded="true" aria-controls="collapse4">
        <span class="lead collapse-title"> Anggaran </span>
    </div>
    <div id="collapse4" role="tabpanel" aria-labelledby="headingCollapse4" class="collapse show" aria-expanded="true">
        <div class="card-body p-2">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered w-100 text-center" id="anggaran-repeater-1">
                        <thead>
                            <th>Item</th>
                            <th>QTY</th>
                            <th>Harga Satuan (Rp)</th>
                            <th>Total Anggaran (Rp)</th>
                            <th colspan="2">
                                <button class="btn btn-sm btn-icon btn-primary" type="button" data-repeater-create title="Tambah Anggaran">
                                    <i data-feather="plus"></i>
                                </button>
                            </th>
                        </thead>
                        <tbody data-repeater-list="budget">
                            <tr data-repeater-item>
                                <td><input type="text" name="item" class="form-control" aria-describedby="item" placeholder="Item" required/></td>
                                <td style="width: 15%"><input type="text" name="qty" class="form-control budget-qty numeral-mask" aria-describedby="qty" placeholder="Quantity" required/></td>
                                <td><input type="text" name="price" class="form-control budget-price numeral-mask" aria-describedby="price_per_pcs" placeholder="Harga Satuan (Rp)" required/></td>
                                <td>
                                    <input type="text" class="form-control-plaintext text-right budget-total" aria-describedby="total_budget" placeholder="Total Anggaran (Rp)" required readonly/>
                                    <input type="hidden" class="budget-total-input" name="total" />
                                </td>
                                <td style="width: 10%">
                                    <button class="btn btn-sm btn-icon btn-danger" data-repeater-delete type="button" title="Hapus Anggaran">
                                        <i data-feather="x"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-right">
                                    Grand Total Anggaran
                                </td>
                                <td>
                                    <input type="text" class="form-control-plaintext text-right grand-total" placeholder="Grand Total Anggaran (Rp)" readonly/>
                                    <input type="hidden" class="grand-total-input" name="total_budget" />
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{{asset('app-assets/vendors/js/forms/cleave/cleave.min.js')}}"></script>

<script>
    $(function () {
        var numeralMask = $('.numeral-mask');
        if (numeralMask.length) {
            numeralMask.each(function() { 
                new Cleave(this, {
                    numeral: true,
                    numeralThousandsGroupStyle: 'thousand', numeralDecimalMark: ',', delimiter: '.'
                });
            })
        }

        const optBudget = {
            show: function () {
                $(this).slideDown();
                // Feather Icons
                if (feather) {
                    feather.replace({ width: 14, height: 14 });
                }

                var numeralMask = $('.numeral-mask');
                if (numeralMask.length) {
                    numeralMask.each(function() { 
                        new Cleave(this, {
                            numeral: true,
                            numeralThousandsGroupStyle: 'thousand', numeralDecimalMark: ',', delimiter: '.'
                        });
                    })
                }
            },
            hide: function (deleteElement) {
                if (confirm('Apakah kamu yakin ingin menghapus data ini?')) {
                    $(this).slideUp(deleteElement);
                    setTimeout(() => {
                        calculateTotal();
                    }, 1000);
                }
            }
        };
        const $budgetRepeater = $('#anggaran-repeater-1').repeater(optBudget);
        const oldBudget = @json(old("budget"));
        if (oldBudget) {
            $budgetRepeater.setList(oldBudget);
        } 

        if ('{{ $dataBudget }}'.length) {
            const dataBudget = @json($dataBudget);
            if (dataBudget) {
                $budgetRepeater.setList(dataBudget);
            }
        } 

        function calculateAnggaran(set) {
            let price = set.find('.budget-price').val();
            let qty = set.find('.budget-qty').val();
            if (price && qty) {
                price = parseInt(price.replace(/\./g, '').replace(/,/g, '.'));
                qty = parseInt(qty.replace(/\./g, '').replace(/,/g, '.'));
                const total = price*qty;
                if (total >= 0) {
                    set.find('.budget-total-input').val(total);
                    new Cleave(set.find('.budget-total'), {
                        numeral: true,
                        numeralThousandsGroupStyle: 'thousand', numeralDecimalMark: ',', delimiter: '.'
                    }).setRawValue(total);

                    calculateTotal();
                    set.find('.budget-total').removeClass('text-danger');
                } else {
                    set.find('.budget-total, budget-total-input').val();
                    set.find('.budget-total').addClass('text-danger');
                }
            } else {
                set.find('.budget-total, .budget-total-input').val('');
            }
        }

        function calculateTotal() {
            let grandTotal = 0;

            $('.budget-total-input').each(function() {
                const value = parseFloat($(this).val()) || 0;
                grandTotal += value;
            });

            new Cleave($('.grand-total'), {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand', numeralDecimalMark: ',', delimiter: '.'
            }).setRawValue(grandTotal);
            $('.grand-total-input').val(grandTotal);
        }

        $('#anggaran-repeater-1').on('change', '.budget-price, .budget-qty', function () {
            const set = $(this).closest('[data-repeater-item]');
            calculateAnggaran(set);
        });

        $('.budget-price, .budget-qty').trigger('change');
    });
</script>