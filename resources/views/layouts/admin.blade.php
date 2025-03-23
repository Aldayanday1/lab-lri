<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <link rel="stylesheet" href="{{ asset('css/layouts/sidebar-admin.css') }}">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

    <!-- Include the Notyf CSS file -->
    <link href="https://cdn.jsdelivr.net/npm/notyf@3.10.0/notyf.min.css" rel="stylesheet">

    <!-- Include the Notyf JavaScript file -->
    <script src="https://cdn.jsdelivr.net/npm/notyf@3.10.0/notyf.min.js"></script>

    <!-- Bootstrap JavaScript (Pastikan hanya satu) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Sweetalert Pop up Logout -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <!-- Profile Info -->
        <div class="p-3 profile-info">
            <i class="fas fa-user-circle fa-3x"></i> <!-- Ukuran 4x lebih besar -->

            <div>
                <strong>{{ Auth::user()->name }}</strong>
                <small>{{ Auth::user()->role }}</small>
            </div>
        </div>

        <!-- Garis Pembatas -->
        <hr class="sidebar-divider">

        <!-- Menu Utama -->
        <a href="{{ route('admin.dashboard') }}" class="{{ Request::is('admin/dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="{{ route('admin.verify-users') }}" class="{{ Request::is('admin/verify-users') ? 'active' : '' }}">
            <i class="fas fa-user-check"></i> Verifikasi Pengguna
        </a>

        @if (auth()->user()->role === 'superadmin')
            <a href="{{ route('admin.manage-users') }}" class="{{ Request::is('admin/manage-users') ? 'active' : '' }}">
                <i class="fas fa-users"></i> Manajemen Pengguna
            </a>
        @endif

        <!-- Menu Tambahan (Hanya Teks & Ikon Sementara) -->

        <div class="sidebar-item">
            <i class="fas fa-flask"></i> Layanan Lab
        </div>
        <div class="sidebar-item">
            <i class="fas fa-calendar-check"></i> Pemesanan Lab
        </div>

        <!-- Logout Button -->
        <a href="#" class="logout-btn" id="logout-confirm">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>

        <!-- Form Logout -->
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </div>

    <!-- Toggle Button -->
    <div class="menu-toggle" id="menu-toggle">
        <i class="fas fa-bars"></i>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        /* ------------- SWALALERT LOGOUT ---------- */

        document.getElementById('logout-confirm').addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: "<h3 style='font-size:27px;'>Konfirmasi Logout</h3>",
                html: "<p style='font-size:17px;'>Apakah Anda yakin ingin logout?</p>",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "<span style='font-size:14px;'>Ya, Logout</span>",
                cancelButtonText: "<span style='font-size:14px;'>Batal</span>"
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            });
        });
    </script>

    <script>
        /* ------------- SIDEBAR - OPEN CLOSE () ---------- */

        document.addEventListener('DOMContentLoaded', function() {
            let sidebar = document.getElementById('sidebar');
            let toggleIcon = document.querySelector('.menu-toggle i');
            let body = document.body; // Ambil elemen body

            // Cek status sidebar di localStorage saat halaman selesai dimuat
            if (localStorage.getItem('sidebarOpen') === 'true') {
                sidebar.classList.add('open');
                toggleIcon.classList.remove('fa-bars');
                toggleIcon.classList.add('fa-times');
                toggleIcon.style.color = 'white';
                body.classList.add('no-scroll'); // Cegah scroll jika sidebar terbuka
            } else {
                sidebar.classList.remove('open');
                toggleIcon.classList.remove('fa-times');
                toggleIcon.classList.add('fa-bars');
                toggleIcon.style.color = 'black';
                body.classList.remove('no-scroll'); // Izinkan scroll jika sidebar tertutup
            }

            // Event listener untuk toggle button
            document.getElementById('menu-toggle').addEventListener('click', function() {
                sidebar.classList.toggle('open');

                // Update icon dan warna
                toggleIcon.classList.add('rotate');
                setTimeout(() => {
                    if (sidebar.classList.contains('open')) {
                        toggleIcon.classList.remove('fa-bars');
                        toggleIcon.classList.add('fa-times');
                        toggleIcon.style.color = 'white';
                        localStorage.setItem('sidebarOpen', 'true');
                        body.classList.add('no-scroll'); // Cegah scroll saat sidebar terbuka
                    } else {
                        toggleIcon.classList.remove('fa-times');
                        toggleIcon.classList.add('fa-bars');
                        toggleIcon.style.color = 'black';
                        localStorage.setItem('sidebarOpen', 'false');
                        body.classList.remove('no-scroll'); // Izinkan scroll saat sidebar tertutup
                    }
                }, 150);

                setTimeout(() => {
                    toggleIcon.classList.remove('rotate');
                }, 300);
            });

            // **Tambahkan event listener untuk setiap menu agar sidebar tertutup saat menu diklik**
            document.querySelectorAll('#sidebar a').forEach(menu => {
                menu.addEventListener('click', function() {
                    sidebar.classList.remove('open'); // Tutup sidebar
                    localStorage.setItem('sidebarOpen', 'false'); // Perbarui status di localStorage
                    body.classList.remove('no-scroll'); // Izinkan scroll kembali

                    // Perbarui icon ke menu toggle
                    toggleIcon.classList.remove('fa-times');
                    toggleIcon.classList.add('fa-bars');
                    toggleIcon.style.color = 'black';
                });
            });
        });
    </script>
</body>

</html>
