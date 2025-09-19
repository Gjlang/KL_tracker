<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OutdoorWhiteboard extends Model
{
    protected $table = 'outdoor_whiteboards';

    // Option A: explicit allow-list
    protected $fillable = [
        'outdoor_item_id',
        'master_file_id',
        'client_text', 'client_date',
        'po_text',     'po_date',
        'supplier_text','supplier_date',
        'storage_text','storage_date',
        'notes',
        'completed_at',
    ];

    protected $casts = [
        'client_date'   => 'date',
        'po_date'       => 'date',
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
