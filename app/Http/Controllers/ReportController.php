<?php

namespace App\Http\Controllers;

use App\Exports\LaporanExport;
use App\Models\Batangtubuh;
use App\Models\Document;
use App\Models\Respond;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{

    public function index(Request $request)
    {
        $documents = Document::orderBy('created_at', 'desc')->get();
        $selectedDocument = null;
        $jenis = null;
        $result = collect();

        if ($request->has(['document', 'type'])) {
            $selectedDocument = Document::find($request->document);
            $jenis = $request->type;

            if ($selectedDocument) {
                $query = Batangtubuh::with(['respond' => function ($q) use ($jenis) {
                        if ($jenis === 'final') {
                            $q->where('is_deleted', false);
                        }
                        $q->with(['pic', 'reviewer']);
                    }])
                    ->where('doc_id', $selectedDocument->id);

                if ($request->filled('search')) {
                    $search = $request->search;
                    $query->where(function ($q) use ($search) {
                        $q->where('batang_tubuh', 'like', "%$search%")
                        ->orWhere('penjelasan', 'like', "%$search%");
                    });
                }

                $result = $query->orderBy('id')->paginate(10)->withQueryString();
            }
        }

        return view('report.index', compact('documents', 'selectedDocument', 'jenis', 'result'));
    }

    public function export(Request $request)
    {
        $document = Document::findOrFail($request->document);
        $jenis = $request->type;
    
        // Ambil semua tanggapan
        $responds = Respond::with(['content', 'pic', 'reviewer', 'document'])
            ->where('doc_id', $document->id)
            ->when($jenis === 'final', fn($q) => $q->where('is_deleted', false))
            ->get();
    
        // Ambil semua content
        $allContent = $document->contents()->get();
    
        // Gabungkan: pastikan setiap content tetap muncul meskipun tidak ada tanggapan
        $merged = collect();
        foreach ($allContent as $content) {
            $related = $responds->where('content_id', $content->id);
            if ($related->isEmpty()) {
                $merged->push((object)[
                    'content' => $content,
                    'document' => $document,
                    'tanggapan' => null,
                    'pic' => null,
                    'reviewer' => null,
                    'perusahaan' => null,
                    'alasan' => null,
                    'is_deleted' => false,
                    'original_data' => null,
                ]);
            } else {
                foreach ($related as $r) {
                    $merged->push($r);
                }
            }
        }
    
        if ($request->format === 'excel') {
            return Excel::download(new LaporanExport($merged, $document, $jenis), 'laporan_' . $document->slug . '.xlsx');
        }
    
        if ($request->format === 'pdf') {
            $pdf = \Barryvdh\Snappy\Facades\SnappyPdf::loadView('report.export_pdf', [
            'result' => $merged,
            'document' => $document,
            'jenis' => $jenis
        ])
        ->setPaper('A4', 'landscape')
        ->setOption('encoding', 'UTF-8')
        ->setOption('margin-top', 30) // cukup kecil karena header sudah fixed di CSS
        ->setOption('margin-bottom', 25)
        ->setOption('footer-center', '')
        ->setOption('disable-smart-shrinking', false)
        ->setOption('print-media-type', true);
        
        return $pdf->stream('laporan_' . $document->slug . '.pdf');
        }
    }
}
