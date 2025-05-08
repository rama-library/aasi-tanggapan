@csrf

<input type="hidden" name="doc_id" value="{{ $doc_id ?? $batangtubuh->doc_id ?? '' }}">

<div class="col-md-12 mb-3">
    <label for="batangtubuh" class="form-label">Batang Tubuh</label>
    <textarea class="form-control" name="batang_tubuh" rows="4" required>{{ old('batang_tubuh', $batangtubuh->batang_tubuh ?? '') }}</textarea>
</div>

<div class="col-md-12 mb-3">
    <label for="penjelasan" class="form-label">Penjelasan</label>
    <textarea id="penjelasan" name="penjelasan" class="form-control">{{ old('penjelasan', $batangtubuh->penjelasan ?? '') }}</textarea>
</div>

<div class="col-md-12 mb-3">
    <label for="gambar" class="form-label">Upload Gambar</label>
    <input type="file" class="form-control" id="gambar" name="gambar">
    @if (!empty($batangtubuh->gambar))
        <p class="mt-2">Gambar lama: <br><img src="{{ asset('storage/' . $batangtubuh->gambar) }}" width="150"></p>
    @endif
</div>

<div class="d-flex justify-content-center">
    <button type="submit" class="btn btn-success me-2">{{ $submit ?? 'Simpan' }}</button>
    <a href="{{ route('admin.documents.show', $document->slug) }}" class="btn btn-danger me-2">Kembali</a>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const penjelasan = document.getElementById('penjelasan');
        const gambarInput = document.getElementById('gambar');
    
        function toggleInputs() {
            if (penjelasan.value.trim() !== '') {
                gambarInput.parentElement.style.display = 'none';
            } else {
                gambarInput.parentElement.style.display = 'block';
            }
        }
    
        penjelasan.addEventListener('input', toggleInputs);
        toggleInputs();
    });
</script>    