@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-3">Daftar Dokumen</h1>

    <div class="mb-3">
        <a href="{{ route('admin.documents.create') }}" class="btn btn-primary">+ Tambah Dokumen</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered text-start" id="alltable">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Jenis Dokumen</th>
                            <th class="text-center">No Dokumen</th>
                            <th class="text-center">Perihal </th>
                            <th class="text-center">Tanggal Upload</th>
                            <th class="text-center">Respond Due Date</th>                                             
                            <th class="text-center">Review Due Date</th>                                                 
                            <th class="text-center">Uploader</th>                            
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($documents as $i => $document)
                        <tr>
                            <td class="text-center">{{ $i + 1 }}</td>
                            <td class="text-center">{{ $document->documentType->name ?? '-' }}</td>
                            <td class="text-center">{{ $document->no_document }}</td>
                            <td class="text-justify">{{ $document->perihal }}</td>
                            <td class="text-justify">{{ Carbon\Carbon::parse($document->created_at)->isoFormat('dddd, D MMMM Y') }}</td>
                            <td class="text-justify">{{ Carbon\Carbon::parse($document->due_date)->isoFormat('dddd, D MMMM Y') }} {{ $document->due_time }}</td>
                            <td class="text-justify">{{ Carbon\Carbon::parse($document->review_due_date)->isoFormat('dddd, D MMMM Y') }} {{ $document->review_due_time }}</td>                                
                            <td class="text-justify">{{ $document->author->name }}</td>
                            <td>
                                <a href="{{ route('admin.documents.show', $document->slug) }}" class="badge bg-info d-inline-flex align-items-center">
                                    <span data-feather="eye"></span>
                                </a>
                                <a href="{{ route('admin.documents.edit', $document->slug) }}" class="badge bg-warning d-inline-flex align-items-center">
                                    <span data-feather="edit"></span>
                                </a>
                                <form id="delete-form-{{ $document->slug }}" action="{{ route('admin.documents.destroy', $document->slug) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmDelete('delete-form-{{ $document->slug }}')" class="badge bg-danger border-0 d-inline-flex align-items-center">
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
