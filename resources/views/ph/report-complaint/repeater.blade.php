<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/pickers/form-pickadate.css')}}">

<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.time.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js')}}"></script>

@php
    $phChickIn = '';
    if (isset($data) && isset($data->ph_chick_in)) {
        $phChickIn = $data->ph_chick_in;
    }
@endphp


<div data-repeater-list="chick_in" id="data-repeater-list">
    <div data-repeater-item class="data-repeater-item">
        <div class="row d-flex align-items-end">
            <div class="col-md-2 col-12">
                <div class="form-group">
                    <label for="datepicker">Tanggal</label>
                    <input type="text" class="form-control flatpickr-basic" name="date" placeholder="Tanggal" required/>
                </div>
            </div>
            <div class="col-md-4 col-12">
                <div class="form-group">
                    <label for="travel_letter_number">No. Surat Jalan</label>
                    <input type="text" class="form-control" name="travel_letter_number" placeholder="No. Surat Jalan" required/>
                </div>
            </div>
            <div class="col-md-2 col-12">
                <div class="form-group">
                    <label for="delivery_time">Jam Kirim</label>
                    <input type="text" class="flatpickr-time delivery-time form-control text-left" name="delivery_time" placeholder="Jam Kirim" required/>
                </div>
            </div>
            <div class="col-md-2 col-12">
                <div class="form-group">
                    <label for="reception_time">Jam Terima</label>
                    <input type="text" class="flatpickr-time reception-time form-control text-left" name="reception_time" placeholder="Jam Terima" required/>
                </div>
            </div>
            <div class="col-md-2 col-12">
                <div class="form-group">
                    <label for="duration">Lama Perjalanan</label>
                    <input type="text" readonly class="form-control-plaintext duration-time" />
                    <input type="hidden" class="duration" name="duration" />
                </div>
            </div>
            <div class="col-md-2 col-12">
                <div class="form-group">
                    <label for="hatchery">Hatchery</label>
                    <select name="hatchery" class="form-control hatchery">
                        <option disabled selected>Pilih Vendor terlebih dahulu</option>
                    </select>
                </div>
            </div>
            <div class="col-md-2 col-12">
                <div class="form-group">
                    <label for="grade">Grade</label>
                    <select class="form-control" name="grade">
                        <option value="">Pilih Grade</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                    </select>
                </div>
            </div>
            <div class="col-md-2 col-12">
                <div class="form-group">
                    <label for="total_box">Box</label>
                    <input type="text" class="form-control" name="total_box" placeholder="Jumlah Box" required/>
                </div>
            </div>
            <div class="col-md-2 col-12">
                <div class="form-group">
                    <label for="total_heads">Ekor</label>
                    <input type="text" class="form-control" name="total_heads" placeholder="Jumlah Ekor" required/>
                </div>
            </div>
            <div class="col-md-2 col-12 mb-50">
                <div class="form-group">
                    <button class="btn btn-outline-danger text-nowrap px-1" data-repeater-delete type="button">
                        <i data-feather="x" class="mr-25"></i>
                        <span>Hapus</span>
                    </button>
                </div>
            </div>
        </div>
        <hr />
    </div>
</div>
<div class="row">
    <div class="col-12">
        <button class="btn btn-icon btn-primary" type="button" id="addbutton" data-repeater-create>
            <i data-feather="plus" class="mr-25"></i>
            <span>Tambah Data Chick In</span>
        </button>
    </div>
</div>
<script src="{{asset('app-assets/vendors/js/forms/repeater/jquery.repeater.min.js')}}"></script>

<script>
    $(function () {
        'use strict';
        let optRepeater = {
            show: function () {
                $(this).slideDown();
                if (feather) { feather.replace({ width: 14, height: 14 }); }
                const dateOpt = { dateFormat: 'd-M-Y' }
                const timeOpt = {
                    enableTime: true,
                    dateFormat: "H:i",
                    noCalendar: true,
                    time_24hr: true
                }
                $('.flatpickr-basic').flatpickr(dateOpt);
                $('.flatpickr-time').flatpickr(timeOpt);

                $('.flatpickr-calendar.hasTime.noCalendar').css({ 'width': '13rem' });
                var supplierId = $('#supplier_id').val();
                var qryHatchery = supplierId?`?supplier_id=${supplierId}`:'';
                var $hatcherySelector = $(this).find('.hatchery');
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
                    $hatcherySelector.html(`<option disabled selected>Pilih Vendor terlebih dahulu</option>`);
                }
            },
            hide: function (deleteElement) {
                if (confirm('Apakah kamu yakin ingin menghapus data chick-in ini ?')) {
                    $(this).slideUp(deleteElement);
                }
            },
            isFirstItemUndeletable: true
        }

        var $repeater = $('.invoice-repeater, .repeater-default').repeater(optRepeater);
        const oldChickIn = @json(old("chick_in"));
        if (oldChickIn) {
            $repeater.setList(oldChickIn);
            console.log('oldChickIn >>>> ', oldChickIn);
            
            for (let i = 0; i < oldChickIn.length; i++) {
                $(`select[name="chick_in[${i}][hatchery]"]`).append(`<option value="${oldChickIn[i].hatchery}" selected>${oldChickIn[i].hatchery}</option>`);
            }
        } 

        if ('{{ $phChickIn }}'.length) {
            const dataChickIn = @json($phChickIn);
            console.log('dataChickIn >>>> ', dataChickIn);
            
            if (dataChickIn) {
                $repeater.setList(dataChickIn);
                for (let i = 0; i < dataChickIn.length; i++) {
                    $(`select[name="chick_in[${i}][hatchery]"]`).append(`<option value="${dataChickIn[i].hatchery}" selected>${dataChickIn[i].hatchery}</option>`);
                }
            }
        }       
    });
</script>

<script>
    $(function () {
        const dateOpt = {
            dateFormat: 'd-M-Y'
        }
        const timeOpt = {
            enableTime: true,
            dateFormat: "H:i",
            noCalendar: true,
            time_24hr: true
        }
        $('.flatpickr-basic').flatpickr(dateOpt);
        $('.flatpickr-time').flatpickr(timeOpt);

        function calculateDuration(set) {
            const deliveryTime = set.find('.delivery-time').val();
            const receptionTime = set.find('.reception-time').val();

            if (deliveryTime && receptionTime) {
                const deliveryDate = new Date(`1970-01-01T${deliveryTime}:00`);
                const receptionDate = new Date(`1970-01-01T${receptionTime}:00`);

                const duration = (receptionDate - deliveryDate) / 1000 / 60; // duration in minutes

                if (duration >= 0) {
                    const hours = Math.floor(duration / 60);
                    const minutes = duration % 60;
                    set.find('.duration-time').val(`${hours} Jam ${minutes} Menit`);
                    set.find('.duration-time').removeClass('text-danger');
                    set.find('.duration').val(duration);
                } else {
                    set.find('.duration-time').val('Durasi error');
                    set.find('.duration-time').addClass('text-danger');
                    set.find('.duration').val();
                }
            } else {
                set.find('.duration-time').val('');
            }
        }

        $('#data-repeater-list').on('change', '.delivery-time, .reception-time', function () {
            const set = $(this).closest('.data-repeater-item');
            calculateDuration(set);
        });

        $('.delivery-time, .reception-time').trigger('change');

        $('.flatpickr-calendar.hasTime.noCalendar').css({
            'width': '13rem'
        });
    });
</script>