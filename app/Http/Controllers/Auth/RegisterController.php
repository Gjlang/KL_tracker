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
        return view('auth.register'); // atau tab register di login page
    }

    public function store(RegisterRequest $request)
    {
        $data = $request->validated();

        $firstUser = User::count() === 0;
        $role = $data['role'] ?? 'user';

        // Cegah publik bikin admin kalau bukan first user
        if ($role === 'admin' && !$firstUser) {
            $role = 'user';
        }

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => $data['password'], // auto hash
            'role'     => $role,
        ]);
        $user->syncRoles([$role]);

        event(new Registered($user));
        Auth::login($user);

        return redirect()->intended(route('dashboard'));
    }

}
