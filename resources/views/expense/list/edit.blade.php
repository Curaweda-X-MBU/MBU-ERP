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
                <form class="form-horizontal" method="post" action="" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    @include('expense.list.sections.filter-lokasi-kategori')
                    @include('expense.list.sections.expense-add')
                    <div class="row justify-content-end mr-2 mt-3">
                        <p class="col-6 col-md-2">Total Biaya Keseluruhan:</p>
                        <p class="col-6 col-md-2 numeral-mask font-weight-bolder text-right" style="font-size: 1.2em;"><span id="total-keseluruhan">0,00</span></p>
                    </div>
                    <div class="col-12 mt-3">
                        <a href="{{ route('expense.list.index') }}" class="btn btn-outline-warning waves-effect">Batal</a>
                        <button id="submitForm" type="submit" class="btn btn-primary waves-effect waves-float waves-light">Edit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
