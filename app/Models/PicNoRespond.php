<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PicNoRespond extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'pic_id',
        'perusahaan',
        'department'
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function pic()
    {
        return $this->belongsTo(User::class, 'pic_id');
    }
}
