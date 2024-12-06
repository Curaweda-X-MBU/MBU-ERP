@php
    $auth = Auth::user();
@endphp    
    
    <nav class="header-navbar navbar navbar-expand-lg align-items-center floating-nav navbar-light navbar-shadow container-xxl">
        <div class="navbar-container d-flex content">
            <div class="bookmark-wrapper d-flex align-items-center">
                <ul class="nav navbar-nav d-xl-none">
                    <li class="nav-item"><a class="nav-link menu-toggle" href="javascript:void(0);"><i class="ficon" data-feather="menu"></i></a></li>
                </ul>
            </div>
            <ul class="nav navbar-nav align-items-center ml-auto">
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