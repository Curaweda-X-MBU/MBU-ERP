@extends('templates.main')
@section('title', $title)
@section('content')

@php
    $action = route('project.recording.add');
    if (isset($data)) {
        $action = route('project.recording.edit', $data->recording_id);
    } else {
        $data = null;
    }
@endphp

<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">

<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/forms/repeater/jquery.repeater.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/forms/cleave/cleave.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js')}}"></script>


                <form class="form form-horizontal" id="formAudit" method="post" action="{{ $action }}">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">{{$title}}</h4>
                                </div>
                                <div class="card-body">
                                    {{ csrf_field() }}
                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-md-6 mt-1">
                                                <div class="row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="company_id" class="float-right">Unit Bisnis</label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        @if ($data)
                                                        <input type="text" class="form-control" value="{{ $data->project->kandang->company->name??'' }}" readonly>
                                                        @else
                                                        <select name="company_id" id="company_id" class="form-control" required>
                                                        </select>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="row mt-1">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="location_id" class="float-right">Lokasi Farm</label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        @if ($data)
                                                        <input type="text" class="form-control" value="{{ $data->project->kandang->location->name??'' }}" readonly>
                                                        @else
                                                        <select id="location_id" class="form-control" required>
                                                            <option disabled selected>Pilih unit bisnis terlebih dahulu</option>
                                                        </select>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="row mt-1">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="project_id" class="float-right">Project</label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        @if ($data)
                                                        <input type="text" class="form-control" value="{{ $data->project->kandang->name??'' }}" readonly>
                                                        <input type="hidden" class="chickin-date" value="{{ $data->project->project_chick_in[0]->chickin_date }}">
                                                        @else
                                                        <select name="project_id" id="project_id" class="form-control" required>
                                                            <option disabled selected>Pilih lokasi terlebih dahulu</option>
                                                        </select>
                                                        <input type="hidden" class="chickin-date">
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="row mt-1">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="project_id" class="float-right">Standar Fcr</label>
                                                    </div>
                                                    <div class="col-sm-9" id="show-fcr">
                                                       
                                                    </div>
                                                </div>
                                                <div class="row mt-1">
                                                    <div class="col-sm-3">
                                                        <label for="standard_mortality" class="float-right">Standar Mortalitas</label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <h4><span id="std-mortality">{{ $data->project->standard_mortality??'' }}</span> %</h4>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mt-1">
                                                <div class="row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="product_category_id" class="float-right">Kategori Produk</label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        @if ($data)
                                                        <input type="text" class="form-control" value="{{ $data->project->product_category->name??'' }}" readonly>
                                                        @else
                                                        <input type="text" class="form-control" placeholder="Produk" id="product_category_name" readonly>
                                                        <input type="hidden" id="product_category_id" name="product_category_id">
                                                        <input type="hidden" id="product_category_code">
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="row mt-1">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="warehouse_id" class="float-right">Gudang</label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        @if ($data)
                                                        @foreach ($data->project->kandang->warehouse??[] as $item)
                                                            @if ($item->type === 2)
                                                            <input type="text" class="form-control" readonly value="{{ $item->name }}">
                                                            <input type="hidden" id="warehouse_id" value="{{ $item->warehouse_id }}">
                                                            @endif
                                                        @endforeach
                                                        @else
                                                        <input type="text" class="form-control" placeholder="Gudang" id="warehouse_name" readonly>
                                                        <input type="hidden" id="warehouse_id" name="warehouse_id">
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="row mt-1">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="record_datetime" class="float-right">Tanggal Record</label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        @if ($data)
                                                        <input type="text" class="form-control record-date" value="{{ date('d-M-Y H:i', strtotime($data->record_datetime)) }}" readonly>
                                                        @else
                                                        <input type="text" class="form-control flatpickr-basic record-date" name="record_datetime" placeholder="Tanggal Record" required>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="row mt-1">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="day" class="float-right">Umur (Hari)</label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        @if ($data)
                                                        <input type="text" class="form-control" name="day" value="{{ $data->day }}" readonly>
                                                        @else
                                                        <input type="text" class="form-control day-old" placeholder="Umur" value="" readonly>
                                                        <input type="hidden" class="day-old" name="day" required>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="row mt-1">
                                                    <div class="col-sm-3">
                                                        <label for="total_chicken" class="text-right">Jumlah ayam saat ini</label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <input type="number" class="total-chicken form-control" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 mt-3">
                                        <li class="mb-2">
                                            <span style="font-size: 15px;"><b>Persediaan</b></span>
                                            @include('project.recording.stock')
                                            <hr>
                                        </li>
                                        <li class="mb-2">
                                            <span style="font-size: 15px;"><b>Non Persediaan</b></span>
                                            @include('project.recording.nonstock')
                                            <hr>
                                        </li>
                                        <li class="mb-2">
                                            <span style="font-size: 15px;"><b>Body Weight</b></span>
                                            @include('project.recording.bw')
                                            <hr>
                                        </li>
                                        <li class="mb-2">
                                            <span style="font-size: 15px;"><b>Deplesi</b></span>
                                            @include('project.recording.depletion')
                                        </li>
                                        <li class="mb-2" id="egg-section">
                                            <span style="font-size: 15px;"><b>Telur</b></span>
                                            @include('project.recording.egg')
                                        </li>
                                    </div>
                                    <div class="col-12 mt-4">
                                        <div class="text-right">
                                            <button id="submitForm" type="submit" class="btn btn-primary mr-1 waves-effect waves-float waves-light">Simpan</button>
                                            <a href="{{ route('project.recording.index') }}" class="btn btn-outline-warning waves-effect">Batal</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="modal fade text-left" id="showFcr" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="myModalLabel1">Detail Standar FCR <span id="fcr-name"></span></h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="col-12">
                                    <div class="row">
                                        <div class="table-responsive">
                                            <table class="table table-bordered w-100 no-wrap text-center">
                                                <thead>
                                                    <tr>
                                                        <th rowspan="2">Umur<br>(Hari)</th>
                                                        <th rowspan="2">Bobot</th>
                                                        <th colspan="2">Peningkatan</th>
                                                        <th colspan="2">Asupan</th>
                                                        <th rowspan="2">FCR</th>
                                                    </tr>
                                                    <tr>
                                                        <th>Harian</th>
                                                        <th>Rata - rata</th>
                                                        <th>Harian</th>
                                                        <th>Kumulatif</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
                    <script>
                        $(document).ready(function() {
                            $('#showFcr').on('show.bs.modal', function (event) {
                                var button = $(event.relatedTarget) 
                                var id = button.data('id')
                                var modal = $(this)

                                $.ajax({
                                    type: "get",
                                    url: `{{ route('data-master.fcr.search-standard') }}?fcr_id=${id}`,
                                    beforeSend: function () {
                                        modal.find('table tbody').html('');
                                    },
                                    success: function (response) {
                                        let html = '';
                                        modal.find('#fcr-name').html(`- ${response.name}`);
                                        const arrFcr = response.fcr_standard;
                                        
                                        arrFcr.forEach(val => {
                                            html += `<tr>
                                                        <td>${formatMoney(val.day)}</td>
                                                        <td>${formatMoney(val.weight)}</td>
                                                        <td>${formatMoney(val.daily_gain)}</td>
                                                        <td>${formatMoney(val.avg_daily_gain)}</td>
                                                        <td>${formatMoney(val.daily_intake)}</td>
                                                        <td>${formatMoney(val.cum_intake)}</td>
                                                        <td>${formatMoney(val.fcr)}</td>
                                                    </tr>`;
                                        });
                                        modal.find('table tbody').html(html);

                                    }
                                });
                            });

                            function formatMoney(amount) {
                                const number = parseFloat(amount);
                                const formatted = number.toFixed(2) 
                                    .replace('.', ',')   
                                    .replace(/\B(?=(\d{3})+(?!\d))/g, '.'); 
                                return formatted.replace(/,00$/, '');
                            }

                            $('#submitForm').click(function(event) {
                                var confirmation = confirm('Apakah kamu yakin ingin menyimpan data ini ?');
                                if (!confirmation) {
                                    event.preventDefault();
                                }
                            });

                            const dateOpt = { dateFormat: 'd-M-Y' }
                            $('.flatpickr-basic').flatpickr({
                                dateFormat: "Y-m-d",
                                altInput: true,
                                altFormat: "d-m-Y H:i",
                                enableTime: true,
                                time_24hr: true,
                                defaultDate: new Date(new Date().setDate(new Date().getDate() - 1))
                            });

                            $('#company_id').select2({
                                placeholder: "Pilih Unit Bisnis",
                                ajax: {
                                    url: '{{ route("data-master.company.search") }}', 
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

                            $('#company_id').change(function (e) { 
                                e.preventDefault();
                                setEmpty();
                                var companyId = $('#company_id').val();

                                $('#location_id').select2({
                                    placeholder: "Pilih Lokasi",
                                    ajax: {
                                        url: `{{ route("data-master.location.search") }}?company_id=${companyId}`, 
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

                                $('#location_id').change(function (e) { 
                                    const locationId = $('#location_id').val();
                                    $('#project_id').val(null).trigger('change');

                                    $('#project_id').select2({
                                        placeholder: "Pilih Project",
                                        ajax: {
                                            url: `{{ route("project.list.search") }}?location_id=${locationId}&chickin_status=3&project_status=2`, 
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
                                });

                            });

                            $('#project_id').on('select2:select', function (e) { 
                                e.preventDefault();
                                
                                const selectedData = e.params.data.data;
                                console.log(selectedData);
                                
                                setEmpty($(this).val());
                                $('#product_category_name').val(selectedData.product_category.name);
                                $('#product_category_code').val(selectedData.product_category.category_code);
                                $('#product_category_id').val(selectedData.product_category_id);

                                if (selectedData.product_category.category_code === 'TLR') {
                                    $('#egg-section').css('display', 'block');
                                } else {
                                    $('#egg-section').css('display', 'none');
                                }

                                const warehouses = selectedData.kandang.warehouse;
                                if (warehouses.length > 0) {
                                    warehouses.forEach(val => {
                                        if (val.type === 2) {
                                            $('#warehouse_name').val(val.name);
                                            $('#warehouse_id').val(val.warehouse_id);
                                        }
                                    });
                                } else {
                                    setEmpty();
                                    alert(`Kandang ${selectedData.kandang.name} belum memiliki gudang.`);
                                }

                                if (selectedData.project_chick_in) {
                                    const chickinData = selectedData.project_chick_in[0];
                                    const chickinDate = selectedData.project_chick_in[0].chickin_date;
                                    const recordDate = $('.record-date').val();
                                    $('.chickin-date').val(chickinDate);
                                    diffDayOldChick(recordDate, chickinDate);

                                    const existRecord = selectedData.recording;
                                    let remainChick = 0;
                                    if (existRecord.length > 0) {
                                        const lastRecord = existRecord.sort((a, b) => b.day - a.day);
                                        remainChick = lastRecord[0].total_chick - lastRecord[0].total_depletion;
                                    } else {
                                        remainChick = chickinData.total_chickin;
                                    }
                                    $('.total-chicken').val(remainChick);
                                }

                                if (selectedData.fcr_id && selectedData.fcr) {
                                    $('#show-fcr').html(`<button type="button" class="btn btn-sm btn-outline-primary" data-id=" ${selectedData.fcr_id} " data-toggle="modal" data-target="#showFcr">
                                                            ${selectedData.fcr.name}
                                                        </button>`);
                                }

                                if (selectedData.standard_mortality) {
                                    $('#std-mortality').html(selectedData.standard_mortality);
                                } 
                            });

                            function diffDayOldChick(recordDate, chickinDate) {
                                if (chickinDate && recordDate) {
                                    const diffTime =  new Date(recordDate) - new Date(chickinDate);
                                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                                    if (diffDays <= 0) {
                                        alert('Umur ayam belum 1 hari');
                                        $('.day-old').val('');                                    
                                        return false;
                                    } else {
                                        $('.day-old').val(diffDays);                                    
                                    }
                                } 
                            }

                            $('.record-date').change(function (e) { 
                                e.preventDefault();
                                const chickinDate = $('.chickin-date').val();
                                const selectedDate = $(this).val();
                                diffDayOldChick(selectedDate, chickinDate);
                            });

                            function setEmpty (projectId = null) {
                                if (!projectId) {
                                    $('#location_id').val(null).trigger('change');
                                    // $('#project_id').val(null).trigger('change');
                                }
                                $('#product_category_name').val('');
                                $('#product_category_id').val('');
                                $('#warehouse_name').val('');
                                $('#warehouse_id').val('');
                                $('#egg-section').css('display', 'none');
                                $('#show-fcr').html('');
                                $('.day-old').val('');
                            }

                            var numeralMask = $('.numeral-mask');
                            if (numeralMask.length) {
                                numeralMask.each(function() { 
                                    new Cleave(this, {
                                        numeral: true,
                                        numeralThousandsGroupStyle: 'thousand', numeralDecimalMark: ',', delimiter: '.'
                                    });
                                })
                            }
                        });
                    </script>
@endsection