<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $users = User::all(); // Mengambil semua data pengguna
        return view('admin.dashboard', compact('users'));
    }
}
