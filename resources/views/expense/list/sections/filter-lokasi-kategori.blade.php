<div class="row">
    <!-- Lokasi -->
    <div class="col-md-2 mt-1">
        <label for="location_id" class="form-label">Lokasi</label>
        <select name="location_id" id="location_id" class="form-control"></select>
    </div>
    <!-- Kategori -->
    <div class="col-md-2 mt-1">
        <label for="category_id" class="form-label">Kategori</label>
        <select name="category_id" id="category_id" class="form-control">
            <option value="">Pilih Kategori</option>
            <option value="bop">BOP (Biaya Operasional)</option>
            <option value="non-bop">Biaya Diluar BOP</option>
        </select>
    </div>
</div>

<!-- container kandang -->
<div id="hatcheryButtonsContainer" class="mt-1"></div>

<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
<script>
    const dataKandang = {
        "1": [
            { "id": 1, "name": "Kandang 1", "status": "Aktif" },
            { "id": 2, "name": "Kandang 2", "status": "Tidak Aktif" },
            { "id": 3, "name": "Kandang 3", "status": "Aktif" },
            { "id": 4, "name": "Kandang 4", "status": "Tidak Aktif" },
        ]
    };

    // Initialize Select2
    const locationIdRoute = '{{ route("data-master.location.search") }}';
    initSelect2($('#location_id'), 'Pilih Lokasi', locationIdRoute);
    initSelect2($('#category_id'), 'Pilih Kategori');

    function renderHatcheryButtons(locationId) {
        const container = $('#hatcheryButtonsContainer');
        container.empty();

        if (dataKandang[locationId]) {
            dataKandang[locationId].forEach(kandang => {
                const button = $('<button>', {
                    class: `btn mr-1 mt-1 rounded-pill ${kandang.status === "Aktif" ? "btn-outline-secondary" : "btn-outline-danger"}`,
                    text: kandang.name,
                    click: function (e) {
                        e.preventDefault();
                        if ($(this).hasClass('btn-outline-primary')) {
                            $(this)
                                .removeClass('btn-outline-primary')
                                .addClass(kandang.status === "Aktif" ? "btn-outline-secondary" : "btn-outline-danger");
                        } else {
                            $(this)
                                .removeClass('btn-outline-secondary btn-outline-danger')
                                .addClass('btn-outline-primary');
                        }
                    }
                });
                container.append(button);
            });
        }
    }

    function updateContainerVisibility() {
        const categoryId = $('#category_id').val();
        const locationId = $('#location_id').val();
        const container = $('#hatcheryButtonsContainer');

        if (categoryId === 'bop') {
            container.show();
            if (locationId) {
                renderHatcheryButtons(locationId);
            }
        } else {
            container.hide();
            container.empty();
        }
    }

    $('#category_id').on('change', function() {
        updateContainerVisibility();
    });

    $('#location_id').on('change', function() {
        if ($('#category_id').val() === 'bop') {
            renderHatcheryButtons($(this).val());
        }
    });

    $(document).ready(function() {
        updateContainerVisibility();
    });
</script>
