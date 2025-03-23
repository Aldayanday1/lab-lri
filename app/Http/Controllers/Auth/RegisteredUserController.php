<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Services\TelegramService;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => 'user', // Default user role
            'status' => 'pending', // Menunggu verifikasi admin
        ]);

        event(new Registered($user));

    // Kirim Notifikasi ke Telegram
    $telegram = new TelegramService();
    $message = "🔔 <b>Pendaftaran Baru</b> 🔔\n\n" .
               "📌 <b>Nama:</b> {$user->name}\n" .
               "👤 <b>Username:</b> {$user->username}\n" .
               "⏳ <b>Status:</b> Menunggu verifikasi admin.";
    $telegram->sendMessage($message);

    return redirect()->route('login')->with('status', 'Akun berhasil dibuat! Silakan tunggu verifikasi admin.');
    }
}
