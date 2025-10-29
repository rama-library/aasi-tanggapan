@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h4 class="mb-4">Daftar PIC Tidak Memberi Tanggapan</h4>

    <form method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" name="no_document" class="form-control" placeholder="Cari No Dokumen..." value="{{ request('no_document') }}">
            <button class="btn btn-primary" type="submit">Cari</button>
        </div>
    </form>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                        <th class="text-center">No</th>
                        <th class="text-center">Nama PIC</th>
                        <th class="text-center">Perusahaan</th>
                        <th class="text-center">Department</th>
                        <th class="text-center">No Dokumen</th>
                        <th class="text-center">Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $index => $item)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="text-center">{{ $item->pic->name ?? '-' }}</td>
                            <td class="text-center">{{ $item->perusahaan ?? '-' }}</td>
                            <td class="text-center">{{ $item->department ?? '-' }}</td>
                            <td class="text-center">{{ $item->document->no_document ?? '-' }}</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($item->created_at)->isoFormat('D MMMM Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Belum ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3">
                {{ $data->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>
@endsection
