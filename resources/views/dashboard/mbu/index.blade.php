@extends('templates.main')
@section('title', $title)
@section('content')
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/charts/apexcharts.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset("app-assets/css/plugins/charts/chart-apex.css")}}">
<link rel="stylesheet" type="text/css" href="{{asset("app-assets/css/pages/dashboard-ecommerce.css")}}">
<style>
    .media {
        border-radius: 10px;
        box-shadow: 1px 8px 25px rgb(196 188 188 / 50%);
        padding: 10px;
        margin: 8px;
    }
</style>
<div class="row match-height">
    <!-- Earnings Card -->
    <div class="col-lg-4 col-md-4 col-12">
        <div class="card earnings-card">
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <h4 class="card-title mb-1">Total Populasi</h4>
                        <div class="font-small-2">Bulan ini</div>
                        <h5 class="mb-1">350,000</h5>
                        <p class="card-text text-muted font-small-2">
                            <button type="button" class="btn btn-primary">Lihat Detail</button>
                        </p>
                    </div>
                    <div class="col-6">
                        <div id="deplesi-chart"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--/ Earnings Card -->
    <!-- Earnings Card -->
    <div class="col-lg-4 col-md-4 col-12">
        <div class="card earnings-card">
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <h4 class="card-title mb-1">Pakan Terpakai</h4>
                        <div class="font-small-2">Bulan ini</div>
                        <h5 class="mb-1">3500 kg</h5>
                        <p class="card-text text-muted font-small-2">
                            <button type="button" class="btn btn-primary">Lihat Detail</button>
                        </p>
                    </div>
                    <div class="col-6">
                        <div id="goal-overview-radial-bar-chart"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--/ Earnings Card -->
    <!-- Earnings Card -->
    <div class="col-lg-4 col-md-4 col-12">
        <div class="card earnings-card">
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <h4 class="card-title mb-1">Earnings</h4>
                        <div class="font-small-2">This Month</div>
                        <h5 class="mb-1">$4055.56</h5>
                        <p class="card-text text-muted font-small-2">
                            <span class="font-weight-bolder">68.2%</span><span> more earnings than last month.</span>
                        </p>
                    </div>
                    <div class="col-6">
                        <div id="ovk-chart"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--/ Earnings Card -->

</div>
<div class="row match-height">
    <!-- Statistics Card -->
    <div class="col-xl-6 col-md-6 col-12">
        <div class="card card-statistics">
            <div class="card-header">
                <h4 class="card-title">Statistik Project</h4>
                <div class="d-flex align-items-center">
                    <p class="card-text font-small-2 mr-25 mb-0">Terakhir Diperbarui Hari ini</p>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-xl-6 col-sm-6 col-12 mb-xl-0">
                        <div class="media"> 
                            <div class="avatar bg-light-primary mr-2">
                                <div class="avatar-content">
                                    {{-- <i data-feather="trending-up" class="avatar-icon"></i> --}}
                                    <img src="{{asset('assets/images/chicken.png')}}" width="30" style="border-radius: unset;">
                                </div>
                            </div>
                            <div class="media-body my-auto">
                                <h4 class="font-weight-bolder mb-0">1500</h4>
                                <p class="card-text font-small-3 mb-0">Ayam</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 col-sm-6 col-12 mb-xl-0">
                        <div class="media">
                            <div class="avatar bg-light-info mr-2">
                                <div class="avatar-content">
                                    {{-- <i data-feather="user" class="avatar-icon"></i> --}}
                                    <img src="{{asset('assets/images/doc.png')}}" width="30" style="border-radius: unset;">
                                </div>
                            </div>
                            <div class="media-body my-auto">
                                <h4 class="font-weight-bolder mb-0">2500</h4>
                                <p class="card-text font-small-3 mb-0">DOC</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-6 col-sm-6 col-12 mb-sm-0">
                        <div class="media">
                            <div class="avatar bg-light-danger mr-2">
                                <div class="avatar-content">
                                    {{-- <i data-feather="box" class="avatar-icon"></i> --}}
                                    <img src="{{asset('assets/images/egg.png')}}" width="30" style="border-radius: unset;">
                                </div>
                            </div>
                            <div class="media-body my-auto">
                                <h4 class="font-weight-bolder mb-0">500</h4>
                                <p class="card-text font-small-3 mb-0">Telur</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 col-sm-6 col-12">
                        <div class="media">
                            <div class="avatar bg-light-success mr-2">
                                <div class="avatar-content">
                                    {{-- <i data-feather="dollar-sign" class="avatar-icon"></i> --}}
                                    <img src="{{asset('assets/images/chicken-feed.png')}}" width="30" style="border-radius: unset;">
                                </div>
                            </div>
                            <div class="media-body my-auto">
                                <h4 class="font-weight-bolder mb-0">203</h4>
                                <p class="card-text font-small-3 mb-0">Pakan</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--/ Statistics Card -->
     <!-- Statistics Card -->
     <div class="col-lg-3 col-sm-6 col-12">
        <div class="card">
            <div class="card-header flex-column align-items-start pb-0">
                <div class="avatar bg-light-primary p-50 m-0">
                    <div class="avatar-content">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users font-medium-5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    </div>
                </div>
                <h2 class="font-weight-bolder mt-1">92.6k</h2>
                <p class="card-text">Subscribers Gained</p>
            </div>
            <div id="gained-chart"></div>
        </div>
    </div>
    <!--/ Statistics Card -->
    <div class="col-lg-3 col-sm-6 col-12">
        <div class="card">
            <div class="card-header flex-column align-items-start pb-0">
                <div class="avatar bg-light-warning p-50 m-0">
                    <div class="avatar-content">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-package font-medium-5"><line x1="16.5" y1="9.4" x2="7.5" y2="4.21"></line><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                    </div>
                </div>
                <h2 class="font-weight-bolder mt-1">38.4K</h2>
                <p class="card-text">Orders Received</p>
            </div>
            <div id="order-chart"></div>
        </div>
    </div>

</div>

<div class="row match-height">
    <div class="col-lg-4 col-12">
        <div class="row match-height">
            <!-- Bar Chart - Orders -->
            <div class="col-lg-6 col-md-3 col-6">
                <div class="card">
                    <div class="card-body pb-50">
                        <h6>Orders</h6>
                        <h2 class="font-weight-bolder mb-1">2,76k</h2>
                        <div id="statistics-order-chart"></div>
                    </div>
                </div>
            </div>
            <!--/ Bar Chart - Orders -->

            <!-- Line Chart - Profit -->
            <div class="col-lg-6 col-md-3 col-6">
                <div class="card card-tiny-line-stats">
                    <div class="card-body pb-50">
                        <h6>Profit</h6>
                        <h2 class="font-weight-bolder mb-1">6,24k</h2>
                        <div id="statistics-profit-chart"></div>
                    </div>
                </div>
            </div>
            <!--/ Line Chart - Profit -->

            <!-- Earnings Card -->
            <div class="col-lg-12 col-md-6 col-12">
                <div class="card earnings-card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <h4 class="card-title mb-1">Earnings</h4>
                                <div class="font-small-2">This Month</div>
                                <h5 class="mb-1">$4055.56</h5>
                                <p class="card-text text-muted font-small-2">
                                    <span class="font-weight-bolder">68.2%</span><span> more earnings than last month.</span>
                                </p>
                            </div>
                            <div class="col-6">
                                <div id="earnings-chart"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ Earnings Card -->
        </div>
    </div>

    <!-- Revenue Report Card -->
    <div class="col-lg-8 col-12">
        <div class="card card-revenue-budget">
            <div class="row mx-0">
                <div class="col-md-8 col-12 revenue-report-wrapper">
                    <div class="d-sm-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-50 mb-sm-0">Revenue Report</h4>
                        <div class="d-flex align-items-center">
                            <div class="d-flex align-items-center mr-2">
                                <span class="bullet bullet-primary font-small-3 mr-50 cursor-pointer"></span>
                                <span>Earning</span>
                            </div>
                            <div class="d-flex align-items-center ml-75">
                                <span class="bullet bullet-warning font-small-3 mr-50 cursor-pointer"></span>
                                <span>Expense</span>
                            </div>
                        </div>
                    </div>
                    <div id="revenue-report-chart"></div>
                </div>
                <div class="col-md-4 col-12 budget-wrapper">
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-primary btn-sm dropdown-toggle budget-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            2020
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="javascript:void(0);">2020</a>
                            <a class="dropdown-item" href="javascript:void(0);">2019</a>
                            <a class="dropdown-item" href="javascript:void(0);">2018</a>
                        </div>
                    </div>
                    <h2 class="mb-25">$25,852</h2>
                    <div class="d-flex justify-content-center">
                        <span class="font-weight-bolder mr-25">Budget:</span>
                        <span>56,800</span>
                    </div>
                    <div id="budget-chart"></div>
                    <button type="button" class="btn btn-primary">Increase Budget</button>
                </div>
            </div>
        </div>
    </div>
    <!--/ Revenue Report Card -->
</div>

<!-- BEGIN: Page Vendor JS-->
<script src="{{asset('app-assets/vendors/js/charts/apexcharts.min.js')}}"></script>
<!-- END: Page Vendor JS-->

<!-- BEGIN: Page JS-->
<script src="{{asset('app-assets/js/scripts/pages/dashboard-ecommerce.js')}}"></script>
<script src="{{asset('app-assets/js/scripts/pages/dashboard-analytics.js')}}"></script>
<!-- END: Page JS-->
@endsection