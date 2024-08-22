<?php

namespace App\Http\Controllers;

date_default_timezone_set('Asia/Kolkata');

use App\Models\Bill;
use App\Models\Customer;
use App\Models\CustomerProduct;
use App\Models\Inventory;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

interface BillInterface
{
    public function index();
    public function store(Request $request);
    public function show(string $id);
    public function getProduct(string $id);
    public function edit(string $billNo, string $date);
    public function update(Request $request, string $billNo, string $date);
    public function destroy(string $billNo, string $date);
}

class BillController extends Controller implements BillInterface
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = Customer::all();
        $products = Product::all();
        $lastBillNo = Bill::select('dayWiseBillNumber')
            ->whereRaw('DATE(`created_at`) = ?', date('Y-m-d'))
            ->orderBy('dayWiseBillNumber', 'DESC')
            ->limit(1)
            ->get();
        if ($lastBillNo->count() > 0) {
            $billNo = $lastBillNo[0]->dayWiseBillNumber + 1;
        } else {
            $billNo = 1;
        }
        if ($lastBillNo->count() > 0) {
            $lastBill = Bill::select('bill_amount')
                ->where('dayWiseBillNumber', $lastBillNo[0]->dayWiseBillNumber)
                ->whereRaw('DATE(`created_at`)=?', date('Y-m-d'))
                ->get();

            $lastBillAmount = $lastBill[0]->bill_amount;
        } else {
            $lastBillAmount = 0;
        }

        return view('bill', compact('customers', 'products', 'billNo', 'lastBillAmount'));
    }

    private function assignForPdf(Request $request): array
    {
        return [
            'customerName' => $request->customerName,
            'contact' => $request->contactNumber,
            'counter' => $request->counterNumber,
            'billNumber' => $request->billNumber,
            'productName' => $request->productName,
            'qty' => $request->qty,
            'rate' => $request->rate,
            'mrp' => $request->mrp,
            'pId' => $request->productId,
            'netAmount' => $request->netAmount,
            'total' => $request->total,
            'paid' => $request->paidAmount,
            'return' => $request->returnAmount,
            'discount' => $request->totalDiscount,
        ];
    }

    private function insertIntoCustomer(Request $request)
    {
        if (empty($request->customerId) && !empty($request->customerName)) {
            $customer = new Customer();
            $customer->customer_name = $request->customerName;
            $customer->customer_email = $request->customerEmail;
            $customer->contact = $request->contactNumber;
            $customer->customer_address = $request->addr;
            if (strpos($request->returnAmount, '-') != false) {
                $customer->pending_amt = preg_replace('/^./', '', $request->returnAmount);
            } else {
                $customer->pending_amt = 0;
            }
            $res = $customer->save();
            return Customer::when($res, function (): int {
                $id = Customer::select('id')->orderByDesc('id')->limit(1)->get();
                return $id[0]->id;
            });
        }

        if (!empty($request->customerId)) {
            $pending = 0;
            if (strpos($request->returnAmount, '-') != false && !empty($request->returnAmount) && !empty($request->pendingAmount)) {
                $pending = $request->returnAmount - $request->pendingAmount;
            } else if (strpos($request->returnAmount, '-')) {
                $pending = $request->pendingAmount + intval(preg_replace('/^./', '', $request->returnAmount));
            }
            $res = Customer::where('id', $request->customerId)->update([
                'customer_name' => $request->customerName,
                'customer_email' => $request->customerEmail,
                'contact' => $request->contactNumber,
                'customer_address' => $request->addr,
                'pending_amt' => $pending,
            ]);
            return $request->customerId;
        }
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request);
        if ($request->total == 0 || $request->total == "") {
            return redirect()->route('bill.index');
        }

        if (!empty($request->customerId) || !empty($request->customerName)) {
            $customer = BillController::insertIntoCustomer($request);
        } else {
            $customer = null;
        }

        $id = DB::table('bills')->insertGetId([
            'dayWiseBillNumber' => $request->billNumber,
            'bill_amount' => $request->total,
            'customer_id' => $customer,
            'created_at' => now('Asia/Kolkata'),
            'updated_at' => now('Asia/Kolkata'),
        ]);

        for ($i = 0; $i < count($request->productId); $i++) {
            $res = CustomerProduct::insert([
                "p_id" => $request->productPId[$i],
                "newQuantity" => $request->qty[$i],
                "newRate" => $request->rate[$i],
                "newMRP" => $request->mrp[$i],
                "newDiscount" => $request->discount[$i],
                "bill_no" => $id,
                'invent_id' => $request->inventId[$i],
            ]);

            Inventory::when($res, function () use ($request, $i) {
                $oldQty = Inventory::select('current_quantity')->where('inventory_id', $request->inventId[$i])->get();
                Inventory::where("inventory_id", $request->inventId[$i])->update([
                    'current_quantity' => $oldQty[0]->current_quantity - $request->qty[$i],
                ]);
            });
        }

        if ($request->print == "Yes") {
            $pdf = FacadePdf::loadView('Reports/pdf/makePdf', BillController::assignForPdf($request), [
                'css' => [
                    File::get(public_path('css/style.css')),
                    File::get(public_path('css/bootstrap.css')),
                ],
            ]);
            return $pdf->download();
        } else {
            return redirect()->route('bill.index');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Product::where("product_id", $id)->get();
        if ($data->count() > 0) {
            return response()->json($data);
        } else {
            return response()->json([
                ["error" => "Please! Enter Valid product ID.."],
            ]);
        }
    }

    public function getProduct(string $id)
    {
        $aId = Product::select('id')->where('product_id', $id)->get();
        $productInfo = Product::with('inventory')->where('id', $aId[0]->id)->get();
        if ($productInfo->count() > 0) {
            return response()->json($productInfo);
        } else {
            return response()->json([
                ["error" => "Please! Enter Valid product ID.."],
            ]);
        }
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $billNo, string $date)
    {
        $billInfo = Bill::with(['billProduct', 'billInventory', 'billCustomer'])
            ->where('dayWiseBillNumber', $billNo)
            ->whereRaw('DATE(`bills`.`created_at`)=?', $date)
            ->get();
        if ($billInfo->count() > 0) {
            session()->put(['oldBillInfo' => $billInfo]);
            return response()->json($billInfo);
        } else {
            return response()->json([
                ['error' => 'Invalid ! date or bill number..'],
            ]);
        }

    }

    private function recoverInventory(bool $res = true)
    {
        Inventory::when($res, function () {
            $billInfo = session()->remove('oldBillInfo');
            $products = $billInfo[0]->billProduct;
            $inventory = $billInfo[0]->billInventory;
            for ($i = 0; $i < count($products); $i++) {
                $arr = [];
                $arr[0] = $inventory[$i]->current_quantity;
                $arr[1] = $inventory[$i]->inventory_id;
                $addedQty = $products[$i]->pivot->newQuantity;
                Inventory::where('inventory_id', $arr[1])->update([
                    'current_quantity' => $arr[0] + $addedQty,
                ]);
            }
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $billNo, string $date)
    {
        if ($request->total == 0 || $request->total == "") {
            return redirect()->route('bill.index');
        }

        if (!empty($request->customerId) || !empty($request->customerName)) {
            $customer = BillController::insertIntoCustomer($request);
        } else {
            $customer = null;
        }
        //$customer = (empty($request->customerId) || empty($request->customerName)) ? BillController::insertIntoCustomer($request) : null;
        $created_at = Bill::select('created_at')->where('dayWiseBillNumber', '=', $billNo)
            ->whereRaw('DATE(`bills`.`created_at`)=?', $date)
            ->get();

        $del = Bill::where('dayWiseBillNumber', '=', $billNo)
            ->whereRaw('DATE(`bills`.`created_at`)=?', $date)
            ->delete();

        if ($del) {
            $id = Bill::insertGetId([
                'dayWiseBillNumber' => $billNo,
                'bill_amount' => $request->total,
                'customer_id' => $customer,
                'created_at' => $created_at[0]->created_at,
                'updated_at' => now('Asia/Kolkata'),
            ]);
            BillController::recoverInventory($del);
            for ($i = 0; $i < count($request->productId); $i++) {
                $res = CustomerProduct::insert([
                    "p_id" => $request->productPId[$i],
                    "newQuantity" => $request->qty[$i],
                    "newRate" => $request->rate[$i],
                    "newMRP" => $request->mrp[$i],
                    "newDiscount" => $request->discount[$i],
                    "bill_no" => $id,
                    'invent_id' => $request->inventId[$i],
                ]);

                Inventory::when($res, function () use ($request, $i) {
                    $current = Inventory::select('current_quantity')->where('inventory_id', $request->inventId[$i])->get();
                    Inventory::where('inventory_id', $request->inventId[$i])->update([
                        'current_quantity' => $current[0]->current_quantity - $request->qty[$i],
                    ]);
                });

            }
            if ($request->print == "Yes") {
                $pdf = FacadePdf::loadView('Reports/pdf/makePdf', BillController::assignForPdf($request), [
                    'css' => [
                        file_get_contents(public_path('css/style.css')),
                        file_get_contents(public_path('css/bootstrap.css')),
                    ],
                ]);
                return $pdf->download();
            } else {
                return redirect()->route('bill.index');
            }

        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $billNo, string $date)
    {
        $res = Bill::where('dayWiseBillNumber', '=', $billNo)
            ->whereRaw('DATE(`bills`.`created_at`)=?', $date)
            ->delete();
        BillController::recoverInventory($res);
        return redirect()->route('bill.index');
    }
}
