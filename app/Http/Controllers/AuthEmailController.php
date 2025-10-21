<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\EmailLoginCode;

class AuthEmailController extends Controller
{
    public function requestCode(Request $r)
    {
        $data = $r->validate([
            'email' => 'required|email',
            'name'  => 'required|string|max:120', // require name here
        ]);

        $code = (string) random_int(100000, 999999);
        EmailLoginCode::updateOrCreate(
            ['email'=>$data['email']],
            ['code_hash'=>Hash::make($code), 'expires_at'=>Carbon::now()->addMinutes(15)]
        );

        // Create user record if not exists (no password needed)
        $user = User::firstOrCreate(
            ['email'=>$data['email']],
            ['name'=>$data['name'], 'password'=>Hash::make(Str::random(16))]
        );
        if (!$user->name) { $user->name = $data['name']; $user->save(); }

        // For real apps, send email; for local dev, log the code:
        Mail::raw("Your login code is: {$code}", fn($m) => $m->to($data['email'])->subject('Your login code'));
        // You can also return it in dev ONLY:
        if (app()->isLocal()) { return response()->json(['sent'=>true, 'dev_code'=>$code]); }

        return response()->json(['sent'=>true]);
    }

    public function verifyCode(Request $r)
    {
        $data = $r->validate([
            'email' => 'required|email',
            'code'  => 'required|string|size:6',
            'name'  => 'nullable|string|max:120', // optional here; may update
        ]);

        $row = EmailLoginCode::where('email', $data['email'])->first();
        if (!$row || !$row->expires_at || now()->gt($row->expires_at)) {
            return response()->json(['message'=>'Code expired'], 422);
        }
        if (!Hash::check($data['code'], $row->code_hash)) {
            return response()->json(['message'=>'Invalid code'], 422);
        }

        $user = User::firstOrCreate(['email'=>$data['email']], [
            'name'=>$data['name'] ?? null,
            'password'=>Hash::make(Str::random(16)),
        ]);
        if (($data['name'] ?? null) && !$user->name) { $user->name = $data['name']; $user->save(); }

        Auth::login($user, true);     // web guard
        $row->delete();               // single-use

        return response()->json(['ok'=>true, 'user'=>$user]);
    }
      
    public function logout(Request $r){
        Auth::guard('web')->logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();
        return response()->json(['ok'=>true]);
    }
}
