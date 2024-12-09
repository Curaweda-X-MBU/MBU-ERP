<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    if(!(Session::get('login'))){
        return redirect('/login');
    }
    return redirect('home.mbu');
});

//Login
Route::get('/logout', 'App\Http\Controllers\AuthController@logout')->name('auth.logout');
Route::any('/forgot', 'App\Http\Controllers\AuthController@forgot')->name('auth.forgot');
Route::get('/reset/{token}', 'App\Http\Controllers\AuthController@resetShow')->name('password.reset');
Route::post('/reset', 'App\Http\Controllers\AuthController@reset')->name('auth.reset.send');
Route::match(['get','post'], '/login', 'App\Http\Controllers\AuthController@login')->name('auth.login');
Route::get('/sidebar-toggle', 'App\Http\Controllers\DashboardController@sidebarToggle')->name('sidebar-toggle');

Route::middleware('auth')->group(function () {
    Route::get('/show-file', 'App\Helpers\FileHelper@show')->name('file.show');
    Route::group(['prefix' => 'dashboard'], function () {
        Route::get('/mbu', 'App\Http\Controllers\DashboardController@indexMbu')->name('dashboard.mbu.index')->middleware('permission:dashboard.mbu.index');
        Route::get('/lti', 'App\Http\Controllers\DashboardController@indexLti')->name('dashboard.lti.index')->middleware('permission:dashboard.lti.index');
        Route::get('/manbu', 'App\Http\Controllers\DashboardController@indexManbu')->name('dashboard.manbu.index')->middleware('permission:dashboard.manbu.index');

    });
    
    Route::group(['prefix' => 'audit'], function () {
        Route::get('/', 'App\Http\Controllers\AuditController@index')->name('audit.index')->middleware('permission:audit.index');
        Route::any('/add', 'App\Http\Controllers\AuditController@add')->name('audit.add')->middleware('permission:audit.add');
        Route::any('/edit/{id}', 'App\Http\Controllers\AuditController@edit')->name('audit.edit')->middleware('permission:audit.edit');
        Route::any('/delete/{id}', 'App\Http\Controllers\AuditController@delete')->name('audit.delete')->middleware('permission:audit.delete');
        Route::get('/search', 'App\Http\Controllers\AuditController@searchAudit')->name('audit.search');
    });

    Route::group(['prefix' => 'project'], function () {
        Route::group(['prefix' => 'list'], function () {
            Route::get('/', 'App\Http\Controllers\Project\ListController@index')->name('project.list.index')->middleware('permission:project.list.index');
            Route::any('/add', 'App\Http\Controllers\Project\ListController@add')->name('project.list.add')->middleware('permission:project.list.add');
            Route::any('/edit/{id}', 'App\Http\Controllers\Project\ListController@edit')->name('project.list.edit')->middleware('permission:project.list.edit');
            Route::get('/detail/{id}', 'App\Http\Controllers\Project\ListController@detail')->name('project.list.detail')->middleware('permission:project.list.detail');
            Route::any('/copy/{id}', 'App\Http\Controllers\Project\ListController@copy')->name('project.list.copy')->middleware('permission:project.list.copy');
            Route::any('/approve/{id}', 'App\Http\Controllers\Project\ListController@approve')->name('project.list.approve')->middleware('permission:project.list.approve');
            Route::any('/delete/{id}', 'App\Http\Controllers\Project\ListController@delete')->name('project.list.delete')->middleware('permission:project.list.delete');
            Route::get('/search', 'App\Http\Controllers\Project\ListController@searchProject')->name('project.list.search');
        });
        Route::group(['prefix' => 'perparation'], function () {
            Route::get('/', 'App\Http\Controllers\Project\PreparationController@index')->name('project.perparation.index')->middleware('permission:project.perparation.index');
        });
        Route::group(['prefix' => 'chick-in'], function () {
            Route::get('/', 'App\Http\Controllers\Project\ChickinController@index')->name('project.chick-in.index')->middleware('permission:project.chick-in.index');
            Route::any('/add/{id}', 'App\Http\Controllers\Project\ChickinController@add')->name('project.chick-in.add')->middleware('permission:project.chick-in.add');
            Route::any('/edit/{id}', 'App\Http\Controllers\Project\ChickinController@edit')->name('project.chick-in.edit')->middleware('permission:project.chick-in.edit');
            Route::get('/detail/{id}', 'App\Http\Controllers\Project\ChickinController@detail')->name('project.chick-in.detail')->middleware('permission:project.chick-in.detail');
            Route::post('/approve/{id}', 'App\Http\Controllers\Project\ChickinController@approve')->name('project.chick-in.approve')->middleware('permission:project.chick-in.approve');
            Route::any('/delete/{id}', 'App\Http\Controllers\Project\ChickinController@delete')->name('project.chick-in.delete')->middleware('permission:project.chick-in.delete');
        });
        Route::group(['prefix' => 'recording'], function () {
            Route::get('/', 'App\Http\Controllers\Project\RecordingController@index')->name('project.recording.index');//->middleware('permission:project.recording.index');
            Route::any('/add', 'App\Http\Controllers\Project\RecordingController@add')->name('project.recording.add');//->middleware('permission:project.recording.index');
            Route::any('/edit/{id}', 'App\Http\Controllers\Project\RecordingController@edit')->name('project.recording.edit');//->middleware('permission:project.recording.index');
            Route::any('/delete/{id}', 'App\Http\Controllers\Project\RecordingController@delete')->name('project.recording.delete');//->middleware('permission:project.recording.index');
            Route::any('/approve/{id}', 'App\Http\Controllers\Project\RecordingController@approve')->name('project.recording.approve');//->middleware('permission:project.recording.index');
        });
    });

    Route::group(['prefix' => 'purchase'], function () {
        Route::get('/', 'App\Http\Controllers\Purchase\ListController@index')->name('purchase.index')->middleware('permission:purchase.index');
        Route::any('/add', 'App\Http\Controllers\Purchase\ListController@add')->name('purchase.add')->middleware('permission:purchase.add');
        Route::any('/copy/{id}', 'App\Http\Controllers\Purchase\ListController@copy')->name('purchase.copy')->middleware('permission:purchase.copy');
        Route::any('/edit/{id}', 'App\Http\Controllers\Purchase\ListController@edit')->name('purchase.edit')->middleware('permission:purchase.edit');
        Route::any('/approve/{id}', 'App\Http\Controllers\Purchase\ListController@approve')->name('purchase.approve')->middleware('permission:purchase.approve');
        Route::any('/detail/{id}', 'App\Http\Controllers\Purchase\ListController@detail')->name('purchase.detail')->middleware('permission:purchase.detail');
        Route::any('/delete/{id}', 'App\Http\Controllers\Purchase\ListController@delete')->name('purchase.delete')->middleware('permission:purchase.delete');
        Route::any('/payment/{id}', 'App\Http\Controllers\Purchase\ListController@payment')->name('purchase.payment');//->middleware('permission:purchase.delete');
    });
    
    Route::group(['prefix' => 'ph'], function () {
        Route::group(['prefix' => 'performance'], function () {
            Route::get('/', 'App\Http\Controllers\Ph\PerformanceController@index')->name('ph.performance.index')->middleware('permission:ph.performance.index');
            Route::any('/detail', 'App\Http\Controllers\Ph\PerformanceController@detail')->name('ph.performance.detail')->middleware('permission:ph.performance.detail');
            Route::any('/download', 'App\Http\Controllers\Ph\PerformanceController@download')->name('ph.performance.download')->middleware('permission:ph.performance.download');
            Route::any('/add', 'App\Http\Controllers\Ph\PerformanceController@add')->name('ph.performance.add')->middleware('permission:ph.performance.add');
            Route::any('/edit/{id}', 'App\Http\Controllers\Ph\PerformanceController@edit')->name('ph.performance.edit')->middleware('permission:ph.performance.edit');
            Route::any('/delete/{id}', 'App\Http\Controllers\Ph\PerformanceController@delete')->name('ph.performance.delete')->middleware('permission:ph.performance.delete');

        });

        Route::group(['prefix' => 'report-complaint'], function () {
            Route::get('/', 'App\Http\Controllers\Ph\PhComplaintController@index')->name('ph.report-complaint.index')->middleware('permission:ph.report-complaint.index');
            Route::any('/add', 'App\Http\Controllers\Ph\PhComplaintController@add')->name('ph.report-complaint.add')->middleware('permission:ph.report-complaint.add');
            Route::any('/edit/{id}', 'App\Http\Controllers\Ph\PhComplaintController@edit')->name('ph.report-complaint.edit')->middleware('permission:ph.report-complaint.edit');
            Route::any('/detail/{id}', 'App\Http\Controllers\Ph\PhComplaintController@detail')->name('ph.report-complaint.detail')->middleware('permission:ph.report-complaint.detail');
            Route::any('/download/{id}', 'App\Http\Controllers\Ph\PhComplaintController@download')->name('ph.report-complaint.download')->middleware('permission:ph.report-complaint.download');
            Route::any('/delete/{id}', 'App\Http\Controllers\Ph\PhComplaintController@delete')->name('ph.report-complaint.delete')->middleware('permission:ph.report-complaint.delete');
            Route::get('/search', 'App\Http\Controllers\Ph\PhComplaintController@searchComplaint')->name('ph.report-complaint.search');
            Route::post('/upload-image', 'App\Http\Controllers\Ph\PhComplaintController@uploadImage')->name('ph.report-complaint.upload-image')->middleware('permission:ph.report-complaint.upload-image');
        });

        Route::group(['prefix' => 'symptom'], function () {
            Route::get('/', 'App\Http\Controllers\Ph\PhSymptomController@index')->name('ph.symptom.index')->middleware('permission:ph.symptom.index');
            Route::any('/add', 'App\Http\Controllers\Ph\PhSymptomController@add')->name('ph.symptom.add')->middleware('permission:ph.symptom.add');
            Route::any('/edit/{id}', 'App\Http\Controllers\Ph\PhSymptomController@edit')->name('ph.symptom.edit')->middleware('permission:ph.symptom.edit');
            Route::any('/delete/{id}', 'App\Http\Controllers\Ph\PhSymptomController@delete')->name('ph.symptom.delete')->middleware('permission:ph.symptom.delete');
            Route::get('/search', 'App\Http\Controllers\Ph\PhSymptomController@searchSymptom')->name('ph.symptom.search');
        });
    });

    Route::group(['prefix' => 'inventory'], function () {
        Route::group(['prefix' => 'product-list'], function () {
            Route::get('/', 'App\Http\Controllers\Inventory\ProductController@index')->name('inventory.product.index')->middleware('permission:inventory.product.index');
            Route::any('/detail/{id}', 'App\Http\Controllers\Inventory\ProductController@detail')->name('inventory.product.detail')->middleware('permission:inventory.product.detail');
        });

        Route::group(['prefix' => 'adjustment'], function () {
            Route::get('/', 'App\Http\Controllers\Inventory\AdjustmentController@index')->name('inventory.adjustment.index')->middleware('permission:inventory.adjustment.index');
            Route::any('/add', 'App\Http\Controllers\Inventory\AdjustmentController@add')->name('inventory.adjustment.add')->middleware('permission:inventory.adjustment.add');
        });
    });
    
    Route::group(['prefix' => 'data-master'], function () {
        Route::group(['prefix' => 'product-category'], function () {
            Route::get('/', 'App\Http\Controllers\DataMaster\ProductCategoryController@index')->name('data-master.product-category.index')->middleware('permission:data-master.product-category.index');
            Route::any('/add', 'App\Http\Controllers\DataMaster\ProductCategoryController@add')->name('data-master.product-category.add')->middleware('permission:data-master.product-category.add');
            Route::any('/edit/{id}', 'App\Http\Controllers\DataMaster\ProductCategoryController@edit')->name('data-master.product-category.edit')->middleware('permission:data-master.product-category.edit');
            Route::any('/delete/{id}', 'App\Http\Controllers\DataMaster\ProductCategoryController@delete')->name('data-master.product-category.delete')->middleware('permission:data-master.product-category.delete');
            Route::get('/search', 'App\Http\Controllers\DataMaster\ProductCategoryController@searchProductCategory')->name('data-master.product-category.search');
        });

        Route::group(['prefix' => 'product-sub-category'], function () {
            Route::get('/', 'App\Http\Controllers\DataMaster\ProductSubCategoryController@index')->name('data-master.product-sub-category.index')->middleware('permission:data-master.product-sub-category.index');
            Route::any('/add', 'App\Http\Controllers\DataMaster\ProductSubCategoryController@add')->name('data-master.product-sub-category.add')->middleware('permission:data-master.product-sub-category.add');
            Route::any('/edit/{id}', 'App\Http\Controllers\DataMaster\ProductSubCategoryController@edit')->name('data-master.product-sub-category.edit')->middleware('permission:data-master.product-sub-category.edit');
            Route::any('/delete/{id}', 'App\Http\Controllers\DataMaster\ProductSubCategoryController@delete')->name('data-master.product-sub-category.delete')->middleware('permission:data-master.product-sub-category.delete');
            Route::get('/search', 'App\Http\Controllers\DataMaster\ProductSubCategoryController@searchProductSubCategory')->name('data-master.product-sub-category.search');
        });

        Route::group(['prefix' => 'product-component'], function () {
            Route::get('/', 'App\Http\Controllers\DataMaster\ProductComponentController@index')->name('data-master.product-component.index')->middleware('permission:data-master.product-component.index');
            Route::any('/add', 'App\Http\Controllers\DataMaster\ProductComponentController@add')->name('data-master.product-component.add')->middleware('permission:data-master.product-component.add');
            Route::any('/edit/{id}', 'App\Http\Controllers\DataMaster\ProductComponentController@edit')->name('data-master.product-component.edit')->middleware('permission:data-master.product-component.edit');
            Route::any('/delete/{id}', 'App\Http\Controllers\DataMaster\ProductComponentController@delete')->name('data-master.product-component.delete')->middleware('permission:data-master.product-component.delete');
            Route::get('/search', 'App\Http\Controllers\DataMaster\ProductComponentController@searchProductComponent')->name('data-master.product-component.search');
        });

        Route::group(['prefix' => 'product'], function () {
            Route::get('/', 'App\Http\Controllers\DataMaster\ProductController@index')->name('data-master.product.index')->middleware('permission:data-master.product.index');
            Route::any('/add', 'App\Http\Controllers\DataMaster\ProductController@add')->name('data-master.product.add')->middleware('permission:data-master.product.add');
            Route::any('/edit/{id}', 'App\Http\Controllers\DataMaster\ProductController@edit')->name('data-master.product.edit')->middleware('permission:data-master.product.edit');
            Route::any('/delete/{id}', 'App\Http\Controllers\DataMaster\ProductController@delete')->name('data-master.product.delete')->middleware('permission:data-master.product.delete');
            Route::get('/search', 'App\Http\Controllers\DataMaster\ProductController@searchProduct')->name('data-master.product.search');
        });

        Route::group(['prefix' => 'bank'], function () {
            Route::get('/', 'App\Http\Controllers\DataMaster\BankController@index')->name('data-master.bank.index')->middleware('permission:data-master.bank.index');
            Route::any('/add', 'App\Http\Controllers\DataMaster\BankController@add')->name('data-master.bank.add')->middleware('permission:data-master.bank.add');
            Route::any('/edit/{id}', 'App\Http\Controllers\DataMaster\BankController@edit')->name('data-master.bank.edit')->middleware('permission:data-master.bank.edit');
            Route::any('/delete/{id}', 'App\Http\Controllers\DataMaster\BankController@delete')->name('data-master.bank.delete')->middleware('permission:data-master.bank.delete');
            Route::get('/search', 'App\Http\Controllers\DataMaster\BankController@searchBank')->name('data-master.bank.search');
        });

        Route::group(['prefix' => 'kandang'], function () {
            Route::get('/', 'App\Http\Controllers\DataMaster\KandangController@index')->name('data-master.kandang.index')->middleware('permission:data-master.kandang.index');
            Route::any('/add', 'App\Http\Controllers\DataMaster\KandangController@add')->name('data-master.kandang.add')->middleware('permission:data-master.kandang.add');
            Route::any('/edit/{id}', 'App\Http\Controllers\DataMaster\KandangController@edit')->name('data-master.kandang.edit')->middleware('permission:data-master.kandang.edit');
            Route::any('/delete/{id}', 'App\Http\Controllers\DataMaster\KandangController@delete')->name('data-master.kandang.delete')->middleware('permission:data-master.kandang.delete');
            Route::get('/search', 'App\Http\Controllers\DataMaster\KandangController@searchKandang')->name('data-master.kandang.search');
        });

        Route::group(['prefix' => 'area'], function () {
            Route::get('/', 'App\Http\Controllers\DataMaster\AreaController@index')->name('data-master.area.index')->middleware('permission:data-master.area.index');
            Route::any('/add', 'App\Http\Controllers\DataMaster\AreaController@add')->name('data-master.area.add')->middleware('permission:data-master.area.add');
            Route::any('/edit/{id}', 'App\Http\Controllers\DataMaster\AreaController@edit')->name('data-master.area.edit')->middleware('permission:data-master.area.edit');
            Route::any('/delete/{id}', 'App\Http\Controllers\DataMaster\AreaController@delete')->name('data-master.area.delete')->middleware('permission:data-master.area.delete');
            Route::get('/search', 'App\Http\Controllers\DataMaster\AreaController@searchArea')->name('data-master.area.search');
        });
    
        Route::group(['prefix' => 'location'], function () {
            Route::get('/', 'App\Http\Controllers\DataMaster\LocationController@index')->name('data-master.location.index')->middleware('permission:data-master.location.index');
            Route::any('/add', 'App\Http\Controllers\DataMaster\LocationController@add')->name('data-master.location.add')->middleware('permission:data-master.location.add');
            Route::any('/edit/{id}', 'App\Http\Controllers\DataMaster\LocationController@edit')->name('data-master.location.edit')->middleware('permission:data-master.location.edit');
            Route::any('/delete/{id}', 'App\Http\Controllers\DataMaster\LocationController@delete')->name('data-master.location.delete')->middleware('permission:data-master.location.delete');
            Route::get('/search', 'App\Http\Controllers\DataMaster\LocationController@searchLocation')->name('data-master.location.search');
        });
    
        Route::group(['prefix' => 'company'], function () {
            Route::get('/', 'App\Http\Controllers\DataMaster\CompanyController@index')->name('data-master.company.index')->middleware('permission:data-master.company.index');
            Route::any('/add', 'App\Http\Controllers\DataMaster\CompanyController@add')->name('data-master.company.add')->middleware('permission:data-master.company.add');
            Route::any('/edit/{id}', 'App\Http\Controllers\DataMaster\CompanyController@edit')->name('data-master.company.edit')->middleware('permission:data-master.company.edit');
            Route::any('/delete/{id}', 'App\Http\Controllers\DataMaster\CompanyController@delete')->name('data-master.company.delete')->middleware('permission:data-master.company.delete');
            Route::get('/search', 'App\Http\Controllers\DataMaster\CompanyController@searchCompany')->name('data-master.company.search');
        });
    
        Route::group(['prefix' => 'department'], function () {
            Route::get('/', 'App\Http\Controllers\DataMaster\DepartmentController@index')->name('data-master.department.index')->middleware('permission:data-master.department.index');
            Route::any('/add', 'App\Http\Controllers\DataMaster\DepartmentController@add')->name('data-master.department.add')->middleware('permission:data-master.department.add');
            Route::any('/edit/{id}', 'App\Http\Controllers\DataMaster\DepartmentController@edit')->name('data-master.department.edit')->middleware('permission:data-master.department.edit');
            Route::any('/delete/{id}', 'App\Http\Controllers\DataMaster\DepartmentController@delete')->name('data-master.department.delete')->middleware('permission:data-master.department.delete');
            Route::get('/search', 'App\Http\Controllers\DataMaster\DepartmentController@searchDepartment')->name('data-master.department.search');
        });
    
        Route::group(['prefix' => 'supplier'], function () {
            Route::get('/', 'App\Http\Controllers\DataMaster\SupplierController@index')->name('data-master.supplier.index')->middleware('permission:data-master.supplier.index');
            Route::any('/add', 'App\Http\Controllers\DataMaster\SupplierController@add')->name('data-master.supplier.add')->middleware('permission:data-master.supplier.add');
            Route::any('/edit/{id}', 'App\Http\Controllers\DataMaster\SupplierController@edit')->name('data-master.supplier.edit')->middleware('permission:data-master.supplier.edit');
            Route::any('/delete/{id}', 'App\Http\Controllers\DataMaster\SupplierController@delete')->name('data-master.supplier.delete')->middleware('permission:data-master.supplier.delete');
            Route::get('/search', 'App\Http\Controllers\DataMaster\SupplierController@searchSupplier')->name('data-master.supplier.search');
            Route::get('/hatchery/search', 'App\Http\Controllers\DataMaster\SupplierController@searchHatchery')->name('data-master.supplier.hatchery.search');
        });
    
        Route::group(['prefix' => 'customer'], function () {
            Route::get('/', 'App\Http\Controllers\DataMaster\CustomerController@index')->name('data-master.customer.index')->middleware('permission:data-master.customer.index');
            Route::any('/add', 'App\Http\Controllers\DataMaster\CustomerController@add')->name('data-master.customer.add')->middleware('permission:data-master.customer.add');
            Route::any('/edit/{id}', 'App\Http\Controllers\DataMaster\CustomerController@edit')->name('data-master.customer.edit')->middleware('permission:data-master.customer.edit');
            Route::any('/delete/{id}', 'App\Http\Controllers\DataMaster\CustomerController@delete')->name('data-master.customer.delete')->middleware('permission:data-master.customer.delete');
        });
    
        Route::group(['prefix' => 'fcr'], function () {
            Route::get('/', 'App\Http\Controllers\DataMaster\FcrController@index')->name('data-master.fcr.index')->middleware('permission:data-master.fcr.index');
            Route::any('/add', 'App\Http\Controllers\DataMaster\FcrController@add')->name('data-master.fcr.add')->middleware('permission:data-master.fcr.add');
            Route::any('/edit/{id}', 'App\Http\Controllers\DataMaster\FcrController@edit')->name('data-master.fcr.edit')->middleware('permission:data-master.fcr.edit');
            Route::any('/delete/{id}', 'App\Http\Controllers\DataMaster\FcrController@delete')->name('data-master.fcr.delete')->middleware('permission:data-master.fcr.delete');
            Route::get('/search', 'App\Http\Controllers\DataMaster\FcrController@searchFcr')->name('data-master.fcr.search');
        });

        Route::group(['prefix' => 'warehouse'], function () {
            Route::get('/', 'App\Http\Controllers\DataMaster\WarehouseController@index')->name('data-master.warehouse.index')->middleware('permission:data-master.warehouse.index');
            Route::any('/add', 'App\Http\Controllers\DataMaster\WarehouseController@add')->name('data-master.warehouse.add')->middleware('permission:data-master.warehouse.add');
            Route::any('/edit/{id}', 'App\Http\Controllers\DataMaster\WarehouseController@edit')->name('data-master.warehouse.edit')->middleware('permission:data-master.warehouse.edit');
            Route::any('/delete/{id}', 'App\Http\Controllers\DataMaster\WarehouseController@delete')->name('data-master.warehouse.delete')->middleware('permission:data-master.warehouse.delete');
            Route::get('/search', 'App\Http\Controllers\DataMaster\WarehouseController@searchWarehouse')->name('data-master.warehouse.search');
        });

        Route::group(['prefix' => 'uom'], function () {
            Route::get('/', 'App\Http\Controllers\DataMaster\UomController@index')->name('data-master.uom.index')->middleware('permission:data-master.uom.index');
            Route::any('/add', 'App\Http\Controllers\DataMaster\UomController@add')->name('data-master.uom.add')->middleware('permission:data-master.uom.add');
            Route::any('/edit/{id}', 'App\Http\Controllers\DataMaster\UomController@edit')->name('data-master.uom.edit')->middleware('permission:data-master.uom.edit');
            Route::any('/delete/{id}', 'App\Http\Controllers\DataMaster\UomController@delete')->name('data-master.uom.delete')->middleware('permission:data-master.uom.delete');
            Route::get('/search', 'App\Http\Controllers\DataMaster\UomController@searchUom')->name('data-master.uom.search');
        });

        Route::group(['prefix' => 'nonstock'], function () {
            Route::get('/', 'App\Http\Controllers\DataMaster\NonstockController@index')->name('data-master.nonstock.index')->middleware('permission:data-master.nonstock.index');
            Route::any('/add', 'App\Http\Controllers\DataMaster\NonstockController@add')->name('data-master.nonstock.add')->middleware('permission:data-master.nonstock.add');
            Route::any('/edit/{id}', 'App\Http\Controllers\DataMaster\NonstockController@edit')->name('data-master.nonstock.edit')->middleware('permission:data-master.nonstock.edit');
            Route::any('/delete/{id}', 'App\Http\Controllers\DataMaster\NonstockController@delete')->name('data-master.nonstock.delete')->middleware('permission:data-master.nonstock.delete');
            Route::get('/search', 'App\Http\Controllers\DataMaster\NonstockController@searchNonstock')->name('data-master.nonstock.search');
        });
    
    });
    
    Route::group(['prefix' => 'user-management'], function () {
        Route::group(['prefix' => 'user'], function () {
            Route::get('/', 'App\Http\Controllers\UserManagement\UsersController@index')->name('user-management.user.index')->middleware('permission:user-management.user.index');
            Route::any('/add', 'App\Http\Controllers\UserManagement\UsersController@add')->name('user-management.user.add')->middleware('permission:user-management.user.add');
            Route::any('/edit/{id}', 'App\Http\Controllers\UserManagement\UsersController@edit')->name('user-management.user.edit')->middleware('permission:user-management.user.edit');
            Route::any('/delete/{id}', 'App\Http\Controllers\UserManagement\UsersController@delete')->name('user-management.user.delete')->middleware('permission:user-management.user.delete');
            Route::get('/search', 'App\Http\Controllers\UserManagement\UsersController@searchUser')->name('user-management.user.search');
        });
    
        Route::group(['prefix' => 'role'], function () {
            Route::get('/', 'App\Http\Controllers\UserManagement\RoleController@index')->name('user-management.role.index')->middleware('permission:user-management.role.index');
            Route::any('/add', 'App\Http\Controllers\UserManagement\RoleController@add')->name('user-management.role.add')->middleware('permission:user-management.role.add');
            Route::any('/edit/{id}', 'App\Http\Controllers\UserManagement\RoleController@edit')->name('user-management.role.edit')->middleware('permission:user-management.role.edit');
            Route::any('/delete/{id}', 'App\Http\Controllers\UserManagement\RoleController@delete')->name('user-management.role.delete')->middleware('permission:user-management.role.delete');
            Route::get('/search', 'App\Http\Controllers\UserManagement\RoleController@searchRole')->name('user-management.role.search');
        });

        Route::group(['prefix' => 'permission'], function () {
            Route::get('/', 'App\Http\Controllers\UserManagement\PermissionController@index')->name('user-management.permission.index')->middleware('permission:user-management.permission.index');
            Route::any('/add', 'App\Http\Controllers\UserManagement\PermissionController@add')->name('user-management.permission.add')->middleware('permission:user-management.permission.add');
            Route::any('/edit/{id}', 'App\Http\Controllers\UserManagement\PermissionController@edit')->name('user-management.permission.edit')->middleware('permission:user-management.permission.edit');
            Route::any('/delete/{id}', 'App\Http\Controllers\UserManagement\PermissionController@delete')->name('user-management.permission.delete')->middleware('permission:user-management.permission.delete');
            Route::get('/search', 'App\Http\Controllers\UserManagement\PermissionController@searchPermission')->name('user-management.permission.search');
        });
    });
});


