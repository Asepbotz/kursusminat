<!DOCTYPE html>
<html>
<head>
    <title>Daftar Kursus {{ ucfirst($subdomain) }}</title>
    <style>
        body { font-family: sans-serif; background: #f5f5f5; padding: 30px; }
        .container { max-width: 800px; margin: auto; background: white; padding: 20px; border-radius: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        a { text-decoration: none; color: blue; }
        .btn { margin-right: 10px; }
    </style>
</head>
<body>
<div class="container">
    <h1>Kursus: {{ ucfirst($subdomain) }}</h1>
    @auth
        <p>Halo, {{ auth()->user()->name }}! Anda login sebagai <strong>{{ auth()->user()->role }}</strong></p>
        <p><a href="/logout">Logout</a></p>
    @else
        <p>Silakan <a href="/login">login</a> terlebih dahulu untuk melihat kursus.</p>
    @endauth
    <a href="/create">+ Tambah Kursus</a>
    <table>
        <tr>
            <th>Judul</th>
            <th>Deskripsi</th>
            <th>Akses</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
        @foreach($kursus as $k)
        <tr>
            <td>{{ $k->judul }}</td>
            <td>{{ $k->deskripsi }}</td>
            <td>
                {{ $k->tanggal_mulai }} s/d 
                {{ $k->tanggal_selesai ?? 'Lifetime' }}
            </td>
            <td>
                @if ($k->status == 'Aktif')
                    <span style="color: green;">Aktif</span>
                @elseif ($k->status == 'Kadaluarsa')
                    <span style="color: red;">Kadaluarsa</span>
                @else
                    <span style="color: blue;">Lifetime</span>
                @endif
            </td>
            <td>
                <a href="/edit/{{ $k->id }}">Edit</a> |
                <a href="/delete/{{ $k->id }}" onclick="return confirm('Yakin hapus?')">Hapus</a>
            </td>
        </tr>
        @endforeach
    </table>
</div>
</body>
</html>
