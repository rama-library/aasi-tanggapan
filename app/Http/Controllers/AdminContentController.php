<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminContentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($doc_id)
    {
        $document = Document::with('contents')->findOrFail($doc_id);
        return view('document.show', compact('document'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($slug)
    {
        $document = Document::where('slug', $slug)->firstOrFail();
        return view('content.create', compact('document'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $slug)
    {
        $document = Document::where('slug', $slug)->firstOrFail();
        $this->validateContent($request);

        $gambarPath = null;
        if ($request->hasFile('gambar')) {
            $gambarPath = $request->file('gambar')->store('gambar_penjelasan', 'public');
        }


        Content::create([
            'doc_id' => $document->id,
            'contents' => $request->contents,
            'detil' => $request->detil,
            'gambar' => $gambarPath,
        ]);

        return $this->alertRedirect('admin.documents.show', $document->slug, 'Konten berhasil ditambahkan.', 'Tersimpan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Document $document, Content $content)
    {
        $content->load(['respond.pic', 'respond.reviewer', 'respond.histories.reviewer']);
        return view('content.show', compact('document', 'content'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Document $document, Content $content)
    {
        return view('content.edit', compact('document', 'content'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Document $document, Content $content)
    {
        $this->validateContent($request);

        $gambarPath = $content->gambar;
        if ($request->hasFile('gambar')) {
            $gambarPath = $request->file('gambar')->store('gambar_penjelasan', 'public');
        }

        $content->update([
            'contents' => $request->contents,
            'detil' => $request->detil,
            'gambar' => $gambarPath,
        ]);

        return $this->alertRedirect('admin.documents.show', $document->slug, 'Konten berhasil diperbarui.', 'Terupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Content $content)
    {   
        if($content->gambar){
            Storage::delete($content->gambar);
        }
        $documentSlug = Document::findOrFail($content->doc_id)->slug;
        $content->delete();

        return $this->alertRedirect('admin.documents.show', $documentSlug, 'Konten berhasil dihapus.', 'Terhapus');
    }
    
    protected function validateContent(Request $request): void
    {
        $request->validate([
            'contents' => 'required',
            'detil' => 'nullable',
            'gambar' => 'nullable|image|max:2048',
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
