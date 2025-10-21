<?php

// app/Http/Controllers/AuthGoogleController.php
namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthGoogleController extends Controller
{
    public function redirect()
    {
        // Optional: pass ?name=... from SPA and hold it in session
        if (request('name')) session(['pending_name'=>request('name')]);

        return Socialite::driver('google')
            ->stateless(false) // use session since same-site cookie within API domain
            ->redirect();
    }

    public function callback()
    {
        $gUser = Socialite::driver('google')->stateless(false)->user();

        $user = User::firstOrCreate(
            ['email' => $gUser->getEmail()],
            ['name'  => $gUser->getName() ?: session('pending_name'), 'password'=>bcrypt(Str::random(16))]
        );

        // Ensure name if still empty
        if (!$user->name && session('pending_name')) { $user->name = session('pending_name'); $user->save(); }
        Auth::login($user, true);

        // PostMessage back to opener (Nuxt) and close popup
        $origin = config('app.url'); // not used here; we’ll post to allowed origin
        $allowed = request()->headers->get('Origin') ?? 'http://localhost:3000';
        return response(<<<HTML
            <!doctype html><html><body>
            <script>
            (function(){
                if (window.opener) {
                window.opener.postMessage({ type: 'oauth:success' }, '*');
                window.close();
                } else {
                window.location = 'http://localhost:3000'; // fallback
                }
            })();
            </script>
            Success. You can close this window.
            </body></html>
            HTML
        );
    }
}

