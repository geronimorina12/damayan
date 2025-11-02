<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArchiveContributions extends Model
{
     protected $fillable = [
        'member_id',
        'deceased_id',
        'collector',
        'amount',
        'payment_date',
        'purok',
        'status',
        'updated_by',
    ];
    protected $table = 'archieve_contributions';
}
