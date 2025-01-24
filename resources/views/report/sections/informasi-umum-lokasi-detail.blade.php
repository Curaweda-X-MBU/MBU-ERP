@php
    $farmType = App\Constants::KANDANG_TYPE;
@endphp

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
                            <td class="col-md-7">{{ $detail->location }}</td>
                        </tr>
                        <tr>
                            <td class="col-md-4">Periode</td>
                            <td class="col-md-1">:</td>
                            <td class="col-md-7">{{ $detail->period }}</td>
                        </tr>
                        <tr>
                            <td class="col-md-4">Jenis Produk</td>
                            <td class="col-md-1">:</td>
                            <td class="col-md-7">{{ $detail->product }}</td>
                        </tr>
                        <tr>
                            <td class="col-md-4">Jumlah DOC</td>
                            <td class="col-md-1">:</td>
                            <td class="col-md-7">{{ $detail->doc }} Ekor</td>
                        </tr>
                        <tr>
                            <td class="col-md-4">Tanggal Closing</td>
                            <td class="col-md-1">:</td>
                            <td class="col-md-7">11-12-2024</td>
                        </tr>
                        <tr>
                            <td class="col-md-4">Jenis Project</td>
                            <td class="col-md-1">:</td>
                            <td class="col-md-7">{{ $farmType[$detail->farm_type] }}</td>
                        </tr>
                        <tr>
                            <td class="col-md-4">Status Project</td>
                            <td class="col-md-1">:</td>
                            <td class="col-md-7">
                                @switch($detail->project_status)
                                    @case(1)
                                        <div class="badge badge-pill badge-warning">Pengajuan</div>
                                        @break
                                    @case(2)
                                        <div class="badge badge-pill badge-primary">Aktif</div>
                                        @break
                                    @case(3)
                                        <div class="badge badge-pill badge-info">Persiapan</div>
                                        @break
                                    @case(4)
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
                            <td class="col-md-4">Kandang AKtif</td>
                            <td class="col-md-1">:</td>
                            <td class="col-md-7">{{ $detail->active_kandang }} Kandang</td>
                        </tr>
                        <tr>
                            <td class="col-md-4">Tanggal Mulai</td>
                            <td class="col-md-1">:</td>
                            <td class="col-md-7">{{ $detail->start_date }}</td>
                        </tr>
                        <tr>
                            <td class="col-md-4">Tanggal Approval</td>
                            <td class="col-md-1">:</td>
                            <td class="col-md-7">{{ @$detail->approval_date ? date('d-M-Y', strtotime($detail->approval_date)) : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="col-md-4">Status Pembayaran</td>
                            <td class="col-md-1">:</td>
                            <td class="col-md-7">
                                @php
                                    $status = 1;
                                @endphp
                                @switch($status)
                                    @case(1)
                                        <div class="badge badge-pill badge-warning">Belum Bayar</div>
                                        @break
                                    @case(2)
                                        <div class="badge badge-pill badge-success">Sudah Bayar</div>
                                        @break
                                    @default
                                        <div class="badge badge-pill badge-secondary">N/A</div>
                                @endswitch
                            </td>
                        </tr>
                        <tr>
                            <td class="col-md-4">Status Closing</td>
                            <td class="col-md-1">:</td>
                            <td class="col-md-7">
                                @php
                                    $status = 2;
                                @endphp
                                @switch($status)
                                    @case(1)
                                        <div class="badge badge-pill badge-warning">Belum Selesai</div>
                                        @break
                                    @case(2)
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
</div>
