@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h4 class="mb-3">Tanggapan Final</h4>

    {{-- Search --}}
    <form method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Cari Dokumen..." value="{{ request('search') }}">
            <button class="btn btn-primary" type="submit">Cari</button>
        </div>
    </form>

    {{-- Table --}}
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>No Dokumen</th>
                            <th>Perihal</th>
                            <th>Tanggal Unggah</th>
                            <th>Batas Akhir Menanggapi</th>
                            <th>Batas Akhir Review</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($documents as $index => $doc)
                            <tr>
                                <td>{{ ($documents->currentPage() - 1) * $documents->perPage() + $index + 1 }}</td>
                                <td>
                                    <a href="{{ route('tanggapan.final.detail', ['document' => $doc->slug]) }}">
                                        {{ $doc->no_document }}
                                    </a>
                                </td>
                                <td>{{ $doc->perihal }}</td>
                                <td>{{ \Carbon\Carbon::parse($doc->created_at)->isoFormat('D MMMM Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($doc->due_date)->isoFormat('D MMMM Y') }} {{ $doc->due_time }}</td>
                                <td>{{ \Carbon\Carbon::parse($doc->review_due_date)->isoFormat('D MMMM Y') }} {{ $doc->review_due_time }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada dokumen ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>                    
                </table>

                {{-- Pagination --}}
                <div class="mt-3">
                    {{ $documents->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection