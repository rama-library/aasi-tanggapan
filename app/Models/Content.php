<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory;

    protected $table = 'contents';
    protected $guarded = ['id'];

    /** Relationships */
    public function document()
    {
        return $this->belongsTo(Document::class, 'doc_id');
    }

    public function respond()
    {
        return $this->hasMany(Respond::class, 'content_id');
    }

    /** Accessor Helpers */
    public function getHasImageAttribute()
    {
        return !empty($this->gambar);
    }

    public function getImageUrlAttribute()
    {
        return $this->gambar ? asset("storage/{$this->gambar}") : null;
    }
}
