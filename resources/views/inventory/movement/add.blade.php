@extends('templates.main')
@section('title', $title)
@section('content')
<script src="{{asset('app-assets/vendors/js/forms/repeater/jquery.repeater.min.js')}}"></script>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">{{$title}}</h4>
                                </div>
                                <div class="card-body">
                                    <form class="form form-horizontal" method="post" action="{{ route('inventory.movement.add') }}" enctype="multipart/form-data">
                                        {{ csrf_field() }}
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-md-6">
                                                        <div class="row">
                                                            <div class="col-sm-4 col-form-label">
                                                                <label class="float-right" for="company_id">Unit Bisnis</label>
                                                            </div>
                                                            <div class="col-sm-8">
                                                                <select name="company_id" id="company_id" class="form-control" required>
                                                                    @if(old('company_id'))
                                                                        <option value="{{ old('company_id') }}" selected="selected">{{ old('company_name') }}</option>
                                                                    @endif
                                                                </select>
                                                                @if ($errors->has('company_id'))
                                                                    <span class="text-danger small">{{ $errors->first('company_id') }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="row">
                                                            <div class="col-sm-4 col-form-label">
                                                                <label class="float-right" for="product_id">Alasan Transfer</label>
                                                            </div>
                                                            <div class="col-sm-8">
                                                                <textarea name="notes" class="form-control" placeholder="Alasan transfer" required>{{ old('notes') }}</textarea>
                                                                @if ($errors->has('notes'))
                                                                    <span class="text-danger small">{{ $errors->first('notes') }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <div class="card">
                                                            <div class="card-body" style="padding-bottom: 1px;">
                                                                <h4 class="card-title">Gudang Asal</h4>
                                                                <hr>
                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        <div class="form-group row">
                                                                            <div class="col-sm-3 col-form-label">
                                                                                <label for="origin_area_id">Area</label>
                                                                            </div>
                                                                            <div class="col-sm-9">
                                                                                <select name="area_id" id="origin_area_id" class="form-control" required>
                                                                                    <option selected disabled>Pilih Unit Bisnis terlebih dahulu</option>
                                                                                    @if(old('origin_area_id'))
                                                                                        <option value="{{ old('origin_area_id') }}" selected="selected">{{ old('origin_area_name') }}</option>
                                                                                    @endif
                                                                                </select>
                                                                                @if ($errors->has('origin_area_id'))
                                                                                    <span class="text-danger small">{{ $errors->first('origin_area_id') }}</span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <div class="form-group row">
                                                                            <div class="col-sm-3 col-form-label">
                                                                                <label for="origin_location_id">Lokasi</label>
                                                                            </div>
                                                                            <div class="col-sm-9">
                                                                                <select name="origin_location_id" id="origin_location_id" class="form-control" required>
                                                                                    <option selected disabled>Pilih Area terlebih dahulu</option>
                                                                                    @if(old('origin_location_id'))
                                                                                        <option value="{{ old('origin_location_id') }}" selected="selected">{{ old('origin_location_name') }}</option>
                                                                                    @endif
                                                                                </select>
                                                                                @if ($errors->has('origin_location_id'))
                                                                                    <span class="text-danger small">{{ $errors->first('origin_location_id') }}</span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <div class="form-group row">
                                                                            <div class="col-sm-3 col-form-label">
                                                                                <label for="origin_id">Gudang</label>
                                                                            </div>
                                                                            <div class="col-sm-9">
                                                                                <select name="origin_id" id="origin_id" class="form-control" required>
                                                                                    <option selected disabled>Pilih Lokasi terlebih dahulu</option>
                                                                                    @if(old('origin_id'))
                                                                                        <option value="{{ old('origin_id') }}" selected="selected">{{ old('origin_name') }}</option>
                                                                                    @endif
                                                                                </select>
                                                                                @if ($errors->has('origin_id'))
                                                                                    <span class="text-danger small">{{ $errors->first('origin_id') }}</span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <div class="form-group row">
                                                                            <div class="col-sm-3 col-form-label">
                                                                                <label for="product_id">Produk</label>
                                                                            </div>
                                                                            <div class="col-sm-9">
                                                                                <select name="product_id" id="product_id" class="form-control" required>
                                                                                    <option selected disabled>Pilih gudang asal terlebih dahulu</option>
                                                                                    @if(old('product_id'))
                                                                                        <option value="{{ old('product_id') }}" selected="selected">{{ old('product_name') }}</option>
                                                                                    @endif
                                                                                </select>
                                                                                <span class="text-muted small">Jumlah produk saat ini : <span class="text-muted" id="current-stock"></span> <span id="current-uom"></span></span>
                                                                                <input type="hidden" name="current_stock" id="current-stock-input">
                                                                                @if ($errors->has('product_id'))
                                                                                    <span class="text-danger small">{{ $errors->first('product_id') }}</span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <h4 class="card-title">Gudang Tujuan</h4>
                                                                <hr>
                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        <div class="form-group row">
                                                                            <div class="col-sm-3 col-form-label">
                                                                                <label for="destinationarea_id">Area</label>
                                                                            </div>
                                                                            <div class="col-sm-9">
                                                                                <select name="area_id" id="destination_area_id" class="form-control" required>
                                                                                    <option selected disabled>Pilih Unit Bisnis terlebih dahulu</option>
                                                                                    @if(old('destination_area_id'))
                                                                                        <option value="{{ old('destination_area_id') }}" selected="selected">{{ old('destination_area_name') }}</option>
                                                                                    @endif
                                                                                </select>
                                                                                @if ($errors->has('destination_area_id'))
                                                                                    <span class="text-danger small">{{ $errors->first('destination_area_id') }}</span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <div class="form-group row">
                                                                            <div class="col-sm-3 col-form-label">
                                                                                <label for="destination_location_id">Lokasi</label>
                                                                            </div>
                                                                            <div class="col-sm-9">
                                                                                <select name="destination_location_id" id="destination_location_id" class="form-control" required>
                                                                                    <option selected disabled>Pilih Area terlebih dahulu</option>
                                                                                    @if(old('destination_location_id'))
                                                                                        <option value="{{ old('destination_location_id') }}" selected="selected">{{ old('destination_location_name') }}</option>
                                                                                    @endif
                                                                                </select>
                                                                                @if ($errors->has('destination_location_id'))
                                                                                    <span class="text-danger small">{{ $errors->first('destination_location_id') }}</span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <div class="form-group row">
                                                                            <div class="col-sm-3 col-form-label">
                                                                                <label for="destination_id">Gudang</label>
                                                                            </div>
                                                                            <div class="col-sm-9">
                                                                                <select name="destination_id" id="destination_id" class="form-control" required>
                                                                                    <option selected disabled>Pilih Lokasi terlebih dahulu</option>
                                                                                    @if(old('destination_id'))
                                                                                        <option value="{{ old('destination_id') }}" selected="selected">{{ old('destination_name') }}</option>
                                                                                    @endif
                                                                                </select>
                                                                                @if ($errors->has('destination_id'))
                                                                                    <span class="text-danger small">{{ $errors->first('destination_id') }}</span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <div class="form-group row">
                                                                            <div class="col-sm-3 col-form-label">
                                                                                <label for="product_id">Jumlah Transfer</label>
                                                                            </div>
                                                                            <div class="col-sm-9">
                                                                                <input type="text" class="form-control numeral-mask" id="transfer_qty" placeholder="Jumlah transfer">
                                                                                <input type="hidden" name="transfer_qty" id="transfer_qty_input">
                                                                                @if ($errors->has('product_id'))
                                                                                    <span class="text-danger small">{{ $errors->first('product_id') }}</span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12 mb-4">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered w-100 no-wrap text-center" id="movement-vehicle">
                                                        <thead>
                                                            <th style="width: 20%;">Vendor</th>
                                                            <th>Plat Nomor</th>
                                                            <th>Nomor Surat Jalan</th>
                                                            <th style="width: 20%;">Dokumen (max. 2 MB)</th>
                                                            <th>Nama Sopir</th>
                                                            <th colspan="2">
                                                                <button class="btn btn-sm btn-icon btn-primary" type="button" id="add-btn" data-repeater-create title="Tambah Item">
                                                                    <i data-feather="plus"></i>
                                                                </button>
                                                            </th>
                                                        </thead>
                                                        <tbody data-repeater-list="movement_vehicle">
                                                            <tr data-repeater-item>
                                                                <td><select name="supplier_id" class="supplier_id form-control" required></select></td>
                                                                <td><input type="text" class="form-control" name="vehicle_number" placeholder="D 1234 ABC" required></td>
                                                                <td><input type="text" class="form-control" name="travel_document_number" placeholder="SJ-123" required></td>
                                                                <td><input type="file" name="travel_document" class="form-control" /></td>
                                                                <td><input type="text" name="driver_name" class="form-control" placeholder="Nama Sopir" required/></td>
                                                                <td>
                                                                    <button class="btn btn-sm btn-icon btn-danger" data-repeater-delete type="button" title="Hapus Item">
                                                                        <i data-feather="x"></i>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    @if ($errors->has('movement_vehicle'))
                                                        <span class="text-danger small">{{ $errors->first('movement_vehicle') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            <div class="col-12">
                                                <div class="text-center">
                                                    <button type="submit" class="btn btn-primary mr-1 waves-effect waves-float waves-light">Simpan</button>
                                                    <a href="{{ route('inventory.movement.index') }}" class="btn btn-outline-warning waves-effect">Batal</a>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <script src="{{asset('app-assets/vendors/js/forms/cleave/cleave.min.js')}}"></script>
                    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
                    <script>
                        $(document).ready(function() {
                            $('#transfer_qty').keyup(function (e) { 
                                const tfQty = parseInt($(this).val().replace(/\./g, ''), 10);
                                $('#transfer_qty_input').val(tfQty);
                            });

                            var numeralMask = $('.numeral-mask');
                            if (numeralMask.length) {
                                numeralMask.each(function() { 
                                    new Cleave(this, {
                                        numeral: true,
                                        numeralThousandsGroupStyle: 'thousand', numeralDecimalMark: ',', delimiter: '.'
                                    });
                                })
                            }

                            const optSelect2 = {
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

                            $('#company_id').select2({
                                placeholder: "Pilih Unit Bisnis",
                                ajax: {
                                    url: '{{ route("data-master.company.search") }}', 
                                    ...optSelect2
                                }
                            });

                            $('#company_id').change(function (e) { 
                                e.preventDefault();
                                const companyId = $(this).val();
                                const arrIdentifier = ['origin', 'destination'];
                                for (let i = 0; i < arrIdentifier.length; i++) {
                                    const val = arrIdentifier[i];
                                    $(`#${val}_area_id`).val(null).trigger('change');
                                    $(`#${val}_area_id`).select2({
                                        placeholder: "Pilih Area",
                                        ajax: {
                                            url: `{{ route("data-master.area.search") }}?company_id=${companyId}`, 
                                            ...optSelect2
                                        }
                                    });
                                    
                                    $(`#${val}_area_id`).change(function (e) { 
                                        e.preventDefault();
                                        $(`#${val}_location_id`).val(null).trigger('change');
                                        const areaId = $(this).val();
                                        $(`#${val}_location_id`).select2({
                                            placeholder: "Pilih Lokasi",
                                            ajax: {
                                                url: `{{ route("data-master.location.search") }}?area_id=${areaId}`, 
                                                ...optSelect2
                                            }
                                        });

                                        $(`#${val}_location_id`).change(function (e) { 
                                            e.preventDefault();
                                            $(`#${val}_id`).val(null).trigger('change');

                                            const locationId = $(this).val();
                                            $(`#${val}_id`).select2({
                                                placeholder: "Pilih Gudang",
                                                ajax: {
                                                    url: `{{ route("data-master.warehouse.search") }}?location_id=${locationId}`, 
                                                    ...optSelect2
                                                }
                                            });

                                            $(`#${val}_id`).change(function (e) { 
                                                e.preventDefault();
                                                if (val === 'origin') {
                                                    $('#product_id').val(null).trigger('change');
                                                    $('#current-stock').html('');

                                                    const warehouseId = $(this).val();
                                                    $(`#product_id`).select2({
                                                        placeholder: "Pilih Produk",
                                                        ajax: {
                                                            url: `{{ route("inventory.product.search-product-warehouse") }}?warehouse_id=${warehouseId}`, 
                                                            ...optSelect2
                                                        }
                                                    });
                                                } else {
                                                    $('#transfer_qty').val('');
                                                }
                                            });
                                        });
                                    });
                                }
                            });

                            $('#product_id').on('select2:select', function (e) { 
                                e.preventDefault();
                                const selectedData = e.params.data.data;
                                const currentQty = selectedData.quantity;
                                const strCurrentQty = Number(currentQty).toLocaleString('id-ID');
                                const uom = selectedData.product.uom.name;
                                $('#current-stock').html(`${strCurrentQty}`);
                                $('#current-stock-input').val(`${currentQty}`);
                                $('#current-uom').html(` ${uom}`);
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

                            const optMovement = {
                                initEmpty: true,
                                show: function () {
                                    $this = $(this);
                                    $this.slideDown();
                                    // Feather Icons
                                    if (feather) {
                                        feather.replace({ width: 14, height: 14 });
                                    }

                                    $this.find('.supplier_id').select2({
                                        placeholder: "Pilih Supplier",
                                        ajax: {
                                            url: `{{ route("data-master.supplier.search") }}`, 
                                            ...optSelect2
                                        }
                                    });

                                    validationFile();
                                },
                                hide: function (deleteElement) {
                                    if (confirm('Apakah kamu yakin ingin menghapus data ini?')) {
                                        $(this).slideUp(deleteElement);
                                    }
                                }
                            };

                            const $itemRepeater = $('#movement-vehicle').repeater(optMovement);
                            const oldItem = @json(old("movement_vehicle"));
                            if (oldItem) {
                                $itemRepeater.setList(oldItem);
                            } 

                            $('#add-btn').trigger('click');
                            @if ($errors->has('company_id'))
                                $('#company_id').next('.select2-container').find('.select2-selection').addClass('is-invalid');
                            @endif

                            $('form').submit(function(event) {
                                let currentStock = $('#current-stock').text();
                                let transferQty = $('#transfer_qty_input').val();
                                currentStock = parseInt(currentStock.replace(/\./g, ''), 10);
                                transferQty = parseInt(transferQty);

                                const originId = $('#origin_id').val();
                                const destinationId = $('#destination_id').val();
                                
                                if (originId == destinationId) {
                                    alert('Gudang asal dan tujuan tidak boleh sama');
                                    return false;
                                }

                                if (transferQty > currentStock) {
                                    alert(`Jumlah transfer tidak boleh melebihi jumlah stok gudang asal`);
                                    return false;
                                }


                            });
                            
                        });
                    </script>
@endsection