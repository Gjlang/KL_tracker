<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MasterFile extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'master_files';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'month',
        'date',
        'company',
        'product',
        'traffic',
        'duration',
        'status',
        'client',
        'date_finish',
        'job_number',
        'artwork',
        'invoice_date',
        'invoice_number',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'date' => 'date',
        'date_finish' => 'date',
        'invoice_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that should be mutated to dates.
     */
    protected $dates = [
        'date',
        'date_finish',
        'invoice_date',
        'created_at',
        'updated_at',
    ];

    /**
     * Get the available product options
     */
    public static function getProductOptions()
    {
        return [
            'HM',
            'TB',
            'TTM',
            'BB',
            'Star',
            'KLTG',
            'Flyers',
            'Bunting',
            'KLTG listing',
            'KLTG quarter page',
            'Signages',
            'FB IG Ad'
        ];
    }

    /**
     * Get the available artwork options
     */
    public static function getArtworkOptions()
    {
        return [
            'BGOC',
            'Client'
        ];
    }

    /**
     * Get the available status options
     */
    public static function getStatusOptions()
    {
        return [
            'pending',
            'ongoing',
            'completed'
        ];
    }

    /**
     * Scope a query to only include records with a specific status.
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include records for a specific month.
     */
    public function scopeMonth($query, $month)
    {
        return $query->where('month', $month);
    }

    /**
     * Scope a query to only include records for a specific product.
     */
    public function scopeProduct($query, $product)
    {
        return $query->where('product', $product);
    }

    /**
     * Get formatted date attribute
     */
    public function getFormattedDateAttribute()
    {
        return $this->date ? Carbon::parse($this->date)->format('Y-m-d') : null;
    }

    /**
     * Get formatted date finish attribute
     */
    public function getFormattedDateFinishAttribute()
    {
        return $this->date_finish ? Carbon::parse($this->date_finish)->format('Y-m-d') : null;
    }

    /**
     * Get formatted invoice date attribute
     */
    public function getFormattedInvoiceDateAttribute()
    {
        return $this->invoice_date ? Carbon::parse($this->invoice_date)->format('Y-m-d') : null;
    }

    public function timeline()
    {
        return $this->hasOne(\App\Models\MasterFileTimeline::class);
    }

    /**
     * Get status badge class for display
     */
    public function getStatusBadgeClassAttribute()
    {
        switch($this->status) {
            case 'completed':
                return 'status-completed';
            case 'ongoing':
                return 'status-ongoing';
            case 'pending':
                return 'status-pending';
            default:
                return 'status-pending';
        }
    }
}
