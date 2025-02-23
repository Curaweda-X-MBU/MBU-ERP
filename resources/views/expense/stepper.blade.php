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

        $stepStatus = '';
        if ($key === $reject_key && $data->expense_status !== $reject_key) {
            continue;
        }

        if ($data->expense_status > $key) {
            $stepStatus = 'complete';
        }

        if ($data->expense_status === $key) {
            $stepStatus = 'in-progress';
        }

        if ($data->expense_status >= $disburse_key && $key === $disburse_key) {
            if ($data->payment_status < array_search('Dibayar Penuh', \App\Constants::EXPENSE_PAYMENT_STATUS)) {
                $stepStatus = 'in-progress';
            } else {
                $stepStatus = 'complete';
            }
        }

        if ($data->expense_status === $reject_key && $key === $reject_key) {
            $stepStatus = 'rejected';
        }

        // set all to complete if status is selesai
        if ($data->expense_status === array_key_last(\App\Constants::EXPENSE_STATUS)) {
            $stepStatus = 'complete';
        }
    @endphp
    <a href="javascript:void(0)" class="step {{ $stepStatus }}">
        <pre class="bg-white">{{ preg_replace('/\s/', "\n", $item, 1) }}</pre>
        <div class="node" data-toggle="{{ strlen($stepStatus) > 0?'tooltip':'' }}" data-placement="right" title="{{ ucfirst($stepStatus) }}" data-html="true" ></div>
    </a>
    @endforeach
</div>
