@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h4 class="mb-4">Rekap Tanggapan</h4>

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
                <option value="beserta perubahan" {{ request('type') == 'beserta perubahan' ? 'selected' : '' }}>Laporan Beserta Perubahan</option>
            </select>
        </div>

        <div class="col-md-3">
            <label for="search" class="form-label">Cari</label>
            <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control" placeholder="Cari Isi, Konten atau Tanggapan">
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
                    @include('partials.table_header', ['document' => $document, 'columns' => 'report'])
                    <tbody>
                        @php
                            $rowNumber = ($result->currentPage() - 1) * $result->perPage() + 1;
                        @endphp

                        @foreach ($result as $content)
                            @php
                                $responds = $content->respond;
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
                                            <td class="text-center" rowspan="{{ $filteredResponds->count() }}">{{ $rowNumber }}</td>
                                            <td class="text-justify" rowspan="{{ $filteredResponds->count() }}">{{ $content->contents ?? '-' }}</td>
                                            <td class="text-justify" rowspan="{{ $filteredResponds->count() }}">
                                                @if ($content->detil && $content->gambar)
                                                <img src="{{ asset('storage/' . $content->gambar) }}" class="img-fluid" alt="Gambar detil">
                                                <br><p>{{ $content->detil }}</p>
                                                @elseif ($content->gambar)
                                                <img src="{{ asset('storage/' . $content->gambar) }}" class="img-fluid" alt="Gambar detil">
                                                @elseif ($content->detil)
                                                <p>{{ $content->detil }}</p>
                                                @else
                                                    <p><em> </em></p>
                                                @endif                                                
                                            </td>
                                            @php $rowNumber++; @endphp
                                        @endif

                                        <td class="text-justify">
                                            @if ($r->is_deleted && $jenis === 'beserta perubahan')
                                                <del class="muted">{{ json_decode($r->original_data)->tanggapan ?? '-' }}</del>
                                                <!--<br>(Dihapus oleh reviewer)-->
                                            @elseif ($r->is_deleted && $jenis === 'final')
                                                -
                                            @else
                                                {{ $r->tanggapan ?? '-' }}
                                                <!--@if ($jenis === 'beserta perubahan' && $r->original_data)-->
                                                <!--    <br>-->
                                                <!--    <small class="muted">-->
                                                <!--        <i>(Tanggapan Sebelumnya: {{ json_decode($r->original_data)->tanggapan ?? '-' }})</i>-->
                                                <!--    </small>-->
                                                <!--@endif-->
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $r->pic->name ?? '-' }}</td>
                                        <td class="text-center">{{ $r->perusahaan ?? '-' }}</td>
                                        <td class="text-center">{{ $r->created_at->format('d M Y') ?? '-' }}</td>
                                        <td class="text-center">
                                            @if($r->histories->count())
                                                <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#historyModal{{ $r->id }}">
                                                    <i class="fas fa-history"></i> History Reviewer
                                                </button>
                                            @else
                                                @if($r->is_deleted)
                                                    <span class="text-danger fw-bold">{{ $r->reviewer->name ?? '-' }}</span>
                                                @else
                                                    {{ $r->reviewer->name ?? '-' }}
                                                @endif
                                            @endif
                                        </td>
                                        <td class="text-danger text-justify">{{ $r->alasan ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td>{{ $rowNumber }}</td>
                                    <td class="text-justify">{{ $content->contents ?? '-' }}</td>
                                    <td class="text-justify">
                                        @if ($content->detil && $content->gambar)
                                        <img src="{{ asset('storage/' . $content->gambar) }}" class="img-fluid" alt="Gambar detil">
                                        <br><p>{{ $content->detil }}</p>
                                        @elseif ($content->gambar)
                                        <img src="{{ asset('storage/' . $content->gambar) }}" class="img-fluid" alt="Gambar detil">
                                        @elseif ($content->detil)
                                        <p>{{ $content->detil }}</p>
                                        @else
                                            <p><em>Tidak ada detil atau gambar.</em></p>
                                        @endif  
                                    </td>
                                    <td colspan="6" class="text-center">-</td>                                    
                                </tr>
                                @php $rowNumber++; @endphp
                            @endif
                        @endforeach
                    </tbody>
                </table>
                    @foreach($result as $content)
                        @foreach($content->respond as $r)
                            @if($r->histories->count())
                            <div class="modal fade" id="historyModal{{ $r->id }}" tabindex="-1" aria-labelledby="historyModalLabel{{ $r->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="historyModalLabel{{ $r->id }}">Riwayat Review Tanggapan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <table class="table table-bordered">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Reviewer</th>
                                                        <th>Tanggapan Lama</th>
                                                        <th>Tanggapan Baru</th>
                                                        <th>Alasan</th>
                                                        <th>Tanggal Review</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($r->histories as $index => $history)
                                                        <tr>
                                                            <td>{{ $index + 1 }}</td>
                                                            <td>{{ $history->reviewer->name ?? '-' }}</td>
                                                            <td>{{ $history->old_tanggapan ?? '-' }}</td>
                                                            <td>{{ $history->new_tanggapan ?? '-' }}</td>
                                                            <td>{{ $history->alasan ?? '-' }}</td>
                                                            <td>{{ \Carbon\Carbon::parse($history->reviewed_at)->format('d M Y') }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    @endforeach
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
