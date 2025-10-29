<table>
    {{-- HEADER --}}
    @php
        $jenisSentence = ucfirst(strtolower(str_replace('_', ' ', $jenis)));
    @endphp

    <tr>
        <td colspan="{{ $jenis == 'full' ? 9 : 8 }}" style="font-weight: bold; font-size: 16px; text-align: center;">
            LAPORAN DOKUMEN {{ strtoupper($jenisSentence) }}
        </td>
    </tr>
    <tr>
        <td colspan="{{ $jenis == 'full' ? 9 : 8 }}" style="text-align: center;">
            No. Dokumen: {{ $document->no_document }}
        </td>
    </tr>
    <tr>
        <td colspan="{{ $jenis == 'full' ? 9 : 8 }}" style="text-align: center;">
            Perihal: {{ $document->perihal }}
        </td>
    </tr>

    {{-- Baris kosong --}}
    <tr><td colspan="{{ $jenis == 'full' ? 9 : 8 }}">&nbsp;</td></tr>

    {{-- HEADER KOLOM --}}
    @include('partials.table_header', ['document' => $document, 'columns' => 'excel'])

    <tbody>
        @php
            $grouped = $result->groupBy(fn($item) => $item->content->id ?? 0);
            $rowNumber = 1;
        @endphp

        @foreach ($grouped as $contentId => $items)
            @foreach ($items as $index => $r)
                <tr>
                    @if ($index == 0)
                        <td rowspan="{{ $items->count() }}">{{ $rowNumber }}</td>
                        <td rowspan="{{ $items->count() }}">{{ $r->content->contents ?? '-' }}</td>
                        <td rowspan="{{ $items->count() }}">
                            @if ($r->content->gambar)
                                {{-- Gambar ditambahkan via WithDrawings --}}
                            @elseif ($r->content->detil)
                                {{ $r->content->detil }}
                            @else
                                <em>-</em>
                            @endif
                        </td>
                        @php $rowNumber++; @endphp
                    @endif

                    <td>
                        @if ($r->is_deleted)
                            <del>{{ json_decode($r->original_data)->tanggapan ?? '-' }}</del>
                            (Dihapus oleh reviewer)
                        @else
                            {{ $r->tanggapan ?? '-' }}
                            @if ($jenis === 'full' && $r->original_data)
                                <br><small><i>(Sebelum revisi: {{ json_decode($r->original_data)->tanggapan ?? '-' }})</i></small>
                            @endif
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
