<!-- SweetAlert + Validasi -->

@if ($errors->any())
    <script>
        Swal.fire({
            title: 'Validation Error!',
            html: `{!! implode('<br>', $errors->all()) !!}`,
            icon: 'error',
            confirmButtonText: 'OK'
        });
    </script>
@endif

@if (session('alert'))
    <script>
        Swal.fire({
            icon: '{{ session("alert_type") }}',
            title: '{{ session("alert_title") }}',
            text: '{{ session("alert") }}',
            confirmButtonText: 'OK'
        });
    </script>
@endif

<!-- Script Tambahan -->
<script>
    document.addEventListener('DOMContentLoaded', () => feather.replace());

    function confirmLogout() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You will be logged out!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, logout!',
            cancelButtonText: 'Cancel'
        }).then(result => {
            if (result.isConfirmed) document.getElementById('logout-form').submit();
        });
    }

    function confirmDelete(formId) {
        Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: "Data tidak dapat dikembalikan setelah dihapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then(result => {
            if (result.isConfirmed) document.getElementById(formId).submit();
        });
    }

    function showChangePasswordForm() {
        Swal.fire({
            title: 'Ganti Password',
            html: `
                <input type="password" id="new_password" class="swal2-input" placeholder="Password Baru">
                <input type="password" id="new_password_confirmation" class="swal2-input" placeholder="Konfirmasi Password Baru">`,
            focusConfirm: false,
            confirmButtonText: 'Simpan',
            showCancelButton: true,
            cancelButtonText: 'Batal',
            preConfirm: () => {
                const pass = document.getElementById('new_password').value;
                const confirm = document.getElementById('new_password_confirmation').value;

                if (!pass || !confirm) return Swal.showValidationMessage('Semua field wajib diisi!');
                if (pass.length < 8) return Swal.showValidationMessage('Password baru minimal 8 karakter!');
                if (pass !== confirm) return Swal.showValidationMessage('Konfirmasi password tidak cocok!');

                return { password: pass, confirm };
            }
        }).then(result => {
            if (result.isConfirmed) {
                fetch("{{ route('password.update.self') }}", {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        new_password: result.value.password,
                        new_password_confirmation: result.value.confirm
                    })
                })
                .then(res => res.json())
                .then(res => Swal.fire(res.status === 'success' ? 'Berhasil!' : 'Gagal!', res.message, res.status))
                .catch(() => Swal.fire('Gagal!', 'Terjadi kesalahan saat mengubah password.', 'error'));
            }
        });
    }

    function hapusTanggapan(url) {
        Swal.fire({
            title: 'Yakin ingin menghapus tanggapan ini?',
            input: 'textarea',
            inputLabel: 'Alasan penghapusan',
            inputPlaceholder: 'Tulis alasan di sini...',
            showCancelButton: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal',
            inputValidator: value => !value && 'Alasan wajib diisi!'
        }).then(result => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;

                ['_token', '{{ csrf_token() }}', '_method', 'DELETE', 'alasan', result.value].forEach((val, i) => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = ['_token', '_method', 'alasan'][i / 2 | 0];
                    input.value = val;
                    form.appendChild(input);
                });

                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    $(document).ready(() => {
        $('#alltable').DataTable({
            responsive: true,
            autoWidth: false
        });
    });
</script>