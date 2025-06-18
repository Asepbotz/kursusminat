<!DOCTYPE html>
<html>
<head>
    <title>Tambah Kursus</title>
</head>
<body>
    <h2>Tambah Kursus - {{ ucfirst($subdomain) }}</h2>
    <form action="/store" method="post">
        @csrf
        <input type="text" name="judul" placeholder="Judul"><br>
        <textarea name="deskripsi" placeholder="Deskripsi"></textarea><br>
        <label>Tanggal Mulai: <input type="date" name="tanggal_mulai"></label><br>
        <label>Tanggal Selesai: <input type="date" name="tanggal_selesai"></label><br>
        <button type="submit">Simpan</button>
    </form>
</body>
</html>

