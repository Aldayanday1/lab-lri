<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Support\Facades\Auth; // Tambahkan ini

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search'); // Ambil input pencarian
        $status = $request->input('status'); // Ambil filter status

        $users = User::where('role', 'user')
                    ->when($search, function ($query) use ($search) {
                        $query->where('name', 'like', "%$search%")
                            ->orWhere('username', 'like', "%$search%");
                    })
                    ->when($status, function ($query) use ($status) {
                        $query->where('status', $status);
                    })
                    ->orderBy('created_at', 'desc') // Urutkan berdasarkan created_at terbaru
                    ->paginate(10); // Batasi 5 data per halaman

        return view('admin.verify-users', compact('users', 'search', 'status'));
    }

    public function verify($id)
    {
        $user = User::findOrFail($id);
        $user->status = 'active';
        $user->save();

        // Kirim Notifikasi ke Telegram
        $telegram = new TelegramService();
        $message = "✅ <b>Akun Terverifikasi</b> ✅\n\n" .
                   "📌 <b>Nama:</b> {$user->name}\n" .
                   "👤 <b>Username:</b> {$user->username}\n" .
                   "🎉 <b>Status:</b> Akun telah diverifikasi oleh Admin!";
        $telegram->sendMessage($message);

        return redirect()->route('admin.verify-users')->with('success', 'User berhasil diverifikasi.');
    }

    public function updateRole(Request $request, $id)
    {
        if (Auth::user()->role !== 'superadmin') {
            return redirect()->route('admin.verify-users')->with('error', 'Anda tidak memiliki izin untuk mengubah role!');
        }

        $user = User::findOrFail($id);
        $user->role = $request->role;
        $user->save();

        return redirect()->route('admin.verify-users')->with('success', 'Role pengguna berhasil diperbarui.');
    }
}
