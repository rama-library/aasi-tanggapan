<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Laporan Dokumen</title>
        <style>
            @page {
                margin: 180px 30px 100px 30px; /* Top margin tinggi supaya header tidak nabrak */
            }
        
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 0;
            }
        
            header {
                position: fixed;
                top: -150px;
                left: 0;
                right: 0;
                height: 120px;
                padding: 10px 20px;
                border-bottom: 2px solid #000;
            }

            main {
                margin-top: 20px; /* Tambahkan padding supaya tabel turun */
            }
        
            footer {
                position: fixed;
                bottom: -100px;
                left: 0;
                right: 0;
                height: 50px;
                text-align: center;
                font-size: 12px;
                color: #555;
                border-top: 1px solid #aaa;
                padding-top: 10px;
            }

            footer .page-number:after {
                content: counter(page) ;
            }
        
            table {
                width: 100%;
                border-collapse: collapse;
                page-break-inside: auto;
            }
        
            table th, table td {
                border: 1px solid #000;
                padding: 8px;
                text-align: center;
            }
        
            table th {
                background-color: #f2f2f2;
            }
        
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
        </style>        
    </head>
    <body>
        <header>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div style="text-align: left;">
                    <img src="{{ public_path('adminpage/assets/img/aasi.png') }}" alt="Logo" style="max-width: 140px;">
                </div>
                <div style="flex-grow: 1; text-align: center;">
                    <h2 style="margin: 0;">Laporan Dokumen {{ ucfirst(str_replace('_', ' ', $jenis)) }}</h2>
                    <small>No. Dokumen: {{ $document->no_document }}</small><br>
                    <small>Perihal: {{ $document->perihal }}</small>
                </div>
            </div>
        </header>
    
        <footer>
            <div class="page-number"></div>
            <div>Asosiasi Asuransi Syariah Indonesia - Jalan Jatinegara Timur II No. 4, Rawa Bunga, Jakarta </div>
        </footer>
    
        <main>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Pasal</th>
                        <th>Penjelasan</th>
                        <th>Tanggapan</th>
                        <th>PIC</th>
                        <th>Perusahaan</th>
                        <th>Reviewer</th>
                        <th>Alasan</th>
                    </tr>
                </thead>
                @php
                    $grouped = $result->groupBy(function ($item) {
                        return $item->pasal->pasal . '|' . $item->pasal->penjelasan;
                    });
                @endphp
    
                @foreach ($grouped as $key => $group)
                    @foreach ($group as $i => $respond)
                        <tr>
                            @if ($i === 0)
                                <td rowspan="{{ $group->count() }}">{{ $loop->parent->iteration }}</td>
                                <td rowspan="{{ $group->count() }}">{{ $respond->pasal->pasal }}</td>
                                <td rowspan="{{ $group->count() }}">{{ $respond->pasal->penjelasan }}</td>
                            @endif
                            <td>
                                @if ($respond->is_deleted)
                                    <del class="muted">{{ json_decode($respond->original_data)->tanggapan ?? '-' }}</del>
                                    <br>(Dihapus oleh reviewer)
                                @else
                                    {{ $respond->tanggapan ?? '-' }}
                                    @if ($jenis === 'full' && $respond->original_data)
                                        <br>
                                        <small class="muted">
                                            <i>(Sebelum revisi: {{ json_decode($respond->original_data)->tanggapan ?? '-' }})</i>
                                        </small>
                                    @endif
                                @endif
                            </td>
                            <td>{{ $respond->pic->name ?? '-' }}</td>
                            <td>{{ $respond->perusahaan ?? '-' }}</td>
                            <td>{{ $respond->reviewer->name ?? '-' }}</td>
                            <td>
                                @if ($respond->alasan)
                                    <span class="text-danger">{{ $respond->alasan }}</span>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            </table>
        </main>
    </body>
    
</html>