@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h4 class="mb-3">Tanggapan Hari Ini</h4>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="alltable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>No Dokumen</th>
                            <th>Batang Tubuh</th>
                            <th>Tanggapan</th>
                            <th>PIC</th>
                            <th>Perusahaan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tanggapanHariIni as $index => $t)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $t->document->no_document ?? '-' }}</td>
                            <td>{{ $t->batangtubuh->batang_tubuh ?? '-' }}</td>
                            <td>{{ $t->tanggapan ?? '-' }}</td>
                            <td>{{ $t->pic->name ?? '-' }}</td>
                            <td>{{ $t->perusahaan() ?? '-' }}</td> <!-- Memanggil method perusahaan() -->
                        </tr>                        
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection