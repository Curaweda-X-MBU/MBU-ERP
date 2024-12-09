<div class="table-responsive">
    <table class="table table-bordered w-100 no-wrap text-center" id="stock-repeater">
        <thead>
            <th>Persediaan</th>
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
                <td><input type="text" class="form-control numeral-mask" name="decrease_stock" required/></td>
                <td><input type="text" class="form-control-plaintext uom" readonly/></td>
                <td>
                    <button class="btn btn-sm btn-icon btn-danger" data-repeater-delete type="button" title="Hapus Fase">
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
                            var selectedValues = $('.product_id').map(function() {
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

                $this.find('.product_id').on('select2:select', function (e) { 
                    e.preventDefault();
                    const selectedData = e.params.data.data;
                    $(this).closest('td').next().next().find('.uom').val(selectedData.uom.name)
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