<div class="row">
    <!-- Lokasi -->
    <div class="col-md-2 mt-1">
        <label for="location_id" class="form-label">Lokasi<i class="text-danger">*</i></label>
        <select name="location_id" id="location_id" class="form-control" required></select>
    </div>
    <!-- Kategori -->
    <div class="col-md-2 mt-1">
        <label for="category_id" class="form-label">Kategori<i class="text-danger">*</i></label>
        <select name="category" id="category_id" class="form-control" required>
            <option value="">Pilih Kategori</option>
            <option value="1">BOP (Biaya Operasional)</option>
            <option value="2">Biaya Diluar BOP</option>
        </select>
    </div>
    <!-- Kandang hidden array -->
    <input type="hidden" name="selected_kandangs" value="[]">
</div>

<!-- container kandang -->
<div id="hatcheryButtonsContainer" class="mt-1"></div>

<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
<script>
    $(function() {
        // Initialize Select2
        const locationIdRoute = '{{ route("data-master.location.search") }}';
        const kandangIdRoute = `{{ route('data-master.kandang.search') }}?location_id=:id`;
        const $locationSelect = $('#location_id');
        const $categorySelect = $('#category_id');
        initSelect2($locationSelect, 'Pilih Lokasi', locationIdRoute);
        initSelect2($('#category_id'), 'Pilih Kategori');

        let selectedKandangs; // array

        function renderHatcheryButtons(data) {
            const $container = $('#hatcheryButtonsContainer');
            const $kandangInput = $('input[name="selected_kandangs"]');

            $container.empty();
            $kandangInput.val('[]');
            selectedKandangs = [];

            if (data) {
                const kandangs = data.map(kandang => kandang.data);

                const buttons = [];
                kandangs.forEach(kandang => {
                    const status = !!kandang.project_status;

                    const button = $('<button>', {
                        class: `kandang_select btn mr-1 mt-1 rounded-pill waves-effect ${status ? "btn-outline-secondary" : "btn-outline-danger"}`,
                        text: kandang.name,
                        click: function (e) {
                            e.preventDefault();
                            if ($(this).hasClass('btn-outline-primary')) {
                                // Unselected
                                $(this)
                                    .removeClass('btn-outline-primary')
                                    .addClass(status ? "btn-outline-secondary" : "btn-outline-danger");

                                selectedKandangs = selectedKandangs.filter((id) => id !== kandang.kandang_id);
                            } else {
                                // Selected
                                $(this)
                                    .removeClass('btn-outline-secondary btn-outline-danger')
                                    .addClass('btn-outline-primary');

                                selectedKandangs.push(kandang.kandang_id);
                            }

                            $kandangInput.val(JSON.stringify(selectedKandangs));
                        }
                    }).attr('data-active', status);
                    buttons.push(button);
                });

                // Sort button by project_status
                buttons.sort(function(a, b) {
                    const aActive = $(a).data('active') ? 1 : 0;
                    const bActive = $(b).data('active') ? 1 : 0;
                    return bActive - aActive;
                });

                // Append after sort so active kandang would be shown first
                buttons.forEach((button) => $container.append(button));
            }
        }

        $(document).on('select2:select', '#category_id, #location_id', function() {
            const category_id = $categorySelect.val();
            const location_id = $locationSelect.val();
            if (category_id.toLowerCase() == 1 && location_id) {
                // Fetch kandangs
                $.getJSON(kandangIdRoute.replace(':id', location_id), function(data) {
                    if (data.length) {
                        renderHatcheryButtons(data);
                    }
                });
            } else {
                // Delete kandangs
                renderHatcheryButtons(null);
            }
        });
    });
</script>
