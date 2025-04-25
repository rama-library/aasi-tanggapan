<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\User;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('document.index',[
            // 'literasis' => Literasi::where('user_id', auth()->user()->id)->get(),
            'documents' => Document::all(),
            'author' => User::all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('document.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_document' => 'required',
            'slug' => 'unique:documents',
            'perihal' => 'required',
            'due_date' => 'required|date',
            'due_time' => 'required',
            'review_due_date' => 'required|date',
            'review_due_time' => 'required',
        ]);

        $document = Document::create([
            'user_id' => Auth::user()->id,
            'no_document' => $validated['no_document'],
            'slug' => $validated['slug'],
            'perihal' => $validated['perihal'],
            'due_date' => $validated['due_date'],
            'due_time' => $validated['due_time'],            
            'review_due_date' => $validated['review_due_date'],
            'review_due_time' => $validated['review_due_time'],            
        ]);

        return redirect()->route('documents.index')->with([
            'alert' => 'Dokumen berhasil dibuat!',
            'alert_title' => 'Berhasil',
            'alert_type' => 'success',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Document $document)
    {
        $document->load('pasal'); // pastikan relasi pasal ikut dimuat
        return view('document.show', compact('document'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Document $document)
    {
        return view('document.edit',[
            'document' => $document
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Document $document)
    {
        $rules = [
            'no_document' => 'required',
            'perihal' => 'required',
            'due_date' => 'required|date',
            'due_time' => 'required',      
            'review_due_date' => 'required|date',
            'review_due_time' => 'required',      
        ];

        if($request->slug != $document->slug){
            $rules['slug'] = 'unique:documents';
        }

        $validatedData = $request->validate($rules);

        $validatedData['user_id'] = Auth::user()->id;        

        Document::find($document->id)
                ->update($validatedData);

        return redirect()->route('documents.index')->with([
            'alert' => 'Dokumen berhasil diubah!',
            'alert_title' => 'Berhasil',
            'alert_type' => 'success',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Document $document)
    {
        Document::destroy($document->id);
        return redirect()->route('documents.index')->with([
            'alert' => 'Dokumen berhasil dihapus!',
            'alert_title' => 'Berhasil',
            'alert_type' => 'success',
        ]);
    }

    public function checkSlug(Request $request){
        $slug = SlugService::createSlug(Document::class, 'slug', $request->no_document);
        return response()->json(['slug' => $slug]);
    }
}
