<?php

namespace App\Http\Controllers;
date_default_timezone_set('Asia/Kolkata');

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Support\Facades\File;

abstract class Controller
{
    /**
     * This method get to arguments and merge it by creating single array.
     * The fileData is the info which are store in the json and other from DB
     * and this simply merge it form the purpose of accurate reports.
     * And returns it by converting into string.
     */
    protected function merge($collection, string $fileData)
    {
        if (count($collection) > 0 && $fileData != '') {
            $p = array_merge(Json::decode($collection), Json::decode($fileData));
            $p = Json::encode($p);
            $p = Json::decode($p, false);
        } else if (count($collection) > 0 && $fileData == '') {
            $p = Json::decode($collection, false);
        } else if (count($collection) <= 0 && $fileData != '') {
            $p = Json::decode($fileData, false);
        } else {
            $p = [];
        }

        return Json::encode($p);
    }

    /**
     * The method filter can modify the data according to the
     * From Date and To Date as per users choice and send the new data.
     */
    protected function filter(string $fileInfo, string $from, string $to)
    {
        $products = Json::decode($fileInfo, false);
        if (count($products) > 0) {
            foreach ($products as $info) {
                $date = substr($info->created_at, 0, 10);
                //if the date is not in between the from date and to date,then it will be remove from the collection.
                if (!($date >= $from) && !($date <= $to)) {
                    unset($info);
                }
            }
            $products = Json::encode($products);
        }
        return $products;
    }

    //This method simply create a pdf by given information.
    protected function makePdf($products, string $view, string $route, string $report)
    {
        if (count($products) > 0) {
            return Pdf::loadView($view, compact('products', 'report'), [
                'css' => [
                    File::get(public_path('css/style.css')),
                    File::get(public_path('css/bootstrap.css')),
                ],
            ])->setPaper('A4', 'landscape')->download('expiry-report.pdf');
        } else {
            return redirect()->route($route)->with('dataException', '<strong>Alert!</strong> Data Not Found ..');
        }
    }
}
