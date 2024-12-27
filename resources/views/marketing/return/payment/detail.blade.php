@php
$is_detail = true;
@endphp

<style>
    .custom-modal-layout {
        gap: 1em;
        justify-content: center;
    }

    .custom-modal-layout > .row {
        justify-content: center;
    }
</style>

<script>
    function setApproval(value) {
        setTimeout(function() {
            $('#is_approved').val(value);

            const route = @js(route('marketing.return.payment.approve', ':id'));
            const id = $('input[name="marketing_return_payment_id"]').val();
            $('#approveForm').attr('action', route.replace(':id', id)).trigger('submit');

        }, 0);
    }
</script>

<form id="approveForm" class="form-horizontal" method="post" action="" enctype="multipart/form-data">
    {{ csrf_field() }}
    <input type="hidden" name="is_approved" id="is_approved" value="">
    <div class="modal fade" id="paymentDetail" tabindex="-1" role="dialog" aria-labelledby="paymentDetailLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-primary" id="paymentDetailLabel">Form Pembayaran</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position: absolute; top: 16px; right: 30px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @include('marketing.return.sections.payment-detail')
                </div>
                @php
                    $roleAccess = Auth::user()->role;
                @endphp
                @if ($roleAccess->hasPermissionTo('marketing.return.payment.approve'))
                <div class="modal-footer" style="display: grid; grid-template-columns: 100%; align-items: baseline; justify-content: center; row-gap: 1rem;">
                    <div class="w-100 col-12 m-0">
                        <div class="row mx-auto">
                            <div class="col-12 col-lg-6 d-flex flex-column align-items-start p-0 offset-lg-6">
                                <label for="approval_notes">Catatan Persetujuan</label>
                                <textarea name="approval_notes" class="form-control" id="approval_notes"></textarea>
                            </div>
                        </div>
                    </div>
                    <div style="justify-self: end;">
                        <button type="button" onclick="setApproval(1)" class="btn btn-success mr-1 waves-effect waves-float waves-light">Setuju</button>
                        <button type="button" onclick="setApproval(0)" class="btn btn-danger mr-1 waves-effect waves-float waves-light">Tidak</button>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</form>
