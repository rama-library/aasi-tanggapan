@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h4 class="mb-3">Detail Dokumen: <strong>{{ $document->no_document }}</strong></h4>

    {{-- Card Detail Dokumen --}}
    <div class="card mb-4">
        <div class="card-body">
            <p><strong>Perihal:</strong> {{ $document->perihal }}</p>
            <p><strong>Tanggal Upload:</strong> {{ \Carbon\Carbon::parse($document->created_at)->isoFormat('D MMMM Y') }}</p>
            <p><strong>Responder Due Date:</strong> {{ \Carbon\Carbon::parse($document->due_date)->isoFormat('D MMMM Y') }} {{ $document->due_time }}</p>
            <p><strong>Reviewer Due Date:</strong> {{ \Carbon\Carbon::parse($document->review_due_date)->isoFormat('D MMMM Y') }} {{ $document->review_due_time }}</p>
        </div>
    </div>

    {{-- Daftar Pasal --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Daftar Pasal</h5>
        <a href="{{ route('pasal.create', ['document' => $document->slug]) }}" class="btn btn-primary">+ Tambah Pasal</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="alltable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Pasal</th>
                            <th>Penjelasan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($document->pasal as $index => $pasal)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $pasal->pasal }}</td>
                            <td>{{ $pasal->penjelasan }}</td>
                            <td>
                                <a href="{{ route('pasal.show', ['document' => $document->slug, 'pasal' => $pasal->id]) }}" class="badge bg-info d-inline-flex align-items-center">
                                    <span data-feather="eye"></span>
                                </a>                            
                                <form id="delete-form-{{ $pasal->id }}" action="{{ route('pasal.destroy', $pasal->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmDelete('delete-form-{{ $pasal->id }}')" class="badge bg-danger border-0 d-inline-flex align-items-center">
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
