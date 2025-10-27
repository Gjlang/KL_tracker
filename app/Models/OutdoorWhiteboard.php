<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $master_file_id
 * @property int $outdoor_item_id
 * @property string|null $client_text
 * @property \Illuminate\Support\Carbon|null $client_date
 * @property string|null $po_text
 * @property \Illuminate\Support\Carbon|null $po_date
 * @property string|null $contractor_id
 * @property \Illuminate\Support\Carbon|null $supplier_date
 * @property string|null $storage_text
 * @property \Illuminate\Support\Carbon|null $storage_date
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\OutdoorItem $item
 * @property-read \App\Models\MasterFile|null $masterFile
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorWhiteboard newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorWhiteboard newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorWhiteboard query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorWhiteboard whereClientDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorWhiteboard whereClientText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorWhiteboard whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorWhiteboard whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorWhiteboard whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorWhiteboard whereMasterFileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorWhiteboard whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorWhiteboard whereOutdoorItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorWhiteboard wherePoDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorWhiteboard wherePoText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorWhiteboard whereStorageDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorWhiteboard whereStorageText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorWhiteboard whereSupplierDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorWhiteboard whereSupplierText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorWhiteboard whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class OutdoorWhiteboard extends Model
{
    protected $table = 'outdoor_whiteboards';

    // Option A: explicit allow-list
    protected $fillable = [
        'outdoor_item_id',
        'master_file_id',
        'client_text',
        'client_date',
        'po_text',
        'po_date',
        'install_date',
        'dismantle_date',
        'contractor_id',
        'supplier_date',
        'storage_text',
        'storage_date',
        'notes',
        'completed_at',
    ];

    protected $casts = [
        'client_date'   => 'date',
        'po_date'       => 'date',
        'install_date' => 'date',
        'dismantle_date' => 'date',
        'supplier_date' => 'date',
        'storage_date'  => 'date',
        'completed_at'  => 'datetime',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(OutdoorItem::class, 'outdoor_item_id');
    }

    public function masterFile(): BelongsTo
    {
        return $this->belongsTo(MasterFile::class, 'master_file_id');
    }
}
