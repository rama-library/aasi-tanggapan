
<div class="col-md-12 mb-3">
    <label for="name" class="form-label">Nama Role</label>
    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" id="name" value="{{ old('name', $role->name ?? '') }}" autofocus>
    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="col-md-12 mb-3">
    <label class="form-label">Assign Permissions</label>
    <div class="row">
        @foreach($permissions as $permission)
        <div class="col-md-2">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                    {{ (isset($role) && $role->permissions->pluck('name')->contains($permission->name)) || in_array($permission->name, old('permissions', [])) ? 'checked' : '' }}>
                <label class="form-check-label">{{ $permission->name }}</label>
            </div>
        </div>
        @endforeach
    </div>
</div>

<button type="submit" class="btn btn-success">{{ $submit }}</button>
<a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Kembali</a>