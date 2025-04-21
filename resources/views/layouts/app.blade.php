<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="E-Tanggapan AASI &amp; E-Tanggapan Anggota Asosiasi Asuransi Syariah Indonesia">
	<meta name="author" content="Asosiasi Asuransi Syariah Indonesia">
	<meta name="keywords" content="e-tanggapan, e-tanggapan aasi, e-tanggapan.aasi, e-tanggapan.aasi.or.id">

	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link rel="shortcut icon" href="img/icons/icon-48x48.png" />

	<link rel="canonical" href="https://e-tanggapan.test" />
    <title>E-Tanggapan AASI</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="{{ asset('frontend/css/app.css') }}" rel="stylesheet">
    <!-- Bootstrap JS (sudah kamu punya) -->
    @role('Main Admin')
    <script src="{{ asset('frontend/js/app.js') }}"></script>
    @else
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @endrole
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="wrapper">

        @role('Main Admin')
            @include('layouts.sidebar') <!-- sidebar hanya untuk Main Admin -->
        @endrole

        <div class="main">
            <nav class="navbar navbar-expand-lg navbar-light navbar-bg">
                <div class="container-fluid">
                    @role('Main Admin')
                        <!-- Sidebar toggle untuk Main Admin -->
                        <a class="sidebar-toggle js-sidebar-toggle">
                            <i class="hamburger align-self-center"></i>
                        </a>
                    @else
                        <!-- Logo dan Brand -->
                        <a class="navbar-brand d-flex align-items-center" href="#">
                            <img src="{{ asset('img/logo.png') }}" alt="Logo" height="32" class="me-2">
                            <span class="fw-bold fs-5">E-Tanggapan</span>
                        </a>
            
                        <!-- Toggle button -->
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu"
                            aria-controls="navbarMenu" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                    @endrole
            
                    @role('Main Admin')
                        <!-- Profile dropdown untuk Main Admin (di kanan navbar) -->
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" data-bs-toggle="dropdown" role="button"
                                    aria-expanded="false">
                                    <img src="{{ asset('frontend/img/avatars/avatar.jpg') }}" class="avatar img-fluid rounded me-1" alt="User" />
                                    <span class="text-dark">Hi, {{ Auth::user()->name }}</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li class="dropdown-item text-wrap">
                                        <strong>{{ Auth::user()->company_name }}</strong><br>
                                        <small class="text-muted">{{ Auth::user()->department }}</small>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="#" onclick="showChangePasswordForm()">
                                            <i data-feather="key" class="me-1"></i> Ganti Password
                                        </a>
                                    </li>  
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="#" onclick="confirmLogout()">
                                        <i class="align-middle me-1" data-feather="log-out"></i> Log out
                                    </a></li>
                                </ul>
                            </li>
                        </ul>
                    @else
                        <!-- Menu + profile untuk Non-Main Admin -->
                        <div class="collapse navbar-collapse justify-content-between" id="navbarMenu">
                            <!-- Navigasi tengah -->
                            <ul class="navbar-nav mx-auto">
                                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Beranda</a></li>
                                <li class="nav-item"><a class="nav-link" href="#">Tanggapan Berlangsung</a></li>
                                <li class="nav-item"><a class="nav-link" href="#">Tanggapan Final</a></li>
                                <li class="nav-item"><a class="nav-link" href="#">Laporan</a></li>
                            </ul>
            
                            <!-- Profile dropdown -->
                            <ul class="navbar-nav">
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" data-bs-toggle="dropdown" role="button"
                                        aria-expanded="false">
                                        <img src="{{ asset('frontend/img/avatars/avatar.jpg') }}" class="avatar img-fluid rounded me-1" alt="User" />
                                        <span class="text-dark">Hi, {{ Auth::user()->name }}</span>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li class="dropdown-item text-wrap">
                                            <strong>{{ Auth::user()->company_name }}</strong><br>
                                            <small class="text-muted">{{ Auth::user()->department }}</small>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item" href="#" onclick="showChangePasswordForm()">
                                                <i data-feather="key" class="me-1"></i> Ganti Password
                                            </a>
                                        </li>                                        
                                        <li><a class="dropdown-item" href="#" onclick="confirmLogout()">
                                            <i class="align-middle me-1" data-feather="log-out"></i> Log out
                                        </a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    @endrole
                </div>
            </nav>            
            <main class="content">
                <div class="container-fluid p-0">
                    @yield('content')
                </div>
            </main>
            @include('layouts.footer')
        </div>
    </div>
    @include('sweetalert::alert')
    @if ($errors->any())
        <script>
            Swal.fire({
                title: 'Validation Error!',
                html: `{!! implode('<br>', $errors->all()) !!}`,
                icon: 'error',
                confirmButtonText: 'OK'
            });
        </script>
    @endif
    @if (session('alert'))
    <script>
        Swal.fire({
            icon: '{{ session("alert_type") }}',
            title: '{{ session("alert_title") }}',
            text: '{{ session("alert") }}',
            confirmButtonText: 'OK'
        });
    </script>
    @endif
    <script>
        function confirmLogout() {
            Swal.fire({
                title: 'Are you sure?',
                text: "You will be logged out!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, logout!',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            });
        }
    </script>
    <script>
        function showChangePasswordForm() {
            Swal.fire({
                title: 'Ganti Password',
                html:
                    `<input type="password" id="new_password" class="swal2-input" placeholder="Password Baru">
                    <input type="password" id="new_password_confirmation" class="swal2-input" placeholder="Konfirmasi Password Baru">`,
                focusConfirm: false,
                confirmButtonText: 'Simpan',
                showCancelButton: true,
                cancelButtonText: 'Batal',
                preConfirm: () => {
                    const password = document.getElementById('new_password').value;
                    const confirm = document.getElementById('new_password_confirmation').value;
    
                    if (!password || !confirm) {
                        Swal.showValidationMessage('Semua field wajib diisi!');
                        return false;
                    }
    
                    if (password.length < 8) {
                        Swal.showValidationMessage('Password baru minimal 8 karakter!');
                        return false;
                    }
    
                    if (password !== confirm) {
                        Swal.showValidationMessage('Konfirmasi password tidak cocok!');
                        return false;
                    }
    
                    return { password, confirm };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const data = result.value;
    
                    fetch("{{ route('password.update.self') }}", {
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            new_password: data.password,
                            new_password_confirmation: data.confirm
                        })
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.status === 'success') {
                            Swal.fire('Berhasil!', res.message, 'success');
                        } else {
                            Swal.fire('Gagal!', res.message, 'error');
                        }
                    })
                    .catch(() => {
                        Swal.fire('Gagal!', 'Terjadi kesalahan saat mengubah password.', 'error');
                    });
                }
            });
        }
    </script>
    
    <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display: none;">
        @csrf
    </form>
    <script>
        function confirmDelete(formId) {
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: "Data tidak dapat dikembalikan setelah dihapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }
    </script>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css">
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>    
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
    <!-- Inisialisasi DataTables -->
    <script>
        $(document).ready(function () {
            $('#alltable').DataTable();
        });
    </script>
    <script src="https://unpkg.com/feather-icons"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            feather.replace();
        });
    </script>    
</body>
</html>
