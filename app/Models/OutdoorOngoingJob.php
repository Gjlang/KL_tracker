<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int|null $master_file_id
 * @property int|null $year
 * @property string|null $date
 * @property string|null $company
 * @property string|null $product
 * @property string|null $category
 * @property string|null $platform
 * @property string|null $location
 * @property string|null $start_date
 * @property string|null $end_date
 * @property string|null $jan
 * @property string|null $feb
 * @property string|null $mar
 * @property string|null $apr
 * @property string|null $may
 * @property string|null $jun
 * @property string|null $jul
 * @property string|null $aug
 * @property string|null $sep
 * @property string|null $oct
 * @property string|null $nov
 * @property string|null $dec
 * @property string|null $status
 * @property string|null $remarks
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorOngoingJob newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorOngoingJob newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorOngoingJob query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorOngoingJob whereApr($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorOngoingJob whereAug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorOngoingJob whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorOngoingJob whereCompany($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorOngoingJob whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorOngoingJob whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorOngoingJob whereDec($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorOngoingJob whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorOngoingJob whereFeb($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorOngoingJob whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorOngoingJob whereJan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorOngoingJob whereJul($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorOngoingJob whereJun($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorOngoingJob whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorOngoingJob whereMar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorOngoingJob whereMasterFileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorOngoingJob whereMay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorOngoingJob whereNov($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorOngoingJob whereOct($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorOngoingJob wherePlatform($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorOngoingJob whereProduct($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorOngoingJob whereRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorOngoingJob whereSep($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorOngoingJob whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorOngoingJob whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorOngoingJob whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutdoorOngoingJob whereYear($value)
 * @mixin \Eloquent
 */
class OutdoorOngoingJob extends Model
{
    use HasFactory;

    protected $table = 'outdoor_ongoing_jobs';

    protected $fillable = [
        'master_file_id','year','date','company','product','category','platform','location',
        'start_date','end_date',
        'jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec',
        'status','remarks'
    ];
}
