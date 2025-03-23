<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class ManageUserController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->role !== 'superadmin') {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki izin!');
        }

        // Ambil nilai pencarian dan filter dari request
        $search = $request->input('search');
        $role = $request->input('role');
        $status = $request->input('status');

        // Query pengguna
        $users = User::where('role', '!=', 'superadmin');

        // Filter berdasarkan pencarian (name & username)
        if ($search) {
            $users->where(function ($query) use ($search) {
                $query->where('name', 'like', "%$search%")
                    ->orWhere('username', 'like', "%$search%");
            });
        }

        // Filter berdasarkan role
        if ($role) {
            $users->where('role', $role);
        }

        // Filter berdasarkan status
        if ($status) {
            $users->where('status', $status);
        }

        // Urutkan berdasarkan waktu dibuat dan pagination
        $users = $users->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.manage-users', compact('users', 'search', 'role', 'status'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'superadmin') {
            return redirect()->route('admin.manage-users')->with('error', 'Anda tidak memiliki izin!');
        }

        $messages = [
            'name.required' => 'Nama wajib diisi.',
            'name.regex' => 'Nama hanya boleh mengandung huruf.',
            'name.max' => 'Nama tidak boleh lebih dari 255 karakter.',
            'username.required' => 'Username wajib diisi.',
            'username.string' => 'Username harus berupa teks.',
            'username.max' => 'Username tidak boleh lebih dari 255 karakter.',
            'username.unique' => 'Username sudah digunakan, silakan pilih username lain.',
            'password.required' => 'Password wajib diisi.',
            'password.string' => 'Password harus berupa teks.',
            'password.min' => 'Password minimal harus terdiri dari 6 karakter.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role yang dipilih tidak valid.',
        ];

        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'regex:/^[\p{L}\s]+$/u', 'max:255'],
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:user,admin',
        ], $messages);

        // Jika validasi gagal, kirim error ke session flash
        if ($validator->fails()) {
            return redirect()->route('admin.manage-users')
                ->withErrors($validator)
                ->withInput()
                ->with('validation_errors', $validator->errors()->all()); // Kirim semua error ke session
        }

        // Buat pengguna baru jika validasi berhasil
        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => 'active',
        ]);

        return redirect()->route('admin.manage-users')->with('success', 'User berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->role !== 'superadmin') {
            return redirect()->route('admin.manage-users')->with('error', 'Anda tidak memiliki izin!');
        }

        $user = User::findOrFail($id);

        $messages = [
            'name.required' => 'Nama wajib diisi.',
            'name.regex' => 'Nama hanya boleh mengandung huruf.',
            'name.max' => 'Nama tidak boleh lebih dari 255 karakter.',
            'password.string' => 'Password harus berupa teks.',
            'password.min' => 'Password minimal harus terdiri dari 6 karakter.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role yang dipilih tidak valid.',
        ];

        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'regex:/^[\p{L}\s]+$/u', 'max:255'],
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:user,admin',
        ], $messages);

        // Jika validasi gagal, kirim error ke session flash
        if ($validator->fails()) {
            return redirect()->route('admin.manage-users')
                ->withErrors($validator)
                ->withInput()
                ->with('validation_errors', $validator->errors()->all()); // Kirim error ke session untuk toast
        }

        // Update data, kecuali username
        $user->update([
            'name' => $request->name,
            'role' => $request->role,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
        ]);

        return redirect()->route('admin.manage-users')->with('success', 'User berhasil diperbarui.');
    }


    public function destroy($id)
    {
        if (Auth::user()->role !== 'superadmin') {
            return redirect()->route('admin.manage-users')->with('error', 'Anda tidak memiliki izin!');
        }

        User::findOrFail($id)->delete();
        return redirect()->route('admin.manage-users')->with('success', 'User berhasil dihapus.');
    }
}
