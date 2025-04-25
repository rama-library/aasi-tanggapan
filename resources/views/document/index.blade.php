@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-3">Daftar Dokumen</h1>

    <div class="mb-3">
        <a href="{{ route('documents.create') }}" class="btn btn-primary">+ Tambah Dokumen</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered text-start" id="alltable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>No Dokumen</th>
                            <th>Perihal </th>
                            <th>Tanggal Upload</th>
                            <th>Respond Due Date</th>                                             
                            <th>Review Due Date</th>                                                 
                            <th>Uploader</th>                            
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($documents as $i => $document)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $document->no_document }}</td>
                            <td>{{ $document->perihal }}</td>
                            <td>{{ Carbon\Carbon::parse($document->created_at)->isoFormat('dddd, D MMMM Y') }}</td>
                            <td>{{ Carbon\Carbon::parse($document->due_date)->isoFormat('dddd, D MMMM Y') }} {{ $document->due_time }}</td>
                            <td>{{ Carbon\Carbon::parse($document->review_due_date)->isoFormat('dddd, D MMMM Y') }} {{ $document->review_due_time }}</td>                                
                            <td>{{ $document->author->name }}</td>
                            <td>
                                <a href="{{ route('documents.show', $document->slug) }}" class="badge bg-info d-inline-flex align-items-center">
                                    <span data-feather="eye"></span>
                                </a>
                                <a href="{{ route('documents.edit', $document->slug) }}" class="badge bg-warning d-inline-flex align-items-center">
                                    <span data-feather="edit"></span>
                                </a>
                                <form id="delete-form-{{ $document->slug }}" action="{{ route('documents.destroy', $document->slug) }}" method="POST">
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
