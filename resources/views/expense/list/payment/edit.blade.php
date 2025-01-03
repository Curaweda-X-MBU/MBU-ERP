@php
$is_edit = true;
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
    function handleEdit() {
            const $form = $('#editForm');

            if (!$form[0].checkValidity()) {
                return;
            }

        setTimeout(function() {
            const route = @js(route('marketing.list.payment.edit', ':id'));
            const id = $('input[name="marketing_payment_id"]').val();
            $form.attr('action', route.replace(':id', id)).trigger('submit');

        }, 0);
    }
</script>

<form id="editForm" class="form-horizontal" method="post" action="" enctype="multipart/form-data">
    {{ csrf_field() }}
    <div class="modal fade" id="paymentEdit" tabindex="-1" role="dialog" aria-labelledby="paymentDetailLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-primary" id="paymentDetailLabel">Form Pembayaran</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close" style="position: absolute; top: 16px; right: 30px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @include('marketing.list.sections.payment-detail')
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="handleEdit()" class="btn btn-success mr-1 waves-effect waves-float waves-light">Edit</button>
                </div>
            </div>
        </div>
    </div>
</form>
