<td class="text-center align-middle">
    @if ($respond->is_deleted)
        <span class="badge bg-danger">Sudah Dihapus</span>
    @elseif ($canReview)
        <a href="{{ route('tanggapan.selesai.edit', ['document' => $document->slug, 'content' => $p->id, 'respond' => $respond->id]) }}" 
           class="btn btn-sm btn-warning">Review</a>
        <button type="button" class="btn btn-sm btn-danger"
            onclick="hapusTanggapan('{{ route('tanggapan.selesai.destroy', ['document' => $document->slug, 'content' => $p->id, 'respond' => $respond->id]) }}', true)">
            Hapus
        </button>
    @else
        <span class="badge bg-secondary">Waktu Review Habis</span>
    @endif
</td>
