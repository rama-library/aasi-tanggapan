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

    {{-- Daftar Konten --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Daftar Konten</h5>
        <a href="{{ route('admin.content.create', ['document' => $document->slug]) }}" class="btn btn-primary">+ Tambah</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="alltable">
                    @include('partials.table_header', ['document' => $document])
                    <tbody>
                        @foreach ($document->contents as $index => $content)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-justify">{{ $content->contents }}</td>
                            <td class="text-justify">
                                @if ($content->detil && $content->gambar)
                                <br><img src="{{ asset('storage/' . $content->gambar) }}" class="img-fluid" width="200" alt="Gambar detil">
                                <p>{{ $content->detil }}</p>
                                @elseif ($content->gambar)
                                <br><img src="{{ asset('storage/' . $content->gambar) }}" class="img-fluid" width="200" alt="Gambar detil">
                                @elseif ($content->detil)
                                <p>{{ $content->detil }}</p>
                                @else
                                    <p><em> </em></p>
                                @endif                             
                            </td>
                            <td>
                                <a href="{{ route('admin.content.show', ['document' => $document->slug, 'content' => $content->id]) }}" class="badge bg-info d-inline-flex align-items-center">
                                    <span data-feather="eye"></span>
                                </a>                            
                                <form id="delete-form-{{ $content->id }}" action="{{ route('admin.content.destroy', $content->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmDelete('delete-form-{{ $content->id }}')" class="badge bg-danger border-0 d-inline-flex align-items-center">
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
