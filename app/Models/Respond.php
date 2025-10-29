<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Respond extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /** Relationships */
    public function content()
    {
        return $this->belongsTo(Content::class, 'content_id');
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

    public function histories()
    {
        return $this->hasMany(RespondHistory::class);
    }

    /** Accessors */
    public function getPicNameAttribute()
    {
        return $this->pic->name ?? '-';
    }

    public function getReviewerNameAttribute()
    {
        return $this->reviewer->name ?? '-';
    }

    public function getPerusahaanAttribute()
    {
        return $this->pic->company_name ?? '-';
    }
}
