<div class="table-responsive">
    <table class="table table-bordered w-100 no-wrap text-center" id="nonstock-repeater">
        <thead>
            <th>Jenis Recording</th>
            <th>Jumlah</th>
            <th>Satuan</th>
            <th colspan="2">
                <button class="btn btn-sm btn-icon btn-primary add-nonstock" type="button" data-repeater-create title="Tambah Data">
                    <i data-feather="plus"></i>
                </button>
            </th>
        </thead>
        <tbody data-repeater-list="nonstock">
            <tr data-repeater-item>
                <td>
                    <select name="nonstock_id" class="form-control nonstock_id" required></select>
                </td>
                <td><input type="text" class="form-control numeral-mask" name="value" required/></td>
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
        const optNonStock = {
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

                $this.find('.nonstock_id').select2({
                    placeholder: "Pilih Jenis Recording",
                    ajax: {
                        url: `{{ route("data-master.nonstock.search") }}`, 
                        dataType: 'json',
                        delay: 250, 
                        data: function(params) {
                            
                            return {
                                q: params.term 
                            };
                        },
                        processResults: function(data) {
                            let result = [];
                            var selectedValues = $('.nonstock_id').map(function() {
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

                $this.find('.nonstock_id').on('select2:select', function (e) { 
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

        const $repeaterNonstock = $('#nonstock-repeater').repeater(optNonStock);
        $('.add-nonstock').trigger('click');

        const dataRecording = @json($data);
        
        if (dataRecording && dataRecording.recording_nonstock) {
            const dataNonstock = dataRecording.recording_nonstock;
            $repeaterNonstock.setList(dataNonstock);

            for (let i = 0; i < dataNonstock.length; i++) {
                $(`select[name="nonstock[${i}][nonstock_id]"]`).append(`<option value="${dataNonstock[i].nonstock.nonstock_id}" selected>${dataNonstock[i].nonstock.name}</option>`);
                const $selector = $(`select[name="nonstock[${i}][nonstock_id]"]`).closest('tr');
                $selector.find('.uom').val(dataNonstock[i].nonstock.uom.name)
            }
        }
    });
</script>