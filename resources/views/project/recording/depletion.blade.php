<div class="table-responsive">
    <table class="table table-bordered w-100 no-wrap text-center" id="depletion">
        <thead>
            <th>Mati</th>
            <th>Culling</th>
            <th>Afkir</th>
            <th>Total</th>
            <th>Satuan</th>
        </thead>
        <tbody>
            <tr data-repeater-item>
                <td><input type="text" name="death" class="death form-control numeral-mask" value="0" required/></td>
                <td><input type="text" name="culling" class="culling form-control numeral-mask" value="0" required/></td>
                <td><input type="text" name="afkir" class="afkir form-control numeral-mask" value="0" required/></td>
                <td>
                    <input type="text" class="form-control-plaintext numeral-mask total-depletion" readonly/>
                    <input type="hidden" name="total_delpletion" class="total-input" required/>
                </td>
                <td>Ekor</td>
            </tr>
        </tbody>
    </table>
</div>

<script>
    $(function () {
        $('#depletion').on('change', '.death, .culling, .afkir', function () {
            let death = parseInt($('.death').val().replace(/\./g, '').replace(/,/g, '.'));
            let culling = parseInt($('.culling').val().replace(/\./g, '').replace(/,/g, '.'));
            let afkir = parseInt($('.afkir').val().replace(/\./g, '').replace(/,/g, '.'));
            let total = death+culling+afkir;
            $('.total-input').val(total);
            new Cleave($('.total-depletion'), {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand', numeralDecimalMark: ',', delimiter: '.'
            }).setRawValue(total);
        });
    });
</script>