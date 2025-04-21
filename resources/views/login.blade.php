<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>E - Tanggapan | AASI</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('assets/admin/img/logoaw2.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="https://unpkg.com/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<section class="h-100 gradient-form" style="background-color: #eee;">
    <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-xl-10">
                <div class="card rounded-3 text-black">
                    <div class="row g-0">
                        <div class="col-lg-6">
                            <div class="card-body p-md-5 mx-md-4">
                                <div class="text-center">
                                    <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-login-form/lotus.webp"
                                         style="width: 185px;" alt="logo">
                                    <h4 class="mt-1 mb-5 pb-1">E - Tanggapan AASI</h4>
                                </div>

                                <form method="POST" action="{{ url('/login') }}" id="loginForm">
                                    @csrf
                                    <p>Please login to your account</p>

                                    <div class="form-outline mb-4">
                                        <input type="email" name="email" class="form-control"
                                               placeholder="Email" />
                                        <label class="form-label">Email</label>
                                    </div>

                                    <div class="form-outline mb-4">
                                        <input type="password" name="password" class="form-control"
                                               placeholder="Password" />
                                        <label class="form-label">Password</label>
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-block">Login</button>
                                </form>
                            </div>
                        </div>
                        <div class="col-lg-6 d-flex align-items-center gradient-custom-2" style="background-color: #37517e;">
                            <div class="text-white px-3 py-4 p-md-5 mx-md-4">
                                <h4 class="mb-4">We are more than just a company</h4>
                                <p class="small mb-0">
                                    Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                                    tempor incididunt ut labore et dolore magna aliqua.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
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
{{-- Client-side empty field validation --}}
<script>
    document.getElementById('loginForm').addEventListener('submit', function (e) {
        const email = this.email.value.trim();
        const password = this.password.value.trim();

        if (!email || !password) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Field kosong!',
                text: 'Email dan Password harus diisi!'
            });
        }
    });
</script>
@if (session('force_login'))
    <script>
        Swal.fire({
            title: '{{ session("alert_title") }}',
            text: '{{ session("alert") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Logout Device Lama',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("force.login") }}';
                form.innerHTML = `
                    @csrf
                `;
                document.body.appendChild(form);
                form.submit();
            }
        });
    </script>
@endif
</body>
</html>
