@csrf

<input type="hidden" name="doc_id" value="{{ $doc_id ?? $content->doc_id ?? '' }}">

<div class="col-md-12 mb-3">
    <label for="content" class="form-label">Konten</label>
    <textarea class="form-control" name="contents" rows="4" required>{{ old('contents', $content->contents ?? '') }}</textarea>
</div>

<div class="col-md-12 mb-3">
    <label for="detil" class="form-label">detil</label>
    <textarea id="detil" name="detil" class="form-control">{{ old('detil', $content->detil ?? '') }}</textarea>
</div>

<div class="col-md-12 mb-3">
    <label for="gambar" class="form-label">Upload Gambar</label>
    <input type="file" class="form-control" id="gambar" name="gambar">
    @if (!empty($content->gambar))
        <p class="mt-2">Gambar lama: <br><img src="{{ asset('storage/' . $content->gambar) }}" width="150"></p>
    @endif
</div>

<div class="d-flex justify-content-center">
    <button type="submit" class="btn btn-success me-2">{{ $submit ?? 'Simpan' }}</button>
    <a href="{{ route('admin.documents.show', $document->slug) }}" class="btn btn-danger me-2">Kembali</a>
</div>