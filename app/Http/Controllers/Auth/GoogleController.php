<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Check if user already exists
            $user = User::where('email', $googleUser->getEmail())->first();

            if(!$user){
                return redirect()->route('login')->withErrors(['email' => 'Usuario no encontrado. Por favor, contacta al administrador.']);
            }

            if(!$user->active){
                return redirect()->route('login')->withErrors(['email' => 'Usuario inactivo. Por favor, contacta al administrador.']);
            }

            if(!$user->role){
                return redirect()->route('login')->withErrors(['email' => 'Usuario no tiene un rol asignado. Por favor, contacta al administrador.']);
            }

            $result = $user->update([
                'name' => $googleUser->getName(),
                'google_id' => $googleUser->getId(),
                'google_token' => $googleUser->token,
                'google_refresh_token' => $googleUser->refreshToken,
                'email_verified_at' => now()
            ]);
            
            Auth::login($user);

            return redirect()->intended(route('dashboard'));
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['email' => 'Google authentication failed. Please try again.']);
        }
    }
}
