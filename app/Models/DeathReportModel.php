<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeathReportModel extends Model
{
    protected $table = 'death_reports';
    protected $fillable = [
        'reported_by',
        'member_id',
        'deceased_name',
        'date_of_death',
        'report_date',
    ];
}
