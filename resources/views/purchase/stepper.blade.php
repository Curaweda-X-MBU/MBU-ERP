<link rel="stylesheet" href="{{asset('assets/css/stepper.css')}}">
<style>
    .tooltip-inner {
        text-align: left
    }
</style>

<div class="wizard-progress">
    @foreach ($purchase_status as $key => $item)
    @php
        $stepStatus = '';
        $arrApprLine = collect(json_decode($data->approval_line));
        $approvalHistory = $arrApprLine->where('status', $key)->first();
        if (isset($approvalHistory->status)) {
            if ($approvalHistory->status === 6) {
                $purchase_approval['Dibayar Sebagian'] = "Manager Finance";
            }
        }

        $tooltipNotes = isset($approvalHistory->notes) && $approvalHistory->notes ? '<br> Catatan : '.$approvalHistory->notes:'';
        $tooltipMsg = $approvalHistory
            ? date('d-M-Y H:i', strtotime($approvalHistory->date??'')).
            '<br>Oleh : '.$approvalHistory->action_by.$tooltipNotes:($data->status === 8 ?'Lunas':'Menunggu persetujuan<br>'.$purchase_approval[$item]);

        if ($data->status >= $key) {
            if ($data->status === $key && $data->status !== 8) {
                if ($data->rejected) {
                    $stepStatus = 'rejected';
                } else  {
                    $stepStatus = 'in-progress';
                } 
            } else {
                $stepStatus = 'complete';
            }
        }
    @endphp
    <a href="javascript:void(0)" class="step {{ $stepStatus }}">
        <pre>{{ preg_replace('/\s/', "\n", $item, 1) }}</pre>
        <div class="node" data-toggle="{{ strlen($stepStatus) > 0?'tooltip':'' }}" data-placement="right" title="{{ $tooltipMsg }}" data-html="true" ></div>
    </a>
    @endforeach
</div>