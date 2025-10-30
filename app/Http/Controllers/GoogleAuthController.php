<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use GuzzleHttp\Client as GuzzleClient;

class GoogleAuthController extends Controller
{
    protected function googleDriver()
    {
        $driver = Socialite::driver('google');
        if (config('services.google.disable_ssl_verify')) {
            $driver->setHttpClient(new GuzzleClient(['verify' => false]));
        }
        return $driver;
    }

    public function redirect(): RedirectResponse
    {
        return $this->googleDriver()->redirect();
    }

    public function callback(): RedirectResponse
    {
        $googleUser = $this->googleDriver()->user();

        // If user is logged in: link Google account
        if (Auth::check()) {
            $current = Auth::user();

            // Prevent linking if another user already uses this google_id
            $exists = User::where('google_id', $googleUser->getId())
                ->where('id', '!=', $current->id)
                ->exists();
            if ($exists) {
                return redirect()->route('profile.edit')
                    ->with('status', 'google-link-failed');
            }

            $current->google_id = $googleUser->getId();
            // Optionally verify email if matches
            if ($googleUser->getEmail() && $current->email === $googleUser->getEmail() && is_null($current->email_verified_at)) {
                $current->email_verified_at = now();
            }
            $current->save();

            return redirect()->route('profile.edit')->with('status', 'google-linked');
        }

        // Not logged in: login or register
        $user = User::where('google_id', $googleUser->getId())->first();
        if (!$user && $googleUser->getEmail()) {
            $user = User::where('email', $googleUser->getEmail())->first();
        }

        if ($user) {
            // Attach google_id if missing
            if (empty($user->google_id)) {
                $user->google_id = $googleUser->getId();
                if (is_null($user->email_verified_at)) {
                    $user->email_verified_at = now();
                }
                $user->save();
            }
        } else {
            // Create new user
            $user = User::create([
                'name' => $googleUser->getName() ?: ($googleUser->getNickname() ?: 'User'),
                'email' => $googleUser->getEmail(),
                'password' => Hash::make(Str::random(32)),
            ]);
            $user->google_id = $googleUser->getId();
            $user->email_verified_at = now();
            $user->save();
        }

        Auth::login($user, true);
        return redirect()->route('dashboard');
    }

    public function unlink(): RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        $user = Auth::user();
        $user->google_id = null;
        $user->save();
        return redirect()->route('profile.edit')->with('status', 'google-unlinked');
    }
}
