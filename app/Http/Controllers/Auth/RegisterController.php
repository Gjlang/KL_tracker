<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function create()
    {
        return view('auth.register'); // separate view OR you can keep using the login page with a toggle
    }

    public function store(RegisterRequest $request)
    {
        $data = $request->validated();

        // default role 'user' if not provided
        if (!isset($data['role'])) {
            $data['role'] = 'user';
        }

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => $data['password'], // will be hashed by the User model cast
            'role'     => $data['role'],
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->intended(route('dashboard'));
    }
}
