<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\User;
use App\Jobs\SendEmail;

class AuthenticatedSessionController extends Controller
{
    /**
     * Show the login page.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => $request->session()->get('status'),
        ]);
    }

    public function sendOTP(Request $request)
    {
        $validated = $request->validate(['email' => 'required|email']);

        $user = User::where('email', $validated['email'])->first();
    
        if (!$user) {
            return response()->json(['status' => 404, 'message' => 'User not found'], 404);
            // return Inertia::render('auth/Login', ['status' => "404", 'message' => 'User not found'], "404");
        }
    
        // Generate a 6-digit OTP
        $otp = rand(100000, 999999);
    
        // Save OTP to the user
        $user->otp = $otp;
        $user->save();
    
        // Send OTP via email (or SMS)
        // Mail::to($user->email)->send(new OtpMail($otp));
        // Mail::to($validated['email'])->send(new SendLoginMail("Your OTP is: ".$otp));
        dispatch(new SendEmail($validated['email'], 'Your OTP is: '.$otp));
    
        // return Inertia::render('auth/Login', ['status' => "200", 'message' => 'OTP sent successfully'], "200");
        return response()->json(['status' => "200", 'message' => 'OTP sent successfully'], "200");
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        
        $request->authenticate();

        $request->session()->regenerate();
        return redirect()->intended(route('home', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
