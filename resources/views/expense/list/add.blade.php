@extends('templates.main')
@section('title', $title)
@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{$title}}</h4>
            </div>
            <div class="card-body">
                <form id="expense-form" class="form-horizontal" method="post" action="{{ route('expense.list.add') }}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <input type="hidden" name="expense_status" value="1">
                    @include('expense.list.sections.filter-lokasi-kategori')
                    @include('expense.list.sections.biaya-utama')
                    @include('expense.list.sections.biaya-lainnya')
                    <div class="row justify-content-end mr-2 mt-3">
                        <p class="col-6 col-md-2">Total Biaya Keseluruhan:</p>
                        <p class="col-6 col-md-2 numeral-mask font-weight-bolder text-right" style="font-size: 1.2em;"><span id="total-expense">0,00</span></p>
                    </div>
                    <div class="col-12 mt-3">
                        <a href="{{ route('expense.list.index') }}" class="btn btn-outline-warning waves-effect">Batal</a>
                        <button id="draftForm" type="submit" class="btn btn-outline-success waves-effect waves-float waves-light">Simpan Draft</button>
                        <button id="submitForm" type="submit" class="btn btn-primary waves-effect waves-float waves-light">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        const $totalUtama = $('#total-biaya-utama');
        const $totalLainnya = $('#total-biaya-lainnya');

        $(`#${$totalUtama.attr('id')}, #${$totalLainnya.attr('id')}`).on('change', function() {
            const totalUtama = parseLocaleToNum($totalUtama.text());
            const totalLainnya = parseLocaleToNum($totalLainnya.text());
            const totalKeseluruhan = totalUtama + totalLainnya;

            $('#total-expense').text(parseNumToLocale(totalKeseluruhan));
        });

        $('#draftForm').on('click', function() {
            const $form = $('#expense-form');

            if (!$form[0].checkValidity()) {
                return;
            }

            $('input[name="expense_status"]').val('0');
            $('form').trigger('submit');
        });
    });
</script>

@endsection
