<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $location_id
 * @property string $site_number
 * @property string|null $site_type
 * @property string $gps_latitude
 * @property string $gps_longitude
 * @property string|null $traffic_volume
 * @property string $size
 * @property string $type
 * @property string $prefix
 * @property string $lighting
 * @property string $status
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $gps_url
 * @property-read \App\Models\User|null $createdBy
 * @property-read \App\Models\Location $location
 * @property-read \App\Models\User|null $updatedBy
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Billboard newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Billboard newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Billboard query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Billboard whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Billboard whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Billboard whereGpsLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Billboard whereGpsLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Billboard whereGpsUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Billboard whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Billboard whereLighting($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Billboard whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Billboard wherePrefix($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Billboard whereSiteNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Billboard whereSiteType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Billboard whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Billboard whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Billboard whereTrafficVolume($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Billboard whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Billboard whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Billboard whereUpdatedBy($value)
 * @mixin \Eloquent
 */
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

    public function outdoorItems()
    {
        return $this->hasMany(OutdoorItem::class);
    }

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
