@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h4 class="mb-3">Detail Dokumen Selesai: <strong>{{ $document->no_document }}</strong></h4>

    {{-- Info Dokumen --}}
    <div class="card mb-4">
        <div class="card-body">
            <p><strong>Perihal:</strong> {{ $document->perihal }}</p>
            <p><strong>Tanggal Upload:</strong> {{ $document->formatted_created_at }}</p>
            <p><strong>Due Date:</strong> {{ $document->formatted_due_date }} {{ $document->due_time }}</p>
            <p><strong>Batas Review Reviewer:</strong> 
                @if ($document->formatted_review_due_date)
                    {{ $document->formatted_review_due_date }} {{ $document->review_due_time }}
                @else
                    <em>Belum ditentukan</em>
                @endif
            </p>
        </div>
    </div>

    {{-- Search --}}
    <form method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Cari..." value="{{ request('search') }}">
            <button class="btn btn-primary" type="submit">Cari</button>
        </div>
    </form>

    {{-- Table --}}
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    @include('partials.table_header', ['document' => $document, 'columns' => 'reviewer'])
                    <tbody>
                        @forelse ($content as $index => $p)
                            @php
                                $responds = $p->respond;
                                $rowspan = max($responds->count(), 1);
                                $now = now();
                                $reviewDeadline = $document->review_due_date && $document->review_due_time
                                    ? \Carbon\Carbon::parse($document->review_due_date . ' ' . $document->review_due_time)
                                    : null;
                                $canReview = !$reviewDeadline || $now->lte($reviewDeadline);
                            @endphp

                            {{-- Jika ada tanggapan --}}
                            @if ($responds->isNotEmpty())
                                @foreach ($responds as $rIndex => $respond)
                                    <tr>
                                        @if ($rIndex === 0)
                                            <td class="text-justify" rowspan="{{ $rowspan }}">{{ $p->contents }}</td>
                                            <td class="text-justify" rowspan="{{ $rowspan }}">
                                                @include('partials.content_detail', ['p' => $p])
                                            </td>
                                        @endif

                                        {{-- kolom tanggapan --}}
                                        <td class="text-justify">
                                            @if ($respond->is_deleted)
                                                <del>{{ json_decode($respond->original_data)->tanggapan ?? '-' }}</del>
                                                <!--<br><small>(Dihapus oleh reviewer)</small>-->
                                            @else
                                                {{ $respond->tanggapan ?? '-' }}
                                                <!--@if ($respond->original_data)-->
                                                <!--    <br><small class="text-muted"><i>(Sebelum revisi: {{ json_decode($respond->original_data)->tanggapan ?? '-' }})</i></small>-->
                                                <!--@endif-->
                                            @endif
                                        </td>

                                        <td class="text-center">{{ $respond->pic->name ?? '-' }}</td>
                                        <td class="text-center">{{ $respond->perusahaan ?? '-' }}</td>

                                        {{-- Kolom reviewer --}}
                                        <td>
                                            @if ($respond->histories->count())
                                                <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#historyModal{{ $respond->id }}">
                                                    <i class="fas fa-history"></i> History Reviewer
                                                </button>
                                            @elseif ($respond->is_deleted)
                                                <span class="badge bg-danger">Sudah Dihapus</span>
                                            @else
                                                {{ $respond->reviewer->name ?? '-' }}
                                            @endif
                                        </td>

                                        <td class="text-justify">{{ $respond->alasan ?: '-' }}</td>

                                        {{-- kolom aksi reviewer --}}
                                        @if (auth()->user()->hasRole('Reviewer'))
                                            @include('partials.reviewer_action', [
                                                'respond' => $respond,
                                                'document' => $document,
                                                'p' => $p,
                                                'canReview' => $canReview
                                            ])
                                        @endif
                                    </tr>
                                @endforeach
                            @else
                                {{-- jika belum ada tanggapan sama sekali --}}
                                <tr>
                                    <td class="text-justify">{{ $p->contents }}</td>
                                    <td class="text-justify">@include('partials.content_detail', ['p' => $p])</td>
                                    <td colspan="5" class="text-center text-muted">Belum ada tanggapan</td>
                                    @if (auth()->user()->hasRole('Reviewer'))
                                        <td class="text-center">
                                            <span class="badge bg-{{ $canReview ? 'info' : 'secondary' }}">
                                                {{ $canReview ? 'Menunggu Tanggapan' : 'Waktu Review Habis' }}
                                            </span>
                                        </td>
                                    @endif
                                </tr>
                            @endif
                        @empty
                            <tr><td colspan="9" class="text-center">Tidak ada data ditemukan.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Modal history reviewer --}}
                @foreach ($content as $p)
                    @foreach ($p->respond as $respond)
                        @if ($respond->histories->count())
                            @include('partials.history_modal', ['respond' => $respond])
                        @endif
                    @endforeach
                @endforeach

                {{-- Pagination --}}
                <div class="mt-3">
                    {{ $content->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
