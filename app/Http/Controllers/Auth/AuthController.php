<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function redirectToERP()
    {
        $uuid = config('services.erp.uuid');
        $erpUrl = config('services.erp.url');
        
        if (!$uuid || !$erpUrl) {
            return back()->with('error', 'Konfigurasi SSO ERP belum diatur.');
        }

        $url = rtrim($erpUrl, '/') . '/auth/sso_request?app=' . $uuid;
        return redirect()->away($url);
    }

    public function handleSSOCallback(Request $request)
    {
        $token = $request->query('token');
        $email = $request->query('email');

        if (!$token || !$email) {
            return redirect()->route('login')->with('error', 'Token atau email tidak valid.');
        }

        $erpUrl = config('services.erp.url');
        $verifyUrl = rtrim($erpUrl, '/') . '/api/auth/verify-sso';

        try {
            $response = Http::post($verifyUrl, [
                'token' => $token,
                'email' => $email,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['status']) && $data['status'] == 200 && isset($data['user'])) {
                    $erpUser = $data['user'];
                    
                    // Create or Update User locally
                    $user = User::updateOrCreate(
                        ['email' => $email],
                        [
                            'name' => $erpUser['name'] ?? 'User ERP',
                            'password' => bcrypt(Str::random(16)) // Dummy password
                        ]
                    );

                    Auth::login($user);
                    
                    return redirect()->intended('/dashboard');
                }
            }
            
            return redirect()->route('login')->with('error', 'Verifikasi SSO gagal. Pastikan token masih valid.');
            
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Terjadi kesalahan komunikasi dengan server ERP.');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
