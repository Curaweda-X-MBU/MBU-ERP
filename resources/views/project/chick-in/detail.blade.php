@extends('templates.main')
@section('title', $title)
@section('content')
<style>
    .color-header {
        color: white;
        background: linear-gradient(118deg, #76A8D8, #c9e5ff);
    }
</style>

<script src="{{asset('app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js')}}"></script>

<div class="col-12">
    <div class="row">
        <div class="no-print pb-2">
            <h4 class="card-title">{{$title}}</h4>
            
                <a href="{{ route('project.chick-in.index') }}" class="btn btn-outline-secondary">
                    <i data-feather="arrow-left" class="mr-50"></i>
                    Kembali
                </a>
                <a href="{{ route('project.list.detail', $data->project_id) }}" class="btn btn-outline-success">
                    <i data-feather="arrow-up" class="mr-50"></i>
                    Lihat Detail Project
                </a>
                @if (count($data->project_chick_in) === 0)
                    @if (Auth::user()->role->hasPermissionTo('project.chick-in.add'))
                    <a href="{{ route('project.chick-in.add', $data->project_id) }}" class="btn btn-primary">
                        <i data-feather="log-in" class="mr-50"></i>
                        Tambah Data Chick In
                    </a>
                    @endif
                @else
                    {{-- @if (Auth::user()->role->hasPermissionTo('project.chick-in.edit'))
                    <a href="{{ route('project.chick-in.edit', $data->project_id) }}" class="btn btn-primary">
                        <i data-feather="edit-2" class="mr-50"></i>
                        Edit
                    </a>
                    @endif --}}
                @endif
                @if (!$data->chickin_approval_date && count($data->project_chick_in) > 0)
                    @if (Auth::user()->role->hasPermissionTo('project.chick-in.approve'))
                    <a class="btn btn-success" href="javascript:void(0);" data-id="{{ $data->project_id }}" data-toggle="modal" data-target="#approve">
                        <i data-feather="check" class="mr-50"></i>
                        Approve
                    </a>
                    @endif
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
                        @include('project.chick-in.detail-collapse.informasi-farm')
                        @include('project.chick-in.detail-collapse.informasi-chickin')
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade text-left" id="approve" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <form method="post" action="{{ route('project.chick-in.approve', 'test') }}">
            {{csrf_field()}}
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel1">Konfirmasi Approve Chick In</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="project_ids[]" id="id" value="">
                    <input type="hidden" name="act" id="act" value="">
                    <br><p>Apakah kamu yakin ingin menyetujui data chick in ini ?</p>
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
        $('#approve').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) 
            var id = button.data('id')
            var modal = $(this)
            modal.find('.modal-body #id').val(id);
        });
    });
</script>
@endsection