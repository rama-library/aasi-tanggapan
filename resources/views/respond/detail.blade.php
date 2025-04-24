@extends('layouts.app')

@section('content')
<style>
    .pagination {
        font-size: 0.875rem; /* kecilkan ukuran font */
    }

    .pagination .page-link {
        padding: 0.25rem 0.5rem; /* kecilkan padding tombol */
    }

    .pagination .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
</style>

<div class="container-fluid">
    <h4 class="mb-3">Detail Dokumen: <strong>{{ $document->no_document }}</strong></h4>

    {{-- Info Dokumen --}}
    <div class="card mb-4">
        <div class="card-body">
            <p><strong>Perihal:</strong> {{ $document->perihal }}</p>
            <p><strong>Tanggal Upload:</strong> {{ \Carbon\Carbon::parse($document->created_at)->isoFormat('D MMMM Y') }}</p>
            <p><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($document->due_date)->isoFormat('D MMMM Y') }} {{ $document->due_time }}</p>
        </div>
    </div>

    {{-- Search --}}
    <form method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Cari Pasal..." value="{{ request('search') }}">
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
                            <th>Pasal</th>
                            <th>Penjelasan</th>
                            <th>Tanggapan</th>
                            <th>PIC</th>
                            <th>Perusahaan</th>
                            <th>Reviewer</th>
                            <th>Alasan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pasal as $index => $p)
                            @php
                                $responds = $p->respond;
                                $rowspan = $responds->count() ?: 1;
                                $userResponded = $responds->contains('pic_id', auth()->id());
                                $isPIC = auth()->user()->hasRole('PIC');
                                $isReviewer = auth()->user()->hasRole('Reviewer');
                            @endphp

                            @if ($responds->isNotEmpty())
                            @foreach ($responds as $rIndex => $respond)
                            <tr class="{{ $respond->is_deleted ? 'table-danger' : '' }}">
                                @if ($rIndex === 0)
                                    <td rowspan="{{ $rowspan }}">{{ ($pasal->currentPage() - 1) * $pasal->perPage() + $index + 1 }}</td>
                                    <td rowspan="{{ $rowspan }}">{{ $p->pasal }}</td>
                                    <td rowspan="{{ $rowspan }}">{{ $p->penjelasan }}</td>
                                @endif
                        
                                <td>
                                    @if ($respond->is_deleted)
                                        <em class="text-muted">Telah dihapus oleh reviewer</em>
                                    @else
                                        {{ $respond->tanggapan ?? '-' }}
                                        @if ($respond->original_data)
                                            <br>
                                            <small class="text-muted">
                                                <i>(Sebelum revisi: {{ json_decode($respond->original_data)->tanggapan ?? '-' }})</i>
                                            </small>
                                        @endif
                                    @endif
                                </td>
                                <td>{{ $respond->pic->name ?? '-' }}</td>
                                <td>{{ $respond->perusahaan ?? '-' }}</td>
                                <td>{{ $respond->reviewer->name ?? '-' }}</td>
                                <td>
                                    @if ($respond->alasan)
                                        <span class="text-danger">{{ $respond->alasan }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if ($isPIC && $respond->pic_id === auth()->id())
                                        <span class="badge bg-success">Sudah Tanggapi</span>
                                    @elseif ($isReviewer && !$respond->is_deleted)
                                        <a href="{{ route('respond.edit', ['document' => $document->slug, 'pasal' => $p->id, 'respond' => $respond->id]) }}" class="btn btn-sm btn-warning">Edit</a>
                                        <button type="button"
                                            class="btn btn-sm btn-danger"
                                            onclick="hapusTanggapan('{{ route('respond.destroy', ['document' => $document->slug, 'pasal' => $p->id, 'respond' => $respond->id]) }}')">
                                            Hapus
                                        </button>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                                @if ($isPIC && !$userResponded)
                                    <tr>
                                        <td colspan="9" class="text-end">
                                            <a href="{{ route('respond.create', ['document' => $document->slug, 'pasal' => $p->id]) }}" class="btn btn-sm btn-primary">Tanggapi</a>
                                        </td>
                                    </tr>
                                @endif
                            @else
                                <tr>
                                    <td>{{ ($pasal->currentPage() - 1) * $pasal->perPage() + $index + 1 }}</td>
                                    <td>{{ $p->pasal }}</td>
                                    <td>{{ $p->penjelasan }}</td>
                                    <td colspan="5" class="text-center">Belum ada tanggapan</td>
                                    <td>
                                        @if ($isPIC)
                                            <a href="{{ route('respond.create', ['document' => $document->slug, 'pasal' => $p->id]) }}" class="btn btn-sm btn-primary">Tanggapi</a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">Tidak ada pasal ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Pagination --}}
                <div class="mt-3">
                    {{ $pasal->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
