<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MediaOngoingJob extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'media_ongoing_jobs';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'master_file_id',
        'date','company','product','category','location',
        'start_date','end_date',
        'jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec',
        'remarks'
    ];


    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that should be mutated to dates.
     */
    protected $dates = [
        'date',
        'start_date',
        'end_date',
        'created_at',
        'updated_at'
    ];

    public function masterFile()
    {
        return $this->belongsTo(MasterFile::class);
    }


    /**
     * Get formatted date attribute
     */
    public function getFormattedDateAttribute()
    {
        return $this->date ? Carbon::parse($this->date)->format('M d, Y') : '';
    }

    /**
     * Get formatted start date attribute
     */
    public function getFormattedStartDateAttribute()
    {
        return $this->start_date ? Carbon::parse($this->start_date)->format('M d, Y') : '';
    }

    /**
     * Get formatted end date attribute
     */
    public function getFormattedEndDateAttribute()
    {
        return $this->end_date ? Carbon::parse($this->end_date)->format('M d, Y') : '';
    }

    /**
     * Get short formatted start date
     */
    public function getFormattedStartAttribute()
    {
        return $this->start_date ? Carbon::parse($this->start_date)->format('M d') : '';
    }

    /**
     * Get short formatted end date
     */
    public function getFormattedEndAttribute()
    {
        return $this->end_date ? Carbon::parse($this->end_date)->format('M d') : '';
    }

    /**
     * Scope for current year jobs
     */
    public function scopeCurrentYear($query)
    {
        return $query->whereYear('date', now()->year);
    }

    /**
     * Scope for jobs by company
     */
    public function scopeByCompany($query, $company)
    {
        return $query->where('company', 'like', '%' . $company . '%');
    }

    /**
     * Scope for jobs by product
     */
    public function scopeByProduct($query, $product)
    {
        return $query->where('product', $product);
    }

    /**
     * Scope for jobs by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get all monthly fields as an array
     */
    public function getMonthlyDataAttribute()
    {
        return [
            'jan' => $this->jan,
            'feb' => $this->feb,
            'mar' => $this->mar,
            'apr' => $this->apr,
            'may' => $this->may,
            'jun' => $this->jun,
            'jul' => $this->jul,
            'aug' => $this->aug,
            'sep' => $this->sep,
            'oct' => $this->oct,
            'nov' => $this->nov,
            'dec' => $this->dec,
        ];
    }

    /**
     * Get months that have data
     */
    public function getActiveMonthsAttribute()
    {
        $months = [];
        $monthNames = [
            'jan' => 'January', 'feb' => 'February', 'mar' => 'March',
            'apr' => 'April', 'may' => 'May', 'jun' => 'June',
            'jul' => 'July', 'aug' => 'August', 'sep' => 'September',
            'oct' => 'October', 'nov' => 'November', 'dec' => 'December'
        ];

        foreach ($monthNames as $short => $full) {
            if (!empty($this->$short)) {
                $months[] = [
                    'short' => $short,
                    'full' => $full,
                    'value' => $this->$short
                ];
            }
        }

        return collect($months);
    }

    /**
     * Check if job is active in a specific month
     */
    public function isActiveInMonth($month)
    {
        $month = strtolower($month);
        return !empty($this->$month);
    }

    /**
     * Get duration in days
     */
    public function getDurationAttribute()
    {
        if ($this->start_date && $this->end_date) {
            return Carbon::parse($this->start_date)->diffInDays(Carbon::parse($this->end_date)) + 1;
        }
        return null;
    }

    /**
     * Get the available product options for Media
     */
    public static function getProductOptions()
    {
        return [
            'FB IG Ad',
            'Social Media Post',
            'Video Content',
            'Story Content',
            'Reel Content',
            'Campaign Ad',
            'Sponsored Content'
        ];
    }

    /**
     * Get the available category options
     */
    public static function getCategoryOptions()
    {
        return [
            'Media',
            'Social Media',
            'Digital Marketing',
            'Content Creation',
            'Advertising'
        ];
    }

    /**
     * Get color class based on category for UI display
     */
    public function getCategoryColorAttribute()
    {
        return match(strtolower($this->category ?? '')) {
            'media' => 'bg-blue-100 text-blue-800',
            'social media' => 'bg-pink-100 text-pink-800',
            'digital marketing' => 'bg-green-100 text-green-800',
            'content creation' => 'bg-purple-100 text-purple-800',
            'advertising' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Scope for jobs within date range
     */
    public function scopeWithinDateRange($query, $startDate, $endDate)
    {
        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate, $endDate])
              ->orWhereBetween('end_date', [$startDate, $endDate])
              ->orWhere(function ($q2) use ($startDate, $endDate) {
                  $q2->where('start_date', '<=', $startDate)
                     ->where('end_date', '>=', $endDate);
              });
        });
    }
}
