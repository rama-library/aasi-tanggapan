<nav class="navbar navbar-expand navbar-light navbar-bg">
    <a class="navbar-brand d-flex align-items-center me-4" href="#">
        <img src="{{ asset('frontend/img/logo.png') }}" alt="Logo" width="30" height="30" class="me-2">
        <strong class="text-dark">E-Tanggapan</strong>
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-between" id="mainNavbar">
        <!-- Menu Tengah -->
        <ul class="navbar-nav mx-auto">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('home') }}">Beranda</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('tanggapan-berlangsung') }}">Tanggapan Berlangsung</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('tanggapan-final') }}">Tanggapan Final</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Laporan</a>
            </li>
        </ul>
        
        <ul class="navbar-nav navbar-align">
            <li class="nav-item dropdown">
                <a class="nav-icon dropdown-toggle d-inline-block d-sm-none" href="#" data-bs-toggle="dropdown">
                    <i class="align-middle" data-feather="settings"></i>
                </a>
                <a class="nav-link dropdown-toggle d-none d-sm-inline-block" href="#" data-bs-toggle="dropdown">
                    <img src="{{ asset('frontend/img/avatars/avatar.jpg') }}" class="avatar img-fluid rounded me-1" alt="User" />
                    <span class="text-dark">
                        Hi, {{ Auth::user()->name }}
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-end">
                    <div class="dropdown-item text-wrap">
                        <strong>{{ Auth::user()->company_name }}</strong><br>
                        <small class="text-muted">{{ Auth::user()->department }}</small>
                    </div>
                    <div class="dropdown-divider"></div>
                    <li>
                        <a class="dropdown-item" href="#" onclick="changePassword()">
                            <i class="align-middle me-1" data-feather="key"></i> Change Password
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="#" onclick="confirmLogout()">
                            <i class="align-middle me-1" data-feather="log-out"></i> Log out
                        </a>    
                    </li>                    
                </div>
            </li>
        </ul>
    </nav>