<style>
    .custom-modal-layout {
        gap: 1em;
        justify-content: center;
    }

    .custom-modal-layout > .row {
        justify-content: center;
    }
</style>

<form class="form-horizontal" method="post" action="{{ route('expense.list.payment.add', $data->expense_id) }}" enctype="multipart/form-data">
    {{ csrf_field() }}
    <div class="modal fade" id="expensePaymentAdd" tabindex="-1" role="dialog" aria-labelledby="paymentDetailLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-primary" id="paymentDetailLabel">Form Pembayaran</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close" style="position: absolute; top: 16px; right: 30px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @include('expense.list.sections.payment-detail')
                </div>
                <div class="modal-footer">
                    <button id="submitForm" type="submit" class="btn btn-primary mr-1 waves-effect waves-float waves-light">Submit</button>
                </div>
            </div>
        </div>
    </div>
</form>
