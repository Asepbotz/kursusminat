<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kursus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Import Log facade untuk debugging

class KursusController extends Controller
{
    /**
     * Menampilkan daftar kursus untuk subdomain tertentu.
     * Konten tabel akan disesuaikan berdasarkan peran pengguna.
     */
    public function index($subdomain)
    {
        $kursus = Kursus::where('kategori', $subdomain)->get();
        return view('kursus.index', [
            'kursus' => $kursus,
            'subdomain' => $subdomain,
            'userRole' => Auth::check() ? Auth::user()->role : null, // Mengirim peran pengguna ke view
        ]);
    }

    /**
     * Menampilkan form untuk membuat kursus baru.
     * Akses dibatasi untuk admin oleh middleware.
     */
    public function create($subdomain)
    {
        return view('kursus.create', compact('subdomain'));
    }

    /**
     * Menyimpan kursus baru ke database.
     * Akses dibatasi untuk admin oleh middleware.
     */
    public function store(Request $request, $subdomain)
    {
        // Validasi input dari form
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
        ]);

        // Membuat entri kursus baru
        Kursus::create([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'kategori' => $subdomain, // Kategori diambil dari subdomain
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
        ]);

        // Mengalihkan kembali ke halaman daftar kursus dengan pesan sukses
        return redirect()->route('kursus.index', ['subdomain' => $subdomain])
                         ->with('success', 'Kursus berhasil ditambahkan!');
    }

    /**
     * Menampilkan form untuk mengedit kursus tertentu.
     * Akses dibatasi untuk admin oleh middleware.
     */
    public function edit($subdomain, $id)
    {
        $kursus = Kursus::findOrFail($id); // Mencari kursus berdasarkan ID atau menghentikan 404
        return view('kursus.edit', compact('kursus', 'subdomain'));
    }

    /**
     * Memperbarui kursus tertentu di database.
     * Akses dibatasi untuk admin oleh middleware.
     */
    public function update(Request $request, $subdomain, $id)
    {
        // Validasi input dari form
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
        ]);

        $kursus = Kursus::findOrFail($id); // Mencari kursus berdasarkan ID
        $kursus->update($request->only(['judul', 'deskripsi', 'tanggal_mulai', 'tanggal_selesai'])); // Memperbarui data

        // Mengalihkan kembali ke halaman daftar kursus dengan pesan sukses
        return redirect()->route('kursus.index', ['subdomain' => $subdomain])
                         ->with('success', 'Kursus berhasil diperbarui!');
    }

    /**
     * Menghapus kursus tertentu dari database.
     * Akses dibatasi untuk admin oleh middleware.
     */
    public function destroy($subdomain, $id)
    {
        Kursus::destroy($id); // Menghapus kursus berdasarkan ID

        // Mengalihkan kembali ke halaman daftar kursus dengan pesan sukses
        return redirect()->route('kursus.index', ['subdomain' => $subdomain])
                         ->with('success', 'Kursus berhasil dihapus!');
    }
}
 