<?php

// app/Http/Controllers/OutdoorOngoingJobController.php
namespace App\Http\Controllers;

use App\Models\MasterFile;                // your existing model
use App\Models\OutdoorMonthlyDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Exports\OutdoorMatrixExport;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Carbon\Carbon;

class OutdoorOngoingJobController extends Controller
{

     private function baseOutdoor(\Illuminate\Database\Eloquent\Builder $q)
{
    // Hanya kategori Outdoor (aman untuk variasi huruf)
    $q->where(function ($qq) {
        $qq->where('product_category', 'Outdoor')
           ->orWhereRaw('LOWER(product_category) LIKE ?', ['%outdoor%']);
    });

    // (Opsional tapi disarankan) Batasi hanya produk Outdoor:
    $allowed = ['TB','BB','Newspaper','Bunting','Flyers','Star','Signages', 'Newspaper'];
    $q->whereIn('product', $allowed);

    return $q;
}


public function index(Request $request)
{
    $year   = (int) ($request->input('year') ?? $request->input('outdoor_year') ?? now('Asia/Kuala_Lumpur')->year);
    $month  = (int) ($request->input('month') ?? $request->input('outdoor_month') ?? 0);
    $search = trim((string) $request->input('search',''));

    // Subproduct whitelist + value from dropdown
    $subproducts = ['BB','TB','Newspaper','Bunting','Flyers','Star','Signages'];
    $sub = trim((string) $request->input('product_category', ''));

    // Year boundaries for overlap checks
    $yearStart = Carbon::create($year, 1, 1)->toDateString();
    $yearEnd   = Carbon::create($year, 12, 31)->toDateString();

    // Preload: is there any detail for selected year (acts as a bypass)
    $d = DB::table('outdoor_monthly_details')
        ->select('outdoor_item_id')
        ->where('year', $year)
        ->distinct();

   $q = DB::table('master_files as mf')
    ->leftJoin('outdoor_items as oi', 'oi.master_file_id', '=', 'mf.id')
    ->leftJoinSub($d, 'd', function($j){
        $j->on('d.outdoor_item_id','=','oi.id');
    })
    // NEW joins to fetch site_number & district name
    ->leftJoin('billboards as bb', 'bb.id', '=', 'oi.billboard_id')
    ->leftJoin('locations as loc', 'loc.id', '=', 'bb.location_id')
    ->leftJoin('districts as dist', 'dist.id', '=', 'loc.district_id')
    // Only Outdoor category (robust)
    ->where(function ($w) {
        $w->whereRaw('LOWER(mf.product_category) LIKE ?', ['%outdoor%'])
          ->orWhereRaw('LOWER(mf.product) LIKE ?', ['%outdoor%']);
    });

    // Text search (optional)
    if ($search !== '') {
    $like = '%'.strtolower($search).'%';
    $q->where(function ($w) use ($like) {
        $w->whereRaw('LOWER(mf.company) LIKE ?', [$like])
          ->orWhereRaw('LOWER(mf.product) LIKE ?', [$like])
          ->orWhereRaw('LOWER(oi.site) LIKE ?',   [$like])
          ->orWhereRaw('LOWER(COALESCE(oi.coordinates,"")) LIKE ?',     [$like])
          ->orWhereRaw('LOWER(COALESCE(oi.district_council,"")) LIKE ?',[$like])
          // NEW:
          ->orWhereRaw('LOWER(COALESCE(bb.site_number,"")) LIKE ?',     [$like])
          ->orWhereRaw('LOWER(COALESCE(dist.name,"")) LIKE ?',          [$like]);
    });
}


    // Subproduct filter (BB/TB/…)
    if ($sub !== '' && in_array($sub, $subproducts, true)) {
        $q->whereRaw('LOWER(mf.product) = ?', [strtolower($sub)]);
    }

    // === Show items relevant to the selected YEAR (overlap OR has details in that year) ===
    $q->where(function ($w) use ($yearStart, $yearEnd) {
        $w->where(function ($x) use ($yearStart, $yearEnd) {
              // overlap if: start <= 31/12/Y AND (end IS NULL OR end >= 01/01/Y)
              $x->whereDate('mf.date', '<=', $yearEnd)
                ->where(function ($y) use ($yearStart) {
                    $y->whereNull('mf.date_finish')
                      ->orWhereDate('mf.date_finish', '>=', $yearStart);
                });
          })
          // or has any monthly details in that year
          ->orWhereNotNull('d.outdoor_item_id');
    });

    // === If a specific MONTH is chosen, also require overlap with that month OR have detail (Y,M) ===
    if ($month > 0) {
        $mStart = Carbon::create($year, $month, 1)->startOfMonth()->toDateString();
        $mEnd   = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();

        $q->where(function ($w) use ($mStart, $mEnd, $year, $month) {
            $w->where(function ($x) use ($mStart, $mEnd) {
                  $x->whereDate('mf.date', '<=', $mEnd)
                    ->where(function ($y) use ($mStart) {
                        $y->whereNull('mf.date_finish')
                          ->orWhereDate('mf.date_finish', '>=', $mStart);
                    });
              })
              ->orWhereExists(function ($sq) use ($year, $month) {
                  $sq->select(DB::raw(1))
                     ->from('outdoor_monthly_details as omd')
                     ->whereColumn('omd.outdoor_item_id', 'oi.id')
                     ->where('omd.year', $year)
                     ->where('omd.month', $month);
              });
        });
    }
$rows = $q->select([
        'mf.id',
        'mf.company','mf.product','mf.product_category',
        'mf.date','mf.date_finish','mf.month','mf.created_at',
        'oi.id as outdoor_item_id','oi.site','oi.size','oi.coordinates','oi.district_council',
        // NEW aliases for Blade:
        'bb.site_number as site_code',
        'dist.name      as district_name',
    ])
    ->whereNotNull('oi.id')
    // optional: sort by company then site_number if ada, else oi.site
    ->orderByRaw('LOWER(mf.company) ASC')
    ->orderByRaw('LOWER(COALESCE(bb.site_number, oi.site)) ASC')
    ->get();


    // Load monthly details for the selected year (+ optional month)
    $details = DB::table('outdoor_monthly_details')
        ->where('year', $year)
        ->when($month > 0, fn($qq) => $qq->where('month',$month))
        ->whereIn('outdoor_item_id', $rows->pluck('outdoor_item_id')->unique()->values())
        ->get();

    $existing = $details->mapWithKeys(fn($r) => [
        $r->outdoor_item_id.':'.$r->month.':'.$r->field_key => $r
    ]);

    // Years dropdown (from masters + details)
    $years = collect()
        ->merge(
            DB::table('master_files as mf')
              ->leftJoin('outdoor_items as oi','oi.master_file_id','=','mf.id')
              ->where(function ($w) {
                  $w->whereRaw('LOWER(mf.product_category) LIKE ?', ['%outdoor%'])
                    ->orWhereRaw('LOWER(mf.product) LIKE ?', ['%outdoor%']);
              })
              ->whereNotNull('mf.date')->selectRaw('YEAR(mf.date) as y')->pluck('y')
        )
        ->merge(
            DB::table('master_files as mf')
              ->leftJoin('outdoor_items as oi','oi.master_file_id','=','mf.id')
              ->where(function ($w) {
                  $w->whereRaw('LOWER(mf.product_category) LIKE ?', ['%outdoor%'])
                    ->orWhereRaw('LOWER(mf.product) LIKE ?', ['%outdoor%']);
              })
              ->whereNotNull('mf.date_finish')->selectRaw('YEAR(mf.date_finish) as y')->pluck('y')
        )
        ->merge(
            DB::table('outdoor_monthly_details')->distinct()->pluck('year')
        )
        ->map(fn($v)=>(int)$v)->filter()->unique()->sort()->values();

    // Months list
    $months = collect(range(1,12))->map(fn($m)=>[
        'value'=>$m,'label'=>Carbon::create(null,$m,1)->format('F')
    ])->all();

    return view('dashboard.outdoor', [
        'rows'        => $rows,
        'years'       => $years,
        'year'        => $year,
        'months'      => $months,
        'month'       => $month,
        'existing'    => $existing,
        'search'      => $search,
        'sub'         => $sub,
        'subproducts' => $subproducts,
    ]);
}




public function cloneYear(Request $request)
{
    $toYear   = (int) $request->input('to_year');
    $fromYear = (int) $request->input('from_year', $toYear - 1);

    if ($toYear < 2000 || $toYear > 2100) {
        return back()->with('status', 'Invalid target year.');
    }

    // Field keys you use in the grid
    $fieldKeys = ['status','installed_on','approved_on','material_received','remark'];

    // Pick the visible Outdoor rows for the target year scope (same base as index)
    $q = DB::table('master_files as mf')
        ->leftJoin('outdoor_items as oi', 'oi.master_file_id', '=', 'mf.id')
        ->where(function ($w) {
            $w->whereRaw('LOWER(mf.product_category) LIKE ?', ['%outdoor%'])
              ->orWhereRaw('LOWER(mf.product) LIKE ?', ['%outdoor%']);
        });

    // use the same finish-year-first constraint used in index()
    $q->where(function ($w) use ($toYear) {
        $w->whereYear('mf.date_finish', $toYear)
          ->orWhere(function ($x) use ($toYear) {
              $x->whereNull('mf.date_finish')
                ->where(function ($yq) use ($toYear) {
                    $yq->whereYear('mf.date', $toYear)
                       ->orWhereYear('mf.created_at', $toYear);
                });
          });
    });

    $sites = $q->select(['mf.id as master_file_id','oi.id as outdoor_item_id'])
               ->whereNotNull('oi.id')
               ->get();

    if ($sites->isEmpty()) {
        return back()->with('status', "No Outdoor sites to scaffold for {$toYear}.");
    }

    // Build empty stubs for every site x month x field_key, but only insert if missing
    $payload = [];
    foreach ($sites as $s) {
        for ($m = 1; $m <= 12; $m++) {
            foreach ($fieldKeys as $k) {
                $payload[] = [
                    'master_file_id'  => (int) $s->master_file_id,
                    'outdoor_item_id' => (int) $s->outdoor_item_id,
                    'year'            => $toYear,
                    'month'           => $m,
                    'field_key'       => $k,
                    'field_type'      => in_array($k, ['installed_on','approved_on','material_received'], true) ? 'date' : 'text',
                    'value_text'      => null,
                    'value_date'      => null,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ];
            }
        }
    }

    // Insert-if-not-exists on the composite key
    // Requires a UNIQUE index (see migration below)
    $chunkSize = 1000;
    foreach (array_chunk($payload, $chunkSize) as $chunk) {
        DB::table('outdoor_monthly_details')->upsert(
            $chunk,
            ['master_file_id','outdoor_item_id','year','month','field_key'], // unique by
            [] // do not update existing rows (we don't want to overwrite values)
        );
    }

    return back()->with('status', "Structure for {$toYear} prepared (from {$fromYear}) with empty values.");
}




    public function events(Request $request)
    {
        $q = MasterFile::query();
        $q = $this->baseOutdoor($q); // Apply Outdoor guard

        // Add any date window constraints here if needed
        // $q->whereBetween('date', [$start, $end]);

        return response()->json(
            $q->select('id','client','product','end_date as start','end_date as end')->get()
        );
    }



public function exportMatrix(Request $req)
{
    $tz    = 'Asia/Kuala_Lumpur';
    $today = now($tz)->format('d/m/Y');                // ✅ current date
    $year  = (int)($req->input('year') ?: now($tz)->year);
    $product = trim((string)$req->input('product', ''));

    // 1) Base query: per-site rows
    $sitesQ = DB::table('master_files as mf')
    ->join('outdoor_items as oi', 'oi.master_file_id', '=', 'mf.id')
    // NEW: pull billboard site code + district name for consistent "Site(s)" display
    ->leftJoin('billboards as bb', 'bb.id', '=', 'oi.billboard_id')
    ->leftJoin('locations as loc', 'loc.id', '=', 'bb.location_id')
    ->leftJoin('districts as d', 'd.id', '=', 'loc.district_id')

    ->when($product !== '', function ($q) use ($product) {
        $q->where('mf.product', $product);
    })
    ->where(function ($w) use ($year) {
        $w->whereYear('mf.date', $year)
          ->orWhereYear('mf.date_finish', $year)
          ->orWhereYear('mf.created_at', $year);
    })

    // Sort: company → site_number (nulls last) → road name (nulls last)
    ->orderByRaw('LOWER(mf.company) ASC')
    ->orderByRaw('bb.site_number IS NULL, LOWER(bb.site_number) ASC')
    ->orderByRaw('oi.site IS NULL, LOWER(oi.site) ASC');

    $siteRows = $sitesQ->get([
    'mf.id              as master_file_id',
    'oi.id              as outdoor_item_id',
    'mf.company',
    'mf.product',
    'mf.product_category',
    'mf.created_at      as created_at',
    'mf.date            as start',
    'mf.date_finish     as end',

    // For "Site(s)" display parity with the Blade
    'oi.site            as road_raw',        // e.g., street/mall name (fallback)
    'bb.site_number     as site_code',       // e.g., TB-SEL-0022-MDSK-A
    'd.name             as district_name',   // e.g., Seri Kembangan
]);


    // 5) File naming - MOVED UP
    $title = "Outdoor - Monthly - {$today} - {$year}";
    $file  = Str::slug($title, '_').'.xlsx';

    if ($siteRows->isEmpty()) {
        return (new OutdoorMatrixExport([], $title))->download($file);  // ✅ Pass title
    }

    // 2) Load monthly details
    $siteIds = $siteRows->pluck('outdoor_item_id')->filter()->unique()->values();
    $details = DB::table('outdoor_monthly_details as omd')
        ->where('omd.year', $year)
        ->whereIn('omd.outdoor_item_id', $siteIds)
        ->orderBy('omd.outdoor_item_id')
        ->orderBy('omd.month')
        ->get([
            'omd.outdoor_item_id',
            'omd.month',
            'omd.field_key',
            'omd.field_type',
            'omd.value_text',
            'omd.value_date',
        ]);

    // 3) Group months by item
    $monthsByItem = [];
    foreach ($details->groupBy('outdoor_item_id') as $itemId => $rows) {
        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $months[$m] = ['status' => '', 'date' => null];
        }
        foreach ($rows as $r) {
            $mn = (int)$r->month;
            if ($mn < 1 || $mn > 12) continue;
            $fk = strtolower((string)$r->field_key);

            if ($r->field_type === 'text' && $fk === 'status' && $r->value_text) {
                $months[$mn]['status'] = $r->value_text;
            }
            if ($r->field_type === 'date' && $r->value_date) {
                $months[$mn]['date'] = $r->value_date;
            }
        }
        $monthsByItem[$itemId] = $months;
    }

    // 4) Build records
    $records = [];
    foreach ($siteRows as $r) {
    $itemId = (int)$r->outdoor_item_id;

    $months = $monthsByItem[$itemId] ?? (function () {
        $m = []; for ($i=1; $i<=12; $i++) $m[$i] = ['status'=>'','date'=>null]; return $m;
    })();

    $siteDisplay = self::composeSiteDisplay($r->site_code ?? '', $r->road_raw ?? '', $r->district_name ?? '');


    $records[] = [
        'summary' => [
            'date'     => $r->created_at,                        // "Date Created"
            'company'  => $r->company,
            'product'  => $r->product,
            'site'     => $siteDisplay,                          // ✅ same as Blade "Site(s)"
            'category' => $r->product_category ?: 'Outdoor',
            'start'    => $r->start,
            'end'      => $r->end,
        ],
        'months' => $months,
    ];
}


    return (new OutdoorMatrixExport($records, $title))->download($file);  // ✅ Pass title
}

private static function composeSiteDisplay(?string $siteCode, ?string $roadRaw, ?string $district): string
{
    $norm = function ($s) {
        return preg_replace('/[^a-z0-9]/i', '', strtolower((string)($s ?? '')));
    };

    $siteCode = (string)($siteCode ?? '');
    $roadRaw  = (string)($roadRaw  ?? '');
    $district = (string)($district ?? '');

    $isDup = $norm($siteCode) !== '' && $norm($siteCode) === $norm($roadRaw);

    if ($siteCode !== '') {
        $parts = $isDup
            ? array_filter([$siteCode, $district], fn ($x) => $x !== '')
            : array_filter([$siteCode, $district], fn ($x) => $x !== '');
        $res = implode(' - ', $parts);
        return $res !== '' ? $res : $siteCode;
    }

    if ($district !== '') return $district;
    if ($roadRaw  !== '') return $roadRaw;
    return '—';
}


  public function upsert(Request $req)
{
    Log::info('=== OUTDOOR UPSERT DEBUG START ===', ['raw' => $req->all()]);

    $data = $req->validate([
        'master_file_id'  => 'required|integer|exists:master_files,id',
        'outdoor_item_id' => 'required|integer|exists:outdoor_items,id',
        'year'            => 'required|integer|min:2000|max:2100',
        'month'           => 'required|integer|min:1|max:12',
        'field_key'       => 'required|string|in:remark,installed_on,material_received,approved_on,status',
        'field_type'      => 'required|string|in:text,date',
        'value_text'      => 'nullable|string',
        'value_date'      => 'nullable|date',
    ]);

    // Normalize field_key consistently (lowercase)
    $fieldKey = strtolower(trim($data['field_key']));

    // Decide which value column to use
    $values = [
        'field_type' => $data['field_type'],
        'value_text' => $data['field_type'] === 'text' ? ($data['value_text'] ?? '') : null,
        'value_date' => $data['field_type'] === 'date' ? ($data['value_date'] ?? null) : null,
    ];

    try {
        $saved = OutdoorMonthlyDetail::updateOrCreate(
            [
                'master_file_id'  => (int) $data['master_file_id'],
                'outdoor_item_id' => (int) $data['outdoor_item_id'], // 🔑 site id included
                'year'            => (int) $data['year'],
                'month'           => (int) $data['month'],
                'field_key'       => $fieldKey,
            ],
            $values
        );

        Log::info('=== OUTDOOR UPSERT OK ===', ['id' => $saved->id]);
        return response()->json(['ok' => true, 'saved' => $saved], 200);

    } catch (\Throwable $e) {
        Log::error('=== OUTDOOR UPSERT FAIL ===', [
            'err'  => $e->getMessage(),
            'data' => $data
        ]);
        return response()->json(['ok' => false, 'message' => $e->getMessage()], 500);
    }
}


}
