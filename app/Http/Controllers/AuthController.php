<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $email = $request->email;
        $key = 'login:attempts:' . $email;

        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()->with([
                'alert' => 'Terlalu banyak percobaan login. Coba lagi dalam 1 menit.',
                'alert_title' => 'Login Diblokir',
                'alert_type' => 'error'
            ]);
        }

        RateLimiter::hit($key, 60);

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = User::find(Auth::id());

            // ✅ Validasi akun tidak aktif
            if (!$user->is_active) {
                Auth::logout();
                return back()->with([
                    'alert' => 'Akun Anda sudah dinonaktifkan. Harap hubungi administrator untuk mengaktifkannya kembali.',
                    'alert_title' => 'Akun Nonaktif',
                    'alert_type' => 'error'
                ]);
            }

            $currentIp = $request->ip();            

            // Jika IP saat ini berbeda dan belum menyetujui logout device lain
            if ($user->last_login_ip && $user->last_login_ip !== $currentIp && !$request->confirm_force_login) {
                Auth::logout();

                // Simpan data sementara di session
                session()->put('force_login_credentials', [
                    'email' => $email,
                    'password' => $request->password
                ]);

                return back()->with([
                    'force_login' => true,
                    'alert_title' => 'Akun sedang digunakan',
                    'alert' => 'Akun Anda sedang aktif di device lain. Logout device tersebut dan lanjut login?',
                ]);
            }

            // Force login atau IP sama, lanjutkan
            $user->last_login_ip = $currentIp;
            $user->save();

            RateLimiter::clear($key);

            return redirect()->route('home')->with([
                'alert' => 'Selamat datang kembali!',
                'alert_title' => 'Login Berhasil',
                'alert_type' => 'success'
            ]);
        }

        return back()->with([
            'alert' => 'Email atau password salah.',
            'alert_title' => 'Login Gagal',
            'alert_type' => 'error'
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with([
            'alert' => 'Sampai jumpa lagi!',
            'alert_title' => 'Logout Berhasil',
            'alert_type' => 'success'
        ]);
    }

    protected function validateLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
    }

    public function forceLogin(Request $request)
    {
        $credentials = session()->pull('force_login_credentials');

        if ($credentials && Auth::attempt($credentials)) {
            $user = User::find(Auth::id());
            $user->last_login_ip = $request->ip();
            $user->save();

            return redirect()->route('home')->with([
                'alert' => 'Login berhasil dan device lama sudah logout.',
                'alert_title' => 'Device Baru Login',
                'alert_type' => 'success'
            ]);
        }

        return redirect('/')->with([
            'alert' => 'Gagal force login.',
            'alert_title' => 'Gagal',
            'alert_type' => 'error'
        ]);
    }
}
