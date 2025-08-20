<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaCoordinatorTracking extends Model
{
    protected $fillable = [
        'master_file_id',
        'date_in_snapshot', 'company_snapshot',
        'title','client_bp','x','material_reminder','material_received',
        'video_done','video_approval','video_approved','video_scheduled',
        'video_posted','post_link'
    ];

    public function masterFile() {
        return $this->belongsTo(MasterFile::class);
    }
}
