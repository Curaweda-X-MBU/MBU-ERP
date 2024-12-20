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
                <form class="form-horizontal" method="post" action="{{ route('marketing.list.add') }}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    @include('marketing.list.sections.informasi-marketing-add')
                    @include('marketing.list.sections.informasi-marketing-products-add')
                    <hr class="border-bottom">
                    <div class="row">
                        @include('marketing.list.sections.informasi-marketing-sale')
                    </div>
                    <div class="col-12 mt-1">
                        <a href="{{ route('marketing.list.index') }}" class="btn btn-outline-warning waves-effect">Batal</a>
                        <button id="submitForm" type="submit" class="btn btn-primary mr-1 waves-effect waves-float waves-light">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
