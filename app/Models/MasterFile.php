<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\ClientCompany;
use App\Models\Client;


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
        'invoice_number','contact_number','email','sales_person', 'client_id',

        // KLTG-only
        'kltg_industry','kltg_x','kltg_edition','kltg_material_cbp','kltg_print',
        'kltg_article','kltg_video','kltg_leaderboard','kltg_qr_code','kltg_blog','kltg_em','kltg_remarks',

        // Outdoor-only
        'outdoor_size','outdoor_district_council','outdoor_coordinates',
        'amount','billboard_id','company_id',
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

    protected static function booted()
    {
        static::creating(function ($m) {
            // Selalu generate baru saat create (abaikan input job_number)
            $m->job_number = app(\App\Services\JobNumberService::class)
                ->generate((string)($m->product_category ?? ''), (string)($m->product ?? ''));
        });

        // Opsional: rapikan saat update kalau formatnya salah
        static::updating(function ($m) {
            if (!$m->job_number || !preg_match('/^[A-Z0-9]{2,6}-\d{4}-\d{4}$/', $m->job_number)) {
                $m->job_number = app(\App\Services\JobNumberService::class)
                    ->generate((string)($m->product_category ?? ''), (string)($m->product ?? ''));
            }
        });
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

    public function billboard()
    {
        return $this->belongsTo(Billboard::class, 'billboard_id');
    }

    public function clientCompany()
    {
        return $this->belongsTo(ClientCompany::class, 'company_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
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



    protected function applyMonthFilter($query, $rawMonth)
{
    // Normalisasi input (1..12), terima "Jan", "January", "9", dll
    $m = null;
    if ($rawMonth !== null && $rawMonth !== '' && (int)$rawMonth !== 0) {
        $s = trim((string)$rawMonth);
        $map = [
            'jan'=>1,'january'=>1,'feb'=>2,'february'=>2,'mar'=>3,'march'=>3,
            'apr'=>4,'april'=>4,'may'=>5,'jun'=>6,'june'=>6,'jul'=>7,'july'=>7,
            'aug'=>8,'august'=>8,'sep'=>9,'sept'=>9,'september'=>9,
            'oct'=>10,'october'=>10,'nov'=>11,'november'=>11,'dec'=>12,'december'=>12,
        ];
        if (ctype_digit($s)) {
            $m = max(1, min(12, (int)$s));
        } else {
            $k = strtolower($s);
            $m = $map[$k] ?? null;
        }
    }

    if (!$m) return $query; // 0/empty = all months

    // Bentuk teks untuk dibandingkan dengan kolom 'month' yang bertipe string
    $full  = strtolower(Carbon::create(null, $m, 1)->format('F')); // January
    $abbr  = strtolower(Carbon::create(null, $m, 1)->format('M')); // Jan

    return $query->where(function ($w) use ($m, $full, $abbr) {
        // 1) Kolom month (bisa angka atau teks)
        if (Schema::hasColumn('master_files', 'month')) {
            $w->where(function($x) use ($m, $full, $abbr) {
                $x->whereRaw('CAST(`month` AS UNSIGNED) = ?', [$m])        // angka "1"
                  ->orWhereRaw('LOWER(`month`) = ?', [$full])              // "january"
                  ->orWhereRaw('LOWER(`month`) = ?', [$abbr]);             // "jan"
            });
        }

        // 2) Fallback: tanggal mulai/akhir
        if (Schema::hasColumn('master_files', 'date')) {
            $w->orWhereRaw('MONTH(`date`) = ?', [$m]);
        }
        if (Schema::hasColumn('master_files', 'date_finish')) {
            $w->orWhereRaw('MONTH(`date_finish`) = ?', [$m]);
        }
    });
}
    protected function masterIndex(Request $request, string $scope)
{
    $month = (int) $request->query('month', 0); // 0 = all
    $q     = trim((string) $request->query('q', ''));

    // Adjust this base query to your real scope logic:
    // Example 1: using product_category
    $query = MasterFile::query()
        ->when($scope === 'kltg', fn($qq) => $qq->where('product_category', 'KLTG'))
        ->when($scope === 'outdoor', fn($qq) => $qq->where('product_category', 'Outdoor'));

    // Month filter (supports either a "month" numeric column, or derives from "date")
    if ($month >= 1 && $month <= 12) {
        if (Schema::hasColumn('master_files', 'month')) {
            $query->where('month', $month);
        } elseif (Schema::hasColumn('master_files', 'date')) {
            $query->whereRaw('MONTH(`date`) = ?', [$month]);
        } else {
            // If your date column is named differently, add more fallbacks here
            $query->whereRaw('1=0'); // no-op fallback if neither exists
        }
    }

    // Global search across common fields (tweak list as needed)
    if ($q !== '') {
        $like = '%' . str_replace(['%', '_'], ['\\%','\\_'], $q) . '%';
        $query->where(function ($w) use ($like) {
            foreach ([
                'company', 'client', 'product', 'product_category',
                'location', 'site', 'size', 'district_council',
                'remarks', 'status', 'job_number', 'invoice_number', 'email',
            ] as $col) {
                if (Schema::hasColumn('master_files', $col)) {
                    $w->orWhere($col, 'LIKE', $like);
                }
            }
            // If you want to search month name via date:
            if (Schema::hasColumn('master_files', 'date')) {
                $w->orWhereRaw("DATE_FORMAT(`date`, '%M %Y') LIKE ?", [$like]);
            }
        });
    }

    // Sort & paginate
    $rows = $query->latest('created_at')->paginate(20)->withQueryString();

    // Build the $columns for your _table partial (if you already do this elsewhere, keep it)
    // Example columns — match your existing table headers/fields:
    $columns = [
        'created_at'       => 'Created',
        'company'          => 'Company',
        'client'           => 'Person In Charge',
        'product'          => 'Product',
        'product_category' => 'Category',
        'location'         => 'Location',
        'status'           => 'Status',
        'job_number'       => 'Job No',
    ];

    $active = $scope; // for the tabs

    // Return the appropriate view
    if ($scope === 'kltg') {
        return view('dashboard.master.kltg', compact('rows', 'columns', 'active'));
    }
    return view('dashboard.master.outdoor', compact('rows', 'columns', 'active'));
}



}
