@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h4 class="mb-3">Berikan Tanggapan</h4>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="alltable">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">No Dokumen</th>
                            <th class="text-center">Perihal</th>
                            <th class="text-center">Tanggal Upload</th>
                            <th class="text-center">Due Date</th>
                            <th class="text-center">Due Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($documents as $index => $doc)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><a href="{{ route('berikan.tanggapan.detail', $doc->slug) }}">{{ $doc->no_document }}</a></td>
                            <td>{{ $doc->perihal }}</td>
                            <td>{{ $doc->created_at->format('d M Y') }}</td>
                            <td>{{ $doc->formatted_due_date }}</td>
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