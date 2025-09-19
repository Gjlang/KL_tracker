<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutdoorWhiteboard extends Model
{
    // Set this ONLY if your table name is NOT the default "outdoor_whiteboards".
    // protected $table = 'outdoor_whiteboards';

    // Allow mass assignment for the columns you actually write to:
    protected $fillable = [
        'master_file_id',
        'client_text',   'client_date',
        'po_text',       'po_date',
        'supplier_text', 'supplier_date',
        'storage_text',  'storage_date',
        'notes',
        'completed_at',
    ];

    // Make date columns real dates (Carbon):
    protected $casts = [
        'client_date'   => 'date',
        'po_date'       => 'date',
        'supplier_date' => 'date',
        'storage_date'  => 'date',
        'completed_at'  => 'datetime',
    ];

    // Scopes for filtering
    public function scopeActive($q)
    {
        return $q->whereNull('completed_at');
    }

    public function scopeCompleted($q)
    {
        return $q->whereNotNull('completed_at');
    }

    // Relationship back to MasterFile
    public function masterFile()
    {
        return $this->belongsTo(MasterFile::class, 'master_file_id', 'id');
    }
}
