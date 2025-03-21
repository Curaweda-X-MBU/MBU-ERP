<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

/*
 * |--------------------------------------------------------------------------
 * | Web Routes
 * |--------------------------------------------------------------------------
 * |
 * | Here is where you can register web routes for your application. These
 * | routes are loaded by the RouteServiceProvider and all of them will
 * | be assigned to the "web" middleware group. Make something great!
 * |
 */

Route::get('/', function() {
    if (! (Session::get('login'))) {
        return redirect('/login');
    }

    return redirect('home.mbu');
});

// Login
Route::get('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('auth.logout');
Route::any('/forgot', [App\Http\Controllers\AuthController::class, 'forgot'])->name('auth.forgot');
Route::get('/reset/{token}', [App\Http\Controllers\AuthController::class, 'resetShow'])->name('password.reset');
Route::post('/reset', [App\Http\Controllers\AuthController::class, 'reset'])->name('auth.reset.send');
Route::match(['get', 'post'], '/login', [App\Http\Controllers\AuthController::class, 'login'])->name('auth.login');
Route::get('/sidebar-toggle', [App\Http\Controllers\DashboardController::class, 'sidebarToggle'])->name('sidebar-toggle');

Route::middleware('auth')->group(function() {
    Route::get('/show-file', [App\Helpers\FileHelper::class, 'show'])->name('file.show');
    Route::group(['prefix' => 'dashboard'], function() {
        Route::get('/mbu', [App\Http\Controllers\DashboardController::class, 'indexMbu'])->name('dashboard.mbu.index')->middleware('permission:dashboard.mbu.index');
        Route::get('/lti', [App\Http\Controllers\DashboardController::class, 'indexLti'])->name('dashboard.lti.index')->middleware('permission:dashboard.lti.index');
        Route::get('/manbu', [App\Http\Controllers\DashboardController::class, 'indexManbu'])->name('dashboard.manbu.index')->middleware('permission:dashboard.manbu.index');
    });

    Route::group(['prefix' => 'audit'], function() {
        Route::get('/', [App\Http\Controllers\AuditController::class, 'index'])->name('audit.index')->middleware('permission:audit.index');
        Route::any('/add', [App\Http\Controllers\AuditController::class, 'add'])->name('audit.add')->middleware('permission:audit.add');
        Route::any('/edit/{id}', [App\Http\Controllers\AuditController::class, 'edit'])->name('audit.edit')->middleware('permission:audit.edit');
        Route::any('/delete/{id}', [App\Http\Controllers\AuditController::class, 'delete'])->name('audit.delete')->middleware('permission:audit.delete');
        Route::get('/search', [App\Http\Controllers\AuditController::class, 'searchAudit'])->name('audit.search');
    });

    Route::group(['prefix' => 'project'], function() {
        Route::group(['prefix' => 'list'], function() {
            Route::get('/', [App\Http\Controllers\Project\ListController::class, 'index'])->name('project.list.index')->middleware('permission:project.list.index');
            Route::any('/add', [App\Http\Controllers\Project\ListController::class, 'add'])->name('project.list.add')->middleware('permission:project.list.add');
            Route::any('/edit/{id}', [App\Http\Controllers\Project\ListController::class, 'edit'])->name('project.list.edit')->middleware('permission:project.list.edit');
            Route::get('/detail/{id}', [App\Http\Controllers\Project\ListController::class, 'detail'])->name('project.list.detail')->middleware('permission:project.list.detail');
            Route::any('/copy/{id}', [App\Http\Controllers\Project\ListController::class, 'copy'])->name('project.list.copy')->middleware('permission:project.list.copy');
            Route::any('/approve/{id}', [App\Http\Controllers\Project\ListController::class, 'approve'])->name('project.list.approve')->middleware('permission:project.list.approve');
            Route::any('/delete/{id}', [App\Http\Controllers\Project\ListController::class, 'delete'])->name('project.list.delete')->middleware('permission:project.list.delete');
            Route::any('/closing/{id}', [App\Http\Controllers\Project\ListController::class, 'closing'])->name('project.list.closing')->middleware('permission:project.list.closing');
            Route::get('/search', [App\Http\Controllers\Project\ListController::class, 'searchProject'])->name('project.list.search');
            Route::get('/search-period', [App\Http\Controllers\Project\ListController::class, 'searchPeriod'])->name('project.list.search-period');
            Route::get('/search-budget', [App\Http\Controllers\Project\ListController::class, 'searchBudget'])->name('project.list.search-budget');
        });
        Route::group(['prefix' => 'perparation'], function() {
            Route::get('/', [App\Http\Controllers\Project\PreparationController::class, 'index'])->name('project.perparation.index')->middleware('permission:project.perparation.index');
        });
        Route::group(['prefix' => 'chick-in'], function() {
            Route::get('/', [App\Http\Controllers\Project\ChickinController::class, 'index'])->name('project.chick-in.index')->middleware('permission:project.chick-in.index');
            Route::any('/add/{id}', [App\Http\Controllers\Project\ChickinController::class, 'add'])->name('project.chick-in.add')->middleware('permission:project.chick-in.add');
            Route::any('/edit/{id}', [App\Http\Controllers\Project\ChickinController::class, 'edit'])->name('project.chick-in.edit')->middleware('permission:project.chick-in.edit');
            Route::get('/detail/{id}', [App\Http\Controllers\Project\ChickinController::class, 'detail'])->name('project.chick-in.detail')->middleware('permission:project.chick-in.detail');
            Route::post('/approve/{id}', [App\Http\Controllers\Project\ChickinController::class, 'approve'])->name('project.chick-in.approve')->middleware('permission:project.chick-in.approve');
            Route::any('/delete/{id}', [App\Http\Controllers\Project\ChickinController::class, 'delete'])->name('project.chick-in.delete')->middleware('permission:project.chick-in.delete');
        });
        Route::group(['prefix' => 'recording'], function() {
            Route::any('/', [App\Http\Controllers\Project\RecordingController::class, 'index'])->name('project.recording.index')->middleware('permission:project.recording.index');
            Route::any('/add', [App\Http\Controllers\Project\RecordingController::class, 'add'])->name('project.recording.add')->middleware('permission:project.recording.add');
            Route::any('/edit/{id}', [App\Http\Controllers\Project\RecordingController::class, 'edit'])->name('project.recording.edit')->middleware('permission:project.recording.edit');
            Route::any('/detail/{id}', [App\Http\Controllers\Project\RecordingController::class, 'detail'])->name('project.recording.detail')->middleware('permission:project.recording.detail');
            Route::any('/revision-submission/{id}', [App\Http\Controllers\Project\RecordingController::class, 'revisionSubmission'])->name('project.recording.revision-submission')->middleware('permission:project.recording.revision-submission');
            Route::any('/revision-approval/{id}', [App\Http\Controllers\Project\RecordingController::class, 'revisionApproval'])->name('project.recording.revision-approval')->middleware('permission:project.recording.revision-approval');
            Route::any('/delete/{id}', [App\Http\Controllers\Project\RecordingController::class, 'delete'])->name('project.recording.delete'); // ->middleware('permission:project.recording.index');
            Route::any('/approve', [App\Http\Controllers\Project\RecordingController::class, 'approve'])->name('project.recording.approve')->middleware('permission:project.recording.approve');
        });
    });

    Route::group(['prefix' => 'marketing'], function() {
        Route::group(['prefix' => 'list'], function() {
            Route::get('/', [App\Http\Controllers\Marketing\ListController::class, 'index'])->name('marketing.list.index')->middleware('permission:marketing.list.index');
            Route::any('/add', [App\Http\Controllers\Marketing\ListController::class, 'add'])->name('marketing.list.add')->middleware('permission:marketing.list.add');
            Route::any('/edit/{marketing}', [App\Http\Controllers\Marketing\ListController::class, 'edit'])->name('marketing.list.edit')->middleware('permission:marketing.list.edit');
            Route::get('/detail/{marketing}', [App\Http\Controllers\Marketing\ListController::class, 'detail'])->name('marketing.list.detail')->middleware('permission:marketing.list.detail');
            Route::any('/delete/{marketing}', [App\Http\Controllers\Marketing\ListController::class, 'delete'])->name('marketing.list.delete')->middleware('permission:marketing.list.delete');
            Route::any('/realization/{marketing}', [App\Http\Controllers\Marketing\ListController::class, 'realization'])->name('marketing.list.realization')->middleware('permission:marketing.list.realization');
            Route::get('/search', [App\Http\Controllers\Marketing\ListController::class, 'searchMarketing'])->name('marketing.list.search');
            Route::post('/approve/{marketing}', [App\Http\Controllers\Marketing\ListController::class, 'approve'])->name('marketing.list.approve')->middleware('permission:marketing.list.approve');
            Route::get('/search-product/{id}', [App\Http\Controllers\Marketing\ListController::class, 'searchProductByWarehouse'])->name('marketing.list.search-product');

            Route::group(['prefix' => 'payment'], function() {
                Route::get('/{marketing}', [App\Http\Controllers\Marketing\ListPaymentController::class, 'index'])->name('marketing.list.payment.index')->middleware('permission:marketing.list.payment.index');
                Route::post('/add/{marketing}', [App\Http\Controllers\Marketing\ListPaymentController::class, 'add'])->name('marketing.list.payment.add')->middleware('permission:marketing.list.payment.add');
                Route::any('/edit/{payment}', [App\Http\Controllers\Marketing\ListPaymentController::class, 'edit'])->name('marketing.list.payment.edit')->middleware('permission:marketing.list.payment.edit');
                Route::get('/detail/{payment}', [App\Http\Controllers\Marketing\ListPaymentController::class, 'detail'])->name('marketing.list.payment.detail')->middleware('permission:marketing.list.payment.detail');
                Route::get('/delete/{payment}', [App\Http\Controllers\Marketing\ListPaymentController::class, 'delete'])->name('marketing.list.payment.delete')->middleware('permission:marketing.list.payment.delete');
                Route::post('/approve/{payment}', [App\Http\Controllers\Marketing\ListPaymentController::class, 'approve'])->name('marketing.list.payment.approve')->middleware('permission:marketing.list.payment.approve');

                Route::group(['prefix' => 'batch'], function() {
                    Route::post('/', [App\Http\Controllers\Marketing\ListPaymentController::class, 'batch'])->name('marketing.list.payment.batch')->middleware('permission:marketing.list.payment.approve')->middleware('permission:marketing.list.payment.add');
                    Route::post('/add', [App\Http\Controllers\Marketing\ListPaymentController::class, 'batchAdd'])->name('marketing.list.payment.batch.add')->middleware('permission:marketing.list.payment.approve')->middleware('permission:marketing.list.payment.add');
                });
            });
        });

        Route::group(['prefix' => 'return'], function() {
            Route::get('/', [App\Http\Controllers\Marketing\ReturnController::class, 'index'])->name('marketing.return.index')->middleware('permission:marketing.return.index');
            Route::any('/add/{marketing}', [App\Http\Controllers\Marketing\ReturnController::class, 'add'])->name('marketing.return.add')->middleware('permission:marketing.return.add');
            Route::any('/edit/{marketing}', [App\Http\Controllers\Marketing\ReturnController::class, 'edit'])->name('marketing.return.edit')->middleware('permission:marketing.return.edit');
            Route::get('/detail/{marketing}', [App\Http\Controllers\Marketing\ReturnController::class, 'detail'])->name('marketing.return.detail')->middleware('permission:marketing.return.detail');
            Route::any('/delete/{return}', [App\Http\Controllers\Marketing\ReturnController::class, 'delete'])->name('marketing.return.delete')->middleware('permission:marketing.return.delete');
            Route::post('/approve/{return}', [App\Http\Controllers\Marketing\ReturnController::class, 'approve'])->name('marketing.return.approve')->middleware('permission:marketing.return.approve');

            Route::group(['prefix' => 'payment'], function() {
                Route::get('/{marketing}', [App\Http\Controllers\Marketing\ReturnPaymentController::class, 'index'])->name('marketing.return.payment.index')->middleware('permission:marketing.return.payment.index');
                Route::post('/add/{marketing}', [App\Http\Controllers\Marketing\ReturnPaymentController::class, 'add'])->name('marketing.return.payment.add')->middleware('permission:marketing.return.payment.add');
                Route::any('/edit/{payment}', [App\Http\Controllers\Marketing\ReturnPaymentController::class, 'edit'])->name('marketing.return.payment.edit')->middleware('permission:marketing.return.payment.edit');
                Route::get('/detail/{payment}', [App\Http\Controllers\Marketing\ReturnPaymentController::class, 'detail'])->name('marketing.return.payment.detail')->middleware('permission:marketing.return.payment.detail');
                Route::get('/delete/{payment}', [App\Http\Controllers\Marketing\ReturnPaymentController::class, 'delete'])->name('marketing.return.payment.delete')->middleware('permission:marketing.return.payment.delete');
                Route::post('/approve/{payment}', [App\Http\Controllers\Marketing\ReturnPaymentController::class, 'approve'])->name('marketing.return.payment.approve')->middleware('permission:marketing.return.payment.approve');
            });
        });
    });

    Route::group(['prefix' => 'expense'], function() {
        Route::group(['prefix' => 'list'], function() {
            Route::get('/', [App\Http\Controllers\Expense\ExpenseController::class, 'index'])->name('expense.list.index')->middleware('permission:expense.list.index');
            Route::any('/add', [App\Http\Controllers\Expense\ExpenseController::class, 'add'])->name('expense.list.add')->middleware('permission:expense.list.add');
            Route::any('/edit/{expense}', [App\Http\Controllers\Expense\ExpenseController::class, 'edit'])->name('expense.list.edit')->middleware('permission:expense.list.edit');
            Route::get('/detail/{expense}', [App\Http\Controllers\Expense\ExpenseController::class, 'detail'])->name('expense.list.detail')->middleware('permission:expense.list.detail');
            Route::get('/delete/{expense}', [App\Http\Controllers\Expense\ExpenseController::class, 'delete'])->name('expense.list.delete')->middleware('permission:expense.list.delete');
            Route::any('/realization/{expense}', [App\Http\Controllers\Expense\ExpenseController::class, 'realization'])->name('expense.list.realization')->middleware('permission:expense.list.realization');
            Route::post('/return-payment/{expense}', [App\Http\Controllers\Expense\ExpenseController::class, 'returnPayment'])->name('expense.list.return-payment')->middleware('permission:expense.list.return-payment');
            Route::group(['prefix' => 'approve'], function() {
                Route::post('/bulk', [App\Http\Controllers\Expense\ExpenseController::class, 'approveBulk'])->name('expense.list.approve.bulk')->middleware('permission:expense.list.approve.farm|expense.list.approve.finance');
                Route::post('/farm/{expense}', [App\Http\Controllers\Expense\ExpenseController::class, 'approve'])->name('expense.list.approve.farm')->middleware('permission:expense.list.approve.farm');
                Route::post('/finance/{expense}', [App\Http\Controllers\Expense\ExpenseController::class, 'approve'])->name('expense.list.approve.finance')->middleware('permission:expense.list.approve.finance');
            });
            Route::get('/finish/{expense}', [App\Http\Controllers\Expense\ExpenseController::class, 'finish'])->name('expense.list.finish');
            Route::get('/search', [App\Http\Controllers\Expense\ExpenseController::class, 'searchExpense'])->name('expense.list.search');

            Route::group(['prefix' => 'disburse'], function() {
                Route::get('/{expense}', [App\Http\Controllers\Expense\ExpenseDisburseController::class, 'index'])->name('expense.list.disburse.index')->middleware('permission:expense.list.disburse.index');
                Route::any('/add/{expense}', [App\Http\Controllers\Expense\ExpenseDisburseController::class, 'add'])->name('expense.list.disburse.add')->middleware('permission:expense.list.disburse.add');
                Route::any('/edit/{disburse}', [App\Http\Controllers\Expense\ExpenseDisburseController::class, 'edit'])->name('expense.list.disburse.edit')->middleware('permission:expense.list.disburse.edit');
                Route::get('/detail/{disburse}', [App\Http\Controllers\Expense\ExpenseDisburseController::class, 'detail'])->name('expense.list.disburse.detail')->middleware('permission:expense.list.disburse.detail');
                Route::get('/delete/{disburse}', [App\Http\Controllers\Expense\ExpenseDisburseController::class, 'delete'])->name('expense.list.disburse.delete')->middleware('permission:expense.list.disburse.delete');

                Route::group(['prefix' => 'batch'], function() {
                    Route::post('/', [App\Http\Controllers\Expense\ExpenseDisburseController::class, 'batch'])->name('expense.list.disburse.batch')->middleware('permission:expense.list.disburse.approve')->middleware('permission:expense.list.disburse.add');
                    Route::post('/add', [App\Http\Controllers\Expense\ExpenseDisburseController::class, 'batchAdd'])->name('expense.list.disburse.batch.add')->middleware('permission:expense.list.disburse.approve')->middleware('permission:expense.list.disburse.add');
                });
            });
        });
        Route::group(['prefix' => 'recap'], function() {
            Route::get('/', [App\Http\Controllers\Expense\ExpenseController::class, 'recap'])->name('expense.recap.index')->middleware('permission:expense.recap.index');
        });
    });

    Route::group(['prefix' => 'finance'], function() {
        Route::get('/', [App\Http\Controllers\Finance\FinanceController::class, 'index'])->name('finance.index')->middleware('permission:finance.index');
    });

    Route::group(['prefix' => 'report'], function() {
        Route::get('/', [App\Http\Controllers\Report\ReportLocationController::class, 'index'])->name('report.index');
        Route::get('/{location}', [App\Http\Controllers\Report\ReportLocationController::class, 'detail'])->name('report.detail.location');
        Route::get('/{location}/{project}', [App\Http\Controllers\Report\ReportKandangController::class, 'detail'])->name('report.detail.kandang');
        Route::group(['prefix' => 'detail/{location}'], function() {
            Route::get('/sapronak', [App\Http\Controllers\Report\ReportLocationController::class, 'sapronak'])->name('report.detail.location.sapronak');
            Route::get('/perhitungan', [App\Http\Controllers\Report\ReportLocationController::class, 'perhitunganSapronak'])->name('report.detail.location.perhitungan');
            Route::get('/penjualan', [App\Http\Controllers\Report\ReportLocationController::class, 'penjualan'])->name('report.detail.location.penjualan');
            Route::get('/overhead', [App\Http\Controllers\Report\ReportLocationController::class, 'overhead'])->name('report.detail.location.overhead');
            Route::get('/ekspedisi', [App\Http\Controllers\Report\ReportLocationController::class, 'hppEkspedisi'])->name('report.detail.location.ekspedisi');
            Route::get('/produksi', [App\Http\Controllers\Report\ReportLocationController::class, 'dataProduksi'])->name('report.detail.location.produksi');
            Route::get('/keuangan', [App\Http\Controllers\Report\ReportLocationController::class, 'keuangan'])->name('report.detail.location.keuangan');
            Route::group(['prefix' => '{project}'], function() {
                Route::get('/sapronak', [App\Http\Controllers\Report\ReportKandangController::class, 'sapronak'])->name('report.detail.kandang.sapronak');
                Route::get('/perhitungan', [App\Http\Controllers\Report\ReportKandangController::class, 'perhitunganSapronak'])->name('report.detail.kandang.perhitungan');
                Route::get('/penjualan', [App\Http\Controllers\Report\ReportKandangController::class, 'penjualan'])->name('report.detail.kandang.penjualan');
                Route::get('/overhead', [App\Http\Controllers\Report\ReportKandangController::class, 'overhead'])->name('report.detail.kandang.overhead');
                Route::get('/ekspedisi', [App\Http\Controllers\Report\ReportKandangController::class, 'hppEkspedisi'])->name('report.detail.kandang.ekspedisi');
                Route::get('/produksi', [App\Http\Controllers\Report\ReportKandangController::class, 'dataProduksi'])->name('report.detail.kandang.produksi');
                Route::get('/keuangan', [App\Http\Controllers\Report\ReportKandangController::class, 'keuangan'])->name('report.detail.kandang.keuangan');
            });
        });
    });

    Route::group(['prefix' => 'purchase'], function() {
        Route::get('/', [App\Http\Controllers\Purchase\ListController::class, 'index'])->name('purchase.index')->middleware('permission:purchase.index');
        Route::any('/add', [App\Http\Controllers\Purchase\ListController::class, 'add'])->name('purchase.add')->middleware('permission:purchase.add');
        Route::any('/copy/{id}', [App\Http\Controllers\Purchase\ListController::class, 'copy'])->name('purchase.copy')->middleware('permission:purchase.copy');
        Route::any('/edit/{id}', [App\Http\Controllers\Purchase\ListController::class, 'edit'])->name('purchase.edit')->middleware('permission:purchase.edit');
        Route::any('/approve/{id}', [App\Http\Controllers\Purchase\ListController::class, 'approve'])->name('purchase.approve')->middleware('permission:purchase.approve');
        Route::any('/detail/{id}', [App\Http\Controllers\Purchase\ListController::class, 'detail'])->name('purchase.detail')->middleware('permission:purchase.detail');
        Route::any('/delete/{id}', [App\Http\Controllers\Purchase\ListController::class, 'delete'])->name('purchase.delete')->middleware('permission:purchase.delete');
        Route::any('/payment/{id}', [App\Http\Controllers\Purchase\ListController::class, 'payment'])->name('purchase.payment');  // ->middleware('permission:purchase.delete');
    });

    Route::group(['prefix' => 'ph'], function() {
        Route::group(['prefix' => 'performance'], function() {
            Route::get('/', [App\Http\Controllers\Ph\PerformanceController::class, 'index'])->name('ph.performance.index')->middleware('permission:ph.performance.index');
            Route::any('/detail', [App\Http\Controllers\Ph\PerformanceController::class, 'detail'])->name('ph.performance.detail')->middleware('permission:ph.performance.detail');
            Route::any('/download', [App\Http\Controllers\Ph\PerformanceController::class, 'download'])->name('ph.performance.download')->middleware('permission:ph.performance.download');
            Route::any('/add', [App\Http\Controllers\Ph\PerformanceController::class, 'add'])->name('ph.performance.add')->middleware('permission:ph.performance.add');
            Route::any('/edit/{id}', [App\Http\Controllers\Ph\PerformanceController::class, 'edit'])->name('ph.performance.edit')->middleware('permission:ph.performance.edit');
            Route::any('/delete/{id}', [App\Http\Controllers\Ph\PerformanceController::class, 'delete'])->name('ph.performance.delete')->middleware('permission:ph.performance.delete');
        });

        Route::group(['prefix' => 'report-complaint'], function() {
            Route::get('/', [App\Http\Controllers\Ph\PhComplaintController::class, 'index'])->name('ph.report-complaint.index')->middleware('permission:ph.report-complaint.index');
            Route::any('/add', [App\Http\Controllers\Ph\PhComplaintController::class, 'add'])->name('ph.report-complaint.add')->middleware('permission:ph.report-complaint.add');
            Route::any('/edit/{id}', [App\Http\Controllers\Ph\PhComplaintController::class, 'edit'])->name('ph.report-complaint.edit')->middleware('permission:ph.report-complaint.edit');
            Route::any('/detail/{id}', [App\Http\Controllers\Ph\PhComplaintController::class, 'detail'])->name('ph.report-complaint.detail')->middleware('permission:ph.report-complaint.detail');
            Route::any('/download/{id}', [App\Http\Controllers\Ph\PhComplaintController::class, 'download'])->name('ph.report-complaint.download')->middleware('permission:ph.report-complaint.download');
            Route::any('/delete/{id}', [App\Http\Controllers\Ph\PhComplaintController::class, 'delete'])->name('ph.report-complaint.delete')->middleware('permission:ph.report-complaint.delete');
            Route::get('/search', [App\Http\Controllers\Ph\PhComplaintController::class, 'searchComplaint'])->name('ph.report-complaint.search');
            Route::post('/upload-image', [App\Http\Controllers\Ph\PhComplaintController::class, 'uploadImage'])->name('ph.report-complaint.upload-image')->middleware('permission:ph.report-complaint.upload-image');
        });

        Route::group(['prefix' => 'symptom'], function() {
            Route::get('/', [App\Http\Controllers\Ph\PhSymptomController::class, 'index'])->name('ph.symptom.index')->middleware('permission:ph.symptom.index');
            Route::any('/add', [App\Http\Controllers\Ph\PhSymptomController::class, 'add'])->name('ph.symptom.add')->middleware('permission:ph.symptom.add');
            Route::any('/edit/{id}', [App\Http\Controllers\Ph\PhSymptomController::class, 'edit'])->name('ph.symptom.edit')->middleware('permission:ph.symptom.edit');
            Route::any('/delete/{id}', [App\Http\Controllers\Ph\PhSymptomController::class, 'delete'])->name('ph.symptom.delete')->middleware('permission:ph.symptom.delete');
            Route::get('/search', [App\Http\Controllers\Ph\PhSymptomController::class, 'searchSymptom'])->name('ph.symptom.search');
        });
    });

    Route::group(['prefix' => 'inventory'], function() {
        Route::group(['prefix' => 'product'], function() {
            Route::get('/', [App\Http\Controllers\Inventory\ProductController::class, 'index'])->name('inventory.product.index')->middleware('permission:inventory.product.index');
            Route::any('/detail/{id}', [App\Http\Controllers\Inventory\ProductController::class, 'detail'])->name('inventory.product.detail')->middleware('permission:inventory.product.detail');
            Route::any('/check-stock-by-warehouse', [App\Http\Controllers\Inventory\ProductController::class, 'checkStockByWarehouse'])->name('inventory.product.check-stock-by-warehouse');
            Route::any('/search-product-warehouse', [App\Http\Controllers\Inventory\ProductController::class, 'searchProductWarehouse'])->name('inventory.product.search-product-warehouse');
        });

        Route::group(['prefix' => 'adjustment'], function() {
            Route::get('/', [App\Http\Controllers\Inventory\AdjustmentController::class, 'index'])->name('inventory.adjustment.index')->middleware('permission:inventory.adjustment.index');
            Route::any('/add', [App\Http\Controllers\Inventory\AdjustmentController::class, 'add'])->name('inventory.adjustment.add')->middleware('permission:inventory.adjustment.add');
        });

        Route::group(['prefix' => 'movement'], function() {
            Route::get('/', [App\Http\Controllers\Inventory\MovementController::class, 'index'])->name('inventory.movement.index')->middleware('permission:inventory.movement.index');
            Route::any('/add', [App\Http\Controllers\Inventory\MovementController::class, 'add'])->name('inventory.movement.add')->middleware('permission:inventory.movement.add');
            Route::any('/detail/{id}', [App\Http\Controllers\Inventory\MovementController::class, 'detail'])->name('inventory.movement.detail')->middleware('permission:inventory.movement.detail');
        });
    });

    Route::group(['prefix' => 'data-master'], function() {
        Route::group(['prefix' => 'product-category'], function() {
            Route::get('/', [App\Http\Controllers\DataMaster\ProductCategoryController::class, 'index'])->name('data-master.product-category.index')->middleware('permission:data-master.product-category.index');
            Route::any('/add', [App\Http\Controllers\DataMaster\ProductCategoryController::class, 'add'])->name('data-master.product-category.add')->middleware('permission:data-master.product-category.add');
            Route::any('/edit/{id}', [App\Http\Controllers\DataMaster\ProductCategoryController::class, 'edit'])->name('data-master.product-category.edit')->middleware('permission:data-master.product-category.edit');
            Route::any('/delete/{id}', [App\Http\Controllers\DataMaster\ProductCategoryController::class, 'delete'])->name('data-master.product-category.delete')->middleware('permission:data-master.product-category.delete');
            Route::get('/search', [App\Http\Controllers\DataMaster\ProductCategoryController::class, 'searchProductCategory'])->name('data-master.product-category.search');
        });

        Route::group(['prefix' => 'product-sub-category'], function() {
            Route::get('/', [App\Http\Controllers\DataMaster\ProductSubCategoryController::class, 'index'])->name('data-master.product-sub-category.index')->middleware('permission:data-master.product-sub-category.index');
            Route::any('/add', [App\Http\Controllers\DataMaster\ProductSubCategoryController::class, 'add'])->name('data-master.product-sub-category.add')->middleware('permission:data-master.product-sub-category.add');
            Route::any('/edit/{id}', [App\Http\Controllers\DataMaster\ProductSubCategoryController::class, 'edit'])->name('data-master.product-sub-category.edit')->middleware('permission:data-master.product-sub-category.edit');
            Route::any('/delete/{id}', [App\Http\Controllers\DataMaster\ProductSubCategoryController::class, 'delete'])->name('data-master.product-sub-category.delete')->middleware('permission:data-master.product-sub-category.delete');
            Route::get('/search', [App\Http\Controllers\DataMaster\ProductSubCategoryController::class, 'searchProductSubCategory'])->name('data-master.product-sub-category.search');
        });

        Route::group(['prefix' => 'product-component'], function() {
            Route::get('/', [App\Http\Controllers\DataMaster\ProductComponentController::class, 'index'])->name('data-master.product-component.index')->middleware('permission:data-master.product-component.index');
            Route::any('/add', [App\Http\Controllers\DataMaster\ProductComponentController::class, 'add'])->name('data-master.product-component.add')->middleware('permission:data-master.product-component.add');
            Route::any('/edit/{id}', [App\Http\Controllers\DataMaster\ProductComponentController::class, 'edit'])->name('data-master.product-component.edit')->middleware('permission:data-master.product-component.edit');
            Route::any('/delete/{id}', [App\Http\Controllers\DataMaster\ProductComponentController::class, 'delete'])->name('data-master.product-component.delete')->middleware('permission:data-master.product-component.delete');
            Route::get('/search', [App\Http\Controllers\DataMaster\ProductComponentController::class, 'searchProductComponent'])->name('data-master.product-component.search');
        });

        Route::group(['prefix' => 'product'], function() {
            Route::get('/', [App\Http\Controllers\DataMaster\ProductController::class, 'index'])->name('data-master.product.index')->middleware('permission:data-master.product.index');
            Route::any('/add', [App\Http\Controllers\DataMaster\ProductController::class, 'add'])->name('data-master.product.add')->middleware('permission:data-master.product.add');
            Route::any('/edit/{id}', [App\Http\Controllers\DataMaster\ProductController::class, 'edit'])->name('data-master.product.edit')->middleware('permission:data-master.product.edit');
            Route::any('/delete/{id}', [App\Http\Controllers\DataMaster\ProductController::class, 'delete'])->name('data-master.product.delete')->middleware('permission:data-master.product.delete');
            Route::get('/search', [App\Http\Controllers\DataMaster\ProductController::class, 'searchProduct'])->name('data-master.product.search');
        });

        Route::group(['prefix' => 'bank'], function() {
            Route::get('/', [App\Http\Controllers\DataMaster\BankController::class, 'index'])->name('data-master.bank.index')->middleware('permission:data-master.bank.index');
            Route::any('/add', [App\Http\Controllers\DataMaster\BankController::class, 'add'])->name('data-master.bank.add')->middleware('permission:data-master.bank.add');
            Route::any('/edit/{id}', [App\Http\Controllers\DataMaster\BankController::class, 'edit'])->name('data-master.bank.edit')->middleware('permission:data-master.bank.edit');
            Route::any('/delete/{id}', [App\Http\Controllers\DataMaster\BankController::class, 'delete'])->name('data-master.bank.delete')->middleware('permission:data-master.bank.delete');
            Route::get('/search', [App\Http\Controllers\DataMaster\BankController::class, 'searchBank'])->name('data-master.bank.search');
        });

        Route::group(['prefix' => 'kandang'], function() {
            Route::get('/', [App\Http\Controllers\DataMaster\KandangController::class, 'index'])->name('data-master.kandang.index')->middleware('permission:data-master.kandang.index');
            Route::any('/add', [App\Http\Controllers\DataMaster\KandangController::class, 'add'])->name('data-master.kandang.add')->middleware('permission:data-master.kandang.add');
            Route::any('/edit/{id}', [App\Http\Controllers\DataMaster\KandangController::class, 'edit'])->name('data-master.kandang.edit')->middleware('permission:data-master.kandang.edit');
            Route::any('/delete/{id}', [App\Http\Controllers\DataMaster\KandangController::class, 'delete'])->name('data-master.kandang.delete')->middleware('permission:data-master.kandang.delete');
            Route::get('/search', [App\Http\Controllers\DataMaster\KandangController::class, 'searchKandang'])->name('data-master.kandang.search');
        });

        Route::group(['prefix' => 'area'], function() {
            Route::get('/', [App\Http\Controllers\DataMaster\AreaController::class, 'index'])->name('data-master.area.index')->middleware('permission:data-master.area.index');
            Route::any('/add', [App\Http\Controllers\DataMaster\AreaController::class, 'add'])->name('data-master.area.add')->middleware('permission:data-master.area.add');
            Route::any('/edit/{id}', [App\Http\Controllers\DataMaster\AreaController::class, 'edit'])->name('data-master.area.edit')->middleware('permission:data-master.area.edit');
            Route::any('/delete/{id}', [App\Http\Controllers\DataMaster\AreaController::class, 'delete'])->name('data-master.area.delete')->middleware('permission:data-master.area.delete');
            Route::get('/search', [App\Http\Controllers\DataMaster\AreaController::class, 'searchArea'])->name('data-master.area.search');
        });

        Route::group(['prefix' => 'location'], function() {
            Route::get('/', [App\Http\Controllers\DataMaster\LocationController::class, 'index'])->name('data-master.location.index')->middleware('permission:data-master.location.index');
            Route::any('/add', [App\Http\Controllers\DataMaster\LocationController::class, 'add'])->name('data-master.location.add')->middleware('permission:data-master.location.add');
            Route::any('/edit/{id}', [App\Http\Controllers\DataMaster\LocationController::class, 'edit'])->name('data-master.location.edit')->middleware('permission:data-master.location.edit');
            Route::any('/delete/{id}', [App\Http\Controllers\DataMaster\LocationController::class, 'delete'])->name('data-master.location.delete')->middleware('permission:data-master.location.delete');
            Route::get('/search', [App\Http\Controllers\DataMaster\LocationController::class, 'searchLocation'])->name('data-master.location.search');
        });

        Route::group(['prefix' => 'company'], function() {
            Route::get('/', [App\Http\Controllers\DataMaster\CompanyController::class, 'index'])->name('data-master.company.index')->middleware('permission:data-master.company.index');
            Route::any('/add', [App\Http\Controllers\DataMaster\CompanyController::class, 'add'])->name('data-master.company.add')->middleware('permission:data-master.company.add');
            Route::any('/edit/{id}', [App\Http\Controllers\DataMaster\CompanyController::class, 'edit'])->name('data-master.company.edit')->middleware('permission:data-master.company.edit');
            Route::any('/delete/{id}', [App\Http\Controllers\DataMaster\CompanyController::class, 'delete'])->name('data-master.company.delete')->middleware('permission:data-master.company.delete');
            Route::get('/search', [App\Http\Controllers\DataMaster\CompanyController::class, 'searchCompany'])->name('data-master.company.search');
        });

        Route::group(['prefix' => 'department'], function() {
            Route::get('/', [App\Http\Controllers\DataMaster\DepartmentController::class, 'index'])->name('data-master.department.index')->middleware('permission:data-master.department.index');
            Route::any('/add', [App\Http\Controllers\DataMaster\DepartmentController::class, 'add'])->name('data-master.department.add')->middleware('permission:data-master.department.add');
            Route::any('/edit/{id}', [App\Http\Controllers\DataMaster\DepartmentController::class, 'edit'])->name('data-master.department.edit')->middleware('permission:data-master.department.edit');
            Route::any('/delete/{id}', [App\Http\Controllers\DataMaster\DepartmentController::class, 'delete'])->name('data-master.department.delete')->middleware('permission:data-master.department.delete');
            Route::get('/search', [App\Http\Controllers\DataMaster\DepartmentController::class, 'searchDepartment'])->name('data-master.department.search');
        });

        Route::group(['prefix' => 'supplier'], function() {
            Route::get('/', [App\Http\Controllers\DataMaster\SupplierController::class, 'index'])->name('data-master.supplier.index')->middleware('permission:data-master.supplier.index');
            Route::any('/add', [App\Http\Controllers\DataMaster\SupplierController::class, 'add'])->name('data-master.supplier.add')->middleware('permission:data-master.supplier.add');
            Route::any('/edit/{id}', [App\Http\Controllers\DataMaster\SupplierController::class, 'edit'])->name('data-master.supplier.edit')->middleware('permission:data-master.supplier.edit');
            Route::any('/delete/{id}', [App\Http\Controllers\DataMaster\SupplierController::class, 'delete'])->name('data-master.supplier.delete')->middleware('permission:data-master.supplier.delete');
            Route::get('/search', [App\Http\Controllers\DataMaster\SupplierController::class, 'searchSupplier'])->name('data-master.supplier.search');
            Route::get('/hatchery/search', [App\Http\Controllers\DataMaster\SupplierController::class, 'searchHatchery'])->name('data-master.supplier.hatchery.search');
        });

        Route::group(['prefix' => 'customer'], function() {
            Route::get('/', [App\Http\Controllers\DataMaster\CustomerController::class, 'index'])->name('data-master.customer.index')->middleware('permission:data-master.customer.index');
            Route::any('/add', [App\Http\Controllers\DataMaster\CustomerController::class, 'add'])->name('data-master.customer.add')->middleware('permission:data-master.customer.add');
            Route::any('/edit/{id}', [App\Http\Controllers\DataMaster\CustomerController::class, 'edit'])->name('data-master.customer.edit')->middleware('permission:data-master.customer.edit');
            Route::any('/delete/{id}', [App\Http\Controllers\DataMaster\CustomerController::class, 'delete'])->name('data-master.customer.delete')->middleware('permission:data-master.customer.delete');
            Route::get('/search', [App\Http\Controllers\DataMaster\CustomerController::class, 'searchCustomer'])->name('data-master.customer.search');
        });

        Route::group(['prefix' => 'fcr'], function() {
            Route::get('/', [App\Http\Controllers\DataMaster\FcrController::class, 'index'])->name('data-master.fcr.index')->middleware('permission:data-master.fcr.index');
            Route::any('/add', [App\Http\Controllers\DataMaster\FcrController::class, 'add'])->name('data-master.fcr.add')->middleware('permission:data-master.fcr.add');
            Route::any('/edit/{id}', [App\Http\Controllers\DataMaster\FcrController::class, 'edit'])->name('data-master.fcr.edit')->middleware('permission:data-master.fcr.edit');
            Route::any('/delete/{id}', [App\Http\Controllers\DataMaster\FcrController::class, 'delete'])->name('data-master.fcr.delete')->middleware('permission:data-master.fcr.delete');
            Route::get('/search', [App\Http\Controllers\DataMaster\FcrController::class, 'searchFcr'])->name('data-master.fcr.search');
            Route::get('/search-standard', [App\Http\Controllers\DataMaster\FcrController::class, 'searchFcrStandard'])->name('data-master.fcr.search-standard');
        });

        Route::group(['prefix' => 'warehouse'], function() {
            Route::get('/', [App\Http\Controllers\DataMaster\WarehouseController::class, 'index'])->name('data-master.warehouse.index')->middleware('permission:data-master.warehouse.index');
            Route::any('/add', [App\Http\Controllers\DataMaster\WarehouseController::class, 'add'])->name('data-master.warehouse.add')->middleware('permission:data-master.warehouse.add');
            Route::any('/edit/{id}', [App\Http\Controllers\DataMaster\WarehouseController::class, 'edit'])->name('data-master.warehouse.edit')->middleware('permission:data-master.warehouse.edit');
            Route::any('/delete/{id}', [App\Http\Controllers\DataMaster\WarehouseController::class, 'delete'])->name('data-master.warehouse.delete')->middleware('permission:data-master.warehouse.delete');
            Route::get('/search', [App\Http\Controllers\DataMaster\WarehouseController::class, 'searchWarehouse'])->name('data-master.warehouse.search');
            Route::get('/search-kandang', [App\Http\Controllers\DataMaster\WarehouseController::class, 'searchKandangWarehouse'])->name('data-master.warehouse.search-kandang');
        });

        Route::group(['prefix' => 'uom'], function() {
            Route::get('/', [App\Http\Controllers\DataMaster\UomController::class, 'index'])->name('data-master.uom.index')->middleware('permission:data-master.uom.index');
            Route::any('/add', [App\Http\Controllers\DataMaster\UomController::class, 'add'])->name('data-master.uom.add')->middleware('permission:data-master.uom.add');
            Route::any('/edit/{id}', [App\Http\Controllers\DataMaster\UomController::class, 'edit'])->name('data-master.uom.edit')->middleware('permission:data-master.uom.edit');
            Route::any('/delete/{id}', [App\Http\Controllers\DataMaster\UomController::class, 'delete'])->name('data-master.uom.delete')->middleware('permission:data-master.uom.delete');
            Route::get('/search', [App\Http\Controllers\DataMaster\UomController::class, 'searchUom'])->name('data-master.uom.search');
        });

        Route::group(['prefix' => 'nonstock'], function() {
            Route::get('/', [App\Http\Controllers\DataMaster\NonstockController::class, 'index'])->name('data-master.nonstock.index')->middleware('permission:data-master.nonstock.index');
            Route::any('/add', [App\Http\Controllers\DataMaster\NonstockController::class, 'add'])->name('data-master.nonstock.add')->middleware('permission:data-master.nonstock.add');
            Route::any('/edit/{id}', [App\Http\Controllers\DataMaster\NonstockController::class, 'edit'])->name('data-master.nonstock.edit')->middleware('permission:data-master.nonstock.edit');
            Route::any('/delete/{id}', [App\Http\Controllers\DataMaster\NonstockController::class, 'delete'])->name('data-master.nonstock.delete')->middleware('permission:data-master.nonstock.delete');
            Route::get('/search', [App\Http\Controllers\DataMaster\NonstockController::class, 'searchNonstock'])->name('data-master.nonstock.search');
        });
    });

    Route::group(['prefix' => 'user-management'], function() {
        Route::group(['prefix' => 'user'], function() {
            Route::get('/', [App\Http\Controllers\UserManagement\UsersController::class, 'index'])->name('user-management.user.index')->middleware('permission:user-management.user.index');
            Route::any('/add', [App\Http\Controllers\UserManagement\UsersController::class, 'add'])->name('user-management.user.add')->middleware('permission:user-management.user.add');
            Route::any('/edit/{id}', [App\Http\Controllers\UserManagement\UsersController::class, 'edit'])->name('user-management.user.edit')->middleware('permission:user-management.user.edit');
            Route::any('/delete/{id}', [App\Http\Controllers\UserManagement\UsersController::class, 'delete'])->name('user-management.user.delete')->middleware('permission:user-management.user.delete');
            Route::get('/search', [App\Http\Controllers\UserManagement\UsersController::class, 'searchUser'])->name('user-management.user.search');
        });

        Route::group(['prefix' => 'role'], function() {
            Route::get('/', [App\Http\Controllers\UserManagement\RoleController::class, 'index'])->name('user-management.role.index')->middleware('permission:user-management.role.index');
            Route::any('/add', [App\Http\Controllers\UserManagement\RoleController::class, 'add'])->name('user-management.role.add')->middleware('permission:user-management.role.add');
            Route::any('/edit/{id}', [App\Http\Controllers\UserManagement\RoleController::class, 'edit'])->name('user-management.role.edit')->middleware('permission:user-management.role.edit');
            Route::any('/delete/{id}', [App\Http\Controllers\UserManagement\RoleController::class, 'delete'])->name('user-management.role.delete')->middleware('permission:user-management.role.delete');
            Route::get('/search', [App\Http\Controllers\UserManagement\RoleController::class, 'searchRole'])->name('user-management.role.search');
        });

        Route::group(['prefix' => 'permission'], function() {
            Route::get('/', [App\Http\Controllers\UserManagement\PermissionController::class, 'index'])->name('user-management.permission.index')->middleware('permission:user-management.permission.index');
            Route::any('/add', [App\Http\Controllers\UserManagement\PermissionController::class, 'add'])->name('user-management.permission.add')->middleware('permission:user-management.permission.add');
            Route::any('/edit/{id}', [App\Http\Controllers\UserManagement\PermissionController::class, 'edit'])->name('user-management.permission.edit')->middleware('permission:user-management.permission.edit');
            Route::any('/delete/{id}', [App\Http\Controllers\UserManagement\PermissionController::class, 'delete'])->name('user-management.permission.delete')->middleware('permission:user-management.permission.delete');
            Route::get('/search', [App\Http\Controllers\UserManagement\PermissionController::class, 'searchPermission'])->name('user-management.permission.search');
        });
    });
});
