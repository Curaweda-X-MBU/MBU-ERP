<div class="table-responsive">
    <table class="table table-bordered w-100 no-wrap text-center" id="egg">
        <thead>
            <th>Bagus</th>
            <th>Jumbo</th>
            <th>kecil</th>
            <th>Retak</th>
            <th>Kotor</th>
            <th>Rusak / Pecah</th>
            <th>Total</th>
            <th>Satuan</th>
        </thead>
        <tbody>
            <tr data-repeater-item>
                <td><input type="text" name="good" class="increase form-control numeral-mask" value="0" required/></td>
                <td><input type="text" name="big" class="big form-control numeral-mask" value="0" required/></td>
                <td><input type="text" name="small" class="small form-control numeral-mask" value="0" required/></td>
                <td><input type="text" name="crack" class="crack form-control numeral-mask" value="0" required/></td>
                <td><input type="text" name="dirty" class="dirty form-control numeral-mask" value="0" required/></td>
                <td><input type="text" name="broken" class="broken form-control numeral-mask" value="0" required/></td>
                <td>
                    <input type="text" class="form-control-plaintext total numeral-mask" readonly/>
                    <input type="hidden" name="total">
                </td>
                <td>Butir</td>
            </tr>
        </tbody>
    </table>
</div>

<script>
    $(function () {
    });
</script>