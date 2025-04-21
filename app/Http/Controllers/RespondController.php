<?php

namespace App\Http\Controllers;

use App\Models\Pasal;
use App\Models\Respond;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RespondController extends Controller
{
    public function index($pasal_id)
    {
        $pasal = Pasal::with('document')->findOrFail($pasal_id);
        $tanggapan = Respond::where('pasal_id', $pasal_id)->get();

        return view('respond.index', compact('pasal', 'respond'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pasal_id' => 'required|exists:pasals,id',
            'doc_id' => 'required|exists:documents,id',
            'tanggapan' => 'required'
        ]);

        $user = Auth::user();

        // Cegah lebih dari 1 tanggapan oleh PIC
        if ($user->hasRole('PIC')) {
            $existing = Respond::where('pasal_id', $request->pasal_id)
                ->where('pic_id', $user->id)
                ->first();

            if ($existing) {
                return back()->with([
                    'alert_type' => 'error',
                    'alert_title' => 'Duplikat',
                    'alert' => 'Anda sudah memberikan tanggapan untuk pasal ini.'
                ]);
            }
        }

        Respond::create([
            'doc_id'     => $request->doc_id,
            'pasal_id'   => $request->pasal_id,
            'tanggapan'  => $request->tanggapan,
            'perusahaan' => $user->company_name,
            'pic_id'     => $user->hasRole('PIC') ? $user->id : null,
            'reviewer_id'=> $user->hasRole('Reviewer') ? $user->id : null
        ]);

        return back()->with([
            'alert_type' => 'success',
            'alert_title' => 'Tersimpan',
            'alert' => 'Tanggapan berhasil ditambahkan.'
        ]);
    }

    public function update(Request $request, Respond $respond)
    {
        $user = Auth::user();

        if ($user->hasRole('PIC') && $respond->pic_id != $user->id) {
            return abort(403, 'Anda tidak berhak mengedit tanggapan ini.');
        }

        if ($user->hasRole('Reviewer')) {
            $request->validate([
                'tanggapan' => 'nullable',
                'alasan'    => 'required'
            ]);
            $respond->alasan = $request->alasan;
            $respond->reviewer_id = $user->id;
        } else {
            $request->validate([
                'tanggapan' => 'required'
            ]);
        }

        $respond->respond = $request->tanggapan;
        $respond->save();

        return back()->with([
            'alert_type' => 'success',
            'alert_title' => 'Diperbarui',
            'alert' => 'Tanggapan berhasil diperbarui.'
        ]);
    }

    public function destroy(Respond $respond, Request $request)
    {
        $user = Auth::user();

        if ($user->hasRole('Reviewer')) {
            if (!$request->filled('alasan')) {
                return back()->with([
                    'alert_type' => 'error',
                    'alert_title' => 'Gagal',
                    'alert' => 'Alasan penghapusan wajib diisi.'
                ]);
            }

            $respond->alasan = $request->alasan;
            $respond->save();
            $respond->delete();

            return back()->with([
                'alert_type' => 'success',
                'alert_title' => 'Dihapus',
                'alert' => 'Tanggapan berhasil dihapus.'
            ]);
        }

        return abort(403, 'Anda tidak berhak menghapus tanggapan ini.');
    }
}
