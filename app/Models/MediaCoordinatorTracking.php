<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

// then define 5 small classes here:

class ContentCalendar extends Model {
    protected $table = 'content_calendars';
    protected $fillable = ['master_file_id','year','month','total_artwork','pending','draft_wa','approved'];
    protected $casts = ['draft_wa'=>'bool','approved'=>'bool','year'=>'int','month'=>'int'];
}

class ArtworkEditing extends Model {
    protected $table = 'artwork_editings';
    protected $fillable = ['master_file_id','year','month','total_artwork','pending','draft_wa','approved'];
    protected $casts = ['draft_wa'=>'bool','approved'=>'bool','year'=>'int','month'=>'int'];
}

class PostingScheduling extends Model {
    protected $table = 'posting_schedulings';
    protected $fillable = ['master_file_id','year','month','total_artwork','crm','meta_mgr','tiktok_ig_draft'];
    protected $casts = ['tiktok_ig_draft'=>'bool','year'=>'int','month'=>'int'];
}

class MediaReport extends Model {
    protected $table = 'media_reports';
    protected $fillable = ['master_file_id','year','month','pending','completed'];
    protected $casts = ['completed'=>'bool','year'=>'int','month'=>'int'];
}

class MediaValueAdd extends Model {
    protected $table = 'media_value_adds';
    protected $fillable = ['master_file_id','year','month','quota','completed'];
    protected $casts = ['completed'=>'int','year'=>'int','month'=>'int'];
}
