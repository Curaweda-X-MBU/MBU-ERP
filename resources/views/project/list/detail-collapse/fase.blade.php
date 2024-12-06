
<div class="card mb-1">
    <div id="headingCollapse3" class="card-header color-header collapsed" data-toggle="collapse" role="button" data-target="#collapse3" aria-expanded="true" aria-controls="collapse3">
        <span class="lead collapse-title"> Fase </span>
    </div>
    <div id="collapse3" role="tabpanel" aria-labelledby="headingCollapse3" class="collapse show" aria-expanded="true">
        <div class="card-body p-2">
            <div class="col-12">
                <table class="mb-2">
                    <tr>
                        <td>Target FCR</td>
                        <td>:</td>
                        <td>{{ $data->fcr?$data->fcr->name.' - '.$data->fcr->value.' '.$data->fcr->uom->name : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Target Deplesi</td>
                        <td>:</td>
                        <td>{{ $data->target_depletion }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered w-100 no-wrap">
                        <thead>
                            <th>Fase</th>
                            <th>Estimasi Tgl. Mulai</th>
                            <th>Estimasi Tgl. Selesai</th>
                            <th>Status Fase</th>
                        </thead>
                        <tbody>
                            @if ($data->project_phase)
                                @foreach ($data->project_phase as $item)
                                    <tr>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ date('d F Y', strtotime($item->start_date_estimate)) }}</td>
                                        <td>{{ date('d F Y', strtotime($item->end_date_estimate)) }}</td>
                                        <td>Belum Mulai</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function () {
        
    });
</script>