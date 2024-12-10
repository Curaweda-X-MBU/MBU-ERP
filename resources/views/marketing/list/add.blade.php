@extends('templates.main')
@section('title', $title)
@section('content')

<style>
    #transparentFileUpload {
        opacity: 0;
        position:absolute;
        inset: 0;
    }
</style>

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
                            <select name="customer_id" id="customer_id" class="form-control">
                                @if(old('customer_id') && old('customer_id'))
                                    <option value="{{ old('customer_id') }}" selected="selected">{{ old('customer_name') }}</option>
                                @endif
                            </select>
                            @if ($errors->has('customer_id'))
                                <span class="text-danger small">{{ $errors->first('customer_id') }}</span>
                            @endif
                        </div>
                        <!-- Tanggal Penjualan -->
                        <div class="col-md-2 mt-1">
                            <label for="sold_at" class="form-label">Tanggal Penjualan</label>
                            <input id="sold_at" name="tanggalPenjualan" type="date" class="form-control" value="{{ old('sold_at', date('Y-m-d')) }}" required>
                        </div>
                        <!-- Status -->
                        <div class="col-md-2 mt-1">
                            <label for="marketing_status" class="form-label">Status</label>
                            <input id="marketing_status" value="Diajukan" name="status" type="text" class="form-control" readonly>
                        </div>
                        <!-- Referensi Dokumen -->
                        <div class="col-md-2 mt-1">
                            <label for="doc_reference" class="form-label">Referensi Dokumen</label>
                            <div class="input-group">
                                <input id="fileName" class="form-control">
                                <button type="button" class="btn btn-icon btn-light" id="uploadButton">
                                    <i data-feather="upload"></i>
                                </button>
                                <input type="file" id="transparentFileUpload" name="doc_reference">
                            </div>
                        </div>
                    </div>

                    <!-- BEGIN: Table-->
                    <div class="table-responsive mt-3">
                        <table id="datatable" class="table table-bordered table-striped w-100">
                            <thead>
                                <tr class="text-center">
                                    <th>No</th>
                                    <th>Kandang/Hatchery*</th>
                                    <th>Nama Produk*</th>
                                    <th>Harga Satuan (Rp)*</th>
                                    <th>Bobot Avg (Kg)*</th>
                                    <th>Qty*</th>
                                    <th>Total Bobot (Kg)*</th>
                                    <th>Total Penjualan (Rp)*</th>
                                    <th>
                                        <button class="btn btn-sm btn-icon btn-primary" type="button" data-repeater-create title="Tambah Kandang">
                                            <i data-feather="plus"></i>
                                        </button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody data-repeater-list="farms">
                                <tr class="text-center" data-repeater-item>
                                    <td>1</td>
                                    <td>
                                        <select class="form-control">
                                            <option value="1" selected="selected">Bedor</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control">
                                            <option value="1" selected="selected">Telur Retak</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" placeholder="Harga Satuan (Rp)">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" placeholder="Bobot Avg (Kg)">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" placeholder="Qty">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control text-center" value="5,000.00" readonly>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control text-right" value="35,000,000.00" readonly>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-icon btn-danger" data-repeater-delete type="button" title="Hapus Kandang">
                                            <i data-feather="x"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- END: Table-->

                    <hr>

                        <div class="row">
                            <!-- BEGIN: Catatan dan Nama Sales -->
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-8 mt-1">
                                        <label for="catatan" class="form-label">Catatan :</label>
                                        <textarea id="catatan" class="form-control" rows="3"></textarea>
                                    </div>

                                    <div class="col-md-4 mt-1">
                                        <label for="namaSales" class="form-label">Nama Sales</label>
                                        <select name="namaSales" id="namaSales" class="form-control">
                                            <option value="1" selected="selected">Ulfah</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            {{-- END: Catatan dan Nama Sales --}}

                            <!-- BEGIN: Total -->
                            <div class="col-md-6 my-1">
                                <table class="table table-borderless">
                                    <tbody class="text-right">
                                        <tr>
                                            <td>Total Sebelum Pajak:</td>
                                            <td>Rp 70.000.000</td>
                                        </tr>
                                        <tr>
                                            <td>Pajak %:</td>
                                            <td><input type="text" class="form-control w-auto d-inline-block"></td>
                                        </tr>
                                        <tr>
                                            <td>Diskon:</td>
                                            <td class="border-bottom border-2"><input type="text" class="form-control w-auto d-inline-block"></td>
                                        </tr>
                                        <tr>
                                            <td>Total setelah pajak & diskon:</td>
                                            <td>Rp 70.000.000</td>
                                        </tr>
                                    </tbody>
                                </table>

                                {{-- BEGIN: Tambah biaya lainnya --}}
                                <table class="table table-borderless">
                                    <thead>
                                        <tr>
                                            <td class="text-right">
                                                <button type="button" class="btn btn-icon btn-primary btn-sm" id="uploadButton">
                                                    Tambah Biaya Lainnya <i data-feather="plus"></i>
                                                </button>
                                            </td>
                                            <td></td>
                                        </tr>
                                    </thead>
                                    <tbody class="text-right">
                                        <tr>
                                            <td><input type="text" class="form-control w-auto d-inline-block"></td>
                                            <td class="border-bottom border-2"><input type="text" class="form-control w-auto d-inline-block"></td>
                                        </tr>
                                    </tbody>
                                </table>
                                {{-- END: Tambah biaya lainnya --}}

                                <table class="table table-borderless">
                                    <tbody class="text-right">
                                        <tr>
                                            <td>Total Piutang:</td>
                                            <td>Rp 70.000.000</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            {{-- END: Total --}}

                            {{-- button --}}
                            <div class="col-12 mt-1">
                                <center>
                                    <a href="{{ route('marketing.list.index') }}" class="btn btn-outline-warning waves-effect">Batal</a>
                                    <button id="submitForm" type="submit" class="btn btn-primary mr-1 waves-effect waves-float waves-light">Submit</button>
                                </center>
                            </div>
                        </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script>
    $(document).ready(function() {
        $('#transparentFileUpload').on('change', function() {
            $('#fileName').val($('#transparentFileUpload').val().split('\\').pop())
        })
    })

    // FORM INPUT DATAS
    // CUSTOMER ID
    $('#customer_id').select2({
        placeholder: "Pilih Pelanggan",
        ajax: {
            url: '{{ route("user-management.user.search") }}',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                console.log(params);
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

    var oldValueCustomer = "{{ old('customer_id') }}";
    if (oldValueCustomer) {
        var oldNameCustomer = "{{ old('customer_name') }}";
        if (oldNameCustomer) {
            var newOption = new Option(oldNameCustomer, oldValueCustomer, true, true);
            $('#customer_id').append(newOption).trigger('change');
        }
    }

    @if ($errors->has('customer_id'))
        $('#customer_id').next('.select2-container').find('.select2-selection').addClass('is-invalid');
    @endif
</script>

@endsection
