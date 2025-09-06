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
        'month','date','company','product','product_category','location','traffic','duration',
        'status','remarks','client','date_finish','job_number','artwork','invoice_date',
        'invoice_number','contact_number','email',

        // KLTG-only
        'kltg_industry','kltg_x','kltg_edition','kltg_material_cbp','kltg_print',
        'kltg_article','kltg_video','kltg_leaderboard','kltg_qr_code','kltg_blog','kltg_em','kltg_remarks',

        // Outdoor-only
        'outdoor_size','outdoor_district_council','outdoor_coordinates',
        'amount',
    ];


    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'date' => 'datetime',
        'date_finish' => 'datetime',
        'invoice_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'amount' => 'decimal:2',
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
            'Newspaper',
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

    // app/Models/MasterFile.php
    public function mediaCoordinator() {
        return $this->hasOne(MediaCoordinatorTracking::class);
    }

    public function outdoorTrackCoordinator()
    {
        return $this->hasOne(OutdoorTrackCoordinator::class);
    }

    // Add to app/Models/MasterFile.php
    public function kltgCoordinatorList()
    {
        return $this->hasOne(KltgCoordinatorList::class, 'master_file_id');
    }

    public function kltgDetails()
    {
        return $this->hasMany(KltgMonthlyDetail::class, 'master_file_id');
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

    /**
     * Auto-detect product category based on product type
     */
    public static function detectCategory($product)
    {
        $product = strtolower(trim($product));

        $outdoor = ['hm', 'tb', 'ttm', 'bb', 'star', 'flyers', 'bunting', 'signages', 'newspaper'];
        $kltg = ['kltg', 'kltg listing', 'kltg quarter page'];
        $media = ['fb ig ad'];

        if (in_array($product, $outdoor)) {
            return 'Outdoor';
        } elseif (in_array($product, $kltg)) {
            return 'KLTG';
        } elseif (in_array($product, $media)) {
            return 'Media';
        }

        return 'Other'; // fallback
    }

    public function mediaOngoingJobs()
    {
        return $this->hasMany(MediaOngoingJob::class);
    }

    public function scopeCategory($query, $category)
    {
        return $query->where('product_category', $category);
    }

      public function kltgMatrix()
    {
        return $this->hasMany(KltgMonthlyDetail::class);
    }

    public function mediaOngoingJob()
    {
        return $this->hasOne(MediaOngoingJob::class, 'master_file_id');
    }

    public function kltgMonthlyDetail()
    {
        return $this->hasOne(KltgMonthlyDetail::class);
    }

    public function kltgMonthly()
    {
        return $this->hasOne(KltgMonthlyDetail::class, 'master_file_id');
    }


    public function outdoorTracking()
    {
        return $this->hasOne(OutdoorCoordinatorTracking::class);
    }

    public function outdoorItems(){
    return $this->hasMany(OutdoorItem::class);
    }

}
