<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\MagicLink;
use App\Models\User;
use App\Notifications\MagicLinkNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MagicLinkController extends Controller
{
    public function create(): View
    {
        return view('auth.magic-link');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
        ]);

        $token = Str::uuid();
        $magicLink = URL::temporarySignedRoute(
            'magic-link.verify',
            now()->addHour(),
            ['token' => $token->toString()]
        );

        MagicLink::create([
            'token' => $token,
            'payload' => encrypt($request->only('email')),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        Notification::route('mail', $request->email)
            ->notify(new MagicLinkNotification($magicLink));

        return back()->with('status', 'We have emailed you a magic link!');
    }

    public function verify(Request $request): RedirectResponse
    {
        $magicLink = MagicLink::where('token', $request->token)
            ->whereNull('used_at')
            ->firstOrFail();

        $data = decrypt($magicLink->payload);
        
        $user = User::firstOrCreate(
            ['email' => $data['email']],
            ['name' => $data['email']]
        );

        $magicLink->update(['used_at' => now()]);
        Auth::login($user);
        
        return redirect()->route('dashboard');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}