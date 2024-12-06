<style>
    #tbl-upload-image tbody tr td {
        padding: 10px !important;
    }
    #tbl-upload-image thead th{
        text-align: left;
    }
</style>
<div class="form-group row">
    <div class="table-responsive">
        <table class="table table-striped w-100" id="tbl-upload-image">
            <thead>
                <th>Upload Foto (Max. 2 MB)*</th>   
                <th>Deskripsi*</th>   
                <th>Preview</th>   
                <th>#</th>   
            </thead>
            <tbody>
                @if (old('images'))
                    @foreach (old('images') as $key => $value)
                    <tr>
                        <td style="vertical-align: top; width: 30%;">
                            <input type="file" style="width: 25rem" class="form-control form-file" accept="image/*">
                            <span class="text-danger small file-error"></span>
                            <input type="hidden" name="images[{{ $key }}][file]" class="input-file" value="{{ $value['file'] }}">
                        </td>
                        <td style="vertical-align: top; width: 35%;">
                            <textarea rows="4" style="width: 30rem;" name="images[{{ $key }}][description]" class="form-control" placeholder="Deskripsi foto" required>{{ $value['description'] }}</textarea>
                        </td>
                        <td style=" text-align:center;" class="preview-image">
                            @if ($value['file'])
                            <img width="250" style="border-radius: 10px;" src="{{ route('file.show', ['filename' => $value["file"]]) }}" alt="ph-complaint-image">
                            @endif
                        </td>
                        <td style="vertical-align: top; text-align: center;">
                            @if ($key !== 0)
                            <button type="button" href="javascript:void(0)" class="btn btn-outline-danger text-nowrap px-1 btn-remove-image">
                                x&nbsp;
                                <span>Hapus</span>
                            </button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                @elseif(isset($data->images))
                    @foreach (json_decode($data->images) as $key => $value)
                    <tr>
                        <td style="vertical-align: top; width: 30%;">
                            <input type="file" style="width: 25rem" class="form-control form-file" accept="image/*">
                            <span class="text-danger small file-error"></span>
                            <input type="hidden" name="images[{{ $key }}][file]" class="input-file" value="{{ $value->file }}">
                        </td>
                        <td style="vertical-align: top; width: 35%;">
                            <textarea rows="4" style="width: 30rem;" name="images[{{ $key }}][description]" class="form-control" placeholder="Deskripsi foto" required>{{ $value->description }}</textarea>
                        </td>
                        <td style=" text-align:center;" class="preview-image">
                            @if ($value->file)
                            <img width="250" style="border-radius: 10px;" src="{{ route('file.show', ['filename' => $value->file]) }}" alt="ph-complaint-image">
                            @endif
                        </td>
                        <td style="vertical-align: top; text-align: center;">
                            @if ($key !== 0)
                            <button type="button" href="javascript:void(0)" class="btn btn-outline-danger text-nowrap px-1 btn-remove-image">
                                x&nbsp;
                                <span>Hapus</span>
                            </button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                @else
                <tr>
                    <td style="vertical-align: top; width: 30%;">
                        <input type="file" style="width: 25rem" class="form-control form-file" accept="image/*" required>
                        <span class="text-danger small file-error"></span>
                        <input type="hidden" name="images[0][file]" class="input-file">
                    </td>
                    <td style="vertical-align: top; width: 35%;">
                        <textarea rows="4" style="width: 30rem;" name="images[0][description]" class="form-control" placeholder="Deskripsi foto" required></textarea>
                    </td>
                    <td style=" text-align:center;" class="preview-image">
                    </td>
                    <td style="vertical-align: top; text-align: center;">
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <button class="btn btn-icon btn-primary" type="button" id="addEvidenceFoto">
            <i data-feather="plus" class="mr-25"></i>
            <span>Tambah Foto Bukti</span>
        </button>
    </div>
</div>

<script>
    $(function () {
        let index = $('#tbl-upload-image tbody tr').length;
        $('#addEvidenceFoto').click(function () {
            const newRow = $(`
                <tr style="display: none;">
                    <td style="vertical-align: top; width: 30%;">
                        <input type="file" style="width: 25rem" class="form-control form-file" accept="image/*" required>
                        <span class="text-danger small file-error"></span>
                        <input type="hidden" name="images[${index}][file]" class="form-control input-file">
                    </td>
                    <td style="vertical-align: top; width: 35%;">
                        <textarea rows="4" style="width: 30rem;" name="images[${index}][description]" class="form-control" placeholder="Deskripsi foto" required></textarea>
                    </td>
                    <td style=" text-align:center;" class="preview-image">
                    </td>
                    <td style="vertical-align: top; text-align: center;">
                        <button type="button" href="javascript:void(0)" class="btn btn-outline-danger text-nowrap px-1 btn-remove-image">
                            x&nbsp;
                            <span>Hapus</span>
                        </button>
                    </td>
                </tr>
            `);

            $('#tbl-upload-image tbody').append(newRow);
            newRow.fadeIn(300);
            index++;
        });

        $(document).on('click', '.btn-remove-image', function () {
            if (confirm('Apakah kamu yakin ingin menghapus data foto bukti ini ?')) {
                $(this).closest('tr').fadeOut(300, function() {
                    $(this).remove();
                    updateIndexes()
                });
            }
        });

        function updateIndexes() {
            $('#tbl-upload-image tbody tr').each(function(i) {
                $(this).find('input[type="hidden"]').attr('name', `images[${i}][file]`);
                $(this).find('textarea').attr('name', `images[${i}][description]`);
            });
            index = $('#tbl-upload-image tbody tr').length; 
            console.log(index);
        }

        $(document).on('change', '.form-file', function (e) {
            const $previewImage = $(this).closest('tr').find('.preview-image');
            const $removeButton = $(this).closest('tr').find('.btn-remove-image');
            const $errorText = $(this).closest('tr').find('.file-error');
            const $inputFile = $(this).closest('tr').find('input[type=file]');
            const $hiddenFile = $(this).closest('tr').find('.input-file');
            const $addButton = $('#addEvidenceFoto');

            const file = e.target.files[0];
            const formData = new FormData();
            formData.append('image', file);
            $.ajax({
                type: "POST",
                url: "{{ route('ph.report-complaint.upload-image') }}",
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $previewImage.html(`<span class="spinner-grow"></span>`);
                    $removeButton.attr('disabled', true);
                    $addButton.attr('disabled', true);
                    $inputFile.removeClass('is-invalid'); 
                    $errorText.text('');
                },
                success: function (response) {
                    $removeButton.removeAttr('disabled');
                    $addButton.removeAttr('disabled');
                    $inputFile.removeClass('is-invalid'); 
                    $errorText.text('');

                    const imagePath = response.data;
                    const fileUrl = `{{ url('show-file') }}/?filename=${imagePath}`;
                    $previewImage.html(`<img width="250" style="border-radius: 10px;" src="${fileUrl}" alt="ph-complaint-image">`);
                    $hiddenFile.val(imagePath);
                },
                error: function(xhr) {
                    $previewImage.html('');
                    $hiddenFile.val();
                    $removeButton.removeAttr('disabled');
                    $addButton.removeAttr('disabled');
                    $inputFile.addClass('is-invalid'); 
                    $inputFile.val(''); 
                    if (xhr.status === 422) { 
                        const errors = xhr.responseJSON.errors; 
                        $errorText.text(errors.image ? errors.image[0] : ''); 
                    } else {
                        $errorText.text('Internal server error');
                    }
                }
            });
        });
    });
</script>