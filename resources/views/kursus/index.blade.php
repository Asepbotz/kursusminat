<!DOCTYPE html>
<html>
<head>
    <title>Daftar Kursus {{ ucfirst($subdomain) }}</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f7f7f7; padding: 30px; }
        .container { max-width: 900px; margin: auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1 { color: #333; text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f2f2f2; color: #555; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        a { text-decoration: none; color: #007bff; }
        a:hover { text-decoration: underline; }
        .btn-link { display: inline-block; margin-right: 10px; padding: 8px 12px; background-color: #007bff; color: white; border-radius: 5px; text-align: center; }
        .btn-link:hover { background-color: #0056b3; text-decoration: none; }
        .action-buttons button { background: none; border: none; color: #dc3545; cursor: pointer; text-decoration: underline; padding: 0; font-size: inherit; }
        .action-buttons button:hover { color: #c82333; }
        .success-message { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
        .error-message { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
        .user-info { margin-bottom: 20px; text-align: center; }
    </style>
</head>
<body>
<div class="container">
    <h1>Daftar Kursus: {{ ucfirst($subdomain) }}</h1>

    @if (session('success'))
        <div class="success-message">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="error-message">
            {{ session('error') }}
        </div>
    @endif

    <div class="user-info">
        @auth
            <p>Halo, {{ auth()->user()->name }}! Anda login sebagai <strong>{{ auth()->user()->role }}</strong></p>
            <p>
                <a href="{{ route('logout') }}" class="btn-link"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </p>
        @else
            <p>Silakan <a href="{{ route('login') }}">login</a> terlebih dahulu untuk melihat kursus atau <a href="{{ route('register') }}">daftar</a> jika belum punya akun.</p>
        @endauth
    </div>

    {{-- Tombol Tambah Kursus hanya untuk admin --}}
    @if ($userRole === 'admin')
        <p><a href="{{ route('kursus.create', ['subdomain' => $subdomain]) }}" class="btn-link">+ Tambah Kursus</a></p>
    @endif

    <table>
        <thead>
            <tr>
                <th>Judul</th>
                <th>Deskripsi</th>
                <th>Akses</th>
                <th>Status</th>
                {{-- Kolom Aksi hanya untuk admin --}}
                @if ($userRole === 'admin')
                    <th>Aksi</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($kursus as $k)
            <tr>
                <td>{{ $k->judul }}</td>
                <td>{{ $k->deskripsi }}</td>
                <td>
                    {{ \Carbon\Carbon::parse($k->tanggal_mulai)->format('d M Y') }} s/d
                    {{ $k->tanggal_selesai ? \Carbon\Carbon::parse($k->tanggal_selesai)->format('d M Y') : 'Lifetime' }}
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
                {{-- Tombol Edit dan Hapus hanya untuk admin --}}
                @if ($userRole === 'admin')
                    <td class="action-buttons">
                        <a href="{{ route('kursus.edit', ['subdomain' => $subdomain, 'id' => $k->id]) }}">Edit</a> |
                        <form action="{{ route('kursus.destroy', ['subdomain' => $subdomain, 'id' => $k->id]) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Yakin hapus kursus ini?')">Hapus</button>
                        </form>
                    </td>
                @endif
            </tr>
            @empty
            <tr>
                <td colspan="{{ $userRole === 'admin' ? 5 : 4 }}" style="text-align: center;">Belum ada kursus untuk kategori ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
</body>
</html>
