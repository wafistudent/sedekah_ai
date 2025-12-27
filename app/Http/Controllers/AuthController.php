<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController
{
    //
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validate the request...
        $credentials = $request->only('id', 'password');

        if (auth()->attempt($credentials)) {
            // Authentication passed...
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout()
    {
        auth()->logout();
        return redirect('/login');
    }
}
