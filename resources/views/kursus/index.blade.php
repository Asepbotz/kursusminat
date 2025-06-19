<!DOCTYPE html>
<html>
<head>
    <title>Daftar Kursus {{ ucfirst($subdomain) }}</title>
    {{-- Pastikan ada meta CSRF token di sini untuk AJAX POST request --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

        /* Live Chatroom Styles */
        .chat-container {
            margin-top: 30px;
            border: 1px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
            background-color: #fefefe;
        }
        .chat-header {
            background-color: #007bff;
            color: white;
            padding: 15px;
            font-weight: bold;
            text-align: center;
            font-size: 1.2em;
        }
        .chat-messages {
            height: 300px;
            overflow-y: auto;
            padding: 15px;
            background-color: #e9ecef;
            display: flex;
            flex-direction: column;
        }
        .chat-message {
            background-color: #ffffff;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            max-width: 80%;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .chat-message.self {
            align-self: flex-end;
            background-color: #d4edda; /* Light green for self messages */
        }
        .chat-message-sender {
            font-weight: bold;
            color: #0056b3; /* Darker blue for sender name */
            margin-bottom: 5px;
        }
        .chat-message-time {
            font-size: 0.8em;
            color: #666;
            text-align: right;
            margin-top: 5px;
        }
        .chat-input {
            display: flex;
            padding: 15px;
            border-top: 1px solid #ddd;
            background-color: #f8f9fa;
        }
        .chat-input input {
            flex-grow: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-right: 10px;
        }
        .chat-input button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
        }
        .chat-input button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Daftar Kursus: {{ ucfirst($subdomain) }}</h1>

    {{-- Menampilkan pesan sukses dari session --}}
    @if (session('success'))
        <div class="success-message">
            {{ session('success') }}
        </div>
    @endif

    {{-- Menampilkan pesan error dari session --}}
    @if (session('error'))
        <div class="error-message">
            {{ session('error') }}
        </div>
    @endif

    <div class="user-info">
        @auth
            <p>Halo, {{ Auth::user()->name }}! Anda login sebagai <strong>{{ $userRole }}</strong></p>
            <p>
                {{-- Tombol Logout --}}
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

    <table id="kursusTable"> {{-- Tambahkan ID untuk JavaScript --}}
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
            {{-- Data kursus akan diisi oleh JavaScript atau PHP awal --}}
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
            <tr id="noKursusRow"> {{-- Tambahkan ID untuk JavaScript --}}
                <td colspan="{{ $userRole === 'admin' ? 5 : 4 }}" style="text-align: center;">Belum ada kursus untuk kategori ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Live Chatroom --}}
    <div class="chat-container">
        <div class="chat-header">Live Chatroom</div>
        <div class="chat-messages" id="chatMessages">
            {{-- Pesan chat akan dimuat di sini oleh JavaScript --}}
            <p style="text-align: center; color: #888;">Memuat pesan...</p>
        </div>
        @auth {{-- Hanya tampilkan input chat jika user sudah login --}}
        <div class="chat-input">
            <input type="text" id="chatInput" placeholder="Ketik pesan Anda..." @if(!Auth::check()) disabled @endif>
            <button id="sendMessageBtn" @if(!Auth::check()) disabled @endif>Kirim</button>
        </div>
        @else
        <div class="chat-input" style="justify-content: center; color: #888;">
            Anda harus login untuk mengirim pesan chat.
        </div>
        @endauth
    </div>
</div>

<script>
    // Fungsi untuk mengubah huruf pertama menjadi kapital
    function ucfirst(string) {
        if (!string) return '';
        return string.charAt(0).toUpperCase() + string.slice(1);
    }

    // Mendapatkan token CSRF dari meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const currentUserId = {{ Auth::check() ? Auth::user()->id : 'null' }}; // ID user yang sedang login
    const userRole = '{{ $userRole }}'; // Peran user yang sedang login

    // --- Fungsi untuk memperbarui daftar kursus secara realtime (polling) ---
    function fetchKursusData() {
        const subdomain = '{{ $subdomain }}'; // Ambil subdomain dari PHP
        fetch(`{{ url('/api/kursus') }}/${subdomain}`)
            .then(response => {
                if (!response.ok) {
                    // Jika respons bukan OK, cek apakah ada redirect ke halaman login
                    if (response.redirected && response.url.includes('/login')) {
                        console.warn('Sesi mungkin habis, dialihkan ke halaman login.');
                        // Opsi: alihkan pengguna ke halaman login
                        // window.location.href = response.url;
                        return; // Hentikan pemrosesan lebih lanjut
                    }
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                const tbody = document.querySelector('#kursusTable tbody');
                let newHtml = '';
                if (data.kursus.length === 0) {
                    newHtml = `<tr id="noKursusRow"><td colspan="${userRole === 'admin' ? 5 : 4}" style="text-align: center;">Belum ada kursus untuk kategori ini.</td></tr>`;
                } else {
                    data.kursus.forEach(kursus => {
                        let statusColor = '';
                        if (kursus.status === 'Aktif') {
                            statusColor = 'green';
                        } else if (kursus.status === 'Kadaluarsa') {
                            statusColor = 'red';
                        } else {
                            statusColor = 'blue'; // Lifetime atau Mendatang
                        }

                        let actionButtons = '';
                        // Pastikan tombol Edit dan Hapus memiliki rute yang benar dan token CSRF
                        if (userRole === 'admin') {
                            const editRoute = `{{ route('kursus.edit', ['subdomain' => $subdomain, 'id' => 'TEMP_ID']) }}`.replace('TEMP_ID', kursus.id);
                            const deleteRoute = `{{ route('kursus.destroy', ['subdomain' => $subdomain, 'id' => 'TEMP_ID']) }}`.replace('TEMP_ID', kursus.id);

                            actionButtons = `
                                <td class="action-buttons">
                                    <a href="${editRoute}">Edit</a> |
                                    <form action="${deleteRoute}" method="POST" style="display:inline;">
                                        <input type="hidden" name="_token" value="${csrfToken}">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" onclick="return confirm('Yakin hapus kursus ini?')">Hapus</button>
                                    </form>
                                </td>
                            `;
                        }

                        newHtml += `
                            <tr>
                                <td>${kursus.judul}</td>
                                <td>${kursus.deskripsi}</td>
                                <td>${kursus.tanggal_mulai} s/d ${kursus.tanggal_selesai}</td>
                                <td><span style="color: ${statusColor};">${kursus.status}</span></td>
                                ${actionButtons}
                            </tr>
                        `;
                    });
                }
                tbody.innerHTML = newHtml; // Perbarui tabel
            })
            .catch(error => console.error('Error fetching kursus:', error));
    }

    // --- Fungsi untuk memperbarui pesan chat secara realtime (polling) ---
    const chatMessagesDiv = document.getElementById('chatMessages');
    const chatInput = document.getElementById('chatInput');
    const sendMessageBtn = document.getElementById('sendMessageBtn');

    function fetchChatMessages() {
        fetch('{{ route('api.chat.messages') }}')
            .then(response => {
                if (!response.ok) {
                    // Jika respons bukan OK, cek apakah ada redirect ke halaman login
                    if (response.redirected && response.url.includes('/login')) {
                        console.warn('Sesi mungkin habis, dialihkan ke halaman login.');
                        // Opsi: alihkan pengguna ke halaman login
                        // window.location.href = response.url;
                        return; // Hentikan pemrosesan lebih lanjut
                    }
                    throw new Error('Network response was not ok');
                }
                return response.json(); // Mencoba mengurai sebagai JSON
            })
            .then(data => {
                const currentScrollPos = chatMessagesDiv.scrollTop;
                const maxScrollPos = chatMessagesDiv.scrollHeight - chatMessagesDiv.clientHeight;
                const isScrolledToBottom = (maxScrollPos - currentScrollPos) <= 1; // Toleransi 1px

                let messagesHtml = '';
                data.messages.forEach(msg => {
                    const isSelf = currentUserId && msg.user_id === currentUserId; // Cek apakah pesan dari user ini
                    messagesHtml += `
                        <div class="chat-message ${isSelf ? 'self' : ''}">
                            <div class="chat-message-sender">${msg.sender_name}:</div>
                            <div class="chat-message-content">${msg.message}</div>
                            <div class="chat-message-time">${new Date(msg.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}</div>
                        </div>
                    `;
                });
                chatMessagesDiv.innerHTML = messagesHtml;

                // Hanya scroll ke bawah jika pengguna sudah di bawah atau ada pesan baru
                if (isScrolledToBottom || data.messages.length > chatMessagesDiv.children.length) {
                    chatMessagesDiv.scrollTop = chatMessagesDiv.scrollHeight;
                }
            })
            .catch(error => {
                console.error('Error fetching chat messages:', error);
                // Tambahkan penanganan error spesifik untuk JSON parsing
                if (error instanceof SyntaxError && error.message.includes('JSON')) {
                    console.error('Respon bukan JSON yang valid. Mungkin terjadi redirect atau error server.');
                }
            });
    }

    // --- Event Listeners untuk chat ---
    if (sendMessageBtn) {
        sendMessageBtn.addEventListener('click', sendMessage);
    }
    if (chatInput) {
        chatInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });
    }

    function sendMessage() {
        const messageText = chatInput.value.trim();
        if (messageText === '') {
            return;
        }

        fetch('{{ route('api.chat.send') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken // Kirim token CSRF
            },
            body: JSON.stringify({ message: messageText })
        })
        .then(response => {
            if (!response.ok) {
                // Jika respons bukan OK (misal 401 Unauthorized, 419 CSRF token mismatch)
                // Coba parse sebagai JSON terlebih dahulu, jika gagal, baca sebagai teks
                return response.text().then(text => {
                    if (response.redirected && response.url.includes('/login')) {
                         alert('Sesi Anda telah berakhir. Mohon login kembali.');
                         window.location.href = response.url; // Arahkan ke halaman login
                         return; // Hentikan
                    }
                    try {
                        const errorJson = JSON.parse(text);
                        throw new Error(errorJson.message || 'Gagal mengirim pesan.');
                    } catch (e) {
                        // Jika bukan JSON, mungkin itu halaman HTML error Laravel
                        console.error("Non-JSON response received:", text);
                        throw new Error('Terjadi kesalahan yang tidak terduga dari server. Mohon coba lagi.');
                    }
                });
            }
            return response.json(); // Asumsi sukses akan selalu JSON
        })
        .then(data => {
            if (data.status === 'success') {
                chatInput.value = ''; // Kosongkan input
                // Polling akan mengambil pesan baru, jadi tidak perlu refresh manual
            } else {
                alert('Gagal mengirim pesan.');
            }
        })
        .catch(error => {
            console.error('Error sending message:', error);
            alert('Terjadi kesalahan: ' + error.message);
        });
    }

    // --- Mulai polling ---
    // Polling setiap 3 detik untuk chat dan kursus
    setInterval(fetchKursusData, 3000);
    setInterval(fetchChatMessages, 3000);

    // Panggil sekali saat halaman dimuat untuk data awal
    document.addEventListener('DOMContentLoaded', () => {
        fetchKursusData();
        fetchChatMessages();
    });
</script>
</body>
</html>
