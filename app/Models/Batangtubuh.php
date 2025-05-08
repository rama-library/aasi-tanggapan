<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batangtubuh extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'batang_tubuh';

    public function document()
    {
        return $this->belongsTo(Document::class, 'doc_id');
    }

    public function respond() // <- ubah dari "respond" ke "responds"
    {
        return $this->hasMany(Respond::class);
    }
}
