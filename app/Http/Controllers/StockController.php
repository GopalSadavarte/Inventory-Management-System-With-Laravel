<?php

namespace App\Http\Controllers;

date_default_timezone_set('Asia/Kolkata');

use App\Http\Controllers\PurchaseController;
use App\Models\Dealer;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Stock;
use Barryvdh\DomPDF\Facade\Pdf;
use DateTime;
use DateTimeZone;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;

interface StockInterface
{
    public function index();
    public function store(Request $request);
    public function show(string $id, string $date);
    public function update(Request $request, string $id, string $date);
    public function destroy(string $id, string $date);
    public function getAvailableStock(string $data);
    public function getRequiredStock(string $data);
    public function getExpired(string $data);
    public function printAvailableStock();
    public function printRequiredStock();
    public function printExpiredStock();
    public function getProductStockEntryByDealer();
    public function printProductStockEntryReport();
}

class StockController extends Controller implements StockInterface
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all();
        $dealers = Dealer::all();
        $lastEntry = Stock::select('stock_id')
            ->whereRaw('DATE(`created_at`)=?', date('Y-m-d'))
            ->orderBy('stock_id', 'DESC')
            ->limit(1)
            ->get();
        if ($lastEntry->count() == 1) {
            $lastEntry = $lastEntry[0]->stock_id + 1;
        } else {
            $lastEntry = 1;
        }
        return view('subSections/stockEntry', compact('products', 'lastEntry', 'dealers'));
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (empty($request->stockAmt) || $request->stockAmt == '') {
            return redirect()->route('stock.index');
        }
        $dealerId = (!empty($request->dealerId) || !empty($request->dealerName)) ? PurchaseController::insertIntoDealer($request) : null;
        $stock = new Stock();
        $stock->dealer_id = $dealerId;
        $stock->stock_id = $request->entryNumber;
        $res = $stock->save();

        $stock_entry_id = Stock::when($res, function () {
            $id = Stock::select('id')->orderByDesc('id')->limit(1)->get();
            return $id[0]->id;
        });

        for ($i = 0; $i < count($request->pId); $i++) {
            if (!empty($request->pId[$i])) {

                $product_stock = new ProductStock();
                $product_stock->stock_entry_no = $stock_entry_id;
                $product_stock->stock_product_id = $request->pId[$i];
                $product_stock->addedQuantity = $request->qty[$i];
                $product_stock->purchase_rate = $request->purchase_rate[$i];
                $product_stock->sale_rate = $request->rate[$i];
                $product_stock->MRP = $request->mrp[$i];
                $product_stock->GST = $request->gst[$i];
                $product_stock->save();

                $invent = Inventory::where('product_id', $request->pId[$i])->where('sale_rate', $request->rate[$i])->where('purchase_rate', $request->purchase_rate[$i])->limit(1)->get();
                if ($invent->count() == 1) {
                    $c = $invent[0]->current_quantity;
                    $n = $request->qty[$i];
                    $newStock = $c + $n;

                    Inventory::where('product_id', $request->pId[$i])->where('sale_rate', $request->rate[$i])->where('purchase_rate', $request->purchase_rate[$i])->update([
                        'current_quantity' => $newStock,
                    ]);
                } else {
                    $add = new Inventory();
                    $add->product_id = $request->pId[$i];
                    $add->sale_rate = $request->rate[$i];
                    $add->purchase_rate = $request->purchase_rate[$i];
                    $add->MRP = $request->mrp[$i];
                    $add->current_quantity = $request->qty[$i];
                    $add->GST = $request->gst[$i];
                    $add->save();
                }
            }
        }
        return redirect()->route('stock.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, string $date)
    {
        $get = Stock::with('product')->with('dealer')->whereRaw('DATE(`stocks`.`created_at`)=?', $date)->where('stock_id', $id)->get();
        if ($get->count() > 0) {
            session()->put(['stockInfo' => $get]);
            return response()->json($get);
        } else {
            return response()->json([['error' => 'Invalid ! Entry number or date..!']]);
        }
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id, string $date)
    {
        if (empty($request->stockAmt) || $request->stockAmt == '') {
            return redirect()->route('stock.index');
        }
        $create_at = Stock::select('created_at')->whereRaw('DATE(`created_at`)=?', $date)->where('stock_id', $id)->get();
        $res = Stock::whereRaw('DATE(`created_at`)=?', $date)->where('stock_id', $id)->delete();
        if ($res) {
            $dealer = (!empty($request->dealerId) || !empty($request->dealerName)) ? PurchaseController::insertIntoDealer($request) : null;

            $stock = new Stock();
            $stock->stock_id = $id;
            $stock->dealer_id = $dealer;
            $stock->created_at = $create_at[0]->created_at;
            $stock->updated_at = NOW('Asia/Kolkata');
            $res1 = $stock->save();

            $stock_entry_id = Stock::when($res1, function () {
                $id = Stock::select('id')->orderByDesc('id')->limit(1)->get();
                return $id[0]->id;
            });

            $oldInfo = session()->remove('stockInfo');
            $products = collect($oldInfo[0]->product);
            $products->each(function ($product) {
                $current = Inventory::where('product_id', $product->id)->where('sale_rate', $product->pivot->sale_rate)->where('purchase_rate', $product->pivot->purchase_rate)->get();
                Inventory::where('product_id', $product->id)->where('sale_rate', $product->pivot->sale_rate)->where('purchase_rate', $product->pivot->purchase_rate)->update([
                    'current_quantity' => $current[0]->current_quantity - $product->pivot->addedQuantity,
                ]);
            });

            for ($i = 0; $i < count($request->pId); $i++) {
                if (!empty($request->pId[$i])) {

                    $product_stock = new ProductStock();
                    $product_stock->stock_entry_no = $stock_entry_id;
                    $product_stock->stock_product_id = $request->pId[$i];
                    $product_stock->addedQuantity = $request->qty[$i];
                    $product_stock->purchase_rate = $request->purchase_rate[$i];
                    $product_stock->sale_rate = $request->rate[$i];
                    $product_stock->MRP = $request->mrp[$i];
                    $product_stock->GST = $request->gst[$i];
                    $product_stock->save();

                    $invent = Inventory::where('product_id', $request->pId[$i])->where('sale_rate', $request->rate[$i])->where('purchase_rate', $request->purchase_rate[$i])->limit(1)->get();
                    if ($invent->count() == 1) {
                        $c = $invent[0]->current_quantity;
                        $n = $request->qty[$i];
                        $newStock = $c + $n;

                        Inventory::where('product_id', $request->pId[$i])->where('sale_rate', $request->rate[$i])->where('purchase_rate', $request->purchase_rate)->update([
                            'current_quantity' => $newStock,
                        ]);
                    } else {
                        $add = new Inventory();
                        $add->product_id = $request->pId[$i];
                        $add->sale_rate = $request->rate[$i];
                        $add->purchase_rate = $request->purchase_rate[$i];
                        $add->MRP = $request->mrp[$i];
                        $add->current_quantity = $request->qty[$i];
                        $add->GST = $request->gst[$i];
                        $add->save();
                    }
                }
            }

            return redirect()->route('stock.index');
        }
        return redirect()->route('stock.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, string $date)
    {
        $res = Stock::whereRaw('DATE(`created_at`)=?', $date)->where('stock_id', $id)->delete();
        $oldInfo = session()->remove('stockInfo');
        $products = collect($oldInfo[0]->product);
        $products->each(function ($product) {
            $current = Inventory::where('product_id', $product->id)->where('sale_rate', $product->pivot->sale_rate)->get();
            Inventory::where('product_id', $product->id)->where('sale_rate', $product->pivot->sale_rate)->update([
                'current_quantity' => $current[0]->current_quantity - $product->pivot->addedQuantity,
            ]);
        });
        if ($res) {
            return redirect()->route('stock.index');
        }
    }

    public function getAvailableStock(string $data = null)
    {
        $products = Inventory::selectCurrentQtyWithPId()
            ->havingRaw('SUM(`current_quantity`)>?', [0])
            ->groupBy('product_id')
            ->get();

        if ($data != null) {
            return $products;
        }
        // return $products;
        return view('Reports.stock.availableStock', compact('products'));
    }

    public function getRequiredStock(string $data = null)
    {
        $products = Inventory::selectCurrentQtyWithPId()
            ->havingRaw('SUM(`current_quantity`)<=?', [0])
            ->groupBy('product_id')
            ->get();
        if ($data != null) {
            return $products;
        }

        return view('Reports.stock.demandedStock', compact('products'));
    }

    public function getExpired(string $data = null)
    {
        $products = Inventory::selectRaw('SUM(`current_quantity`) as CQTY,product_id,EXP')
            ->withWhereHas('product', function ($query) {
                $query->with('group', 'subGroup');
            })->where('EXP', '<=', date('Y-m-d'))
            ->havingRaw('SUM(`current_quantity`)>?', [0])
            ->groupBy('product_id', 'EXP')
            ->get();
        if ($data != null) {
            return $products;
        }
        return view('Reports.stock.expiredStock', compact('products'));
    }

    public function printAvailableStock()
    {
        $products = StockController::getAvailableStock('get');
        return StockController::makePdf($products, 'Reports/pdf/stockReport', 'Available Stock Report');
    }
    public function printRequiredStock()
    {
        $products = StockController::getRequiredStock('get');
        return StockController::makePdf($products, 'Reports/pdf/stockReport', 'Required Stock Report');
    }
    public function printExpiredStock()
    {
        $products = StockController::getExpired('get');
        return StockController::makePdf($products, 'Reports/pdf/stockReport', 'Expired Stock Report');
    }

    public function getProductStockEntryByDealer()
    {
        $date = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
        $date = $date->modify('-10 day')->format('Y-m-d');
        $products = File::get(public_path('./json/stock.json'));
        $stocks = Stock::withProductAndDealer()->whereRaw('DATE(`created_at`)BETWEEN ? AND ?', [$date, date('Y-m-d')])->get();
        $products = ($products == '') ? Json::decode($stocks, false) : Json::decode(array_merge(Json::decode($products), Json::decode($stocks)), false);
        return View::make('Reports.stock.stockEntryReport', compact('products'));
    }

    public function printProductStockEntryReport()
    {
        $date = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
        $date = $date->modify('-10 day')->format('Y-m-d');
        $products = File::get(public_path('./json/stock.json'));
        $stocks = Stock::withProductAndDealer()->whereRaw('DATE(`created_at`)BETWEEN ? AND ?', [$date, date('Y-m-d')])->get();
        $products = ($products == '') ? Json::decode($stocks, false) : Json::decode(array_merge(Json::decode($products), Json::decode($stocks)), false);
        return StockController::makePdf($products, 'Reports/pdf/stockReportByDealer');
    }

    public function printStockReportByDates(string $from, string $to)
    {
        $products = Stock::withProductAndDealer()->get();
        return StockController::makePdf($products, 'Reports/pdf/stockReportByDealer');
    }

    private function makePdf(object | array $products, string $view, string $report = null)
    {
        return Pdf::loadView($view, compact('products', 'report'), [
            'css' => [
                File::get(public_path('css/style.css')),
                File::get(public_path('css/bootstrap.css')),
            ],
        ])->setPaper('A4', 'landscape')->download();
    }
}
