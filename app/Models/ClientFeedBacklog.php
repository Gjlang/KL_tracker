<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientFeedBacklog extends Model
{
    protected $fillable = [
        'master_file_id','date','servicing','product','location',
        'client','status','attended_by','reasons',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    // Optional relation
    // public function masterFile() { return $this->belongsTo(MasterFile::class); }
}
