<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeathReportModel extends Model
{
    protected $table = 'death_reports';
      protected $primaryKey = 'report_id';
    protected $fillable = [
        'reported_by',
        'member_id',
        'deceased_name',
        'date_of_death',
        'report_date',
    ];
    public function member(){
      return $this->belongsTo(memberModel::class,'member_id', 'id');
    }
}
