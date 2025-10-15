<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContributionModel extends Model
{
    protected $table = 'contributions';
    protected $fillable = [
        'member_id',
        'amount',
        'payment_date',
        'updated_by',
        'collector',
        'purok',
        'status',
        'deceased_id',
    ];

    public function memberContribution(){
        return $this->belongsTo(memberModel::class, 'member_id');
    }
}
