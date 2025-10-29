<!DOCTYPE html>
<html lang="en">
<head>
    @include('layouts.head')
</head>
<body>
    <div class="wrapper">
        @if(auth()->user()?->hasRole('Main Admin'))
            @include('layouts.sidebar')
        @endif

        <div class="main">
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light navbar-bg">
                <div class="container-fluid">
                    @if(auth()->user()?->hasRole('Main Admin'))
                        <a class="sidebar-toggle js-sidebar-toggle">
                            <i class="hamburger align-self-center"></i>
                        </a>
                    @else
                        <a class="navbar-brand d-flex align-items-center" href="#">
                            <img src="{{ asset('frontend/img/logoaw2.png') }}" alt="Logo" height="32" class="me-2">
                            <span class="fw-bold fs-5">E-Tanggapan</span>
                        </a>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                    @endif

                    @if(auth()->user()?->hasRole('Main Admin'))
                        <ul class="navbar-nav ms-auto">
                            @include('layouts.profile_dropdown')
                        </ul>
                    @else
                        <div class="collapse navbar-collapse justify-content-between" id="navbarMenu">
                            <ul class="navbar-nav mx-auto">
                                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Beranda</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('berikan.tanggapan') }}">Berikan Tanggapan</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('tanggapan.selesai') }}">Tanggapan Selesai</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('laporan.index') }}">Rekap Tanggapan</a></li>
                            </ul>
                            <ul class="navbar-nav">
                                @include('layouts.profile_dropdown')
                            </ul>
                        </div>
                    @endif
                </div>
            </nav>

            <!-- Content -->
            <main class="content">
                <div class="container-fluid p-0">
                    @yield('content')
                </div>
            </main>

            @include('layouts.footer')
        </div>
    </div>

    <!-- Logout Form -->
    <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display: none;">@csrf</form>
    @include('sweetalert::alert')
    <!-- JS Dependencies -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
    @include('layouts.scripts')
</body>
</html>
