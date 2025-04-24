<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Pasal;
use Illuminate\Http\Request;

class AdminPasalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($doc_id)
    {
        $document = Document::with('pasal')->findOrFail($doc_id);
        return view('document.show', compact('document'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($slug)
    {
        // Ambil dokumen berdasarkan slug
        $document = Document::where('slug', $slug)->firstOrFail();
        
        // Tampilkan view create dengan membawa dokumen yang sesuai
        return view('pasal.create', compact('document'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $slug)
    {
        // Cari dokumen berdasarkan slug
        $document = Document::where('slug', $slug)->firstOrFail();

        // Validasi input
        $request->validate([
            'pasal' => 'required',
            'penjelasan' => 'required',
        ]);

        // Menambahkan pasal
        Pasal::create([
            'doc_id' => $document->id,
            'pasal' => $request->pasal,
            'penjelasan' => $request->penjelasan,
        ]);

        // Redirect kembali ke halaman dokumen
        return redirect()->route('documents.show', ['document' => $document->slug])->with([
            'alert_type' => 'success',
            'alert_title' => 'Tersimpan',
            'alert' => 'Pasal berhasil ditambahkan.'
        ]);
    }


    /**
     * Display the specified resource.
     */
    public function show(Document $document, Pasal $pasal)
    {
        $pasal->load(['respond.pic', 'respond.reviewer']); // Load nested relasi
        return view('pasal.show', compact('document', 'pasal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Document $document, Pasal $pasal)
    {
        return view('pasal.edit', compact('document', 'pasal'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Document $document, Pasal $pasal)
    {
        $request->validate([
            'pasal' => 'required',
            'penjelasan' => 'required'
        ]);

        $pasal->update([
            'pasal' => $request->pasal,
            'penjelasan' => $request->penjelasan
        ]);

        $document = Document::findOrFail($pasal->doc_id);
        return redirect()->route('documents.show', $document->slug)->with([
            'alert_type' => 'success',
            'alert_title' => 'Terupdate',
            'alert' => 'Pasal berhasil diperbarui.'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pasal $pasal)
    {
        $document = Document::findOrFail($pasal->doc_id);
        $pasal->delete();

        return redirect()->route('documents.show', $document->slug)->with([
            'alert_type' => 'success',
            'alert_title' => 'Terhapus',
            'alert' => 'Pasal berhasil dihapus.'
        ]);
    }
}
