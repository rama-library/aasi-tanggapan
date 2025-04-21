@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-3">Manajemen Role</h1>

    <div class="mb-3">
        <a href="{{ route('roles.create') }}" class="btn btn-primary">+ Tambah Role</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered text-start" id="alltable">
                    <thead>
                        <tr>
                            <th class="text-start">No</th>
                            <th class="text-start">Nama Role</th>
                            <th class="text-start">Hak Akses</th>
                            <th class="text-start">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($roles as $role)
                            <tr>
                                <td class="text-start">{{ $loop->iteration }}</td>
                                <td>{{ $role->name }}</td>
                                <td>{{ $role->permissions->pluck('name')->join(', ') }}</td>                                                                                
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('roles.edit', $role->id) }}" class="badge bg-warning d-flex align-items-center">
                                            <span data-feather="edit" class="me-1"></span>Edit
                                        </a>
                                        <form id="delete-form-{{ $role->id }}" action="{{ route('roles.destroy', $role->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="confirmDelete('delete-form-{{ $role->id }}')" class="badge bg-danger border-0 d-flex align-items-center">
                                                <span data-feather="x-circle" class="me-1"></span>Hapus
                                            </button>
                                        </form>
                                    </div>
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