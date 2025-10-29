<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('btnNoRespond')?.addEventListener('click', function() {
    Swal.fire({
        title: 'Yakin Tidak Ada Tanggapan?',
        text: "Tindakan ini akan menandai Anda tidak memberikan tanggapan untuk dokumen ini.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Konfirmasi',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch("{{ route('respond.noRespond', $document->slug) }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json"
                }
            })
            .then(res => res.json())
            .then(data => {
                Swal.fire({
                    icon: data.status,
                    title: data.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => location.reload());
            })
            .catch(() => Swal.fire('Error', 'Terjadi kesalahan, coba lagi.', 'error'));
        }
    });
});
</script>
