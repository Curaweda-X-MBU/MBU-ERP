<style>
    .instruction {
        display: grid;
        grid-template-columns: max-content min-content auto;
        row-gap: 0.3rem;
        column-gap: 1rem;
        align-items: center;
    }

    .instruction > hr, .instruction > button {
        grid-column: 1 / span 3;
        border-bottom: solid 1px #ececec;
        width: 100%;
    }

    #transparentFileUpload {
        opacity: 0;
        position: absolute;
        inset: 0;
    }

    #formPaymentCsv #submitButton {
        transition: height 500ms ease, opacity 300ms ease;
        overflow: hidden;
        opacity: 0;
        pointer-events: none;
    }

    #formPaymentCsv #submitButton.visible {
        opacity: 1;
        pointer-events: auto;
    }
</style>

<button type="button" class="btn btn-success waves-effect" data-toggle="modal" data-target="#modalUploadPembayaran"><i data-feather="upload"></i>&nbsp;Upload Pembayaran</button>

<div class="modal fade" id="modalUploadPembayaran" tabindex="-1" role="dialog" aria-labelledby="modalUploadPembayaranLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalUploadPembayaranLabel">Upload CSV Pembayaran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12 col-lg-6 mb-2">
                            <h5 class="font-weight-bolder">Download Template</h5>
                            <div class="mb-2">Edit data pembayaran dalam file csv dengan format:</div>
                        </div>
                        <div class="col-12 col-lg-6 mb-2">
                            <h5 class="font-weight-bolder">Upload CSV</h5>
                            <div class="mb-2">Upload file template yang sudah disesuaikan.</div>
                        </div>
                        <div class="instruction col-12 mb-3">
                            <div>
                                <div><b>ID Biaya</b></div>
                                <div><i>id_expense</i></div>
                            </div>
                            <span><i data-feather="chevron-right"></i></span>
                            <span>...</span>
                            <hr>

                            <div>
                                <div><b>Metode Pembayaran</b></div>
                                <div><i>payment_method</i></div>
                            </div>
                            <span><i data-feather="chevron-right"></i></span>
                            <span>Transfer | Cash | Credit | Cheque</span>
                            <hr>

                            <div>
                                <div><b>Akun Bank</b></div>
                                <div><i>bank_account</i></div>
                            </div>
                            <span><i data-feather="chevron-right"></i></span>
                            <span>...</span>
                            <hr>

                            <div>
                                <div><b>Referensi Pembayaran</b></div>
                                <div><i>payment_reference</i></div>
                            </div>
                            <span><i data-feather="chevron-right"></i></span>
                            <span>xxxx-xxxx-xxxx-xxxx</span>
                            <hr>

                            <div>
                                <div><b>No. Transaksi</b></div>
                                <div><i>transaction_number</i></div>
                            </div>
                            <span><i data-feather="chevron-right"></i></span>
                            <span>1234567890</span>
                            <hr>

                            <div>
                                <div><b>Tanggal Bayar</b></div>
                                <div><i>payment_date</i></div>
                            </div>
                            <span><i data-feather="chevron-right"></i></span>
                            <span>01-Jan-2025</span>
                            <hr>

                            <div>
                                <div><b>Nominal Pembayaran</b></div>
                                <div><i>payment_nominal</i></div>
                            </div>
                            <span><i data-feather="chevron-right"></i></span>
                            <span>199000000</span>
                        </div>
                        <form id="formPaymentCsv" class="col-12 mb-2" action="{{ route('expense.list.payment.batch') }}" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="col-12 d-flex flex-column flex-lg-row mb-1" style="gap: 1em;">
                                <a role="button" class="btn btn-success w-100" href="{{ asset('assets/files/expense_payment_batch_upload_template.csv') }}"><i data-feather="download"></i>&nbsp;Download Template</a>
                                <div class="input-group w-100">
                                    <input type="text" id="fileName" placeholder="Upload" class="form-control" required>
                                    <input type="file" id="transparentFileUpload" name="payment_csv" accept=".csv" required>
                                    <div class="input-group-append" style="pointer-events: none;">
                                        <span class="input-group-text btn btn-primary"><i data-feather="upload"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" id="submitButton" class="btn btn-primary w-100">Lanjutkan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        const $submitButton = $('#submitButton');
        toggleSubmitButton($('#transparentFileUpload'));
        function toggleSubmitButton(el) {
            if ($(el).val() && $(el).val() !== '') {
                $submitButton.addClass('visible');
            } else {
                $submitButton.removeClass('visible');
            }
        }

        $('#transparentFileUpload').on('change', function() {
            $('#fileName').val($('#transparentFileUpload').val().split('\\').pop()).trigger('change');
            toggleSubmitButton(this);
        });

        $submitButton.on('click', function(e) {
            e.preventDefault();
            $('#formPaymentCsv').trigger('submit');
        });
    });
</script>
