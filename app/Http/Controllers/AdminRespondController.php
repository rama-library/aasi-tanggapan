<?php

namespace App\Http\Controllers;

use App\Models\PicNoRespond;
use App\Models\Respond;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AdminRespondController extends Controller
{
    public function today()
    {
        $tanggapanHariIni = Respond::with(['document', 'batangtubuh', 'pic'])
            ->whereDate('created_at', Carbon::today())
            ->whereNotNull('tanggapan')
            ->get();

        return view('respond.today', compact('tanggapanHariIni'));
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
