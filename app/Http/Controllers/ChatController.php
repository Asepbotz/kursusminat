<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\Kursus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Import Log facade untuk debugging

class ChatController extends Controller
{
    /**
     * Mengambil pesan chat terbaru.
     */
    public function fetchMessages()
    {
        try {
            // Mengambil 50 pesan chat terbaru, diurutkan berdasarkan yang terbaru
            $messages = ChatMessage::with('user')
                                   ->orderBy('created_at', 'asc')
                                   ->limit(50)
                                   ->get();

            return response()->json([
                'messages' => $messages->map(function ($message) {
                    // Mengirim nama pengguna atau 'Admin' jika role admin
                    // Menggunakan sender_name dari database jika user terkait tidak ada (misal: user_id null)
                    $senderName = $message->user ?
                        ($message->user->role === 'admin' ? 'Admin' : $message->user->name) :
                        $message->sender_name;

                    return [
                        'id' => $message->id,
                        'user_id' => $message->user_id,
                        'sender_name' => $senderName,
                        'message' => $message->message,
                        'created_at_human' => $message->created_at->diffForHumans(),
                        'created_at' => $message->created_at, // Mengirim timestamp asli
                    ];
                })
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching chat messages: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Failed to fetch messages.'], 500);
        }
    }

    /**
     * Menyimpan pesan chat baru.
     */
    public function sendMessage(Request $request)
    {
        try {
            $request->validate([
                'message' => 'required|string|max:1000',
            ]);

            $user = Auth::user();
            // Menggunakan nama yang lebih informatif untuk user biasa dan 'Admin' untuk admin
            // Jika tidak ada user login, gunakan 'Anonim' atau nama yang tersimpan
            $senderName = 'Anonim'; // Default value
            if ($user) {
                $senderName = ($user->role === 'admin' ? 'Admin' : $user->name);
            }

            $message = ChatMessage::create([
                'user_id' => $user ? $user->id : null,
                'sender_name' => $senderName,
                'message' => $request->message,
            ]);

            // Mengembalikan pesan yang baru disimpan
            return response()->json([
                'status' => 'success',
                'message' => [
                    'id' => $message->id,
                    'user_id' => $message->user_id,
                    'sender_name' => $senderName,
                    'message' => $message->message,
                    'created_at_human' => $message->created_at->diffForHumans(),
                    'created_at' => $message->created_at,
                ],
            ], 201); // Menggunakan status 201 Created
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error sending chat message: ' . $e->getMessage(), ['errors' => $e->errors()]);
            return response()->json(['error' => $e->errors()], 422); // Unprocessable Entity
        } catch (\Exception $e) {
            Log::error('Error sending chat message: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Failed to send message. Please try again.'], 500);
        }
    }

    /**
     * Mengambil data kursus terbaru untuk polling.
     */
    public function fetchKursus(Request $request, $subdomain)
    {
        try {
            $kursus = Kursus::where('kategori', $subdomain)->get();

            // Mengonversi data kursus menjadi format yang mudah dikonsumsi oleh JS
            $kursusData = $kursus->map(function ($k) {
                return [
                    'id' => $k->id,
                    'judul' => $k->judul,
                    'deskripsi' => $k->deskripsi,
                    'tanggal_mulai' => $k->tanggal_mulai ? \Carbon\Carbon::parse($k->tanggal_mulai)->format('d M Y') : 'N/A',
                    'tanggal_selesai' => $k->tanggal_selesai ? \Carbon\Carbon::parse($k->tanggal_selesai)->format('d M Y') : 'Lifetime',
                    'status' => $k->status, // Menggunakan accessor getStatusAttribute dari model
                ];
            });

            return response()->json(['kursus' => $kursusData]);
        } catch (\Exception $e) {
            Log::error('Error fetching kursus data: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Failed to fetch kursus data.'], 500);
        }
    }
}
