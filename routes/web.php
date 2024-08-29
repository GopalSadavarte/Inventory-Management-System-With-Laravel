<?php

use App\Http\Controllers\BillController;
use App\Http\Controllers\ExpiryController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\JsonController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PurchaseReturnController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StockController;
use Illuminate\Support\Facades\Route;

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
        Route::prefix('/{id}/{date}')->group(function () {
            Route::get('/', 'show');
            Route::put('/', 'update');
            Route::delete('/', 'destroy');
        })->whereNumber('id');
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
            Route::get('/available', 'printAvailableStock')->name('availableStockPrint');
            Route::get('/expired', 'printExpiredStock')->name('expiredStockPrint');
            Route::get('/required', 'printRequiredStock')->name('requiredStockPrint');
            Route::get('/product-stock', 'printProductStockEntryReport')->name('stock.print');
        });

        Route::prefix('/{id}/{date}')->group(function () {
            Route::get('/', 'show')->name('getStockEntry');
            Route::put('/', 'update')->name('updateStock');
            Route::delete('/', 'destroy')->name('deleteStockEntry');
        })->whereNumber('id');

        Route::get('/available', 'getAvailableStock')->name('getAvailable');
        Route::get('/expired', 'getExpired')->name('getExpired');
        Route::get('/required', 'getRequiredStock')->name('getRequired');
        Route::get('/product-stock', 'getProductStockEntryByDealer')->name('getStockReport');
    });
});

Route::resource('/stock', StockController::class);

Route::controller(PurchaseController::class)->group(function () {

    Route::get('/reports/purchase', 'getPurchaseReport')->name('purchaseReport');

    Route::prefix('/purchase/{id}/{date}')->group(function () {
        Route::get('/', 'show');
        Route::put('/', 'update');
        Route::delete('/', 'destroy');
    })->whereNumber('id');

    Route::prefix('/purchase/print')->group(function () {
        Route::get('/', 'printPurchaseReport')->name('purchase.print');
        Route::get('/{fromDate}/{toDate}', 'printPurchaseReportByDates')->name('purchase.printByDate');
    });
});

Route::resource('/purchase', PurchaseController::class);

Route::controller(PurchaseReturnController::class)->group(function () {
    Route::prefix('/purchase-return/{id}/{date}')->group(function () {
        Route::get('/', 'show');
        Route::put('/', 'update');
        Route::delete('/', 'destroy');
    })->whereNumber('id');
});

Route::resource('/purchaseReturn', PurchaseReturnController::class);

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
        Route::prefix('/{billNo}')->group(function () {
            Route::get('/edit/{date}', 'edit');
            Route::put('/{date}', 'update');
            Route::delete('/{date}', 'destroy');
        })->whereNumber('billNo');
    });
});

Route::resource('/bill', BillController::class);

Route::fallback(function () {
    return view('partials.pageNotFound');
});
