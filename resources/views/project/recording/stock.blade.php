<div class="table-responsive">
    <table class="table table-bordered w-100 no-wrap text-center" id="stock-repeater">
        <thead>
            <th>Persediaan</th>
            <th>Total Stock Saat Ini</th>
            <th>Jumlah Stock Digunakan</th>
            <th>Satuan</th>
            <th colspan="2">
                <button class="btn btn-sm btn-icon btn-primary add-stock" type="button" data-repeater-create title="Tambah Data">
                    <i data-feather="plus"></i>
                </button>
            </th>
        </thead>
        <tbody data-repeater-list="stock">
            <tr data-repeater-item>
                <td>
                    <select name="product_id" class="form-control product_id" required></select>
                </td>
                <td><input type="text" class="form-control-plaintext numeral-mask text-center current-stock" readonly/></td>
                <td><input type="text" class="form-control numeral-mask decrease_stock" name="decrease_stock" required/></td>
                <td><input type="text" class="form-control-plaintext uom" readonly/></td>
                <td>
                    <button class="btn btn-sm btn-icon btn-danger" data-repeater-delete type="button" title="Hapus Stock">
                        <i data-feather="x"></i>
                    </button>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<script>
    $(function () {
        const optStock = {
            initEmpty: true,
            show: function (e) {
                var $this = $(this);
                $this.slideDown();
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

                $this.find('.product_id').select2({
                    placeholder: "Pilih Persediaan",
                    ajax: {
                        url: `{{ route("data-master.product.search") }}`, 
                        dataType: 'json',
                        delay: 250, 
                        data: function(params) {
                            
                            return {
                                q: params.term 
                            };
                        },
                        processResults: function(data) {
                            let result = [];
                            var selectedValues = $('#stock-repeater').find('.product_id').map(function() {
                                return parseInt($(this).val()); 
                            }).get();
                            
                            data.forEach(val => {
                                let option = {
                                    id: val.id,
                                    text: val.text,
                                    data: val.data
                                };
                               
                                if (selectedValues.includes(val.id)) {
                                    option.disabled = true;
                                }
                                result.push(option);
                            });
                            
                            return {
                                results: result
                            };
                        },
                        cache: true
                    }
                });

                $this.find('.current-stock').val(0);
                $this.find('.product_id').on('select2:select', function (e) { 
                    e.preventDefault();
                    const productId = e.params.data.id;
                    const selectedData = e.params.data.data;
                    $(this).closest('td').next().next().next().find('.uom').val(selectedData.uom.name);
                    $(this).closest('td').next().next().find('.decrease_stock').val(null);
                    $.ajax({
                        type: "post",
                        url: "{{ route('inventory.product.check-stock-by-warehouse') }}",
                        data: {
                            product_id: productId,
                            warehouse_id: $('#warehouse_id').val()
                        },
                        beforeSend: function() {
                            $this.find('.current-stock').val(null);
                        },
                        success: function (response) {
                            $this.find('.current-stock').val(response);
                            var numeralMask = $('.numeral-mask');
                            if (numeralMask.length) {
                                numeralMask.each(function() { 
                                    new Cleave(this, {
                                        numeral: true,
                                        numeralThousandsGroupStyle: 'thousand', numeralDecimalMark: ',', delimiter: '.'
                                    });
                                })
                            }
                        }
                    });
                });

                $this.find('.decrease_stock').change(function (e) { 
                    e.preventDefault();
                    const totalUsed = $(this).val().replace(/\./g, '').replace(/,/g, '.');
                    const currentStock = $this.find('.current-stock').val().replace(/\./g, '').replace(/,/g, '.');
                    
                    if (parseInt(totalUsed) > parseInt(currentStock) ) {
                        $(this).val(null);
                        alert('Jumlah stok digunakan harus lebih kecil atau sama dengan total stok saat ini');
                    }
                });
            },
            hide: function (deleteElement) {
                if (confirm('Apakah kamu yakin ingin menghapus data ini?')) {
                    $(this).slideUp(deleteElement);
                }
            }
        };

        const $repeaterStock = $('#stock-repeater').repeater(optStock);
        $('.add-stock').trigger('click');
    });
</script>