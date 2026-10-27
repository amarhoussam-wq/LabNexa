<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Administrator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        // Redirect to unified portal with admin tab active
        return redirect()->route('access', ['tab' => 'admin']);
    }

    /**
     * Handle administrator login
     */
    public function administrator_login(Request $request)
    {


        // Validate credentials
        $credentials = $request->validate([
            'name' => ['required', 'string'],
            'password' => ['required'],
        ]);



        // Check if admin exists
        $admin = Administrator::where('name', $credentials['name'])->first();


        // Attempt authentication
        if (Auth::guard('administrator')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended('/dashboard');
        }



        // Authentication failed
        return back()->withErrors([
            'name' => 'بيانات الدخول غير صحيحة',
        ])->withInput($request->only('name'));
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::guard('administrator')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('access');
    }
}
