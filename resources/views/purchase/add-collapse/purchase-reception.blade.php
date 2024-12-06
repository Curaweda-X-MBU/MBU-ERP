<section id="collapsibleModal">
    <div class="row">
        <div class="col-sm-12">
            <div class=" collapse-icon">
                <div class=" p-0">
                    <div class="card mb-1">
                        <div id="inputReception" class="card-header color-header collapsed" data-toggle="collapse" role="button" data-target="#input-reception" aria-expanded="true" aria-controls="input-reception">
                            <span class="lead collapse-title"> Input Data Penerimaan Barang </span>
                        </div>
                        <div id="input-reception" role="tabpanel" aria-labelledby="inputReception" class="collapse show" aria-expanded="true">
                            <div class="card-body p-2">
                                <div class="col-12">
                                    <div class="col-12">
                                        <div class="table-responsive">
                                            <table class="w-100">
                                                @foreach ($data->purchase_item as $key => $item)
                                                <tr>
                                                    <td style="vertical-align: top; padding: 0px !important;">{{ $key+1 }}.</td>
                                                    <td>
                                                        <table class="mb-1">
                                                            <tr>
                                                                <td>Nama Produk</td>
                                                                <td>:</td>
                                                                <td class="text-left">{{ $item->product->name }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Jumlah</td>
                                                                <td>:</td>
                                                                <td>{{ number_format($item->qty, '0', ',', '.') }}</td>
                                                            </tr>
                                                        </table>
                                                        <table class="table table-bordered w-100" id="purchase-reception-repeater-{{$item->purchase_item_id}}">
                                                            <thead>
                                                                <th>Tanggal Penerimaan</th>
                                                                <th>No. Surat Jalan</th>
                                                                <th>Dokumen Surat Jalan (2 MB)</th>
                                                                <th>No. Armada Pengangkut</th>
                                                                <th>Jumlah Diterima</th>
                                                                <th>Jumlah Retur</th>
                                                                <th>
                                                                    <button class="btn btn-sm btn-icon btn-primary" type="button" id="add-btn-reception-{{$item->purchase_item_id}}" data-repeater-create title="Tambah Item">
                                                                        <i data-feather="plus"></i>
                                                                    </button>
                                                                </th>
                                                            </thead>
                                                            <tbody data-repeater-list="purchase_item_reception_{{$item->purchase_item_id}}">
                                                                <tr data-repeater-item>
                                                                    <td>
                                                                        <input type="text" class="form-control flatpickr-basic" name="date" placeholder="Tanggal" required/>
                                                                        <input type="text" class="flatpickr-time delivery-time form-control text-left" name="time" placeholder="Jam Penerimaan" required/>
                                                                    </td>
                                                                    <td><input type="text" class="form-control" name="travel_number" placeholder="No. Surat Jalan" required></td>
                                                                    <td><input type="file" class="form-control sj-doc" name="travel_number_document" accept=".pdf, image/jpeg"></td>
                                                                    <td><input type="text" class="form-control" name="vehicle_number" placeholder="Plat Nomor" required></td>
                                                                    <td><input type="text" name="total_received" class="form-control numeral-mask text-right" placeholder="Total Diterima" required /></td>
                                                                    <td><input type="text" name="total_retur" class="form-control numeral-mask text-right" placeholder="Total Retur" required/></td>
                                                                    <td>
                                                                        <button class="btn btn-sm btn-icon btn-danger" data-repeater-delete type="button" title="Hapus Item">
                                                                            <i data-feather="x"></i>
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" style="padding-left: 0px !important;"><hr></td>
                                                </tr>
                                                @endforeach
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

