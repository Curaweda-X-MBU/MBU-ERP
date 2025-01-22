<style>
    #tbl-mortality input {
        width: 100px;
        text-align: center;
    }

    #tbl-mortality td {
        padding: 10px;
    }
</style>

<div class="form-group row">
    <div class="table-responsive">
        <table class="table table-bordered table-striped w-100" id="tbl-mortality">
            <thead>
            <tr>
                <th>Umur<br>(Hari)</th>
                <th>1</th>
                <th>2</th>
                <th>3</th>
                <th>4</th>
                <th>5</th>
                <th>6</th>
                <th>7</th>
                <th>Jumlah</th>
            </tr>
            <tr>
                <th>Jumlah Mati</th>
                @for ($i = 1; $i <= 7; $i++)
                    @if (isset($data) && isset($data->ph_mortality))
                        @php
                            $currentData = collect($data->ph_mortality)->firstWhere('day', $i);
                        @endphp
                    @endif
                    <td><input type="number" class="form-control input-death" name="mortality[{{$i}}][death]" value="{{ isset($currentData)?$currentData['death']:old('mortality.'.$i.'.death', 0) }}" required></td>
                @endfor
                <td>
                    <input type="text" class="form-control" id="total_death" disabled value="{{ isset($data->total_deaths)?$data->total_deaths:old('total_deaths') }}">
                    <input type="hidden" name="total_deaths" id="total_death_input" value="{{ isset($data->total_deaths)?$data->total_deaths:old('total_deaths') }}">
                </td>
            </tr>
            <tr>
                <th>Culling</th>
                @for ($i = 1; $i <= 7; $i++)
                    @if (isset($data) && isset($data->ph_mortality))
                        @php
                            $currentData = collect($data->ph_mortality)->firstWhere('day', $i);
                        @endphp
                    @endif
                    <td><input type="number" class="form-control input-culling" name="mortality[{{$i}}][culling]" value="{{ isset($currentData)?$currentData['culling']:old('mortality.'.$i.'.culling', 0) }}" required></td>
                @endfor
                <td>
                    <input type="text" class="form-control" id="total_culling" disabled value="{{ isset($data->total_culling)?$data->total_culling:old('total_culling') }}">
                    <input type="hidden" name="total_culling" id="total_culling_input" value="{{ isset($data->total_culling)?$data->total_culling:old('total_culling') }}">
                </td>
            </tr>
            </thead>
        </table>
    </div>
</div>

<script>
    $(function () {
        function calculateTotal(selector, selectorTotal, selectorTotalInput) {
            let total = 0;
            $(selector).each(function () {
                const value = parseFloat($(this).val()) || 0;
                total += value;
            });

            $(selectorTotal).val(total);
            $(selectorTotalInput).val(total);
        }

        $('.input-death').on('input change', function() {
            calculateTotal('.input-death', '#total_death', '#total_death_input');
        }); 

        $('.input-culling').on('input change', function() {
            calculateTotal('.input-culling', '#total_culling', '#total_culling_input');
        }); 
    });
</script>