<?php

namespace App\Http\Controllers;

use App\Models\Batangtubuh;
use App\Models\Document;
use App\Models\PicNoRespond;
use App\Models\Respond;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class RespondController extends Controller
{
    public function indexBerlangsung()
    {
        $now = now();
        $documents = Document::where(function ($query) use ($now) {
            $query->where('due_date', '>', $now->toDateString())
                ->orWhere(function ($q) use ($now) {
                    $q->where('due_date', $now->toDateString())
                        ->where('due_time', '>', $now->format('H:i:s'));
                });
        })->latest()->paginate(10);

        return view('respond.index', compact('documents'));
    }

    public function indexFinal(Request $request)
    {
        $search = $request->input('search');
        $now = now();

        $documents = Document::when($search, function ($query) use ($search) {
            $query->where('no_document', 'like', "%{$search}%")
                ->orWhere('perihal', 'like', "%{$search}%");
        })
        ->where(function ($query) use ($now) {
            $query->where('due_date', '<', $now->toDateString())
                ->orWhere(function ($subQuery) use ($now) {
                    $subQuery->where('due_date', $now->toDateString())
                        ->where('due_time', '<', $now->format('H:i:s'));
                });
        })
        ->latest()
        ->paginate(10);

        return view('respond.indexfinal', compact('documents'));
    }

    public function show(Request $request, Document $document)
    {
        return $this->showBatangTubuh($request, $document, 'respond.detail');
    }

    public function showFinal(Request $request, Document $document)
    {
        return $this->showBatangTubuh($request, $document, 'respond.detailfinal');
    }

    public function create(Document $document, Batangtubuh $batangtubuh)
    {
        return view('respond.create', compact('document', 'batangtubuh'));
    }

    public function store(Request $request, Document $document, Batangtubuh $batangtubuh)
    {
        $request->validate([
            'tanggapan' => 'required|string',
        ]);

        Respond::create([
            'doc_id' => $document->id,
            'batangtubuh_id' => $batangtubuh->id,
            'pic_id' => Auth::id(),
            'tanggapan' => $request->tanggapan,
            'perusahaan' => Auth::user()->company_name,
        ]);
        
        return $this->successRedirect($document->slug, 'Tanggapan berhasil ditambahkan.');
    }

    public function edit(Document $document, Batangtubuh $batangtubuh, Respond $respond)
    {
        $user = Auth::user();

        if ($user->hasRole('Reviewer') && $this->isPastReviewDeadline($document)) {
            return $this->errorRedirect('Tanggapan Sudah Melewati Batas Waktu Untuk di Review.');
        }

        if ($user->hasRole('Reviewer') && now()->lessThan($document->due_date . ' ' . $document->due_time)){
            return $this->errorRedirect('Review Belum Diperbolehkan Sebelum Due Date.');
        }

        if ($user->hasRole('PIC')) {
            if ($respond->pic_id !== $user->id) {
                abort(403, 'Anda hanya dapat mengedit tanggapan Anda sendiri.');
            }

            if ($this->isPastDueDate($document)) {
                return $this->errorRedirect('Waktu untuk mengedit tanggapan telah habis.');
            }
        }

        return view('respond.edit', compact('document', 'batangtubuh', 'respond'));
    }


    public function update(Request $request, Document $document, $batangtubuhId, $respondId)
    {
        $request->validate([
            'tanggapan' => 'required|string',
            'alasan' => 'required|string',
        ]);

        $respond = Respond::where([
            'id' => $respondId,
            'doc_id' => $document->id,
            'batangtubuh_id' => $batangtubuhId
        ])->firstOrFail();

        $user = Auth::user();

        if ($user->hasRole('Reviewer')) {
            if ($this->isPastReviewDeadline($document)) {
                return $this->errorRedirect('Waktu review sudah habis.');
            }

            $this->storeOriginalData($respond);
            $respond->fill([
                'tanggapan' => $request->tanggapan,
                'alasan' => $request->alasan,
                'reviewer_id' => $user->id,
            ]);
        } elseif ($user->hasRole('PIC')) {
            if ($respond->pic_id !== $user->id) {
                abort(403);
            }

            if ($this->isPastDueDate($document)) {
                return $this->errorRedirect('Waktu edit tanggapan sudah habis.');
            }

            $this->storeOriginalData($respond);
            $respond->fill([
                'tanggapan' => $request->tanggapan,
                'alasan' => $request->alasan,
            ]);
        } else {
            abort(403);
        }

        $respond->save();

        return $this->successRedirect($document->slug, 'Tanggapan berhasil diperbarui.');
    }

    public function destroy(Request $request, Document $document, $batangtubuhId, $respondId)
    {
        $respond = Respond::findOrFail($respondId);

        if (Auth::user()->hasRole('Reviewer') && now()->lessThan($document->due_date . ' ' . $document->due_time)) {
            return $this->errorRedirect('Hapus Belum Diperbolehkan Sebelum Due Date.');
        }

        $this->storeOriginalData($respond);

        $respond->update([
            'alasan' => $request->alasan,
            'tanggapan' => null,
            'reviewer_id' => Auth::id(),
            'is_deleted' => true,
        ]);

        return $this->successRedirect($document->slug, 'Tanggapan berhasil dihapus.');
    }

    public function noRespond(Document $document)
    {
        $user = auth()->user();
    
        // Cegah duplikat
        $exists = PicNoRespond::where('document_id', $document->id)
                    ->where('pic_id', $user->id)
                    ->exists();
    
        if ($exists) {
            return response()->json([
                'status' => 'warning',
                'message' => 'Anda sudah menandai tidak ada tanggapan untuk dokumen ini.'
            ]);
        }
    
        PicNoRespond::create([
            'document_id' => $document->id,
            'pic_id' => $user->id,
            'perusahaan' => $user->company_name ?? '-',
            'department' => $user->department ?? '-',
        ]);
    
        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil disimpan. Anda telah menandai tidak ada tanggapan.'
        ]);
    }

    /* ========== Helper Functions ========== */

    private function isPastDueDate(Document $document)
    {
        return now()->gt(Carbon::parse($document->due_date . ' ' . $document->due_time));
    }

    private function isPastReviewDeadline(Document $document)
    {
        return now()->gt(Carbon::parse($document->review_due_date . ' ' . $document->review_due_time));
    }

    private function storeOriginalData(Respond $respond)
    {
        $respond->original_data = json_encode([
            'tanggapan' => $respond->tanggapan,
            'alasan' => $respond->alasan,
            'pic_id' => $respond->pic_id,
            'reviewer_id' => $respond->reviewer_id,
            'edited_at' => now()->toDateTimeString(),
        ]);
    }

    private function successRedirect($slug, $message)
    {
        return redirect()->route('tanggapan.berlangsung.detail', $slug)->with([
            'alert_type' => 'success',
            'alert_title' => 'Berhasil',
            'alert' => $message
        ]);
    }

    private function errorRedirect($message)
    {
        return back()->with([
            'alert_type' => 'error',
            'alert_title' => 'Terlambat',
            'alert' => $message
        ]);
    }

    private function showBatangTubuh(Request $request, Document $document, $view)
    {
        $search = $request->input('search');
        $batangtubuhQuery = $document->batangtubuh()->with(['respond.pic', 'respond.reviewer']);

        if ($search) {
            $batangtubuhQuery->where('batang_tubuh', 'like', "%$search%");
        }

        $batangtubuh = $batangtubuhQuery->paginate(10)->withQueryString();

        return view($view, compact('document', 'batangtubuh', 'search'));
    }

}
