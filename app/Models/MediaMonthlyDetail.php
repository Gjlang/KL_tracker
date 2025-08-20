<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaMonthlyDetail extends Model
{
    protected $fillable = [
        'master_file_id','year','month','value_text','value_date'
    ];
}
