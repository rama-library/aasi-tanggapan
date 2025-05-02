@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h4 class="mb-3">Detail Batang Tubuh</strong></h4>

    {{-- Detail Pasal --}}
    <div class="card mb-4">
        <div class="card-body">            
            <p class="text-justify"><strong>Batang Tubuh:</strong> {{ $pasal->pasal }}</p>
            <p class="text-justify"><strong>Penjelasan:</strong> {{ $pasal->penjelasan }}</p>
        </div>
    </div>

    {{-- Daftar Tanggapan --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Tanggapan</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="alltable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Perusahaan</th>
                            <th>PIC</th>
                            <th>Tanggapan</th>
                            <th>Reviewer</th>
                            <th>Alasan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pasal->respond as $index => $respond)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $respond->perusahaan ?? '-' }}</td>
                                <td>{{ $respond->pic_name }}</td>
                                <td>{{ $respond->tanggapan ?? '-' }}</td>
                                <td>{{ $respond->reviewer_name }}</td>
                                <td>{{ $respond->alasan ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection