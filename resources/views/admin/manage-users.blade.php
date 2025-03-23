@extends('layouts.admin')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/admin/manage-users.css') }}">

    @if (session('success'))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var notyf = new Notyf({
                    duration: 3000,
                    position: {
                        x: 'right',
                        y: 'top',
                    }
                });
                notyf.success("{{ session('success') }}");
            });
        </script>
    @endif

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".btn-edit").forEach(button => {
                button.addEventListener("click", function() {
                    let userStatus = this.getAttribute("data-status");
                    let userId = this.getAttribute("data-id");
                    let userName = this.getAttribute("data-name"); // Ambil nama user
                    let modalId = "#editModal" + userId;

                    if (userStatus === "pending") {
                        var notyf = new Notyf({
                            duration: 3000,
                            position: {
                                x: 'right',
                                y: 'top'
                            }
                        });
                        notyf.error(
                            `Pengguna "${userName}" belum terverifikasi. Harap verifikasi sebelum mengedit.`
                        );
                    } else {
                        let modal = new bootstrap.Modal(document.querySelector(modalId));
                        modal.show();
                    }
                });
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var toasts = document.querySelectorAll('.toast');
            toasts.forEach(function(toastEl) {
                var toast = new bootstrap.Toast(toastEl);
                toast.show();
            });
        });
    </script>

    <!-- Container Toast -->
    <div class="top-0 p-3 toast-container position-fixed end-0">
        @if (session('validation_errors'))
            @foreach (session('validation_errors') as $error)
                <div class="mb-2 text-white border-0 toast show align-items-center bg-danger" role="alert"
                    aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            {{ $error }}
                        </div>
                        <button type="button" class="m-auto btn-close btn-close-white me-2"
                            data-bs-dismiss="toast"></button>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <div class="mt-4 container-fluid">
        <!-- Header Card -->
        <div class="border-0 shadow-lg card">
            <div class="text-white card-header bg-secondary d-flex justify-content-between align-items-center">
                <h5 class="m-2">
                    <i class="fas fa-users" style="font-size: 14px; margin-right: 3px;"></i>
                    <span style="font-size: 15px;">Kelola Pengguna</span>
                </h5>
            </div>
        </div>

        <!-- Notes / Informasi -->
        <div class="mt-3 border-0 shadow-lg card">
            <div class="p-3 bg-light border-bottom">
                <p class="m-2 text-muted" style="font-size: 14px;">
                    <i class="fas fa-info-circle" style="font-size: 14px; margin-right: 10px;"></i>
                    Halaman ini menampilkan daftar pengguna dalam sistem. Anda dapat
                    <strong>menambah</strong> pengguna baru, <strong>mengedit</strong> informasi pengguna,
                    serta <strong>menghapus</strong> akun pengguna yang tidak lagi aktif.
                </p>
            </div>
        </div>

        <!-- Tabel Pengguna -->
        <div class="mt-3 border-0 shadow-lg card">
            <div class="p-3 card-body">
                <!-- Search & Filter dalam Satu Baris -->
                <div class="flex-wrap gap-2 mb-3 d-flex align-items-center">
                    <!-- Form Pencarian (Lebih Lebar) -->
                    <form method="GET" action="{{ route('admin.manage-users') }}" class="d-flex flex-grow-1">
                        <div class="input-group w-100">
                            <input type="text" name="search" class="form-control search-input"
                                placeholder="Cari nama atau username..." value="{{ request('search') }}"
                                style="font-size: 14px; padding: 8px 12px;">
                            <button class="btn search-button" type="submit" style="font-size: 12px; padding: 10px 14px;">
                                <i class="fas fa-search"></i> Cari
                            </button>
                        </div>
                    </form>

                    <!-- Dropdown Filter Role -->
                    <div>
                        <div class="dropdown w-100">
                            <button class="btn glass-button dropdown-toggle w-100 text-start icon-role" type="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-users filter-icon icon-role"></i>
                                <span
                                    class="text-role">{{ request('role') ? ucfirst(request('role')) : 'Role User' }}</span>
                            </button>
                            <ul class="dropdown-menu w-100" style="font-size: 14px;">
                                <li>
                                    <a class="dropdown-item d-flex align-items-center"
                                        href="{{ route('admin.manage-users', array_merge(request()->query(), ['role' => ''])) }}"
                                        style="gap: 10px;">
                                        <i class="fas fa-list text-muted"></i> Semua
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center"
                                        href="{{ route('admin.manage-users', array_merge(request()->query(), ['role' => 'admin'])) }}"
                                        style="gap: 10px;">
                                        <i class="fas fa-user-shield text-muted"></i> Admin
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center"
                                        href="{{ route('admin.manage-users', array_merge(request()->query(), ['role' => 'user'])) }}"
                                        style="gap: 10px;">
                                        <i class="fas fa-user text-muted"></i> User
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Dropdown Filter Status -->
                    <div>
                        <div class="dropdown w-100">
                            <button class="btn glass-button dropdown-toggle w-100 text-start icon-status" type="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-clipboard-check filter-icon icon-status"></i>
                                <span
                                    class="text-status">{{ request('status') ? ucfirst(request('status')) : 'Filter Status' }}</span>
                            </button>
                            <ul class="dropdown-menu w-100" style="font-size: 14px;">
                                <li>
                                    <a class="dropdown-item d-flex align-items-center"
                                        href="{{ route('admin.manage-users', array_merge(request()->query(), ['status' => ''])) }}"
                                        style="gap: 10px;">
                                        <i class="fas fa-list text-muted"></i> Semua
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center"
                                        href="{{ route('admin.manage-users', array_merge(request()->query(), ['status' => 'active'])) }}"
                                        style="gap: 10px;">
                                        <i class="fas fa-check-circle text-muted"></i> Active
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center"
                                        href="{{ route('admin.manage-users', array_merge(request()->query(), ['status' => 'pending'])) }}"
                                        style="gap: 10px;">
                                        <i class="fas fa-clock text-muted"></i> Pending
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Tombol Tambah Pengguna -->
                    <button class="btn glass-button d-flex align-items-center justify-content-center icon-add"
                        data-bs-toggle="modal" data-bs-target="#addUserModal">
                        <i class="fas fa-user-plus filter-icon icon-add"></i>
                        <span class="text-add">Tambah</span>
                    </button>

                </div>

                <div class="table-responsive" style="width: 100%;">
                    <table class="table mb-0 table-bordered table-hover table-striped">
                        <thead class="text-center">
                            <tr style="font-size: 14px;">
                                <th class="py-2">Nama</th>
                                <th class="py-2">Username</th>
                                <th class="py-2">Status</th>
                                <th class="py-2">Role</th>
                                <th class="py-2 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                @if ($user->role !== 'superadmin')
                                    <tr>
                                        <td style="font-size: 14px; color: #606970;">{{ $user->name }}</td>
                                        <td style="font-size: 14px; color: #606970;">{{ $user->username }}</td>
                                        <td class="text-center">
                                            <span
                                                class="status-badge {{ $user->status == 'active' ? 'status-active' : 'status-inactive' }}">
                                                <i
                                                    class="status-icon fas {{ $user->status == 'active' ? 'fa-check-circle' : 'fa-exclamation-circle' }}"></i>
                                                {{ ucfirst($user->status) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="role-badge {{ $user->role == 'admin' ? 'role-admin' : 'role-user' }}">
                                                <i
                                                    class="role-icon fas {{ $user->role == 'admin' ? 'fa-user-shield' : 'fa-user' }}"></i>
                                                {{ ucfirst($user->role) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="action-buttons">
                                                <!-- Tombol Edit -->
                                                <button class="icon-btn btn-edit" data-id="{{ $user->id }}"
                                                    data-status="{{ $user->status }}" data-name="{{ $user->name }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>

                                                <!-- Tombol Hapus -->
                                                <form action="{{ route('admin.users.destroy', $user->id) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="icon-btn btn-delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Tidak ada data pengguna.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Navigasi Pagination -->
                <div class="mt-3 d-flex justify-content-center">
                    {{ $users->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    {{-- <div class="modal fade" id="editModal{{ $user->id }}" tabindex="-1"
        aria-labelledby="editModalLabel{{ $user->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Pengguna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.users.update', $user->id) }}" method="POST"
                        onsubmit="return confirmSubmit(event)">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label>Nama</label>
                            <!-- Nama (bisa diubah setelah verifikasi) -->
                            <input type="text" class="form-control" name="name" value="{{ $user->name }}"
                                {{ $user->status === 'pending' ? 'disabled' : '' }} required>
                        </div>
                        <div class="mb-3">
                            <label>Username</label>
                            <!-- Username (tetap terkunci setelah verifikasi) -->
                            <input type="text" class="form-control" name="username" value="{{ $user->username }}"
                                disabled>
                        </div>
                        <div class="mb-3">
                            <label>Password (Opsional)</label>
                            <!-- Password (bisa diubah setelah verifikasi) -->
                            <input type="password" class="form-control" name="password"
                                {{ $user->status === 'pending' ? 'disabled' : '' }}>
                        </div>
                        <div class="mb-3">
                            <label>Role</label>
                            <select name="role" class="form-select" id="roleSelect{{ $user->id }}"
                                {{ $user->status === 'pending' ? 'disabled' : '' }}>
                                <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User
                                </option>
                                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin
                                </option>
                            </select>
                            @if ($user->status === 'pending')
                                <small class="text-danger">Verifikasi terlebih dahulu untuk
                                    mengubah role.</small>
                            @endif
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div> --}}

    <!-- Modal Tambah Pengguna -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="p-4 border-0 shadow-sm modal-content">
                <!-- Header -->
                <div class="border-0 modal-header d-flex flex-column align-items-start">
                    <h5 class="mb-1 modal-title text-secondary fw-bold fs-5">
                        Tambah Pengguna
                    </h5>
                    <p class="mb-0 text-muted fs-6">
                        Silakan isi data untuk menambahkan pengguna baru.
                    </p>
                </div>

                <!-- Body -->
                <div class="modal-body">
                    <form action="{{ route('admin.users.store') }}" method="POST">
                        @csrf
                        <div class="mb-3 d-flex align-items-center">
                            <i class="bi bi-person text-muted me-2 fs-5"></i>
                            <label class="form-label text-muted me-3" style="width: 100px;">Nama</label>
                            <input type="text" name="name"
                                class="px-3 border form-control border-1 border-secondary-subtle w-100"
                                placeholder="Contoh: Budi Santoso" required>
                        </div>

                        <div class="mb-3 d-flex align-items-center">
                            <i class="bi bi-person-circle text-muted me-2 fs-5"></i>
                            <label class="form-label text-muted me-3" style="width: 100px;">Username</label>
                            <input type="text" name="username"
                                class="px-3 border form-control border-1 border-secondary-subtle w-100"
                                placeholder="Contoh: budi123" required>
                        </div>

                        <div class="mb-3 d-flex align-items-center">
                            <i class="bi bi-key text-muted me-2 fs-5"></i>
                            <label class="form-label text-muted me-3" style="width: 100px;">Password</label>
                            <input type="password" name="password"
                                class="px-3 border form-control border-1 border-secondary-subtle w-100"
                                placeholder="Minimal 6 karakter (contoh: rahasia123)" required>
                        </div>

                        <div class="mb-4 d-flex align-items-center">
                            <i class="bi bi-person-badge text-muted me-2 fs-5"></i>
                            <label class="form-label text-muted me-3" style="width: 100px;">Role</label>
                            <select name="role" class="px-3 border form-select border-1 border-secondary-subtle w-100"
                                required>
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>

                        <!-- Button -->
                        <div class="gap-2 d-grid" style="grid-template-columns: 1fr 1fr;">
                            <button type="submit"
                                class="py-2 text-white btn fw-bold d-flex align-items-center justify-content-center"
                                style="background-color: #007bff;">
                                <i class="bi bi-check-circle me-1 fs-6"></i> Simpan
                            </button>
                            <button type="button"
                                class="py-2 text-white btn fw-bold d-flex align-items-center justify-content-center"
                                style="background-color: #6c757d;" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle me-1 fs-6"></i> Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Pengguna -->
    @foreach ($users as $user)
        <div class="modal fade" id="editModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="p-4 border-0 shadow-sm modal-content">
                    <!-- Header -->
                    <div class="border-0 modal-header d-flex flex-column align-items-start">
                        <h5 class="mb-1 modal-title text-secondary fw-bold fs-5">Edit Pengguna</h5>
                        <p class="mb-0 text-muted fs-6">Perbarui informasi pengguna di bawah ini.</p>
                    </div>

                    <!-- Body -->
                    <div class="modal-body">
                        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3 d-flex align-items-center">
                                <i class="bi bi-person text-muted me-2 fs-5"></i>
                                <label class="form-label text-muted me-3" style="width: 100px;">Nama</label>
                                <input type="text" name="name" value="{{ $user->name }}"
                                    class="px-3 border form-control border-1 border-secondary-subtle w-100" required>
                            </div>

                            <div class="mb-3 d-flex align-items-center">
                                <i class="bi bi-person-circle text-muted me-2 fs-5"></i>
                                <label class="form-label text-muted me-3" style="width: 100px;">Username</label>
                                <input type="text"
                                    class="px-3 border form-control border-1 border-secondary-subtle w-100"
                                    value="{{ $user->username }}" disabled>
                            </div>

                            <div class="mb-3 d-flex align-items-center">
                                <i class="bi bi-key text-muted me-2 fs-5"></i>
                                <label class="form-label text-muted me-3" style="width: 100px;">Password</label>
                                <input type="password" name="password"
                                    class="px-3 border form-control border-1 border-secondary-subtle w-100 placeholder-italic"
                                    placeholder="Kosongkan jika tidak ingin mengubah">
                            </div>

                            <div class="mb-4 d-flex align-items-center">
                                <i class="bi bi-person-badge text-muted me-2 fs-5"></i>
                                <label class="form-label text-muted me-3" style="width: 100px;">Role</label>
                                <select name="role"
                                    class="px-3 border form-select border-1 border-secondary-subtle w-100" required>
                                    <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
                                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                </select>
                            </div>

                            <!-- Button -->
                            <div class="gap-2 d-grid" style="grid-template-columns: 1fr 1fr;">
                                <button type="submit"
                                    class="py-2 text-white btn fw-bold d-flex align-items-center justify-content-center"
                                    style="background-color: #007bff;">
                                    <i class="bi bi-check-circle me-1 fs-6"></i> Simpan Perubahan
                                </button>
                                <button type="button"
                                    class="py-2 text-white btn fw-bold d-flex align-items-center justify-content-center"
                                    style="background-color: #6c757d;" data-bs-dismiss="modal">
                                    <i class="bi bi-x-circle me-1 fs-6"></i> Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <script>
        function confirmSubmit(event) {
            event.preventDefault(); // Mencegah submit otomatis
            Swal.fire({
                title: "Konfirmasi",
                text: "Apakah Anda yakin ingin menyimpan perubahan?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, Simpan",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    event.target.submit(); // Submit form jika dikonfirmasi
                }
            });
        }
    </script>

    {{-- <script>
        document.addEventListener("DOMContentLoaded", function() {
            @if ($errors->any())
                var addUserModal = new bootstrap.Modal(document.getElementById('addUserModal'));
                addUserModal.show();
            @endif
        });
    </script> --}}

    <!-- Tambahkan SweetAlert jika belum ada -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection
