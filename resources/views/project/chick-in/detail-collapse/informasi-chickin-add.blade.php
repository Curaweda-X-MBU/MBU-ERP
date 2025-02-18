@php
    $dataChick = '';
    if (isset($data)) {
        if (isset($data->project_chick_in)) {
            $dataChick = $data->project_chick_in;
        }
    }
@endphp

<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">

<script src="{{asset('app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js')}}"></script>

<div class="card mb-1">
    <div id="headingCollapse3" class="card-header color-header collapsed" data-toggle="collapse" role="button" data-target="#collapse3" aria-expanded="true" aria-controls="collapse3">
        <span class="lead collapse-title"> Informasi Chick In </span>
    </div>
    <div id="collapse3" role="tabpanel" aria-labelledby="headingCollapse3" class="collapse show" aria-expanded="true">
        <div class="card-body p-2">
            <div class="col-12">
                <div data-repeater-list="chick_in">
                    <div data-repeater-item>
                        <div class="row d-flex align-items-end">
                            <div class="col-md-4 col-12">
                                <div class="form-group">
                                    <label for="travel_letter_number">No. Surat Jalan</label>
                                    <input type="text" class="form-control" name="travel_letter_number" placeholder="No. Surat Jalan" value="{{ $travel_number??'' }}" required/>
                                </div>
                            </div>
                            <div class="col-md-4 col-12">
                                <div class="form-group">
                                    <label for="travel_letter_number">Dokumen Surat Jalan (Max. 2 MB)</label>
                                    <div id="file-div">
                                        @if ($travel_number_document)
                                        <div class="float-right">
                                            <a href="javascript:void(0)" id="remove-file" class="btn btn-outline-danger">
                                                <i data-feather='trash'></i>
                                            </a>
                                        </div>
                                        <a href="{{ route('file.show', ['filename' => $travel_number_document]) }}" class="btn btn-outline-primary" target="_blank">
                                            <i data-feather='download' class="mr-50"></i>
                                            <span>Download</span>
                                        </a>
                                        @else
                                        <input type="file" class="form-control" name="travel_letter_document" placeholder="Dokumen Surat Jalan" />
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-12">
                                <div class="form-group">
                                    <label for="chickin_date">Tanggal Chick In</label>
                                    <input type="text" class="form-control flatpickr-basic" name="chickin_date" placeholder="Tanggal Chick In" value="{{ $received_date??''}}" required/>
                                </div>
                            </div>
                        </div>
                        <div class="row d-flex align-items-end">
                            <div class="col-md-4 col-12">
                                <div class="form-group">
                                    <label for="supplier_id">Supplier</label>
                                    <select name="supplier_id" class="form-control supplier_id" required>
                                        @if ($supplier_id && $supplier_name)
                                            <option value="{{$supplier_id}}" selected>{{ $supplier_name }}</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 col-12">
                                <div class="form-group">
                                    <label for="hatchery">Hatchery</label>
                                    <select name="hatchery" class="form-control hatchery" required>
                                        <option disabled selected>Pilih Supplier terlebih dahulu</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class="form-group">
                                    <label for="total_chickin">Jumlah (Ekor)</label>
                                    <input type="text" class="form-control numeral-mask" placeholder="Jumlah (Ekor)" value="{{ $chickin_qty }}" readonly/>
                                    <input type="hidden" name="total_chickin" value="{{ $chickin_qty }}" readonly/>
                                </div>
                            </div>
                        </div>
                        <hr />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{{asset('app-assets/vendors/js/forms/cleave/cleave.min.js')}}"></script>

<script>
    $(function () {
        'use strict';
        var numeralMask = $('.numeral-mask');
        if (numeralMask.length) {
            numeralMask.each(function() { 
                new Cleave(this, {
                    numeral: true,
                    numeralThousandsGroupStyle: 'thousand', numeralDecimalMark: ',', delimiter: '.'
                });
            })
        }

        const dateOpt = { dateFormat: 'd-M-Y' }
        $('.flatpickr-basic').flatpickr(dateOpt);

        $('#remove-file').click(function (e) { 
            e.preventDefault();
            $('#file-div').html('<input type="file" class="form-control" name="travel_letter_document" placeholder="Dokumen Surat Jalan" />');
        });

        validationFile();
        $('.supplier_id').select2({
            placeholder: "Pilih Supplier",
            ajax: {
                url: '{{ route("data-master.supplier.search") }}', 
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

        var $hatcherySelector = $('.hatchery');
        $hatcherySelector.html(`<option disabled selected>Pilih Supplier terlebih dahulu</option>`);
        $('.supplier_id').change(function (e) { 
            e.preventDefault();
            var supplierId = $(this).val();
            var qryHatchery = supplierId?`?supplier_id=${supplierId}`:'';
            $hatcherySelector.val(null).trigger('change');
            if (qryHatchery.length > 0) {
                $hatcherySelector.select2({
                    placeholder: "Pilih Hatchery",
                    ajax: {
                        url: '{{ url("data-master/supplier/hatchery/search/") }}'+qryHatchery,
                        dataType: 'json',
                        delay: 250, 
                        data: function(params) {
                            return {
                                q: params.term 
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data
                            };
                        },
                        cache: true
                    }
                });
            } else {
                $hatcherySelector.html(`<option disabled selected>Pilih Supplier terlebih dahulu</option>`);
            }
        });
        
        $('.supplier_id').trigger('change');
        
        const optChick = {
            initEmpty: false,
            show: function (e) {
                var $this = $(this);
                $this.slideDown();
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

        const $repeaterChickIn = $('#chickin-repeater').repeater(optChick);
        const oldChickIn = @json(old("chick_in"));
        if (oldChickIn) {
            console.log(oldChickIn);
            
            $repeaterChickIn.setList(oldChickIn);
            for (let i = 0; i < oldChickIn.length; i++) {
                $(`select[name="chick_in[${i}][supplier_id]"]`).append(`<option value="${oldChickIn[i].supplier_id}" selected>${oldChickIn[i].supplier_name}</option>`);
                $(`select[name="chick_in[${i}][supplier_id]"]`).trigger('change');
                $(`select[name="chick_in[${i}][hatchery]"]`).append(`<option value="${oldChickIn[i].hatchery}" selected>${oldChickIn[i].hatchery}</option>`);
            }
        } 
        
        if ('{{ $dataChick }}'.length) {
            let dataChickIn = @json($dataChick);
            let arrFile = [];
            dataChickIn.forEach(item => {
                const date = new Date(item.chickin_date);
                const options = { day: '2-digit', year: 'numeric', month: 'short' };
                item.chickin_date = date.toLocaleDateString('en-GB', options).replace(/ /g, '-');
                arrFile.push({ 
                    file_name: item.travel_letter_document??null
                });
                delete item.travel_letter_document;
            });
            
            if (dataChickIn.length > 0) {
                $repeaterChickIn.setList(dataChickIn);
                for (let i = 0; i < dataChickIn.length; i++) {
                    $(`select[name="chick_in[${i}][supplier_id]"]`).append(`<option value="${dataChickIn[i].supplier_id}" selected>${dataChickIn[i].supplier.name}</option>`);
                    $(`select[name="chick_in[${i}][supplier_id]"]`).trigger('change');
                    $(`select[name="chick_in[${i}][hatchery]"]`).append(`<option value="${dataChickIn[i].hatchery}" selected>${dataChickIn[i].hatchery}</option>`);

                    if (arrFile[i].file_name) {
                        const fileName = arrFile[i].file_name;
                        $(`input[name="chick_in[${i}][travel_letter_document]"]`)
                            .closest('.file-div').html(`
                                <a href="{{ route('file.show', ['filename' => '__FILE_NAME__']) }}" target="_blank">
                                    <i data-feather='download' class="mr-50"></i>
                                    <span>Download</span>
                                </a>
                                <input type="hidden" class="hidden-name" name="chick_in[${i}][travel_letter_document]" value="${fileName}">
                                <div class="float-right">
                                    <a href="javascript:void(0)" class="delete-file text-danger" title="Hapus File">
                                        <i data-feather="trash"></i>
                                    </a>
                                </div>
                            `.replace('__FILE_NAME__', fileName));
                    }
                }
            }
        } 

        $('.delete-file').on('click', function () { 
            const inputName = $(this).closest('.file-div').find('input[type="hidden"]').attr('name');
            $(this).closest('.file-div').html(`<input type="file" class="form-control" name="${inputName}" accept=".pdf, image/jpeg">`)
            validationFile();
        });

        function validationFile() {
            $('input[type="file"]').on('change', function() {
                const file = this.files[0];
                if (file) {
                    const fileType = file.type;
                    const maxSize = 2 * 1024 * 1024;
                    const fileSize = file.size;
                    const allowedTypes = /^(application\/pdf|image\/(jpeg|jpg))$/;
                    if (!allowedTypes.test(fileType)) {
                        alert('Mohon upload file berformat PDF atau JPEG/JPG.');
                        $(this).val('');
                    } else if (fileSize > maxSize) {
                        alert('Ukuran file harus kurang dari 2 MB');
                        $(this).val('');
                    } 
                }
            });
        }
    });
</script>