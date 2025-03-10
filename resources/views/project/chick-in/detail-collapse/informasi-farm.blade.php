<style>
    th {
        padding-left: 10px !important;
        padding-right: 10px !important;
    }
</style>

<div class="card mb-1">
    <div id="headingCollapse1" class="card-header color-header collapsed" data-toggle="collapse" role="button" data-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
        <span class="lead collapse-title"> Informasi  Kandang </span>
    </div>
    <div id="collapse1" role="tabpanel" aria-labelledby="headingCollapse1" class="collapse show" aria-expanded="true">
        <div class="card-body p-2">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered w-100">
                        <thead>
                            <th>Unit Bisnis</th>
                            <th>Area</th>
                            <th>Lokasi</th>
                            <th>Kategori Produk</th>
                            <th>Kandang</th>
                            <th>Periode</th>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $data->kandang->company->name??'' }}</td>
                                <td>{{ $data->kandang->location->area->name??'' }}</td>
                                <td>{{ $data->kandang->location->name??'' }}</td>
                                <td>{{ $data->product_category->name??'' }}</td>
                                <td>{{ $data->kandang->name??'' }}</td>
                                <td>{{ $data->period }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {

    });
</script>