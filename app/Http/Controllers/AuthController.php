<?php

namespace App\Http\Controllers;

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
