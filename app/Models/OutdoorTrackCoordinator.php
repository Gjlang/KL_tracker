<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutdoorTrackCoordinator extends Model
{
    protected $table = 'outdoor_track_coordinator';

    protected $fillable = [
        'master_file_id','company_snapshot','product_snapshot',
        'site','payment','material','artwork','approval','sent',
        'collected','install','dismantle','status',
    ];

    public function masterFile()
    {
        return $this->belongsTo(MasterFile::class);
    }
}
