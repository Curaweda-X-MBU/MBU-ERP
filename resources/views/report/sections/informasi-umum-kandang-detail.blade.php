<div class="row">
    {{-- left column --}}
    <div class="col-md-6 col-12">
        <div class="card-datatable">
            <div class="table-responsive mb-2">
                <table id="datatable" class="table table-borderless table-striped w-100">
                    <tbody>
                        <tr>
                            <td class="col-md-4">Lokasi</td>
                            <td class="col-md-1">:</td>
                            <td class="col-md-7">Pandeglang</td>
                        </tr>
                        <tr>
                            <td class="col-md-4">Periode</td>
                            <td class="col-md-1">:</td>
                            <td class="col-md-7">9</td>
                        </tr>
                        <tr>
                            <td class="col-md-4">Jenis Produk</td>
                            <td class="col-md-1">:</td>
                            <td class="col-md-7">AYAM BROILER</td>
                        </tr>
                        <tr>
                            <td class="col-md-4">Tanggal Masuk DOC</td>
                            <td class="col-md-1">:</td>
                            <td class="col-md-7">12-10-2024</td>
                        </tr>
                        <tr>
                            <td class="col-md-4">Jumlah DOC</td>
                            <td class="col-md-1">:</td>
                            <td class="col-md-7">Jumlah DOC</td>
                        </tr>
                        <tr>
                            <td class="col-md-4">Tanggal Closing</td>
                            <td class="col-md-1">:</td>
                            <td class="col-md-7">11-12-2024</td>
                        </tr>
                        <tr>
                            <td class="col-md-4">Jenis Project</td>
                            <td class="col-md-1">:</td>
                            <td class="col-md-7">Own Farm</td>
                        </tr>
                        <tr>
                            <td class="col-md-4">Status Project</td>
                            <td class="col-md-1">:</td>
                            <td class="col-md-7">
                                @php
                            $status = 1;
                        @endphp
                        @switch($status)
                            @case(1)
                                <div class="badge badge-pill badge-success">Selesai</div>
                                @break
                            @default
                                <div class="badge badge-pill badge-secondary">N/A</div>
                        @endswitch
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- right column --}}
    <div class="col-md-6 col-12">
        <div class="card-datatable">
            <div class="table-responsive mb-2">
                <table id="datatable" class="table table-borderless table-striped w-100">
                    <tbody>
                        <tr>
                            <td class="col-md-4">Nama Kandang</td>
                            <td class="col-md-1">:</td>
                            <td class="col-md-7">Pandeglang 1</td>
                        </tr>
                        <tr>
                            <td class="col-md-4">Tanggal Mulai</td>
                            <td class="col-md-1">:</td>
                            <td class="col-md-7">30-09-2024</td>
                        </tr>
                        <tr>
                            <td class="col-md-4">Jenis Ayam</td>
                            <td class="col-md-1">:</td>
                            <td class="col-md-7">CP VAKSIN (50.000 ekor)</td>
                        </tr>
                        <tr>
                            <td class="col-md-4">PPL / TS</td>
                            <td class="col-md-1">:</td>
                            <td class="col-md-7">TULUS</td>
                        </tr>
                        <tr>
                            <td class="col-md-4">Tanggal Approval</td>
                            <td class="col-md-1">:</td>
                            <td class="col-md-7">11-12-2024</td>
                        </tr>
                        </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
