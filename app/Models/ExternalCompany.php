<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExternalCompany extends Model
{
    protected $connection = 'external';
    protected $table = 'member_companies';

    public $timestamps = false;
    protected $fillable = ['namapt'];
}
