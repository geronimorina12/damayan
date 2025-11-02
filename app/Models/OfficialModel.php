<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfficialModel extends Model
{
    use SoftDeletes;
    protected $table = 'officials';
    protected $fillable = [
        'name',
        'position',
        'term_start',
        'term_end',
        'status',
        'email'
    ];
}
