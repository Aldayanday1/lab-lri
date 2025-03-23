@extends('layouts.admin')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/admin/verify-users.css') }}">

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
    <div class="mt-4 container-fluid">
        <!-- Header Card -->
        <div class="border-0 shadow-lg card">
            <div class="text-white card-header bg-secondary d-flex justify-content-between align-items-center">
                <h5 class="m-2">
                    <i class="fas fa-user-check" style="font-size: 14px; margin-right: 3px;"></i>
                    <span style="font-size: 15px;">Verifikasi Pengguna</span>
                </h5>
                {{-- <span class="badge bg-primary">{{ count($users) }} Pengguna</span> --}}
            </div>
        </div>

        <!-- Notes / Informasi -->
        <div class="mt-3 border-0 shadow-lg card">
            <div class="p-3 bg-light border-bottom">
                <p class="m-2 text-muted" style="font-size: 14px;">
                    <i class="fas fa-info-circle" style="font-size: 14px; margin-right: 10px;"></i> Berikut adalah daftar
                    pengguna dalam sistem.
                    Anda dapat memverifikasi pengguna yang masih dalam status <strong>"Pending"</strong>.
                </p>
            </div>
        </div>

        <!-- Tabel Pengguna -->
        <div class="mt-3 border-0 shadow-lg card">
            <div class="p-3 card-body">
                <!-- Search & Filter dalam Satu Baris -->
                <div class="flex-wrap gap-2 mb-3 d-flex align-items-center">
                    <!-- Form Pencarian (Lebih Lebar) -->
                    <form method="GET" action="{{ route('admin.verify-users') }}" class="d-flex flex-grow-1">
                        <div class="input-group w-100">
                            <input type="text" name="search" class="form-control search-input"
                                placeholder="Cari nama atau username..." value="{{ request('search') }}"
                                style="font-size: 14px; padding: 8px 12px;">
                            <button class="btn search-button" type="submit" style="font-size: 12px; padding: 10px 14px;">
                                <i class="fas fa-search"></i> Cari
                            </button>
                        </div>
                    </form>

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
                                        href="{{ route('admin.verify-users', array_merge(request()->query(), ['status' => ''])) }}"
                                        style="gap: 10px;">
                                        <i class="fas fa-list text-muted"></i> Semua
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center"
                                        href="{{ route('admin.verify-users', array_merge(request()->query(), ['status' => 'active'])) }}"
                                        style="gap: 10px;">
                                        <i class="fas fa-check-circle text-muted"></i> Active
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center"
                                        href="{{ route('admin.verify-users', array_merge(request()->query(), ['status' => 'pending'])) }}"
                                        style="gap: 10px;">
                                        <i class="fas fa-clock text-muted"></i> Pending
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
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
                            @if ($users->isEmpty())
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Tidak ada data pengguna.</td>
                                </tr>
                            @else
                                @foreach ($users as $user)
                                    @if ($user->role === 'user')
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
                                                <span class="role-badge role-user">
                                                    <i class="role-icon fas fa-user"></i>
                                                    User
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                @if ($user->status === 'pending')
                                                    <button class="btn btn-verify" data-bs-toggle="modal"
                                                        data-bs-target="#confirmModal{{ $user->id }}">
                                                        <i class="fas fa-check"></i> Verifikasi
                                                    </button>

                                                    <!-- Modal Konfirmasi Verifikasi -->
                                                    <div class="modal fade" id="confirmModal{{ $user->id }}"
                                                        tabindex="-1" aria-labelledby="modalLabel{{ $user->id }}"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title"
                                                                        id="modalLabel{{ $user->id }}">
                                                                        Konfirmasi Verifikasi
                                                                    </h5>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="text-center modal-body">
                                                                    <i
                                                                        class="mb-3 fas fa-user-check fa-3x text-success"></i>
                                                                    <p>Apakah Anda yakin ingin memverifikasi pengguna <br>
                                                                        <strong>{{ $user->name }}</strong>?
                                                                    </p>
                                                                </div>
                                                                <div class="modal-footer justify-content-center">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-bs-dismiss="modal">
                                                                        <i class="fas fa-times"></i> Batal
                                                                    </button>
                                                                    <form action="{{ route('admin.verify', $user->id) }}"
                                                                        method="POST">
                                                                        @csrf
                                                                        @method('PUT')
                                                                        <button type="submit"
                                                                            class="btn btn-success btn-verify">
                                                                            <i class="fas fa-check-circle"></i> Verifikasi
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="badge badge-verify">
                                                        <i class="fas fa-check-circle"></i> Terverifikasi
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Tambahkan Navigasi Pagination -->
                <div class="mt-3 d-flex justify-content-center">
                    {{ $users->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
@endsection
