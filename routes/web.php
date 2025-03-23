<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\ManageUserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//------------

// Superadmin - CRUD Akun
Route::middleware(['auth', 'superadmin'])->group(function () {
    Route::put('/admin/update-role/{id}', [AdminUserController::class, 'updateRole'])->name('admin.update-role');

    Route::get('/admin/manage-users', [ManageUserController::class, 'index'])->name('admin.manage-users');
    Route::post('/admin/manage-users', [ManageUserController::class, 'store'])->name('admin.users.store');
    Route::put('/admin/manage-users/{id}', [ManageUserController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/manage-users/{id}', [ManageUserController::class, 'destroy'])->name('admin.users.destroy');
});

// Route untuk admin, hanya dapat diakses oleh pengguna dengan peran admin (yg sudah login )
Route::middleware(['auth', 'admin'])->group(function () {

    // Route untuk menampilkan dan memverifikasi pengguna pending
    Route::get('/admin/verify-users', [AdminUserController::class, 'index'])->name('admin.verify-users');
    Route::put('/admin/verify-users/{id}', [AdminUserController::class, 'verify'])->name('admin.verify');
});

// Route untuk pengguna yang telah login dan terverifikasi
Route::middleware(['auth', 'verified'])->group(function () {

    // Route dashboard untuk pengguna umum
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Dashboard khusus admin (Menggunakan Controller)
    Route::middleware('admin')->group(function () {
        Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    });
});

Route::post('/clear-errors', function () {
    session()->forget('errors');
    session()->forget('editUserId');
    return response()->json(['status' => 'success']);
})->name('clear.errors');


require __DIR__.'/auth.php';
