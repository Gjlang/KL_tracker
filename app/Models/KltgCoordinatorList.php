<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KltgCoordinatorList extends Model
{
    // Fix: Specify the correct table name
    protected $table = 'kltg_coordinator_lists';

     protected $fillable = [
         'master_file_id','subcategory',
         'title_snapshot','company_snapshot','client_bp','x','edition','publication','remarks',
         'artwork_bp_client','material_record','send_chop_sign','chop_sign_approval','park_in_file_server',
         'material_reminder_text','post_link',
         'artwork_reminder',
         'collection_printer','sent_to_client','approved_client','sent_to_printer','printed','delivered',
         'video_done','pending_approval','video_approved','video_scheduled','video_posted',
         'article_done','article_approved','article_scheduled','article_posted',
         'em_date_write','em_date_to_post','em_post_date','em_qty','blog_link',
     ];

     protected $casts = [
         'x' => 'string',
         'video_done' => 'date',
         'artwork_reminder' => 'date',
         'pending_approval' => 'date',
         'video_approved' => 'date',
         'video_scheduled' => 'date',
         'video_posted' => 'date',
         'article_done' => 'date',
         'article_approved' => 'date',
         'article_scheduled' => 'date',
         'article_posted' => 'date',
         'em_date_write' => 'date',
         'em_date_to_post' => 'date',
         'em_post_date' => 'date',
     ];
    public function masterFile()
    {
        return $this->belongsTo(MasterFile::class, 'master_file_id');
    }
}
