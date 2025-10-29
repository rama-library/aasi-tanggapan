<div class="modal fade" id="historyModal{{ $respond->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Riwayat Review Tanggapan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th><th>Reviewer</th><th>Tanggapan Lama</th>
                            <th>Tanggapan Baru</th><th>Alasan</th><th>Tanggal Review</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($respond->histories as $index => $history)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $history->reviewer->name ?? '-' }}</td>
                                <td>{{ $history->old_tanggapan ?? '-' }}</td>
                                <td>{{ $history->new_tanggapan ?? '-' }}</td>
                                <td>{{ $history->alasan ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($history->reviewed_at)->format('d M Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
