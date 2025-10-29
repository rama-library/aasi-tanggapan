<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RespondHistory extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function respond()
    {
        return $this->belongsTo(Respond::class);
    }
}
