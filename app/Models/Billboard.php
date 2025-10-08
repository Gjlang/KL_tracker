<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Billboard extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'billboards';

    protected $fillable = [
        'location_id',
        'site_number',
        'site_type',
        'gps_latitude',
        'gps_longitude',
        'gps_url',
        'traffic_volume',
        'size',
        'type',
        'prefix',
        'lighting',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

}
