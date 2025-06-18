<!DOCTYPE html>
<html>
<head>
    <title>Halaman Kursus Minat</title>
    <style>
        body {
            font-family: Arial;
            padding: 50px;
            background-color: #f7f7f7;
            text-align: center;
        }
        h1 {
            color: #333;
        }
        .card {
            background: white;
            padding: 30px;
            margin: auto;
            border-radius: 10px;
            width: 50%;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>Selamat datang di Kursus {{ ucfirst($minat) }}</h1>
        <p>Ini adalah halaman khusus untuk topik <strong>{{ $minat }}</strong>.</p>
    </div>
</body>
</html>
