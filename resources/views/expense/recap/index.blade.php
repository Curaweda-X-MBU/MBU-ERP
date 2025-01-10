@extends('templates.main')
@section('title', $title)
@section('content')

@php
$bop = $data['bop'] ?? collect();
$non_bop = $data['non_bop'] ?? collect();
$statusPayment = App\Constants::MARKETING_PAYMENT_STATUS;
@endphp

<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/sweetalert2.min.css')}}" />

<script>
    function resetFilters() {
        const $locationSelect = $('#location_id');
        const $container = $('#hatcheryButtonsContainer');
        const $kandangInput = $('input[name="selected_kandangs"]');
        const $startDate = $('#start_date');
        const $endDate = $('#end_date');

        $locationSelect.val('').trigger('change');
        $container.empty();
        $kandangInput.val('[]').trigger('input');
        $startDate.val('').trigger('input');
        $endDate.val('').trigger('input');
        $startDate.siblings('.input.flatpickr-basic').val('').trigger('input');
        $endDate.siblings('.input.flatpickr-basic').val('').trigger('input');
    }

    function searchRecap(isPrint = false) {
        const locationId = $('#location_id').val();
        const startDate = $('#start_date').val();
        const endDate = $('#end_date').val();

        const selectedKandangs = JSON.parse($('input[name="selected_kandangs"]').val());

        if (!startDate || startDate == '') {
            Swal.fire({
                title: 'Filter',
                text: 'Isi Tanggal Mulai',
                icon: 'warning',
                showCancelButton: false,
                confirmButtonText: 'Ok',
                customClass: {
                    confirmButton: "btn mr-1 btn-warning",
                },
                buttonsStyling: false,
            }).then(function (result) {
                if (result.value) {
                    return;
                }
            });

            return;
        }

        const url = getUrl({
            locationId,
            startDate,
            endDate,
            selectedKandangs,
            isPrint,
        });

        window.location.href = url;
    }

    function getUrl({
        locationId,
        startDate,
        endDate,
        selectedKandangs,
        isPrint
    }) {
        const params = new URLSearchParams();
        if (locationId) params.append('location_id', locationId);
        if (startDate) params.append('date_start', startDate);
        if (endDate) params.append('date_end', endDate);
        if (selectedKandangs.length > 0) params.append('farms', JSON.stringify(selectedKandangs));
        if (isPrint) params.append('print', 'true');

        return `{{ route("expense.recap.index") }}?${params.toString()}`;
    }
</script>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title">{{$title}}</div>
                <div class="float-right row">
                    @php
                        $modals = [
                            'biaya',
                        ];
                    @endphp
                    @foreach ($modals as $modal)
                    <div id="{{ $modal }}ModalWrapper">
                        <button data-toggle="modal" data-target="#{{ $modal }}Recap" id="{{ $modal }}ModalButton" type="button" class="btn btn-primary waves-effect waves-float waves-light mr-1">Rekap {{ ucfirst($modal) }}</button>
                        @include('expense.recap.sections.modal-recap')
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Lokasi -->
                    <div class="col-md-2 mt-1">
                        <label for="location_id" class="form-label">Lokasi</label>
                        <select name="location_id" id="location_id" class="form-control">
                            @if(old('location_id', $old['location_id'] ?? null))
                                <option value="{{ old('location_id', $old['location_id']) }}" selected="selected">{{ old('location_name', $old['location_name']) }}</option>
                            @endif
                        </select>
                    </div>
                    <!-- Kategori -->
                    <div class="col-md-10 mt-1">
                        <label for="start_date" class="form-label">Tanggal Biaya<i class="text-danger">*</i></label>
                        <div class="row align-items-center">
                            <div class="col-md-2">
                                <input type="date" id="start_date" class="form-control flatpickr-basic" value="{{ old('date_start', $old['date_start'] ?? '') }}" name="start_date" placeholder="Pilih Tanggal Mulai">
                            </div>
                            <span style="font-size: 1.2rem;">-</span>
                            <div class="col-md-2">
                                <input type="date" id="end_date" class="form-control flatpickr-basic" value="{{ old('date_end', $old['date_end'] ?? '') }}" name="start_date" placeholder="Pilih Tanggal Selesai">
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mt-1">
                        <button type="button" class="btn btn-outline-primary waves-effect mr-1" onclick="searchRecap()">Cari</button>
                        <button type="button" class="btn btn-outline-warning waves-effect" onclick="resetFilters()">Reset</button>
                    </div>
                    <input type="hidden" name="selected_kandangs" value="{{ old('farms', $old['farms'] ?? '[]') }}">
                </div>

                <!-- container kandang -->
                <div id="hatcheryButtonsContainer" class="mt-1">
                    @if (old('location_id', $old['location_id'] ?? null))
                        @foreach ($old['kandangs'] as $kandang)
                        @php
                        $is_active = $kandang->project_status;
                        $is_selected = in_array($kandang->kandang_id, json_decode(old('farms', $old['farms'] ?? '[]')));
                        @endphp

                        <button
                            type="button"
                            class="kandang_select btn mr-1 mt-1 rounded-pill waves-effect {{ $is_selected ? 'btn-outline-primary' : ($is_active ? 'btn-outline-secondary' : 'btn-outline-danger') }}"
                            data-active={{ $is_active }}
                            data-kandang-id={{ $kandang->kandang_id }}
                        >
                            {{ $kandang->name }}
                        </button>
                        @endforeach
                    @endif
                </div>

                {{-- table--}}
                @include('expense.recap.sections.bop-table')
                @include('expense.recap.sections.non-bop-table')

                <div class="row justify-content-end mr-2 mt-3">
                    <p class="col-6 col-md-2">Total Keseluruhan Biaya</p>
                    <p class="col-6 col-md-2 numeral-mask font-weight-bolder text-right" style="font-size: 1.2em;"><span id="total-recap">{{
                        \App\Helpers\Parser::toLocale(
                            old('farms', $old['farms'] ?? null)
                                ? ($bop->sum('price') ?? 0) + ($non_bop->sum('price') ?? 0)
                                : ($bop->sum('total_price') ?? 0) + ($non_bop->sum('total_price') ?? 0)
                        )
                    }}</span></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{asset('app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js')}}"></script>
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
<script src="{{asset('app-assets/vendors/js/extensions/sweetalert2.all.min.js')}}"></script>

<script>
    // flatpickr
    initFlatpickrDate($('.flatpickr-basic'));

    $(function() {
        const locationIdRoute = '{{ route("data-master.location.search") }}';
        const kandangIdRoute = `{{ route('data-master.kandang.search') }}?location_id=:id`;
        const $locationSelect = $('#location_id');
        const $container = $('#hatcheryButtonsContainer');
        const $kandangInput = $('input[name="selected_kandangs"]');
        initSelect2($locationSelect, 'Pilih Lokasi', locationIdRoute, '');

        let selectedKandangs = JSON.parse($kandangInput.val());

        function renderHatcheryButtons(data) {
            $container.empty();
            $kandangInput.val('[]').trigger('input');
            selectedKandangs = [];

            if (data) {
                const kandangs = data.map(kandang => kandang.data);

                const buttons = [];
                kandangs.forEach(kandang => {
                    const status = !!kandang.project_status;

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
                    const aActive = $(a).data('active') ? 1 : 0;
                    const bActive = $(b).data('active') ? 1 : 0;
                    return bActive - aActive;
                });

                // Append after sort so active kandang would be shown first
                buttons.forEach((button) => $container.append(button));
            }
        }

        $container.on('click', 'button', function(e) {
            e.preventDefault();
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

        $(document).on('select2:select', '#location_id', function() {
            const location_id = $locationSelect.val();
            if (location_id) {
                // Fetch kandangs
                $.getJSON(kandangIdRoute.replace(':id', location_id), function(data) {
                    if (data.length) {
                        renderHatcheryButtons(data);
                    } else {
                        renderHatcheryButtons(null);
                    }
                });
            } else {
                // Delete kandangs
                renderHatcheryButtons(null);
            }
        });
    });
    // END :: calculate total

    // START :: export
    let $tableBOP = $('#datatableBOP').DataTable({
        dom: '<"custom-table-wrapper"t>',
        // buttons: [
        //     {
        //         extend: 'excelHtml5',
        //         className: 'd-none',
        //         title: 'Rekap Biaya',
        //         footer: true
        //     },
        //     {
        //         extend: 'pdfHtml5',
        //         className: 'd-none',
        //         title: 'Rekap Biaya Operasional',
        //         customize: function(doc) {
        //             doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
        //         },
        //         footer: true
        //     }
        // ]
    });

    let $tableNonBOP = $('#datatableNonBOP').DataTable({
        dom: '<"custom-table-wrapper"t>',
    });

    // Event handler untuk tombol export
    $('.modal-footer .btn-primary').on('click', function() {
        const $modal = $(this).closest('.modal');
        const selectedFormat = $modal.find('input[name*="fileType"]:checked').val();

        if (selectedFormat == 'pdf') {
            searchRecap(true);
        }

        // TODO: trigger datatable export button

        $modal.modal('hide');
    });

    // END :: export
</script>

@endsection
