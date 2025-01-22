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
                                                            <input type="text" name="purchase_item[{{$item->purchase_item_id}}][price]" id="price-{{$key}}" class="form-control text-right numeral-mask" placeholder="Harga Satuan" value="{{ $item->price }}" required>
                                                        </td>
                                                        <td class="text-right">
                                                            <input type="text" name="purchase_item[{{$item->purchase_item_id}}][tax]" id="tax-{{$key}}" max="100" class="form-control text-right numeral-mask" placeholder="Pajak" required value="{{ $item->tax }}">
                                                        </td>
                                                        <td class="text-right">
                                                            <input type="text" name="purchase_item[{{$item->purchase_item_id}}][discount]" id="discount-{{$key}}" max="100" class="form-control text-right numeral-mask" placeholder="Discount" required value="{{ $item->discount }}">
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
                                                    <td><input type="text" name="name" class="form-control" placeholder="Nama Biaya"/></td>
                                                    <td><input type="text" name="amount" class="amount form-control numeral-mask text-right" placeholder="Harga"/></td>
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
    });
</script>

