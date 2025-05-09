<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="E-Tanggapan AASI &amp; E-Tanggapan Anggota Asosiasi Asuransi Syariah Indonesia">
	<meta name="author" content="Asosiasi Asuransi Syariah Indonesia">
	<meta name="keywords" content="e-tanggapan, e-tanggapan aasi, e-tanggapan.aasi, e-tanggapan.aasi.or.id">

	<link rel="shortcut icon" href="{{ asset('img/icons/icon-48x48.png') }}">
    <link rel="canonical" href="https://e-tanggapan.test">
    <title>E-Tanggapan AASI</title>

    <!-- Fonts & CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('frontend/css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css">

    <!-- JS Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    @if(auth()->user()?->hasRole('Main Admin'))
        <script src="{{ asset('frontend/js/app.js') }}"></script>
    @else
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @endif