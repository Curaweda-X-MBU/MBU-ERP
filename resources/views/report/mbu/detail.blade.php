@extends('templates.main')
@section('title', $title)
@section('content')
@php
dump($detail);
@endphp
<style>
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

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{$title}}</h4>
                <div class="text-right mt-1">
                    <button class="btn btn-outline-secondary dropdown-toggle waves-effect" type="button" id="exportDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Export
                    </button>
                    <div class="dropdown-menu" aria-labelledby="exportDropdown">
                        <button id="exportExcel" class="dropdown-item w-100">Excel</button>
                        <button id="exportPdf" class="dropdown-item w-100">PDF</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row align-items-center my-1">
                    <h4 class="col-md-10">Informasi Umum</h4>
                    <div class="col-md-2">
                        <select name="period" id="period" class="form-control"></select>
                    </div>
                </div>
                @include('report.sections.informasi-umum-lokasi-detail')
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <h4>Kandang</h4>
                    <div class="row">
                        <div class="mx-1"><i class="circle bg-warning"></i>Kandang Diajukan</div>
                        <div class="mx-1"><i class="circle bg-primary"></i>Kandang Aktif</div>
                        <div class="mx-1"><i class="circle bg-secondary"></i>Kandang Non-Aktif</div>
                    </div>
                </div>
                <div id="kandang-container" class="kandang-container">
                    @foreach ($detail->kandangs as $kandang)
                    @php
                        $btnClass = 'warning';
                        if ($kandang->is_active && $kandang->latest_project) {
                            $btnClass = 'primary';
                        } elseif (empty($kandang->latest_project)) {
                            $btnClass = 'secondary';
                        }

                        $href = $kandang->latest_project
                            ? route('report.detail.kandang', ['location' => $detail->location_id, 'project' => $kandang->latest_project]) . '?' . Request::getQueryString()
                            : '#';
                    @endphp
                    <a class="btn mr-1 mt-1 rounded-pill btn-outline-{{ $btnClass }}" href="{{ $href }}">
                        {{ $kandang->name }}
                    </a>
                    @endforeach
                </div>
            </div>
        </div>


        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <button class="nav-link rounded active" id="nav-sapronak-tab" data-toggle="tab" data-target="#nav-sapronak" type="button" role="tab" aria-controls="nav-sapronak" aria-selected="true">Sapronak</button>
                <button class="nav-link rounded" id="nav-perhitungan-sapronak-tab" data-toggle="tab" data-target="#nav-perhitungan-sapronak" type="button" role="tab" aria-controls="nav-perhitungan-sapronak" aria-selected="false" data-load="location_perhitungan_sapronak">Perhitungan Sapronak</button>
                <button class="nav-link rounded" id="nav-penjualan-tab" data-toggle="tab" data-target="#nav-penjualan" type="button" role="tab" aria-controls="nav-penjualan" aria-selected="false" data-load="location_penjualan">Penjualan</button>
                <button class="nav-link rounded" id="nav-overhead-tab" data-toggle="tab" data-target="#nav-overhead" type="button" role="tab" aria-controls="nav-overhead" aria-selected="false" data-load="location_overhead">Overhead</button>
                <button class="nav-link rounded" id="nav-hppEkspedisi-tab" data-toggle="tab" data-target="#nav-hppEkspedisi" type="button" role="tab" aria-controls="nav-hppEkspedisi" aria-selected="false" data-load="location_hpp_ekspedisi">HPP Ekspedisi</button>
                <button class="nav-link rounded" id="nav-dataProduksi-tab" data-toggle="tab" data-target="#nav-dataProduksi" type="button" role="tab" aria-controls="nav-dataProduksi" aria-selected="false" data-load="location_data_produksi">Data Produksi</button>
                <button class="nav-link rounded" id="nav-keuangan-tab" data-toggle="tab" data-target="#nav-keuangan" type="button" role="tab" aria-controls="nav-keuangan" aria-selected="false" data-load="location_keuangan">Keuangan</button>
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show active" id="nav-sapronak" role="tabpanel" aria-labelledby="nav-sapronak-tab">
                @include('report.sections.sapronak-detail')
            </div>
            <div class="tab-pane fade" id="nav-perhitungan-sapronak" role="tabpanel" aria-labelledby="nav-perhitungan-sapronak-tab">
                @include('report.sections.perhitungan-sapronak-detail')
            </div>
            <div class="tab-pane fade" id="nav-penjualan" role="tabpanel" aria-labelledby="nav-penjualan-tab">
                @include('report.sections.penjualan-detail')
            </div>
            <div class="tab-pane fade" id="nav-overhead" role="tabpanel" aria-labelledby="nav-overhead-tab">
                @include('report.sections.overhead-lokasi-detail')
            </div>
            <div class="tab-pane fade" id="nav-hppEkspedisi" role="tabpanel" aria-labelledby="nav-hppEkspedisi-tab">
                @include('report.sections.hpp-ekspedisi-detail')
            </div>
            <div class="tab-pane fade" id="nav-dataProduksi" role="tabpanel" aria-labelledby="nav-dataProduksi-tab">
                @include('report.sections.data-produksi-detail')
            </div>
            <div class="tab-pane fade" id="nav-keuangan" role="tabpanel" aria-labelledby="nav-keuangan-tab">
                @include('report.sections.keuangan-detail')
            </div>
        </div>
    </div>
</div>

<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>

<script>
    // select period
    const $periodSelect = $('#period');
    initSelect2($periodSelect, 'Pilih Periode');

    $('.nav-tabs .nav-link').on('click', function() {
        const $loadState = $(`.${$(this).data('load')}_loaded`);
        if ($loadState.val() != 1) {
            $loadState.val(1).trigger('change');
        }
    });
</script>
@endsection
