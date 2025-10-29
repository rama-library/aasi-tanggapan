@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h4 class="mb-3">Detail</strong></h4>
    @php
        $docType = strtolower($document->documentType->name ?? '');
        $label1 = 'Batang Tubuh';
        $label2 = 'Penjelasan';
    
        if (in_array($docType, ['ad', 'art'])) {
            $label1 = 'Klausul';
            $label2 = 'Penjelasan';
        } elseif ($docType === 'kompilasi peraturan') {
            $label1 = 'Peraturan';
            $label2 = 'Keterangan / Penjelasan';
        } elseif (!in_array($docType, ['peraturan', 'ad', 'art', 'kompilasi peraturan'])) {
            $label1 = 'Halaman / Isi';
            $label2 = 'Penjelasan';
        }
    @endphp
    
    <div class="card mb-4">
        <div class="card-body">
            <p class="text-justify"><strong>{{ $label1 }}:</strong> {{ $content->contents }}</p>
            <p class="text-justify">
                <strong>{{ $label2 }}: </strong>
                @if ($content->detil && $content->gambar)
                    <br><img src="{{ asset('storage/' . $content->gambar) }}" class="img-fluid" width="200" alt="Gambar detil">
                    <p>{{ $content->detil }}</p>
                @elseif ($content->gambar)
                    <br><img src="{{ asset('storage/' . $content->gambar) }}" class="img-fluid" width="200" alt="Gambar detil">
                @elseif ($content->detil)
                    <p>{{ $content->detil }}</p>
                @else
                    <p><em> </em></p>
                @endif
            </p>
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
                            <th class="text-center">No</th>
                            <th class="text-center">Perusahaan</th>
                            <th class="text-center">Nama</th>
                            <th class="text-center">Tanggapan</th>
                            <th class="text-center">Reviewer</th>
                            <th class="text-center">Alasan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($content->respond as $index => $respond)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="text-center">{{ $respond->perusahaan ?? '-' }}</td>
                                <td class="text-center">{{ $respond->pic_name }}</td>
                                <td class="text-justify">{{ $respond->tanggapan ?? '-' }}</td>
                                <td class="text-center">
                                    @if($respond->histories->count())
                                        <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#historyModal{{ $respond->id }}">
                                            <i class="fas fa-history"></i> History Reviewer
                                        </button>
                                    @else
                                        {{ $respond->reviewer->name ?? '-' }}
                                    @endif
                                </td>
                                <td class="text-justify">{{ $respond->alasan ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{-- Modal History Reviewer --}}
                @foreach($content->respond as $respond)
                    @if($respond->histories->count())
                        <div class="modal fade" id="historyModal{{ $respond->id }}" tabindex="-1" aria-labelledby="historyModalLabel{{ $respond->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title" id="historyModalLabel{{ $respond->id }}">
                                            Riwayat Review Tanggapan — {{ $respond->pic->name ?? 'PIC Tidak Dikenal' }}
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <table class="table table-bordered table-striped align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="text-center">No</th>
                                                    <th class="text-center">Reviewer</th>
                                                    <th class="text-center">Tanggapan Lama</th>
                                                    <th class="text-center">Tanggapan Baru</th>
                                                    <th class="text-center">Alasan</th>
                                                    <th class="text-center">Tanggal Review</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($respond->histories as $index => $history)
                                                    <tr>
                                                        <td class="text-center">{{ $index + 1 }}</td>
                                                        <td class="text-center">{{ $history->reviewer->name ?? '-' }}</td>
                                                        <td class="text-justify">{!! $history->old_tanggapan ?? '-' !!}</td>
                                                        <td class="text-justify">{!! $history->new_tanggapan ?? '-' !!}</td>
                                                        <td class="text-justify">{{ $history->alasan ?? '-' }}</td>
                                                        <td class="text-center">{{ \Carbon\Carbon::parse($history->reviewed_at)->format('d M Y') }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection