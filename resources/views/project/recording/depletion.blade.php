<div class="table-responsive">
    <table class="table table-bordered w-100 no-wrap text-center" id="depletion">
        <thead>
            <th>Pilih Kondisi</th>
            <th>Jumlah</th>
            <th>Satuan</th>
            <th>
                <button class="btn btn-sm btn-icon btn-primary add-depletion" type="button" data-repeater-create title="Tambah Data">
                    <i data-feather="plus"></i>
                </button>
            </th>
        </thead>
        <tbody data-repeater-list="depletions">
            <tr data-repeater-item>
                <td><select name="product_id" class="product_id form-control" required></select></td>
                {{-- <td><input type="text" name="death" class="death form-control numeral-mask" value="0" required/></td>
                <td><input type="text" name="culling" class="culling form-control numeral-mask" value="0" required/></td>
                <td><input type="text" name="afkir" class="afkir form-control numeral-mask" value="0" required/></td> --}}
                <td>
                    <input type="text" name="total" class="form-control numeral-mask total_depletion" required/>
                </td>
                <td>Ekor</td>
                <td>
                    <button class="btn btn-sm btn-icon btn-danger" data-repeater-delete type="button" title="Hapus Data">
                        <i data-feather="x"></i>
                    </button>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<script>
    $(function () {
        const optDepletion = {
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

                $this.find('.total').val(0);
                $this.find('.product_id').select2({
                    placeholder: "Pilih Kondisi",
                    ajax: {
                        url: `{{ route("data-master.product.search") }}?product_category-category_code=BRO&can_be_purchased=0`, 
                        dataType: 'json',
                        delay: 250, 
                        data: function(params) {
                            
                            return {
                                q: params.term 
                            };
                        },
                        processResults: function(data) {
                            let result = [];
                            var selectedValues = $('#depletion').find('.product_id').map(function() {
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
            },
            hide: function (deleteElement) {
                if (confirm('Apakah kamu yakin ingin menghapus data ini?')) {
                    $(this).slideUp(deleteElement);
                }
            }
        };

        const $repeaterDepletion = $('#depletion').repeater(optDepletion);
        $('.add-depletion').trigger('click');

        const dataRecording = @json($data);
        
        if (dataRecording && dataRecording.recording_depletion) {
            const dataDepletion = dataRecording.recording_depletion;
            $repeaterDepletion.setList(dataDepletion);

            for (let i = 0; i < dataDepletion.length; i++) {
                $(`select[name="depletions[${i}][product_id]"]`).append(`<option value="${dataDepletion[i].product_warehouse.product_id}" selected>${dataDepletion[i].product_warehouse.product.name}</option>`);
            }
        }
    });
</script>