<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $res = Auth::attempt(['email' => $request->email, 'password' => $request->password]);
        if ($res) {
            return redirect()->route('home');
        } else {
            return redirect()->route('login')->with('invalid', '<strong>Alert !</strong>Invalid Credentials,try again!');
        }
    }

    public function logout()
    {
        if (Auth::check()) {
            Auth::logout();
            return redirect()->route('login');
        }
    }
}
