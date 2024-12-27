<style>
    .custom-modal-layout {
        gap: 1em;
        justify-content: center;
    }

    .custom-modal-layout > .row {
        justify-content: center;
    }
</style>

<form class="form-horizontal" method="post" action="{{ route('marketing.return.payment.add', $data->marketing_id) }}" enctype="multipart/form-data">
    {{ csrf_field() }}
    <div class="modal fade" id="paymentAdd" tabindex="-1" role="dialog" aria-labelledby="returnPaymentLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-primary" id="returnPaymentLabel">Form Pembayaran</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position: absolute; top: 16px; right: 30px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @include('marketing.return.sections.payment-detail');
                </div>
                <div class="modal-footer">
                    <a href="{{ route('marketing.return.payment.index', $data->marketing_id) }}" class="btn btn-outline-warning waves-effect">Batal</a>
                    <button id="submitForm" type="submit" class="btn btn-primary mr-1 waves-effect waves-float waves-light">Submit</button>
                </div>
            </div>
        </div>
    </div>
</form>
