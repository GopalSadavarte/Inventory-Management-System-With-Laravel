<?php

namespace App\Http\Controllers;
date_default_timezone_set('Asia/Kolkata');
use App\Models\Dealer;
use App\Models\Expiry;
use App\Models\ExpiryEntry;
use App\Models\Inventory;
use App\Models\Product;
use DateTime;
use DateTimeZone;
use Exception;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
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
     *This function are return the view file expiry entry and the entry number by selecting using query.
     */
    public function index()
    {
        $dealers = Dealer::all();
        $products = Product::all();

        //this query select the last entry number by the current date
        $id = Expiry::select('expiry_entry_id')
            ->whereRaw('DATE(`created_at`)=?', date('Y-m-d'))
            ->orderByDesc('expiry_entry_id')
            ->limit(1)
            ->get();

        //if the entry are found increment and assign to the variable otherwise set it to the 1.
        $newEntry = ($id->count() == 1) ? $id[0]->expiry_entry_id + 1 : 1;
        return view('Reports.expiry.expiryEntry', compact('dealers', 'products', 'newEntry'));
    }
    /**
     * The method are stores the new expiry entry information into the DB and update the stock of entered products.
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

    /**
     * This function is recover old expiry entry info into the DB,basically inventory.
     */
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
     * This method shows the entry according to the entry number and date.
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
     * This method are update the expiry entry of the given entry number and date.
     */
    public function update(Request $request, string $id, string $date)
    {
        if (empty($request->stockAmt) || $request->stockAmt == '') {
            return redirect()->route('expiry.index');
        }

        $dealer = (!empty($request->dealerId) || !empty($request->dealerName)) ? PurchaseController::insertIntoDealer($request) : null;

        $created = Expiry::select('created_at')->where('expiry_entry_id', $id)->whereRaw('DATE(`created_at`)=?', $date)->get();
        $del = Expiry::where('expiry_entry_id', $id)->whereRaw('DATE(`created_at`)=?', $date)->delete();

        //This recover the old entry info.
        $this->recoverOldExp($del);
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
     * This function are remove the specified entry from the DB as per Date and Entry number.
     */
    public function destroy(string $id, string $date)
    {
        $del = Expiry::where('expiry_entry_id', $id)->whereRaw('DATE(`created_at`)=?', $date)->delete();
        $this->recoverOldExp($del);
        return redirect()->route('expiry.index');
    }

    /**
     * This function are select the data within 10 days from DB and 10 days ago data are get from json file .
     * And merge it using merge method and send JSON array.
     */
    protected function getInfo()
    {
        $date = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
        $start = $date->modify('-10 days')->format('Y-m-d');
        $product = Expiry::withProductAndDealer()->whereRaw('DATE(`created_at`) BETWEEN ? AND ?', [$start, date('Y-m-d')])->get();
        $fromFile = File::get(public_path('json/expiry.json'));
        $merge = $this->merge($product, $fromFile);
        return Json::decode($merge, false);
    }

    /**
     * This method get info from inventory and product by relation and send to view
     * @return \Illuminate\Contracts\View\View
     */
    public function weeklyReport()
    {
        $products = Inventory::getFromInventory()->get();
        return View::make('Reports.expiry.weeklyReport', compact('products'));
    }

    /**
     * This method get info from inventory and product by relation and send to view
     * @return \Illuminate\Contracts\View\View
     */
    public function monthlyReport()
    {
        $products = Inventory::getFromInventory()->get();
        return view('Reports.expiry.monthlyReport', compact('products'));
    }

    /**
     * This method get info from inventory and product by relation and send to view
     * @return \Illuminate\Contracts\View\View
     */

    public function yearlyReport()
    {
        $products = Inventory::getFromInventory()->get();
        return view('Reports.expiry.yearlyReport', compact('products'));
    }

    /**
     * This function get info of the all reports and send it to the view for display it.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function returnExpReport()
    {
        $products = $this->getInfo();
        return view('Reports.expiry.returnExpReport', compact('products'));
    }

    /**
     * This method make the pdf of data give by getInfo() method.
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function printWeekly()
    {
        $merge = $this->getInfo();
        return $this->makePdf($merge, 'Reports.pdf.weeklyExpiryPrint', 'weeklyExpiry', 'Weekly Expiry Report');
    }

    /**
     * This method make the pdf of data give by getInfo() method.
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function printMonthly()
    {
        $merge = $this->getInfo();
        return $this->makePdf($merge, 'Reports.pdf.monthlyExpiryPrint', 'monthlyExpiry', 'Monthly Expiry Report');
    }

    /**
     * This method make the pdf of data give by getInfo() method.
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function printYearly()
    {
        $merge = $this->getInfo();
        return $this->makePdf($merge, 'Reports.pdf.yearlyExpiryPrint', 'yearlyExpiry', 'Yearly Expiry Report');
    }

    /**
     * This method make the pdf of data give by getInfo() method.
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */

    public function printExpReport()
    {
        $products = $this->getInfo();
        return $this->makePdf($products, 'Reports.pdf.expiryReportPrint', 'expiryReturnReport', 'Expiry Return Report');
    }

    /**
     * This method make the pdf of data give by getInfo() method between two dates as per users choice.
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */

    public function printWeeklyByDates(string $from = null, string $to = null)
    {
        $merge = $this->getInfo();
        $products = $this->filter($merge, $from, $to);
        return $this->makePdf($products, 'Reports.pdf.yearlyExpiryPrint', 'yearlyExpiry', 'Yearly Expiry Report');
    }

    /**
     * This method make the pdf of data give by getInfo() method between two dates as per users choice.
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function printMonthlyByDates(string $from = null, string $to = null)
    {
        $merge = $this->getInfo();
        $products = $this->filter($merge, $from, $to);
        return $this->makePdf($products, 'Reports.pdf.yearlyExpiryPrint', 'yearlyExpiry', 'Yearly Expiry Report');
    }

    /**
     * This method make the pdf of data give by getInfo() method between two dates as per users choice.
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function printYearlyByDates(string $from = null, string $to = null)
    {
        $merge = $this->getInfo();
        $products = $this->filter($merge, $from, $to);
        return $this->makePdf($products, 'Reports.pdf.yearlyExpiryPrint', 'yearlyExpiry', 'Yearly Expiry Report');
    }

    /**
     * This method make the pdf of data give by getInfo() method between two dates as per users choice.
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function printExpReportByDates(string $from = null, string $to = null)
    {
        $merge = $this->getInfo();
        $products = $this->filter($merge, $from, $to);
        return $this->makePdf($products, 'Reports.pdf.yearlyExpiryPrint', 'yearlyExpiry', 'Yearly Expiry Report');
    }
}
