<style>
    .td-top{
        vertical-align: top;
    }
    .feather-icon-large {
        width: 20px;
        height: 20px;
    }
</style>

@php
    $warehouseNames = [];
    $locationNames = [];
    $companyName = '';
    $areaName = '';
    foreach ($data->warehouse_details as $key => $value) {
        $warehouseNames[] = $value['warehouse_name'];
        $locationNames[] = $value['location_name'];
        $companyName = $value['company_name'];
        $areaName = $value['area_name'];
    }
    $locationNames = array_unique($locationNames);
@endphp

<div class="card mb-1">
    <div id="headingCollapse3" class="card-header color-header collapsed" data-toggle="collapse" role="button" data-target="#collapse3" aria-expanded="true" aria-controls="collapse3">
        <span class="lead collapse-title"> Item Pembelian </span>
    </div>
    <div id="collapse3" role="tabpanel" aria-labelledby="headingCollapse3" class="collapse show" aria-expanded="true">
        <div class="card-body p-2">
            <div class="col-12">
                <table>
                    <td style="width: 53%; vertical-align: top;">
                        <table class="mb-2">
                            <tr>
                                <td><b>Unit Bisnis</b></td>
                                <td>:</td>
                                <td>{{ $companyName }}</td>
                            </tr>
                            <tr>
                                <td><b>Area</b></td>
                                <td>:</td>
                                <td>{{ $areaName }}</td>
                            </tr>
                            <tr>
                                <td class="td-top"><b>Lokasi</b></td>
                                <td class="td-top">:</td>
                                <td class="td-top">
                                    @foreach ($locationNames as $item)
                                        <div class="badge badge-light-primary">{{ $item }}</div>
                                    @endforeach
                                </td>
                            </tr>
                            <tr>
                                <td class="td-top"><b>Gudang Penyimpanan</b></td>
                                <td class="td-top">:</td>
                                {{-- <td class="td-top">{{ implode(', ', $data->warehouse_names)??'' }}</td> --}}
                                <td class="td-top">
                                    @foreach ($warehouseNames as $item)
                                        <div class="badge badge-light-primary">{{ $item }}</div>
                                    @endforeach
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td style="vertical-align: top;">
                        <table class="mb-2">
                            <tr>
                                <td><b>Nama Vendor</b></td>
                                <td>:</td>
                                <td>{{ $data->supplier->name??'' }}</td>
                            </tr>
                            <tr>
                                <td><b>Alamat Vendor</b></td>
                                <td>:</td>
                                <td>{{ $data->supplier->address??'' }}</td>
                            </tr>
                            <tr>
                                <td><b>Tgl. Dibutuhkan</b></td>
                                <td>:</td>
                                <td>{{ date('d-M-Y', strtotime($data->require_date)) }}</td>
                            </tr>
                            <tr>
                                <td><b>Nomor</b></td>
                                <td>:</td>
                                <td>{{ $data->pr_number }}</td>
                            </tr>
                            <tr>
                                <td><b>Nomor PO</b></td>
                                <td>:</td>
                                <td>
                                    @if ($data->po_number)
                                        <a class="btn btn-sm btn-outline-primary" target="_blank" href="{{ route('purchase.detail', ['id' => $data->purchase_id, 'po_number' => $data->po_number]) }}">
                                            {{ $data->po_number }}
                                        </a>
                                    @else
                                        <i class="text-muted">Belum dibuat</i>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                </table>
            </div>
            <div class="col-12">
                <div class="table-responsive">
                    <form action="{{ route('purchase.edit', $data->purchase_id) }}" id="form-purchase-item">
                    @csrf
                    <table class="table table-sm table-bordered table-striped w-100 no-wrap text-center" id="purchase-repeater">
                        <thead>
                            @if (Auth::user()->role->name === 'Super Admin')
                            <tr>
                                <th colspan="9">
                                    <div class="float-right">
                                        <div id="purchase-item-edit-section">
                                            <a href="javascript:void(0)" class="btn btn-sm btn-primary purchase-item-edit">
                                                <i data-feather="edit-2" class="mr-50"></i>
                                                Edit
                                            </a>
                                        </div>
                                    </div>
                                </th>
                            </tr>
                            @endif
                            <tr>
                                <th class="hidden"></th>
                                <th>Produk</th>
                                <th>Jenis Produk</th>
                                <th width="30">Jumlah</th>
                                <th>Satuan</th>
                                <th>Harga Satuan</th>
                                <th>Pajak (%)</th>
                                <th>Discount (%)</th>
                                <th>Total (Rp.)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data->purchase_item as $item)
                                <tr>
                                    <td class="hidden">{{ $item->purchase_item_id }}</td>
                                    <td>{{ $item->product->name??'' }} {{ $item->product->product_sub_category->name==="DOC"?" (DOC)":'' }}</td>
                                    <td>{{ $item->product->product_category->name??'' }}</td>
                                    <td class="text-right">
                                        @php
                                            $qty = number_format($item->qty, '0', ',', '.');
                                        @endphp
                                        <input type="hidden" name="purchase_item[{{$item->purchase_item_id}}][qty]" value="{{ $qty }}">
                                        {{ $qty }}
                                    </td>
                                    <td>{{ $item->product->uom->name??'' }}</td>
                                    <td class="text-right">{{ number_format($item->price, '0', ',', '.') }}</td>
                                    <td class="text-right">{{ $item->tax }}</td>
                                    <td class="text-right">{{ $item->discount }}</td>
                                    <td class="text-right">{{ number_format($item->price*$item->qty, '0', ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="6" rowspan="4" class="text-left" style="vertical-align: top">
                                    Catatan : <br>{{ $data->notes }} <br>
                                </td>
                                <td colspan="1" class="text-right">
                                    Total Sebelum Pajak
                                </td>
                                <td style="padding: 0 10px 0 10px;" class="text-right">
                                    {{ number_format($data->total_before_tax, '0', ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="1" class="text-right">
                                    Pajak
                                </td>
                                <td style="padding: 0 10px 0 10px;" class="text-right">
                                    {{ number_format($data->total_tax, '0', ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="1" class="text-right">
                                    Discount
                                </td>
                                <td style="padding: 0 10px 0 10px;" class="text-right">
                                    {{ number_format($data->total_discount, '0', ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="1" class="text-right">
                                    <b>Total</b>
                                </td>
                                <td style="padding: 0 10px 0 10px;" class="text-right">
                                    <b>{{ number_format($data->total_after_tax==0?$data->total_before_tax:$data->total_after_tax, '0', ',', '.') }}</b>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function () {
        $(document).on('click', '.purchase-item-edit', function () {
            
            $(this).closest('div').html(`<a javascript:void(0) class="btn btn-sm btn-danger purchase-item-submit" title="Submit">
                <i data-feather="check" class="mr-50"></i>
                Submit
            </a>
            <a javascript:void(0) class="btn btn-sm btn-warning purchase-item-close" title="Close">
                <i data-feather="x" class="mr-50"></i>
                Batal
            </a>
            `);

            feather.replace();
            var $table = $('#purchase-repeater');
            $table.find('tbody tr').each(function () {
                var $row = $(this);
                $row.find('td').each(function (index, element) {
                    var purchaseItemId = $row.find('td').eq(0).text();
                    if ([5, 6, 7].includes(index)) {
                        var originalValue = $(element).text();
                        $(element).data('original-value', originalValue);
                        let inputName = '';
                        switch (index) {
                            case 5:
                                inputName = 'price';
                                break;
                            case 6:
                                inputName = 'tax';
                                break;
                            case 7:
                                inputName = 'discount';
                                break;
                        }
                        $(element).html('<input type="text" class="form-control numeral-mask" name="purchase_item['+ purchaseItemId +']['+inputName+']" value="' + originalValue + '" required>');
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

        $(document).on('click', '.purchase-item-submit', function () {
            const serializedData = $('#form-purchase-item').serialize();
            console.log(serializedData);
            if (confirm('Apakah anda yakin ingin menyimpan data ini?')) {
                $('#form-purchase-item').submit();
            }
        });        

        $(document).on('click', '.purchase-item-close', function () {
            $(this).closest('div').html(`<a href="javascript:void(0)" class="btn btn-sm btn-primary purchase-item-edit">
                <i data-feather="edit-2" class="mr-50"></i>
                Edit
            </a>`);
            feather.replace();
            var $table = $('#purchase-repeater');
            $table.find('tbody tr').each(function () {
                var $row = $(this).closest('tr');
                $row.find('td').each(function (index, element) {
                    if ([5,6,7].includes(index)) {
                        var originalValue = $(element).data('original-value');
                        $(element).html(originalValue);
                    }
                });
            });
        });
    });
</script>