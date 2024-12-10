@extends('templates.main')
@section('title', $title)
@section('content')
<style>
    .color-header {
        color: white;
        background: linear-gradient(118deg, #76A8D8, #c9e5ff);
    }
</style>
<div class="col-12">
    <div class="row">
        <div class="no-print pb-2">
            <h4 class="card-title">{{$title}}</h4>
            
                <a href="{{ route('project.list.index') }}" class="btn btn-outline-secondary">
                    <i data-feather="arrow-left" class="mr-50"></i>
                    Kembali
                </a>
                {{-- <button class="btn btn-success" id="download-project">
                    <i data-feather="download" class="mr-50"></i>
                    Download
                </button> --}}
                @if (Auth::user()->role->hasPermissionTo('project.list.edit'))
                <a href="{{ route('project.list.edit', $data->project_id) }}" class="btn btn-primary">
                    <i data-feather="edit-2" class="mr-50"></i>
                    Edit
                </a>
                @endif
                @if (Auth::user()->role->hasPermissionTo('project.list.copy'))
                <a href="{{ route('project.list.copy', $data->project_id) }}" class="btn btn-warning">
                    <i data-feather="copy" class="mr-50"></i>
                    Copy
                </a>
                @endif
                @if (!$data->approval_date && Auth::user()->role->hasPermissionTo('project.list.approve'))
                <a class="btn btn-success" href="javascript:void(0);" data-id="{{ $data->project_id }}" data-toggle="modal" data-target="#approve">
                    <i data-feather="check" class="mr-50"></i>
                    Approve
                </a>
                @endif
        </div>
    </div>
</div>
<section id="collapsible">
    <div class="row">
        <div class="col-sm-12">
            <div class=" collapse-icon">
                <div class=" p-0">
                    <div class="collapse-default">
                        @include('project.list.detail-collapse.informasi-umum')
                        @include('project.list.detail-collapse.informasi-farm')
                        {{-- @include('project.list.detail-collapse.fase') --}}
                        @include('project.list.detail-collapse.anggaran')
                        {{-- @include('project.list.detail-collapse.recording') --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade text-left" id="approve" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <form method="post" action="{{ route('project.list.approve', 'test') }}">
            {{csrf_field()}}
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel1">Konfirmasi Approve Project</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="id" value="">
                    <input type="hidden" name="act" id="act" value="">
                    <p>Apakah kamu yakin ingin menyetujui project ini ?</p>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Ya</button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Tidak</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="{{asset('app-assets/js/scripts/components/components-collapse.js')}}"></script>
<script>
    $(document).ready(function () {
        $('#download-project').click(function (e) { 
            e.preventDefault();
            window.print()
        });

        $('#approve').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) 
            var id = button.data('id')
            var modal = $(this)
            modal.find('.modal-body #id').val(id)
        });
    });
</script>
@endsection