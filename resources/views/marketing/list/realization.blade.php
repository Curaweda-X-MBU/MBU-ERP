@extends('templates.main')
@section('title', $title)
@section('content')

<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{$title}}</h4>
            </div>
            <div class="card-body">
                <form class="form-horizontal" method="post" action="{{ route('marketing.list.add') }}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="row row-cols-2 row-cols-md-4">
                        <!-- Nama Pelanggan -->
                        <div class="col-md-2 mt-1">
                            <label for="customer_id" class="form-label">Nama Pelanggan</label>
                            <input type="text" name="customer_id" id="customer_id" class="form-control" readonly>
                        </div>
                        <!-- Tanggal Penjualan -->
                        <div class="col-md-2 mt-1">
                            <label for="sold_at" class="form-label">Tanggal Penjualan</label>
                            <input type="text" id="sold_at" name="sold_at" class="form-control" aria-desribedby="sold_at" placeholder="Pilih Tanggal" readonly>
                        </div>
                        <!-- Status -->
                        <div class="col-md-2 mt-1">
                            <label for="marketing_status" class="form-label">Status</label>
                            <input type="text" id="marketing_status" value="Diajukan" name="status" class="form-control" readonly>
                        </div>
                        <!-- Referensi Dokumen -->
                        <div class="col-md-2 mt-1">
                            <label for="doc_reference" class="form-label">Referensi Dokumen</label>
                            <div class="input-group">
                                <input type="text" id="fileName" placeholder="Upload" class="form-control" readonly>
                                <div class="input-group-append">
                                    <span class="input-group-text"> <i data-feather="upload"></i> </span>
                                </div>
                            </div>
                        </div>
                        <!-- Tanggal Realisasi -->
                        <div class="col-md-2 mt-1">
                            <label for="realized_at" class="form-label">Tanggal Realisasi</label>
                            <input id="realized_at" name="realized_at" class="form-control flatpickr-basic" aria-desribedby="realized_at" placeholder="Pilih Tanggal" required>
                            @if ($errors->has('realized_at'))
                                <span class="text-danger small">{{ $errors->first('realized_at') }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- BEGIN: Table-->
                    <div class="table-responsive mt-3">
                        <table id="marketing-product-repeater-1" class="table w-100">
                            <thead>
                                <tr class="text-center">
                                    <th>Kandang/Hatchery*</th>
                                    <th>Nama Produk*</th>
                                    <th>Harga Satuan (Rp)*</th>
                                    <th>Bobot Avg (Kg)*</th>
                                    <th>Qty*</th>
                                    <th>Total Bobot (Kg)*</th>
                                    <th>Total Penjualan (Rp)*</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="text-center">
                                    <td class="py-2">
                                        <input type="text" name="marketing_kandang_id" class="form-control" readonly>
                                    </td>
                                    <td class="py-2 position-relative">
                                        <input type="text" name="marketing_product_id" class="form-control" readonly>
                                    </td>
                                    <td class="py-2">
                                        <input type="text" name="price" class="form-control numeral-mask" readonly>
                                    </td>
                                    <td class="py-2">
                                        <input type="text" name="weight_avg" class="form-control numeral-mask" readonly>
                                    </td>
                                    <td class="py-2">
                                        <input type="text" name="qty" class="form-control numeral-mask" readonly>
                                    </td>
                                    <td class="py-2">
                                        <input type="text" name="weight_total" class="form-control numeral-mask" readonly>
                                    </td>
                                    <td class="py-2">
                                        <input type="text" name="price_total" class="form-control numeral-mask" readonly>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- END: Table-->

                    <hr class="border-bottom">

                        <div class="row">
                            <!-- BEGIN: Catatan dan Nama Sales -->
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-8 mt-1">
                                        <label for="catatan" class="form-label">Catatan :</label>
                                        <textarea id="catatan" class="form-control" rows="3" readonly></textarea>
                                    </div>

                                    <div class="col-md-4 mt-1">
                                        <label for="sales_id" class="form-label">Nama Sales</label>
                                        <input type="text" name="sales_id" id="sales_id" class="form-control" readonly>
                                    </div>
                                </div>
                            </div>
                            {{-- END: Catatan dan Nama Sales --}}

                            <!-- BEGIN: Total -->
                            <div class="row col-md-6 my-1" id="marketing-addit-prices-repeater-1" style="row-gap: 1em;">
                                <div class="row col-12 text-right align-items-center" style="row-gap: 0.5em;">
                                    <div class="col-5"> <span>Total Sebelum Pajak:</span> </div>
                                    <div class="col-5"> <span>Rp. 70,000,000.00</span> </div>
                                    <div class="col-5"> <span>Pajak:</span> </div>
                                    <div class="col-5 input-group">
                                        <input type="number" min="0" max="100" class="form-control" value="0" readonly>
                                        <div class="input-group-append"><span class="input-group-text">%</span></div>
                                    </div>
                                    <div class="col-5"> <span>Diskon:</span> </div>
                                    <div class="col-5 input-group">
                                        <div class="input-group-prepend"><span class="input-group-text">Rp.</span></div>
                                        <input type="text" class="form-control numeral-mask" value="0" readonly>
                                    </div>
                                    <div class="offset-5 col-5"> <hr class="border-bottom"> </div>
                                    <div class="col-5"> <span>Total Setelah Pajak dan Diskon:</span> </div>
                                    <div class="col-5"> <span class="font-weight-bolder">Rp. 70,000,000.00</span> </div>
                                </div>
                                {{-- BEGIN: Tambah biaya lainnya --}}
                                <div class="row col-12 text-right align-items-center" data-repeater-list="marketing_addit_prices" style="row-gap: 0.5em;">
                                    <div class="col-5">
                                        <span>Biaya Lainnya:</span>
                                    </div>
                                    <div class="row align-items-center" data-repeater-item>
                                        <div class="col-5"> <input type="text" class="form-control" placeholder="Item" readonly> </div>
                                        <div class="col-5 input-group">
                                            <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                                            <input type="text" class="form-control numeral-mask" placeholder="Harga" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row col-12 text-right align-items-center" data-repeater-list="marketing_addit_prices" style="row-gap: 0.5em;">
                                    <div class="offset-5 col-5"> <hr class="border-bottom" style="border-color: black;"> </div>
                                    <div class="col-5"> <span>Total Piutang Penjualan:</span> </div>
                                    <div class="col-5"> <span class="font-weight-bolder" style="font-size: 1.2em;">Rp. 120,000,000.00</span> </div>
                                </div>
                                {{-- START: Grand Total --}}
                                {{-- END: Grand Total --}}
                                <div class="row col-12 text-right align-items-center" data-repeater-list="marketing_addit_prices" style="row-gap: 0.5em;">
                                {{-- END: Tambah biaya lainnya --}}

                            </div>
                            {{-- END: Total --}}

                            {{-- button --}}
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Armada Angkut</h4>
            </div>
            <div class="card-body">
                <form class="form-horizontal">
                    <!-- BEGIN: Table-->
                    <div class="table-responsive mt-3">
                        <table id="marketing-delivery-vehicles-repeater-1" class="table w-100">
                            <thead>
                                <tr class="text-center">
                                    <th>No Polisi</th>
                                    <th>Jumlah</th>
                                    <th>UOM</th>
                                    <th>Waktu Keluar Kandang</th>
                                    <th>Nama Pengirim</th>
                                    <th>Nama Driver</th>
                                    <th>
                                        <button class="btn btn-sm btn-icon btn-primary" type="button" data-repeater-create title="Tambah Armada">
                                            <i data-feather="plus"></i>
                                        </button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody data-repeater-list="marketing_delivery_vehicles">
                                <tr class="text-center" data-repeater-item>
                                    <td class="py-2">
                                        <input type="text" class="form-control" placeholder="No Polisi">
                                    </td>
                                    <td class="py-2">
                                        <input type="text" class="form-control numeral-mask" placeholder="Jumlah">
                                    </td>
                                    <td class="py-2">
                                        <select name="uom_id" class="form-control uom_select">
                                            @if(old('uom_id') && old('uom_id'))
                                                <option value="{{ old('uom_id') }}" selected="selected">{{ old('uom_name') }}</option>
                                            @endif
                                        </select>
                                    </td>
                                    <td class="py-2">
                                        <input id="exit_at" name="exit_at" class="form-control flatpickr-datetime" placeholder="Waktu Keluar Kandang" required>
                                    </td>
                                    <td class="py-2">
                                        <select name="sender_id" class="form-control sender_select">
                                            @if(old('sender_id') && old('sender_id'))
                                                <option value="{{ old('sender_id') }}" selected="selected">{{ old('sender_name') }}</option>
                                            @endif
                                        </select>
                                    </td>
                                    <td class="py-2">
                                        <input type="text" class="form-control" placeholder="Nama Driver">
                                    </td>
                                    <td class="py-2">
                                        <button class="btn btn-sm btn-icon btn-danger" data-repeater-delete type="button" title="Hapus Armada">
                                            <i data-feather="x"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- END: Table-->
                    <hr>
                    {{-- button --}}
                    <div class="col-12 mt-1 text-left">
                            <a href="{{ route('marketing.list.index') }}" class="btn btn-outline-warning waves-effect">Batal</a>
                            <button id="submitForm" type="submit" class="btn btn-outline-success waves-effect">Simpan Draft</button>
                            <button id="submitForm" type="submit" class="btn btn-primary mr-1 waves-effect waves-float waves-light">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="{{asset('app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/forms/repeater/jquery.repeater.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/forms/cleave/cleave.min.js')}}"></script>

<script>
    $(function() {
        /*
        ----- FLATPICKR OPTS -----
        */
        const dateOpt = { dateFormat: 'd-M-Y' };
        const dateTimeOpt = { dateFormat: 'd-M-Y H:i', enableTime: true };
        // ? START :: FLATPICKR ::  REALIZED AT
        $('.flatpickr-basic').flatpickr(dateOpt);
        // ? END :: FLATPICKR ::  REALIZED AT

        // ? START :: FLATPICKR ::  REALIZED AT
        $('.flatpickr-datetime').flatpickr(dateTimeOpt);
        // ? END :: FLATPICKR ::  REALIZED AT

        // ? START :: NUMERAL MASK
            $('.numeral-mask').each(function() {
                new Cleave(this, {
                    numeral: true,
                    numeralThousandsGroupStyle: 'thousand'
                });
            });
        // ? END :: NUMERAL MASK

        // ? START :: SELECT2 :: UOM
        $('.uom_select').select2({
            placeholder: 'Pilih Unit',
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
                        results: data
                    };
                },
                cache: true
            }
        });
        // ? END :: SELECT2 :: MARKETING PRODUCT

        // ? START :: SELECT2 :: SENDER
        $('.sender_select').select2({
            placeholder: 'Pilih Unit',
            ajax: {
                url: '{{ route("user-management.user.search") }}',
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
        // ? END :: SELECT2 :: MARKETING PRODUCT

        // ? START :: REPEATER :: MARKETING DELIVERY VEHICLES
        const optMarketingDeliveryVehicles = {
            show: function() {
                $(this).slideDown();

                new Cleave ($(this).find('.numeral-mask'), {
                    numeral: true,
                    numeralThousandGropuStyle: 'thousand'
                });

                // ? START :: SELECT2 :: UOM
                $('#marketing-delivery-vehicles-repeater-1 .select2-container').remove();
                $('.uom_select').select2({
                    placeholder: 'Pilih Unit',
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
                                results: data
                            };
                        },
                        cache: true
                    }
                });
                // ? END :: SELECT2 :: MARKETING PRODUCT

                // ? START :: SELECT2 :: SENDER
                $('.sender_select').select2({
                    placeholder: 'Pilih Unit',
                    ajax: {
                        url: '{{ route("user-management.user.search") }}',
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
                // ? END :: SELECT2 :: MARKETING PRODUCT


                // FEATHER ICONS
                if (feather) {
                    feather.replace({ width: 14, height: 14 });
                }
            },
            hide: function(deleteElement) {
                if (confirm('Apakah kamu yakin ingin menghapus data ini?')) {
                    $(this).slideUp(deleteElement);
                }
            },
        };

        const $marketingDeliveryVehiclesRepeater = $('#marketing-delivery-vehicles-repeater-1').repeater(optMarketingDeliveryVehicles);
    });
</script>

@endsection
