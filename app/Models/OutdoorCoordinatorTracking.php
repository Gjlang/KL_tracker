<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class OutdoorCoordinatorTracking extends Model
{
    use HasFactory;

    protected $guarded = [];

protected $casts = [
    // Existing date/datetime fields
    'received_approval'      => 'date',
    'sent_to_printer'        => 'date',
    'collection_printer'     => 'date',
    'installation'            => 'date',
    'dismantle'               => 'date',
    'next_follow_up'          => 'date',
    'masterfile_created_at'   => 'datetime',
    'created_at'               => 'datetime',
    'updated_at'               => 'datetime',

    // NEW paired DATE fields
    'site_date'                => 'date',
    'payment_date'             => 'date',
    'material_date'             => 'date',
    'artwork_date'               => 'date',

    // Notes paired with existing milestone dates
    'received_approval_note'      => 'string',
    'sent_to_printer_note'        => 'string',
    'collection_printer_note'     => 'string',
    'installation_note'            => 'string',
    'dismantle_note'               => 'string',
    'next_follow_up_note'          => 'string',

    // Month flags – keep as boolean for easy checkbox binding
    'month_jan' => 'boolean',
    'month_feb' => 'boolean',
    'month_mar' => 'boolean',
    'month_apr' => 'boolean',
    'month_may' => 'boolean',
    'month_jun' => 'boolean',
    'month_jul' => 'boolean',
    'month_aug' => 'boolean',
    'month_sep' => 'boolean',
    'month_oct' => 'boolean',
    'month_nov' => 'boolean',
    'month_dec' => 'boolean',
];

protected $fillable = [
    'master_file_id','outdoor_item_id','masterfile_created_at',
    'year','month','client','product','site',
    'payment','material','artwork',

    // NEW pairs
    'site_date','payment_date','material_date','artwork_date',
    'received_approval_note','sent_to_printer_note',
    'collection_printer_note','installation_note','dismantle_note','next_follow_up_note',

    // existing dates
    'received_approval','sent_to_printer','collection_printer','installation','dismantle',

    'remarks','next_follow_up','status',
    'month_jan','month_feb','month_mar','month_apr','month_may','month_jun',
    'month_jul','month_aug','month_sep','month_oct','month_nov','month_dec',
];


    /**
     * Relationship to MasterFile
     */
    public function masterFile()
    {
        return $this->belongsTo(MasterFile::class, 'master_file_id');
    }

    /**
     * Scope for filtering by year
     */
    public function scopeFilterByYear($query, $year)
    {
        if ($year) {
            return $query->whereYear('created_at', $year);
        }
        return $query;
    }

    /**
     * Scope for filtering by client (through master file)
     */
    public function scopeFilterByClient($query, $client)
    {
        if ($client) {
            return $query->whereHas('masterFile', function ($q) use ($client) {
                $q->where('client', $client);
            });
        }
        return $query;
    }

    /**
     * Scope for filtering by state/location
     */
    public function scopeFilterByState($query, $state)
    {
        if ($state) {
            return $query->where('site', 'LIKE', "%{$state}%");
        }
        return $query;
    }

    /**
     * Scope for filtering by status
     */
    public function scopeFilterByStatus($query, $status)
    {
        if ($status) {
            return $query->where('status', $status);
        }
        return $query;
    }

    /**
     * Get the current month's status based on job progress
     */
    public function getCurrentMonthStatus()
    {
        $currentMonth = strtolower(now()->format('M'));
        $monthField = "month_{$currentMonth}";

        if ($this->$monthField) {
            return $this->$monthField;
        }

        // Auto-generate status based on job progress
        if ($this->status === 'completed') {
            return '✓ Done';
        } elseif ($this->status === 'ongoing') {
            return 'In Progress';
        } elseif ($this->status === 'pending') {
            return 'Pending';
        }

        return '';
    }

    /**
     * Get status color class for UI
     */
    public function getStatusColorClass()
    {
        return match ($this->status) {
            'completed' => 'bg-green-100 text-green-800',
            'ongoing' => 'bg-yellow-100 text-yellow-800',
            'pending' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get progress percentage based on completed milestones
     */
    public function getProgressPercentage()
    {
        $milestones = [
            'received_approval',
            'sent_to_printer',
            'collection_printer',
            'installation',
            'dismantle'
        ];

        $completed = 0;
        foreach ($milestones as $milestone) {
            if (!empty($this->$milestone)) {
                $completed++;
            }
        }

        return round(($completed / count($milestones)) * 100);
    }

    /**
     * Auto-update status based on progress
     */
    public function updateStatusBasedOnProgress()
    {
        if (!empty($this->dismantle)) {
            $this->status = 'completed';
        } elseif (!empty($this->installation)) {
            $this->status = 'ongoing';
        } else {
            $this->status = 'pending';
        }

        return $this;
    }

    /**
     * Get next milestone that needs completion
     */
    public function getNextMilestone()
    {
        $milestones = [
            'received_approval' => 'Awaiting Approval',
            'sent_to_printer' => 'Send to Printer',
            'collection_printer' => 'Collect from Printer',
            'installation' => 'Installation',
            'dismantle' => 'Dismantle'
        ];

        foreach ($milestones as $field => $label) {
            if (empty($this->$field)) {
                return $label;
            }
        }

        return 'Completed';
    }

    /**
     * Scope for outdoor products only
     */
    public function scopeOutdoorOnly($query)
    {
        return $query->whereHas('masterFile', function ($q) {
            $q->whereIn('product_category', ['HM', 'TB', 'TTM', 'BB', 'Star', 'Flyers', 'Bunting', 'Signages', 'Outdoor', 'Newspaper'])
              ->orWhere('product_category', 'LIKE', '%outdoor%');
        });
    }

    /**
     * Get monthly status for a specific month
     */
    public function getMonthlyStatus($month)
    {
        $monthField = "month_" . strtolower($month);
        return $this->$monthField ?? '';
    }

    /**
     * Set monthly status for a specific month
     */
    public function setMonthlyStatus($month, $status)
    {
        $monthField = "month_" . strtolower($month);
        $this->$monthField = $status;
        return $this;
    }

    /**
     * Boot method to auto-update status on save
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Auto-update status based on progress milestones
            if (!empty($model->dismantle)) {
                $model->status = 'completed';
            } elseif (!empty($model->installation)) {
                $model->status = 'ongoing';
            } elseif (empty($model->status)) {
                $model->status = 'pending';
            }
        });
    }

    /**
     * Check if job is overdue based on master file date
     */
    public function isOverdue()
    {
        if (!$this->masterFile || !$this->masterFile->date_finish) {
            return false;
        }

        return $this->status !== 'completed' &&
               Carbon::parse($this->masterFile->date_finish)->isPast();
    }

    /**
     * Get days until deadline
     */
    public function getDaysUntilDeadline()
    {
        if (!$this->masterFile || !$this->masterFile->date_finish) {
            return null;
        }

        return Carbon::now()->diffInDays(Carbon::parse($this->masterFile->date_finish), false);
    }
}
