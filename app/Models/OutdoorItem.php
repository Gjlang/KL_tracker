<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutdoorItem extends Model
{
    protected $fillable = [
        'master_file_id',
        'sub_product','qty','site','size',
        'district_council','coordinates','remarks',
        'start_date','end_date',
        'billboard_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function billboard()
    {
        return $this->belongsTo(Billboard::class, 'billboard_id');
    }
}
