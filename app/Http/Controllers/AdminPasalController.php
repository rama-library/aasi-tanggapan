<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Pasal;
use Illuminate\Http\Request;

class AdminPasalController extends Controller
{
    /**
     * Display all pasal for the given document ID.
     */
    public function index($doc_id)
    {
        $document = Document::with('pasal')->findOrFail($doc_id);
        return view('document.show', compact('document'));
    }

    /**
     * Show the form for creating a new pasal.
     */
    public function create($slug)
    {
        $document = Document::where('slug', $slug)->firstOrFail();
        return view('pasal.create', compact('document'));
    }

    /**
     * Store a newly created pasal.
     */
    public function store(Request $request, $slug)
    {
        $document = Document::where('slug', $slug)->firstOrFail();
        $this->validatePasal($request);

        Pasal::create([
            'doc_id' => $document->id,
            'pasal' => $request->pasal,
            'penjelasan' => $request->penjelasan,
        ]);

        return $this->alertRedirect('admin.documents.show', $document->slug, 'Pasal berhasil ditambahkan.', 'Tersimpan');
    }

    /**
     * Display the specified pasal.
     */
    public function show(Document $document, Pasal $pasal)
    {
        $pasal->load(['respond.pic', 'respond.reviewer']);
        return view('pasal.show', compact('document', 'pasal'));
    }

    /**
     * Show the form for editing the specified pasal.
     */
    public function edit(Document $document, Pasal $pasal)
    {
        return view('pasal.edit', compact('document', 'pasal'));
    }

    /**
     * Update the specified pasal.
     */
    public function update(Request $request, Document $document, Pasal $pasal)
    {
        $this->validatePasal($request);

        $pasal->update([
            'pasal' => $request->pasal,
            'penjelasan' => $request->penjelasan,
        ]);

        return $this->alertRedirect('admin.documents.show', $document->slug, 'Pasal berhasil diperbarui.', 'Terupdate');
    }

    /**
     * Remove the specified pasal.
     */
    public function destroy(Pasal $pasal)
    {
        $documentSlug = Document::findOrFail($pasal->doc_id)->slug;
        $pasal->delete();

        return $this->alertRedirect('admin.documents.show', $documentSlug, 'Pasal berhasil dihapus.', 'Terhapus');
    }

    /**
     * Validate pasal input.
     */
    protected function validatePasal(Request $request): void
    {
        $request->validate([
            'pasal' => 'required',
            'penjelasan' => 'required',
        ]);
    }

    /**
     * Redirect with alert flash data.
     */
    protected function alertRedirect(string $route, string $param, string $message, string $title)
    {
        return redirect()->route($route, $param)->with([
            'alert_type' => 'success',
            'alert_title' => $title,
            'alert' => $message,
        ]);
    }
}
