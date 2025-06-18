<!DOCTYPE html>
<html>
<head>
    <title>Edit Kursus</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f7f7f7; padding: 30px; }
        .container { max-width: 600px; margin: auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { color: #333; text-align: center; margin-bottom: 20px; }
        form div { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; }
        input[type="text"], input[type="date"], textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box; /* Memastikan padding termasuk dalam lebar */
            font-size: 1em;
        }
        button {
            background-color: #007bff; /* Blue */
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            margin-right: 10px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .cancel-link {
            background-color: #6c757d; /* Gray */
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 1em;
        }
        .cancel-link:hover {
            background-color: #5a6268;
        }
        .error-message { color: red; font-size: 0.9em; margin-top: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Kursus - {{ ucfirst($subdomain) }}</h2>
        <form action="{{ route('kursus.update', ['subdomain' => $subdomain, 'id' => $kursus->id]) }}" method="post">
            @csrf
            @method('PUT') {{-- Menggunakan metode PUT untuk update --}}
            <div>
                <label for="judul">Judul:</label>
                <input type="text" id="judul" name="judul" placeholder="Judul Kursus" value="{{ old('judul', $kursus->judul) }}" required>
                @error('judul')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label for="deskripsi">Deskripsi:</label>
                <textarea id="deskripsi" name="deskripsi" placeholder="Deskripsi Kursus" rows="5" required>{{ old('deskripsi', $kursus->deskripsi) }}</textarea>
                @error('deskripsi')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label for="tanggal_mulai">Tanggal Mulai:</label>
                <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="{{ old('tanggal_mulai', $kursus->tanggal_mulai) }}">
                @error('tanggal_mulai')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label for="tanggal_selesai">Tanggal Selesai (opsional):</label>
                <input type="date" id="tanggal_selesai" name="tanggal_selesai" value="{{ old('tanggal_selesai', $kursus->tanggal_selesai) }}">
                @error('tanggal_selesai')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit">Update Kursus</button>
            <a href="{{ route('kursus.index', ['subdomain' => $subdomain]) }}" class="cancel-link">Batal</a>
        </form>
    </div>
</body>
</html>
