<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Pasal;
use App\Models\Respond;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RespondController extends Controller
{
    public function indexBerlangsung()
    {
        $documents = Document::with('pasal.respond')->orderByDesc('created_at')->get();
        return view('respond.index', compact('documents'));
    }

    public function show(Request $request, Document $document)
    {
        $search = $request->input('search');

        $pasalQuery = $document->pasal()->with(['respond.pic', 'respond.reviewer']);

        if ($search) {
            $pasalQuery->where('pasal', 'like', '%' . $search . '%');
        }

        $pasal = $pasalQuery->paginate(10)->withQueryString();

        return view('respond.detail', compact('document', 'pasal', 'search'));
    }

    public function create(Document $document, Pasal $pasal)
    {
        return view('respond.create', compact('document', 'pasal'));
    }

    public function store(Request $request, Document $document, Pasal $pasal)
    {
        $request->validate([
            'tanggapan' => 'required|string',
        ]);

        Respond::create([
            'doc_id' => $document->id,
            'pasal_id' => $pasal->id,
            'pic_id' => Auth::id(),
            'tanggapan' => $request->tanggapan,
            'perusahaan' => Auth::user()->company_name,
        ]);
        
        return redirect()->route('tanggapan.detail', $document->slug)->with([
            'alert_type' => 'success',
            'alert_title' => 'Tersimpan',
            'alert' => 'Tanggapan berhasil ditambahkan.'
        ]);
    }

    public function edit(Document $document, Pasal $pasal, Respond $respond)
    {
        // $respond = $pasal->respond()->first();
        
        return view('respond.edit', compact('document', 'pasal', 'respond'));
    }

    public function update(Request $request, Document $document, $pasalId, $respondId)
    {
        // Validasi input
        $request->validate([
            'tanggapan' => 'required|string',
            'alasan' => 'required|string',
        ]);

        // Ambil data tanggapan
        $respond = Respond::where('id', $respondId)
            ->where('doc_id', $document->id)
            ->where('pasal_id', $pasalId)
            ->firstOrFail();

        // Cek apakah user adalah reviewer
        if (!auth()->user()->hasRole('Reviewer')) {
            abort(403, 'Hanya Reviewer yang dapat mengedit tanggapan.');
        }

        // Simpan data original sebelum diedit
        $respond->original_data = json_encode([
            'tanggapan' => $respond->tanggapan,
            'alasan' => $respond->alasan,
            'reviewer_id' => $respond->reviewer_id,
            'edited_at' => now()->toDateTimeString(),
        ]);

        // Update tanggapan
        $respond->tanggapan = $request->tanggapan;
        $respond->alasan = $request->alasan;
        $respond->reviewer_id = auth()->id();
        $respond->save();

        return redirect()->route('tanggapan.detail', $document->slug)->with([
            'alert_type' => 'success',
            'alert_title' => 'Berhasil',
            'alert' => 'Tanggapan berhasil direview.'
        ]);
    }


    public function destroy(Request $request, Document $document, $pasalId, $respondId)
    {
        $respond = Respond::findOrFail($respondId);

        // Simpan data sebelum dihapus ke original_data
        $respond->update([
            'alasan' => $request->alasan,
            'original_data' => json_encode([
                'tanggapan' => $respond->tanggapan,
                'pic_id' => $respond->pic_id,
                'perusahaan' => $respond->perusahaan,
                'reviewer_id' => $respond->reviewer_id,
            ]),
            'tanggapan' => null, // Kosongkan isinya karena dihapus
            'reviewer_id' => Auth::id(),
            'is_deleted' => true,
        ]);

        return redirect()->back()->with('success', 'Tanggapan berhasil dihapus dengan alasan.');
    }

}
