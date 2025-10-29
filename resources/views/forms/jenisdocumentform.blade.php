<div class="col-md-6">
    <label for="name" class="form-label">Nama Jenis</label>
    <input type="text" name="name" id="name" value="{{ old('name', $documentType->name ?? '') }}" class="form-control" autofocus>
</div>

<div class="d-flex justify-content-center">
    <button type="submit" class="btn btn-success me-2">{{ $submit ?? 'Simpan' }}</button>
    <a href="{{ route('admin.document-types.index') }}" class="btn btn-danger me-2">Kembali</a>
</div>
