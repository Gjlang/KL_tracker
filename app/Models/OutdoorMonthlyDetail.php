<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutdoorMonthlyDetail extends Model
{
    protected $table = 'outdoor_monthly_details';

    protected $fillable = [
        'master_file_id',
        'outdoor_item_id',
        'year',
        'month',
        'field_key',
        'field_type',   // enum: 'text' | 'date'
        'value_text',
        'value_date',
    ];

    protected $casts = [
        'master_file_id'  => 'integer',
        'outdoor_item_id' => 'integer',
        'year'            => 'integer',
        'month'           => 'integer',
        'value_date'      => 'date',
    ];
}
