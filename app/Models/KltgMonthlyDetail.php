<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KltgMonthlyDetail extends Model
{
    protected $table = 'kltg_monthly_details';

    protected $fillable = [
        'master_file_id','year','month','category','field_type',
        'value','value_text','value_date','is_date','type','status'
    ];

    protected $casts = [
        'year'  => 'integer',
        'month' => 'integer',
    ];

    public function masterFile()
    {
        return $this->belongsTo(\App\Models\MasterFile::class, 'master_file_id');
    }
}
