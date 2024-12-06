<div class="card mb-1">
    <div id="headingCollapse6" class="card-header color-header collapsed" data-toggle="collapse" role="button" data-target="#collapse6" aria-expanded="true" aria-controls="collapse6">
        <span class="lead collapse-title">Informasi Pembayaran</span>
    </div>
    <div id="collapse6" role="tabpanel" aria-labelledby="headingCollapse6" class="collapse show" aria-expanded="true">
        <div class="card-body p-2">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped w-100 no-wrap text-center" id="purchase-repeater">
                        <thead>
                            <th>Tanggal Pembayaran</th>
                            <th>Metode Pembayaran</th>
                            <th>Bank Penerima</th>
                            <th>Referensi Pembayaran</th>
                            <th>Dokumen</th>
                            <th>Status</th>
                            <th>Nominal</th>
                            @if (in_array(auth()->user()->role->name, ['Super Admin', 'Manager Finance']) )
                                <th>Aksi</th>
                            @endif
                        </thead>
                        <tbody>
                            @if (count($data->purchase_payment) > 0)
                                @foreach ($data->purchase_payment as $item)
                                <tr>
                                    <td>{{ date('d-M-Y', strtotime($item->payment_date)) }}</td>
                                    <td>{{ $payment_method[$item->payment_method] }}</td>
                                    <td>{{ $item->recipient_bank->name??'' }}</td>
                                    <td>{{ $item->ref_number }}</td>
                                    <td>
                                        @if ($item->document)
                                        <a href="{{ route('file.show', ['filename' => $item->document]) }}" target="_blank">
                                            <i data-feather='download' class="mr-50"></i>
                                            <span>Download</span>
                                        </a>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="badge badge-glow badge-{{ $item->status==0?'warning':($item->status==1?'success':'danger') }}">
                                            {{ $payment_status[$item->status] }}
                                        </div>
                                    </td>
                                    <td class="text-right">{{ number_format($item->amount, '0', ',', '.') }}</td>
                                    @if (in_array(auth()->user()->role->name, ['Super Admin', 'Manager Finance']) )
                                        <td>
                                            <div class="dropdown dropleft">
                                                <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-toggle="dropdown">
                                                    <i data-feather="more-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <div class="dropdown-ite">
                                                        <table>
                                                            <tr>
                                                                <td>
                                                                    <a class="dropdown-item text-primary" href="javascript:void(0);" data-id="{{ $item->purchase_payment_id }}" data-toggle="modal" data-target="#paymentDetail">
                                                                        <i data-feather="info" class="mr-50"></i>
                                                                        <span>Detail</span>
                                                                    </a>
                                                                </td>
                                                                <td>
                                                                    @if (array_search('Menunggu persetujuan', $payment_status) || auth()->user()->role->name == "Super Admin")
                                                                    <a class="dropdown-item text-danger" href="javascript:void(0);" data-id="{{ $item->purchase_payment_id }}-delete" data-toggle="modal" data-target="#paymentApprove">
                                                                        <i data-feather="trash" class="mr-50"></i>
                                                                        <span>Hapus</span>
                                                                    </a>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            @if ((in_array(auth()->user()->role->name, ['Manager Finance', 'Super Admin']) && $item->status == 0) || auth()->user()->role->name == "Super Admin")
                                                            <tr>
                                                                <td>
                                                                    <a class="dropdown-item text-success" href="javascript:void(0);" data-id="{{ $item->purchase_payment_id }}-approved" data-toggle="modal" data-target="#paymentApprove">
                                                                        <i data-feather="check" class="mr-50"></i>
                                                                        <span>Approve</span>
                                                                    </a>
                                                                </td>
                                                                <td>
                                                                    <a class="dropdown-item text-danger" href="javascript:void(0);" data-id="{{ $item->purchase_payment_id }}-reject" data-toggle="modal" data-target="#paymentApprove">
                                                                        <i data-feather="x" class="mr-50"></i>
                                                                        <span>Tolak</span>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                            @endif
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                                @endforeach
                            @else
                            <tr>
                                <td class="text-center" colspan="{{in_array(auth()->user()->role->name, ['Super Admin', 'Manager Finance'])?8:7}}">Belum ada data</td>
                            </tr>
                            @endif
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="6" class="text-right">
                                    Total Pembayaran
                                </td>
                                <td style="padding: 0 10px 0 10px;" class="text-right">
                                    {{ number_format($data->total_payment, '0', ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="6" class="text-right">
                                    Nominal Pembelian
                                </td>
                                <td style="padding: 0 10px 0 10px;" class="text-right">
                                    {{ number_format($data->grand_total, '0', ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="6" class="text-right">
                                    Sisa Belum Bayar
                                </td>
                                <td style="padding: 0 10px 0 10px;" class="text-right">
                                    {{ number_format($data->total_remaining_payment, '0', ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade text-left" id="paymentDetail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel1">Detail Pembayaran</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modal-body">
                <div id="modal-spinner">
                    <div class="text-center" style="padding: 15px;">
                        <div class="spinner-grow" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
                <div id="modal-content">
                    @include('purchase.detail-collapse.payment-detail')
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade text-left" id="paymentApprove" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <form method="post" action="{{ route('purchase.payment', $data->purchase_id) }}">
            {{csrf_field()}}
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel1">Konfirmasi Approve Pembayaran</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="method" id="method" value="put">
                    <input type="hidden" name="purchase_payment_id" id="purchase_payment_id" value="">
                    <input type="hidden" name="action" id="action">
                    <p id="confirmationMsg">Apakah kamu yakin ingin menyetujui pembayaran ini ?</p>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Ya</button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Tidak</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(function () {
        $('#paymentDetail').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) 
            var id = button.data('id')
            var modal = $(this)
            modal.find('td').css('vertical-align', 'top');
            const paymentMethod = @json($payment_method);
            $.ajax({
                type: "get",
                url: `{{ route('purchase.payment', $data->purchase_id) }}?purchase_payment_id=${id}`,
                dataType: "json",
                beforeSend: function() {
                    modal.find('#modal-spinner').show();
                    modal.find('#modal-content').hide();
                },
                success: function (res) {
                    modal.find('#modal-spinner').hide();
                    modal.find('#modal-content').show();
                    modal.find('#payment_date').html(formatDate(res.payment_date));
                    modal.find('#payment_method').html(paymentMethod[res.payment_method]);
                    modal.find('#own_bank_id').html(`${res.own_bank.alias} <br> ${res.own_bank.account_number} <br> ${res.own_bank.owner}`);
                    modal.find('#recipient_bank_id').html(`${res.recipient_bank.alias} <br> ${res.recipient_bank.account_number} <br> ${res.recipient_bank.owner}`);
                    modal.find('#ref_number').html(res.ref_number);
                    modal.find('#transaction_number').html(res.transaction_number);
                    modal.find('#amount').html(new Intl.NumberFormat('id-ID').format(res.amount));
                    modal.find('#bank_charge').html(new Intl.NumberFormat('id-ID').format(res.bank_charge));
                    let downloadDoc = '';
                    if(res.document) {
                        downloadDoc = `
                            <a class="btn btn-sm btn-primary" href="{{ route('file.show', ['filename' => '__FILE_NAME__']) }}" target="_blank">
                                <span>Download</span>
                            </a>
                        `.replace('__FILE_NAME__', res.document);
                    }
                    modal.find('#document').html(downloadDoc);
                }
            });
        });

        function formatDate(dateString) {
            const date = new Date(dateString);
            const day = ('0' + date.getDate()).slice(-2);
            const month = ('0' + (date.getMonth() + 1)).slice(-2); // Months are zero-indexed
            const year = date.getFullYear();
            return `${day}-${month}-${year}`;
        }

        $('#paymentApprove').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) 
            var id = button.data('id')
            var purchasePaymentId = id.split("-")[0];
            var action = id.split("-")[1];
            var modal = $(this)
            modal.find('.modal-body #purchase_payment_id').val(purchasePaymentId);
            modal.find('.modal-body #action').val(action);
            if (action === "reject") {
                modal.find('#method').val('put');
                modal.find('#myModalLabel1').html("Konfirmasi Tolak Pembayaran");
                modal.find('#confirmationMsg').html("Apakah kamu yakin ingin menolak pembayaran ini ?");
            } else if (action === "delete") {
                modal.find('#method').val('delete');
                modal.find('#myModalLabel1').html("Konfirmasi Hapus Pembayaran");
                modal.find('#confirmationMsg').html("Apakah kamu yakin ingin mengahapus pembayaran ini ?");
            } else {
                modal.find('#method').val('put');
                modal.find('#myModalLabel1').html("Konfirmasi Approve Pembayaran");
                modal.find('#confirmationMsg').html("Apakah kamu yakin ingin menyetujui pembayaran ini ?");
            }
        });
    });
</script>