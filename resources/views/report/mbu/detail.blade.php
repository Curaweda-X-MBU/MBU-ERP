@extends('templates.main')
@section('title', $title)
@section('content')

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
                <h4>Informasi Umum</h4>
                @include('report.sections.informasi-umum-detail')
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h4>Kandang</h4>
                <div id="kandang-container" class="kandang-container"></div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                      <button class="nav-link active" id="nav-sapronak-tab" data-toggle="tab" data-target="#nav-sapronak" type="button" role="tab" aria-controls="nav-sapronak" aria-selected="true">Sapronak</button>
                      <button class="nav-link" id="nav-perhitungan-sapronak-tab" data-toggle="tab" data-target="#nav-perhitungan-sapronak" type="button" role="tab" aria-controls="nav-perhitungan-sapronak" aria-selected="false">Perhitungan Sapronak</button>
                      <button class="nav-link" id="nav-penjualan-tab" data-toggle="tab" data-target="#nav-penjualan" type="button" role="tab" aria-controls="nav-penjualan" aria-selected="false">Penjualan</button>
                      <button class="nav-link" id="nav-overhead-tab" data-toggle="tab" data-target="#nav-overhead" type="button" role="tab" aria-controls="nav-overhead" aria-selected="false">Overhead</button>
                      <button class="nav-link" id="nav-hppEkspedisi-tab" data-toggle="tab" data-target="#nav-hppEkspedisi" type="button" role="tab" aria-controls="nav-hppEkspedisi" aria-selected="false">HPP Ekspedisi</button>
                      <button class="nav-link" id="nav-dataProduksi-tab" data-toggle="tab" data-target="#nav-dataProduksi" type="button" role="tab" aria-controls="nav-dataProduksi" aria-selected="false">Data Produksi</button>
                      <button class="nav-link" id="nav-keuangan-tab" data-toggle="tab" data-target="#nav-keuangan" type="button" role="tab" aria-controls="nav-keuangan" aria-selected="false">Keuangan</button>
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
                        @include('report.sections.overhead-detail')
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
    </div>
</div>

<script>
    const dataKandang = [
        { name: "Pandeglang 1", status: "aktif", link: "/report/mbu" },
        { name: "Pandeglang 2", status: "tidak aktif", link: "/report/mbu" },
        { name: "Pandeglang 3", status: "aktif", link: "/report/mbu" },
        { name: "Pandeglang 4", status: "tidak aktif", link: "/report/mbu" },
        { name: "Pandeglang 5", status: "aktif", link: "/report/mbu" }
    ];

    function generateKandang() {
        const container = document.getElementById("kandang-container");
        container.innerHTML = "";

        dataKandang.forEach((kandang) => {
            const kandangItem = document.createElement("button");
            kandangItem.className = `btn mr-1 mt-1 rounded-pill btn-outline-${
                kandang.status === "aktif" ? "success" : "danger"
            }`;
            kandangItem.textContent = kandang.name;

            kandangItem.onclick = () => {
                window.location.href = kandang.link;
            };

            container.appendChild(kandangItem);
        });
    }

    generateKandang();
</script>




@endsection
