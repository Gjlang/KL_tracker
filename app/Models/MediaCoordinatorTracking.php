<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MediaCoordinatorTracking
{
    // Factory method: return the right model instance by section
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

// Base trait for common functionality
trait MediaCoordinatorBase
{
    public $timestamps = true; // Ensure timestamps are enabled

    public function masterFile(): BelongsTo
    {
        return $this->belongsTo(MasterFile::class);
    }

    protected static function boot()
    {
        parent::boot();

        // Ensure master_file_id, year, and month are always set
        static::creating(function ($model) {
            if (empty($model->year)) {
                $model->year = now()->year;
            }
        });
    }
}

class ContentCalendar extends Model {
    use MediaCoordinatorBase;

    protected $table = 'content_calendars';
    protected $fillable = ['master_file_id','year','month','total_artwork','pending','draft_wa','approved'];
    protected $casts = [
        'draft_wa' => 'boolean',
        'approved' => 'boolean',
        'year' => 'integer',
        'month' => 'integer',
        'total_artwork' => 'integer',
        'pending' => 'integer',
        'master_file_id' => 'integer',
    ];
}

class ArtworkEditing extends Model {
    use MediaCoordinatorBase;

    protected $table = 'artwork_editings';
    protected $fillable = ['master_file_id','year','month','total_artwork','pending','draft_wa','approved'];
    protected $casts = [
        'draft_wa' => 'boolean',
        'approved' => 'boolean',
        'year' => 'integer',
        'month' => 'integer',
        'total_artwork' => 'integer',
        'pending' => 'integer',
        'master_file_id' => 'integer',
    ];
}

class PostingScheduling extends Model {
    use MediaCoordinatorBase;

    protected $table = 'posting_schedulings';
    // FIXED: Use 'meta_manager' to match your database column name
    protected $fillable = ['master_file_id','year','month','total_artwork','crm','meta_manager','tiktok_ig_draft'];
    protected $casts = [
        'tiktok_ig_draft' => 'boolean',
        'year' => 'integer',
        'month' => 'integer',
        'total_artwork' => 'integer',
        'crm' => 'integer',
        'meta_manager' => 'integer',
        'master_file_id' => 'integer',
    ];
}

class MediaReport extends Model {
    use MediaCoordinatorBase;

    protected $table = 'media_reports';
    protected $fillable = ['master_file_id','year','month','pending','completed'];
    protected $casts = [
        'completed' => 'boolean',
        'year' => 'integer',
        'month' => 'integer',
        'pending' => 'integer',
        'master_file_id' => 'integer',
    ];
}

class MediaValueAdd extends Model {
    use MediaCoordinatorBase;

    protected $table = 'media_value_adds';
    protected $fillable = ['master_file_id','year','month','quota','completed'];
    protected $casts = [
        'completed' => 'integer', // Can be numeric for valueadd
        'year' => 'integer',
        'month' => 'integer',
        'quota' => 'integer',
        'master_file_id' => 'integer',
    ];
}
