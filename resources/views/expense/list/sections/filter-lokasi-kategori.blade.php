@php
$category = \App\Constants::EXPENSE_CATEGORY;
$kandangs = '';
$selected_kandang_id = [];
if (isset($data->expense_kandang) && !$data->expense_kandang->isEmpty()) {
    $kandangs = \App\Models\DataMaster\Kandang::where('location_id', $data->location_id)
        ->get()
        ->sortByDesc('project_status');
    $selected_kandang_id = $data->expense_kandang->map(fn($k) => $k->kandang_id)->toArray();
}
@endphp

<style>
.color-information {
    visibility: hidden;
}
.circle {
    display: inline-block;
    border-radius: 100%;
    height: 1em;
    width: 1em;
    margin: auto 0.5rem;
}
</style>

<div class="row">
    <!-- Lokasi -->
    <div class="col-md-2 mt-1">
        <label for="location_id" class="form-label">Lokasi<i class="text-danger">*</i></label>
        <select name="location_id" id="location_id" class="form-control" required>
            @if (@$data->location_id)
            <option value="{{ $data->location_id }}" selected>{{ $data->location->name }}</option>
            @endif
        </select>
    </div>
    <!-- Kategori -->
    <div class="col-md-2 mt-1">
        <label for="category_id" class="form-label">Kategori<i class="text-danger">*</i></label>
        <select name="category" id="category_id" class="form-control" required>
            <option value="">Pilih Kategori</option>
            <option value="1" {{ @$data->category == array_search('Biaya Operasional', $category) ? 'selected' : '' }}>Biaya Operasional</option>
            <option value="2" {{ @$data->category == array_search('Bukan BOP', $category) ? 'selected' : '' }}>Bukan BOP</option>
        </select>
    </div>
    <!-- Color information -->
    <div class="color-information col mt-1">
        <h5 class="text-right">Keterangan Kandang</h5>
        <div class="row justify-content-end">
            <div class="m-1"><i class="circle bg-primary"></i>Dipilih</div>
            <div class="m-1"><i class="circle bg-danger"></i>Kandang Non-Aktif</div>
            <div class="m-1"><i class="circle bg-secondary"></i>Kandang Aktif</div>
        </div>
    </div>
    <!-- Kandang hidden array -->
    <input type="hidden" name="selected_kandangs" value="{{ @$data->expense_kandang ? $data->expense_kandang->map(fn($k) => $k->kandang_id) : '[]' }}">
</div>

<!-- container kandang -->
<div id="hatcheryButtonsContainer" class="mt-1">
    @if (! empty($kandangs))
        @foreach ($kandangs as $kandang)
        @php
        $is_active = $kandang->project_status;
        @endphp

        <button
            type="button"
            class="kandang_select btn mr-1 mt-1 rounded-pill waves-effect {{ in_array($kandang->kandang_id, $selected_kandang_id) ? 'btn-outline-primary' : ($is_active ? 'btn-outline-secondary' : 'btn-outline-danger') }}"
            data-active={{ $is_active }}
            data-kandang-id={{ $kandang->kandang_id }}
        >
            {{ $kandang->name }}
        </button>
        @endforeach
    @endif
</div>

<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
<script>
    $(function() {
        // Initialize Select2
        const locationIdRoute = '{{ route("data-master.location.search") }}';
        const kandangIdRoute = `{{ route('data-master.kandang.search') }}?location_id=:id`;
        const $locationSelect = $('#location_id');
        const $categorySelect = $('#category_id');
        const $container = $('#hatcheryButtonsContainer');
        const $kandangInput = $('input[name="selected_kandangs"]');

        initSelect2($locationSelect, 'Pilih Lokasi', locationIdRoute);
        initSelect2($categorySelect, 'Pilih Kategori');
        $locationSelect.trigger('select2:select');
        $categorySelect.trigger('select2:select');

        let selectedKandangs = JSON.parse($kandangInput.val()); // array

        function renderHatcheryButtons(data) {
            $container.empty();
            $kandangInput.val('[]').trigger('input');
            selectedKandangs = [];
            $('.color-information').css('visibility', 'hidden');

            if (data) {
                $('.color-information').css('visibility', 'visible');
                const kandangs = data.map(kandang => kandang.data);

                const buttons = [];
                kandangs.forEach(kandang => {
                    const status = !!kandang.project_status ? 1 : 0;

                    const button = $('<button>', {
                        class: `kandang_select btn mr-1 mt-1 rounded-pill waves-effect ${status ? "btn-outline-secondary" : "btn-outline-danger"}`,
                        text: kandang.name,
                    })
                    .attr('data-kandang-id', kandang.kandang_id)
                    .attr('data-active', status);
                    buttons.push(button);
                });

                // Sort button by project_status
                buttons.sort(function(a, b) {
                    const aActive = $(a).data('active');
                    const bActive = $(b).data('active');
                    return bActive - aActive;
                });

                // Append after sort so active kandang would be shown first
                buttons.forEach((button) => $container.append(button));
            }
        }

        $container.on('click', 'button', function(e) {
            e.preventDefault();
            const status = $(this).data('active');
            if ($(this).hasClass('btn-outline-primary')) {
                // Unselected
                $(this)
                    .removeClass('btn-outline-primary')
                    .addClass(status ? "btn-outline-secondary" : "btn-outline-danger");

                selectedKandangs = selectedKandangs.filter((id) => id !== $(this).data('kandang-id'));
            } else {
                // Selected
                $(this)
                    .removeClass('btn-outline-secondary btn-outline-danger')
                    .addClass('btn-outline-primary');

                selectedKandangs.push($(this).data('kandang-id'));
            }

            $kandangInput.val(JSON.stringify(selectedKandangs)).trigger('input');
        });

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
