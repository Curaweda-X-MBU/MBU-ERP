        @php
            use App\Models\UserManagement\Permission;
            $roleAccess = Auth::user()->role;
            $permissionData = Permission::pluck('name')->toArray();
            $collection = collect($permissionData);

            function hasAccess($data, $modul, $role) {
                return $data->filter(function ($item) use ($modul, $role) {
                    return
                        str_contains($item, $modul)
                        && str_contains($item, 'index')
                        && collect($item)->contains(fn($val) => $role->hasPermissionTo($val));
                })->all();
            }
        @endphp

        <div class="navbar-header">
            <ul class="nav navbar-nav flex-row">
                <li class="nav-item mr-auto"><a class="navbar-brand" href="javascript:void(0)" style="margin-top: 0.35rem;"><span class="brand-logo">
                        <img src="{{ asset('app-assets/images/logo/mbu_logo_only.png') }}" alt="" style="max-width: 40px; margin-top: 4px;"> </span>
                        <h2 class="brand-text"><img src="{{ asset('app-assets/images/logo/mbu_text.png') }}" alt="" style="max-width: 80px; margin-left: -5px; margin-top: 4px;"> </span></h2>
                    </a></li>
                <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse" id="toggleSidebar"><i class="d-block d-xl-none text-primary toggle-icon font-medium-4" data-feather="x"></i><i class="d-none d-xl-block collapse-toggle-icon font-medium-4  text-primary" data-feather="disc" data-ticon="disc"></i></a></li>
            </ul>
        </div>
        <div class="shadow-bottom"></div>

        <input type="hidden" id="url" value="{{ Request::path() }}">
        <div class="main-menu-content mt-1">
            <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
                @if (hasAccess($collection, 'dashboard', $roleAccess))
                <li class=" nav-item has-sub {{ Request::segment(1)=='dashboard'?'sidebar-group-active':'' }}"><a class="d-flex align-items-center" href="#"><i data-feather="home"></i><span class="menu-title text-truncate" data-i18n="Dashboard">Dashboard</span></a>
                    <ul class="menu-content">
                        @if ($roleAccess->hasPermissionTo('dashboard.mbu.index'))
                        <li id="mbu"><a class="d-flex align-items-center" href="{{ route('dashboard.mbu.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="MBU">MBU</span></a>
                        </li>
                        @endif
                        @if ($roleAccess->hasPermissionTo('dashboard.lti.index'))
                        <li id="lti"><a class="d-flex align-items-center" href="{{ route('dashboard.lti.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="LTI">LTI</span></a>
                        </li>
                        @endif
                        @if ($roleAccess->hasPermissionTo('dashboard.manbu.index'))
                        <li id="manbu"><a class="d-flex align-items-center" href="{{ route('dashboard.manbu.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="MANBU">MANBU</span></a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif
                <li class=" navigation-header"><span data-i18n="Features">Features</span><i data-feather="more-horizontal"></i>
                </li>
                @if ($roleAccess->hasPermissionTo('audit.index'))
                <li id="audit" class=" nav-item"><a class="d-flex align-items-center" href="{{ route('audit.index') }}"><i data-feather="file-text"></i><span class="menu-title text-truncate" data-i18n="Audit">Audit</span></a>
                </li>
                @endif
                @if (hasAccess($collection, 'project', $roleAccess))
                <li class=" nav-item has-sub {{ Request::segment(1)=='project'?'sidebar-group-active':'' }}"><a class="d-flex align-items-center" href="#"><i data-feather='target'></i></i><span class="menu-title text-truncate" data-i18n="Project">Project</span></a>
                    <ul class="menu-content">
                        @if ($roleAccess->hasPermissionTo('project.list.index'))
                        <li id="list"><a class="d-flex align-items-center" href="{{ route('project.list.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="List">List Project</span></a>
                        </li>
                        @endif
                        @if ($roleAccess->hasPermissionTo('project.chick-in.index'))
                        <li id="chick-in"><a class="d-flex align-items-center" href="{{ route('project.chick-in.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Chick In">Chick In</span></a>
                        </li>
                        @endif
                        @if ($roleAccess->hasPermissionTo('project.recording.index'))
                        <li id="recording"><a class="d-flex align-items-center" href="{{ route('project.recording.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Recording">Recording</span></a>
                        </li>
                        @endif
                        @if ($roleAccess->hasPermissionTo('project.perparation.index'))
                        <li id="perparation"><a class="d-flex align-items-center" href="{{ route('project.perparation.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Persiapan">Persiapan</span></a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif
                @if (hasAccess($collection, 'ph', $roleAccess))
                <li class=" nav-item has-sub {{ Request::segment(1)=='ph'?'sidebar-group-active':'' }}"><a class="d-flex align-items-center" href="#"><i data-feather='activity'></i></i><span class="menu-title text-truncate" data-i18n="PH">Poultry Health</span></a>
                    <ul class="menu-content">
                        @if ($roleAccess->hasPermissionTo('ph.performance.index'))
                        <li id="performance"><a class="d-flex align-items-center" href="{{ route('ph.performance.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Performance">Performance</span></a>
                        </li>
                        @endif
                        @if ($roleAccess->hasPermissionTo('ph.report-complaint.index'))
                        <li id="report-complaint"><a class="d-flex align-items-center" href="{{ route('ph.report-complaint.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Report Komplain">Report Komplain</span></a>
                        </li>
                        @endif
                        @if ($roleAccess->hasPermissionTo('ph.symptom.index'))
                        <li id="symptom"><a class="d-flex align-items-center" href="{{ route('ph.symptom.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Gejala Klinis">Gejala Klinis</span></a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif
                @if (hasAccess($collection, 'marketing', $roleAccess))
                <li class=" nav-item has-sub {{ Request::segment(1)=='marketing'?'sidebar-group-active':'' }}"><a class="d-flex align-items-center" href="#"><i data-feather="dollar-sign"></i><span class="menu-title text-truncate" data-i18n="Marketing">Penjualan</span></a>
                    <ul class="menu-content">
                        @if ($roleAccess->hasPermissionTo('marketing.list.index'))
                        <li id="list-marketing"><a class="d-flex align-items-center" href="{{ route('marketing.list.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Marketing List">List Penjualan</span></a>
                        </li>
                        @endif
                        @if ($roleAccess->hasPermissionTo('marketing.return.index'))
                        <li id="return-marketing"><a class="d-flex align-items-center" href="{{ route('marketing.return.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Marketing Retur">Retur Penjualan</span></a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif
                @if (hasAccess($collection, 'purchase.index', $roleAccess))
                <li id="purchase" class=" nav-item"><a class="d-flex align-items-center" href="{{ route('purchase.index') }}"><i data-feather="shopping-cart"></i><span class="menu-title text-truncate" data-i18n="Pembelian">Pembelian</span></a>
                </li>
                @endif
                @if (hasAccess($collection, 'inventory', $roleAccess))
                <li class=" nav-item has-sub {{ Request::segment(1)=='inventory'?'sidebar-group-active':'' }}"><a class="d-flex align-items-center" href="#"><i data-feather='package'></i><span class="menu-title text-truncate" data-i18n="Inventory">Persediaan</span></a>
                    <ul class="menu-content">
                        @if ($roleAccess->hasPermissionTo('inventory.product.index'))
                        <li id="product-list"><a class="d-flex align-items-center" href="{{ route('inventory.product.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Produk">Produk</span></a>
                        </li>
                        @endif
                        @if ($roleAccess->hasPermissionTo('inventory.adjustment.index'))
                        <li id="adjustment"><a class="d-flex align-items-center" href="{{ route('inventory.adjustment.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Penyesuaian">Penyesuaian Stok</span></a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif
                @if (hasAccess($collection, 'data-master', $roleAccess))
                <li class=" nav-item has-sub {{ Request::segment(1)=='data-master'?'sidebar-group-active':'' }}"><a class="d-flex align-items-center" href="#"><i data-feather='database'></i><span class="menu-title text-truncate" data-i18n="Data Master">Master Data</span></a>
                    <ul class="menu-content">
                        @if ($roleAccess->hasPermissionTo('data-master.product.index'))
                        <li id="product"><a class="d-flex align-items-center" href="{{ route('data-master.product.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Product">Produk</span></a>
                        </li>
                        @endif
                        @if ($roleAccess->hasPermissionTo('data-master.product-category.index'))
                        <li id="product-category"><a class="d-flex align-items-center" href="{{ route('data-master.product-category.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Product">Kategori Produk</span></a>
                        </li>
                        @endif
                        @if ($roleAccess->hasPermissionTo('data-master.product-sub-category.index'))
                        <li id="product-sub-category"><a class="d-flex align-items-center" href="{{ route('data-master.product-sub-category.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Product Category">Sub Kategori Produk</span></a>
                        </li>
                        @endif
                        @if ($roleAccess->hasPermissionTo('data-master.product-component.index'))
                        <li id="product-component"><a class="d-flex align-items-center" href="{{ route('data-master.product-component.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Product Componet">Bahan Baku</span></a>
                        </li>
                        @endif
                        @if ($roleAccess->hasPermissionTo('data-master.bank.index'))
                        <li id="bank"><a class="d-flex align-items-center" href="{{ route('data-master.bank.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Bank">Bank</span></a>
                        </li>
                        @endif
                        @if ($roleAccess->hasPermissionTo('data-master.kandang.index'))
                        <li id="kandang"><a class="d-flex align-items-center" href="{{ route('data-master.kandang.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Kandang">Kandang</span></a>
                        </li>
                        @endif
                        @if ($roleAccess->hasPermissionTo('data-master.warehouse.index'))
                        <li id="warehouse"><a class="d-flex align-items-center" href="{{ route('data-master.warehouse.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Gudang">Gudang</span></a>
                        </li>
                        @endif
                        @if ($roleAccess->hasPermissionTo('data-master.area.index'))
                        <li id="area"><a class="d-flex align-items-center" href="{{ route('data-master.area.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Area">Area</span></a>
                        </li>
                        @endif
                        @if ($roleAccess->hasPermissionTo('data-master.location.index'))
                        <li id="location"><a class="d-flex align-items-center" href="{{ route('data-master.location.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Location">Lokasi</span></a>
                        </li>
                        @endif
                        @if ($roleAccess->hasPermissionTo('data-master.company.index'))
                        <li id="company"><a class="d-flex align-items-center" href="{{ route('data-master.company.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Location">Unit Bisnis</span></a>
                        </li>
                        @endif
                        @if ($roleAccess->hasPermissionTo('data-master.department.index'))
                        <li id="department"><a class="d-flex align-items-center" href="{{ route('data-master.department.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Department">Departemen</span></a>
                        </li>
                        @endif
                        @if ($roleAccess->hasPermissionTo('data-master.customer.index'))
                        <li id="customer"><a class="d-flex align-items-center" href="{{ route('data-master.customer.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Customer">Pelanggan</span></a>
                        </li>
                        @endif
                        @if ($roleAccess->hasPermissionTo('data-master.supplier.index'))
                        <li id="supplier"><a class="d-flex align-items-center" href="{{ route('data-master.supplier.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Supplier">Pemasok</span></a>
                        </li>
                        @endif
                        @if ($roleAccess->hasPermissionTo('data-master.fcr.index'))
                        <li id="fcr"><a class="d-flex align-items-center" href="{{ route('data-master.fcr.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="FCR">FCR</span></a>
                        </li>
                        @endif
                        @if ($roleAccess->hasPermissionTo('data-master.uom.index'))
                        <li id="uom"><a class="d-flex align-items-center" href="{{ route('data-master.uom.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="UOM">UOM</span></a>
                        </li>
                        @endif
                        @if ($roleAccess->hasPermissionTo('data-master.nonstock.index'))
                        <li id="nonstock"><a class="d-flex align-items-center" href="{{route('data-master.nonstock.index')}}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Non Stock">Non Stock</span></a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif
                @if (hasAccess($collection, 'user-management', $roleAccess))
                <li class=" nav-item has-sub {{ Request::segment(1)=='user-management'?'sidebar-group-active':'' }}"><a class="d-flex align-items-center" href="#"><i data-feather="users"></i><span class="menu-title text-truncate" data-i18n="User Management">Management User</span></a>
                    <ul class="menu-content">
                        @if ($roleAccess->hasPermissionTo('user-management.user.index'))
                        <li id="user"><a class="d-flex align-items-center" href="{{ route('user-management.user.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="User">User</span></a>
                        </li>
                        @endif
                        @if ($roleAccess->hasPermissionTo('user-management.role.index'))
                        <li id="role"><a class="d-flex align-items-center" href="{{ route('user-management.role.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Role">Role</span></a>
                        </li>
                        @endif
                        @if ($roleAccess->hasPermissionTo('user-management.permission.index'))
                        <li id="permission"><a class="d-flex align-items-center" href="{{ route('user-management.permission.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Permission">Permission</span></a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif
            </ul>
        </div>

        <script>
            $(function () {
                var urlPath = $('#url').val();
                var arrPath = urlPath.split('/');
                $('#'+arrPath[0]).addClass('active');
                $('#'+arrPath[1]).addClass('active');

                $('#toggleSidebar').click(function (e) {
                    e.preventDefault();
                    $.ajax({
                        type: "get",
                        url: "{{ route('sidebar-toggle') }}",
                        success: function (response) {
                            console.log('sidebar toggle has been changed');
                        }
                    });
                });
            });
        </script>
