<?php

namespace App\Http\Controllers;

use App\Models\Batangtubuh;
use App\Models\Document;
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

        $documents = Document::where(function ($query) use ($search) {
                if ($search) {
                    $query->where('no_document', 'like', "%{$search}%")
                        ->orWhere('perihal', 'like', "%{$search}%");
                }
            })
            ->where(function ($query) {
                $now = now();
                $query->where('due_date', '<', $now->toDateString())
                    ->orWhere(function ($subQuery) use ($now) {
                        $subQuery->where('due_date', $now->toDateString())
                                ->where('due_time', '<', $now->format('H:i:s'));
                    });
            })
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('respond.indexfinal', compact('documents'));
    }

    public function show(Request $request, Document $document)
    {
        $search = $request->input('search');

        $batangtubuhQuery = $document->batangtubuh()->with(['respond.pic', 'respond.reviewer']);

        if ($search) {
            $batangtubuhQuery->where('batang_tubuh', 'like', '%' . $search . '%');
        }

        $batangtubuh = $batangtubuhQuery->paginate(10)->withQueryString();

        return view('respond.detail', compact('document', 'batangtubuh', 'search'));
    }

    public function showFinal(Request $request, Document $document)
    {
        $search = $request->input('search');

        $batangtubuhQuery = $document->batangtubuh()->with(['respond.pic', 'respond.reviewer']);

        if ($search) {
            $batangtubuhQuery->where('batangtubuh', 'like', '%' . $search . '%');
        }

        $batangtubuh = $batangtubuhQuery->paginate(10)->withQueryString();

        return view('respond.detailfinal', compact('document', 'batangtubuh', 'search'));
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
        
        return redirect()->route('tanggapan.berlangsung.detail', $document->slug)->with([
            'alert_type' => 'success',
            'alert_title' => 'Tersimpan',
            'alert' => 'Tanggapan berhasil ditambahkan.'
        ]);
    }

    public function edit(Document $document, Batangtubuh $batangtubuh, Respond $respond)
    {
        $user = Auth::user();
        $now = now();

        if ($user->hasRole('Reviewer')) {
            $reviewDeadline = Carbon::parse($document->review_due_date . ' ' . $document->review_due_time);

            if ($now->gt($reviewDeadline)) {
                return back()->with([
                    'alert_type' => 'error',
                    'alert_title' => 'Prohibited',
                    'alert' => 'Tanggapan Sudah Melewati Batas Waktu Untuk di Review.'
                ]);
            }
        } elseif ($user->hasRole('PIC')) {
            // Cek apakah user adalah pemilik tanggapan
            if ($respond->pic_id !== $user->id) {
                abort(403, 'Anda hanya dapat mengedit tanggapan Anda sendiri.');
            }

            // Cek batas waktu PIC
            $dueDate = Carbon::parse($document->due_date . ' ' . $document->due_time);
            if ($now->gt($dueDate)) {
                return back()->with([
                    'alert_type' => 'error',
                    'alert_title' => 'Terlambat',
                    'alert' => 'Waktu untuk mengedit tanggapan telah habis.'
                ]);
            }
        } else {
            abort(403);
        }

        return view('respond.edit', compact('document', 'batangtubuh', 'respond'));
    }


    public function update(Request $request, Document $document, $batangtubuhId, $respondId)
    {
        $request->validate([
            'tanggapan' => 'required|string',
            'alasan' => 'required|string',
        ]);

        $respond = Respond::where('id', $respondId)
            ->where('doc_id', $document->id)
            ->where('batangtubuh_id', $batangtubuhId)
            ->firstOrFail();

        $user = Auth::user();
        $now = now();

        if ($user->hasRole('Reviewer')) {
            $reviewDeadline = Carbon::parse($document->review_due_date . ' ' . $document->review_due_time);
            if ($now->gt($reviewDeadline)) {
                return back()->with([
                    'alert_type' => 'error',
                    'alert_title' => 'Terlambat',
                    'alert' => 'Waktu review sudah habis.'
                ]);
            }

            $respond->original_data = json_encode([
                'tanggapan' => $respond->tanggapan,
                'alasan' => $respond->alasan,
                'reviewer_id' => $respond->reviewer_id,
                'edited_at' => now()->toDateTimeString(),
            ]);

            $respond->tanggapan = $request->tanggapan;
            $respond->alasan = $request->alasan;
            $respond->reviewer_id = $user->id;
        } elseif ($user->hasRole('PIC')) {
            // Cek kepemilikan dan batas waktu
            if ($respond->pic_id !== $user->id) {
                abort(403, 'Anda hanya dapat mengedit tanggapan Anda sendiri.');
            }

            $dueDate = Carbon::parse($document->due_date . ' ' . $document->due_time);
            if ($now->gt($dueDate)) {
                return back()->with([
                    'alert_type' => 'error',
                    'alert_title' => 'Terlambat',
                    'alert' => 'Waktu edit tanggapan sudah habis.'
                ]);
            }

            $respond->original_data = json_encode([
                'tanggapan' => $respond->tanggapan,
                'alasan' => $respond->alasan,
                'pic_id' => $respond->pic_id,
                'edited_at' => now()->toDateTimeString(),
            ]);

            $respond->tanggapan = $request->tanggapan;
            $respond->alasan = $request->alasan;
        } else {
            abort(403);
        }

        $respond->save();

        return redirect()->route('tanggapan.berlangsung.detail', $document->slug)->with([
            'alert_type' => 'success',
            'alert_title' => 'Berhasil',
            'alert' => 'Tanggapan berhasil diperbarui.'
        ]);
    }



    public function destroy(Request $request, Document $document, $batangtubuhId, $respondId)
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

        return redirect()->back()->with([
            'alert_type' => 'success',
            'alert_title' => 'Berhasil',
            'alert' => 'Tanggapan berhasil dihapus.'
        ]);
    }

}
