<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GitHubController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('github')->redirect();
    }

    public function callback(): RedirectResponse
    {
        $githubUser = Socialite::driver('github')->user();

        $user = User::where('github_id', $githubUser->id)
            ->orWhere('email', $githubUser->email)
            ->first();

        if ($user) {
            // Update existing user with GitHub ID if they don't have one
            if (!$user->github_id) {
                $user->update(['github_id' => $githubUser->id]);
            }
        } else {
            $user = User::create([
                'name' => $githubUser->name ?? $githubUser->nickname,
                'email' => $githubUser->email,
                'github_id' => $githubUser->id,
            ]);
        }

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}