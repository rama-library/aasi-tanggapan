<table>
    {{-- Baris 1–3: Judul Laporan --}}
    <tr>
        <td colspan="{{ $jenis == 'full' ? 10 : 9 }}" style="font-weight: bold; font-size: 16px; text-align: center;">
            LAPORAN TANGGAPAN DOKUMEN
        </td>
    </tr>
    <tr>
        <td colspan="{{ $jenis == 'full' ? 10 : 9 }}" style="text-align: center;">
            Dokumen: {{ $document->no_document }} - {{ $document->perihal }}
        </td>
    </tr>
    <tr>
        <td colspan="{{ $jenis == 'full' ? 10 : 9 }}" style="text-align: center;">
            Jenis Laporan: {{ strtoupper($jenis === 'final' ? 'Final' : 'Full dengan Perubahan') }}
        </td>
    </tr>

    {{-- Baris kosong --}}
    <tr><td colspan="{{ $jenis == 'full' ? 10 : 9 }}">&nbsp;</td></tr>

    {{-- Header Kolom --}}
    <thead>
        <tr>
            <th>No</th>
            <th>Dokumen</th>
            <th>Pasal</th>
            <th>Penjelasan / Gambar</th>
            <th>Tanggapan</th>
            <th>PIC</th>
            <th>Perusahaan</th>
            <th>Reviewer</th>
            <th>Alasan</th>
            @if($jenis == 'full')
                <th>Data Sebelumnya</th>
            @endif
        </tr>
    </thead>

    <tbody>
        @php
            $grouped = $result->groupBy(fn($item) => $item->batangtubuh->id ?? 0);
            $rowNumber = 1;
        @endphp

        @foreach ($grouped as $batangtubuhId => $items)
            @foreach ($items as $index => $r)
                <tr>
                    @if ($index == 0)
                        <td rowspan="{{ $items->count() }}">{{ $rowNumber }}</td>
                        <td rowspan="{{ $items->count() }}">{{ $r->document->no_document ?? '-' }}</td>
                        <td rowspan="{{ $items->count() }}">{{ $r->batangtubuh->batang_tubuh ?? '-' }}</td>
                        <td rowspan="{{ $items->count() }}">
                            @if ($r->batangtubuh->gambar)
                                {{-- Gambar akan ditambahkan via WithDrawings, jangan tampilkan di sini --}}
                            @elseif ($r->batangtubuh->penjelasan)
                                {{ $r->batangtubuh->penjelasan }}
                            @else
                                <em>Tidak ada penjelasan atau gambar.</em>
                            @endif
                        </td>
                        @php $rowNumber++; @endphp
                    @endif

                    <td>
                        @if ($r->tanggapan)
                            {{ $r->is_deleted ? 'Dihapus' : $r->tanggapan }}
                        @else
                            Tidak ada tanggapan
                        @endif
                    </td>
                    <td>{{ $r->pic->name ?? '-' }}</td>
                    <td>{{ $r->perusahaan ?? '-' }}</td>
                    <td>{{ $r->reviewer->name ?? '-' }}</td>
                    <td>{{ $r->alasan ?? '-' }}</td>

                    @if($jenis == 'full')
                        <td>
                            @if ($r->original_data)
                                {{ json_decode($r->original_data)->tanggapan ?? '-' }}
                            @else
                                -
                            @endif
                        </td>
                    @endif
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>
