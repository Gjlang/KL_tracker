<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static static firstOrCreate(array $attributes, array $values = [])
 * @method static static firstOrNew(array $attributes, array $values = [])
 * @property int $id
 * @property int $master_file_id
 * @property int $year
 * @property int $month
 * @property string $section
 * @property array<array-key, mixed>|null $payload
 * @property string|null $date_in_snapshot
 * @property string|null $company_snapshot
 * @property string|null $title
 * @property string|null $client_bp
 * @property string|null $x
 * @property string|null $material_reminder
 * @property string|null $material_received
 * @property string|null $video_done
 * @property string|null $video_approval
 * @property string|null $video_approved
 * @property string|null $video_scheduled
 * @property \Illuminate\Support\Carbon|null $video_posted
 * @property string|null $post_link
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaCoordinatorTracking newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaCoordinatorTracking newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaCoordinatorTracking query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaCoordinatorTracking whereClientBp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaCoordinatorTracking whereCompanySnapshot($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaCoordinatorTracking whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaCoordinatorTracking whereDateInSnapshot($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaCoordinatorTracking whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaCoordinatorTracking whereMasterFileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaCoordinatorTracking whereMaterialReceived($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaCoordinatorTracking whereMaterialReminder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaCoordinatorTracking whereMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaCoordinatorTracking wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaCoordinatorTracking wherePostLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaCoordinatorTracking whereSection($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaCoordinatorTracking whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaCoordinatorTracking whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaCoordinatorTracking whereVideoApproval($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaCoordinatorTracking whereVideoApproved($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaCoordinatorTracking whereVideoDone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaCoordinatorTracking whereVideoPosted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaCoordinatorTracking whereVideoScheduled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaCoordinatorTracking whereX($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaCoordinatorTracking whereYear($value)
 * @mixin \Eloquent
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
