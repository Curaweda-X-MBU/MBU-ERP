<div class="card mb-1">
    <div id="headingCollapse2" class="card-header color-header collapsed" data-toggle="collapse" role="button" data-target="#collapse2" aria-expanded="true" aria-controls="collapse2">
        <span class="lead collapse-title"> Informasi  Farm </span>
    </div>
    <div id="collapse2" role="tabpanel" aria-labelledby="headingCollapse2" class="collapse show" aria-expanded="true">
        <div class="card-body p-2">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered w-100">
                        <thead>
                            <th>Kandang</th>
                            <th>Jenis Farm</th>
                            <th>Kapasitas</th>
                            <th>Periode</th>
                            <th>Penanggung Jawab</th>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $data->kandang->name??'' }}</td>
                                <td>{{ $type[$data->farm_type] }}</td>
                                <td>{{ number_format($data->capacity, '0', ',', '.') }}</td>
                                <td>{{ $data->period }}</td>
                                <td>{{ $data->kandang->user->name??'' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        
    });
</script>