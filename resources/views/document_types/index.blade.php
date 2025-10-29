@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-3">Jenis Dokumen</h1>

    <div class="mb-3">
        <a href="{{ route('admin.document-types.create') }}" class="btn btn-primary">+ Tambah Jenis</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered text-start" id="alltable">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Nama</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($types as $i => $type)
                        <tr>
                            <td class="text-center">{{ $i + 1 }}</td>
                            <td class="text-justify">{{ $type->name }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.document-types.edit', $type->slug) }}" class="badge bg-warning d-inline-flex align-items-center">
                                    <span data-feather="edit"></span>
                                </a>
                                <form id="delete-form-{{ $type->slug }}" action="{{ route('admin.document-types.destroy', $type->slug) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmDelete('delete-form-{{ $type->slug }}')" class="badge bg-danger border-0 d-inline-flex align-items-center">
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
