<style>
    #transparentFileUpload {
        opacity: 0;
        position:absolute;
        inset: 0;
    }
</style>
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/animate/animate.css')}}" />

<div class="modal fade" id="returnPayment" tabindex="-1" role="dialog" aria-labelledby="returnPaymentLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary" id="returnPaymentLabel">Form Pembayaran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position: absolute; top: 16px; right: 30px;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" method="post" action="{{ route('marketing.return.payment') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        {{-- Table kiri --}}
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <td><label for="do_number">No. DO</label></td>
                                        <td><input type="text" class="form-control" id="do_number" value="DO-MBU-19289" disabled></td>
                                    </tr>
                                    <tr>
                                        <td><label for="customer_name">Nama Customer</label></td>
                                        <td><input type="text" class="form-control" id="customer_name" value="Abd. Muis" disabled></td>
                                    </tr>
                                    <tr>
                                        <td><label for="return_nominal">Nominal Retur</label></td>
                                        <td><input type="text" class="form-control text-right" id="return_nominal" value="8,020,000.00" disabled></td>
                                    </tr>
                                    <tr>
                                        <td><label for="payment_method">Metode Pembayaran*</label></td>
                                        <td>
                                            <select class="form-control" id="payment_method">
                                                <option value="">Pilih Metode Pembayaran</option>
                                                <option value="transfer">Transfer</option>
                                                <option value="cash">Cash</option>
                                                <option value="credit_card">Kartu Kredit</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><label for="own_bank_id">Akun Bank*</label></td>
                                        <td>
                                            <select class="form-control" id="own_bank_id">
                                                <option value="">Pilih Akun Bank</option>
                                                <option value="bank1">Mandiri - 012345678 - Mitra Berlian</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><label for="recipient_bank_id">Bank Penerima*</label></td>
                                        <td>
                                            <select class="form-control" id="recipient_bank_id">
                                                <option value="">Pilih Akun Bank</option>
                                                <option value="bank2">Mandiri - 85462220 - Abd. Muis</option>
                                            </select>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- Table kanan --}}
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <td><label for="ref_number">Referensi Pembayaran</label></td>
                                        <td><input type="text" class="form-control" id="ref_number" value="INV-0929"></td>
                                    </tr>
                                    <tr>
                                        <td><label for="transaction_number">Nomor Transaksi</label></td>
                                        <td><input type="text" class="form-control" id="transaction_number" value="12345678"></td>
                                    </tr>
                                    <tr>
                                        <td><label for="payment_amount">Nominal Pembayaran*</label></td>
                                        <td><input type="text" class="form-control numeral-mask" id="payment_amount" value="0"></td>
                                    </tr>
                                    <tr>
                                        <td><label for="admin_fee">Biaya Admin Bank*</label></td>
                                        <td><input type="text" class="form-control numeral-mask" id="admin_fee" value="0"></td>
                                    </tr>
                                    <tr>
                                        <td><label for="payment_at">Tanggal Bayar*</label></td>
                                        <td><input type="date" class="form-control flatpickr-basic" id="payment_at" value="0"></td>
                                    </tr>
                                    <tr>
                                        <td><label for="doc_reference">Upload Dokumen</label></td>
                                        <td>
                                            <div class="input-group">
                                                <input type="text" id="fileName" placeholder="Upload" class="form-control">
                                                <input type="file" id="transparentFileUpload" name="doc_reference">
                                                <div class="input-group-append">
                                                    <span class="input-group-text"> <i data-feather="upload"></i> </span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><label for="notes">Catatan</label></td>
                                        <td colspan="3"><textarea class="form-control" id="notes"></textarea></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <a href="{{ route('marketing.return.payment') }}" class="btn btn-outline-warning waves-effect">Batal</a>
                <button id="submitForm" type="submit" data-repeater-create class="btn btn-primary mr-1 waves-effect waves-float waves-light">Submit</button>
            </div>
        </div>
    </div>
</div>

<script src="{{asset('app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/forms/repeater/jquery.repeater.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/forms/cleave/cleave.min.js')}}"></script>
<script>
    $(document).ready(function() {
        $('#transparentFileUpload').on('change', function() {
            $('#fileName').val($('#transparentFileUpload').val().split('\\').pop())
        })
    })

    initNumeralMask('.numeral-mask');

    const dateOpt = { dateFormat: 'd-M-Y' };
        $('.flatpickr-basic').flatpickr(dateOpt);

    initSelect2($('#payment_method'), 'Pilih Metode Pembayaran');
    initSelect2($('#own_bank_id'), 'Pilih Bank');
    initSelect2($('#recipient_bank_id'), 'Pilih Bank');

</script>
