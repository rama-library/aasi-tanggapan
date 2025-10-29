<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Dokumen</title>

    <style>
        /* Kurangi margin atas halaman supaya header lebih dekat */
        @page {
            margin: 95px 30px 80px 30px;
        }
    
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 15px;
            color: #000;
            margin: 0;
            padding: 0;
        }
    
        /* HEADER HANYA DI HALAMAN PERTAMA */
        header {
            position: running(header);
            height: 70px; /* lebih pendek agar proporsional */
            border-bottom: 2px solid #000;
            /*padding: 5px 15px 0 15px;*/
            text-align: center;
        }
    
        @page:first {
            @top-center {
                content: element(header);
            }
        }
    
        /* KONTEN */
        main {
            margin-top: 15px; /* tambahkan sedikit jarak agar tabel tidak nabrak header */
        }
    
        table {
            margin-top: 20px;
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            word-wrap: break-word;
            page-break-inside: auto;
        }
    
        thead {
            display: table-row-group !important;
        }
    
        tr {
            page-break-inside: auto;
        }
    
        th, td {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
            text-align: justify;
            word-break: break-word;
        }
    
        th {
            background-color: #f2f2f2;
            text-align: center;
        }
    
        td img {
            display: block;
            margin: 5px auto;
            max-width: 180px;
            height: auto;
        }
    
        h2 {
            margin: 0;
            padding: 0;
        }
    
        small {
            font-size: 15px;
        }
    </style>
</head>

<body>
    <!-- HEADER -->
    <header>
        <h2 style="margin-bottom: 3px;">Rekap Tanggapan {{ ucwords(str_replace('_', ' ', $jenis)) }}</h2>
        <small>No. Dokumen: {{ $document->no_document }}</small><br>
        <small>Perihal: {{ $document->perihal }}</small>
    </header>

    <!-- KONTEN UTAMA -->
    <main>
        <table>
            @include('partials.table_header', ['document' => $document, 'columns' => 'pdf'])
            <tbody>
                @php
                    $grouped = $result->groupBy(function ($item) {
                        return $item->content->contents . '|' . $item->content->detil . '|' . $item->content->gambar;
                    });
                @endphp

                @foreach ($grouped as $key => $group)
                    @foreach ($group as $i => $respond)
                        <tr>
                            @if ($i === 0)
                                <td rowspan="{{ $group->count() }}">
                                    {{ $respond->content->contents }}
                                </td>
                                <td rowspan="{{ $group->count() }}">
                                    @if ($respond->content->gambar)
                                        <img src="{{ public_path('storage/' . $respond->content->gambar) }}" alt="Gambar">
                                    @elseif ($respond->content->detil)
                                        {{ $respond->content->detil }}
                                    @else
                                        <em>-</em>
                                    @endif
                                </td>
                            @endif

                            <td>
                                @if ($respond->is_deleted)
                                    <del>{{ json_decode($respond->original_data)->tanggapan ?? '-' }}</del>
                                    <br><small>(Dihapus oleh reviewer)</small>
                                @else
                                    {{ $respond->tanggapan ?? '-' }}
                                    @if ($jenis === 'full' && $respond->original_data)
                                        <br><small><i>(Sebelum revisi: {{ json_decode($respond->original_data)->tanggapan ?? '-' }})</i></small>
                                    @endif
                                @endif
                            </td>
                            <td>{{ $respond->pic->name ?? '-' }}</td>
                            <td>{{ $respond->perusahaan ?? '-' }}</td>
                            <td>{{ $respond->reviewer->name ?? '-' }}</td>
                            <td>{{ $respond->alasan ?? '-' }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </main>
</body>
</html>
