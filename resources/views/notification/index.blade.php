@extends('templates.main')
@section('title', $title)
@section('content')

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
                            <a href="#list-purchase" class="list-group-item list-group-item-action active" data-toggle="list" role="tab" aria-controls="purchase">Pembelian</a>
                            <a href="#list-marketing" class="list-group-item list-group-item-action" data-toggle="list" role="tab" aria-controls="markteting">Penjualan</a>
                            <a href="#list-expense" class="list-group-item list-group-item-action" data-toggle="list" role="tab" aria-controls="expense">Biaya</a>
                        </div>
                    </div>
                    <div class="col-8">
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="list-purchase" role="tabpanel" aria-labelledby="list-purchase-list">
                                <ul class="list-group list-group-flush">
                                    <!-- Notification Item -->
                                    <li class="list-group-item list-group-item-action waves-effect">
                                        <div class="d-flex">
                                          <div class="flex-grow-1">
                                            <h6 class="small mb-1">Congratulation Lettie 🎉</h6>
                                            <small class="mb-1 d-block text-body">Won the monthly best seller gold badge</small>
                                            <small class="text-body-secondary">1h ago</small>
                                          </div>
                                          <div class="flex-shrink-0 dropdown-notifications-actions">
                                            <a href="javascript:void(0)" class="dropdown-notifications-read"><span class="badge badge-dot"></span></a>
                                            <a href="javascript:void(0)" class="dropdown-notifications-archive"><i data-feather="x"></i></a>
                                          </div>
                                        </div>
                                    </li>
                                    <!--/ Notification Item -->
                                </ul>
                            </div>
                            <div class="tab-pane fade" id="list-marketing" role="tabpanel" aria-labelledby="list-marketing-list">

                            </div>
                            <div class="tab-pane fade" id="list-expense" role="tabpanel" aria-labelledby="list-expense-list">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
