@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-3">Daftar Akun</h1>

    <div class="mb-3">
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">+ Tambah Akun</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered text-start" id="alltable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Perusahaan</th>
                            <th>Role</th>
                            <th>Departemen</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $i => $user)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->company_name ?? '-' }}</td>
                            <td>{{ $user->roles->pluck('name')->join(', ') }}</td>
                            <td>{{ $user->department }}</td>
                            <td>
                                <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }}">
                                    {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="badge bg-warning d-inline-flex align-items-center">
                                    <span data-feather="edit"></span>
                                </a>
                                <a href="{{ route('admin.users.change-password', $user->id) }}" class="badge bg-primary d-inline-flex align-items-center">
                                    <span data-feather="key"></span>
                                </a>
                            
                                <form id="delete-form-{{ $user->id }}" action="{{ route('admin.users.destroy', $user->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmDelete('delete-form-{{ $user->id }}')" class="badge bg-danger border-0 d-inline-flex align-items-center">
                                        <span data-feather="x-circle"></span>
                                    </button>
                                </form>
                            </td>                            
                        </tr>
                        @endforeach
                    </tbody>
                </table>                
            </div>
        </div>
    </div>
</div>
@endsection
