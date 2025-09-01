<?php

// app/Models/OutdoorMonthlyDetail.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutdoorMonthlyDetail extends Model
{
    protected $table = 'outdoor_monthly_details';

    protected $fillable = [
        'master_file_id','year','month','field_key','field_type',
        'value_text','value_date',
    ];

    protected $casts = [
        'value_date' => 'date:Y-m-d',
        'year'       => 'integer',
        'month'      => 'integer',
    ];
}
