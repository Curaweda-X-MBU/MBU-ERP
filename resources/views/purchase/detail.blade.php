@extends('templates.main')
@section('title', $title)
@section('content')
<style>
    .color-header {
        color: white;
        background: linear-gradient(118deg, #76A8D8, #c9e5ff);
    }
</style>
<script src="{{asset('app-assets/vendors/js/forms/repeater/jquery.repeater.min.js')}}"></script>
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/pickers/form-pickadate.css')}}">

<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.time.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js')}}"></script>

<div class="col-12">
    <div class="row">
        <div class="no-print pb-2">
            <h4 class="card-title">{{$title}}</h4>
            
                <a href="{{ route('purchase.index') }}" class="btn btn-outline-secondary">
                    <i data-feather="arrow-left" class="mr-50"></i>
                    Kembali
                </a>
                @if (Auth::user()->role->name === 'Super Admin' && $data->status < 3 && !$data->rejected )
                <a href="{{ route('purchase.edit', $data->purchase_id) }}" class="btn btn-primary">
                    <i data-feather="edit-2" class="mr-50"></i>
                    Edit
                </a>
                @endif
                {{-- @if (Auth::user()->role->hasPermissionTo('purchase.copy'))
                <a href="{{ route('purchase.copy', $data->purchase_id) }}" class="btn btn-warning">
                    <i data-feather="copy" class="mr-50"></i>
                    Copy
                </a>
                @endif --}}
                @php
                    $modalId = 'approve';
                    if ($data->status == 3) {
                        $modalId = 'approve_purchase';
                    }
                    if ($data->status == 6) {
                        $modalId = 'approve_reception';
                    }
                    $btnApproval = '<a class="btn btn-success" href="javascript:void(0);" data-id="'.$data->purchase_id.'" data-toggle="modal" data-target="#'.$modalId.'">
                        <i data-feather="check" class="mr-50"></i>
                        Approve
                    </a> <a class="btn btn-danger" href="javascript:void(0);" data-id="'.$data->purchase_id.'" data-toggle="modal" data-target="#reject">
                        <i data-feather="x" class="mr-50"></i>
                        Tolak
                    </a>';
                    
                    $roleName = auth()->user()->role->name;
                    $currentStatus = $purchase_status[$data->status];

                    if ((isset($purchase_approval[$currentStatus]) && $purchase_approval[$currentStatus] === $roleName) || $roleName === 'Super Admin' && $data->status !== 8) {
                        if (!$data->rejected && Auth::user()->role->hasPermissionTo('purchase.approve')) {
                            if ($data->status === 7) {
                                echo '<a class="btn btn-success" href="javascript:void(0);" data-id="'.$data->purchase_id.'" data-toggle="modal" data-target="#add_payment">
                                    <i data-feather="plus" class="mr-50"></i>
                                    Tambah Data Pembayaran
                                </a> ';
                            } else {
                                echo $btnApproval;
                            }
                        } 
                    }
                @endphp
                {{-- @endif --}}
        </div>
    </div>
</div>

@include('purchase.stepper')
<section id="collapsible">
    <div class="row">
        <div class="col-sm-12">
            <div class=" collapse-icon">
                <div class=" p-0">
                    <div class="collapse-default">
                        @include('purchase.detail-collapse.informasi-umum')
                        {{-- @include('purchase.detail-collapse.purchase-alocation') --}}
                        @include('purchase.detail-collapse.purchase-other')
                        @include('purchase.detail-collapse.purchase-reception')
                        @include('purchase.detail-collapse.payment-info')
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade text-left" id="reject" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <form method="post" action="{{ route('purchase.approve', 'test') }}">
            {{csrf_field()}}
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel1">Konfirmasi Tolak Pembelian</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="id" value="">
                    <input type="hidden" name="reject" value="1">
                    <p>Apakah kamu yakin ingin menolak pembelian ini ?</p>
                    Alasan penolakan : 
                    <textarea name="reason" class="form-control" placeholder="Alasan" required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Ya</button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Tidak</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade text-left" id="add_payment" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
        <div class="modal-content">
            <form method="post" action="{{ route('purchase.payment', 'test') }}" enctype="multipart/form-data">
            {{csrf_field()}}
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel1">Isi Data Pembayaran</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="id" value="">
                    <input type="hidden" name="reject" value="1">
                    @include('purchase.add-collapse.purchase-payment')
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Simpan</button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Tidak</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade text-left" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog {{ $data->status===3||$data->status===6?'modal-xl':'' }} modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <form method="post" action="{{ route('purchase.approve', 'test') }}" enctype="multipart/form-data">
            {{csrf_field()}}
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel1">Konfirmasi Approve Pembelian</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="max-height: 630px; overflow-y: auto;">
                    <input type="hidden" name="id" id="id" value="">
                    @if ($data->status===3)
                    @include('purchase.add-collapse.purchase-other')
                    @elseif ($data->status===6)
                    @include('purchase.add-collapse.purchase-reception')
                    @else 
                    <p>Apakah kamu yakin ingin menyetujui pembelian ini ?</p>
                    @endif
                    @if (( $data->status===4 && $data->grand_total < 100000000 ) || $data->status===5)
                    <i><p class="text-danger"><b>Dengan menyetujui pemebelian ini, PO akan dibuat secara otomatis <b></p></i>
                    @endif
                    Catatan : 
                    <textarea name="notes" class="form-control" placeholder="Optional"></textarea>
                </div>
                <div class="modal-footer">
                    @if ($data->status===3||$data->status===6)
                    <button type="submit" class="save_approve btn btn-danger">Simpan & Setujui</button>
                    <input type="submit" class="btn btn-warning" name="save_only" value="Simpan Draft">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Batal</button>
                    @else 
                    <button type="submit" class="btn btn-danger">Ya</button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Tidak</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

<script src="{{asset('app-assets/js/scripts/components/components-collapse.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/forms/cleave/cleave.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>

@php
    $dataPurchaseOther = '';
    if (isset($data->purchase_other)) {
        $dataPurchaseOther = $data->purchase_other;
    }
    
    $dataPurchaseItem = '';
    if (isset($data->purchase_item)) {
        $dataPurchaseItem = $data->purchase_item;
    }

    $dataPurchaseWarehouse = [];
    if (isset($data->warehouse_ids)) {
        $dataPurchaseWarehouse = $data->warehouse_ids;
    }
@endphp
<script>
    $(document).ready(function () {
        $('.save_approve').click(function(event) {
            var confirmation = confirm('Apakah kamu yakin ingin menyimpan dan menyetujui ?');
            console.log('status', @json($data->status));
            
            if (@json($data->status) === 3 ) {
                let remainAlocation = 0;
                $(".remain-item").each(function () {
                    remainAlocation += parseFloat($(this).text()) || 0; // Sum all .amount-text values
                });
                if (remainAlocation !== 0) {
                    alert('Sisa alokasi harus 0');
                    return false;
                }
            }

            if (!confirmation) {
                event.preventDefault();
            }
        });

        $('#download-project').click(function (e) { 
            e.preventDefault();
            window.print()
        });

        $('#reject').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) 
            var id = button.data('id')
            var modal = $(this)
            modal.find('.modal-body #id').val(id)
        });

        $('#approve').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) 
            var id = button.data('id')
            var modal = $(this)
            modal.find('.modal-body #id').val(id)
        });

        $('#approve_purchase').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) 
            var id = button.data('id')
            var modal = $(this)
            modal.find('.modal-body #id').val(id)
            
            const optPurchaseOther = {
                initEmpty: true,
                show: function () {
                    $(this).slideDown();
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
                },
                hide: function (deleteElement) {
                    if (confirm('Apakah kamu yakin ingin menghapus data ini?')) {
                        $(this).slideUp(deleteElement);
                    }
                }
            };

            const $otherRepeater = $('#purchase-other-repeater').repeater(optPurchaseOther);
            if (@json($dataPurchaseOther).length > 0) {
                const dataPurchaseOther = @json($dataPurchaseOther);
                $otherRepeater.setList(dataPurchaseOther);
            } else {
                $('#add-btn-other').trigger('click');
            }

            $('.amount').trigger('change');
            console.log('dataPurchaseItem', @json($dataPurchaseItem));
        });

        $('#approve_reception').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) 
            var id = button.data('id')
            var modal = $(this)
            modal.find('.modal-body #id').val(id)
            
            const optPurchaseReception = {
                initEmpty: true,
                show: function () {
                    $(this).slideDown();
                    // Feather Icons
                    if (feather) {
                        feather.replace({ width: 14, height: 14 });
                    }
                    $('.flatpickr-inline').flatpickr({
                        dateFormat: "d-M-Y H:i",
                        enableTime: true,
                        time_24hr: true
                    });
                    $('.flatpickr-calendar').css('margin', 'auto');
                    validationFile();
                    var numeralMask = $('.numeral-mask');
                    if (numeralMask.length) {
                        numeralMask.each(function() { 
                            new Cleave(this, {
                                numeral: true,
                                numeralThousandsGroupStyle: 'thousand', numeralDecimalMark: ',', delimiter: '.'
                            });
                        })
                    }

                    $(this).find('.warehouse_id').select2({
                        placeholder: "Pilih Gudang",
                        ajax: {
                            url: `{{ route("data-master.warehouse.search") }}`, 
                            dataType: 'json',
                            delay: 250, 
                            data: function(params) {
                                return {
                                    q: params.term,
                                    warehouse_ids: @json($dataPurchaseWarehouse)
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

                    $(this).find('.supplier_id').select2({
                        placeholder: "Pilih Vendor",
                        ajax: {
                            url: `{{ route("data-master.supplier.search") }}`, 
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

                    const receivedTotal = parseInt($(this).find('.total_received').val().replace(/\./g, '').replace(/,/g, '.')) || 0;
                    $(this).find('.transport_per_item').keyup(function (e) { 
                        e.preventDefault();
                        const perItem = parseInt($(this).val().replace(/\./g, '').replace(/,/g, '.')) || 0;
                        const transTotal = perItem*receivedTotal;
                        $transTotalInput = $(this).closest('td').next().find('.transport_total');
                        new Cleave($transTotalInput, {
                            numeral: true,
                            numeralThousandsGroupStyle: 'thousand', numeralDecimalMark: ',', delimiter: '.'
                        }).setRawValue(transTotal);
                    });
                },
                hide: function (deleteElement) {
                    if (confirm('Apakah kamu yakin ingin menghapus data ini?')) {
                        $(this).slideUp(deleteElement);
                    }
                }
            };
            
            const dataPurchaseItem = @json($dataPurchaseItem);
            dataPurchaseItem.forEach(val => {
                const $receptionRepeater = $(`#purchase-reception-repeater-${val.purchase_item_id}`).repeater(optPurchaseReception);
                const arrItemAlocation = val.purchase_item_alocation;
                let arrItemReception = val.purchase_item_reception;
                
                let arrSetListReception = [];
                let customSet = [];
                arrItemAlocation.forEach(element => {
                    let receivedDate = '';
                    let travelNumber = '';
                    let travelNumberDoc = '';
                    let vehicleNumber = '';
                    let totalReceived = '';
                    let totalRetur = 0;
                    let supplierId = '';
                    let supplierName = '';
                    let transPerItem = 0;
                    let transTotal = 0;
                    if (arrItemReception.length > 0) {
                        arrItemReception.forEach(item => {
                            if (item.warehouse_id == element.warehouse_id) {
                                const date = new Date(item.received_date);
                                const options = { day: '2-digit', year: 'numeric', month: 'short' };
                                const timeOptions = { hour: '2-digit', minute: '2-digit', hour12: false }; // 24-hour format
                                receivedDate = `${date.toLocaleDateString('en-GB', options).replace(/ /g, '-')} ${date.toLocaleTimeString('en-GB', timeOptions)}`;
                                travelNumberDoc = item.travel_number_document??'';
                                delete item.travel_number_document;
                                travelNumber = item.travel_number;
                                vehicleNumber = item.vehicle_number;
                                totalRetur += item.total_retur;
                                supplierId = item.supplier_id??'';
                                supplierName = item.supplier?.name??'';
                                transPerItem = item.transport_per_item;
                                transTotal = item.transport_total;
                            } 
                        });
                    }
                    customSet.push({
                        warehouse_name: element.warehouse.name??'N/A', 
                        file_name: travelNumberDoc,
                        supplier_name: supplierName
                    });
                    arrSetListReception.push({ 
                        date : receivedDate,
                        warehouse_id : element.warehouse_id,
                        travel_number : travelNumber,
                        vehicle_number : vehicleNumber,
                        total_received : element.alocation_qty,
                        total_retur : totalRetur,
                        supplier_id : supplierId,
                        transport_per_item : transPerItem,
                        transport_total : transTotal
                    });
                });

                console.log('customSet', customSet);
                console.log('arrSetListReception', arrSetListReception);
                $receptionRepeater.setList(arrSetListReception);

                for (let i = 0; i < arrSetListReception.length; i++) {
                    if (customSet[i].file_name.length > 0) {
                        const fileName = customSet[i].file_name;
                        $(`input[name="purchase_item_reception_${val.purchase_item_id}[${i}][travel_number_document]"]`)
                            .closest('td').html(`
                                <a href="{{ route('file.show', ['filename' => '__FILE_NAME__']) }}" target="_blank">
                                    <i data-feather='download' class="mr-50"></i>
                                    <span>Download</span>
                                </a>
                                <input type="hidden" name="purchase_item_reception_${val.purchase_item_id}[${i}][travel_number_document]" value="${fileName}">
                                <div class="float-right">
                                    <a href="javascript:void(0)" class="delete-file text-danger" title="Hapus File">
                                        <i data-feather="trash"></i>
                                    </a>
                                </div>
                            `.replace('__FILE_NAME__', fileName));
                    }
                    $(`select[name="purchase_item_reception_${val.purchase_item_id}[${i}][warehouse_id]"]`).append(`<option value="${arrSetListReception[i].warehouse_id}" selected>${customSet[i].warehouse_name}</option>`);
                    $(`select[name="purchase_item_reception_${val.purchase_item_id}[${i}][warehouse_id]"]`).trigger('change');
                    $(`select[name="purchase_item_reception_${val.purchase_item_id}[${i}][supplier_id]"]`).append(`<option value="${arrSetListReception[i].supplier_id}" selected>${customSet[i].supplier_name}</option>`);
                    $(`select[name="purchase_item_reception_${val.purchase_item_id}[${i}][supplier_id]"]`).trigger('change');
                }

            });
            if (feather) {
                feather.replace({ width: 14, height: 14 });
            }
            $('.delete-file').on('click', function () { 
                $(this).closest('td').html('<input type="file" class="form-control sj-doc" name="travel_number_document" accept=".pdf, image/jpeg">')
                validationFile();
            });

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

        $('#add_payment').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) 
            var id = button.data('id')
            var modal = $(this)
            modal.find('.modal-body #id').val(id)

            const dateOpt = { dateFormat: 'd-M-Y' }
            $('.flatpickr-basic').flatpickr(dateOpt);
            validationFile();
            
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

        $('#own_bank_id, #recipient_bank_id').select2({
            dropdownParent: $('#add_payment'),
            placeholder: "Pilih Akun Bank",
            ajax: {
                url: '{{ route("data-master.bank.search") }}',
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
</script>
@endsection