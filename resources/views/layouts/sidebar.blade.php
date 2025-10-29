<nav id="sidebar" class="sidebar js-sidebar">
    <div class="sidebar-content js-simplebar">
        <a class="sidebar-brand" href="{{ route('home') }}">
            <span class="align-middle">E-Tanggapan</span>
        </a>

        <li class="sidebar-item {{ Request::is('home') ? 'active' : '' }}">
            <a class="sidebar-link" href="{{ route('home') }}">
                <i class="align-middle" data-feather="home"></i> <span class="align-middle">Dashboard</span>
            </a>
        </li>

        <ul class="sidebar-nav">
            <li class="sidebar-header">
                Manajemen Tanggapan
            </li>
            <li class="sidebar-item {{ Request::routeIs('admin.document-types.*') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.document-types.index') }}">
                    <i class="align-middle" data-feather="file"></i> <span class="align-middle">Jenis Dokumen</span>
                </a>
            </li>
            <li class="sidebar-item {{ Request::routeIs('admin.documents.*') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.documents.index') }}">
                    <i class="align-middle" data-feather="file"></i> <span class="align-middle">Dokumen</span>
                </a>
            </li>
            <li class="sidebar-item {{ Request::routeIs('admin.responds.*') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.responds.today') }}">
                    <i class="align-middle" data-feather="message-square"></i> <span class="align-middle">Tanggapan Hari Ini</span>
                </a>
            </li>
            <li class="sidebar-item {{ request()->routeIs('admin.picnorespond') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.picnorespond') }}">
                    <i class="align-middle" data-feather="x-square"></i> <span class="align-middle">PIC No Respond</span>
                </a>
            </li>
            <li class="sidebar-item {{ Request::routeIs('laporan.*') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('laporan.index') }}">
                    <i class="align-middle" data-feather="download"></i> <span class="align-middle">Laporan</span>
                </a>
            </li>
            
            <li class="sidebar-header">
                Manajemen Akun
            </li>
            <li class="sidebar-item {{ Request::routeIs('admin.users.*') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.users.index') }}">
                    <i class="align-middle" data-feather="user"></i> <span class="align-middle">Akun</span>
                </a>
            </li>

            <li class="sidebar-item {{ Request::routeIs('admin.roles.*') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.roles.index') }}">
                    <i class="align-middle" data-feather="shield"></i> <span class="align-middle">Role</span>
                </a>
            </li>
            
            <li class="sidebar-item {{ Request::routeIs('admin.permissions.*') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.permissions.index') }}">
                    <i class="align-middle" data-feather="lock"></i> <span class="align-middle">Hak Akses</span>
                </a>
            </li>
        </ul>
    </div>
</nav>