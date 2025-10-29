@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h4 class="mb-3">Detail Dokumen: <strong>{{ $document->no_document }}</strong></h4>
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
    <form method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Cari..." value="{{ request('search') }}">
            <button class="btn btn-primary" type="submit">Cari</button>
        </div>
    </form>

    <div class="card">
        <div class="card-body">
            {{-- Tombol PIC --}}
            @if ($isPIC)
                @if (!$sudahNoRespond && !$sudahPernahTanggapan)
                    <div class="mb-3 text-end">
                        <button type="button" id="btnNoRespond" class="btn btn-outline-danger">
                            <i class="fas fa-times-circle"></i> Tidak Ada Tanggapan
                        </button>
                    </div>
                @elseif ($sudahNoRespond)
                    <div class="mb-3 text-end">
                        <span class="badge bg-secondary">Anda sudah menandai Tidak Ada Tanggapan</span>
                    </div>
                @endif
            @endif
            <div class="table-responsive">
                <table class="table table-bordered">
                    @include('partials.table_header', ['document' => $document, 'columns' => 'full'])
                    <tbody>
                        @forelse ($content as $p)
                            @php
                                $responds = $p->respond;
                                $hasResponds = $responds->isNotEmpty();
                                $rowspan = $hasResponds ? $responds->count() : 1;
                                $userResponded = $responds->contains('pic_id', auth()->id());
                            @endphp
                            @if ($hasResponds)
                                @foreach ($responds as $rIndex => $respond)
                                    <tr>
                                        @if ($rIndex === 0)
                                            <td rowspan="{{ $rowspan + ((!$userResponded && $canRespond && !$sudahNoRespond) ? 1 : 0) }}" class="text-justify">
                                                {{ $p->contents }}
                                            </td>
                                            <td rowspan="{{ $rowspan + ((!$userResponded && $canRespond && !$sudahNoRespond) ? 1 : 0) }}" class="text-justify">
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
                                                <!--    <br><small class="text-muted">-->
                                                <!--        <i>(Sebelum revisi: {{ json_decode($respond->original_data)->tanggapan ?? '-' }})</i>-->
                                                <!--    </small>-->
                                                <!--@endif-->
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $respond->pic_name }}</td>
                                        <td class="text-center">{{ $respond->perusahaan }}</td>
                                        <td class="text-center">
                                            @if ($respond->histories->count())
                                                <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#historyModal{{ $respond->id }}">
                                                    <i class="fas fa-history"></i> History Reviewer
                                                </button>
                                            @else
                                                {!! $respond->is_deleted
                                                    ? '<span class="text-danger fw-bold">' . e($respond->reviewer_name) . '</span>'
                                                    : e($respond->reviewer_name) !!}
                                            @endif
                                        </td>
                                        <td class="text-justify">{{ $respond->alasan ?: '-' }}</td>

                                        {{-- kolom aksi --}}
                                        <td class="text-center">
                                            @include('partials.respond_action', [
                                                'respond' => $respond,
                                                'document' => $document,
                                                'isPIC' => $isPIC,
                                                'isReviewer' => $isReviewer,
                                                'sudahNoRespond' => $sudahNoRespond,
                                                'canReview' => $canReview,
                                                'canRespond' => $canRespond
                                            ])
                                        </td>
                                    </tr>
                                @endforeach
                                {{-- Tambahkan baris jika user belum tanggapan --}}
                                @if (!$userResponded && $canRespond && !$sudahNoRespond)
                                    <tr>
                                        <td colspan="5" class="text-center text-muted align-middle">-</td>
                                        <td class="text-center align-middle">
                                            <a href="{{ route('respond.create', [
                                                'document' => $document->slug,
                                                'content' => $p->id
                                            ]) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-comment-dots"></i> Tanggapi
                                            </a>
                                        </td>
                                    </tr>
                                @endif
                            @elseif (!$userResponded && $canRespond)
                                {{-- Jika belum ada respond sama sekali --}}
                                <tr>
                                    <td class="text-justify">{{ $p->contents }}</td>
                                    <td class="text-justify">@include('partials.content_detail', ['p' => $p])</td>
                                    <td colspan="5" class="text-center text-muted align-middle">
                                        -
                                    </td>
                                    <td class="text-center align-middle">
                                        @if ($sudahNoRespond)
                                        <span class="badge bg-secondary">
                                            Anda Sudah Menandai Dokumen Ini
                                            <br>Tidak Ada Tanggapan
                                        </span>
                                        @else
                                        <a href="{{ route('respond.create', [
                                            'document' => $document->slug,
                                            'content' => $p->id
                                        ]) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-comment-dots"></i> Tanggapi
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                            @else
                                <tr>
                                    <td class="text-justify">{{ $p->contents }}</td>
                                    <td class="text-justify">@include('partials.content_detail', ['p' => $p])</td>
                                    <td colspan="5" class="text-center text-muted align-middle">-</td>
                                    <td class="text-center align-middle">
                                        @if ($sudahNoRespond)
                                            <span class="badge bg-secondary">
                                                Anda Sudah Menandai Dokumen Ini
                                                <br>Tidak Ada Tanggapan
                                            </span>
                                        @else
                                            <a href="{{ route('respond.create', [
                                                'document' => $document->slug,
                                                'content' => $p->id
                                            ]) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-comment-dots"></i> Tanggapi
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada data ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                @foreach ($content as $p)
                    @foreach ($p->respond as $respond)
                        @if ($respond->histories->count())
                            @include('partials.history_modal', ['respond' => $respond])
                        @endif
                    @endforeach
                @endforeach

                <div class="mt-3">
                    {{ $content->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>

@include('partials.no_respond_script', ['document' => $document])
@endsection
