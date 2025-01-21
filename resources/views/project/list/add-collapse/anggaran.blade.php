@php
    $fcr_id = old('fcr_id');
    $fcr_name = old('fcr_name');
    $target_depletion = old('target_depletion');
    $dataBudget = '';
    if (isset($data) && isset($data->project_budget)) {
        $dataBudget = $data->project_budget;
    }
@endphp

<div class="card mb-1">
    <div id="headingCollapse4" class="card-header color-header collapsed" data-toggle="collapse" role="button" data-target="#collapse4" aria-expanded="true" aria-controls="collapse4">
        <span class="lead collapse-title">Estimasi Anggaran Per Kandang</span>
    </div>
    <div id="collapse4" role="tabpanel" aria-labelledby="headingCollapse4" class="collapse show" aria-expanded="true">
        <div class="card-body p-2">
            <div class="col-12">
                <div data-repeater-list="budget" id="anggaran-repeater">
                    <div data-repeater-item class="data-repeater-item">
                        <div class="row d-flex align-items-end">
                            <div class="col-md-3 col-12">
                                <div class="form-group">
                                    <label for="stock_type">Tipe Persediaan</label>
                                    <select name="stock_type" class="form-control stock_type" required>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class="form-group">
                                    <label for="nonstock_id">Jenis Produk</label>
                                    <select name="nonstock_id" class="form-control nonstock_id">
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class="form-group">
                                    <label for="product_category_id">Kategori Persediaan</label>
                                    <select name="product_category_id" class="form-control product_category_id" required>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class="form-group">
                                    <label for="product_id">Produk</label>
                                    <select name="product_id" class="form-control product_id" required>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class="form-group">
                                <label for="qty">Jumlah Pembelian</label>
                                    <input type="text" name="qty" class="form-control numeral-mask qty" placeholder="1234" required>
                                </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class="form-group">
                                    <label for="uom_name">Nama Satuan</label>
                                    <input type="text" class="form-control-plaintext uom_name" disabled>
                                </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class="form-group">
                                <label for="price">Harga Satuan (Rp.)</label>
                                    <input type="text" name="price" class="form-control numeral-mask price" placeholder="1234" required>
                                </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class="form-group">
                                <label for="total">Total (Rp.)</label>
                                    <input type="text" class="form-control-plaintext numeral-mask total" disabled>
                                    <input type="hidden" name="total-input" class="total-input" required>
                                </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class="form-group">
                                    <button class="btn btn-outline-danger text-nowrap px-1" data-repeater-delete type="button">
                                        <i data-feather="x" class="mr-25"></i>
                                        <span>Hapus</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <hr />
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <button class="btn btn-icon btn-primary" type="button" id="addbutton" data-repeater-create>
                            <i data-feather="plus" class="mr-25"></i>
                            <span>Tambah Data Anggaran</span>
                        </button>
                        <div class="float-right">
                            <span><h4>Grand Total : Rp. <span id="grand-total">0</span></h4></span>
                            <input type="hidden" name="total_budget" id="grand-total-input">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{{asset('app-assets/vendors/js/forms/cleave/cleave.min.js')}}"></script>

<script>
    $(function () {
        const optSelect2 = {
            dataType: 'json',
            delay: 250, 
            data: function(params) {
                return {
                    q: params.term 
                };
            },
            processResults: function(data) {
                return {
                    results: data
                };
            },
            cache: true
        }
        const optBudget = {
            initEmpty: true,
            show: function () {
                $this = $(this);
                $this.slideDown();
                // Feather Icons
                if (feather) {
                    feather.replace({ width: 14, height: 14 });
                }

                var numeralMask = $('.numeral-mask');
                if (numeralMask.length) {
                    numeralMask.each(function() { 
                        new Cleave(this, {
                            numeral: true,
                            numeralThousandsGroupStyle: 'thousand', numeralDecimalMark: ',', delimiter: '.'
                        });
                    })
                }

                $stockType = $this.find('.stock_type');
                $nonstockId = $this.find('.nonstock_id');
                $productCategory = $this.find('.product_category_id');
                $product = $this.find('.product_id');
                $total = $this.find('.total');
                $totalInput = $this.find('.total-input');
                $total.val('0');
                $totalInput.val('0');
                $stockType.html(`
                    <option selected disabled>Pilih</option>
                    <option value="1">Persediaan</option>
                    <option value="2">Non Persediaan</option>
                `);

                $nonstockId.html('<option selected disabled>Pilih jenis persediaan terlebih dahulu</option>');
                $productCategory.html('<option selected disabled>Pilih jenis persediaan terlebih dahulu</option>');
                $product.html('<option selected disabled>Pilih kategori produk terlebih dahulu</option>');

                $stockType.change(function (e) { 
                    e.preventDefault();
                    $nonstock = $(this).closest('.col-md-3').next('.col-md-3').find('.nonstock_id');
                    $prodCatStock = $(this).closest('.col-md-3').next('.col-md-3').next('.col-md-3').find('.product_category_id');
                    $prodStock = $(this).closest('.col-md-3').next('.col-md-3').next('.col-md-3').next('.col-md-3').find('.product_id');
                    $uomStock = $(this).closest('.col-md-3').next('.col-md-3').next('.col-md-3').next('.col-md-3').next('.col-md-3').find('.uom_name');
                    $nonstock.add($prodStock).add($prodCatStock).val(null).trigger('change');
                    if ($(this).val() == 2) {
                        $prodCatStock.add($prodStock).removeAttr('required');
                        $prodCatStock.add($prodStock).attr('disabled', 'disabled');

                        $nonstock.removeAttr('disabled');
                        $nonstock.attr('required', 'required');
                        $nonstock.html('');
                        $nonstock.select2({
                            placeholder: "Pilih Persediaan",
                            ajax: {
                                url: '{{ route("data-master.nonstock.search") }}', 
                                ...optSelect2
                            }
                        });
                    } else {
                        $nonstock.removeAttr('required');
                        $nonstock.attr('disabled', 'disabled');
                        $prodCatStock.add($prodStock).removeAttr('disabled');
                        $prodCatStock.add($prodStock).attr('required', 'required');
                        $prodStock.html('<option selected disabled>Pilih kategori produk terlebih dahulu</option>');
                        $prodCatStock.select2({
                            placeholder: "Pilih Kategori Produk",
                            ajax: {
                                url: `{{ route("data-master.product-category.search") }}`, 
                                ...optSelect2
                            }
                        });

                        $prodCatStock.change(function (e) { 
                            e.preventDefault();
                            $prodStock.html('');
                            $prodStock.val(null).trigger('change');
                            const prodCatId = $(this).val();
                            $prodStock.select2({
                                placeholder: "Pilih Produk",
                                ajax: {
                                    url: `{{ route("data-master.product.search") }}?product_category_id=${prodCatId}&can_be_purchased=1`, 
                                    ...optSelect2
                                }
                            });
                        });

                    }

                    $nonstock.add($prodStock).on('select2:select', function (e) { 
                        e.preventDefault();
                        const selectedData = e.params.data.data;
                        $selector = $(this).closest('.col-md-3').next('.col-md-3').next('.col-md-3');
                        if ($(this).is('.nonstock_id')) {
                            $selector = $selector.next('.col-md-3').next('.col-md-3');
                        } 

                        $selector.find('.uom_name').val(selectedData.uom.name); 
                    });
                });

            },
            hide: function (deleteElement) {
                if (confirm('Apakah kamu yakin ingin menghapus data ini?')) {
                    $(this).slideUp(deleteElement);
                    setTimeout(() => {
                        calculateTotal();
                    }, 1000);
                }
            }
        };
        const $budgetRepeater = $('.repeater-default').repeater(optBudget);
        const oldBudget = @json(old("budget"));
        if (oldBudget) {
            $budgetRepeater.setList(oldBudget);
        } 

        if ('{{ $dataBudget }}'.length) {
            const dataBudget = @json($dataBudget);
            if (dataBudget) {
                $budgetRepeater.setList(dataBudget);
            }
        } 
        
        $('#addbutton').trigger('click');

        function calculateAnggaran(set) {
            let price = set.find('.price').val();
            let qty = set.find('.qty').val();
            console.log('calculateAnggaran', [price, qty]);
            
            if (price && qty) {
                price = parseInt(price.replace(/\./g, '').replace(/,/g, '.'));
                qty = parseInt(qty.replace(/\./g, '').replace(/,/g, '.'));
                const total = price*qty;
                if (total >= 0) {
                    set.find('.total-input').val(total);
                    new Cleave(set.find('.total'), {
                        numeral: true,
                        numeralThousandsGroupStyle: 'thousand', numeralDecimalMark: ',', delimiter: '.'
                    }).setRawValue(total);

                    calculateTotal();
                    set.find('.total').removeClass('text-danger');
                } else {
                    set.find('.total, total-input').val();
                    set.find('.total').addClass('text-danger');
                }
            } else {
                set.find('.total, .total-input').val('');
            }
        }

        function calculateTotal() {
            let grandTotal = 0;
            $('.total-input').each(function() {
                const value = parseFloat($(this).val()) || 0;
                grandTotal += value;
            });

            const grandTotalFormatted = new Intl.NumberFormat('de-DE').format(grandTotal);
            $('#grand-total-input').val(grandTotal);
            $('#grand-total').html(grandTotalFormatted);
        }

        $('#anggaran-repeater').on('change', '.price, .qty', function () {
            const set = $(this).closest('[data-repeater-item]');
            calculateAnggaran(set);
        });

        $('.price, .qty').trigger('change');
    });
</script>