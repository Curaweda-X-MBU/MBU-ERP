@extends('templates.main')
@section('title', $title)
@section('content')

@php
$mappedModule = \App\Constants::NOTIFICATION_MODULE;
@endphp

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{ $title }} | {{ $currentRole }}</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-4 d-none d-sm-block">
                        <div class="list-group" id="list-tab" role="tablist">
                            @foreach($data as $module => $value)
                            <a href="#list/{{ $module }}" class="list-group-item list-group-item-action {{ $loop->first ? 'active' : '' }}" data-toggle="list" role="tab" aria-controls="{{ $module }}">{{ $mappedModule[$module] }}</a>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-8">
                        <div class="tab-content">
                            @foreach($data as $module => $value)
                            <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="list/{{ $module }}" role="tabpanel" aria-labelledby="list/{{ $module }}/list">
                                <ul class="list-group list-group-flush">
                                    <!-- Notification Item -->
                                    @foreach($value as $item)
                                    <li class="list-group-item list-group-item-action waves-effect">
                                        <a class="d-flex" href="{{ url($item->url) }}">
                                          <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $mappedModule[$module] }}</h6>
                                            <small class="mb-1 d-block text-body">
                                                @foreach(explode('\n', $item->message) as $m)
                                                @if ($loop->last)
                                                <span class="font-weight-bolder">{{ $m }}</span>
                                                @else
                                                {{ $m }}
                                                @endif
                                                <br>
                                                @endforeach
                                            </small>
                                            <small class="text-body-secondary">{{ $item->created_at->diffForHumans() }}</small>
                                          </div>
                                        </a>
                                    </li>
                                    @endforeach
                                    <!--/ Notification Item -->
                                </ul>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
