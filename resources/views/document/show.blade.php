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

    {{-- Daftar Batang Tubuh --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Daftar Batang Tubuh</h5>
        <a href="{{ route('admin.batangtubuh.create', ['document' => $document->slug]) }}" class="btn btn-primary">+ Tambah Batang Tubuh</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="alltable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Batang Tubuh</th>
                            <th>Penjelasan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($document->batangtubuh as $index => $batangtubuh)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="text-justify">{{ $batangtubuh->batang_tubuh }}</td>
                            <td class="text-justify">
                                @if ($batangtubuh->penjelasan && $batangtubuh->gambar)
                                <br><img src="{{ asset('storage/' . $batangtubuh->gambar) }}" class="img-fluid" width="200" alt="Gambar Penjelasan">
                                <p>{{ $batangtubuh->penjelasan }}</p>
                                @elseif ($batangtubuh->gambar)
                                <br><img src="{{ asset('storage/' . $batangtubuh->gambar) }}" class="img-fluid" width="200" alt="Gambar Penjelasan">
                                @elseif ($batangtubuh->penjelasan)
                                <p>{{ $batangtubuh->penjelasan }}</p>
                                @else
                                    <p><em>Tidak ada penjelasan atau gambar.</em></p>
                                @endif                             
                            </td>
                            <td>
                                <a href="{{ route('admin.batangtubuh.show', ['document' => $document->slug, 'batangtubuh' => $batangtubuh->id]) }}" class="badge bg-info d-inline-flex align-items-center">
                                    <span data-feather="eye"></span>
                                </a>                            
                                <form id="delete-form-{{ $batangtubuh->id }}" action="{{ route('admin.batangtubuh.destroy', $batangtubuh->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmDelete('delete-form-{{ $batangtubuh->id }}')" class="badge bg-danger border-0 d-inline-flex align-items-center">
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
