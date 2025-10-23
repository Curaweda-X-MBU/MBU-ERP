@php
    $auth = Auth::user();

    $notif_query = \App\Models\Notification::where('is_done', 0);

    if ($auth->role->name !== 'Super Admin') {
        $notif_query
        ->where('role_id', $auth->role->role_id);
    }
    if (! $auth->role->all_location) {
        $notif_query->where('location_id', $auth->department->location_id);
    }

    $notifications = (clone $notif_query)->limit(5)->get();
    $notification_total = $notif_query->count();

    $module_mappings = \App\Constants::NOTIFICATION_MODULE;
@endphp

    <nav class="header-navbar navbar navbar-expand-lg align-items-center floating-nav navbar-light navbar-shadow container-xxl">
        <div class="navbar-container d-flex content">
            <div class="bookmark-wrapper d-flex align-items-center">
                <ul class="nav navbar-nav d-xl-none">
                    <li class="nav-item"><a class="nav-link menu-toggle" href="javascript:void(0);"><i class="ficon" data-feather="menu"></i></a></li>
                </ul>
            </div>
            <ul class="nav navbar-nav align-items-center ml-auto">
                <!-- Notification -->
                <li class="nav-item dropdown-notifications navbar-dropdown dropleft mr-2 mr-xl-1">
                    <a href="javascript:void(0)" class="nav-link dropdown-toggle hide-arrow btn btn-icon btn-text-secondary rounded-pill waves-effect"
                        data-toggle="dropdown" data-auto-close="outside" aria-expanded="false"
                    >
                        <span class="position-relative">
                            <i data-feather="bell"></i>
                            @if (count($notifications))
                            <span class="badge rounded-pill bg-danger badge-dot badge-notifications border p-0 m-0" style="display: inline-block; aspect-ratio: 1; position: absolute; width: 8px; top: 3px; right: 0px;"></span>
                            @endif
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end p-0" data-popper="static">
                      <li class="dropdown-menu-header border-bottom">
                        <div class="dropdown-header d-flex align-items-center justify-content-between py-1">
                          <h6 class="mb-0 mr-4">Notification</h6>
                          <div class="d-flex align-items-center h6 mb-0">
                            <span class="badge bg-primary">{{ $notification_total }} Total</span>
                            <a href="javascript:void(0)" class="dropdown-notifications-all pr-0 py-1 btn btn-icon waves-effect waves-light"><i data-feather="x"></i></a>
                          </div>
                        </div>
                      </li>
                      <li class="dropdown-notifications-list scrollable-container ps ps--active-y">
                        <ul class="list-group list-group-flush">
                          <!-- Notification Item -->
                          @foreach($notifications as $item)
                          <li class="list-group-item list-group-item-action dropdown-notifications-item waves-effect">
                            <a class="d-flex" href="{{ url($item->url) }}">
                              <div class="flex-grow-1">
                                <h6 class="small mb-1">{{$module_mappings[$item->module]}}</h6>
                                <small class="mb-1 d-block text-body">
                                    @foreach(explode('\n', $item->message) as $m)
                                    {{ $m }}
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
                      <div class="ps__rail-x" style="left: 0px; bottom: 0px;"><div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div></div><div class="ps__rail-y" style="top: 0px; right: 0px; height: 385px;"><div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 153px;"></div></div></li>
                      <li class="border-top">
                        <div class="d-grid p-2" style="place-items: center;">
                          <a class="d-flex btn btn-primary btn-sm waves-effect waves-light justify-content-center" href="{{ route('notification') }}">
                            <small class="align-middle">View all notifications</small>
                          </a>
                        </div>
                      </li>
                    </ul>
                </li>
                <!--/ Notification -->
                <li class="nav-item dropdown dropdown-user"><a class="nav-link dropdown-toggle dropdown-user-link" id="dropdown-user" href="javascript:void(0);" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="user-nav d-sm-flex">
                            <span class="user-name font-weight-bolder">{{ $auth->name }}</span>
                            <span class="user-status">{{ $auth->role->name }}</span>
                        </div>
                        <span class="avatar">
                            @if ($auth->image)
                            <img class="round" src="{{ route('file.show', ['filename' => $auth->image]) }}" alt="avatar" height="40" width="40">
                            @endif
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-user">
                        @if ($auth->role->hasPermissionTo('user-management.user.edit'))
                        <a class="dropdown-item" href="{{ route('user-management.user.edit', $auth->user_id) }}"><i class="mr-50" data-feather='user'></i>Profile</a>
                        @endif
                        <a class="dropdown-item" href="{{ route('auth.logout') }}"><i class="mr-50" data-feather="power"></i> Logout</a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
