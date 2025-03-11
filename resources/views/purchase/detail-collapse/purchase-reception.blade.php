<div class="card mb-1">
    <div id="headingCollapse5" class="card-header color-header collapsed" data-toggle="collapse" role="button" data-target="#collapse5" aria-expanded="true" aria-controls="collapse5">
        <span class="lead collapse-title">Penerimaan Barang</span>
    </div>
    <div id="collapse5" role="tabpanel" aria-labelledby="headingCollapse5" class="collapse show" aria-expanded="true">
        <div class="card-body p-2">
            <div class="table-responsive">
                <table class="w-100 table-sm">
                @foreach ($data->purchase_item as $key => $item)
                <tr>
                    {{-- <td style="vertical-align: top; padding: 0px !important;">{{ $key+1 }}.</td> --}}
                    <td>
                        <form action="{{ route('purchase.edit', $data->purchase_id) }}" id="form-purchase-reception-{{$key}}" method="post">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                            <input type="hidden" name="purchase_item_id" value="{{ $item->purchase_item_id }}">
                        <table class="table table-sm table-bordered table-striped w-100 table-{{$key}}">
                            <thead>
                                <tr>
                                    <th colspan="11" class="text-left">
                                        <div class="float-right">
                                            @if (Auth::user()->role->name === 'Super Admin')
                                                <div id="purchase-item-edit-section">
                                                    <a href="javascript:void(0)" class="btn btn-sm btn-primary purchase-reception-edit-{{$key}}">
                                                        <i data-feather="edit-2" class="mr-50"></i>
                                                        Edit
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                        <h6>{{ $key+1 }}. {{ $item->product->name }} {{ $item->product->product_sub_category->name==="DOC"?" (DOC)":"" }}</h6>
                                    </th>
                                </tr>
                                <tr>
                                    <th class="hidden"></th>
                                    <th>Tanggal Penerimaan</th>
                                    <th>Gudang Tujuan</th>
                                    <th>No. Surat Jalan</th>
                                    <th>Dokumen Surat Jalan</th>
                                    <th>No. Armada Pengangkut</th>
                                    <th>Jumlah Total</th>
                                    <th>Jumlah Diterima</th>
                                    <th>Jumlah Retur</th>
                                    <th>Ekspedisi</th>
                                    <th>Transport /Item</th>
                                    <th>Transport Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($item->purchase_item_alocation) > 0)
                                    @foreach ($item->purchase_item_alocation as $key => $val)
                                        @php
                                            $receivedDate = '';
                                            $travelNumber = '';
                                            $travelNumberDoc = false;
                                            $vehicleNumber = '';
                                            $totalRetur = 0;
                                            $totalRecieved = 0;
                                            $supplierName = '';
                                            $transItem = 0;
                                            $transTotal = 0;
                                            $receptionId = '';
                                            if (count($item->purchase_item_reception) > 0) {
                                                foreach ($item->purchase_item_reception as $k => $v) {
                                                    if ($v->warehouse_id === $val->warehouse_id) {
                                                        $receptionId = $v->purchase_item_reception_id;
                                                        $receivedDate = date('d-M-Y H:i', strtotime($v->received_date));
                                                        $travelNumber = $v->travel_number;
                                                        $travelNumberDoc = $v->travel_number_document;
                                                        $vehicleNumber = $v->vehicle_number;
                                                        $totalRetur = $v->total_retur;
                                                        $supplierName = $v->supplier->name??'';
                                                        $transItem = $v->transport_per_item;
                                                        $transTotal = $v->transport_total;
                                                        $totalRecieved = $v->total_received;
                                                    }
                                                }
                                            }
                                        @endphp
                                        <tr>
                                            <td class="hidden">{{ $receptionId }}</td>
                                            <td>{{ $receivedDate }}</td>
                                            <td>{{ $val->warehouse->name ??'' }}</td>
                                            <td>{{ $travelNumber }}</td>
                                            <td>
                                                @if ($travelNumberDoc)
                                                <a href="{{ route('file.show', ['filename' => $travelNumberDoc]) }}" target="_blank">
                                                    <i data-feather='download' class="mr-50"></i>
                                                    <span>Download</span>
                                                </a>
                                                @endif
                                            </td>
                                            <td>{{ $vehicleNumber }}</td>
                                            <td class="text-right">{{ number_format($val->alocation_qty, '0', ',', '.') }}</td>
                                            <td class="text-right">{{ number_format($totalRecieved, '0', ',', '.') }}</td>
                                            <td class="text-right">{{ number_format($totalRetur??0, '0', ',', '.') }}</td>
                                            <td>{{ $supplierName }}</td>
                                            <td class="text-right">{{ number_format($transItem??0, '0', ',', '.') }}</td>
                                            <td class="text-right">{{ number_format($transTotal??0, '0', ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="11" class="text-center">Belum ada data</td>
                                    </tr>
                                @endif
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="text-right" colspan="9">Jumlah Produk</td>
                                    <td colspan="2" class="text-right">{{ number_format($item->qty, '0', ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-right" colspan="9">Jumlah Produk Diterima</td>
                                    <td colspan="2" class="text-right">{{ number_format($item->total_received, '0', ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-right" colspan="9">Jumlah Produk Belum Diterima</td>
                                    <td colspan="2" class="text-right">{{ number_format($item->total_not_received, '0', ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-right" colspan="9">Nominal Produk Diterima</td>
                                    <td colspan="2">Rp. <span class="pl-2 float-right"> {{ number_format($item->amount_received, '0', ',', '.') }}</span></td>
                                </tr>
                                <tr>
                                    <td class="text-right" colspan="9">Nominal Produk Belum Diterima</td>
                                    <td colspan="2">Rp. <span class="pl-2 float-right"> {{ number_format($item->amount_not_received, '0', ',', '.') }}</span></td>
                                </tr>
                            </tfoot>
                        </table>
                        </form>
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

<script>
    $(function () {
        const purchaseItem = @json($data->purchase_item);
        for (let i = 0; i < purchaseItem.length; i++) {
            $(document).on('click', `.purchase-reception-edit-${i}`, function () {
                $(this).closest('div').html(`<a javascript:void(0) class="btn btn-sm btn-danger purchase-reception-submit-${i}" title="Submit">
                    <i data-feather="check" class="mr-50"></i>
                    Submit
                </a>
                <a javascript:void(0) class="btn btn-sm btn-warning purchase-reception-close-${i}" title="Close">
                    <i data-feather="x" class="mr-50"></i>
                    Batal
                </a>
                `);

                feather.replace();
                var $table = $(`table.table-${i}`);
                $table.find('tbody tr').each(function () {
                    var $row = $(this);
                    $row.find('td').each(function (index, element) {
                        var receptionId = $row.find('td').eq(0).text();
                        if ([6, 7, 10].includes(index)) {
                            var originalValue = $(element).text();
                            $(element).data('original-value', originalValue);
                            let inputName = '';
                            switch (index) {
                                case 6:
                                    inputName = 'qty';
                                    break;
                                case 7:
                                    inputName = 'total_received';
                                    break;
                                case 10:
                                    inputName = 'transport_per_item';
                                    break;
                            }
                            $(element).html('<input type="text" class="form-control numeral-mask '+inputName+'" name="purchase_item_reception['+receptionId+']['+inputName+']" value="' + originalValue + '" required>');
                        }
                    });
                });

                var numeralMask = $('.numeral-mask');
                if (numeralMask.length) {
                    numeralMask.each(function() { 
                        new Cleave(this, {
                            numeral: true,
                            numeralThousandsGroupStyle: 'thousand', numeralDecimalMark: ',', delimiter: '.'
                        });
                    })
                }
            });

            $(document).on('keyup', '.total_received, .transport_per_item', function () {
                var $row = $(this).closest('tr');
                var qty = $row.find('.total_received').val();
                var transportItem = $row.find('.transport_per_item').val();
                var transportTotal = parseInt(transportItem.replace('.', '')) * parseInt(qty.replace('.', ''));
                var formattedTransportTotal = new Intl.NumberFormat('id-ID').format(transportTotal);
                $row.find('td').eq(11).text(formattedTransportTotal);
            });

            $(document).on('click', `.purchase-reception-submit-${i}`, function () {
                const serializedData = $(`#form-purchase-reception-${i}`).serialize();
                console.log(serializedData);
                if (confirm('Data recording yang berkaitan dengan pembelian ini akan terhapus. Apakah anda yakin ingin menyimpan data ini?')) {
                    $(`#form-purchase-reception-${i}`).submit();
                }
            });        

            $(document).on('click', `.purchase-reception-close-${i}`, function () {
                $(this).closest('div').html(`<a href="javascript:void(0)" class="btn btn-sm btn-primary purchase-reception-edit-${i}">
                    <i data-feather="edit-2" class="mr-50"></i>
                    Edit
                </a>`);
                feather.replace();
                var $table = $(`table.table-${i}`);
                $table.find('tbody tr').each(function () {
                    var $row = $(this).closest('tr');
                    $row.find('td').each(function (index, element) {
                        if ([6,7,10].includes(index)) {
                            var originalValue = $(element).data('original-value');
                            $(element).html(originalValue);
                        }
                    });
                });
            });            
        }
    });
</script>