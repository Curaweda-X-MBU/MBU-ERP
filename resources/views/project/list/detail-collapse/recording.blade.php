<div class="card mb-1">
    <div id="headingCollapse5" class="card-header color-header collapsed" data-toggle="collapse" role="button" data-target="#collapse5" aria-expanded="true" aria-controls="collapse5">
        <span class="lead collapse-title"> Recording </span>
    </div>
    <div id="collapse5" role="tabpanel" aria-labelledby="headingCollapse5" class="collapse show" aria-expanded="true">
        <div class="card-body p-2">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered w-100">
                        <thead>
                            <th>Item</th>
                            <th>Satuan</th>
                            <th>Interval Recording</th>
                        </thead>
                        <tbody>
                            @if ($data->project_recording)
                                @foreach ($data->project_recording as $item)
                                    <tr>
                                        <td>{{ $item->item }}</td>
                                        <td>{{ $item->unit_name }}</td>
                                        <td>{{ $item->interval }}</td>
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
    $(document).ready(function () {
    });
</script>