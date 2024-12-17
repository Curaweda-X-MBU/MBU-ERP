<style>
    table tfoot tr td {
        padding: 0px 10px 0px 10px !important;
    }
</style>

<div class="table-responsive">
    <table class="table table-bordered w-100 no-wrap text-center" id="bw-repeater">
        <thead>
            <th>Berat (Kg)</th>
            <th>Jumlah</th>
            <th>Total Rataan</th>
            <th>Satuan</th>
            <th colspan="2">
                <button class="btn btn-sm btn-icon btn-primary add-bw" type="button" data-repeater-create title="Tambah Data">
                    <i data-feather="plus"></i>
                </button>
            </th>
        </thead>
        <tbody data-repeater-list="bw">
            <tr data-repeater-item>
                <td><input type="text" name="weight" class="form-control numeral-mask weight" value="0" required/></td>
                <td><input type="text" name="total" class="form-control numeral-mask total" value="0" required/></td>
                <td>
                    <input type="text" name="weight_calc" class="form-control-plaintext weight_calc numeral-mask" readonly/>
                    <input type="hidden" name="weight_calc_input">
                </td>
                <td>Ekor</td>
                <td>
                    <button class="btn btn-sm btn-icon btn-danger" data-repeater-delete type="button" title="Hapus Fase">
                        <i data-feather="x"></i>
                    </button>
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-right">Rata-rata Berat</td>
                <td>
                    <input type="text" class="avg_weight form-control-plaintext text-right numeral-mask" readonly/>
                    <input type="hidden" name="avg_weight" />
                </td>
            </tr>
            <tr>
                <td colspan="4" class="text-right">Jumlah Ekor</td>
                <td>
                    <input type="text" class="total_chick form-control-plaintext text-right numeral-mask" readonly/>
                    <input type="hidden" name="total_chick" />
                </td>
            </tr>
            <tr>
                <td colspan="4" class="text-right">Jumlah Berat</td>
                <td>
                    <input type="text" class="total_calc form-control-plaintext text-right numeral-mask" readonly/>
                    <input type="hidden" name="total_calc" />
                </td>
            </tr>
            <tr>
                <td colspan="4" class="text-right">Bobot Rataan</td>
                <td>
                    <input type="text" class="value form-control-plaintext text-right numeral-mask" readonly/>
                    <input type="hidden" name="value" />
                </td>
            </tr>
        </tfoot>
    </table>
</div>

<script>
    $(function () {
        const optBw = {
            initEmpty: true,
            show: function (e) {
                var $this = $(this);
                $this.slideDown();
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

        const $repeaterBw = $('#bw-repeater').repeater(optBw);
        $('.add-bw').trigger('click');

        $('#bw-repeater').on('change', '.weight, .total', function () {
            const set = $(this).closest('[data-repeater-item]');
            calculateWeight(set);
        });

        function calculateWeight(set) {
            let weight = set.find('.weight').val();
            let qty = set.find('.total').val();
            if (weight && qty) {
                weight = parseFloat(weight.replace(/\./g, '').replace(/,/g, '.'));
                qty = parseInt(qty.replace(/\./g, '').replace(/,/g, '.'));
                const weightCalc = weight*qty;
                
                if (weightCalc >= 0) {
                    set.find('.weight_calc_input').val(weightCalc);
                    new Cleave(set.find('.weight_calc'), {
                        numeral: true,
                        numeralThousandsGroupStyle: 'thousand', numeralDecimalMark: ',', delimiter: '.'
                    }).setRawValue(weightCalc);

                    calculateTotal();
                    set.find('.weight_calc').removeClass('text-danger');
                } else {
                    set.find('.weight_calc, weight_calc_input').val();
                    set.find('.weight_calc').addClass('text-danger');
                }
            } else {
                set.find('.weight_calc, .weight_calc_input').val('');
            }
        }

        function calculateTotal() {
            let totalWeight = 0;
            let totalChick = 0;
            let totalCalc = 0;
            
            function calculateTotalFromSelector(selector) {
                let total = 0;
                $(selector).each(function() {
                    const value = parseFloat($(this).val().replace(/\./g, '').replace(/,/g, '.')) || 0;
                    // console.log(value);
                    
                    total += value;
                });
                return total;
            }

            totalWeight = calculateTotalFromSelector('.weight');
            totalChick = calculateTotalFromSelector('.total');
            totalCalc = calculateTotalFromSelector('.weight_calc');
            
            const avgWeight = totalWeight / $('.weight').length;

            function applyCleave(inputName, value) {
                $(`input[name="${inputName}"]`).val(value);
                new Cleave($(`.${inputName}`), {
                    numeral: true,
                    numeralThousandsGroupStyle: 'thousand', numeralDecimalMark: ',', delimiter: '.'
                }).setRawValue(value);
            }

            applyCleave('avg_weight', avgWeight);
            applyCleave('total_chick', totalChick);
            applyCleave('total_calc', totalCalc);
            
            const bwValue = totalCalc / totalChick;
            applyCleave('value', bwValue);
        }

        const dataRecording = @json($data);
        
        if (dataRecording && dataRecording.recording_bw[0]) {
            const dataBwList = dataRecording.recording_bw[0].recording_bw_list;
            dataBwList.forEach(item => {
                item.weight = item.weight.replace('.', ',');
            });
            // console.log('data bw: ', dataBwList);
            $repeaterBw.setList(dataBwList);
            $('.weight, .total').trigger('change');
        }

    });
</script>