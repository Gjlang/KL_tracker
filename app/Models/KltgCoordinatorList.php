<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $master_file_id
 * @property int|null $year
 * @property int|null $month
 * @property string|null $subcategory
 * @property string|null $company_snapshot
 * @property string|null $client_bp
 * @property string|null $material_reminder_text
 * @property string|null $title_snapshot
 * @property string|null $x
 * @property string|null $edition
 * @property string|null $publication
 * @property string|null $artwork_bp_client
 * @property \Illuminate\Support\Carbon|null $artwork_reminder
 * @property string|null $material_record
 * @property string|null $artwork_done
 * @property string|null $send_chop_sign
 * @property string|null $chop_sign_approval
 * @property string|null $park_in_file_server
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $collection_printer
 * @property string|null $sent_to_client
 * @property string|null $approved_client
 * @property string|null $sent_to_printer
 * @property string|null $printed
 * @property string|null $delivered
 * @property string|null $remarks
 * @property string|null $post_link
 * @property \Illuminate\Support\Carbon|null $em_date_write
 * @property \Illuminate\Support\Carbon|null $em_date_to_post
 * @property \Illuminate\Support\Carbon|null $em_post_date
 * @property int|null $em_qty
 * @property string|null $blog_link
 * @property \Illuminate\Support\Carbon|null $video_done
 * @property \Illuminate\Support\Carbon|null $pending_approval
 * @property \Illuminate\Support\Carbon|null $video_approved
 * @property \Illuminate\Support\Carbon|null $video_scheduled
 * @property \Illuminate\Support\Carbon|null $video_posted
 * @property \Illuminate\Support\Carbon|null $article_done
 * @property \Illuminate\Support\Carbon|null $article_approved
 * @property \Illuminate\Support\Carbon|null $article_scheduled
 * @property \Illuminate\Support\Carbon|null $article_posted
 * @property-read \App\Models\MasterFile $masterFile
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgCoordinatorList newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgCoordinatorList newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgCoordinatorList query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgCoordinatorList whereApprovedClient($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgCoordinatorList whereArticleApproved($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgCoordinatorList whereArticleDone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgCoordinatorList whereArticlePosted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgCoordinatorList whereArticleScheduled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgCoordinatorList whereArtworkBpClient($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgCoordinatorList whereArtworkDone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgCoordinatorList whereArtworkReminder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgCoordinatorList whereBlogLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgCoordinatorList whereChopSignApproval($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgCoordinatorList whereClientBp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgCoordinatorList whereCollectionPrinter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgCoordinatorList whereCompanySnapshot($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgCoordinatorList whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgCoordinatorList whereDelivered($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgCoordinatorList whereEdition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgCoordinatorList whereEmDateToPost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgCoordinatorList whereEmDateWrite($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgCoordinatorList whereEmPostDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgCoordinatorList whereEmQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgCoordinatorList whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgCoordinatorList whereMasterFileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgCoordinatorList whereMaterialRecord($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgCoordinatorList whereMaterialReminderText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgCoordinatorList whereMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgCoordinatorList whereParkInFileServer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgCoordinatorList wherePendingApproval($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgCoordinatorList wherePostLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgCoordinatorList wherePrinted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgCoordinatorList wherePublication($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgCoordinatorList whereRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgCoordinatorList whereSendChopSign($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgCoordinatorList whereSentToClient($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgCoordinatorList whereSentToPrinter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgCoordinatorList whereSubcategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgCoordinatorList whereTitleSnapshot($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgCoordinatorList whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgCoordinatorList whereVideoApproved($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgCoordinatorList whereVideoDone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgCoordinatorList whereVideoPosted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgCoordinatorList whereVideoScheduled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgCoordinatorList whereX($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KltgCoordinatorList whereYear($value)
 * @mixin \Eloquent
 */
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
