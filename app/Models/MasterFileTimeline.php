<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $master_file_id
 * @property string|null $product
 * @property string|null $site
 * @property string|null $client
 * @property string|null $payment
 * @property string|null $material_received
 * @property string|null $artwork
 * @property string|null $approval
 * @property string|null $sent_to_printer
 * @property string|null $installation
 * @property string|null $dismantle
 * @property string|null $remarks
 * @property string|null $next_follow_up
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\MasterFile $masterFile
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFileTimeline newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFileTimeline newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFileTimeline query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFileTimeline whereApproval($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFileTimeline whereArtwork($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFileTimeline whereClient($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFileTimeline whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFileTimeline whereDismantle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFileTimeline whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFileTimeline whereInstallation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFileTimeline whereMasterFileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFileTimeline whereMaterialReceived($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFileTimeline whereNextFollowUp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFileTimeline wherePayment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFileTimeline whereProduct($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFileTimeline whereRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFileTimeline whereSentToPrinter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFileTimeline whereSite($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFileTimeline whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
