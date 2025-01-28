@php
    $supplier_id = old('supplier_id');
    $supplier_name = old('supplier_name');
    $require_date = old('require_date');
    $notes = old('notes');
    $grandTotal = old('total_before_tax');
    $dataPurchase = '';
    $company_id = old('company_id');
    $company_name = old('company_name');
    $area_id = old('area_id');
    $area_name = old('area_name');
    $location_id = old('location_id');
    $location_name = old('location_name');
    $warehouse_id = old('warehouse_id');
    $warehouse_name = old('warehouse_name');

    if (isset($data)) {
        $company_id = $data->warehouse->location->company_id??'';
        $company_name = $data->warehouse->location->company->name??'';
        $area_id = $data->warehouse->location->area_id??'';
        $area_name = $data->warehouse->location->area->name??'';
        $location_id = $data->warehouse->location_id??'';
        $location_name = $data->warehouse->location->name??'';
        $warehouse_id = $data->warehouse_id??'';
        $warehouse_name = $data->warehouse->name??'';
        $supplier_id = $data->supplier->supplier_id??"";
        $supplier_name = $data->supplier->name;
        $require_date = $data->require_date;
        $notes = $data->notes;
        $grandTotal = $data->total_before_tax;
        if (isset($data->purchase_item)) {
            $dataPurchase = $data->purchase_item;
        }
    }
@endphp

<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">

<script src="{{asset('app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js')}}"></script>

<div class="card mb-1">
    <div id="headingCollapse3" class="card-header color-header collapsed" data-toggle="collapse" role="button" data-target="#collapse3" aria-expanded="true" aria-controls="collapse3">
        <span class="lead collapse-title"> Item Pembelian </span>
    </div>
    <div id="collapse3" role="tabpanel" aria-labelledby="headingCollapse3" class="collapse show" aria-expanded="true">
        <div class="card-body p-2">
            <div class="col-12">
                <div class="form-group row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-sm-3 col-form-label">
                                <label for="supplier_id" class="float-right">Nama Vendor</label>
                            </div>
                            <div class="col-sm-9">
                                <select name="supplier_id" id="supplier_id" class="form-control {{$errors->has('supplier_id')?'is-invalid':''}}" required>
                                    @if($supplier_id && $supplier_name)
                                        <option value="{{ $supplier_id }}" selected="selected">{{ $supplier_name }}</option>
                                    @endif
                                </select>
                                @if ($errors->has('supplier_id'))
                                    <span class="text-danger small">{{ $errors->first('supplier_id') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-sm-3 col-form-label">
                                <label for="require_date" class="float-right">Tgl. Dibutuhkan</label>
                            </div>
                            <div class="col-sm-9">
                                <input type="text" id="require_date" class="{{$errors->has('require_date')?'is-invalid':''}} form-control flatpickr-basic" name="require_date" placeholder="Tanggal Dibutuhkan" value="{{ $require_date?date('d-M-Y', strtotime($require_date)):'' }}" required>
                                @if ($errors->has('require_date'))
                                    <span class="text-danger small">{{ $errors->first('require_date') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="form-group row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-sm-3 col-form-label">
                                <label for="company_id" class="float-right">Unit Bisnis</label>
                            </div>
                            <div class="col-sm-9">
                                <select name="company_id" id="company_id" class="form-control" required>
                                    @if($company_id && $company_name)
                                        <option value="{{ $company_id }}" selected="selected">{{ $company_name }}</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-sm-3 col-form-label">
                                <label for="area_id" class="float-right">Area</label>
                            </div>
                            <div class="col-sm-9">
                                <select name="area_id" id="area_id" class="form-control" required>
                                    <option disabled selected>Pilih Unit Bisnis terlebih dahulu</option>
                                    @if($area_id && $area_name)
                                        <option value="{{ $area_id }}" selected="selected">{{ $area_name }}</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="form-group row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-sm-3 col-form-label">
                                <label for="location_id" class="float-right">Lokasi</label>
                            </div>
                            <div class="col-sm-9">
                                <select name="location_id" id="location_id" class="form-control" required>
                                    <option disabled selected>Pilih Area terlebih dahulu</option>
                                    @if($location_id && $location_name)
                                    <option value="{{ $location_id }}" selected="selected">{{ $location_name }}</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-sm-3 col-form-label">
                                <label for="warehouse_id" class="float-right">Gudang</label>
                            </div>
                            <div class="col-sm-9">
                                <select name="warehouse_id" id="warehouse_id" class="form-control" required>
                                    <option disabled selected>Pilih Lokasi terlebih dahulu</option>
                                    @if($warehouse_id && $warehouse_name)
                                    <option value="{{ $warehouse_id }}" selected="selected">{{ $warehouse_name }}</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-12">
                @include('purchase.add-collapse.informasi-farm')
            </div>
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered w-100 no-wrap text-center" id="purchase-repeater">
                        <thead>
                            <th>Kategori<br>Produk</th>
                            <th>Produk</th>
                            {{-- <th>Project Aktif</th> --}}
                            {{-- <th>Gudang/Tempat<br>Pengiriman</th> --}}
                            <th style="width: 20%;">Jumlah</th>
                            <th>Satuan</th>
                            <th colspan="2">
                                <button class="btn btn-sm btn-icon btn-primary" type="button" id="add-btn" data-repeater-create title="Tambah Item">
                                    <i data-feather="plus"></i>
                                </button>
                            </th>
                        </thead>
                        <tbody data-repeater-list="purchase_item">
                            <tr data-repeater-item>
                                <td><select name="product_category_id" class="product_category_id form-control" required></select></td>
                                <td><select name="product_id" class="product_id form-control" required></select></td>
                                {{-- <td><select name="project_id" class="project_id form-control"></select></td> --}}
                                {{-- <td><select name="warehouse_id" class="warehouse_id form-control" required></select></td> --}}
                                <td><input type="text" name="qty" class="qty form-control numeral-mask" placeholder="Qty" required/></td>
                                <td><input type="text" name="uom_id" class="form-control-plaintext satuan" readonly/></td>
                                <td>
                                    <button class="btn btn-sm btn-icon btn-danger" data-repeater-delete type="button" title="Hapus Item">
                                        <i data-feather="x"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <th colspan="3" class="text-left">
                                Catatan : <br>
                                <textarea name="notes" class="form-control" placeholder="Optional">{{ $notes }}</textarea>
                            </th>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{{asset('app-assets/vendors/js/forms/cleave/cleave.min.js')}}"></script>

<script>
    $(function () {
        let select2Opt = {
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

        const companyId = $('#company_id').val();
        const qryProduct = companyId?`?company_id=${companyId}`:'';
        const locationId = $('#location_id').val();
        const qryWarehouse = locationId?`?location_id=${locationId}`:'';
        $('#supplier_id').select2({
            placeholder: "Pilih supplier",
            ajax: {
                url: `{{ route("data-master.supplier.search") }}`, 
                ...select2Opt
            }
        });

        $('#company_id').select2({
            placeholder: "Pilih Unit Bisnis",
            ajax: {
                url: '{{ route("data-master.company.search") }}', 
                ...select2Opt
            }
        });

        $('#company_id').change(function (e) { 
            e.preventDefault();
            $('#area_id').val(null).trigger('change');
            $('#product_category_id').val(null).trigger('change');
            var companyId = $('#company_id').val();

            $('#area_id').select2({
                placeholder: "Pilih Area",
                ajax: {
                    url: `{{ route("data-master.area.search") }}`, 
                    ...select2Opt
                }
            });

            const qryProduct = companyId?`?company_id=${companyId}`:'';

            $('#area_id').change(function (e) { 
                e.preventDefault();
                $('#location_id').val(null).trigger('change');
                var areaId = $('#area_id').val();
                var qryLocation = areaId&&companyId?`?company_id=${companyId}&area_id=${areaId}`:'';

                $('#location_id').select2({
                    placeholder: "Pilih Lokasi",
                    ajax: {
                        url: `{{ route("data-master.location.search") }}${qryLocation}`, 
                        ...select2Opt
                    }
                });
            });

        });

        $('#product_category_id').select2({
            placeholder: "Pilih Kategori Produk",
            ajax: {
                url: `{{ route("data-master.product-category.search") }}`, 
                ...select2Opt
            }
        });

        const dateOpt = { dateFormat: 'd-M-Y' }
        $('.flatpickr-basic').flatpickr(dateOpt);

        const optPurchase = {
            initEmpty: true,
            show: function () {
                $this = $(this);
                $this.slideDown();
                // Feather Icons
                if (feather) {
                    feather.replace({ width: 14, height: 14 });
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

                $this.find('.product_id').html(`<option selected disabled>Pilih Kategori Produk terlebih dahulu</option>`);
                $this.find('.product_category_id').select2({
                    placeholder: "Pilih Kategori Produk",
                    ajax: {
                        url: `{{ route("data-master.product-category.search") }}${qryProduct}`, 
                        ...select2Opt
                    }
                });

                $this.find('.product_category_id').change(function (e) { 
                    e.preventDefault();
                    const prodCatId = $(this).val();
                    $productId = $(this).closest('td').next().find('.product_id');
                    $productId.val(null).trigger('change');

                    $productId.select2({
                        placeholder: "Pilih Produk",
                        ajax: {
                            url: `{{ route("data-master.product.search") }}?product_category_id=${prodCatId}&can_be_purchased=1`, 
                            ...select2Opt
                        }
                    });

                    $productId.on('select2:select', function (e) { 
                        e.preventDefault();
                        const selectedData = e.params.data.data;
                        $(this).closest('td').next().next().find('.satuan').val(selectedData.uom.name)
                    });
                });


                const dateOpt = { dateFormat: 'd-M-Y' }
                $this.find('.flatpickr-basic').flatpickr(dateOpt);

            },
            hide: function (deleteElement) {
                if (confirm('Apakah kamu yakin ingin menghapus data ini?')) {
                    $(this).slideUp(deleteElement);
                    setTimeout(() => {
                        calculateTotal();
                    }, 1000);
                }
            }
        };

        const $itemRepeater = $('#purchase-repeater').repeater(optPurchase);
        const oldItem = @json(old("purchase_item"));
        if (oldItem) {
            $itemRepeater.setList(oldItem);
        } 

        if ('{{ $dataPurchase }}'.length) {
            const dataPurchase = @json($dataPurchase);
            console.log(dataPurchase);
            getKandangByLocationId('{{$location_id}}');
            if (dataPurchase) {
                $itemRepeater.setList(dataPurchase);
                for (let i = 0; i < dataPurchase.length; i++) {
                    $(`select[name="purchase_item[${i}][product_category_id]"]`).append(`<option value="${dataPurchase[i].product.product_category_id}" selected>${dataPurchase[i].product.product_category.name}</option>`);
                    $(`select[name="purchase_item[${i}][product_category_id]"]`).trigger('change');
                    $(`select[name="purchase_item[${i}][product_id]"]`).append(`<option value="${dataPurchase[i].product_id}" selected>${dataPurchase[i].product.name}</option>`);
                    $(`select[name="purchase_item[${i}][product_id]"]`).trigger('change');
                    $(`input[name="purchase_item[${i}][product_category]"]`).val(dataPurchase[i].product.product_category.name);
                    $(`input[name="purchase_item[${i}][uom_id]"]`).val(dataPurchase[i].product.uom.name);
                }
            }
        }

        function calculateItem(set) {
            let price = set.find('.price').val();
            let qty = set.find('.qty').val();
            if (price && qty) {
                price = parseInt(price.replace(/\./g, '').replace(/,/g, '.'));
                qty = parseInt(qty.replace(/\./g, '').replace(/,/g, '.'));
                const total = price*qty;
                if (total >= 0) {
                    set.find('.total-input').val(total);
                    new Cleave(set.find('.total'), {
                        numeral: true,
                        numeralThousandsGroupStyle: 'thousand', numeralDecimalMark: ',', delimiter: '.'
                    }).setRawValue(total);

                    calculateTotal();
                    set.find('.total').removeClass('text-danger');
                } else {
                    set.find('.total, total-input').val();
                    set.find('.total').addClass('text-danger');
                }
            } else {
                set.find('.total, .total-input').val('');
            }
        }

        function calculateTotal() {
            let grandTotal = 0;

            $('.total-input').each(function() {
                const value = parseFloat($(this).val()) || 0;
                grandTotal += value;
            });

            new Cleave($('.grand-total'), {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand', numeralDecimalMark: ',', delimiter: '.'
            }).setRawValue(grandTotal);
            $('.grand-total-input').val(grandTotal);
        }

        $('#purchase-repeater').on('change', '.price, .qty', function () {
            const set = $(this).closest('[data-repeater-item]');
            calculateItem(set);
        });

        $('.price, .qty').trigger('change');

        if (!oldItem && @json($dataPurchase).length === 0) {
            $('#add-btn').trigger('click');
        }
    });

</script>