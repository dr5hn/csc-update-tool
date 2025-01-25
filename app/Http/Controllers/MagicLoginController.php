<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Maize\MagicLogin\Facades\MagicLink;

class MagicLoginController extends Controller
{
    public function showMagicLoginForm()
    {
        return view('auth.magic-login');
    }

    public function sendMagicLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $user = User::where('email', $request->email)->first();

        // Create magic link with explicit configuration
        MagicLink::send(
            authenticatable: $user,
            redirectUrl: route('dashboard'),
            expiration: now()->addMinutes(30),
            guard: 'web'
        );

        return back()->with('status', 'We have emailed you a magic link to login!');
    }
}