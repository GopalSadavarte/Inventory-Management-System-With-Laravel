<?php

use App\Http\Controllers\BillController;
use App\Http\Controllers\ExpiryController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\JsonController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StockController;
use Illuminate\Support\Facades\Route;

const DateExpression = '\d{4}-\d{2}-\d{2}';

Route::resource('/group', GroupController::class)->whereNumber('group');

Route::resource('/product', ProductController::class);

Route::get('/', [JsonController::class, 'storeToJson'])->name('home');

Route::get('/bill/{id}/get', [BillController::class, 'getProduct']);

Route::controller(SaleController::class)->group(function () {
    Route::get('/sale', 'getSaleReport')->name('sale.index');
    Route::prefix('/reports')->group(function () {
        Route::get('/sale/print/{from}/{to}', 'printSaleReportByDate')->name('printSaleByDate')->where(['from', 'to'], DateExpression);
        Route::get('/sale/print', 'printSaleReport')->name('printSaleReport');
    });
});

Route::controller(ExpiryController::class)->group(function () {
    Route::prefix('/expiry')->group(function () {

        Route::get('/{id}/{date}', 'show')->whereNumber('id')->where('date', DateExpression);
        Route::put('/{id}/{date}', 'update')->whereNumber('id')->where('date', DateExpression);
        Route::delete('/{id}/{date}', 'destroy')->whereNumber('id')->where('date', DateExpression);

        Route::prefix('/print')->group(function () {
            Route::get('/weekly', 'printWeekly')->name('printWeeklyExp');
            Route::get('/monthly', 'printMonthly')->name('printMonthlyExp');
            Route::get('/yearly', 'printYearly')->name('printYearlyExp');
            Route::get('/return-report', 'printExpReport')->name('printExpReturnReport');
        });

        Route::prefix('/print/report')->group(function () {
            Route::get('/weekly/{from?}/{to?}', 'printWeeklyByDates')->name('printWeeklyByDates')->where(['from', 'to'], DateExpression);
            Route::get('/monthly/{from?}/{to?}', 'printMonthlyByDates')->name('printMonthlyByDates')->where(['from', 'to'], DateExpression);
            Route::get('/yearly/{from?}/{to?}', 'printYearlyByDates')->name('printYearlyByDates')->where(['from', 'to'], DateExpression);
            Route::get('/return-report/{from?}/{to?}', 'printExpReportByDates')->name('printExpReturnReportByDates')->where(['from', 'to'], DateExpression);
        });

        Route::get('/weekly', 'weeklyReport')->name('weeklyExpiry');
        Route::get('/monthly', 'monthlyReport')->name('monthlyExpiry');
        Route::get('/yearly', 'yearlyReport')->name('yearlyExpiry');
        Route::get('/return-report', 'returnExpReport')->name('expiryReturnReport');
    });
});

Route::resource('/expiry', ExpiryController::class);

Route::controller(StockController::class)->group(function () {
    Route::prefix('/stock')->group(function () {
        Route::prefix('/print')->group(function () {
            Route::prefix('/available')->group(function () {
                Route::get('/', 'printAvailableStock')->name('availableStockPrint');
                Route::get('/{from?}/{to?}', 'printAvailableStockByDates')->name('printAvailableByDate')->where(['from', 'to'], DateExpression);
            });
            Route::prefix('/expired')->group(function () {
                Route::get('/', 'printExpiredStock')->name('expiredStockPrint');
                Route::get('/{from?}/{to?}', 'printExpiredStockByDates')->name('printExpStockByDate')->where(['from', 'to'], DateExpression);
            });
            Route::prefix('/required')->group(function () {
                Route::get('/', 'printRequiredStock')->name('requiredStockPrint');
                Route::get('/{from?}/{to?}', 'printRequiredStockByDates')->name('printRequiredStockByDate')->where(['from', 'to'], DateExpression);
            });
            Route::prefix('/product-stock')->group(function () {
                Route::get('/', 'printProductStockEntryReport')->name('stock.print');
                Route::get('/{from?}/{to?}', 'printStockByDates')->name('stock.printByDate')->where(['from', 'to'], DateExpression);
            });
        });

        Route::get('/{id}/{date}', 'show')->name('getStockEntry')->whereNumber('id')->where('date', DateExpression);
        Route::put('/{id}/{date}', 'update')->name('updateStock')->whereNumber('id')->where('date', DateExpression);
        Route::delete('/{id}/{date}', 'destroy')->name('deleteStockEntry')->whereNumber('id')->where('date', DateExpression);

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
        Route::get('/{id}/{date}', 'show')->whereNumber('id')->where('date', DateExpression);
        Route::put('/{id}/{date}', 'update')->whereNumber('id')->where('date', DateExpression);
        Route::delete('/{id}/{date}', 'destroy')->whereNumber('id')->where('date', DateExpression);
    });

    Route::prefix('/purchase/print')->group(function () {
        Route::get('/', 'printPurchaseReport')->name('purchase.print');
        Route::get('/{fromDate?}/{toDate?}', 'printPurchaseReportByDates')->name('purchase.printByDate')->where(['fromDate', 'toDate'], DateExpression);
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
        Route::get('/{billNo}/edit/{date}', 'edit')->whereNumber('billNo')->where('date', DateExpression);
        Route::put('/{billNo}/{date}', 'update')->whereNumber('billNo')->where('date', DateExpression);
        Route::delete('/{billNo}/{date}', 'destroy')->whereNumber('billNo')->where('date', DateExpression);
    });
});

Route::resource('/bill', BillController::class)->whereNumber('bill');

Route::fallback(function () {
    return view('partials.pageNotFound');
});
