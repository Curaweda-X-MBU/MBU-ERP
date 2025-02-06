<div class="card">
    <input type="hidden" class="location_penjualan_loaded" value="0">
    <input type="hidden" class="kandang_penjualan_loaded" value="0">
    <div class="card-body">
        <h4>Penjualan Ayam Besar</h4>
        <div class="table-responsive mt-2" style="overflow-x: auto;">
            @include('report.sections.modal-penjualan.index')
            <table id="penjualan_datatable" class="table" style="margin: 0 0 !important;">
                <thead>
                    <tr class="text-center">
                    <th rowspan="2" style="vertical-align: middle">Tanggal</th>
                    <th rowspan="2" style="vertical-align: middle">Umur</th>
                    <th rowspan="2" style="vertical-align: middle">No. DO</th>
                    <th rowspan="2" style="vertical-align: middle">Costumer</th>
                    <th colspan="2">Jumlah</th>
                    <th rowspan="2" style="vertical-align: middle">Harga (RP)</th>
                    <th rowspan="2" style="vertical-align: middle">CN (RP)</th>
                    <th rowspan="2" style="vertical-align: middle">Total (RP)</th>
                    <th rowspan="2" style="vertical-align: middle">Kandang</th>
                    <th rowspan="2" style="vertical-align: middle">Status Pembayaran</th>
                    </tr>
                    <tr class="text-center">
                    <th>Ekor</th>
                    <th>Kg</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- DATA from AJAX --}}
                    <tr>
                        <td class="text-center" colspan="11">Mengambil data ...</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="font-weight-bolder">
                        <td>Total Penjualan</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="sum_qty">-</td>
                        <td class="sum_weight">-</td>
                        <td class="text-right sub_total">-</td>
                        <td class="text-right sum_cn">-</td>
                        <td class="text-right grand_total">-</td>
                        <td></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script src="{{ asset('app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js') }}"></script>

<script>
$(function() {
    function fetchLocationPenjualanData() {
        fetchPenjualanData("{{ route('report.detail.location.penjualan', [ 'location' => $detail->location_id ]) . '?period=' . $detail->period }}");
    }

    function fetchKandangPenjualanData() {
        fetchPenjualanData("{{ route('report.detail.kandang.penjualan', [ 'location' => $detail->location_id , 'project' => $detail->project_id]) . '?period=' . $detail->period }}");
    }

    function fetchPenjualanData(route) {
        $.get(route)
            .then(function(result) {
                if (!result.error) {
                    $('#penjualan_datatable').DataTable({
                        destroy: true,  // Allows reloading data dynamically
                        responsive: true,
                        ordering: true,
                        dom: '<"custom-table-wrapper"t>',
                        data: result,
                        columns: [
                            {data: 'tanggal'},
                            {data: 'umur'},
                            {
                                data: 'no_do',
                                render: function(data, type, row) {
                                    if (type === 'display') {
                                        data = '<span class="toggle_do_modal text-primary cursor-pointer" data-products=\'' + JSON.stringify(row.marketing_products) +'\' data-addit-prices=\'' + JSON.stringify(row.marketing_addit_prices) + '\'>' + data + '</span>';
                                    }

                                    return data;
                                },
                            },
                            {data: 'customer'},
                            {
                                data: 'jumlah_ekor',
                                className: 'qty',
                                render: function(data, type) {
                                    if (type === 'display') {
                                        data = trimLocale(data);
                                    }

                                    return data;
                                },
                            },
                            {
                                data: 'jumlah_kg',
                                className: 'weight',
                                render: function(data, type) {
                                    if (type === 'display') {
                                        data = trimLocale(data);
                                    }

                                    return data;
                                },
                            },
                            {
                                data: 'harga',
                                className: 'price text-right',
                            },
                            {
                                data: 'cn',
                                className: 'cn text-right',
                            },
                            {
                                data: 'total',
                                className: 'total_price text-right',
                            },
                            {
                                data: 'kandang',
                                render: function(data, type) {
                                    if (type === 'display') {
                                        data = '<span>' + data + ' Kandang</span>';
                                    }

                                    return data;
                                },
                            },
                            {
                                data: 'status',
                                render: function(data, type) {
                                    if (type === 'display') {
                                        let classes = 'badge badge-pill badge-';
                                        switch(data[0]) {
                                            case 1:
                                                classes += 'warning';
                                                break;
                                            case 2:
                                                classes += 'success';
                                                break;
                                            case 3:
                                                classes += 'primary';
                                                break;
                                            default:
                                                classes += 'danger';
                                                break;
                                        }
                                        data = '<div class="' + classes + '">' + data[1] + '</div>';
                                    }

                                    return data;
                                },
                            },
                        ],
                        footerCallback: function(row, data) {
                            let api = this.api();

                            const $footer = $(api.column(0).footer()).closest('tfoot');

                            sumQty = (api
                                .column('.qty')
                                .data() ?? [])
                                .reduce((a, b) => intVal(a) + intVal(b), 0);

                            $footer.find('.sum_qty').html(trimLocale(sumQty));

                            sumWeight = (api
                                .column('.weight')
                                .data() ?? [])
                                .reduce((a, b) => intVal(a) + intVal(b), 0);

                            $footer.find('.sum_weight').html(trimLocale(sumWeight));

                            subTotal = (api
                                .column('.price')
                                .data() ?? [])
                                .reduce((a, b) => intVal(a) + intVal(b), 0);

                            $footer.find('.sub_total').html(`Rp&nbsp${parseNumToLocale(subTotal)}`);

                            sumCn = (api
                                .column('.cn')
                                .data() ?? [])
                                .reduce((a, b) => intVal(a) + intVal(b), 0);

                            $footer.find('.sum_cn').html(`Rp&nbsp${parseNumToLocale(sumCn)}`);

                            grandTotal = (api
                                .column('.total_price')
                                .data() ?? [])
                                .reduce((a, b) => intVal(a) + intVal(b), 0);

                            $footer.find('.grand_total').html(`Rp&nbsp${parseNumToLocale(grandTotal)}`);
                        }
                    });
                }
            });
    }

    function populatePenjualanModal(noDo, products, prices) {
        $('#penjualanModalLabel').text('Detail Penjualan Ayam Besar | ' + noDo);
        $('#penjualan_produk_datatable').DataTable({
            destroy: true,  // Allows reloading data dynamically
            responsive: true,
            ordering: true,
            dom: '<"custom-table-wrapper"t>',
            data: products,
            columns: [
                {
                    data: null,
                    render: function(data, type, _, meta) {
                        if (type === 'display') {
                            data = meta.row + 1;
                        }

                        return data;
                    },
                },
                {data: 'warehouse.kandang.name'},
                {data: 'product.name'},
                {
                    data: 'price',
                    render: function(data, type) {
                        if (type === 'display') {
                            data = parseNumToLocale(data);
                        }

                        return data;
                    }
                },
                {data: 'weight_avg'},
                {data: 'product.uom.name'},
                {data: 'qty'},
                {data: 'weight_total'},
                {
                    data: 'total_price',
                    className: 'total_price text-right',
                    render: function(data, type) {
                        if (type === 'display') {
                            data = parseNumToLocale(data);
                        }

                        return data;
                    }
                },
            ],
            footerCallback: function(row, data) {
                let api = this.api();

                total = (api
                    .column('.total_price')
                    .data() ?? [])
                    .reduce((a, b) => intVal(a) + intVal(b), 0);

                $(api.column(0).footer()).closest('tfoot').find('.grand_total').html(`Rp&nbsp;${parseNumToLocale(total)}`);
            },
        });
        $('#penjualan_lainnya_datatable').DataTable({
            destroy: true,  // Allows reloading data dynamically
            responsive: true,
            ordering: true,
            dom: '<"custom-table-wrapper"t>',
            data: prices,
            columns: [
                {
                    data: null,
                    render: function(data, type, _, meta) {
                        if (type === 'display')  {
                            data = meta.row + 1;
                        }

                        return data;
                    },
                },
                {data: 'item'},
                {
                    data: 'price',
                    className: 'price text-right',
                    render: function(data, type) {
                        if (type === 'display') {
                            data = parseNumToLocale(data);
                        }

                        return data;
                    }
                },
            ],
            footerCallback: function(row, data) {
                let api = this.api();

                total = (api
                    .column('.price')
                    .data() ?? [])
                    .reduce((a, b) => intVal(a) + intVal(b), 0);

                $(api.column(1).footer()).closest('tfoot').find('.grand_total').html(`Rp&nbsp;${parseNumToLocale(total)}`);
            },
        });
    }

    $('.location_penjualan_loaded').on('change', fetchLocationPenjualanData);
    $('.kandang_penjualan_loaded').on('change', fetchKandangPenjualanData);
    $('#penjualan_datatable').on('click', '.toggle_do_modal', function() {
        const marketingProducts = JSON.parse($(this).attr('data-products'));
        const marketingAdditPrices = JSON.parse($(this).attr('data-addit-prices'));
        const noDo = $(this).text();
        populatePenjualanModal(noDo, marketingProducts, marketingAdditPrices);
        $('#penjualanModal').modal('toggle');
    });
});
</script>
