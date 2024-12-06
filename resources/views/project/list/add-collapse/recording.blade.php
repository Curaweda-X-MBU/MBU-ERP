@php
    $dataRecording = '';
    if (isset($data) && isset($data->project_recording)) {
        $dataRecording = $data->project_recording;
    }
@endphp

<div class="card mb-1">
    <div id="headingCollapse5" class="card-header color-header collapsed" data-toggle="collapse" role="button" data-target="#collapse5" aria-expanded="true" aria-controls="collapse5">
        <span class="lead collapse-title"> Recording </span>
    </div>
    <div id="collapse5" role="tabpanel" aria-labelledby="headingCollapse5" class="collapse show" aria-expanded="true">
        <div class="card-body p-2">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered w-100 text-center" id="recording-repeater">
                        <thead>
                            <th>Item</th>
                            <th>Satuan</th>
                            <th>Interval Recording</th>
                            <th colspan="2">
                                <button class="btn btn-sm btn-icon btn-primary" type="button" data-repeater-create title="Tambah Recording">
                                    <i data-feather="plus"></i>
                                </button>
                            </th>
                        </thead>
                        <tbody data-repeater-list="recording">
                            <tr data-repeater-item>
                                <td><input type="text" name="item" class="form-control" aria-describedby="phase" placeholder="Item" required/></td>
                                <td><input type="text" name="unit_name" class="form-control" aria-describedby="unit_name" placeholder="Satuan" required/></td>
                                <td>
                                    <select name="interval" class="form-control" required>
                                        <option disabled selected>Pilih interval</option>
                                        @foreach ($recording_interval as $item)
                                            <option value="{{ $item }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-icon btn-danger" data-repeater-delete type="button" title="Hapus Recording">
                                        <i data-feather="x"></i>
                                    </button>
                                </td>
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
        const optRecording = {
            show: function () {
                $(this).slideDown();
                // Feather Icons
                if (feather) {
                    feather.replace({ width: 14, height: 14 });
                }
            },
            hide: function (deleteElement) {
                if (confirm('Apakah kamu yakin ingin menghapus data ini?')) {
                    $(this).slideUp(deleteElement);
                }
            }
        };

        const $recordingRepeater = $('#recording-repeater').repeater(optRecording);
        const oldRecording = @json(old("recording"));
        if (oldRecording) {
            $recordingRepeater.setList(oldRecording);
        } 

        if ('{{ $dataRecording }}'.length) {
            const dataRecording = @json($dataRecording);
            if (dataRecording) {
                $recordingRepeater.setList(dataRecording);
            }
        }
    });
</script>