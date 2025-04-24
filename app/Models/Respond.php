<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Respond extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function pasal()
    {
        return $this->belongsTo(Pasal::class);
    }

    public function document()
    {
        return $this->belongsTo(Document::class, 'doc_id');
    }

    public function pic()
    {
        return $this->belongsTo(User::class, 'pic_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function getReviewerNameAttribute()
    {
        return $this->reviewer ? $this->reviewer->name : '-';
    }

    public function getPicNameAttribute()
    {
        return $this->pic ? $this->pic->name : '-';
    }

    // Ambil nama perusahaan yang terkait dengan PIC
    public function perusahaan()
    {
        return $this->pic->perusahaan; // Anggap perusahaan ada di kolom 'perusahaan' milik User
    }
}
