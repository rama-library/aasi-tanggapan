@php
    $isDeleted = $respond->is_deleted ?? false;
    $isPastReview = isset($canReview) ? !$canReview : false;
    $isPastRespond = isset($canRespond) ? !$canRespond : false;
    $isOwner = auth()->id() === ($respond->pic_id ?? null);
    $deletedByReviewer = $isDeleted && $respond->reviewer_id; // Reviewer yang hapus
    $deletedByPIC = $isDeleted && !$respond->reviewer_id; // PIC sendiri yang hapus
@endphp

<div class="d-flex flex-wrap gap-1 justify-content-center">
    @if ($isReviewer && !$isDeleted)
        @if (!$isPastReview)
            {{-- Tombol edit review --}}
            <a href="{{ route('respond.edit', [
                'document' => $document->slug,
                'content' => $p->id,
                'respond' => $respond->id
            ]) }}" class="badge bg-warning">
                <span data-feather="edit"></span>
            </a>

            {{-- Tombol hapus review --}}
            <button type="button" class="btn btn-sm btn-danger"
                onclick="hapusTanggapan('{{ route('respond.destroy', [
                    'document' => $document->slug,
                    'content' => $p->id,
                    'respond' => $respond->id
                ]) }}', true)">
                <span data-feather="delete"></span>
            </button>
        @else
            <span class="badge bg-secondary">Review Ditutup</span>
        @endif
    @endif
    @if ($isPIC && $isOwner)
        @if ($deletedByReviewer)
            <span class="badge bg-danger">Dihapus oleh Reviewer</span>
        @elseif ($deletedByPIC)
            @if (!$isPastRespond)
                <a href="{{ route('respond.create', [
                    'document' => $document->slug,
                    'content' => $p->id
                ]) }}" class="badge bg-primary">
                    <span data-feather="plus"></span> Tanggapi Ulang
                </a>
            @else
                <span class="badge bg-secondary">Waktu Respon Berakhir</span>
            @endif
        @elseif (!$respond->reviewer_id && !$isDeleted && !$isPastRespond)
            <a href="{{ route('respond.edit', [
                'document' => $document->slug,
                'content' => $p->id,
                'respond' => $respond->id
            ]) }}" class="badge bg-warning text-dark">
                <span data-feather="edit"></span>
            </a>
            <button type="button" class="btn btn-sm btn-danger"
                onclick="hapusTanggapan('{{ route('respond.destroy', [
                    'document' => $document->slug,
                    'content' => $p->id,
                    'respond' => $respond->id
                ]) }}', false)">
                <span data-feather="trash-2"></span>
            </button>
        @elseif ($respond->reviewer_id)
            <span class="badge bg-success">Sudah Direview</span>
        @elseif ($isPastRespond)
            <span class="badge bg-secondary">Waktu Respon Berakhir</span>
        @endif
    @endif
    @if (!$isReviewer && !$isPIC)
        <span class="text-muted">Tidak ada aksi</span>
    @endif
</div>