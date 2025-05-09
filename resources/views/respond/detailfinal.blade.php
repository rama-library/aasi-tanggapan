@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h4 class="mb-3">Detail Dokumen Final: <strong>{{ $document->no_document }}</strong></h4>

    {{-- Info Dokumen --}}
    <div class="card mb-4">
        <div class="card-body">
            <p><strong>Perihal:</strong> {{ $document->perihal }}</p>
            <p><strong>Tanggal Upload:</strong> {{ \Carbon\Carbon::parse($document->created_at)->isoFormat('D MMMM Y') }}</p>
            <p><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($document->due_date)->isoFormat('D MMMM Y') }} {{ $document->due_time }}</p>
            <p><strong>Batas Review Reviewer:</strong> 
                @if ($document->review_due_date && $document->review_due_time)
                    {{ \Carbon\Carbon::parse($document->review_due_date)->isoFormat('D MMMM Y') }} {{ $document->review_due_time }}
                @else
                    <em>Belum ditentukan</em>
                @endif
            </p>            
        </div>
    </div>

    {{-- Search --}}
    <form method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Cari Batang Tubuh..." value="{{ request('search') }}">
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
                            <th>Batang Tubuh</th>
                            <th>Penjelasan</th>
                            <th>Tanggapan</th>
                            <th>PIC</th>
                            <th>Perusahaan</th>
                            <th>Reviewer</th>
                            <th>Alasan</th>
                            @if (auth()->user()->hasRole('Reviewer'))
                                <th>Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($batangtubuh as $index => $p)
                            @php
                                $responds = $p->respond;
                                $rowspan = $responds->count() ?: 1;
                                $now = now();
                                $reviewDeadline = $document->review_due_date && $document->review_due_time
                                    ? \Carbon\Carbon::parse($document->review_due_date . ' ' . $document->review_due_time)
                                    : null;
                                $canReview = !$reviewDeadline || $now->lte($reviewDeadline);
                                $role = auth()->user()->getRoleNames()->first();
                            @endphp

                            @if ($responds->isNotEmpty())
                                @foreach ($responds as $rIndex => $respond)
                                <tr>
                                    @if ($rIndex === 0)
                                        <td rowspan="{{ $rowspan }}">{{ ($batangtubuh->currentPage() - 1) * $batangtubuh->perPage() + $index + 1 }}</td>
                                        <td rowspan="{{ $rowspan }}">{{ $p->batang_tubuh }}</td>
                                        <td rowspan="{{ $rowspan }}">
                                            @if ($p->penjelasan && $p->gambar)
                                            <img src="{{ asset('storage/' . $p->gambar) }}" class="img-fluid" alt="Gambar Penjelasan">
                                            <br><p>{{ $p->penjelasan }}</p>
                                            @elseif ($p->gambar)
                                            <img src="{{ asset('storage/' . $p->gambar) }}" class="img-fluid" alt="Gambar Penjelasan">
                                            @elseif ($p->penjelasan)
                                            <p>{{ $p->penjelasan }}</p>
                                            @else
                                                <p><em>Tidak ada penjelasan atau gambar.</em></p>
                                            @endif  
                                        </td>
                                    @endif

                                    {{-- Tanggapan --}}
                                    <td>
                                        @if ($respond->is_deleted)
                                            <del class="muted">{{ json_decode($respond->original_data)->tanggapan ?? '-' }} </del>
                                            <br>(Dihapus oleh reviewer)
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

                                    @if ($role === 'Reviewer')
                                        <td>
                                            @if (!$respond->is_deleted && $canReview)
                                                <a href="{{ route('tanggapan.final.edit', ['document' => $document->slug, 'batangtubuh' => $p->id, 'respond' => $respond->id]) }}" class="btn btn-sm btn-warning">Review</a>
                                                <button type="button"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="hapusTanggapan('{{ route('tanggapan.final.destroy', ['document' => $document->slug, 'batangtubuh' => $p->id, 'respond' => $respond->id]) }}')">
                                                    Hapus
                                                </button>
                                            @elseif (!$canReview)
                                                <span class="badge bg-secondary">Waktu Review Habis</span>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                                @endforeach
                            @else
                                {{-- batangtubuh tanpa tanggapan --}}
                                <tr>
                                    <td>{{ ($batangtubuh->currentPage() - 1) * $batangtubuh->perPage() + $index + 1 }}</td>
                                    <td>{{ $p->batang_tubuh }}</td>
                                    <td>
                                        @if ($p->penjelasan && $p->gambar)
                                        <img src="{{ asset('storage/' . $p->gambar) }}" class="img-fluid" alt="Gambar Penjelasan">
                                        <br><p>{{ $p->penjelasan }}</p>
                                        @elseif ($p->gambar)
                                        <img src="{{ asset('storage/' . $p->gambar) }}" class="img-fluid" alt="Gambar Penjelasan">
                                        @elseif ($p->penjelasan)
                                        <p>{{ $p->penjelasan }}</p>
                                        @else
                                        <p><em>Tidak ada penjelasan atau gambar.</em></p>
                                        @endif    
                                    </td>
                                    <td colspan="5" class="text-center">Belum ada tanggapan.</td>
                                    @if ($role === 'Reviewer')
                                        <td>
                                            @if ($canReview)
                                                <span class="badge bg-info">Menunggu Tanggapan</span>
                                            @else
                                                <span class="badge bg-secondary">Waktu Review Habis</span>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">Tidak ada batang tubuh ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-3">
                    {{ $batangtubuh->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
