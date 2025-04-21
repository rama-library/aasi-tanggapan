<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Konfirmasi Perangkat Baru</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<script>
    Swal.fire({
        icon: 'warning',
        title: 'Perangkat Berbeda Terdeteksi!',
        text: 'Akun ini sedang aktif di perangkat lain. Lanjutkan login dan logout perangkat sebelumnya?',
        showCancelButton: true,
        confirmButtonText: 'Ya, Lanjutkan',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ url("/force-login") }}';

            const email = document.createElement('input');
            email.type = 'hidden';
            email.name = 'email';
            email.value = '{{ $email }}';

            const password = document.createElement('input');
            password.type = 'hidden';
            password.name = 'password';
            password.value = '{{ $password }}';

            const token = document.createElement('input');
            token.type = 'hidden';
            token.name = '_token';
            token.value = '{{ csrf_token() }}';

            form.appendChild(email);
            form.appendChild(password);
            form.appendChild(token);
            document.body.appendChild(form);
            form.submit();
        } else {
            window.location.href = "{{ url('/') }}";
        }
    });
</script>

</body>
</html>
