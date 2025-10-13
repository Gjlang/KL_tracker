<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $master_file_id
 * @property string $sub_product
 * @property int $qty
 * @property string|null $site
 * @property string|null $size
 * @property string|null $district_council
 * @property string|null $coordinates
 * @property string|null $remarks
 * @property \Illuminate\Support\Carbon|null $start_date
 * @property \Illuminate\Support\Carbon|null $end_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $billboard_id
 * @property-read \App\Models\Billboard|null $billboard
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorItem whereBillboardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorItem whereCoordinates($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorItem whereDistrictCouncil($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorItem whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorItem whereMasterFileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorItem whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorItem whereRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorItem whereSite($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorItem whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorItem whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorItem whereSubProduct($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorItem whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class OutdoorItem extends Model
{
    protected $fillable = [
        'master_file_id',
        'sub_product','qty','site','size',
        'district_council','coordinates','remarks',
        'start_date','end_date',
        'billboard_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function billboard()
    {
        return $this->belongsTo(Billboard::class, 'billboard_id');
    }
}
