<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class memberModel extends Model
{
    use SoftDeletes;
    protected $table = 'members';
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'first_name',
        'last_name',
        'address',
        'contact_number',
        'date_of_birth',
        'registration_date',
        'purok',
        'age',
        'middle_name',
        'status',
        'occupation'
    ];
    public function beneficiaries()
{
    return $this->hasMany(BeneficiaryModel::class, 'user_id');
}
public function contributions(){
    return $this->hasMany(ContributionModel::class, 'member_id');
}
public function member(){
    return $this->belongsTo(MemberModel::class, 'member_id', 'member_id');
}
}
