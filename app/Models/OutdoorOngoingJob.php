<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutdoorOngoingJob extends Model
{
    use HasFactory;

    protected $table = 'outdoor_ongoing_jobs';

    protected $fillable = [
        'master_file_id','year','date','company','product','category','platform','location',
        'start_date','end_date',
        'jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec',
        'status','remarks'
    ];
}
