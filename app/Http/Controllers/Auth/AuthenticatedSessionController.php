<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Menampilkan tampilan login.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Menangani permintaan autentikasi masuk.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Mengambil subdomain dari host permintaan
        $subdomain = explode('.', $request->getHost())[0];

        // Mengalihkan pengguna ke halaman kursus yang sesuai dengan subdomain
        // Misalnya: math.kursusku.test/kursus
        return redirect()->route('kursus.index', ['subdomain' => $subdomain]);
    }

    /**
     * Menghancurkan sesi terautentikasi.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout(); // Melakukan logout

        $request->session()->invalidate(); // Membatalkan sesi

        $request->session()->regenerateToken(); // Meregenerasi token sesi

        // Mengalihkan pengguna kembali ke halaman login
        return redirect()->route('login');
    }
}

