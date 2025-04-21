@csrf

<input type="hidden" name="doc_id" value="{{ $doc_id ?? $pasal->doc_id ?? '' }}">

<div class="col-md-12 mb-3">
    <label for="pasal" class="form-label">Pasal</label>
    <input type="text" class="form-control" name="pasal" value="{{ old('pasal', $pasal->pasal ?? '') }}" autofocus>
</div>

<div class="col-md-12 mb-3">
    <label for="penjelasan" class="form-label">Penjelasan</label>
    <textarea class="form-control" name="penjelasan" rows="4" required>{{ old('penjelasan', $pasal->penjelasan ?? '') }}</textarea>
</div>

<div class="d-flex justify-content-center">
    <button type="submit" class="btn btn-success me-2">{{ $submit ?? 'Simpan' }}</button>
    <a href="{{ route('documents.show', $document->slug) }}" class="btn btn-danger me-2">Kembali</a>
</div>