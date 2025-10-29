<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    protected $fillable = ['name', 'slug', 'note'];

    public function documents()
    {
        return $this->hasMany(Document::class, 'document_type_id');
    }
    
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
