<?php

namespace App\Http\Controllers;

date_default_timezone_set('Asia/Kolkata');
use App\Models\Bill;
use App\Models\Expiry;
use App\Models\Purchase;
use App\Models\Stock;
use DateTime;
use DateTimeZone;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class JsonController extends Controller
{

    /**
     * This method are get the data of Bill Entries,purchase entries,stock and expiry entries
     * from DB and store it into the json files and remove it from the DB.
     */
    public function storeToJson()
    {
        $d = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
        $d->modify('-10 day');
        $date = $d->format('Y-m-d');

        $billInfo = Bill::with('billProduct', 'billInventory', 'billCustomer')
            ->whereRaw('DATE(`created_at`)=?', $date)
            ->get();

        $purchaseEntries = Purchase::withProductAndDealer()->whereRaw('DATE(`purchases`.`created_at`)=?', $date)->get();

        $stocks = Stock::withProductAndDealer()->whereRaw('DATE(`stocks`.`created_at`)=?', $date)->get();

        $expiries = Expiry::withProductAndDealer()->whereRaw('DATE(`created_at`)=?', $date)->get();

        $res = $res1 = $res2 = $res3 = false;
        if ($billInfo->count() > 0) {
            $res = JsonController::insertIntoFile($billInfo, 'bill');
        }
        if ($purchaseEntries->count() > 0) {
            $res1 = JsonController::insertIntoFile($purchaseEntries, 'purchase');
        }
        if ($stocks->count() > 0) {
            $res2 = JsonController::insertIntoFile($stocks, 'stock');
        }
        if ($expiries->count() > 0) {
            $res3 = JsonController::insertIntoFile($expiries, 'expiry');
        }
        if ($res) {
            Bill::whereRaw('DATE(`created_at`)=?', $date)->delete();
        }

        if ($res1) {
            Purchase::whereRaw('DATE(`created_at`)=?', $date)->delete();
        }

        if ($res2) {
            Stock::whereRaw('DATE(`created_at`)=?', $date)->delete();
        }

        if ($res3) {
            Expiry::whereRaw('DATE(`created_at`)=?', $date)->delete();
        }

        return view('welcome');
    }

    /**
     * This function is merge the old file data and new Data and return after merge.
     */
    protected function insertIntoFile(object | array $newData, string $fileName)
    {
        $string = Json::encode($newData);
        $string = Str::replaceFirst('[', '', $string);
        $string = Str::replaceLast(']', '', $string);

        $content = File::get(public_path('json/' . $fileName . '.json'));
        $content = Str::replaceFirst('[', '', $content);
        $content = Str::replaceLast(']', '', $content);

        if (empty($content)) {
            $data = '[' . $string . ']';
        } else {
            $data = '[' . $content . ',' . $string . ']';
        }
        if (!session()->has($fileName . 'Data')) {
            $res = File::put(public_path('json/' . $fileName . '.json'), $data);
            if ($res) {
                session()->put($fileName . 'Data', 'This session are set!');
                return true;
            }
        }
    }
}
