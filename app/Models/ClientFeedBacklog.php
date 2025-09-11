<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientFeedBacklog extends Model
{
    protected $fillable = [
        'master_file_id','date','servicing','product','location',
        'client','status','attended_by','reasons','expected_finish_date','company',
    ];

    protected $casts = [
        'date' => 'date',
        'expected_finish_date' => 'date',
    ];

    // Optional relation
    // public function masterFile() { return $this->belongsTo(MasterFile::class); }
}
