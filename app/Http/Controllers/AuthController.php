<?php

namespace App\Http\Controllers;
date_default_timezone_set('Asia/Kolkata');

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {

        $res = Auth::attempt(['username' => $request->username, 'password' => $request->password]);
        if ($res) {
            return true;
        }
    }
}
