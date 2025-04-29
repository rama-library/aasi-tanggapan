<table>
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
    <tr><td colspan="{{ $jenis == 'full' ? 10 : 9 }}">&nbsp;</td></tr> {{-- Spasi sebelum tabel utama --}}
</table>


@php
    $grouped = $result->groupBy(fn($item) => $item->pasal->id ?? 0);
    $rowNumber = 1;
@endphp

@foreach ($grouped as $pasalId => $items)
    @foreach ($items as $index => $r)
        <tr>
            @if ($index == 0)
                <td rowspan="{{ $items->count() }}">{{ $rowNumber }}</td>
                <td rowspan="{{ $items->count() }}">{{ $r->document->no_document ?? '-' }}</td>
                <td rowspan="{{ $items->count() }}">{{ $r->pasal->pasal ?? '-' }}</td>
                <td rowspan="{{ $items->count() }}">{{ $r->pasal->penjelasan ?? '-' }}</td>
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