<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class ClientFeedBacklog extends Model
{
    protected $fillable = [
        'master_file_id',
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

    // Optional relation
    // public function masterFile()
    // {
    //     return $this->belongsTo(MasterFile::class);
    // }
}
