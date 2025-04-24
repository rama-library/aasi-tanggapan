<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Document extends Model
{
    use HasFactory, Sluggable;

    protected $guarded = ['id'];

    protected $table = 'documents';

    protected $with = 'author';

    public function pasal()
    {
        return $this->hasMany(Pasal::class, 'doc_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'no_document'
            ]
        ];
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getDueDateFormattedAttribute()
    {
        return $this->due_date ? Carbon::parse($this->due_date)->format('d M Y') : '-';
    }
}
