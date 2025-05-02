@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-3">Manajemen Permission</h1>

    <div class="mb-3">
        <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary">+ Tambah Permission</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered text-start" id="alltable">
                    <thead>
                        <tr>
                            <th class="text-start">No</th>
                            <th class="text-start">Nama Permission</th>
                            <th class="text-start">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($permissions as $i => $permission)
                            <tr>
                                <td class="text-start">{{ $i + 1 }}</td>
                                <td>{{ $permission->name }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('permissions.edit', $permission->id) }}" class="badge bg-warning d-flex align-items-center">
                                            <span data-feather="edit" class="me-1"></span>Edit
                                        </a>
                                        <form id="delete-form-{{ $permission->id }}" action="{{ route('permissions.destroy', $permission->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="confirmDelete('delete-form-{{ $permission->id }}')" class="badge bg-danger border-0 d-flex align-items-center">
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