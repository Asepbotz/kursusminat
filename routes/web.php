<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\KursusController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth; // Import Auth facade

// Grup rute untuk subdomain (misal: math.kursusku.test)
Route::domain('{subdomain}.kursusku.test')->group(function () {
    // Rute utama untuk subdomain. Mengalihkan berdasarkan status login dan peran.
    Route::get('/', function ($subdomain) {
        if (Auth::check()) {
            // Jika pengguna login, alihkan ke halaman daftar kursus spesifik subdomain
            return redirect()->route('kursus.index', ['subdomain' => $subdomain]);
        }
        // Jika tidak login, tampilkan halaman selamat datang generik
        return view('welcome', ['minat' => $subdomain]);
    });

    // Rute-rute yang memerlukan autentikasi
    Route::middleware(['auth'])->group(function () {
        // Rute untuk menampilkan daftar kursus di subdomain tertentu.
        // Dapat diakses oleh user dan admin.
        Route::get('/kursus', [KursusController::class, 'index'])->name('kursus.index');

        // Rute-rute khusus admin untuk pengelolaan kursus (CRUD)
        Route::middleware('admin')->group(function () {
            // Tampilkan form untuk membuat kursus baru
            Route::get('/kursus/create', [KursusController::class, 'create'])->name('kursus.create');
            // Simpan data kursus baru
            Route::post('/kursus', [KursusController::class, 'store'])->name('kursus.store');
            // Tampilkan form untuk mengedit kursus yang ada
            Route::get('/kursus/{id}/edit', [KursusController::class, 'edit'])->name('kursus.edit');
            // Perbarui data kursus yang ada
            Route::put('/kursus/{id}', [KursusController::class, 'update'])->name('kursus.update');
            // Hapus kursus
            Route::delete('/kursus/{id}', [KursusController::class, 'destroy'])->name('kursus.destroy');
        });
    });
});

// Rute Dashboard generik (setelah login)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Rute pengelolaan profil pengguna
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Memuat rute autentikasi Laravel Breeze
require __DIR__.'/auth.php';

