@php
    // Ambil slug tipe dokumen
    $slug = $document->documentType->slug ?? '';

    // Default label
    $col1 = 'Batang Tubuh';
    $col2 = 'Penjelasan';

    switch ($slug) {
        case 'ad-atau-art':
            $col1 = 'Klausul';
            $col2 = 'Penjelasan';
            break;

        case 'kompilasi-peraturan':
            $col1 = 'Peraturan';
            $col2 = 'Keterangan / Penjelasan';
            break;

        case 'peraturan':
            $col1 = 'Batang Tubuh';
            $col2 = 'Penjelasan';
            break;

        default:
            $col1 = 'Halaman / Isi';
            $col2 = 'Penjelasan';
            break;
    }
@endphp

<thead>
    <tr>
        {{-- Tampilan untuk tabel dengan kolom lengkap --}}
        @if(isset($columns) && $columns === 'full')
            <th class="text-center">{{ $col1 }}</th>
            <th class="text-center">{{ $col2 }}</th>
            <th class="text-center">Tanggapan</th>
            <th class="text-center">Nama</th>
            <th class="text-center">Perusahaan</th>
            <th class="text-center">Reviewer</th>
            <th class="text-center">Alasan</th>
            <th class="text-center">Aksi</th>

        {{-- Tampilan untuk laporan / report --}}
        @elseif(isset($columns) && $columns === 'report')
            <th class="text-center">No</th>
            <th class="text-center">{{ $col1 }}</th>
            <th class="text-center">{{ $col2 }}</th>
            <th class="text-center">Tanggapan</th>
            <th class="text-center">Nama</th>
            <th class="text-center">Perusahaan</th>
            <th class="text-center">Tanggal</th>
            <th class="text-center">Reviewer</th>
            <th class="text-center">Alasan</th>
        
        @elseif(isset($columns) && $columns === 'pdf')
            <th style="width: 20%;">{{ $col1 }}</th>
            <th style="width: 25%;">{{ $col2 }}</th>
            <th style="width: 15%;">Tanggapan</th>
            <th style="width: 10%;">Nama</th>
            <th style="width: 10%;">Perusahaan</th>
            <th style="width: 10%;">Reviewer</th>
            <th style="width: 7%;">Alasan</th>
        
        @elseif(isset($columns) && $columns === 'reviewer')
            <th class="text-center">{{ $col1 }}</th>
            <th class="text-center">{{ $col2 }}</th>
            <th class="text-center">Tanggapan</th>
            <th class="text-center">Nama</th>
            <th class="text-center">Perusahaan</th>
            <th class="text-center">Reviewer</th>
            <th class="text-center">Alasan</th>
            @if (auth()->user()->hasRole('Reviewer'))
                <th class="text-center">Aksi</th>
            @endif
            
        @elseif(isset($columns) && $columns === 'excel')
            <th>No</th>
            <th>{{ $col1 }}</th>
            <th>{{ $col2 }}</th>
            <th>Tanggapan</th>
            <th>Nama</th>
            <th>Perusahaan</th>
            <th>Reviewer</th>
            <th>Alasan</th>
            @if($jenis == 'full')
                <th>Data Sebelumnya</th>
            @endif
            
        @elseif(isset($columns) && $columns === 'today')
            <th class="text-center">No</th>
            <th class="text-center">No Dokumen</th>
            <th class="text-center">{{ $col1 }}</th>
            <th class="text-center">Tanggapan</th>
            <th class="text-center">Nama</th>
            <th class="text-center">Perusahaan</th>

        {{-- Default untuk halaman show document --}}
        @else
            <th class="text-center">No</th>
            <th class="text-center">{{ $col1 }}</th>
            <th class="text-center">{{ $col2 }}</th>
            <th class="text-center">Aksi</th>
        @endif
    </tr>
</thead>
