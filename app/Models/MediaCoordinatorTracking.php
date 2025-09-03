<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin \Eloquent
 * @method static static firstOrCreate(array $attributes, array $values = [])
 * @method static static firstOrNew(array $attributes, array $values = [])
 */
class MediaCoordinatorTracking extends Model
{
    protected $table = 'media_coordinator_trackings';

    protected $fillable = [
        'master_file_id',
        'year',
        'month',
        'section',   // 'content' | 'editing' | 'schedule' | 'report' | 'valueadd'
        'payload',   // JSON berisi field2 per-section

        // kolom snapshot yang sudah ada di skema kamu (opsional dipakai)
        'date_in_snapshot',
        'company_snapshot',
        'title',
        'client_bp',
        'x',
        'material_reminder',
        'material_received',
        'video_done',
        'video_approval',
        'video_approved',
        'video_scheduled',
        'video_posted',
        'post_link',
    ];

    protected $casts = [
        'payload'      => 'array',
        'video_posted' => 'date',
        'year'         => 'integer',
        'month'        => 'integer',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];
}
