@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h4 class="mb-3">Tanggapan Berlangsung</h4>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="alltable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>No Dokumen</th>
                            <th>Perihal</th>
                            <th>Tanggal Upload</th>
                            <th>Due Date</th>
                            <th>Due Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($documents as $index => $doc)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><a href="{{ route('tanggapan.berlangsung.detail', $doc->slug) }}">{{ $doc->no_document }}</a></td>
                            <td>{{ $doc->perihal }}</td>
                            <td>{{ $doc->created_at->format('d M Y') }}</td>
                            <td>{{ $doc->due_date_formatted }}</td>
                            <td>{{ $doc->due_time ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection