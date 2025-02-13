<style>
    .tbl-alocation thead th {
        font-size: 15px;
    }
</style>

<section id="collapsibleModal">
    <div class="row">
        <div class="col-sm-12">
            <div class=" collapse-icon">
                <div class=" p-0">
                    <div class="card mb-1">
                        <div id="inputPrice" class="card-header color-header collapsed" data-toggle="collapse" role="button" data-target="#input-price" aria-expanded="true" aria-controls="input-price">
                            <span class="lead collapse-title"> Input Harga </span>
                        </div>
                        <div id="input-price" role="tabpanel" aria-labelledby="inputPrice" class="collapse show" aria-expanded="true">
                            <div class="card-body p-2">
                                <div class="col-12">
                                    <div class="col-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered w-100 no-wrap text-center">
                                                <thead>
                                                    <th>Produk</th>
                                                    <th>Jenis Produk</th>
                                                    <th width="30">Jumlah</th>
                                                    <th>Satuan</th>
                                                    <th>Harga Satuan</th>
                                                    <th width="30">Pajak (%)</th>
                                                    <th width="30">Discount (%)</th>
                                                    <th>Total (Rp.)</th>
                                                </thead>
                                                <tbody>
                                                    @foreach ($data->purchase_item as $key => $item)
                                                    <tr>
                                                        <td>{{ $item->product->name??'' }}</td>
                                                        <td>{{ $item->product->product_category->name??'' }}</td>
                                                        <td>
                                                            <input type="text" name="purchase_item[{{$item->purchase_item_id}}][qty]" class="form-control-plaintext text-right numeral-mask" id="qty-{{$key}}" value="{{ $item->qty }}">
                                                        </td>
                                                        <td>{{ $item->product->uom->name??'' }}</td>
                                                        <td class="text-right">
                                                            <input type="text" name="purchase_item[{{$item->purchase_item_id}}][price]" id="price-{{$key}}" class="form-control price-text text-right numeral-mask" placeholder="Harga Satuan" value="{{ $item->price??$item->product->product_price }}" required>
                                                        </td>
                                                        <td class="text-right">
                                                            <input type="text" name="purchase_item[{{$item->purchase_item_id}}][tax]" id="tax-{{$key}}" max="100" class="form-control price-text text-right numeral-mask" placeholder="Pajak" required value="{{ $item->tax }}">
                                                        </td>
                                                        <td class="text-right">
                                                            <input type="text" name="purchase_item[{{$item->purchase_item_id}}][discount]" id="discount-{{$key}}" max="100" class="form-control price-text text-right numeral-mask" placeholder="Discount" required value="{{ $item->discount }}">
                                                        </td>
                                                        <td class="text-right">
                                                            <input type="text" class="form-control-plaintext text-right numeral-mask" id="total-{{$key}}" value="{{$item->total}}" readonly>
                                                            <input type="hidden" name="purchase_item[{{$item->purchase_item_id}}][total]" id="total-input-{{ $key }}" value="{{$item->total}}">
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 mt-2">
                                    <div class="col-12">
                                        <h4><li>Alokasi Produk</li></h4>
                                        @foreach ($data->purchase_item as $key => $item)
                                        <div class="table-responsive mt-2">
                                            <input type="hidden" id="item-qty-{{$item->product_id}}" value="{{ $item->qty??0 }}">
                                            <table class="table table-bordered w-100 no-wrap text-center tbl-alocation" id="purchase-alocation-repeater-{{ $item->product_id }}">
                                                <thead>
                                                    <th class="text-left w-50">{{ $item->product->name??'' }}</th>
                                                    <th id="remain-qty-{{$item->product_id}}" class="remain-item">{{ $item->qty??'' }}</th>
                                                    <th class="w-25">
                                                        <button class="btn btn-sm btn-icon btn-primary" type="button" id="add-btn-alocation" data-repeater-create title="Tambah Item">
                                                            <i data-feather="plus"></i>
                                                        </button>
                                                    </th>
                                                </thead>
                                                <tbody>
                                                    <tbody data-repeater-list="purchase_alocation[{{ $item->product_id }}]">
                                                        <tr data-repeater-item>
                                                            <td>
                                                                <input type="hidden" class="alocation-product-id" value="{{ $item->product_id }}">
                                                                <select name="warehouse_id" class="form-control warehouseid" required></select>
                                                            </td>
                                                            <td><input type="text" name="alocation_qty" class="alocation-qty-{{ $item->product_id }} alocation-input form-control numeral-mask text-right" placeholder="Jumlah Alokasi" required/></td>
                                                            <td>
                                                                <button class="btn btn-sm btn-icon btn-danger" data-repeater-delete type="button" title="Hapus Item">
                                                                    <i data-feather="x"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-1">
                        <div id="otherPurchase" class="card-header color-header collapsed" data-toggle="collapse" role="button" data-target="#other-purchase" aria-expanded="true" aria-controls="other-purchase">
                            <span class="lead collapse-title"> Biaya Lainnya (Optional)</span>
                        </div>
                        <div id="other-purchase" role="tabpanel" aria-labelledby="otherPurchase" class="collapse show" aria-expanded="true">
                            <div class="card-body p-2">
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered w-100 no-wrap text-center" id="purchase-other-repeater">
                                            <thead>
                                                <th>Nama Biaya</th>
                                                <th>Harga</th>
                                                <th colspan="2">
                                                    <button class="btn btn-sm btn-icon btn-primary" type="button" id="add-btn-other" data-repeater-create title="Tambah Item">
                                                        <i data-feather="plus"></i>
                                                    </button>
                                                </th>
                                            </thead>
                                            <tbody data-repeater-list="purchase_other">
                                                <tr data-repeater-item>
                                                    <td><input type="text" name="name" class="form-control" placeholder="Nama Biaya" required/></td>
                                                    <td><input type="text" name="amount" class="amount form-control numeral-mask text-right" placeholder="Harga" required/></td>
                                                    <td>
                                                        <button class="btn btn-sm btn-icon btn-danger" data-repeater-delete type="button" title="Hapus Item">
                                                            <i data-feather="x"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
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
</section>

<script>
    $(function () {
        const dataItem = @json($data->purchase_item);
        if (dataItem.length > 0) {
            for (let i = 0; i < dataItem.length; i++) {
                const idPrice = `#price-${i}`;
                const idTax = `#tax-${i}`;
                const idDiscount = `#discount-${i}`;
                const idQty = `#qty-${i}`;
                const idTotal = `#total-${i}`;
                const idTotalInput = `#total-input-${i}`;

                $(`${idPrice}, ${idTax}, ${idDiscount}`).change(function (e) { 
                    let price =$(idPrice).val();
                    let qty =$(idQty).val();
                    let tax = parseFloat($(idTax).val());
                    let discount = parseFloat($(idDiscount).val());

                    if (price && qty) {
                        price = parseInt(price.replace(/\./g, '').replace(/,/g, '.'));
                        qty = parseInt(qty.replace(/\./g, '').replace(/,/g, '.'));
                        let totalPerQty = price*qty;
                        if (totalPerQty >= 0) {
                            const totalTax = tax*totalPerQty/100;
                            const totalDiscount = discount*totalPerQty/100;
                            let total = totalPerQty+totalTax-totalDiscount;
                            $(idTotalInput).val(total);
                            new Cleave($(idTotal), {
                                numeral: true,
                                numeralThousandsGroupStyle: 'thousand', numeralDecimalMark: ',', delimiter: '.'
                            }).setRawValue(total);
                        } else {
                            $(`${idTotal}, ${idTotalInput}`).val(0);
                        }
                    } else {
                        $(`${idTotal}, ${idTotalInput}`).val(0);
                    }
                });
            }
        }

        $('.price-text').trigger('change');
    });
</script>

