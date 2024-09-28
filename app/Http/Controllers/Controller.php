<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Support\Facades\File;

abstract class Controller
{
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

    protected function filter(string $fileInfo, string $from, string $to)
    {
        $products = Json::decode($fileInfo, false);
        if (count($products) > 0) {
            foreach ($products as $info) {
                $date = substr($info, 0, 10);
                if (!($date >= $from) && !($date <= $to)) {
                    unset($info);
                }
            }
            $products = Json::encode($products);
        }
        return $products;
    }

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
