<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Factory holder to resolve section → concrete model class.
 */
class MediaCoordinatorTracking
{
    /**
     * Return the Eloquent model class for a given media coordinator section.
     */
    public static function forSection(string $section): string
    {
        return match ($section) {
            'content'  => ContentCalendar::class,
            'editing'  => ArtworkEditing::class,
            'schedule' => PostingScheduling::class,
            'report'   => MediaReport::class,
            'valueadd' => MediaValueAdd::class,
            default    => throw new \InvalidArgumentException("Unknown section: $section"),
        };
    }
}

/**
 * Shared behavior for all media coordinator row models.
 */
trait MediaCoordinatorBase
{
    /** Ensure timestamps are on for all models using this trait. */
    public $timestamps = true;

    public function masterFile(): BelongsTo
    {
        return $this->belongsTo(MasterFile::class);
    }

    /**
     * Ensure reasonable defaults (e.g., current year if not provided).
     * Note: placing boot() in a trait is acceptable as long as parent::boot() is called.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function (Model $model) {
            if (empty($model->year)) {
                $model->year = now()->year;
            }
        });
    }
}

/**
 * ===== CONTENT CALENDAR =====
 */
class ContentCalendar extends Model
{
    use MediaCoordinatorBase;

    protected $table = 'content_calendars';

    protected $fillable = [
        'master_file_id', 'year', 'month', 'total_artwork',
        'pending', 'draft_wa', 'approved',
    ];

    protected $casts = [
        'draft_wa'       => 'boolean',
        'approved'       => 'boolean',
        'year'           => 'integer',
        'month'          => 'integer',
        'total_artwork'  => 'integer',
        'pending'        => 'integer',
        'master_file_id' => 'integer',
    ];
}

/**
 * ===== ARTWORK EDITING =====
 */
class ArtworkEditing extends Model
{
    use MediaCoordinatorBase;

    protected $table = 'artwork_editings';

    protected $fillable = [
        'master_file_id', 'year', 'month', 'total_artwork',
        'pending', 'draft_wa', 'approved',
    ];

    protected $casts = [
        'draft_wa'       => 'boolean',
        'approved'       => 'boolean',
        'year'           => 'integer',
        'month'          => 'integer',
        'total_artwork'  => 'integer',
        'pending'        => 'integer',
        'master_file_id' => 'integer',
    ];
}

/**
 * ===== POSTING / SCHEDULING =====
 * IMPORTANT: DB column is meta_manager (NOT meta_mgr).
 * We keep a legacy alias (accessor/mutator) so existing Blade/JS that uses "meta_mgr"
 * continues to work while the DB column remains "meta_manager".
 */
class PostingScheduling extends Model
{
    use MediaCoordinatorBase;

    protected $table = 'posting_schedulings';

    protected $fillable = [
        'master_file_id', 'year', 'month', 'total_artwork',
        'crm', 'meta_manager', 'tiktok_ig_draft',
    ];

    protected $casts = [
        'tiktok_ig_draft' => 'boolean',
        'year'            => 'integer',
        'month'           => 'integer',
        'total_artwork'   => 'integer',
        'crm'             => 'integer',
        'meta_manager'    => 'integer',
        'master_file_id'  => 'integer',
    ];

    // ---- Back-compat alias for legacy "meta_mgr" usage in Blade/JS
    public function getMetaMgrAttribute()
    {
        return $this->attributes['meta_manager'] ?? null;
    }

    public function setMetaMgrAttribute($value): void
    {
        $this->attributes['meta_manager'] = is_null($value) ? null : (int) $value;
    }
}

/**
 * ===== MEDIA REPORT =====
 */
class MediaReport extends Model
{
    use MediaCoordinatorBase;

    protected $table = 'media_reports';

    protected $fillable = [
        'master_file_id', 'year', 'month', 'pending', 'completed',
    ];

    protected $casts = [
        'completed'      => 'boolean',
        'year'           => 'integer',
        'month'          => 'integer',
        'pending'        => 'integer',
        'master_file_id' => 'integer',
    ];
}

/**
 * ===== MEDIA VALUE ADD =====
 * "completed" kept as integer per your requirement (quota/achieved style).
 */
class MediaValueAdd extends Model
{
    use MediaCoordinatorBase;

    protected $table = 'media_value_adds';

    protected $fillable = [
        'master_file_id', 'year', 'month', 'quota', 'completed',
    ];

    protected $casts = [
        'completed'      => 'integer', // numeric progress counter
        'year'           => 'integer',
        'month'          => 'integer',
        'quota'          => 'integer',
        'master_file_id' => 'integer',
    ];
}
