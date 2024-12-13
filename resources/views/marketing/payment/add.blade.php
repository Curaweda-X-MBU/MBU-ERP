<style>
    #transparentFileUpload {
        opacity: 0;
        position:absolute;
        inset: 0;
    }
</style>
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">

<div class="table-responsive">
    <div class="form-row w-100">
        <div class="form-section" style="flex: 1; padding: 10px;">
            <div class="form-group">
                <label for="do_number">Nomor DO</label>
                <input type="text" id="do_number" class="form-control" readonly />
            </div>
            <div class="form-group">
                <label for="customer_name">Nama Customer</label>
                <input type="text" id="customer_name" class="form-control" readonly />
            </div>
            <div class="form-group">
                <label for="sales_nominal">Nominal Penjualan</label>
                <input type="text" id="sales_nominal" class="form-control" readonly />
            </div>
            <div class="form-group">
                <label for="payment_method">Metode Pembayaran<i class="text-danger">*</i></label>
                <select id="payment_method" class="form-control">
                    <option value="">Pilih Metode Pembayaran</option>
                    <option value="transfer">Transfer</option>
                    <option value="cash">Cash</option>
                    <option value="credit_card">Kartu Kredit</option>
                </select>
            </div>
            <div class="form-group">
                <label for="own_bank_id">Akun Bank<i class="text-danger">*</i></label>
                <select id="own_bank_id" class="form-control">
                    <option value="">Pilih Akun Bank</option>
                    <option value="bank1">Bank 1</option>
                    <option value="bank2">Bank 2</option>
                </select>
            </div>
        </div>

        <div class="form-section" style="flex: 1; padding: 10px;">
            <div class="form-group">
                <label for="ref_number">Referensi Pembayaran</label>
                <input type="text" id="ref_number" class="form-control" />
            </div>
            <div class="form-group">
                <label for="transaction_number">Nomor Transaksi</label>
                <input type="text" id="transaction_number" class="form-control" />
            </div>
            <div class="form-group">
                <label for="payment_amount">Nominal Pembayaran<i class="text-danger">*</i></label>
                <input type="text" id="payment_amount" class="form-control numeral-mask" />
            </div>
            <div class="form-group">
                <label for="payment_at">Tanggal Bayar<i class="text-danger">*</i></label>
                <input id="payment_at" name="payment_at" class="form-control flatpickr-basic" aria-desribedby="payment_at" placeholder="Pilih Tanggal" required>
            </div>
            <div class="form-group">
                <label for="document">Upload Dokumen</label>
                <div class="input-group position-relative">
                    <input type="text" id="fileName" placeholder="Upload" class="form-control">
                    <input type="file" id="transparentFileUpload" name="doc_reference">
                    <div class="input-group-append">
                        <span class="input-group-text btn-primary">Upload</span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="notes">Catatan</label>
                <textarea id="notes" class="form-control" rows="3"></textarea>
            </div>
        </div>
    </div>
</div>

<script src="{{asset('app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/forms/cleave/cleave.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script>
    $(document).ready(function() {
        $('#transparentFileUpload').on('change', function() {
            $('#fileName').val($('#transparentFileUpload').val().split('\\').pop())
        })
    })

    // ? START :: NUMERAL MASK :: INITIALIZE
    function initNumeralMask() {
        $('.numeral-mask').each(function() {
            new Cleave(this, {
                numeral: true,
                numeralDecimalMark: ',', delimiter: '.',
            });
        });
    }
    initNumeralMask();
    // ? END :: NUMERAL MASK :: INITIALIZE

    // ? START :: SELECT2 :: INITIALIZE
    function initSelect2($component, placeholder, routePath) {
        if (routePath) {
            $component.select2({
                placeholder: placeholder,
                ajax: {
                    url: routePath,
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
            $component.select2({
                placeholder: placeholder,
            });
        }
    }
    // ? END :: SELECT2 :: INITIALIZE
    initSelect2($('#payment_method'), 'Pilih Metode Pembayaran');
    initSelect2($('#own_bank_id'), 'Pilih Bank');

    // ? START :: FLATPICKR ::  PAID AT
    const dateOpt = { dateFormat: 'd-M-Y' };
    $('.flatpickr-basic').flatpickr(dateOpt);
    // ? END :: FLATPICKR ::  PAID AT
</script>
