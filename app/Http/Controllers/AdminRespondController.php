<?php

namespace App\Http\Controllers;

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
}
