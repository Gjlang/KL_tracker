<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\KltgMonthlyDetail;
use App\Models\MasterFile;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

use App\Exports\KltgMatrixExport;


use App\Exports\KltgMonthlyExport;

class KltgMonthlyController extends Controller
{
   public function upsert(Request $req)
{
    $data = $req->validate([
        'master_file_id' => 'required|integer|exists:master_files,id',
        'year'           => 'required|integer|min:2000|max:2100',
        'month'          => 'required|integer|min:0|max:12',
        'category'       => 'required|string|in:KLTG,VIDEO,ARTICLE,LB,EM',
        'type'           => 'required|string|in:PUBLICATION,EDITION,STATUS,START,END',
        'field_type'     => 'nullable|string|in:text,date',
        'value'          => 'nullable|string',
        'color'          => ['nullable','string','max:9','regex:/^#?[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/'],
    ]);

    // Normalize
    $data['category'] = strtoupper($data['category']);
    $data['type']     = strtoupper($data['type']);

    // Infer field_type if not provided
    if (empty($data['field_type'])) {
        $data['field_type'] = in_array($data['type'], ['START','END']) ? 'date' : 'text';
    }

    // Validate/normalize value based on field_type
    $valueText = null;
    $valueDate = null;

    if ($data['field_type'] === 'date') {
        // allow empty (clears the date)
        if (!empty($data['value'])) {
            // Expect YYYY-MM-DD
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['value'])) {
                return response()->json(['message' => 'Invalid date format. Use YYYY-MM-DD.'], 422);
            }
            $valueDate = $data['value'];
        }
    } else { // text
        $valueText = $data['value'] ?? null;
    }

    // === Composite key MUST match DB unique index ===
    // Use ALL of: master_file_id, year, month, category, type, field_type
    $key = [
        'master_file_id' => (int) $data['master_file_id'],
        'year'           => (int) $data['year'],
        'month'          => (int) $data['month'],
        'category'       => $data['category'],
        'type'           => $data['type'],
        'field_type'     => $data['field_type'],  // important
    ];

    $attrs = [
        'value'      => $data['value'] ?? null,   // optional raw mirror
        'value_text' => $valueText,
        'value_date' => $valueDate,
        'is_date'    => $data['field_type'] === 'date' ? 1 : 0,
        'status'     => 'ACTIVE',
    ];

    if ($data['type'] === 'STATUS') {
    $attrs['status'] = $valueText; // simpan teks status
}

// Simpan color kalau dikirim
if (!empty($data['color'])) {
    $attrs['color'] = $data['color'];
}

    $row = KltgMonthlyDetail::updateOrCreate($key, $attrs);

    return response()->json([
        'ok'   => true,
        'id'   => $row->id,
        'item' => $row->only([
            'master_file_id','year','month','category','type','field_type','value_text','value_date','is_date','status','color'
        ]),
    ]);
}


    public static function getDetailMap($masterFileIds, $year)
    {
        $details = KltgMonthlyDetail::whereIn('master_file_id', $masterFileIds)
            ->where('year', $year)
            ->get();

        $detailMap = [];
        foreach ($details as $detail) {
            $mfId = $detail->master_file_id;
            $month = (int)$detail->month;
            $category = strtoupper($detail->category);
            $fieldType = $detail->field_type;

            $detailMap[$mfId][$month][$category][$fieldType] = [
                'text' => $detail->value_text,
                'date' => $detail->value_date,
                'value' => $fieldType === 'date' ? $detail->value_date : $detail->value_text,
                'id' => $detail->id,
                'color' => (strtoupper($detail->type) === 'STATUS') ? ($detail->color ?? null) : null,
            ];
        }

        return $detailMap;
    }


public function index(Request $request)
{
     $activeYear = (int) ($request->input('year')
        ?? session('kltg.activeYear')
        ?? now()->year);
    session(['kltg.activeYear' => $activeYear]);


$yearStart = Carbon::create($activeYear, 1, 1)->startOfDay();
$yearEnd   = Carbon::create($activeYear, 12, 31)->endOfDay();

// 0a) master_file_id that have REAL content in details for this year (as before)
$idsWithContent = DB::table('kltg_monthly_details')
    ->where('year', $activeYear)
    ->whereBetween('month', [1, 12])
    ->where(function ($q) {
        $q->where(function ($q2) {
              $q2->whereIn('type', ['START','END','DATE'])
                 ->whereNotNull('value_date');
          })
          ->orWhere(function ($q2) {
              $q2->where('type', 'STATUS')
                 ->where(function ($q3) {
                     $q3->whereNotNull('value')->where('value','!=','')
                        ->orWhereNotNull('value_text')->where('value_text','!=','');
                 });
          });
    })
    ->distinct()
    ->pluck('master_file_id');

// 0b) master_file_id whose (date .. date_finish) OVERLAPS this year
$idsByOverlap = MasterFile::query()
    ->whereRaw("UPPER(TRIM(COALESCE(product_category, ''))) = 'KLTG'")
    ->whereNotNull('date')
    ->whereNotNull('date_finish')
    // overlap test: start <= yearEnd AND finish >= yearStart
    ->whereDate('date', '<=', $yearEnd)
    ->whereDate('date_finish', '>=', $yearStart)
    ->pluck('id');

// UNION both
$masterIdsForYear = $idsWithContent->merge($idsByOverlap)->unique()->values();

// If none, keep empty (or show Clone button)
if ($masterIdsForYear->isEmpty()) {
    $baseRows = collect();
} else {
    $baseRows = MasterFile::query()
        ->select([
            'id','company','product',
            DB::raw('COALESCE(product_category, "") as product_category'),
            'month as month_name',
            'date as start_date',
            'date_finish as end_date',
            DB::raw('CASE WHEN date IS NOT NULL AND date_finish IS NOT NULL
                      THEN DATEDIFF(date_finish, date) + 1 ELSE 0 END as duration_days'),
            'created_at',
        ])
        ->whereIn('id', $masterIdsForYear)
        ->whereRaw("UPPER(TRIM(COALESCE(product_category, ''))) = 'KLTG'")
        ->latest('created_at')
        ->orderByDesc('id')
        ->get();
}


    // Kalau belum ada data (belum clone), biar kosong saja
    if ($masterIdsForYear->isEmpty()) {
        $baseRows = collect();
    } else {
        // 1) Base rows HANYA untuk id yang ada di tahun aktif
        $baseRows = MasterFile::query()
            ->select([
                'id',
                'company',
                'product',
                DB::raw('COALESCE(product_category, "") as product_category'),
                'month as month_name',
                'date as start_date',
                'date_finish as end_date',
                DB::raw('CASE WHEN date IS NOT NULL AND date_finish IS NOT NULL
                          THEN DATEDIFF(date_finish, date) + 1 ELSE 0 END as duration_days'),
                'created_at',
            ])
            ->whereIn('id', $masterIdsForYear)
            ->whereRaw("UPPER(TRIM(COALESCE(product_category, ''))) = 'KLTG'")
            ->latest('created_at')
            ->orderByDesc('id')
            ->get();
    }

    // 2) Details untuk tahun aktif & master ids tersebut
    $masterIds = $baseRows->pluck('id')->all();
    $details = $masterIds
        ? KltgMonthlyDetail::whereIn('master_file_id', $masterIds)
            ->where('year', $activeYear)
            ->get([
                'id','master_file_id','year','month','category','type',
                'field_type','value','value_text','value_date','color','status'
            ])
        : collect();

    // 3) Build map: $map[mf][year][month][CATEGORY][TYPE] = ['text'=>..,'date'=>..,'value'=>..]
    $map = [];
    foreach ($details as $d) {
        $mf  = (int) $d->master_file_id;
        $yr  = (int) $d->year;
        $mo  = (int) $d->month;
        $cat = strtoupper((string) $d->category);
        $typ = strtoupper((string) $d->type);

        // ---- FIXED: More specific TYPE normalization ----
        // Only normalize EMPTY/NULL types, preserve explicit types like START/END
        if ($d->field_type === 'date') {
            if ($typ === '' || $typ === '0' || $typ === null || $typ === 'NULL') {
                $typ = 'DATE';   // generic date (fallback)
            }
            // Keep explicit START/END as is
        }

        if ($d->field_type === 'text') {
            if ($typ === '' || $typ === '0' || $typ === null ||
                in_array($typ, ['KLTG','VIDEO','ARTICLE','LB','EM'], true)) {
                $typ = 'STATUS'; // generic status label
            }
            // Keep explicit PUBLICATION/EDITION as is
        }

        if (!isset($map[$mf][$yr][$mo][$cat][$typ])) {
    $map[$mf][$yr][$mo][$cat][$typ] = ['value'=>null,'text'=>null,'date'=>null];
}

if (!empty($d->value)) {
    $map[$mf][$yr][$mo][$cat][$typ]['value'] = $d->value;
}
if (!empty($d->value_text)) {
    $map[$mf][$yr][$mo][$cat][$typ]['text'] = $d->value_text;
}
if (!empty($d->value_date)) {
    $dateValue = $d->value_date;
    if (strlen($dateValue) > 10) $dateValue = substr($dateValue, 0, 10);
    $map[$mf][$yr][$mo][$cat][$typ]['date'] = $dateValue;
}

if ($typ === 'STATUS' && !empty($d->color)) {
    $map[$mf][$yr][$mo][$cat][$typ]['color'] = $d->color;
}

if ($typ === 'STATUS' && empty($map[$mf][$yr][$mo][$cat][$typ]['text']) && !empty($d->status)) {
    $map[$mf][$yr][$mo][$cat][$typ]['text'] = $d->status;
}
    }

    $categories = ['KLTG','VIDEO','ARTICLE','LB','EM'];

    // 4) Shape rows for Blade
    $rows = $baseRows->map(function ($mf) use ($map, $categories, $activeYear) {
        // Publication & Edition live at month=0 under KLTG
        $pub  = $map[$mf->id][$activeYear][0]['KLTG']['PUBLICATION']['text']
            ?? $map[$mf->id][$activeYear][0]['KLTG']['PUBLICATION']['value']
            ?? '';
        $edit = $map[$mf->id][$activeYear][0]['KLTG']['EDITION']['text']
            ?? $map[$mf->id][$activeYear][0]['KLTG']['EDITION']['value']
            ?? '';

        $grid = [];
        for ($m = 1; $m <= 12; $m++) {
            foreach ($categories as $cat) {
                $gridKey = sprintf('%02d_%s', $m, $cat);

                $status = $map[$mf->id][$activeYear][$m][$cat]['STATUS']['text']
                       ?? $map[$mf->id][$activeYear][$m][$cat]['STATUS']['value']
                       ?? '';

                // FIXED: Check START first, then DATE fallback, with better error handling
                $start = '';
                if (isset($map[$mf->id][$activeYear][$m][$cat]['START']['date'])) {
                    $start = $map[$mf->id][$activeYear][$m][$cat]['START']['date'];
                } elseif (isset($map[$mf->id][$activeYear][$m][$cat]['DATE']['date'])) {
                    $start = $map[$mf->id][$activeYear][$m][$cat]['DATE']['date'];
                }

                $end = '';
                if (isset($map[$mf->id][$activeYear][$m][$cat]['END']['date'])) {
                    $end = $map[$mf->id][$activeYear][$m][$cat]['END']['date'];
                }
                $color = $map[$mf->id][$activeYear][$m][$cat]['STATUS']['color'] ?? '';


                $grid[$gridKey] = [
                    'status' => $status,
                    'start'  => $start,  // YYYY-MM-DD → renders in <input type="date">
                    'end'    => $end,
                    'color'  => $color,    // YYYY-MM-DD
                ];
            }
        }

        return [
            'id'          => $mf->id,
            'month_name'  => $mf->month_name ?? '',
            'created_at'  => optional($mf->created_at)->format('d/m/y'),
            'company'     => $mf->company,
            'product'     => $mf->product,
            'status'      => 'Pending',
            'start' => $mf->start_date ? Carbon::parse($mf->start_date)->format('Y-m-d') : null,
            'end'   => $mf->end_date   ? Carbon::parse($mf->end_date)->format('Y-m-d')   : null,
            'duration'    => $mf->duration_days,
            'publication' => $pub,
            'edition'     => $edit,
            'grid'        => $grid,
        ];
    })->values();

// 5) Filters + view
$companies = MasterFile::whereNotNull('company')->distinct()->orderBy('company')->pluck('company');
$products  = MasterFile::whereNotNull('product')->distinct()->orderBy('product')->pluck('product');
$statuses  = collect(['Pending','Ongoing','Completed']);

// Get companies from the actual filtered rows (KLTG data for this year)
$outdoorCompanies = $rows->pluck('company')
    ->filter()
    ->unique()
    ->sort()
    ->values();

return view('dashboard.kltg', [
    'year'        => $activeYear,
    'activeYear'  => $activeYear,
    'rows'        => $rows,
    'categories'  => $categories,
    'companies'   => $companies,
    'outdoorCompanies' => $outdoorCompanies,
    'products'    => $products,
    'statuses'    => $statuses,
    'selected'    => ['status' => '', 'company' => '', 'product' => ''],
    'detailsMap'  => $map,
    'hasAnyForYear' => $this->hasAnyForYear($activeYear),
    'bestSourceYear' => $this->findLatestSourceYear($activeYear),
]);
}

public function cloneYear(Request $request)
   {
       $targetYear = (int) $request->validate([
           'year' => 'required|integer|min:2000|max:2100',
       ])['year'];

       // If target already has data, do nothing (idempotent).
       if ($this->hasAnyForYear($targetYear)) {
           return redirect()->route('kltg.index', ['year' => $targetYear])
               ->with('status', "Year $targetYear already has rows.");
       }

       $sourceYear = $this->findLatestSourceYear($targetYear);
       if (!$sourceYear) {
           return redirect()->route('kltg.index', ['year' => $targetYear])
               ->with('status', "No earlier year found to clone from.");
       }

       // Pull distinct structures (keys), not values
       $keys = KltgMonthlyDetail::query()
           ->select([
               'master_file_id',
               'month',
               'category',
               'type',
               'field_type',
           ])
           ->where('year', $sourceYear)
           ->distinct()
           ->get();

       if ($keys->isEmpty()) {
           return redirect()->route('kltg.index', ['year' => $targetYear])
               ->with('status', "Source year $sourceYear has no rows to clone.");
       }

       // Insert rows for target year with NULL values
       $now = now();
       $payload = [];
       foreach ($keys as $k) {
           $payload[] = [
               'master_file_id' => (int) $k->master_file_id,
               'year'           => $targetYear,
               'month'          => (int) $k->month,               // 0..12 as you use today
               'category'       => strtoupper($k->category),      // KLTG/VIDEO/ARTICLE/LB/EM
               'type'           => strtoupper($k->type),          // PUBLICATION/EDITION/STATUS/START/END
               'field_type'     => $k->field_type ?: 'text',      // text|date
               'value'          => null,
               'value_text'     => null,
               'value_date'     => null,
               'is_date'        => $k->field_type === 'date' ? 1 : 0,
               'client'         => null,                          // optional columns left empty
               'status'         => null,
               'color'         => null,
               'created_at'     => $now,
               'updated_at'     => $now,
           ];
       }

       // Use upsert to avoid duplicates if the unique index already exists
       DB::table('kltg_monthly_details')->upsert(
           $payload,
           ['master_file_id','year','month','category','type','field_type'],
           ['value','value_text','value_date','is_date','client','status','color','updated_at']
       );

       return redirect()->route('kltg.index', ['year' => $targetYear])
           ->with('status', "Cloned structure from $sourceYear to $targetYear.");
   }

   /** True if the target year already has any KLTG rows */
   private function hasAnyForYear(int $year): bool
   {
       return DB::table('kltg_monthly_details')->where('year', $year)->exists();
   }

   /** Find the latest prior year (< target) with any rows; returns null if none */
   private function findLatestSourceYear(int $targetYear): ?int
   {
       $row = DB::table('kltg_monthly_details')
           ->select('year')
           ->where('year', '<', $targetYear)
           ->orderByDesc('year')
           ->limit(1)
           ->first();
       return $row?->year ?? null;
   }

   /** Build the exact same payload the Blade uses, honoring filters */



    /** Build the exact same payload the Blade uses, honoring filters */
    private function buildKltgPayload(Request $request): array
    {
        $activeYear = (int)($request->get('year') ?: now()->year);
        $month      = $request->get('month');     // "", "January", ...
        $status     = $request->get('status', '');
        $company    = $request->get('company', '');
        $product    = $request->get('product', '');

        $rows       = $this->getKltgRows($activeYear, $company, $product);
        $detailsMap = $this->getKltgDetailsMap($activeYear, $month, $status);

        // Optional: apply "header table" filters (status/company/product) to rows if your UI does
        if ($company) $rows = $rows->where('company', $company)->values();
        if ($product) $rows = $rows->where('product', $product)->values();

        return [
            'activeYear' => $activeYear,
            'rows'       => $rows,
            'detailsMap' => $detailsMap,
            // Keep the same fixed order & labels used on the page
            'subcats'    => ['KLTG','Video','Article','LB','EM'],
            'months'     => ['January','February','March','April','May','June','July','August','September','October','November','December'],
        ];
    }


    public function exportMatrix(Request $req)
{
    $baseYear = (int)($req->input('year') ?: now('Asia/Kuala_Lumpur')->year);

    // Remove 'color' from master_files query - it doesn't exist there
    $masters = DB::table('master_files')
        ->whereRaw("UPPER(TRIM(COALESCE(product_category, ''))) = 'KLTG'")
        ->get([
            'id','month','date','date_finish','company','product','status','created_at'
        ]);

    // Ambil semua details dengan color (color ada di tabel ini)
    $detailRows = DB::table('kltg_monthly_details')
        ->whereIn('master_file_id', $masters->pluck('id'))
        ->get([
            'id','master_file_id','year','month','category','type',
            'field_type','value','value_text','value_date','status','color'
        ]);

    $years = collect();
    $years = $years->merge($detailRows->pluck('year')->unique()->values());

    foreach ($masters as $m) {
        if ($m->date && $m->date_finish) {
            $startY = (int)Carbon::parse($m->date)->year;
            $endY   = (int)Carbon::parse($m->date_finish)->year;
            for ($y = $startY; $y <= $endY; $y++) $years->push($y);
        }
    }
    $years = $years->unique()->sort()->values();

    if ($years->isEmpty()) $years = collect([$baseYear]);

    $catLabels = ['KLTG','Video','Article','LB','EM'];
    $catKeys   = ['KLTG','VIDEO','ARTICLE','LB','EM'];
    $monthsMap = collect(range(1,12))
        ->mapWithKeys(fn($m)=>[$m=>Carbon::create()->month($m)->format('F')]);

    $makeEmptyMatrix = function() use($catKeys,$monthsMap){
        $m = [];
        foreach($monthsMap as $num=>$name){
            $m[$num] = [
                'monthName'=>$name,
                'cats'=>collect($catKeys)->mapWithKeys(
                    fn($k)=>[$k=>['status'=>null,'start'=>null,'end'=>null,'color'=>null]]
                )->all()
            ];
        }
        return $m;
    };

    $parseMonth = function($v){
        if(is_numeric($v)) return (int)$v;
        try { return (int)Carbon::parse("1 ".$v." 2000")->format('n'); }
        catch(\Throwable $e){ return null; }
    };

    $detailsByMaster = $detailRows->groupBy('master_file_id');
    $mastersById     = $masters->keyBy('id');

    $recordsByYear = [];
    foreach ($years as $year) {
        $yearStart = Carbon::create($year,1,1)->startOfDay();
        $yearEnd   = Carbon::create($year,12,31)->endOfDay();

        $records = [];
        $no = 1;

        foreach ($masters as $mrow) {
            $overlap = ($mrow->date && $mrow->date_finish)
                ? (Carbon::parse($mrow->date) <= $yearEnd && Carbon::parse($mrow->date_finish) >= $yearStart)
                : false;

            $rows = $detailsByMaster[$mrow->id] ?? collect();
            $matrix = $makeEmptyMatrix();
            $hasContent = false;

            foreach ($rows as $r) {
                if ((int)$r->year !== (int)$year) continue;

                $mn = $parseMonth($r->month);
                if (!$mn || $mn < 1 || $mn > 12 || !isset($matrix[$mn])) continue;

                $key = strtoupper((string)$r->category);
                if (!in_array($key, $catKeys, true)) continue;

                if ($r->field_type === 'text') {
                    $t = strtoupper((string)$r->type);
                    if ($t === '' || $t === 'STATUS' || in_array($t, $catKeys, true)) {
                        if (!empty($r->value_text)) {
                            $matrix[$mn]['cats'][$key]['status'] = $r->value_text;
                            $hasContent = true;
                        }
                    }
                }

                if (empty($matrix[$mn]['cats'][$key]['status']) && !empty($r->status)) {
                    $matrix[$mn]['cats'][$key]['status'] = $r->status;
                    $hasContent = true;
                }

                // Store color from details table
                if (!empty($r->color)) {
                    $matrix[$mn]['cats'][$key]['color'] = $r->color;
                }

                if ($r->field_type === 'date' && $r->value_date) {
                    $t = strtoupper((string)$r->type);
                    if ($t === 'START') $matrix[$mn]['cats'][$key]['start'] = substr($r->value_date,0,10);
                    if ($t === 'END')   $matrix[$mn]['cats'][$key]['end']   = substr($r->value_date,0,10);
                    $hasContent = true;
                }
            }

            if (!$overlap && !$hasContent) continue;

            $publication = optional($rows->first(function($x) use ($year){
                return (int)$x->year === (int)$year && $x->field_type==='text' && strtoupper((string)$x->type)==='PUBLICATION';
            }))->value_text;

            $edition = optional($rows->first(function($x) use ($year){
                return (int)$x->year === (int)$year && $x->field_type==='text' && strtoupper((string)$x->type)==='EDITION';
            }))->value_text;

            $summary = [
                'no'          => $no++,
                'month'       => $mrow->month,
                'created_at'  => $mrow->created_at ? Carbon::parse($mrow->created_at)->format('Y-m-d') : '',
                'company'     => $mrow->company,
                'product'     => $mrow->product,
                'publication' => $publication ?? '',
                'edition'     => $edition ?? '',
                'status'      => $mrow->status ?? '',
                'start'       => $mrow->date ? Carbon::parse($mrow->date)->format('Y-m-d') : '',
                'end'         => $mrow->date_finish ? Carbon::parse($mrow->date_finish)->format('Y-m-d') : '',
            ];

            $records[] = ['summary' => $summary, 'matrix' => array_values($matrix)];
        }

        $recordsByYear[$year] = $records;
    }

    $fileName = 'kltg_matrix_'.now('Asia/Kuala_Lumpur')->format('Ymd_His').'.xlsx';
    $export = new KltgMatrixExport([], $catLabels, $catKeys);
    return $export->downloadByYear($recordsByYear, $fileName);
}
    // ===== Helper stubs (mirror your index queries) =====
    private function getKltgRows(int $year, string $company = '', string $product = '')
    {
        $query = MasterFile::query()
            ->select([
                'id',
                'company',
                'product',
                DB::raw('COALESCE(product_category, "") as product_category'),
                'month as month_name',
                'date as start_date',
                'date_finish as end_date',
                DB::raw('CASE WHEN date IS NOT NULL AND date_finish IS NOT NULL
                          THEN DATEDIFF(date_finish, date) + 1 ELSE 0 END as duration_days'),
                'created_at',
            ])
            ->where('product_category', 'KLTG')
            ->when($company, fn($q) => $q->where('company', $company))
            ->when($product, fn($q) => $q->where('product', $product))
            ->latest('created_at')
            ->orderByDesc('id');

        return $query->get();
    }

    private function getKltgDetailsMap(int $year, ?string $monthFilter = '', string $statusFilter = '')
    {
        $details = KltgMonthlyDetail::where('year', $year)->get();

        $map = [];
        foreach ($details as $d) {
            $k = "{$d->master_file_id}|{$d->year}|{$d->month}|{$d->category}|{$d->type}";
            $map[$k] = $d;
        }

        return $map;
    }

    public static function getCellValue($detailMap, $masterFileId, $month, $category, $fieldType)
    {
        $category = strtoupper($category);
        $data = $detailMap[$masterFileId][$month][$category][$fieldType] ?? null;

        if (!$data) {
            return '';
        }

        return $fieldType === 'date' ? ($data['date'] ?? '') : ($data['text'] ?? '');
    }
}
