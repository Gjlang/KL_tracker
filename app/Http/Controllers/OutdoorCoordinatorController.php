<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterFile;
use App\Models\OutdoorCoordinatorTracking;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse; // add at top
use Illuminate\Foundation\Configuration\Exceptions;
use Throwable;


class OutdoorCoordinatorController extends Controller
{
//    public function index(Request $request)
// {
//     // 1) Read inputs (support both names) and normalize month
//     $rawMonth = $request->input('month', $request->input('outdoor_month'));
//     $rawYear  = $request->input('year',  $request->input('outdoor_year'));

//     $normalize = function($raw): ?int {
//         if ($raw === null || $raw === '') return null; // null => "All Months"
//         $m = trim((string)$raw);
//         if (ctype_digit($m)) {
//             $n = (int)$m;
//             return ($n >= 1 && $n <= 12) ? $n : null;
//         }
//         $map = [
//             'jan'=>1,'january'=>1,'feb'=>2,'february'=>2,'mar'=>3,'march'=>3,
//             'apr'=>4,'april'=>4,'may'=>5,'jun'=>6,'june'=>6,'jul'=>7,'july'=>7,
//             'aug'=>8,'august'=>8,'sep'=>9,'sept'=>9,'september'=>9,'oct'=>10,'october'=>10,
//             'nov'=>11,'november'=>11,'dec'=>12,'december'=>12
//         ];
//         $key = strtolower(preg_replace('/[^a-z]/i', '', $m));
//         return $map[$key] ?? null;
//     };

//     $month = $normalize($rawMonth);                 // IMPORTANT: null means "All Months"
//     $year  = (int) ($rawYear ?: now()->year);

//     // 2) If a month is chosen, collect outdoor_item_ids that actually have data that month
//     $selectedItemIds = collect();
//     if ($month !== null) {
//         $selectedItemIds = DB::table('outdoor_monthly_details')
//             ->where('year', $year)
//             ->where('month', $month)
//             ->where(function ($q) {
//                 $q->whereNotNull('value_date')
//                   ->orWhere(function ($w) {
//                       $w->whereNotNull('value_text')
//                         ->whereRaw('TRIM(value_text) <> ""');
//                   });
//             })
//             ->whereNotNull('outdoor_item_id')               // ← site-level
//             ->distinct()
//             ->pluck('outdoor_item_id')                      // ← pluck item IDs, not master IDs
//             ->map(fn($v) => (int)$v)
//             ->unique()
//             ->values();
//     }

//     // 3) Base dataset (one row per site via outdoor_items)
//     $base = DB::table('master_files as mf')
//         ->join('outdoor_items as oi', 'oi.master_file_id', '=', 'mf.id') // fan out per site
//         ->leftJoin('outdoor_coordinator_trackings as t', function ($j) {
//             $j->on('t.master_file_id', '=', 'mf.id')
//               ->on('t.outdoor_item_id', '=', 'oi.id'); // tie tracking row to exact site
//         })
//         ->where(function ($q) {
//             $q->whereRaw('LOWER(mf.product) LIKE ?', ['%outdoor%'])
//               ->orWhereRaw('LOWER(mf.product_category) LIKE ?', ['%outdoor%'])
//               ->orWhereIn('mf.product_category', [
//                   'TB','BB','NEWSPAPER','BUNTING','FLYERS','STAR','SIGNAGES',
//                   'TB - Tempboard','BB - Billboard','Newspaper','Bunting','Flyers','Star','Signages'
//               ]);
//         });

//     // Apply the strict site-level month filter only when month is chosen
//     if ($month !== null) {
//         if ($selectedItemIds->isNotEmpty()) {
//             $base->whereIn('oi.id', $selectedItemIds->all());   // ← gate by outdoor_item_id
//         } else {
//             $base->whereRaw('1=0'); // no sites in that month => empty table
//         }
//     }

//     $records = $base->select([
//             't.id as id',
//             'mf.id as master_file_id',
//             'oi.id as outdoor_item_id',                 // unique per site
//             'mf.company','mf.client','mf.product','mf.product_category',
//             DB::raw('oi.site as site'),                 // site from outdoor_items
//             // Baseline tracking values (overlaid by monthly_details if month selected)
//             't.payment','t.material','t.artwork',
//             't.received_approval','t.sent_to_printer','t.collection_printer','t.installation',
//             't.dismantle','t.remarks','t.next_follow_up','t.status',
//             't.created_at as tracking_created_at',
//             // Snapshots as fallback
//             'mf.company as company_snapshot',
//             'mf.product as product_snapshot',
//         ])
//         ->orderByRaw('LOWER(mf.company) asc')
//         ->paginate(20)
//         ->appends($request->query());

//     // 4) Overlay month-specific values (site-level) ONLY if a month is chosen
//     $textCols = ['payment','material','artwork','remarks','status'];
//     $dateCols = ['received_approval','sent_to_printer','collection_printer','installation','dismantle','next_follow_up'];
//     $validKeys = array_merge($textCols, $dateCols);

//     if ($month !== null) {
//         $pageItemIds = $records->getCollection()->pluck('outdoor_item_id')->unique()->all();

//         $monthlyRows = DB::table('outdoor_monthly_details')
//             ->select('outdoor_item_id','field_key','value_text','value_date')
//             ->where('year', $year)
//             ->where('month', $month)
//             ->whereIn('outdoor_item_id', $pageItemIds)        // ← fetch per site
//             ->get()
//             ->groupBy('outdoor_item_id');                     // ← key by site

//         $records->setCollection(
//             $records->getCollection()->map(function ($row) use ($monthlyRows, $validKeys, $dateCols) {
//                 // Provide masterFile object for Blade compatibility (location pulled from site)
//                 $row->masterFile = (object)[
//                     'id'               => $row->master_file_id,
//                     'company'          => $row->company,
//                     'client'           => $row->client,
//                     'product'          => $row->product,
//                     'product_category' => $row->product_category,
//                     'location'         => $row->site, // from outdoor_items
//                 ];

//                 $oid = $row->outdoor_item_id;
//                 if (!$monthlyRows->has($oid)) return $row;

//                 foreach ($monthlyRows->get($oid) as $md) {
//                     $key = strtolower((string)$md->field_key);
//                     if (!in_array($key, $validKeys, true)) continue;

//                     if (in_array($key, $dateCols, true)) {
//                         if (!empty($md->value_date)) {
//                             $row->{$key} = $md->value_date; // YYYY-MM-DD
//                         }
//                     } else {
//                         $val = trim((string)($md->value_text ?? ''));
//                         if ($val !== '') {
//                             $row->{$key} = $val;
//                         }
//                     }
//                 }
//                 return $row;
//             })
//         );
//     } else {
//         // All Months view: still set masterFile for Blade; no overlay
//         $records->setCollection(
//             $records->getCollection()->map(function ($row) {
//                 $row->masterFile = (object)[
//                     'id'               => $row->master_file_id,
//                     'company'          => $row->company,
//                     'client'           => $row->client,
//                     'product'          => $row->product,
//                     'product_category' => $row->product_category,
//                     'location'         => $row->site,
//                 ];
//                 return $row;
//             })
//         );
//     }

//     // 5) Month dropdown data
//     $months = [];
//     for ($i = 1; $i <= 12; $i++) {
//         $months[] = ['value' => $i, 'label' => \Carbon\Carbon::create()->month($i)->format('F')];
//     }

//     return view('coordinators.outdoor', [
//         'rows'          => $records,
//         'months'        => $months,
//         'month'         => $month,
//         'year'          => $year,
//         'selectedCount' => $selectedItemIds->count(),              // site count this month
//         'hasSelection'  => ($month !== null) && $selectedItemIds->isNotEmpty(),
//     ]);
// }

// public function index(Request $request)
// {
//     // --- Inputs ---
//     $rawMonth = $request->input('month', $request->input('outdoor_month'));
//     $rawYear  = $request->input('year',  $request->input('outdoor_year'));
//     $filterActive = (bool) $request->boolean('active'); // ← NEW: toggle

//     $normalize = function($raw): ?int {
//         if ($raw === null || $raw === '') return null;
//         $m = trim((string)$raw);
//         if (ctype_digit($m)) { $n = (int)$m; return ($n>=1 && $n<=12) ? $n : null; }
//         $map = [
//             'jan'=>1,'january'=>1,'feb'=>2,'february'=>2,'mar'=>3,'march'=>3,
//             'apr'=>4,'april'=>4,'may'=>5,'jun'=>6,'june'=>6,'jul'=>7,'july'=>7,
//             'aug'=>8,'august'=>8,'sep'=>9,'sept'=>9,'september'=>9,'oct'=>10,'october'=>10,
//             'nov'=>11,'november'=>11,'dec'=>12,'december'=>12
//         ];
//         $key = strtolower(preg_replace('/[^a-z]/i', '', $m));
//         return $map[$key] ?? null;
//     };

//     $month = $normalize($rawMonth);                 // null => All Months
//     $year  = (int) ($rawYear ?: now()->year);

//     // --- Kumpulkan OUTDOOR ITEM IDs yang aktif DI BULAN itu (union 2 sumber) ---
//     $activeItemIds = collect();

//     if ($month !== null) {
//         // a) Ada row di outdoor_coordinator_trackings untuk (year, month)
//         $idsFromOCT = DB::table('outdoor_coordinator_trackings')
//             ->where('year', $year)->where('month', $month)
//             ->whereNotNull('outdoor_item_id')
//             ->pluck('outdoor_item_id');

//         // b) Ada entri non-kosong di outdoor_monthly_details untuk (year, month)
//         $idsFromOMD = DB::table('outdoor_monthly_details')
//             ->where('year', $year)->where('month', $month)
//             ->whereNotNull('outdoor_item_id')
//             ->where(function ($q) {
//                 $q->whereNotNull('value_date')
//                   ->orWhere(function ($w) {
//                       $w->whereNotNull('value_text')
//                         ->whereRaw('TRIM(value_text) <> ""');
//                   });
//             })
//             ->pluck('outdoor_item_id');

//         $activeItemIds = $idsFromOCT->merge($idsFromOMD)->map(fn($v)=>(int)$v)->unique()->values();
//     }

//     // --- Base dataset: satu baris per site melalui outdoor_items ---
//     $base = DB::table('master_files as mf')
//         ->join('outdoor_items as oi', 'oi.master_file_id', '=', 'mf.id')
//         ->leftJoin('outdoor_coordinator_trackings as t', function ($j) use ($year, $month) {
//             $j->on('t.master_file_id', '=', 'mf.id')
//               ->on('t.outdoor_item_id', '=', 'oi.id');
//             if ($month !== null) {
//                 $j->where('t.year', '=', $year)
//                   ->where('t.month', '=', $month);
//             }
//         })
//         ->where(function ($q) {
//             $q->whereRaw('LOWER(mf.product) LIKE ?', ['%outdoor%'])
//               ->orWhereRaw('LOWER(mf.product_category) LIKE ?', ['%outdoor%'])
//               ->orWhereIn('mf.product_category', [
//                   'TB','BB','NEWSPAPER','BUNTING','FLYERS','STAR','SIGNAGES',
//                   'TB - Tempboard','BB - Billboard','Newspaper','Bunting','Flyers','Star','Signages'
//               ]);
//         });

//     // --- Gating OPSIONAL: jika pilih bulan & active=1 → hanya item yang aktif ---
//     if ($month !== null && $filterActive) {
//         // jika tidak ada yang aktif, buat kosong sekalian (agar tidak tampil semua)
//         if ($activeItemIds->isEmpty()) {
//             $base->whereRaw('1=0');
//         } else {
//             $base->whereIn('oi.id', $activeItemIds->all());
//         }
//     }

//     $records = $base->select([
//             't.id as id',
//             'mf.id as master_file_id',
//             'oi.id as outdoor_item_id',
//             'mf.company','mf.client','mf.product','mf.product_category',
//             DB::raw('oi.site as site'),
//             't.payment','t.material','t.artwork',
//             't.received_approval','t.sent_to_printer','t.collection_printer','t.installation',
//             't.dismantle','t.remarks','t.next_follow_up','t.status',
//             't.created_at as tracking_created_at',
//             'mf.company as company_snapshot',
//             'mf.product as product_snapshot',
//         ])
//         ->orderByRaw('LOWER(mf.company) asc')
//         ->paginate(20)
//         ->appends($request->query());

//     // --- Overlay dari monthly_details tetap sama seperti kodenya sekarang ---
//     $textCols = ['payment','material','artwork','remarks','status'];
//     $dateCols = ['received_approval','sent_to_printer','collection_printer','installation','dismantle','next_follow_up'];
//     $validKeys = array_merge($textCols, $dateCols);

//     if ($month !== null) {
//         $pageItemIds = $records->getCollection()->pluck('outdoor_item_id')->unique()->all();

//         $monthlyRows = DB::table('outdoor_monthly_details')
//             ->select('outdoor_item_id','field_key','value_text','value_date')
//             ->where('year', $year)->where('month', $month)
//             ->whereIn('outdoor_item_id', $pageItemIds)
//             ->get()->groupBy('outdoor_item_id');

//         $records->setCollection(
//             $records->getCollection()->map(function ($row) use ($monthlyRows, $validKeys, $dateCols) {
//                 $row->masterFile = (object)[
//                     'id'               => $row->master_file_id,
//                     'company'          => $row->company,
//                     'client'           => $row->client,
//                     'product'          => $row->product,
//                     'product_category' => $row->product_category,
//                     'location'         => $row->site,
//                 ];
//                 $oid = $row->outdoor_item_id;
//                 if (!$monthlyRows->has($oid)) return $row;
//                 foreach ($monthlyRows->get($oid) as $md) {
//                     $key = strtolower((string)$md->field_key);
//                     if (!in_array($key, $validKeys, true)) continue;
//                     if (in_array($key, $dateCols, true)) {
//                         if (!empty($md->value_date)) $row->{$key} = $md->value_date;
//                     } else {
//                         $val = trim((string)($md->value_text ?? ''));
//                         if ($val !== '') $row->{$key} = $val;
//                     }
//                 }
//                 return $row;
//             })
//         );
//     } else {
//         $records->setCollection(
//             $records->getCollection()->map(function ($row) {
//                 $row->masterFile = (object)[
//                     'id'               => $row->master_file_id,
//                     'company'          => $row->company,
//                     'client'           => $row->client,
//                     'product'          => $row->product,
//                     'product_category' => $row->product_category,
//                     'location'         => $row->site,
//                 ];
//                 return $row;
//             })
//         );
//     }

//     // --- Month dropdown ---
//     $months = [];
//     for ($i=1;$i<=12;$i++) {
//         $months[] = ['value'=>$i,'label'=>\Carbon\Carbon::create()->month($i)->format('F')];
//     }

//     return view('coordinators.outdoor', [
//         'rows'          => $records,
//         'months'        => $months,
//         'month'         => $month,
//         'year'          => $year,
//         'selectedCount' => $activeItemIds->count(), // now = count aktif bulan tsb
//         'hasSelection'  => ($month !== null) && $activeItemIds->isNotEmpty(),
//         'active'        => $filterActive,          // ← NEW: pass to Blade
//     ]);
// }

// public function index(Request $request)
// {
//     // --- Inputs ---
//     $rawMonth = $request->input('month', $request->input('outdoor_month'));
//     $rawYear  = $request->input('year',  $request->input('outdoor_year'));
//     $filterActive = (bool) $request->boolean('active');

//     $normalize = function($raw): ?int {
//         if ($raw === null || $raw === '') return null;
//         $m = trim((string)$raw);
//         if (ctype_digit($m)) { $n = (int)$m; return ($n>=1 && $n<=12) ? $n : null; }
//         $map = [
//             'jan'=>1,'january'=>1,'feb'=>2,'february'=>2,'mar'=>3,'march'=>3,
//             'apr'=>4,'april'=>4,'may'=>5,'jun'=>6,'june'=>6,'jul'=>7,'july'=>7,
//             'aug'=>8,'august'=>8,'sep'=>9,'sept'=>9,'september'=>9,'oct'=>10,'october'=>10,
//             'nov'=>11,'november'=>11,'dec'=>12,'december'=>12
//         ];
//         $key = strtolower(preg_replace('/[^a-z]/i', '', $m));
//         return $map[$key] ?? null;
//     };

//     $month = $normalize($rawMonth);
//     $year  = (int) ($rawYear ?: now()->year);

//     // If month is "All Months", ignore the active-only filter
//     if ($month === null) {
//         $filterActive = false;
//     }

//     // --- Kumpulkan MASTER FILE IDs yang aktif (ganti dari outdoor_item_id ke master_file_id) ---
//     $activeMasterIds = collect();

//     if ($month !== null && $filterActive) {
//         // Cari master_file_id yang punya tracking di bulan itu
//         $activeMasterIds = DB::table('outdoor_coordinator_trackings')
//             ->where('year', $year)->where('month', $month)
//             ->whereNotNull('master_file_id')
//             ->pluck('master_file_id')
//             ->map(fn($v)=>(int)$v)->unique()->values();
//     }

//     // --- Base dataset: langsung dari master_files ---
//     $base = DB::table('master_files as mf')
//         ->leftJoin('outdoor_coordinator_trackings as t', function($j) use ($year, $month) {
//             $j->on('t.master_file_id', '=', 'mf.id');
//             // Jika ada month filter, tambahkan ke JOIN untuk ambil data bulan spesifik
//             if ($month !== null) {
//                 $j->where('t.year', '=', $year)
//                   ->where('t.month', '=', $month);
//             }
//         })
//         ->where(function ($q) {
//             $q->whereRaw('LOWER(mf.product) LIKE ?', ['%outdoor%'])
//               ->orWhereRaw('LOWER(mf.product_category) LIKE ?', ['%outdoor%'])
//               ->orWhereIn('mf.product_category', [
//                   'TB','BB','NEWSPAPER','BUNTING','FLYERS','STAR','SIGNAGES',
//                   'TB - Tempboard','BB - Billboard','Newspaper','Bunting','Flyers','Star','Signages'
//               ]);
//         });

//     // Filter active berdasarkan master_file_id
//     if ($month !== null && $filterActive) {
//         if ($activeMasterIds->isEmpty()) {
//             $base->whereRaw('1=0');
//         } else {
//             $base->whereIn('mf.id', $activeMasterIds->all());
//         }
//     }

//     $records = $base->select([
//         't.id as id',
//         'mf.id as master_file_id',
//         'null as outdoor_item_id',
//         'mf.company','mf.client','mf.product','mf.product_category',
//         'mf.location as site',
//         't.payment','t.material','t.artwork',
//         't.received_approval','t.sent_to_printer','t.collection_printer','t.installation',
//         't.dismantle','t.remarks','t.next_follow_up','t.status',
//         't.created_at as tracking_created_at',
//         'mf.company as company_snapshot',
//         'mf.product as product_snapshot',
//     ])
//     ->orderByRaw('LOWER(mf.company) asc')
//     ->paginate(20)
//     ->appends($request->query());

//     // --- Skip monthly overlay karena outdoor_monthly_details juga pakai outdoor_item_id ---
//     // Set masterFile object untuk Blade
//     $records->setCollection(
//         $records->getCollection()->map(function ($row) {
//             $row->masterFile = (object)[
//                 'id'               => $row->master_file_id,
//                 'company'          => $row->company,
//                 'client'           => $row->client,
//                 'product'          => $row->product,
//                 'product_category' => $row->product_category,
//                 'location'         => $row->site,
//             ];
//             return $row;
//         })
//     );

//     // --- Month dropdown ---
//     $months = [];
//     for ($i=1;$i<=12;$i++) {
//         $months[] = ['value'=>$i,'label'=>\Carbon\Carbon::create()->month($i)->format('F')];
//     }

//     return view('coordinators.outdoor', [
//         'rows'          => $records,
//         'months'        => $months,
//         'month'         => $month,
//         'year'          => $year,
//         'selectedCount' => $activeMasterIds->count(),
//         'hasSelection'  => ($month !== null) && $activeMasterIds->isNotEmpty(),
//         'active'        => $filterActive,
//     ]);
// }

public function index(Request $request)
{
    // -------- Inputs + normalization --------
    $rawMonth = $request->input('month', $request->input('outdoor_month')); // support both
    $rawYear  = $request->input('year',  $request->input('outdoor_year'));

    $normalizeMonth = function ($raw): ?int {
        if ($raw === null || $raw === '') return null; // All Months
        $m = strtolower(trim((string)$raw));
        if (ctype_digit($m)) { $n=(int)$m; return ($n>=1 && $n<=12) ? $n : null; }
        $map = [
            'jan'=>1,'january'=>1,'feb'=>2,'february'=>2,'mar'=>3,'march'=>3,
            'apr'=>4,'april'=>4,'may'=>5,'jun'=>6,'june'=>6,'jul'=>7,'july'=>7,
            'aug'=>8,'august'=>8,'sep'=>9,'sept'=>9,'september'=>9,'oct'=>10,'october'=>10,
            'nov'=>11,'november'=>11,'dec'=>12,'december'=>12,
        ];
        return $map[$m] ?? null;
    };

    $month      = $normalizeMonth($rawMonth);                 // 1..12 or null (All Months)
    $year       = is_numeric($rawYear) ? (int)$rawYear : (int) now()->year;
    $search     = trim((string) $request->get('search', ''));
    $activeOnly = $month !== null && $request->boolean('active'); // ignore toggle when All Months

    // -------- Base set: all Outdoor sites (mf JOIN oi) --------
    $q = DB::table('master_files as mf')
        ->leftJoin('outdoor_items as oi', 'oi.master_file_id', '=', 'mf.id')
        ->where(function ($w) {
            $w->whereRaw('LOWER(mf.product_category) REGEXP ?', ['(^|[^a-z])(outdoor|billboard)([^a-z]|$)'])
              ->orWhereRaw('LOWER(mf.product) REGEXP ?',          ['(^|[^a-z])(outdoor|billboard)([^a-z]|$)']);
        });

    // -------- Search --------
    if ($search !== '') {
        $like = '%' . strtolower($search) . '%';
        $q->where(function ($w) use ($like) {
            $w->whereRaw('LOWER(mf.company) LIKE ?', [$like])
              ->orWhereRaw('LOWER(mf.product) LIKE ?', [$like])
              ->orWhereRaw('LOWER(oi.site) LIKE ?', [$like])
              ->orWhereRaw('LOWER(oi.district_council) LIKE ?', [$like])
              ->orWhereRaw('LOWER(oi.coordinates) LIKE ?', [$like]);
        });
    }

    // -------- Month-aware LEFT JOIN to OMD (pivoted per item) --------
    if ($month !== null) {
        // Subquery: pivot OMD ke kolom2 per item untuk year+month yang dipilih
        $omd = DB::table('outdoor_monthly_details as md')
            ->select([
                'md.outdoor_item_id',
                DB::raw("MAX(CASE WHEN md.field_key='status'        AND md.field_type='text' THEN md.value_text END)         AS status"),
                DB::raw("MAX(CASE WHEN md.field_key='remarks'       AND md.field_type='text' THEN md.value_text END)         AS remarks"),
                DB::raw("MAX(CASE WHEN md.field_key='payment'       AND md.field_type='text' THEN md.value_text END)         AS payment"),
                DB::raw("MAX(CASE WHEN md.field_key='material'      AND md.field_type='text' THEN md.value_text END)         AS material"),
                DB::raw("MAX(CASE WHEN md.field_key='artwork'       AND md.field_type='text' THEN md.value_text END)         AS artwork"),
                DB::raw("MAX(CASE WHEN md.field_key='received_approval' AND md.field_type='date' THEN md.value_date END)     AS received_approval"),
                DB::raw("MAX(CASE WHEN md.field_key='sent_to_printer'  AND md.field_type='date' THEN md.value_date END)      AS sent_to_printer"),
                DB::raw("MAX(CASE WHEN md.field_key='collection_printer' AND md.field_type='date' THEN md.value_date END)    AS collection_printer"),
                DB::raw("MAX(CASE WHEN md.field_key='installation'  AND md.field_type='date' THEN md.value_date END)         AS installation"),
                DB::raw("MAX(CASE WHEN md.field_key='dismantle'     AND md.field_type='date' THEN md.value_date END)         AS dismantle"),
                DB::raw("MAX(CASE WHEN md.field_key='next_follow_up'AND md.field_type='date' THEN md.value_date END)         AS next_follow_up"),
                // simpan 1 id md sebagai penanda ada data (untuk Active/UPDATE path)
                DB::raw("MAX(md.id) AS tracking_id")
            ])
            ->where('md.year', $year)
            ->where('md.month', $month)
            ->groupBy('md.outdoor_item_id');

        $q->leftJoinSub($omd, 'md', 'md.outdoor_item_id', '=', 'oi.id');

        // STRICT Active: hanya yang punya record OMD bulan tsb
        if ($activeOnly) {
            $q->whereNotNull('md.tracking_id');
        }
    }

    // -------- Selects --------
    $q->select([
        'mf.id as master_file_id',
        'mf.company as company',
        'mf.product as product',
        'mf.product_category as product_category',
        DB::raw('oi.id as outdoor_item_id'),
        DB::raw('oi.site as site'),
        DB::raw('oi.district_council as district_council'),
        DB::raw('oi.coordinates as coordinates'),
        DB::raw('oi.size as size'),

        // kolom-kolom bulan (NULL kalau tidak ada OMD di bulan tsb)
        DB::raw(($month !== null) ? 'md.status'              : 'NULL as status'),
        DB::raw(($month !== null) ? 'md.remarks'             : 'NULL as remarks'),
        DB::raw(($month !== null) ? 'md.payment'             : 'NULL as payment'),
        DB::raw(($month !== null) ? 'md.material'            : 'NULL as material'),
        DB::raw(($month !== null) ? 'md.artwork'             : 'NULL as artwork'),
        DB::raw(($month !== null) ? 'md.received_approval'   : 'NULL as received_approval'),
        DB::raw(($month !== null) ? 'md.sent_to_printer'     : 'NULL as sent_to_printer'),
        DB::raw(($month !== null) ? 'md.collection_printer'  : 'NULL as collection_printer'),
        DB::raw(($month !== null) ? 'md.installation'        : 'NULL as installation'),
        DB::raw(($month !== null) ? 'md.dismantle'           : 'NULL as dismantle'),
        DB::raw(($month !== null) ? 'md.next_follow_up'      : 'NULL as next_follow_up'),
        DB::raw(($month !== null) ? 'md.tracking_id'         : 'NULL as tracking_id'),
    ]);

    $q->orderBy('mf.company')->orderBy('oi.site');

    // -------- Paginate + page correction --------
    $rows = $q->paginate(50)->withQueryString();
    if ($rows->isEmpty() && $rows->currentPage() > 1) {
        return redirect()->to(url()->current() . '?' . http_build_query($request->except('page') + ['page' => 1]));
    }

    // -------- Month list for the dropdown --------
    $monthLabels = [1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',
                    7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December'];
    $months = collect(range(1,12))->map(fn($m)=>['value'=>$m,'label'=>$monthLabels[$m]]);

    return view('coordinators.outdoor', [
        'rows'   => $rows,
        'year'   => $year,
        'month'  => $month,       // null = All Months
        'active' => $activeOnly,
        'search' => $search,
        'months' => $months,
    ]);
}


    /**
     * 🔥 UPDATED: AJAX Update Field for Inline Editing - Enhanced version
     */
    public function updateField(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|exists:outdoor_coordinator_trackings,id',
                'field' => 'required|string',
                'value' => 'nullable|string'
            ]);

            $job = OutdoorCoordinatorTracking::with('masterFile')->findOrFail($validated['id']);
            $field = $validated['field'];
            $value = $validated['value'];

            // Handle month checkboxes that need to go to master_files table
            $masterFields = [
                'check_jan', 'check_feb', 'check_mar', 'check_apr',
                'check_may', 'check_jun', 'check_jul', 'check_aug',
                'check_sep', 'check_oct', 'check_nov', 'check_dec',
            ];

            if (in_array($field, $masterFields)) {
                if ($job->masterFile) {
                    // Convert value to boolean for checkboxes
                    $boolValue = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
                    $job->masterFile->{$field} = $boolValue;
                    $job->masterFile->save();
                    return response()->json(['success' => true, 'value' => $boolValue]);
                } else {
                    return response()->json(['error' => 'No master file found.'], 404);
                }
            }

            // Fields that go to outdoor_coordinator_trackings table
            $allowedFields = [
                'site', 'payment', 'material', 'artwork', 'received_approval',
                'sent_to_printer', 'collection_printer', 'installation',
                'dismantle', 'remarks', 'next_follow_up', 'status'
            ];

            if (!in_array($field, $allowedFields)) {
                return response()->json(['error' => 'Field not allowed for editing.'], 400);
            }

            // Handle date fields
            $dateFields = [
                'received_approval', 'sent_to_printer', 'collection_printer',
                'installation', 'dismantle', 'next_follow_up'
            ];

            if (in_array($field, $dateFields) && !empty($value)) {
                // Validate date format
                $date = \DateTime::createFromFormat('Y-m-d', $value);
                if (!$date || $date->format('Y-m-d') !== $value) {
                    return response()->json(['error' => 'Invalid date format. Use YYYY-MM-DD.'], 400);
                }
            }

            // Handle status field validation
            if ($field === 'status' && !in_array($value, ['pending', 'ongoing', 'completed', null])) {
                return response()->json(['error' => 'Invalid status value.'], 400);
            }

            // Update the field
            $job->{$field} = $value;
            $job->save();

            // Auto-update status based on progress if we're not directly updating status
            if ($field !== 'status') {
                $newStatus = $this->calculateStatus($job);
                if ($newStatus !== $job->status) {
                    $job->status = $newStatus;
                    $job->save();
                }
            }

            return response()->json([
                'success' => true,
                'value' => $value,
                'status' => $job->status // Return updated status
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            Log::error('updateField error', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Server error occurred.'], 500);
        }
    }

    /**
     * Calculate status based on progress
     */
    private function calculateStatus($job)
    {
        if (!empty($job->dismantle)) {
            return 'completed';
        } elseif (!empty($job->installation)) {
            return 'ongoing';
        } else {
            return 'pending';
        }
    }

    public function syncWithMasterFiles()
    {
        // Ambil semua master file dengan kategori Outdoor
        $masterFiles = MasterFile::where('product_category', 'Outdoor')->get();

        $synced = 0;

        foreach ($masterFiles as $mf) {
            // Cek kalau belum ada tracking-nya
            $exists = OutdoorCoordinatorTracking::where('master_file_id', $mf->id)->exists();
            if (!$exists) {
                OutdoorCoordinatorTracking::create([
                    'master_file_id' => $mf->id,
                    'status'         => 'pending',
                    // kolom lainnya bisa default null
                ]);
                $synced++;
            }
        }

        return redirect()->back()->with('success', "$synced outdoor data synced successfully.");
    }

    /**
     * 🔥 NEW: Get Dynamic Years for Filter Dropdown
     */
    public function getAvailableYears()
    {
        $years = OutdoorCoordinatorTracking::selectRaw('DISTINCT YEAR(created_at) as year')
            ->whereNotNull('created_at')
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        // Add current year if not in list
        $currentYear = now()->year;
        if (!in_array($currentYear, $years)) {
            $years[] = $currentYear;
            rsort($years); // Sort descending
        }

        return $years;
    }

    public function syncFromMasterFile()
    {
        $outdoor = MasterFile::where(function($query) {
            $query->whereIn('product_category', ['HM', 'TB', 'TTM', 'BB', 'Star', 'Flyers', 'Bunting', 'Signages', 'Newspaper'])
                  ->orWhere('product_category', 'LIKE', '%outdoor%')
                  ->orWhere('product_category', 'Outdoor');
        })->get();

        $createdCount = 0;
        $updatedCount = 0;

        foreach ($outdoor as $item) {
            $tracking = OutdoorCoordinatorTracking::updateOrCreate(
                ['master_file_id' => $item->id],
                [
                    'site' => $item->location,
                    'status' => 'pending',
                ]
            );

            if ($tracking->wasRecentlyCreated) {
                $createdCount++;
            } else {
                $updatedCount++;
            }
        }

        $message = "Sync completed! Created: {$createdCount}, Updated: {$updatedCount} records.";
        return redirect()->route('coordinator.outdoor.index')->with('success', $message);
    }

    public function show($id)
    {
        $tracking = OutdoorCoordinatorTracking::with('masterFile')->findOrFail($id);
        return view('coordinator.outdoor.show', compact('tracking'));
    }

    public function edit($id)
    {
        $tracking = OutdoorCoordinatorTracking::with('masterFile')->findOrFail($id);

        // Get all outdoor master files for the dropdown
        $masterFiles = MasterFile::where(function($query) {
            $query->whereIn('product_category', ['HM', 'TB', 'TTM', 'BB', 'Star', 'Flyers', 'Bunting', 'Signages', 'Newspaper'])
                  ->orWhere('product_category', 'LIKE', '%outdoor%')
                  ->orWhere('product_category', 'Outdoor');
        })
        ->orderBy('client')
        ->get();

        return view('coordinator.outdoor.edit', compact('tracking', 'masterFiles'));
    }

    public function destroy($id)
    {
        $tracking = OutdoorCoordinatorTracking::findOrFail($id);
        $tracking->delete();

        return redirect()->route('coordinator.outdoor.index')
                        ->with('success', 'Outdoor tracking record deleted successfully!');
    }

    /**
     * Manually seed/sync tracking records from master files
     */
    public function seedFromMasterFiles()
    {
        $createdCount = 0;

        // Get outdoor master files that don't have tracking records yet
        $outdoorFiles = MasterFile::where(function($query) {
            $query->whereIn('product_category', ['HM', 'TB', 'TTM', 'BB', 'Star', 'Flyers', 'Bunting', 'Signages', 'Newspaper'])
                  ->orWhere('product_category', 'LIKE', '%outdoor%')
                  ->orWhere('product_category', 'Outdoor');
        })
        ->whereNotExists(function($query) {
            $query->select(DB::raw(1))
                  ->from('outdoor_coordinator_trackings')
                  ->whereRaw('outdoor_coordinator_trackings.master_file_id = master_files.id');
        })
        ->get();

        foreach ($outdoorFiles as $file) {
            OutdoorCoordinatorTracking::create([
                'master_file_id' => $file->id,
                'status' => 'pending',
                'site' => $file->location ?? null,
                'payment' => null,
                'material' => null,
                'artwork' => null,
                'received_approval' => null,
                'sent_to_printer' => null,
                'collection_printer' => null,
                'installation' => null,
                'dismantle' => null,
                'remarks' => null,
                'next_follow_up' => null,
            ]);
            $createdCount++;
        }

        if ($createdCount > 0) {
            return redirect()->route('coordinator.outdoor.index')
                           ->with('success', "Created {$createdCount} tracking records from Master Files!");
        }

        return redirect()->route('coordinator.outdoor.index')
                       ->with('info', 'No new outdoor master files found to create tracking records.');
    }


public function upsert(Request $request)
{
    try {
        $data = $request->validate([
            'id'             => 'nullable|integer|exists:outdoor_coordinator_trackings,id',
            'master_file_id' => 'required_without:id|integer|exists:master_files,id',
            'field'          => 'required|string',
            'value'          => 'nullable|string',
        ]);

        // Allowed fields
        $allowedFields = [
            'site','payment','material','artwork','received_approval',
            'sent_to_printer','collection_printer','installation',
            'dismantle','remarks','next_follow_up','status',
            // Add month fields if you want those inline-editable too:
            'month_jan','month_feb','month_mar','month_apr','month_may',
            'month_jun','month_jul','month_aug','month_sep','month_oct','month_nov','month_dec',
        ];
        if (!in_array($data['field'], $allowedFields, true)) {
            return response()->json(['error' => 'Field not allowed'], 400);
        }

        // Find or create the row
        if (!empty($data['id'])) {
            $job = OutdoorCoordinatorTracking::findOrFail($data['id']);
        } else {
            $job = OutdoorCoordinatorTracking::firstOrCreate([
                'master_file_id' => $data['master_file_id'],
            ]);
        }

        // Normalize dates
        $dateFields = [
            'received_approval','sent_to_printer','collection_printer',
            'installation','dismantle','next_follow_up'
        ];
        $value = $data['value'];
        if (in_array($data['field'], $dateFields, true) && !empty($value)) {
            $value = date('Y-m-d', strtotime($value));
        }

        // Save
        $job->{$data['field']} = $value;
        $job->save();

        // Optional: auto status from progress (keep your existing logic if any)
        // if ($data['field'] !== 'status') { ... }

        return response()->json(['success' => true, 'id' => $job->id]);
    } catch (\Throwable $e) {
        Log::error('Outdoor upsert error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
        return response()->json(['error' => 'Server error'], 500);
    }
}


    public function getAvailableMasterFiles()
{
    $masterFiles = MasterFile::query()
        ->where(function ($q) {
            // case-insensitive "Outdoor"
            $q->whereRaw('LOWER(product_category) = ?', ['outdoor'])
              ->orWhereRaw('LOWER(product_category) LIKE ?', ['%outdoor%']);
        })
        ->whereNotExists(function ($query) {
            $query->select(DB::raw(1))
                  ->from('outdoor_coordinator_trackings')
                  ->whereRaw('outdoor_coordinator_trackings.master_file_id = master_files.id');
        })
        // master_files has "company", not "client" → alias it for the UI
        ->select([
            'id',
            DB::raw('company as client'),
            'product',
            'product_category',
            // add more fields if they actually exist in master_files
            // e.g. 'location' only if that column is present
        ])
        ->orderBy('client')
        ->get();

    return response()->json($masterFiles);
}


    public function export(Request $request): StreamedResponse
{
    // ---- DEBUG: Log untuk melihat apa yang terjadi ----
    Log::info('Export started', [
        'month_requested' => $request->integer('month'),
        'all_params' => $request->all()
    ]);

    // ---- 1) Get month filter ----
    $month = $request->integer('month'); // 1-12

    // ---- DEBUG: Cek total data di tabel ----
    $totalRows = DB::table('outdoor_coordinator_trackings')->count();
    Log::info("Total rows in outdoor_coordinator_trackings: {$totalRows}");

    // ---- 2) Build query dengan JOIN ke master_files ----
    $q = DB::table('outdoor_coordinator_trackings as oct')
        ->join('master_files as mf', 'oct.master_file_id', '=', 'mf.id')
        ->select([
            'oct.id',
            DB::raw('COALESCE(mf.company, mf.client) as company'),
            'mf.client',
            'mf.product',
            'oct.site',
            'oct.payment',
            'oct.material',
            'oct.artwork',
            'oct.received_approval',
            'oct.sent_to_printer',
            'oct.collection_printer',
            'oct.installation',
            'oct.dismantle',
        ]);

    // ---- 3) Month filtering ----
    if ($month) {
        Log::info("Filtering by month: {$month}");

        // Filter berdasarkan date fields yang ada di outdoor_coordinator_trackings
        $q->where(function ($query) use ($month) {
            $query->whereRaw("MONTH(oct.received_approval) = ?", [$month])
                  ->orWhereRaw("MONTH(oct.sent_to_printer) = ?", [$month])
                  ->orWhereRaw("MONTH(oct.collection_printer) = ?", [$month])
                  ->orWhereRaw("MONTH(oct.installation) = ?", [$month])
                  ->orWhereRaw("MONTH(oct.dismantle) = ?", [$month]);
        });

        // DEBUG: Cek berapa yang match filter
        $filteredCount = $q->count();
        Log::info("Rows matching month filter: {$filteredCount}");
    }

    $rows = $q->orderBy('oct.id')->get();

    // ---- DEBUG: Log hasil query ----
    Log::info("Final query returned rows: " . count($rows));
    if (count($rows) > 0) {
        Log::info("Sample first row: ", (array) $rows[0]);
    }

    // ---- 4) Jika tidak ada data dengan filter, export semua data ----
    if ($rows->isEmpty()) {
        Log::info("No filtered data found, exporting ALL data");
        $rows = DB::table('outdoor_coordinator_trackings as oct')
            ->join('master_files as mf', 'oct.master_file_id', '=', 'mf.id')
            ->select([
                'oct.id',
                DB::raw('COALESCE(mf.company, mf.client) as company'),
                'mf.client',
                'mf.product',
                'oct.site',
                'oct.payment',
                'oct.material',
                'oct.artwork',
                'oct.received_approval',
                'oct.sent_to_printer',
                'oct.collection_printer',
                'oct.installation',
                'oct.dismantle',
            ])
            ->orderBy('oct.id')
            ->get();

        Log::info("All data export returned rows: " . count($rows));
    }

    // ---- 5) Generate XLSX with enhanced header ----
    $monthName = $month ? "month-{$month}" : 'all';
    $filename = "outdoor-coordinator-{$monthName}.xlsx";

    // Headers in the exact order: ID | Company | Client | Product | Site | Payment | Material | Artwork | Approval | Sent | Collected | Install | Dismantle
    $csvHeaders = [
        'ID', 'Company', 'Client', 'Product', 'Site',
        'Payment', 'Material', 'Artwork', 'Approval',
        'Sent', 'Collected', 'Install', 'Dismantle'
    ];

    // Database columns matching the header order
    $dbColumns = [
        'id', 'company', 'client', 'product', 'site',
        'payment', 'material', 'artwork', 'received_approval',
        'sent_to_printer', 'collection_printer', 'installation', 'dismantle'
    ];

    // Generate month label for header
    $monthLabel = null;
    if ($month) {
        try {
            $monthLabel = Carbon::createFromDate(now()->year, $month, 1)->format('F Y');
        } catch (\Throwable $e) {
            $monthLabel = "Month {$month}";
        }
    }

    return response()->streamDownload(function () use ($rows, $csvHeaders, $dbColumns, $monthLabel) {
        $this->generateOutdoorCoordinatorXlsx($rows, $csvHeaders, $dbColumns, $monthLabel);
    }, $filename, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        'Cache-Control' => 'no-cache, no-store, max-age=0',
    ]);
}
    /**
     * Generate outdoor coordinator XLSX with proper formatting
     */
    private function generateOutdoorCoordinatorXlsx($rows, array $headers, array $dbColumns, ?string $monthLabel): void
    {
        // Create a new Spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $colCount = count($headers);
        $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colCount);

        // ====== TITLE SECTION ======
        $currentRow = 1;

        // Main title
        $sheet->setCellValue('A'.$currentRow, 'OUTDOOR COORDINATOR TRACKING REPORT');
        $sheet->mergeCells('A'.$currentRow.':'.$lastColumn.$currentRow);
        $currentRow++;

        // Generated timestamp
        $sheet->setCellValue('A'.$currentRow, 'Generated: ' . now()->format('Y-m-d H:i:s'));
        $sheet->mergeCells('A'.$currentRow.':'.$lastColumn.$currentRow);
        $currentRow++;

        // Month filter
        if ($monthLabel) {
            $sheet->setCellValue('A'.$currentRow, 'Month Filter: ' . $monthLabel);
        } else {
            $sheet->setCellValue('A'.$currentRow, 'Month Filter: All Data');
        }
        $sheet->mergeCells('A'.$currentRow.':'.$lastColumn.$currentRow);
        $currentRow++;

        // Total records
        $sheet->setCellValue('A'.$currentRow, 'Total Records: ' . count($rows));
        $sheet->mergeCells('A'.$currentRow.':'.$lastColumn.$currentRow);
        $currentRow++;

        // ====== HEADERS ======
        $headerRow = $currentRow;
        foreach ($headers as $index => $header) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
            $sheet->setCellValue($col.$headerRow, $header);
        }
        $currentRow++;

        // ====== DATA ROWS ======
        foreach ($rows as $row) {
            foreach ($dbColumns as $index => $col) {
                $cellCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
                $value = $row->$col ?? '';

                // Format dates
                if (in_array($col, ['received_approval', 'sent_to_printer', 'collection_printer', 'installation', 'dismantle', 'next_follow_up'])) {
                    if ($value && !in_array($value, ['0000-00-00', '0000-00-00 00:00:00', '', null])) {
                        try {
                            $value = date('Y-m-d', strtotime($value));
                        } catch (Throwable $e) {
                            // Keep original
                        }
                    } else {
                        $value = '';
                    }
                }

                $sheet->setCellValue($cellCol.$currentRow, $value);
            }
            $currentRow++;
        }

        // ====== STYLING ======
        $dataRange = 'A1:'.$lastColumn.($currentRow-1);

        // Yellow background for title rows (1-4)
        $titleRange = 'A1:'.$lastColumn.'4';
        $sheet->getStyle($titleRange)->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFFFFF00'], // Yellow
            ],
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);

        // Yellow background for header row
        $headerRange = 'A'.$headerRow.':'.$lastColumn.$headerRow;
        $sheet->getStyle($headerRange)->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFFFFF00'], // Yellow
            ],
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);

        // Borders for all data
        $sheet->getStyle($dataRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);

        // Auto-size columns
        foreach (range('A', $lastColumn) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // ====== OUTPUT ======
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
    }
}
