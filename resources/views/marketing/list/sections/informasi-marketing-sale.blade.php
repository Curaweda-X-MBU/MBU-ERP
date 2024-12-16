<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/animate/animate.css')}}" />
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/sweetalert2.min.css')}}" />

<div class="row">
    <!-- BEGIN: Catatan dan Nama Sales -->
    <div class="col-md-6">
        <div class="row">
            <div class="col-md-8 mt-1">
                <label for="catatan" class="form-label">Catatan :</label>
                <textarea id="catatan" name="notes" class="form-control" rows="3"></textarea>
            </div>

            <div class="col-md-4 mt-1">
                <label for="sales_id" class="form-label">Nama Sales<i class="text-danger">*</i></label>
                <select name="sales_id" id="sales_id" class="form-control" required>
                    @if(old('sales_id') && old('sales_id'))
                        <option value="{{ old('sales_id') }}" selected="selected">{{ old('sales_name') }}</option>
                    @endif
                </select>
                @if ($errors->has('sales_id'))
                    <span class="text-danger small">{{ $errors->first('sales_id') }}</span>
                @endif
            </div>
        </div>
    </div>
    {{-- END: Catatan dan Nama Sales --}}

    <!-- BEGIN: Total -->
    <div class="row col-md-6 my-1" id="marketing-addit-prices-repeater-1" style="row-gap: 1em;">
        <div class="row col-12 text-right align-items-center" style="row-gap: 0.5em;">
            <div class="col-5"> <span>Total Sebelum Pajak:</span> </div>
            <div class="col-5"> Rp. <span id="total_sebelum_pajak">0,00</span> </div>
            <div class="col-5"> <span>Pajak:</span> </div>
            <div class="col-5 input-group">
                <input name="tax" type="number" min="0" max="100" class="form-control" value="0">
                <div class="input-group-append"><span class="input-group-text">%</span></div>
            </div>
            <div class="col-5"> <span>Diskon:</span> </div>
            <div class="col-5 input-group">
                <div class="input-group-prepend"><span class="input-group-text">Rp.</span></div>
                <input name="discount" type="text" class="form-control numeral-mask" value="0">
            </div>
            <div class="offset-5 col-5"> <hr class="border-bottom"> </div>
            <div class="col-5"> <span>Total Setelah Pajak dan Diskon:</span> </div>
            <div class="col-5"> Rp. <span id="total_setelah_pajak" class="font-weight-bolder">0,00</span> </div>
        </div>
        {{-- BEGIN: Tambah biaya lainnya --}}
        <div class="row col-12 text-right align-items-center" data-repeater-list="marketing_addit_prices" style="row-gap: 0.5em;">
            <div class="col-12 text-left">
                <button type="button" class="btn btn-primary btn-sm" data-repeater-create>
                    <span>Tambah Biaya Lainnya</span>
                    <span><i data-feather="plus"></i></span>
                </button>
            </div>
            <div class="row align-items-center" data-repeater-item>
                <div class="col-5"> <input name="item" type="text" class="form-control" placeholder="Item"> </div>
                <div class="col-5 input-group">
                    <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                    <input type="text" name="price" id ="addit_price" class="form-control numeral-mask" placeholder="Harga">
                </div>
                <div class="col-2 text-left">
                    <button class="btn btn-sm btn-icon btn-danger" data-repeater-delete type="button" title="Hapus Produk">
                        <i data-feather="x"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="row col-12 text-right align-items-center" data-repeater-list="marketing_addit_prices" style="row-gap: 0.5em;">
            <div class="offset-5 col-5"> <hr class="border-bottom" style="border-color: black;"> </div>
            <div class="col-5"> <span>Total Piutang Penjualan:</span> </div>
            <div class="col-5"> Rp. <span id="total_piutang" class="font-weight-bolder" style="font-size: 1.2em;">0,00</span> </div>
        </div>
        {{-- START: Grand Total --}}
        {{-- END: Grand Total --}}
        <div class="row col-12 text-right align-items-center" data-repeater-list="marketing_addit_prices" style="row-gap: 0.5em;">
        {{-- END: Tambah biaya lainnya --}}

    </div>
    {{-- END: Total --}}
</div>

<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/forms/repeater/jquery.repeater.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/forms/cleave/cleave.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/extensions/sweetalert2.all.min.js')}}"></script>
<script>
    // ? START :: CALCULATION ::  GRAND TOTAL
    function calculateGrandTotal() {
        $(document).on('change input', '#addit_price, #total_setelah_pajak', function () {
            const totalSetelahPajak = parseLocale($('#total_setelah_pajak').text());
            const $totalPiutang = $('#total_piutang');
            setTimeout(function(){
                const priceAllRow = $('#addit_price').get().reduce(function(acc, elem) {
                    const value = parseLocale($(elem).val());
                    return acc + value;
                }, 0);
                $totalPiutang.text((totalSetelahPajak + priceAllRow).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            }, 0);
        });
    }
    // ? END :: CALCULATION ::  GRAND TOTAL

    // ? START :: CALCULATION ::  BEFORE TAX
    function calculateBeforeTax() {
        $(document).on('change input', 'input[name="discount"], input[name="tax"], #total_sebelum_pajak', function () {
            const $totalSetelahPajak = $('#total_setelah_pajak');
            const totalSebelumPajak = parseLocale($('#total_sebelum_pajak').text());
            const tax = $('input[name="tax"]').val();
            const discount = parseLocale($('input[name="discount"]').val());
            let total;
            if (tax > 0) {
                total = totalSebelumPajak + (totalSebelumPajak * (tax / 100)) - discount;
            } else {
                total = totalSebelumPajak - discount;
            }
            $totalSetelahPajak.text(total.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })).trigger('change');
        });
    }
    // ? END :: CALCULATION ::  BEFORE TAX

    // ? START :: REPEATER OPTS :: MARKETING ADDIT PRICES
    const optMarketingAdditPrices = {
        initEmpty: true,
        show: function() {
            $(this).slideDown();
            // FEATHER ICONS
            if (feather) {
                feather.replace({ width: 14, height: 14 });
            }
            initNumeralMask('.numeral-mask');
        },
        hide: function(deleteElement) {
            confirmDelete($(this), deleteElement);
        },
    };
    // ? END :: REPEATER OPTS :: MARKETING ADDIT PRICES

    // ? START :: SELECT2 :: INITIALIZE
    const salesIdRoute = '{{ route("user-management.user.search") }}';
    initSelect2($('#sales_id'), 'Pilih Sales', salesIdRoute);
    // ? END :: SELECT2 :: INITIALIZE

    // ? START :: REPEATER :: INITIALIZE
    $('#marketing-addit-prices-repeater-1').repeater(optMarketingAdditPrices);
    $('#marketing-addit-prices-repeater-1').find('button[data-repeater-create]').trigger('click');
    calculateBeforeTax();
    calculateGrandTotal();
    // ? END :: REPEATER :: INITIALIZE
</script>
