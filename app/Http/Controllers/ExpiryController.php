<?php

namespace App\Http\Controllers;

use App\Models\Dealer;
use App\Models\Expiry;
use App\Models\ExpiryEntry;
use App\Models\Inventory;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

interface ExpiryInterface
{
    public function index();
    public function store(Request $request);
    public function show(string $id, string $date);
    public function update(Request $request, string $id, string $date);
    public function destroy(string $id, string $date);
    public function weeklyReport();
    public function monthlyReport();
    public function yearlyReport();
    public function returnExpReport();
}
class ExpiryController extends Controller implements ExpiryInterface
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dealers = Dealer::all();
        $products = Product::all();

        $id = Expiry::select('expiry_entry_id')->orderByDesc('expiry_entry_id')->limit(1)->get();
        $newEntry = ($id->count() == 1) ? $id[0]->expiry_entry_id + 1 : 1;
        return view('Reports.expiry.expiryEntry', compact('dealers', 'products', 'newEntry'));
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (empty($request->stockAmt) || $request->stockAmt == '') {
            return redirect()->route('expiry.index');
        }

        $dealer = (!empty($request->dealerId) || !empty($request->dealerName)) ? PurchaseController::insertIntoDealer($request) : null;
        $id = Expiry::insertGetId([
            "expiry_entry_id" => $request->entryNumber,
            "dealer_id" => $dealer,
            "created_at" => NOW('Asia/Kolkata'),
            "updated_at" => NOW('Asia/Kolkata'),
        ]);
        Expiry::when($id, function () use ($request, $id) {
            for ($i = 0; $i < count($request->pId); $i++) {
                try {
                    $res = ExpiryEntry::create([
                        "expiry_entry_no" => $id,
                        "product_id" => $request->pId[$i],
                        "returnQuantity" => $request->qty[$i],
                        "rate" => $request->rate[$i],
                        "MRP" => $request->mrp[$i],
                        "GST" => $request->gst[$i],
                        "expiry_date" => $request->expDate[$i],
                    ]);
                } catch (Exception $e) {
                    return redirect()->route('expiry.index')->with('exception', '<strong>Invalid!</strong> Something missing in the entry,try to complete it!');
                }
                Inventory::when($res, function () use ($request, $i): void {
                    $current = Inventory::select('current_quantity')->where('product_id', $request->pId[$i])->whereRaw('DATE(`EXP`)=?', $request->expDate[$i])->get();
                    Inventory::when($current->count() == 1, function () use ($request, $i, $current): void {
                        Inventory::where('product_id', $request->pId[$i])->whereRaw('DATE(`EXP`)=?', $request->expDate[$i])->update([
                            'current_quantity' => $current[0]->current_quantity - $request->qty[$i],
                        ]);
                    });
                });
            }
        });
        return redirect()->route('expiry.index');
    }
    protected function recoverOldExp(bool $del)
    {
        Inventory::when($del, function () {
            $old = session()->remove('oldExpiryInfo');
            foreach ($old[0]->product as $product) {
                $current = Inventory::select('current_quantity')->where('product_id', $product->id)->whereRaw('DATE(`EXP`)=?', $product->pivot->expiry_date)->get();
                Inventory::when($current->count() == 1, function () use ($current, $product) {
                    Inventory::where('product_id', $product->id)->whereRaw('DATE(`EXP`)=?', $product->pivot->exp_date)->update([
                        'current_quantity' => $current[0]->current_quantity + $product->pivot->returnQuantity,
                    ]);
                });
            }
        });
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id, string $date)
    {
        $data = Expiry::with('product')->with('dealer')->where('expiry_entry_id', $id)->whereRaw('DATE(`created_at`)=?', $date)->get();
        if ($data->count() > 0) {
            session()->put(['oldExpiryInfo' => $data]);
            return response()->json($data);
        } else {
            return response()->json([['error' => 'Invalid! Entry no or date!']]);
        }
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id, string $date)
    {
        if (empty($request->stockAmt) || $request->stockAmt == '') {
            return redirect()->route('expiry.index');
        }

        $dealer = (!empty($request->dealerId) || !empty($request->dealerName)) ? PurchaseController::insertIntoDealer($request) : null;

        $created = Expiry::select('created_at')->where('expiry_entry_id', $id)->whereRaw('DATE(`created_at`)=?', $date)->get();
        $del = Expiry::where('expiry_entry_id', $id)->whereRaw('DATE(`created_at`)=?', $date)->delete();
        ExpiryController::recoverOldExp($del);
        Expiry::when($del, function () use ($request, $id, $dealer, $created) {
            $expId = Expiry::insertGetId([
                "expiry_entry_id" => $id,
                "dealer_id" => $dealer,
                "created_at" => $created[0]->created_at,
                "updated_at" => NOW('Asia/Kolkata'),
            ]);
            Expiry::when($expId, function () use ($request, $expId): void {
                for ($i = 0; $i < count($request->pId); $i++) {
                    $newExp = new ExpiryEntry();
                    $newExp->expiry_entry_no = $expId;
                    $newExp->product_id = $request->pId[$i];
                    $newExp->returnQuantity = $request->qty[$i];
                    $newExp->rate = $request->rate[$i];
                    $newExp->MRP = $request->mrp[$i];
                    $newExp->GST = $request->gst[$i];
                    $newExp->expiry_date = $request->expDate[$i];
                    $res = $newExp->save();
                    Inventory::when($res, function () use ($request, $i): void {
                        $current = Inventory::select('current_quantity')->where('product_id', $request->pId[$i])->whereRaw('DATE(`EXP`)=?', $request->expDate[$i])->get();
                        Inventory::when($current->count() == 1, function () use ($request, $i, $current): void {
                            Inventory::where('product_id', $request->pId[$i])->whereRaw('DATE(`EXP`)=?', $request->expDate[$i])->update([
                                'current_quantity' => $current[0]->current_quantity - $request->qty[$i],
                            ]);
                        });
                    });
                }
            });

        });
        return redirect()->route('expiry.index');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, string $date)
    {
        $del = Expiry::where('expiry_entry_id', $id)->whereRaw('DATE(`created_at`)=?', $date)->delete();
        ExpiryController::recoverOldExp($del);
        return redirect()->route('expiry.index');
    }
    protected function printExpReport()
    {
        //
    }

    public function weeklyReport()
    {
        $products = Inventory::getFromInventory()->get();
        // return $products;
        return View::make('Reports.expiry.weeklyReport', compact('products'));
    }

    public function monthlyReport()
    {
        $products = Inventory::getFromInventory()->get();
        return view('Reports.expiry.monthlyReport', compact('products'));
    }

    public function yearlyReport()
    {
        $products = Inventory::getFromInventory()->get();
        return view('Reports.expiry.yearlyReport', compact('products'));
    }

    public function returnExpReport()
    {
        $products = Expiry::with('product', 'dealer')->get();

        return view('Reports.expiry.returnExpReport', compact('products'));
    }
}
