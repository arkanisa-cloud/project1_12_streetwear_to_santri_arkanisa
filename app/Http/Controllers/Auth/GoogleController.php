<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    // Redirect user ke halaman login Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // Handle callback dari Google setelah user login
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Cari user berdasarkan google_id atau email
            $user = User::where('google_id', $googleUser->id)
                        ->orWhere('email', $googleUser->email)
                        ->first();

            if ($user) {
                // Jika user ada tapi belum punya google_id, update datanya
                if (!$user->google_id) {
                    $user->update([
                        'google_id' => $googleUser->id,
                        'google_token' => $googleUser->token,
                    ]);
                }
                
                Auth::login($user);
            } else {
                // Jika user belum terdaftar, buat user baru
                $newUser = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'google_token' => $googleUser->token,
                    // Karena login lewat google, password kita buat random & secure
                    'password' => Hash::make(Str::random(24)), 
                ]);

                Auth::login($newUser);
            }

            // Redirect ke halaman dashboard atau home toko online STS
            return redirect()->route('home')->with('success', 'Berhasil login dengan Google!');

        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Terjadi kesalahan saat login menggunakan Google.');
        }
    }
}