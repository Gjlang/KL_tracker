<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $master_file_id
 * @property int|null $outdoor_item_id
 * @property int $year
 * @property int $month
 * @property string $field_key
 * @property string $field_type
 * @property string|null $value_text
 * @property \Illuminate\Support\Carbon|null $value_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorMonthlyDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorMonthlyDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorMonthlyDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorMonthlyDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorMonthlyDetail whereFieldKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorMonthlyDetail whereFieldType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorMonthlyDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorMonthlyDetail whereMasterFileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorMonthlyDetail whereMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorMonthlyDetail whereOutdoorItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorMonthlyDetail whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorMonthlyDetail whereValueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorMonthlyDetail whereValueText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorMonthlyDetail whereYear($value)
 * @mixin \Eloquent
 */
class OutdoorMonthlyDetail extends Model
{
    protected $table = 'outdoor_monthly_details';

    protected $fillable = [
        'master_file_id',
        'outdoor_item_id',
        'year',
        'month',
        'field_key',
        'field_type',   // enum: 'text' | 'date'
        'value_text',
        'value_date',
    ];

    protected $casts = [
        'master_file_id'  => 'integer',
        'outdoor_item_id' => 'integer',
        'year'            => 'integer',
        'month'           => 'integer',
        'value_date'      => 'date',
    ];
}
