<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\ExpiryController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\JsonController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StockController;
use App\Http\Middleware\AuthMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/login', function () {
    return view('Auth.login');
})->name('login');

Route::post('/login/authorize', [AuthController::class, 'login'])->name('authorize');

Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(AuthMiddleware::class)->group(function () {
    Route::resource('/group', GroupController::class)->whereNumber('group');

    Route::resource('/product', ProductController::class);

    Route::get('/', [JsonController::class, 'storeToJson'])->name('home');

    Route::get('/bill/{id}/get', [BillController::class, 'getProduct']);

    Route::controller(SaleController::class)->group(function () {
        Route::get('/sale', 'getSaleReport')->name('sale.index');
        Route::prefix('/reports')->group(function () {
            Route::get('/sale/print/{from}/{to}', 'printSaleReportByDate')->name('printSaleByDate');
            Route::get('/sale/print', 'printSaleReport')->name('printSaleReport');
        });
    });

    Route::controller(ExpiryController::class)->group(function () {
        Route::prefix('/expiry')->group(function () {

            Route::get('/{id}/{date}', 'show')->whereNumber('id');
            Route::put('/{id}/{date}', 'update')->whereNumber('id');
            Route::delete('/{id}/{date}', 'destroy')->whereNumber('id');
        });

        Route::prefix('/print')->group(function () {
            Route::get('/weekly', 'printWeekly')->name('printWeeklyExp');
            Route::get('/monthly', 'printMonthly')->name('printMonthlyExp');
            Route::get('/yearly', 'printYearly')->name('printYearlyExp');
            Route::get('/return-report', 'printExpReport')->name('printExpReturnReport');
        });

        Route::prefix('/print/report')->group(function () {
            Route::get('/weekly/{from?}/{to?}', 'printWeeklyByDates')->name('printWeeklyByDates');
            Route::get('/monthly/{from?}/{to?}', 'printMonthlyByDates')->name('printMonthlyByDates');
            Route::get('/yearly/{from?}/{to?}', 'printYearlyByDates')->name('printYearlyByDates');
            Route::get('/return-report/{from?}/{to?}', 'printExpReportByDates')->name('printExpReturnReportByDates');
        });

        Route::get('/weekly', 'weeklyReport')->name('weeklyExpiry');
        Route::get('/monthly', 'monthlyReport')->name('monthlyExpiry');
        Route::get('/yearly', 'yearlyReport')->name('yearlyExpiry');
        Route::get('/return-report', 'returnExpReport')->name('expiryReturnReport');
    });

    Route::resource('/expiry', ExpiryController::class);

    Route::controller(StockController::class)->group(function () {
        Route::prefix('/stock')->group(function () {
            Route::prefix('/print')->group(function () {
                Route::prefix('/available')->group(function () {
                    Route::get('/', 'printAvailableStock')->name('availableStockPrint');
                    Route::get('/{from?}/{to?}', 'printAvailableStockByDates')->name('printAvailableByDate');
                });
                Route::prefix('/expired')->group(function () {
                    Route::get('/', 'printExpiredStock')->name('expiredStockPrint');
                    Route::get('/{from?}/{to?}', 'printExpiredStockByDates')->name('printExpStockByDate');
                });
                Route::prefix('/required')->group(function () {
                    Route::get('/', 'printRequiredStock')->name('requiredStockPrint');
                    Route::get('/{from?}/{to?}', 'printRequiredStockByDates')->name('printRequiredStockByDate');
                });
                Route::prefix('/product-stock')->group(function () {
                    Route::get('/', 'printProductStockEntryReport')->name('stock.print');
                    Route::get('/{from?}/{to?}', 'printStockByDates')->name('stock.printByDate');
                });
            });

            Route::get('/{id}/{date}', 'show')->name('getStockEntry')->whereNumber('id');
            Route::put('/{id}/{date}', 'update')->name('updateStock')->whereNumber('id');
            Route::delete('/{id}/{date}', 'destroy')->name('deleteStockEntry')->whereNumber('id');

            Route::get('/available', 'getAvailableStock')->name('getAvailable');
            Route::get('/expired', 'getExpired')->name('getExpired');
            Route::get('/required', 'getRequiredStock')->name('getRequired');
            Route::get('/product-stock', 'getProductStockEntryByDealer')->name('getStockReport');
        });
    });

    Route::resource('/stock', StockController::class);

    Route::controller(PurchaseController::class)->group(function () {

        Route::get('/reports/purchase', 'getPurchaseReport')->name('purchaseReport');

        Route::prefix('/purchase')->group(function () {
            Route::get('/{id}/{date}', 'show')->whereNumber('id');
            Route::put('/{id}/{date}', 'update')->whereNumber('id');
            Route::delete('/{id}/{date}', 'destroy')->whereNumber('id');
        });

        Route::prefix('/purchase/print')->group(function () {
            Route::get('/', 'printPurchaseReport')->name('purchase.print');
            Route::get('/{fromDate?}/{toDate?}', 'printPurchaseReportByDates')->name('purchase.printByDate');
        });
    });

    Route::resource('/purchase', PurchaseController::class);

    Route::controller(GroupController::class)->group(function () {
        Route::prefix('/sub-group')->group(function () {
            Route::get('/show/{id}', 'getSubGroup')->whereNumber('id')->name('showSubGroup');
            Route::put('/{id}', 'updateSubGroup')->whereNumber('id')->name('updateSubGroup');
            Route::delete('/{id}', 'deleteSubGroup')->whereNumber('id')->name('deleteSubGroup');
            Route::post('/store', 'newSubGroup')->name('storeSubGroup');
        });
    });

    Route::controller(BillController::class)->group(function () {
        Route::prefix('/bill')->group(function () {
            Route::get('/{billNo}/edit/{date}', 'edit')->whereNumber('billNo');
            Route::put('/{billNo}/{date}', 'update')->whereNumber('billNo');
            Route::delete('/{billNo}/{date}', 'destroy')->whereNumber('billNo');
        });
    });

    Route::resource('/bill', BillController::class)->whereNumber('bill');
});
Route::fallback(function () {
    return view('partials.pageNotFound');
});
