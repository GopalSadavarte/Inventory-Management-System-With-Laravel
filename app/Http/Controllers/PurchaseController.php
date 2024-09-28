<?php

namespace App\Http\Controllers;

date_default_timezone_set('Asia/Kolkata');
use App\Models\Dealer;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseEntry;
use Barryvdh\DomPDF\Facade\Pdf;
use DateTime;
use DateTimeZone;
use Exception;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

interface PurchaseInterface
{
    public function index();
    public function store(Request $request);
    public function show(string $entryNumber, string $date);
    public function update(Request $request, string $id, string $date);
    public function destroy(string $id, string $date);
}
class PurchaseController extends Controller implements PurchaseInterface
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all();
        $dealers = Dealer::all();
        $lastEntry = Purchase::select('purchase_entry_id')
            ->whereRaw('DATE(`created_at`)=?', date('Y-m-d'))
            ->orderByDesc('purchase_entry_id')
            ->limit(1)
            ->get();
        $newEntry = ($lastEntry->count() == 1) ? $lastEntry[0]->purchase_entry_id + 1 : 1;
        return view('subSections.purchaseEntry', compact('products', 'dealers', 'newEntry'));
    }
    /**store or update dealer into database*/
    public static function insertIntoDealer(Request $request)
    {
        if (empty($request->dealerId) && !empty($request->dealerName)) {
            $dealer = new Dealer();
            $dealer->dealer_name = $request->dealerName;
            $dealer->email = $request->Email;
            $dealer->contact = $request->contactNumber;
            $dealer->GST_no = $request->GSTNumber;
            $res = $dealer->save();

            return Dealer::when($res, function () {
                $Id = Dealer::select('id')->orderByDesc('id')->limit(1)->get();
                return $Id[0]->id;
            });
        }

        if (!empty($request->dealerId)) {
            $dealer = Dealer::where('id', $request->dealerId)->update([
                'dealer_name' => $request->dealerName,
                'email' => $request->Email,
                'contact' => $request->contactNumber,
                'GST_no' => $request->GSTNumber,
            ]);
            return $request->dealerId;
        }
    }
    public function store(Request $request)
    {
        if (empty($request->stockAmt)) {
            return redirect()->route('purchase.index');
        }

        $dealer = (!empty($request->dealerId) || !empty($request->dealerName)) ? PurchaseController::insertIntoDealer($request) : null;

        $id = Purchase::insertGetId([
            'purchase_entry_id' => $request->entryNumber,
            'dealer_id' => $dealer,
            'created_at' => NOW('Asia/Kolkata'),
            'updated_at' => NOW('Asia/Kolkata'),
        ]);

        for ($i = 0; $i < count($request->pId); $i++) {
            if (!empty($request->pId[$i])) {
                try {
                    $insert = new PurchaseEntry();
                    $insert->purchase_entry_id = $id;
                    $insert->purchase_product_id = $request->pId[$i];
                    $insert->addedQuantity = $request->qty[$i];
                    $insert->purchase_rate = $request->purchase_rate[$i];
                    $insert->sale_rate = $request->rate[$i];
                    $insert->MRP = $request->mrp[$i];
                    $insert->GST = $request->gst[$i];
                    $insert->productMFD = $request->mfdDate[$i];
                    $insert->productEXP = $request->expDate[$i];
                    $result = $insert->save();
                } catch (Exception $exception) {
                    Purchase::find($id)->delete();
                    return redirect()->route('purchase.index')->with('exception', 'Some Data is missing in entry,try again!');
                }
                Inventory::when($result, function () use ($request, $i) {
                    $invent = Inventory::where('product_id', $request->pId[$i])->where('EXP', $request->expDate[$i])->limit(1)->get();
                    if ($invent->count() == 1) {
                        $c = $invent[0]->current_quantity;
                        $n = $request->qty[$i];
                        $newStock = $c + $n;

                        Inventory::where('product_id', $request->pId[$i])->where('EXP', $request->expDate[$i])->update([
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
                        $add->MFD = $request->mfdDate[$i];
                        $add->EXP = $request->expDate[$i];
                        $add->save();
                    }
                });
            }

        }
        return redirect()->route('purchase.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $entryNumber, string $date)
    {
        $res = Purchase::with('product')->with('dealer')->whereRaw('DATE(`purchases`.`created_at`)=?', $date)->where('purchase_entry_id', $entryNumber)->get();
        if ($res->count() > 0) {
            session()->put(['purchaseInfo' => $res]);
            return response()->json($res);
        } else {
            return response()->json([['error' => 'Invalid ! Entry Number or Date!']]);
        }
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id, string $date)
    {
        if (empty($request->stockAmt) || $request->stockAmt == '') {
            return redirect()->route('purchase.index');
        }
        $dealer = (!empty($request->dealerId) || !empty($request->dealerName)) ? PurchaseController::insertIntoDealer($request) : null;

        $create = Purchase::select('created_at')->where('purchase_entry_id', $id)->whereRaw('DATE(`purchases`.`created_at`)=?', $date)->get();
        $res = Purchase::where('purchase_entry_id', $id)->whereRaw('DATE(`purchases`.`created_at`)=?', $date)->delete();

        Purchase::when($res, function () use ($request, $create, $dealer, $id) {
            $new = new Purchase();
            $new->purchase_entry_id = $id;
            $new->dealer_id = $dealer;
            $new->created_at = $create[0]->created_at;
            $new->updated_at = NOW('Asia/Kolkata');
            $res = $new->save();

            $oldInfo = session()->remove('purchaseInfo');
            $products = collect($oldInfo[0]->product);
            $products->each(function ($product) {
                $current = Inventory::where('product_id', $product->id)->where('EXP', $product->pivot->productEXP)->get();
                Inventory::where('product_id', $product->id)->where('EXP', $product->pivot->productEXP)->update([
                    'current_quantity' => $current[0]->current_quantity - $product->pivot->addedQuantity,
                ]);
            });

            $id = Purchase::when($res, function () {
                $id = Purchase::select('id')->whereRaw('DATE(`created_at`)=?', date('Y-m-d'))->orderBy('id', 'DESC')->get();
                return $id[0]->id;
            });
            for ($i = 0; $i < count($request->pId); $i++) {
                if (!empty($request->pId[$i])) {
                    $insert = new PurchaseEntry();
                    $insert->purchase_entry_id = $id;
                    $insert->purchase_product_id = $request->pId[$i];
                    $insert->addedQuantity = $request->qty[$i];
                    $insert->purchase_rate = $request->purchase_rate[$i];
                    $insert->sale_rate = $request->rate[$i];
                    $insert->MRP = $request->mrp[$i];
                    $insert->GST = $request->gst[$i];
                    $insert->productMFD = $request->mfdDate[$i];
                    $insert->productEXP = $request->expDate[$i];
                    $insert->save();

                    $invent = Inventory::where('product_id', $request->pId[$i])->where('EXP', $request->expDate[$i])->limit(1)->get();
                    if ($invent->count() == 1) {
                        $c = $invent[0]->current_quantity;
                        $n = $request->qty[$i];
                        $newStock = $c + $n;

                        Inventory::where('product_id', $request->pId[$i])->where('EXP', $request->expDate[$i])->update([
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
                        $add->MFD = $request->mfdDate[$i];
                        $add->EXP = $request->expDate[$i];
                        $add->save();
                    }
                }

            }

        });
        return redirect()->route('purchase.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, string $date)
    {
        $res = Purchase::where('purchase_entry_id', $id)->whereRaw('DATE(`created_at`)=?', $date)->delete();
        Purchase::when($res, function () {
            $oldInfo = session()->remove('purchaseInfo');
            $products = collect($oldInfo[0]->product);
            $products->each(function ($product) {
                $current = Inventory::where('product_id', $product->id)->where('EXP', $product->pivot->productEXP)->get();
                Inventory::where('product_id', $product->id)->where('EXP', $product->pivot->productEXP)->update([
                    'current_quantity' => $current[0]->current_quantity - $product->pivot->addedQuantity,
                ]);
            });
        });
        return redirect()->route('purchase.index');
    }

    protected function verifyingData(mixed $products, mixed $purchaseEntries, mixed $purchaseEntriesWithoutDealer)
    {
        if (!empty($products) && $purchaseEntries->count() > 0 && $purchaseEntriesWithoutDealer->count() > 0) {
            $products = array_merge(Json::decode($products), Json::decode($purchaseEntries), Json::decode($purchaseEntriesWithoutDealer));
        } else if (empty($products) && $purchaseEntries->count() > 0 && $purchaseEntriesWithoutDealer->count() > 0) {
            $products = array_merge(Json::decode($purchaseEntries), Json::decode($purchaseEntriesWithoutDealer));
        } else if (!empty($products) && $purchaseEntries->count() == 0 && $purchaseEntriesWithoutDealer->count() > 0) {
            $products = array_merge(Json::decode($products), Json::decode($purchaseEntriesWithoutDealer));
        } else if (!empty($products) && $purchaseEntries->count() > 0 && $purchaseEntriesWithoutDealer->count() == 0) {
            $products = array_merge(Json::decode($products), Json::decode($purchaseEntries));
        } else if (!empty($products) && $purchaseEntries->count() == 0 && $purchaseEntriesWithoutDealer->count() == 0) {
            $products = Json::decode($products);
        } else if (empty($products) && $purchaseEntries->count() > 0 && $purchaseEntriesWithoutDealer->count() == 0) {
            $products = Json::decode($purchaseEntries);
        } else if (empty($products) && $purchaseEntries->count() == 0 && $purchaseEntriesWithoutDealer->count() > 0) {
            $products = Json::decode($purchaseEntriesWithoutDealer);
        } else {
            $products = null;
        }
        return $products;
    }

    protected function getDataFromFile(): array
    {
        $d = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
        $d->modify('-10 day');
        $date = $d->format('Y-m-d');

        $products = File::get(public_path('json/purchase.json'));

        $allProducts = Product::all();

        $purchaseEntries = Dealer::withWhereHas('purchaseEntry', function ($query) use ($date) {
            $query->whereRaw('DATE(`purchases`.`created_at`) BETWEEN ? AND ?', [$date, date('Y-m-d')]);
        })->get();

        $purchaseEntriesWithoutDealer = Purchase::with('product')->whereRaw('DATE(`purchases`.`created_at`) BETWEEN ? AND ?', [$date, date('Y-m-d')])->where('dealer_id', null)->get();

        $products = $this->verifyingData($products, $purchaseEntries, $purchaseEntriesWithoutDealer);
        if ($products != null) {
            $products = Json::encode($products);
            $products = Json::decode($products, false);
        }

        return [$products, $allProducts];
    }

    protected function getDataFromFileByDates(string $from, string $to): array
    {
        $products = File::get(public_path('json/purchase.json'));
        if (!empty($products)) {
            $products = Json::decode($products, false);
            foreach ($products as $product) {
                if (isset($product->purchase_entry)) {
                    foreach ($product->purchase_entry as $purchase) {
                        $date = substr($purchase->created_at, 0, 10);
                        if (!($date >= $from) && !($date <= $to)) {
                            unset($purchase);
                        }
                    }
                    if ($product->purchase_entry->count() <= 0) {
                        unset($product);
                    }
                } else {
                    $date = substr($product->created_at, 0, 10);
                    if (!($date >= $from) && !($date <= $to)) {
                        unset($product);
                    }
                }
            }
            $products = Json::encode($products);
        }
        $purchaseEntries = Dealer::withWhereHas('purchaseEntry', function ($query) use ($from, $to) {
            $query->whereRaw('DATE(`purchases`.`created_at`) BETWEEN ? AND ?', [$from, $to]);
        })->get();
        $withoutDealer = Purchase::with('product')->whereRaw('DATE(`purchases`.`created_at`) BETWEEN ? AND ?', [$from, $to])->where('dealer_id', null)->get();
        $allProducts = Product::all();
        $products = $this->verifyingData($products, $purchaseEntries, $withoutDealer);
        if ($products != null) {
            $products = Json::encode($products);
            $products = Json::decode($products, false);
        }

        return [$products, $allProducts];
    }

    public function getPurchaseReport()
    {
        $d = $this->getDataFromFile();
        $products = $d[0];
        $allProducts = $d[1];
        return view('Reports/purchase/purchaseReport', compact('products', 'allProducts'));
    }

    public function printPurchaseReport()
    {
        $data = $this->getDataFromFile();
        $products = $data[0];
        $allProducts = $data[1];
        // return $products;
        return Pdf::loadView('Reports/pdf/purchaseReportPrint', compact('products', 'allProducts'), [
            'css' => [
                File::get(public_path('./css/style.css')),
                File::get(public_path('./css/bootstrap.css')),
            ],
        ])->setPaper('A4', 'landscape')->download();
    }

    public function printPurchaseReportByDates(string $from, string $to)
    {
        $data = $this->getDataFromFileByDates($from, $to);
        $products = $data[0];
        $allProducts = $data[1];
        if (count($products) > 0) {
            return Pdf::loadView('Reports/pdf/purchaseReportPrint', compact('products', 'allProducts'), [
                'css' => [
                    File::get(public_path('./css/style.css')),
                    File::get(public_path('./css/bootstrap.css')),
                ],
            ])->setPaper('A4', 'landscape')->download();
        } else {
            return redirect()->route('purchaseReport')->with('dataException', '<b>Alert! </b>Data Not Found,So PDF cannot Generated!!');
        }
    }
}
