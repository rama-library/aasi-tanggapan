@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h4 class="mb-4">Laporan Tanggapan</h4>

    {{-- Filter --}}
    <form method="GET" class="row mb-4">
        <div class="col-md-3">
            <label for="document" class="form-label">Pilih Dokumen</label>
            <select name="document" id="document" class="form-select">
                <option value="">-- Pilih Dokumen --</option>
                @foreach ($documents as $doc)
                    <option value="{{ $doc->id }}" {{ request('document') == $doc->id ? 'selected' : '' }}>
                        {{ $doc->no_document }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label for="type" class="form-label">Jenis Laporan</label>
            <select name="type" id="type" class="form-select">
                <option value="final" {{ request('type') == 'final' ? 'selected' : '' }}>Laporan Final</option>
                <option value="full" {{ request('type') == 'full' ? 'selected' : '' }}>Laporan Beserta Perubahan</option>
            </select>
        </div>

        <div class="col-md-3">
            <label for="search" class="form-label">Cari</label>
            <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control" placeholder="Cari pasal atau tanggapan">
        </div>

        <div class="col-md-3 d-flex align-items-end">
            <button class="btn btn-primary me-2" type="submit">
                <i data-feather="search" class="me-1"></i> Filter
            </button>

            @if($result->count())
                <a href="{{ route('laporan.export', ['document' => request('document'), 'type' => request('type'), 'format' => 'excel']) }}" class="btn btn-success me-2">
                    <i data-feather="download" class="me-1"></i> Excel
                </a>
                <a target="_blank" href="{{ route('laporan.export', ['document' => request('document'), 'type' => request('type'), 'format' => 'pdf']) }}" class="btn btn-danger">
                    <i data-feather="download" class="me-1"></i> PDF
                </a>
            @endif
        </div>
    </form>

    {{-- Table --}}
    @if($result->count())
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr class="table-light">
                            <th>No</th>
                            <th>Dokumen</th>
                            <th>Pasal</th>
                            <th>Penjelasan</th>
                            <th>Tanggapan</th>
                            <th>PIC</th>
                            <th>Perusahaan</th>
                            <th>Reviewer</th>
                            <th>Alasan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $rowNumber = ($result->currentPage() - 1) * $result->perPage() + 1;
                        @endphp

                        @foreach ($result as $pasal)
                            @php
                                $responds = $pasal->respond;
                                $filteredResponds = $responds;

                                // Kalau tipe final, buang data yang dihapus
                                if($jenis === 'final') {
                                    $filteredResponds = $responds->where('is_deleted', false);
                                }
                            @endphp

                            @if($filteredResponds->count())
                                @foreach ($filteredResponds as $index => $r)
                                    <tr>
                                        @if ($index == 0)
                                            <td rowspan="{{ $filteredResponds->count() }}">{{ $rowNumber }}</td>
                                            <td rowspan="{{ $filteredResponds->count() }}">{{ $selectedDocument->no_document ?? '-' }}</td>
                                            <td rowspan="{{ $filteredResponds->count() }}">{{ $pasal->pasal ?? '-' }}</td>
                                            <td rowspan="{{ $filteredResponds->count() }}">{{ $pasal->penjelasan ?? '-' }}</td>
                                            @php $rowNumber++; @endphp
                                        @endif

                                        <td>
                                            @if ($r->is_deleted && $jenis === 'full')
                                                <del class="muted">{{ json_decode($r->original_data)->tanggapan ?? '-' }}</del>
                                                <br>(Dihapus oleh reviewer)
                                            @elseif ($r->is_deleted && $jenis === 'final')
                                                -
                                            @else
                                                {{ $r->tanggapan ?? '-' }}
                                                @if ($jenis === 'full' && $r->original_data)
                                                    <br>
                                                    <small class="muted">
                                                        <i>(Tanggapan Sebelumnya: {{ json_decode($r->original_data)->tanggapan ?? '-' }})</i>
                                                    </small>
                                                @endif
                                            @endif
                                        </td>
                                        <td>{{ $r->pic->name ?? '-' }}</td>
                                        <td>{{ $r->perusahaan ?? '-' }}</td>
                                        <td>{{ $r->reviewer->name ?? '-' }}</td>
                                        <td class="text-danger">{{ $r->alasan ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td>{{ $rowNumber }}</td>
                                    <td>{{ $selectedDocument->no_document ?? '-' }}</td>
                                    <td>{{ $pasal->pasal ?? '-' }}</td>
                                    <td>{{ $pasal->penjelasan ?? '-' }}</td>
                                    <td colspan="5" class="text-center">-</td>                                    
                                </tr>
                                @php $rowNumber++; @endphp
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-end">
                {{ $result->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
    @else
        <div class="alert alert-info">
            Silakan pilih dokumen dan jenis laporan untuk melihat data.
        </div>
    @endif
</div>
@endsection
