<link rel="stylesheet" href="{{asset('assets/css/stepper.css')}}">
<style>
    .tooltip-inner {
        text-align: left
    }
</style>

<div class="wizard-progress">
    @foreach ($expense_status as $key => $item)
    @php
        $reject_key = array_search('Ditolak', \App\Constants::EXPENSE_STATUS);
        $disburse_key = array_search('Pencairan', \App\Constants::EXPENSE_STATUS);
        $approval_keys = array_keys(array_filter(\App\Constants::EXPENSE_STATUS, function($status) {
            return strpos($status, 'Approval') !== false;
        }));

        $stepStatus = '';
        $approvalTooltip = null;

        // Hide 'Ditolak' step unless expense is rejected
        if ($key === $reject_key) {
            continue;
        }

        // Set style for completed step
        if ($data->expense_status > $key) {
            $stepStatus = 'complete';
        }

        // Set style for current step
        if ($data->expense_status === $key) {
            $stepStatus = 'in-progress';
        }

        // Logic for 'Pencairan'
        if ($data->expense_status >= $disburse_key && $key === $disburse_key) {
            if ($data->payment_status < array_search('Dibayar Penuh', \App\Constants::EXPENSE_PAYMENT_STATUS)) {
                $stepStatus = 'in-progress';
            } else {
                // Only set status as completed if payment is full or more
                $stepStatus = 'complete';
            }
        }

        // Logic for 'Approval'
        if (in_array($key, $approval_keys)) {
            $approvalData = collect(json_decode($data->approval_line));
            $currentApproval = $approvalData->where('status', $key)->last(); // use last() in case there are second re-approval after editing and resubmitting
            $notes = isset($currentApproval->notes) ? '<br />Catatan: ' . $currentApproval->notes : '';
            $approvalTooltip = $currentApproval
                ? date('d-M-Y H:i', strtotime($currentApproval->date ?? '')).
                '<br />Oleh: '.$currentApproval->action_by.
                $notes
                : 'Menunggu Persetujuan';

            $stepStatus = isset($currentApproval) ? ($currentApproval->is_approved ? 'complete' : 'rejected') : $stepStatus;
        }

        // Set all to complete if status is selesai
        if ($data->expense_status === array_key_last(\App\Constants::EXPENSE_STATUS)) {
            $stepStatus = 'complete';
        }

        $tooltipMsg = $approvalTooltip ?: ucfirst($stepStatus);
    @endphp
    <a href="javascript:void(0)" class="step {{ $stepStatus }}">
        <pre class="bg-white">{{ preg_replace('/\s/', "\n", $item, 1) }}</pre>
        <div class="node" data-toggle="{{ strlen($stepStatus) > 0?'tooltip':'' }}" data-placement="right" title="{{ $tooltipMsg }}" data-html="true" ></div>
    </a>
    @endforeach
</div>
