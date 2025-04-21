<div class="col-md-12 mb-3">
    <label for="name" class="form-label">Nama Permission</label>
    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
           value="{{ old('name', $permission->name ?? '') }}" autofocus>
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<button type="submit" class="btn btn-success">{{ $submit }}</button>
<a href="{{ route('permissions.index') }}" class="btn btn-secondary">Kembali</a>
