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
     * This function are run when the bill route are call.In this function
     * The Data are fetch from customers,products and select the last bill entry
     * according to the given SQL query and get the last entry,when last entry are
     *found then new entry number are generated incrementing the old value and it the
     *view file and also send the exact previous bill amount of the bill.
     */
    public function index()
    {
        $customers = Customer::all();
        $products = Product::all();

        //this is for selecting the last bill entry.
        $lastBillNo = Bill::select('dayWiseBillNumber')
            ->whereRaw('DATE(`created_at`) = ?', date('Y-m-d'))
            ->orderBy('dayWiseBillNumber', 'DESC')
            ->limit(1)
            ->get();

        //if the entry found then add one to bill number to generate new number.
        if ($lastBillNo->count() > 0) {
            $billNo = $lastBillNo[0]->dayWiseBillNumber + 1;
        } else {
            $billNo = 1;
        }

        //this is for returning the exact previous bill amount for displaying it to the new bill.
        if ($lastBillNo->count() > 0) {
            $lastBill = Bill::select('bill_amount')
                ->where('dayWiseBillNumber', $lastBillNo[0]->dayWiseBillNumber)
                ->whereRaw('DATE(`created_at`)=?', date('Y-m-d'))
                ->get();

            $lastBillAmount = $lastBill[0]->bill_amount;
        } else {
            $lastBillAmount = 0;
        }

        //this return a view bill
        return view('bill', compact('customers', 'products', 'billNo', 'lastBillAmount'));
    }

    /**
     * This function create an array of the form request send for creating a pdf.
     */
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

    /**
     *This function is insert a new or update the existing customer data according to the request.
     */
    private function insertIntoCustomer(Request $request)
    {
        if (empty($request->customerId) && !empty($request->customerName)) {
            $customer = new Customer();
            $customer->customer_name = $request->customerName;
            $customer->customer_email = $request->customerEmail;
            $customer->contact = $request->contactNumber;
            $customer->customer_address = $request->addr;

            /*If the given return amount is in minus then it remove it and save it on the name of this customer ,
            if customer is come in next time,then this amount will show.
             */
            if (strpos($request->returnAmount, '-') != false) {
                $customer->pending_amt = preg_replace('/^./', '', $request->returnAmount);
            } else {
                $customer->pending_amt = 0;
            }
            $res = $customer->save();

            /*
            this return statement return a function that return a integer value if the above query are executed properly.
             */
            return Customer::when($res, function (): int {
                $id = Customer::select('id')->orderByDesc('id')->limit(1)->get();
                return $id[0]->id;
            });
        }

        //This statement are executed when a customer are already exist.
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
        //if the user can send store request with no data then it redirect to there route.
        if ($request->total == 0 || $request->total == "") {
            return redirect()->route('bill.index');
        }

        //This statement checks if the customer are choices by the user then execute it,otherwise set null to it.
        if (!empty($request->customerId) || !empty($request->customerName)) {
            $customer = $this->insertIntoCustomer($request);
        } else {
            $customer = null;
        }

        //this query are insert a new record and returns the primary key value.
        $id = DB::table('bills')->insertGetId([
            'dayWiseBillNumber' => $request->billNumber,
            'bill_amount' => $request->total,
            'customer_id' => $customer,
            'created_at' => now('Asia/Kolkata'),
            'updated_at' => now('Asia/Kolkata'),
        ]);

        //This loop are execute for each bill entry to store it into DB.
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

            //This statement are continuously updates the stock of the current product in the inventories table.
            Inventory::when($res, function () use ($request, $i) {
                //this is for getting the old stock
                $oldQty = Inventory::select('current_quantity')->where('inventory_id', $request->inventId[$i])->get();
                //this for updating old stock by removing the sale stock.
                Inventory::where("inventory_id", $request->inventId[$i])->update([
                    'current_quantity' => $oldQty[0]->current_quantity - $request->qty[$i],
                ]);
            });
        }

        //this statement are execute to create the pdf by View file.
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
     *This function send the information of particular product
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

    /**
     * This function is returns the information of particular product by its current stock.
     */
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
     * This function is return the bill information of the particular bill number and date for the purpose of updating the bill.
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

    /**
     * This function is only recover the old stock if the bill are
     * updated or deleted then it recover it using the information,which are store in the session variable.
     * (In above function the information are put into the session and this use in this function .)
     */
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
     * This function can updates the bill info and restore and remodify the product stock as per changes.
     */
    public function update(Request $request, string $billNo, string $date)
    {
        if ($request->total == 0 || $request->total == "") {
            return redirect()->route('bill.index');
        }

        if (!empty($request->customerId) || !empty($request->customerName)) {
            $customer = $this->insertIntoCustomer($request);
        } else {
            $customer = null;
        }

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
            //recovering done here
            $this->recoverInventory($del);
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
                $pdf = FacadePdf::loadView('Reports/pdf/makePdf', $this->assignForPdf($request), [
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
     * This function are remove the specified bill information from the DB
     */
    public function destroy(string $billNo, string $date)
    {
        //First remove the bill from DB
        $res = Bill::where('dayWiseBillNumber', '=', $billNo)
            ->whereRaw('DATE(`bills`.`created_at`)=?', $date)
            ->delete();
        //Recover the information of the deleted bill,such as stock info.
        $this->recoverInventory($res);
        return redirect()->route('bill.index');
    }
}
