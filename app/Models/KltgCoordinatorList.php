<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KltgCoordinatorList extends Model
{
    // Fix: Specify the correct table name
    protected $table = 'kltg_coordinator_trackings';

    protected $fillable = [
        'master_file_id','subcategory','x','edition','publication','artwork_bp_client',
        'artwork_reminder','material_record','send_chop_sign','chop_sign_approval',
        'park_in_file_server','remarks','title','client_bp','post_link',
        'video_done_date','pending_approval_date','video_approved_date',
        'video_scheduled_date','video_posted_date',
        'article_done_date','article_approved_date','article_scheduled_date','article_posted_date',
        'em_date_write','em_date_to_post','em_post_date','em_qty','blog_link',
        // include any snapshot fields you persist (company_snapshot, title_snapshot, …) if used
    ];

    protected $casts = [
        'artwork_reminder'      => 'date',
        'material_record'       => 'date',
        'send_chop_sign'        => 'date',
        'chop_sign_approval'    => 'date',
        'park_in_file_server'   => 'date',
        'video_done_date'       => 'date',
        'pending_approval_date' => 'date',
        'video_approved_date'   => 'date',
        'video_scheduled_date'  => 'date',
        'video_posted_date'     => 'date',
        'article_done_date'     => 'date',
        'article_approved_date' => 'date',
        'article_scheduled_date'=> 'date',
        'article_posted_date'   => 'date',
        'em_date_write'         => 'date',
        'em_date_to_post'       => 'date',
        'em_post_date'          => 'date',
    ];
    public function masterFile()
    {
        return $this->belongsTo(MasterFile::class, 'master_file_id');
    }
}
