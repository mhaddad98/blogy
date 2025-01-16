<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class SessionController extends Controller
{
    function create()
    {
        return Inertia::render('Auth/Login');
    }


    public function store(Request $request)
    {
        $userAttributes = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        if (! Auth::attempt($userAttributes)) {
            throw ValidationException::withMessages([
                'email' => 'Your provided credentials could not be verified.'
            ]);
        }

        $request->session()->regenerate();
        return redirect()->intended();
    }

    public function destroy()
    {
        Auth::logout();
        return redirect()->intended();;
    }
}
