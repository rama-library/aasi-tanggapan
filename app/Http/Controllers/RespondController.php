<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Document;
use App\Models\Respond;
use App\Models\PicNoRespond;
use App\Models\RespondHistory;
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
        return $this->showContent($request, $document, 'respond.detail');
    }

    public function showFinal(Request $request, Document $document)
    {
        return $this->showContent($request, $document, 'respond.detailfinal');
    }

    public function create(Document $document, Content $content)
    {
        return view('respond.create', compact('document', 'content'));
    }

    public function store(Request $request, Document $document, Content $content)
    {
        $request->validate([
            'tanggapan' => 'required|string',
        ]);

        Respond::create([
            'doc_id' => $document->id,
            'content_id' => $content->id,
            'pic_id' => Auth::id(),
            'tanggapan' => $request->tanggapan,
            'perusahaan' => Auth::user()->company_name,
        ]);
        
        return $this->successRedirect($document->slug, 'Tanggapan berhasil ditambahkan.');
    }

    public function edit(Document $document, Content $content, Respond $respond)
    {
        $user = Auth::user();

        if ($user->hasRole('Reviewer') && $this->isPastReviewDeadline($document)) {
            return $this->errorRedirect('Tanggapan Sudah Melewati Batas Waktu Untuk di Review.');
        }

        // if ($user->hasRole('Reviewer') && now()->lessThan($document->due_date . ' ' . $document->due_time)){
        //     return $this->errorRedirect('Review Belum Diperbolehkan Sebelum Due Date.');
        // }

        if ($user->hasRole('PIC')) {
            if ($respond->pic_id !== $user->id) {
                abort(403, 'Anda hanya dapat mengedit tanggapan Anda sendiri.');
            }

            if ($this->isPastDueDate($document)) {
                return $this->errorRedirect('Waktu untuk mengedit tanggapan telah habis.');
            }
        }

        return view('respond.edit', compact('document', 'content', 'respond'));
    }


    public function update(Request $request, Document $document, $contentId, $respondId)
    {
        $request->validate([
            'tanggapan' => 'required|string',
            'alasan' => 'required|string',
        ]);
    
        $respond = Respond::where([
            'id' => $respondId,
            'doc_id' => $document->id,
            'content_id' => $contentId
        ])->firstOrFail();
    
        $user = Auth::user();
    
        $oldTanggapan = $respond->tanggapan; // simpan sebelum diubah
    
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
    
        // Simpan history sebelum model disave
        if ($user->hasRole('Reviewer') && $oldTanggapan !== $request->tanggapan) {
            RespondHistory::create([
                'respond_id' => $respond->id,
                'reviewer_id' => $user->id,
                'old_tanggapan' => $oldTanggapan,
                'new_tanggapan' => $request->tanggapan,
                'alasan' => $request->alasan ?? null,
                'reviewed_at' => now(),
            ]);
        }
    
        $respond->save();
    
        return $this->successRedirect($document->slug, 'Tanggapan berhasil diperbarui.');
    }

    public function destroy(Request $request, Document $document, $contentId, $respondId)
    {
        $respond = Respond::findOrFail($respondId);
        $user = Auth::user();
    
        // Jika yang menghapus adalah Reviewer
        if ($user->hasRole('Reviewer')) {
            // Simpan data lama sebelum dihapus
            $this->storeOriginalData($respond);
    
            // Simpan history penghapusan oleh reviewer
            RespondHistory::create([
                'respond_id'     => $respond->id,
                'reviewer_id'    => $user->id,
                'old_tanggapan'  => $respond->tanggapan,
                'new_tanggapan'  => 'DIHAPUS',
                'alasan'         => $request->alasan ?? 'Dihapus oleh reviewer',
                'reviewed_at'    => now(),
            ]);
    
            // Update data tanggapan utama
            $respond->update([
                'alasan'       => $request->alasan,
                'tanggapan'    => null,
                'reviewer_id'  => $user->id,
                'is_deleted'   => true,
            ]);
    
            return $this->successRedirect($document->slug, 'Tanggapan berhasil dihapus oleh reviewer dan dicatat di history.');
        }
    
        // Jika yang menghapus adalah PIC
        if ($user->hasRole('PIC')) {
            // Pastikan hanya bisa hapus tanggapan miliknya sendiri
            if ($respond->pic_id !== $user->id) {
                abort(403, 'Anda tidak memiliki izin untuk menghapus tanggapan ini.');
            }
        
            // Hapus record tanggapan sepenuhnya agar dianggap "belum ada tanggapan"
            $respond->delete();
        
            return $this->successRedirect($document->slug, 'Tanggapan Anda berhasil dihapus. Anda dapat memberikan tanggapan kembali.');
        }
        // Jika bukan Reviewer atau PIC
        abort(403, 'Anda tidak memiliki izin untuk melakukan tindakan ini.');
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

    private function isPastDueDate(Document $document): bool
    {
        return now()->gt(Carbon::parse($document->due_date . ' ' . $document->due_time));
    }

    private function isPastReviewDeadline(Document $document): bool
    {
        // Gabungkan tanggal & waktu dari database (hasil input form)
        $deadline = Carbon::parse(
            $document->review_due_date . ' ' . $document->review_due_time,
            'Asia/Jakarta' // pastikan interpretasi sesuai waktu lokal
        );
    
        // Bandingkan dengan waktu saat ini di Asia/Jakarta
        return now('Asia/Jakarta')->greaterThan($deadline);
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
        return redirect()->route('berikan.tanggapan.detail', $slug)->with([
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

    private function showContent(Request $request, Document $document, $view)
    {
        $search = $request->input('search');

        // Eager load untuk hindari N+1
        $contentQuery = $document->contents()
            ->with([
                'respond.pic',
                'respond.reviewer',
                'respond.histories.reviewer',
            ]);

        if ($search) {
            $contentQuery->where('contents', 'like', "%{$search}%");
        }

        $content = $contentQuery
            ->orderBy('created_at', 'ASC')
            ->paginate(10)
            ->withQueryString();

        // Siapkan variabel view (hindari query di Blade)
        $userId = Auth::id();
        $isPIC = Auth::user()->hasRole('PIC');
        $isReviewer = Auth::user()->hasRole('Reviewer');

        $sudahNoRespond = $document->userHasNoRespond($userId);
        $sudahPernahTanggapan = $document->userHasResponded($userId);

        $canRespond = $document->canRespond();
        $canReview = $document->canReview();

        return view($view, compact(
            'document',
            'content',
            'search',
            'isPIC',
            'isReviewer',
            'sudahNoRespond',
            'sudahPernahTanggapan',
            'canRespond',
            'canReview'
        ));
    }
}
