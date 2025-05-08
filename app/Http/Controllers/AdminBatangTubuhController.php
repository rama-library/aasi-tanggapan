<?php

namespace App\Http\Controllers;

use App\Models\Batangtubuh;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminBatangTubuhController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($doc_id)
    {
        $document = Document::with('batangtubuh')->findOrFail($doc_id);
        return view('document.show', compact('document'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($slug)
    {
        $document = Document::where('slug', $slug)->firstOrFail();
        return view('batangtubuh.create', compact('document'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $slug)
    {
        $document = Document::where('slug', $slug)->firstOrFail();
        $this->validateBatangTubuh($request);

        $gambarPath = null;
        if ($request->hasFile('gambar')) {
            $gambarPath = $request->file('gambar')->store('gambar_penjelasan', 'public');
        }


        Batangtubuh::create([
            'doc_id' => $document->id,
            'batang_tubuh' => $request->batang_tubuh,
            'penjelasan' => $request->penjelasan,
            'gambar' => $gambarPath,
        ]);

        return $this->alertRedirect('admin.documents.show', $document->slug, 'Batang tubuh berhasil ditambahkan.', 'Tersimpan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Document $document, Batangtubuh $batangtubuh)
    {
        $batangtubuh->load(['respond.pic', 'respond.reviewer']);
        return view('batangtubuh.show', compact('document', 'batangtubuh'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Document $document, Batangtubuh $batangtubuh)
    {
        return view('batangtubuh.edit', compact('document', 'batangtubuh'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Document $document, Batangtubuh $batangtubuh)
    {
        $this->validateBatangTubuh($request);

        $gambarPath = $batangtubuh->gambar;
        if ($request->hasFile('gambar')) {
            $gambarPath = $request->file('gambar')->store('gambar_penjelasan', 'public');
        }

        $batangtubuh->update([
            'batang_tubuh' => $request->batang_tubuh,
            'penjelasan' => $request->penjelasan,
            'gambar' => $gambarPath,
        ]);

        return $this->alertRedirect('admin.documents.show', $document->slug, 'Batang Tubuh berhasil diperbarui.', 'Terupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Batangtubuh $batangtubuh)
    {   
        if($batangtubuh->gambar){
            Storage::delete($batangtubuh->gambar);
        }
        $documentSlug = Document::findOrFail($batangtubuh->doc_id)->slug;
        $batangtubuh->delete();

        return $this->alertRedirect('admin.documents.show', $documentSlug, 'Batang Tubuh berhasil dihapus.', 'Terhapus');
    }

    protected function validateBatangTubuh(Request $request): void
    {
        $request->validate([
            'batang_tubuh' => 'required',
            'penjelasan' => 'nullable|required_without:gambar',
            'gambar' => 'nullable|image|max:2048|required_without:penjelasan',
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
