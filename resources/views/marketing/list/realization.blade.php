@extends('templates.main')
@section('title', $title)
@section('content')

<div class="row">
    <div class="col-12">
        <form id="realizationForm" class="form-horizontal" method="post" action="{{ route('marketing.list.realization', $data->marketing_id) }}" enctype="multipart/form-data">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{$title}}</h4>
                </div>
                <div class="card-body">
                    {{ csrf_field() }}
                    @include('marketing.list.sections.informasi-marketing-add')
                    @include('marketing.list.sections.informasi-marketing-products-add')
                    <hr class="border-bottom">
                    <div class="row">
                        @include('marketing.list.sections.informasi-marketing-sale')
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Armada Angkut</h4>
                </div>
                <div class="card-body">
                    @include('marketing.list.sections.realisasi-marketing-armada-angkut')
                    <!-- END: Table-->
                    <hr>
                    {{-- button --}}
                    <div class="col-12 mt-1 text-left">
                        <a href="{{ route('marketing.list.index') }}" class="btn btn-outline-warning waves-effect">Batal</a>
                        <button id="draftForm" type="button" class="btn btn-outline-success waves-effect">Simpan Draft</button>
                        <button id="submitForm" type="submit" class="btn btn-primary mr-1 waves-effect waves-float waves-light">Submit</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(function() {
        $('#draftForm').on('click', function() {
            $('#realized_at').val(null);
            $('form').trigger('submit');
        });

        $('#submitForm').on('click', function(e) {
            e.preventDefault();

            const $form = $('#realizationForm');

            if (!$form[0].checkValidity()) {
                return;
            }

            const realizedAt = $('#realized_at').val();
            if (realizedAt === null || realizedAt === '') {
                Swal.fire({
                    title: 'Gagal',
                    text: 'Isi tanggal realisasi terlebih dahulu!',
                    icon: 'warning',
                });
            } else {
                $('form').trigger('submit');
            }
        });
    });
</script>

@endsection
