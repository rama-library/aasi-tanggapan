<div class="col-md-6">
    <label for="name" class="form-label">Nama</label>
    <input type="text" name="name" id="name" value="{{ old('name', $user->name ?? '') }}" class="form-control" autofocus>
</div>

<div class="col-md-6">
    <label for="email" class="form-label">Email</label>
    <input type="email" name="email" id="email" value="{{ old('email', $user->email ?? '') }}" class="form-control">
</div>

@if (!isset($user))
<div class="col-md-6">
    <label for="password" class="form-label">Password</label>
    <input type="password" name="password" id="password" class="form-control">
</div>
@endif

<div class="col-md-6" id="company-field">
    <label for="company_name" class="form-label">Perusahaan</label>
    <select name="company_name" class="form-select">
    <option value="AASI">AASI</option>
    @foreach ($companies as $company)
    <option value="{{ $company->namapt }}" {{ old('company_name', $user->company_name ?? '') == $company->namapt ? 'selected' : '' }}>
        {{ $company->namapt }}
    </option>
    @endforeach
    </select>
</div>

<div class="col-md-6">
    <label for="department" class="form-label">Departemen</label>
    <input type="text" name="department" id="department" value="{{ old('department', $user->department ?? '') }}" class="form-control">
</div>

<div class="col-md-6">
    <label for="role" class="form-label">Role</label>
    <select name="role" id="role" class="form-select">
    <option value="">-- Pilih Role --</option>
    @foreach ($roles as $role)
    <option value="{{ $role->name }}"
        {{ (isset($user) && $user->roles->pluck('name')->contains($role->name)) ? 'selected' : '' }}>
        {{ $role->name }}
    </option>
    @endforeach
    </select>
</div>

<div class="col-md-6">
    <label for="is_active" class="form-label">Status Akun</label>
    <select name="is_active" id="is_active" class="form-select">
        <option value="1" {{ old('is_active', $user->is_active ?? 1) == 1 ? 'selected' : '' }}>Aktif</option>
        <option value="0" {{ old('is_active', $user->is_active ?? 1) == 0 ? 'selected' : '' }}>Nonaktif</option>
    </select>
</div>

<div class="d-flex justify-content-center">
    <button type="submit" class="btn btn-success me-2">{{ $submit ?? 'Simpan' }}</button>
    <a href="{{ route('admin.users.index') }}" class="btn btn-danger me-2">Kembali</a>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    const companyField = document.getElementById('company-field');
    function toggleCompanyField() {
        const selectedRole = roleSelect.value;
        if (selectedRole === 'Reviewer' || selectedRole === 'Main Admin') {
            companyField.style.display = 'none';
            companyField.querySelector('select').value = 'AASI';
        } else {
        companyField.style.display = 'block';
        }
    }
    roleSelect.addEventListener('change', toggleCompanyField);
    toggleCompanyField();
});
</script>
@endsection