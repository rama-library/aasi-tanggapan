<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class Document extends Model
{
    use HasFactory, Sluggable;

    protected $guarded = ['id'];
    protected $table = 'documents';
    protected $dates = ['due_date', 'review_due_date'];
    protected $with = ['author'];

    /** Relationships */
    public function contents()
    {
        return $this->hasMany(Content::class, 'doc_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class, 'document_type_id');
    }

    /** Accessors */
    public function getFormattedCreatedAtAttribute()
    {
        return Carbon::parse($this->created_at)->isoFormat('D MMMM Y');
    }

    public function getFormattedDueDateAttribute()
    {
        return $this->due_date ? Carbon::parse($this->due_date)->isoFormat('D MMMM Y') : '-';
    }

    public function getFormattedReviewDueDateAttribute()
    {
        return $this->review_due_date ? Carbon::parse($this->review_due_date)->isoFormat('D MMMM Y') : null;
    }

    /** Logic Helpers */
    public function userHasNoRespond($userId)
    {
        return \App\Models\PicNoRespond::where('document_id', $this->id)
            ->where('pic_id', $userId)
            ->exists();
    }

    public function userHasResponded($userId)
    {
        return \App\Models\Respond::whereHas('content', fn($q) => $q->where('doc_id', $this->id))
            ->where('pic_id', $userId)
            ->exists();
    }

    public function canRespond()
    {
        return !$this->due_date || !$this->due_time
            ? true
            : now('Asia/Jakarta')->lte(Carbon::parse("{$this->due_date} {$this->due_time}", 'Asia/Jakarta'));
    }

    public function canReview()
    {
        if (!$this->review_due_date || !$this->review_due_time) {
            return true;
        }
    
        // Pastikan kedua waktu dibandingkan dalam Asia/Jakarta
        $deadline = Carbon::parse("{$this->review_due_date} {$this->review_due_time}", 'Asia/Jakarta');
        $now = now('Asia/Jakarta');
    
        return $now->lessThanOrEqualTo($deadline);
    }

    public function sluggable(): array
    {
        return ['slug' => ['source' => 'no_document']];
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
