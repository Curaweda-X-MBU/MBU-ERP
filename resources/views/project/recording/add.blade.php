@extends('templates.main')
@section('title', $title)
@section('content')
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">

<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/forms/repeater/jquery.repeater.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/forms/cleave/cleave.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js')}}"></script>


                <form class="form form-horizontal" id="formAudit" method="post" action="{{ route('project.recording.add') }}">
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
                                                        <select name="company_id" id="company_id" class="form-control" required>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row mt-1">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="project_id" class="float-right">Project</label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <select name="project_id" id="project_id" class="form-control" required>
                                                            <option disabled selected>Pilih unit bisnis terlebih dahulu</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mt-1">
                                                <div class="row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="product_category_id" class="float-right">Kategori Produk</label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <input type="text" class="form-control" placeholder="Produk" id="product_category_name" readonly>
                                                        <input type="hidden" id="product_category_id" name="product_category_id">
                                                    </div>
                                                </div>
                                                <div class="row mt-1">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="warehouse_id" class="float-right">Gudang</label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <input type="text" class="form-control" placeholder="Gudang" id="warehouse_name" readonly>
                                                        <input type="hidden" id="warehouse_id" name="warehouse_id">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mt-1">
                                                <div class="row">
                                                    <div class="col-sm-3 col-form-label">
                                                        <label for="record_datetime" class="float-right">Tanggal Record</label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <input type="text" class="form-control flatpickr-basic" name="record_datetime" placeholder="Tanggal Record" required>
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
                                        <li class="mb-2">
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
                    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
                    <script>
                        $(document).ready(function() {
                            $('#submitForm').click(function(event) {
                                var confirmation = confirm('Apakah kamu yakin ingin menyimpan data ini ?');
                                if (!confirmation) {
                                    event.preventDefault();
                                }
                            });

                            const dateOpt = { dateFormat: 'd-M-Y' }
                            $('.flatpickr-basic').flatpickr({
                                dateFormat: "d-m-Y H:i",
                                enableTime: true,
                                time_24hr: true,
                                defaultDate: new Date()
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

                                $('#project_id').select2({
                                    placeholder: "Pilih Project",
                                    ajax: {
                                        url: `{{ route("project.list.search") }}?company_id=${companyId}&chickin_status=3&project_status=2`, 
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

                            $('#project_id').on('select2:select', function (e) { 
                                e.preventDefault();
                                
                                const selectedData = e.params.data.data;
                                setEmpty($(this).val());
                                $('#product_category_name').val(selectedData.product_category.name);
                                $('#product_category_id').val(selectedData.product_category_id);
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
                            });

                            function setEmpty (projectId = null) {
                                if (!projectId) {
                                    $('#project_id').val(null).trigger('change');
                                }
                                $('#product_category_name').val('');
                                $('#product_category_id').val('');
                                $('#warehouse_name').val('');
                                $('#warehouse_id').val('');
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