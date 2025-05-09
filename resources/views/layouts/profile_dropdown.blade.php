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
