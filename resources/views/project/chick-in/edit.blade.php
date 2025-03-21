@extends('templates.main')
@section('title', $title)
@section('content')
<style>
    .color-header {
        color: white;
        background: linear-gradient(118deg, #76A8D8, #c9e5ff);
    }
</style>
<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/forms/repeater/jquery.repeater.min.js')}}"></script>

<form method="post" id="chickin-repeater" action="{{ route('project.chick-in.edit', $data->project_id) }}" enctype="multipart/form-data">
    {{ csrf_field() }}
    <h4 class="card-title">{{$title}}</h4>
    <section id="collapsible">
        <div class="row">
            <div class="col-sm-12">
                <div class=" collapse-icon">
                    <div class=" p-0">
                        <div class="collapse-default">
                            @include('project.chick-in.detail-collapse.informasi-farm')
                            @include('project.chick-in.detail-collapse.informasi-chickin-add')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="row">
        <div class="col-12 mt-2">
            <div class="text-center">
                <button id="submitForm" type="submit" class="btn btn-primary mr-1 waves-effect waves-float waves-light">Simpan</button>
                <a href="{{ route('project.chick-in.detail', $data->project_id) }}" class="btn btn-outline-warning waves-effect">Batal</a>
            </div>
        </div>
    </div>
</form>

<script src="{{asset('app-assets/js/scripts/components/components-collapse.js')}}"></script>
@endsection