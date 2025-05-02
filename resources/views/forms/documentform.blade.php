<div class="col-md-6">
    <label for="no_document" class="form-label">No Dokumen</label>
    <input type="text" name="no_document" id="no_document" value="{{ old('no_document', $document->no_document ?? '') }}" class="form-control" autofocus>
</div>

<div class="col-md-6">
    <label for="slug" class="form-label">Slug</label>
    <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug', $document->slug ?? '') }}" readonly>
</div>

<div class="col-md-12">
    <label for="perihal" class="form-label">Perihal</label>
    <textarea name="perihal" class="form-control" cols="30" rows="10">{{ old('perihal', $document->perihal ?? '') }}</textarea>
</div>

<div class="col-md-6">
    <label for="due_date" class="form-label">Due Date</label>
    <input type="date" class="form-control @error('due_date') is-invalid @enderror" id="due_date" name="due_date" value="{{ old('due_date', $document->due_date ?? '') }}">
</div>

<div class="col-md-6">
    <label for="due_time" class="form-label">Due Time</label>
    <input type="time" class="form-control @error('due_time') is-invalid @enderror" id="due_time" name="due_time" value="{{ old('due_time', $document->due_time ?? '') }}">
</div>

<div class="col-md-6">
    <label for="review_due_date" class="form-label">Review Due Date</label>
    <input type="date" name="review_due_date" class="form-control" value="{{ old('review_due_date', $document->review_due_date ?? '') }}">
</div>

<div class="col-md-6">
    <label for="review_due_time" class="form-label">Review Due Time</label>
    <input type="time" name="review_due_time" class="form-control" value="{{ old('review_due_time', $document->review_due_time ?? '') }}">
</div>


<div class="d-flex justify-content-center">
    <button type="submit" class="btn btn-success me-2">{{ $submit ?? 'Simpan' }}</button>
    <a href="{{ route('admin.documents.index') }}" class="btn btn-danger me-2">Kembali</a>
</div>

<script>
    const no_document = document.querySelector('#no_document');
    const slug = document.querySelector('#slug');

    let debounceTimer;

    no_document.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            fetch('/admin/documents/checkSlug?no_document=' + encodeURIComponent(no_document.value))
                .then(response => response.json())
                .then(data => slug.value = data.slug);
        }, 300); // delay 300ms setelah terakhir user mengetik
    });
</script>
