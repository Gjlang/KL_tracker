<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $master_file_id
 * @property int $year
 * @property int $month
 * @property string $subcategory
 * @property string|null $value_text
 * @property string|null $value_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaMonthlyDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaMonthlyDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaMonthlyDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaMonthlyDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaMonthlyDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaMonthlyDetail whereMasterFileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaMonthlyDetail whereMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaMonthlyDetail whereSubcategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaMonthlyDetail whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaMonthlyDetail whereValueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaMonthlyDetail whereValueText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaMonthlyDetail whereYear($value)
 * @mixin \Eloquent
 */
class MediaMonthlyDetail extends Model
{
    protected $fillable = [
        'master_file_id','year','month','value_text','value_date'
    ];
}
