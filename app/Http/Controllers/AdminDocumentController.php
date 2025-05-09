<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Cviebrock\EloquentSluggable\Services\SlugService;

class AdminDocumentController extends Controller
{
    public function index()
    {
        return view('document.index', [
            'documents' => Document::with('author')->get(),
        ]);
    }

    public function create()
    {
        return view('document.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());

        $validated['user_id'] = Auth::id();

        Document::create($validated);

        return $this->redirectWithSuccess('Dokumen berhasil dibuat!');
    }

    public function show(Document $document)
    {
        $document->load('batangtubuh');

        return view('document.show', compact('document'));
    }

    public function edit(Document $document)
    {
        $document->load('author');
        return view('document.edit', compact('document'));
    }

    public function update(Request $request, Document $document)
    {
        $rules = $this->rules($document);
        $validated = $request->validate($rules);
        $validated['user_id'] = Auth::id();

        $document->update($validated);

        return $this->redirectWithSuccess('Dokumen berhasil diubah!');
    }

    public function destroy(Document $document)
    {
        $document->delete();

        return $this->redirectWithSuccess('Dokumen berhasil dihapus!');
    }

    public function checkSlug(Request $request)
    {
        $slug = SlugService::createSlug(Document::class, 'slug', $request->no_document);
        return response()->json(['slug' => $slug]);
    }

    /**
     * Validation rules for store and update.
     */
    private function rules(?Document $document = null): array
    {
        $uniqueSlug = 'unique:documents';

        if ($document && request()->slug === $document->slug) {
            $uniqueSlug = 'sometimes';
        }

        return [
            'no_document' => 'required',
            'slug' => $uniqueSlug,
            'perihal' => 'required',
            'due_date' => 'required|date',
            'due_time' => 'required',
            'review_due_date' => 'required|date',
            'review_due_time' => 'required',
        ];
    }

    /**
     * Redirect helper with SweetAlert-style flash message.
     */
    private function redirectWithSuccess(string $message)
    {
        return redirect()->route('admin.documents.index')->with([
            'alert' => $message,
            'alert_title' => 'Berhasil',
            'alert_type' => 'success',
        ]);
    }
}
