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


/**
 * @property int $id
 * @property string $month
 * @property \Illuminate\Support\Carbon $date
 * @property string $company
 * @property string $product
 * @property string|null $product_category
 * @property string|null $location
 * @property string $traffic
 * @property string|null $duration
 * @property numeric|null $amount
 * @property string $status
 * @property string|null $remarks
 * @property Client|null $client
 * @property string|null $sales_person
 * @property string|null $barter
 * @property \Illuminate\Support\Carbon|null $date_finish
 * @property string|null $job_number
 * @property string|null $artwork
 * @property \Illuminate\Support\Carbon|null $invoice_date
 * @property string|null $invoice_number
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $contact_number
 * @property string|null $email
 * @property string|null $kltg_industry
 * @property string|null $kltg_x
 * @property string|null $kltg_edition
 * @property string|null $kltg_material_cbp
 * @property string|null $kltg_print
 * @property string|null $kltg_article
 * @property string|null $kltg_video
 * @property string|null $kltg_leaderboard
 * @property string|null $kltg_qr_code
 * @property string|null $kltg_blog
 * @property string|null $kltg_em
 * @property string|null $kltg_remarks
 * @property string|null $outdoor_size
 * @property string|null $outdoor_district_council
 * @property string|null $outdoor_coordinates
 * @property int|null $company_id
 * @property string|null $dbp_approval
 * @property-read \App\Models\Billboard|null $billboard
 * @property-read ClientCompany|null $clientCompany
 * @property-read mixed $formatted_date
 * @property-read mixed $formatted_date_finish
 * @property-read mixed $formatted_invoice_date
 * @property-read mixed $status_badge_class
 * @property-read \App\Models\KltgCoordinatorList|null $kltgCoordinatorList
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\KltgMonthlyDetail> $kltgDetails
 * @property-read int|null $kltg_details_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\KltgMonthlyDetail> $kltgMatrix
 * @property-read int|null $kltg_matrix_count
 * @property-read \App\Models\KltgMonthlyDetail|null $kltgMonthly
 * @property-read \App\Models\KltgMonthlyDetail|null $kltgMonthlyDetail
 * @property-read \App\Models\MediaCoordinatorTracking|null $mediaCoordinator
 * @property-read \App\Models\MediaOngoingJob|null $mediaOngoingJob
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MediaOngoingJob> $mediaOngoingJobs
 * @property-read int|null $media_ongoing_jobs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OutdoorItem> $outdoorItems
 * @property-read int|null $outdoor_items_count
 * @property-read \App\Models\OutdoorTrackCoordinator|null $outdoorTrackCoordinator
 * @property-read \App\Models\OutdoorCoordinatorTracking|null $outdoorTracking
 * @property-read \App\Models\MasterFileTimeline|null $timeline
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFile category($category)
 * @method static \Database\Factories\MasterFileFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFile month($month)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFile product($product)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFile query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFile status($status)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFile whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFile whereArtwork($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFile whereBarter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFile whereClient($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFile whereCompany($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFile whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFile whereContactNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFile whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFile whereDateFinish($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFile whereDbpApproval($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFile whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFile whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFile whereInvoiceDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFile whereInvoiceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFile whereJobNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFile whereKltgArticle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFile whereKltgBlog($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFile whereKltgEdition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFile whereKltgEm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFile whereKltgIndustry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFile whereKltgLeaderboard($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFile whereKltgMaterialCbp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFile whereKltgPrint($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFile whereKltgQrCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFile whereKltgRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFile whereKltgVideo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFile whereKltgX($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFile whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFile whereMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFile whereOutdoorCoordinates($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFile whereOutdoorDistrictCouncil($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFile whereOutdoorSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFile whereProduct($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFile whereProductCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFile whereRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFile whereSalesPerson($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFile whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFile whereTraffic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasterFile whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
