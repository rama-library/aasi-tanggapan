<?php

namespace App\Http\Controllers;

use App\Models\DocumentType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminDocumentTypeController extends Controller
{
    public function index()
    {
        $types = DocumentType::orderBy('name')->paginate(20);
        return view('document_types.index', compact('types'));
    }

    public function create()
    {
        return view('document_types.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:document_types,name',
        ]);

        $data['slug'] = Str::slug($data['name']);
        DocumentType::create($data);

        return $this->redirectWithSuccess('Jenis Dokumen berhasil ditambahkan!');
    }

    public function edit(DocumentType $documentType)
    {
        return view('document_types.edit', compact('documentType'));
    }

    public function update(Request $request, DocumentType $documentType)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:document_types,name,' . $documentType->id,
        ]);
        $data['slug'] = Str::slug($data['name']);
        $documentType->update($data);

        return $this->redirectWithSuccess('Jenis Dokumen berhasil diubah!');
    }

    public function destroy(DocumentType $documentType)
    {
        // safe delete: jika ada docs, set null atau block berdasarkan policy
        $documentType->delete();
        return $this->redirectWithSuccess('Jenis Dokumen berhasil dihapus!');
    }
    
    
    private function redirectWithSuccess(string $message)
    {
        return redirect()->route('admin.document-types.index')->with([
            'alert' => $message,
            'alert_title' => 'Berhasil',
            'alert_type' => 'success',
        ]);
    }
}
