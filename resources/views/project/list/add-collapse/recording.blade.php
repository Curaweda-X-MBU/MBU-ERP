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
                                <button class="btn btn-sm btn-icon btn-primary" id="add-btn" type="button" data-repeater-create title="Tambah Recording">
                                    <i data-feather="plus"></i>
                                </button>
                            </th>
                        </thead>
                        <tbody data-repeater-list="recording">
                            <tr data-repeater-item>
                                <td><input type="text" name="item" class="form-control" aria-describedby="phase" placeholder="Item" required/></td>
                                <td>
                                    <select name="uom_id" class="form-control uom_id" required>
                                    </select>
                                </td>
                                <td>
                                    <select name="interval" class="form-control interval" required>
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
            initEmpty: true,
            show: function () {
                $(this).slideDown();
                // Feather Icons
                if (feather) {
                    feather.replace({ width: 14, height: 14 });
                }

                let optInterval = '';
                @json($recording_interval).forEach(element => {
                    optInterval += `<option value="${element}">${element}</option>`
                });
                
                $(this).find('.interval').html(`<option disabled selected>Pilih interval</option>${optInterval}`);
                $(this).find('.uom_id').select2({
                    placeholder: "Pilih Satuan",
                    ajax: {
                        url: '{{ route("data-master.uom.search") }}', 
                        dataType: 'json',
                        delay: 250, 
                        data: function(params) {
                            return {
                                q: params.term 
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data,
                            };
                        },
                        cache: true
                    }
                });
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
            for (let i = 0; i < oldRecording.length; i++) {
                $(`select[name="recording[${i}][interval]"]`).append(`<option value="${dataRecording[i].interval}" selected>${dataRecording[i].interval}</option>`);
                $(`select[name="recording[${i}][uom_id]"]`).append(`<option value="${oldRecording[i].uom_id}" selected>${oldRecording[i].uom_name}</option>`);
                $(`select[name="recording[${i}][uom_id]"]`).trigger('change');
            }
        } 

        if ('{{ $dataRecording }}'.length) {
            const dataRecording = @json($dataRecording);
            if (dataRecording) {
                console.log(dataRecording);
                
                $recordingRepeater.setList(dataRecording);
                for (let i = 0; i < dataRecording.length; i++) {
                    $(`select[name="recording[${i}][interval]"]`).append(`<option value="${dataRecording[i].interval}" selected>${dataRecording[i].interval}</option>`);
                    if (dataRecording[i].uom_id) {
                        $(`select[name="recording[${i}][uom_id]"]`).append(`<option value="${dataRecording[i].uom_id}" selected>${dataRecording[i].uom.name}</option>`);
                    }
                    $(`select[name="recording[${i}][uom_id]"]`).trigger('change');
                }
            }
        }

        if (!oldRecording && @json($dataRecording).length === 0) {
            $('#add-btn').trigger('click');
        }
    });
</script>