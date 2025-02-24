@extends('templates.main')
@section('title', $title)
@section('content')
@php
    $nominalBiaya = $data->grand_total;
    $nominalSisaBayar = $data->expense_disburses->sum('payment_nominal');
@endphp

<style>
    .color-header {
        color: white;
        background: linear-gradient(118deg, #76A8D8, #c9e5ff);
    }
    .color-header.success{
        background: linear-gradient(118deg, #4CA555, #54FF65);
    }
    th {
        padding-left: 10px !important;
        padding-right: 10px !important;
    }
    .nav-link {
        background: white;
        margin-right: 1rem;
    }
    .nav-link.active {
        background-color: #79AEDD !important;
        color: white !important;
    }

    .nav-tabs {
        margin-bottom: 0 !important;
    }

    .tab-content {
        margin-top: 0 !important;
    }
    .circle {
        display: inline-block;
        border-radius: 100%;
        height: 1em;
        width: 1em;
        margin: auto 0.5rem;
    }
</style>

<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">

<script>
    function setApproval(value) {
        $('#is_approved').val(value);
        $('#approveForm').trigger('submit');
    }
</script>

<div class="row">
    <div class="col-12">
        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <button class="nav-link rounded active" id="nav-detail-pengajuan-tab" data-toggle="tab" data-target="#nav-detail-pengajuan" type="button" role="tab" aria-controls="nav-detail-pengajuan" aria-selected="true">Pengajuan</button>
                <button class="nav-link rounded" id="nav-detail-realisasi-tab" data-toggle="tab" data-target="#nav-detail-realisasi" type="button" role="tab" aria-controls="nav-detail-realisasi">Realisasi</button>
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show active" id="nav-detail-pengajuan" role="tabpanel" aria-labelledby="nav-detail-pengajuan-tab">
            @include('expense.list.sections.detail-pengajuan')
            </div>
            <div class="tab-pane fade show" id="nav-detail-realisasi" role="tabpanel" aria-labelledby="nav-detail-realisasi-tab">
            @include('expense.list.sections.detail-realisasi')
            </div>
        </div>
    </div>
</div>

@include('expense.list.sections.biaya-pengajuan-card')
@include('expense.list.sections.biaya-realisasi-card')

<script>
    $(function() {
        $('#nav-tab').on('click', 'button', function() {
            setTimeout(() => {
                const isShow = $('#nav-detail-realisasi-tab').hasClass('active');
                $('#biaya-realisasi-card').toggleClass('show', isShow);
            }, 0);
        });
    });
</script>
@endsection
