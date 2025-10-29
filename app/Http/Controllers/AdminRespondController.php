<?php

namespace App\Http\Controllers;

use App\Models\Respond;
use App\Models\PicNoRespond;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AdminRespondController extends Controller
{
    public function today(Document $document)
    {
        $tanggapanHariIni = Respond::with(['document', 'content', 'pic'])
            ->whereDate('created_at', Carbon::today())
            ->whereNotNull('tanggapan')
            ->get();

        return view('respond.today', compact('tanggapanHariIni', 'document'));
    }
    
    public function picNoRespond(Request $request)
    {
        $query = PicNoRespond::with(['pic', 'document']);
    
        if ($request->filled('no_document')) {
            $query->whereHas('document', function ($q) use ($request) {
                $q->where('no_document', 'like', '%'.$request->no_document.'%');
            });
        }
    
        $data = $query->latest()->paginate(15);
    
        return view('respond.picnorespond', compact('data'));
    }
}
