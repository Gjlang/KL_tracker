<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterFileTimeline extends Model
{
    use HasFactory;

    protected $fillable = [
        'master_file_id',
        'product', 'site', 'client', 'payment',
        'material_received', 'artwork', 'approval',
        'sent_to_printer', 'installation', 'dismantle',
        'remarks', 'next_follow_up'
    ];

    public function masterFile()
    {
        return $this->belongsTo(MasterFile::class);
    }
}
