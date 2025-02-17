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
                
                const warehouseId = $('#warehouse_id').val();
                $this.find('.product_id').select2({
                    placeholder: "Pilih Persediaan",
                    ajax: {
                        url: `{{ route("inventory.product.search-product-warehouse") }}?warehouse_id=${warehouseId}`, 
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

                                if (val.data.product.product_category.category_code === 'RAW') {
                                    result.push(option);
                                }
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
                    $(this).closest('td').next().next().next().find('.uom').val(selectedData.product.uom.name);
                    getCurrentStock(productId, $('#warehouse_id').val(), $this)
                });

                $this.find('.decrease_stock').change(function (e) { 
                    e.preventDefault();
                    const totalUsed = $(this).val().replace(/\./g, '').replace(/,/g, '.');
                    const currentStock = $this.find('.current-stock').val().replace(/\./g, '').replace(/,/g, '.');
                    
                    if (parseInt(totalUsed) > parseInt(currentStock) ) {
                        $(this).val(null);
                        alert('Jumlah stok digunakan harus kurang dari atau sama dengan total stok saat ini');
                    }
                });
            },
            hide: function (deleteElement) {
                if (confirm('Apakah kamu yakin ingin menghapus data ini?')) {
                    $(this).slideUp(deleteElement);
                }
            }
        };

        function getCurrentStock(productId, warehouseId, $this) {
            $.ajax({
                type: "post",
                url: "{{ route('inventory.product.check-stock-by-warehouse') }}",
                data: {
                    product_id: productId,
                    warehouse_id: warehouseId
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
        }

        const $repeaterStock = $('#stock-repeater').repeater(optStock);
        // $('.add-stock').trigger('click');
        const dataRecording = @json($data);
        
        if (dataRecording && dataRecording.recording_stock) {
            const dataStock = dataRecording.recording_stock;
            dataStock.forEach(item => {
                item.decrease_stock = item.decrease
            });

            $repeaterStock.setList(dataStock);

            for (let i = 0; i < dataStock.length; i++) {
                $(`select[name="stock[${i}][product_id]"]`).append(`<option value="${dataStock[i].product_warehouse.product.product_id}" selected>${dataStock[i].product_warehouse.product.name}</option>`);
                const $selector = $(`select[name="stock[${i}][product_id]"]`).closest('tr');
                const productId = dataStock[i].product_warehouse.product.product_id;
                const warehouseId = dataStock[i].product_warehouse.warehouse_id;
                getCurrentStock(productId, warehouseId, $selector)
                $selector.find('.uom').val(dataStock[i].product_warehouse.product.uom.name)
            }
        }
    });
</script>