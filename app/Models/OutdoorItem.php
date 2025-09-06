<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutdoorItem extends Model
{
    protected $fillable = [
        'master_file_id','sub_product','qty','site','size',
        'district_council','coordinates','remarks'
    ];

    public function masterFile(){
        return $this->belongsTo(MasterFile::class);
    }
}
