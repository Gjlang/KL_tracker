<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutdoorTrackCoordinator extends Model
{
    // 👇 pastikan table name benar
    protected $table = 'outdoor_coordinator_trackings';

    // kalau primary key bukan 'id', set disini (seharusnya id)
    protected $primaryKey = 'id';

    public $timestamps = true; // karena tabel ada created_at/updated_at

    protected $guarded = []; // SEMENTARA kosongkan guard agar semua field bisa diisi

    protected $casts = [
        'received_approval'   => 'date',
        'sent_to_printer'     => 'date',
        'collection_printer'  => 'date',
        'installation'        => 'date',
        'dismantle'           => 'date',
        'next_follow_up'      => 'date',
        'masterfile_created_at' => 'datetime',
    ];
}
