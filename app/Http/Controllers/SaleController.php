<?php

namespace App\Http\Controllers;

date_default_timezone_set('Asia/Kolkata');
use App\Models\Bill;
use Barryvdh\DomPDF\Facade\Pdf;
use DateTime;
use DateTimeZone;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Support\Facades\File;

class SaleController extends Controller
{
    /**
     * This method This method are get and return the sale report view file.
     */
    public function getSaleReport()
    {
        $d = $this->getDataFromFile();
        $fileInfo = $d[0];
        $bills = $d[1];

        $bills = $this->verifyingData($fileInfo, $bills);
        if ($bills != null) {
            $bills = Json::encode($bills);
            $bills = Json::decode($bills, false);
        }

        return view('Reports.sale.saleReport', compact('bills'));
    }
    /**
     * This method are get data in between dates and generate the pdf of the view.
     *  */
    public function printSaleReportByDate(string $from, string $to)
    {
        $d = $this->getDataFromFileByDates($from, $to);
        $fileInfo = $d[0];
        $bills = $d[1];
        $bills = $this->verifyingData($fileInfo, $bills);
        if ($bills != null) {
            $bills = Json::encode($bills);
            $bills = Json::decode($bills, false);
        }
        return $this->downloadPdf($bills);
    }

    /**
     * This method are generate the pdf of the sale report.
     */
    public function printSaleReport()
    {
        $d = $this->getDataFromFile();
        $fileInfo = $d[0];
        $bills = $d[1];
        $bills = $this->verifyingData($fileInfo, $bills);
        if ($bills != null) {
            $data = Json::encode($bills);
            $billInfo = Json::decode($data, false);
        }
        return $this->downloadPdf($billInfo);
    }

    /**
     * This method are merge data of to different collections.
     */
    protected function verifyingData(mixed $fromJson, mixed $fromDatabase)
    {
        if (!empty($fromJson) && $fromDatabase->count() > 0) {
            $products = array_merge(Json::decode($fromJson), Json::decode($fromDatabase));
        } else if (!empty($fromJson) && $fromDatabase->count() <= 0) {
            $products = Json::decode($fromJson);
        } else if (empty($fromJson) && $fromDatabase->count() > 0) {
            $products = Json::decode($fromDatabase);
        } else {
            $products = null;
        }
        return $products;
    }

    /**
     * This method are generate and return  the pdf of given information.
     */
    protected function downloadPdf($bills)
    {
        if ($bills != null) {
            return Pdf::loadView('Reports/pdf/saleReportPrint', compact('bills'), [
                'css' => [
                    File::get(public_path('css/style.css')),
                    File::get(public_path('css/bootstrap.css')),
                ],
            ])->setPaper('A4', 'landscape')->download();
        } else {
            return redirect()->route('sale.index')->with('exception', '<b>Alert! </b>Sorry,Data not found!!');
        }
    }

    /**
     * This method are read the data from JSON file and Database and return it.
     */
    protected function getDataFromFile(): array
    {
        $d = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
        $d->modify('-10 day');
        $date = $d->format('Y-m-d');
        $fileInfo = File::get(public_path('json/bill.json'));
        $bills = Bill::with('billCustomer', 'billProduct', 'billInventory')->whereRaw('DATE(`bills`.`created_at`) BETWEEN ? AND ?', [$date, date('Y-m-d')])->get();
        return [$fileInfo, $bills];
    }

    /**
     * This method get and filter the data in between to dates according to the user request.
     */
    protected function getDataFromFileByDates(string $from, string $to): array
    {
        $fileInfo = File::get(public_path('json/bill.json'));
        if (!empty($fileInfo)) {
            $data = Json::decode($fileInfo, false);
            foreach ($data as $bill) {
                $date = substr($bill->created_at, 0, 10);
                //If the date is not in between from and to then it will be remove from collection.
                if (!($date >= $from) && !($date <= $to)) {
                    unset($bill);
                }
            }
            $fileInfo = Json::encode($data);
        }
        $bills = Bill::with('billCustomer', 'billProduct', 'billInventory')->whereRaw('DATE(`bills`.`created_at`) BETWEEN ? AND ?', [$from, $to])->get();
        return [$fileInfo, $bills];
    }
}
