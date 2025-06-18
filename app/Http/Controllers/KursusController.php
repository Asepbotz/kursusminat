<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kursus;
use Illuminate\Routing\Attributes\Middleware;

#[Middleware('auth')]
class KursusController extends Controller
{
    public function index($subdomain)
    {
        $kursus = Kursus::where('kategori', $subdomain)->get();
        return view('kursus.index', [
            'kursus' => $kursus,
            'subdomain' => $subdomain,
            'minat' => $subdomain // jika kamu tetap mau pakai $minat
        ]);
    }

    public function create($subdomain)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Hanya admin yang boleh mengakses halaman ini.');
        }
        return view('kursus.create', compact('subdomain'));
    }

    public function store(Request $request, $subdomain)
    {
        Kursus::create([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'kategori' => $subdomain,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
        ]);
        return redirect('/');
    }

    public function edit($subdomain, $id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Hanya admin yang boleh mengakses halaman ini.');
        }
        $kursus = Kursus::findOrFail($id);
        return view('kursus.edit', compact('kursus', 'subdomain'));
    }

    public function update(Request $request, $subdomain, $id)
    {
        $kursus = Kursus::findOrFail($id);
        $kursus->update($request->only(['judul', 'deskripsi', 'tanggal_mulai', 'tanggal_selesai']));
        return redirect('/');
    }

    public function destroy($subdomain, $id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Hanya admin yang boleh mengakses halaman ini.');
        }
        Kursus::destroy($id);
        return redirect('/');
    }
}
