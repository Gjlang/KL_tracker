<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $master_file_id
 * @property Carbon $date
 * @property Carbon|null $expected_finish_date
 * @property string|null $servicing
 * @property string|null $product
 * @property string|null $location
 * @property string $client
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClientCompany> $company
 * @property string $status
 * @property string|null $attended_by
 * @property string|null $reasons
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $billboard_id
 * @property int|null $company_id
 * @property-read \App\Models\Billboard|null $billboard
 * @property-read int|null $company_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientFeedBacklog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientFeedBacklog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientFeedBacklog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientFeedBacklog whereAttendedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientFeedBacklog whereBillboardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientFeedBacklog whereClient($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientFeedBacklog whereCompany($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientFeedBacklog whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientFeedBacklog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientFeedBacklog whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientFeedBacklog whereExpectedFinishDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientFeedBacklog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientFeedBacklog whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientFeedBacklog whereMasterFileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientFeedBacklog whereProduct($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientFeedBacklog whereReasons($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientFeedBacklog whereServicing($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientFeedBacklog whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientFeedBacklog whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ClientFeedBacklog extends Model
{
    protected $fillable = [
        'master_file_id',
        'billboard_id',
        'company_id',
        'date',
        'servicing',
        'product',
        'location',
        'client',
        'status',
        'attended_by',
        'reasons',
        'expected_finish_date',
        'company',
    ];

    protected $casts = [
        'date'                 => 'date',
        'expected_finish_date' => 'date',
    ];

    /**
     * Mutator: normalize expected_finish_date to Y-m-d before save.
     */
    public function setExpectedFinishDateAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['expected_finish_date'] = null;
            return;
        }

        // coba parse beberapa format umum
        $formats = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'm/d/Y'];
        foreach ($formats as $fmt) {
            try {
                $d = Carbon::createFromFormat($fmt, (string) $value);
                $this->attributes['expected_finish_date'] = $d->format('Y-m-d');
                return;
            } catch (\Throwable $e) {
                // coba format berikutnya
            }
        }

        // fallback ke Carbon parser default
        try {
            $d = Carbon::parse($value);
            $this->attributes['expected_finish_date'] = $d->format('Y-m-d');
        } catch (\Throwable $e) {
            $this->attributes['expected_finish_date'] = null;
        }
    }

    /**
     * Accessor: always return Carbon instance or null.
     */
    public function getExpectedFinishDateAttribute($value)
    {
        return $value ? Carbon::parse($value) : null;
    }

    public function billboard()
    {
        return $this->belongsTo(Billboard::class, 'billboard_id');
    }

    public function company()
    {
        return $this->belongsToMany(ClientCompany::class);
    }

    // Optional relation
    // public function masterFile()
    // {
    //     return $this->belongsTo(MasterFile::class);
    // }
}
