<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function create()
    {
        return view('auth.login'); // sesuai path blade-mu
    }

    public function store(LoginRequest $request)
    {
        $login = $request->input('login');
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [$field => $login, 'password' => $request->input('password')];

       if (Auth::guard('web')->attempt($credentials)) {
    return redirect()->intended('dashboard');
}


        return back()->withErrors(['login' => 'Invalid credentials.'])->onlyInput('login');
    }

    public function destroy()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login');
    }
}
