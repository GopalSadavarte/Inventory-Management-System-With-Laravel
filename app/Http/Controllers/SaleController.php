<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use Barryvdh\DomPDF\Facade\Pdf;
use DateTime;
use DateTimeZone;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Support\Facades\File;

class SaleController extends Controller
{
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
        // return $bills;
        return view('Reports.sale.saleReport', compact('bills'));
    }
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
        // return $bills;
        return $this->downloadPdf($bills);
    }
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
        // return $billInfo;
        return $this->downloadPdf($billInfo);
    }

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

    protected function getDataFromFile(): array
    {
        $d = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
        $d->modify('-10 day');
        $date = $d->format('Y-m-d');
        $fileInfo = File::get(public_path('json/bill.json'));
        $bills = Bill::with('billCustomer', 'billProduct', 'billInventory')->whereRaw('DATE(`bills`.`created_at`) BETWEEN ? AND ?', [$date, date('Y-m-d')])->get();
        return [$fileInfo, $bills];
    }

    protected function getDataFromFileByDates(string $from, string $to): array
    {
        $fileInfo = File::get(public_path('json/bill.json'));
        if (!empty($fileInfo)) {
            $data = Json::decode($fileInfo, false);
            foreach ($data as $bill) {
                $date = substr($bill->created_at, 0, 10);
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
