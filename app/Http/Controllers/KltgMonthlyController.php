<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\KltgMonthlyDetail;
use App\Models\MasterFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Schema; // 🔧 NEW: Added Schema facade
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\KltgMatrixExport;

use App\Exports\KltgMonthlyExport;

class KltgMonthlyController extends Controller
{
    public function upsert(Request $req)
{
    $data = $req->validate([
        'master_file_id' => 'required|integer|exists:master_files,id',
        'year'           => 'required|integer|min:2000|max:2100',
        'month'          => 'required|integer|min:1|max:12',
        // Subcategory tab (must be one of these)
        'category'       => 'required|string|in:KLTG,VIDEO,ARTICLE,LB,EM',
        // Which logical field is being saved
        'type'           => 'required|string|in:PUBLICATION,EDITION,STATUS,START,END',
        // Value kind
        'field_type'     => 'nullable|string|in:text,date',
        // Unified input; server will split into value_text/value_date
        'value'          => 'nullable|string',
    ]);

    // Normalize
    $data['category'] = strtoupper($data['category']);
    $data['type']     = strtoupper($data['type']);

    // Infer field_type if not provided (fallback to text)
    if (empty($data['field_type'])) {
        // Dates only when type is START or END
        $data['field_type'] = in_array($data['type'], ['START','END']) ? 'date' : 'text';
    }

    if ($data['field_type'] === 'date' && !empty($data['value']) &&
        !preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['value'])) {
        return response()->json(['message' => 'Invalid date format. Use YYYY-MM-DD.'], 422);
    }

    // Composite key MUST include "type" so START and END are separate rows
    $key = [
        'master_file_id' => $data['master_file_id'],
        'year'           => $data['year'],
        'month'          => $data['month'],
        'category'       => $data['category'],
        'type'           => $data['type'],       // <— important
    ];

    $attrs = [
        'field_type' => $data['field_type'],     // text | date
        'value'      => $data['value'],
        'value_text' => $data['field_type'] === 'text' ? ($data['value'] ?? null) : null,
        'value_date' => $data['field_type'] === 'date' ? ($data['value'] ?? null) : null,
        'is_date'    => $data['field_type'] === 'date' ? 1 : 0,
        'status'     => 'ACTIVE',
    ];

    $row = KltgMonthlyDetail::updateOrCreate($key, $attrs);

    return response()->json([
        'ok'  => true,
        'id'  => $row->id,
        'key' => $key,
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
            ];
        }

        return $detailMap;
    }

    public function index(Request $request)
{
    // 1) Resolve active year (int). If you later support "All Years", just don't filter.
    $activeYear = (int) $request->input('year', now()->year);

    // 2) Base rows: only KLTG product_category, newest first with stable tiebreaker
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
        ->where('product_category', 'KLTG')
        ->latest('created_at')
        ->orderByDesc('id')
        ->get();

    $masterIds = $baseRows->pluck('id')->all();

    // 3) Pull details for those master IDs
    $detailQ = KltgMonthlyDetail::whereIn('master_file_id', $masterIds);
    // If you later have an "All Years" option, only apply this where when a year is chosen.
    $detailQ->where('year', $activeYear);

    $details = $detailQ->get();

    // 4) Build a lookup map: mf|year|month|category|type  -> detail row
    $map = [];
    foreach ($details as $d) {
        $k = "{$d->master_file_id}|{$d->year}|{$d->month}|{$d->category}|{$d->type}";
        $map[$k] = $d; // unique ensures one row per key; latest write wins
    }

    // 5) Categories present in the grid
    $categories = ['KLTG', 'VIDEO', 'ARTICLE', 'LB', 'EM'];

    // 6) Shape rows for the Blade
    $rows = $baseRows->map(function ($mf) use ($map, $categories, $activeYear) {
        // Publication & Edition live at month=1, category=KLTG, types PUBLICATION/EDITION
        $pubKey = "{$mf->id}|{$activeYear}|1|KLTG|PUBLICATION";
        $ediKey = "{$mf->id}|{$activeYear}|1|KLTG|EDITION";

        $publication = isset($map[$pubKey]) ? ($map[$pubKey]->value_text ?? '') : '';
        $edition     = isset($map[$ediKey]) ? ($map[$ediKey]->value_text ?? '') : '';

        // Build 12 x categories grid for status/start/end
        $grid = [];
        for ($m = 1; $m <= 12; $m++) {
            foreach ($categories as $cat) {
                $statusKey = "{$mf->id}|{$activeYear}|{$m}|{$cat}|STATUS";
                $startKey  = "{$mf->id}|{$activeYear}|{$m}|{$cat}|START";
                $endKey    = "{$mf->id}|{$activeYear}|{$m}|{$cat}|END";

                $gridKey = sprintf('%02d_%s', $m, $cat);
                $grid[$gridKey] = [
                    'status' => isset($map[$statusKey]) ? ($map[$statusKey]->value_text ?? '') : '',
                    'start'=> isset($map[$startKey])  ? ($map[$startKey]->value_date ?? '') : '',
                    'end'  => isset($map[$endKey])    ? ($map[$endKey]->value_date ?? '') : '',
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
            'start'       => $mf->start_date ? Carbon::parse($mf->start_date)->format('d/m') : null,
            'end'         => $mf->end_date   ? Carbon::parse($mf->end_date)->format('d/m')   : null,
            'duration'    => $mf->duration_days,
            'publication' => $publication,   // used only if your Blade still reads these
            'edition'     => $edition,       // (you're now binding via map in the inputs)
            'grid'        => $grid,
        ];
    })->values();

    // 7) Filters for the UI
    $companies = MasterFile::whereNotNull('company')->distinct()->orderBy('company')->pluck('company');
    $products  = MasterFile::whereNotNull('product')->distinct()->orderBy('product')->pluck('product');
    $statuses  = collect(['Pending', 'Ongoing', 'Completed']);

    // 8) Send both the rows AND the map so the Blade can rehydrate inputs
    return view('dashboard.kltg', [
        'year'        => $activeYear,
        'activeYear'  => $activeYear,
        'rows'        => $rows,
        'categories'  => $categories,
        'companies'   => $companies,
        'products'    => $products,
        'statuses'    => $statuses,
        'selected'    => ['status' => '', 'company' => '', 'product' => ''],
        'detailsMap'  => $map, // <— used in Blade to prefill Publication/Edition + month cells
    ]);
}

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
    // ===== 1) Filters =====
    $year     = (int)($req->input('year') ?: now()->year);
    $rawMonth = $req->input('month');         // "All Months" | "10" | "October"
    $search   = trim((string)$req->input('q'));
    $statusF  = $req->input('status');

    // ===== 2) Table autodetect (or hardcode yours here) =====
    $candidates = [
        'kltg_monthly_details',
        'kltg_monthly',
        'monthly_ongoing_jobs',
        'masterfile_monthly_details',
        'kltg_matrix',
        'kltg_details',
        'media_coordinator_trackings',  // fallback if you stored there
    ];
    $table = null;
    foreach ($candidates as $name) {
        if (Schema::hasTable($name)) { $table = $name; break; }
    }
    // If you know the exact name, just do: $table = '<<YOUR_REAL_TABLE>>';
    if (!$table) {
        // Helpful message for quick fix
        abort(500, 'Export error: could not find the monthly detail table. Set $table to your real table name (the one with columns: year, month, category, field_type, value, value_text, value_date, type, status).');
    }

    // ===== 3) Month helpers =====
    $idxToName = [
        1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',
        7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December'
    ];
    $nameToIdx = array_flip($idxToName);

    $normMonthName = null;
    if ($rawMonth && strcasecmp($rawMonth, 'All Months') !== 0) {
        if (is_numeric($rawMonth)) {
            $normMonthName = $idxToName[max(1,min(12,(int)$rawMonth))];
        } else {
            $key = ucfirst(strtolower($rawMonth));
            $normMonthName = $idxToName[$nameToIdx[$key] ?? 0] ?? null;
        }
    }

    // ===== 4) Query correct columns from the detected table =====
    // Your categories in DB are often UPPERCASE; normalize to target set.
    $targetCats = ['KLTG','VIDEO','ARTICLE','LB','EM'];

    $q = DB::table($table)
        ->select(['year','month','category','type','value_date','status','client','created_at'])
        ->where('year', $year)
        ->whereIn(DB::raw('UPPER(category)'), $targetCats);

    if ($normMonthName) {
        // Your month column may be stored as number ("10") or name ("October")
        // We’ll accept either: we compare both ways using OR.
        $q->where(function($w) use ($normMonthName, $nameToIdx) {
            $mi = $nameToIdx[$normMonthName] ?? null;
            $w->where('month', $normMonthName);
            if ($mi !== null) { $w->orWhere('month', (string)$mi); }
        });
    }

    if ($search !== '') {
        $q->where(function($w) use ($search) {
            $w->where('client', 'like', "%{$search}%")
              ->orWhere('category', 'like', "%{$search}%")
              ->orWhere('status', 'like', "%{$search}%")
              ->orWhere('type', 'like', "%{$search}%")
              ->orWhere('value_text', 'like', "%{$search}%")
              ->orWhere('value', 'like', "%{$search}%");
        });
    }

    if ($statusF) {
        $q->where('status', $statusF);
    }

    // Newest first so "first seen wins" while filling grid
    $rows = $q->orderByDesc('created_at')->get();

    // ===== 5) Build Month × Category grid =====
    $displayCats = ['KLTG','Video','Article','LB','EM'];  // final header labels
    $grid = [];
    foreach ($idxToName as $mn) {
        $grid[$mn] = [];
        foreach ($displayCats as $c) {
            $grid[$mn][$c] = ['status'=>null,'start'=>null,'end'=>null];
        }
    }

    // Helper: map DB category to display header (ARTICLE -> Article)
    $catDisplay = function($dbCat) {
        $u = strtoupper($dbCat);
        return match ($u) {
            'KLTG'   => 'KLTG',
            'VIDEO'  => 'Video',
            'ARTICLE'=> 'Article',
            'LB'     => 'LB',
            'EM'     => 'EM',
            default  => null,
        };
    };

    foreach ($rows as $r) {
        // Normalize month to display name
        $mn = is_numeric($r->month) ? ($idxToName[(int)$r->month] ?? null)
                                    : ( $idxToName[$nameToIdx[ucfirst(strtolower($r->month))] ?? 0] ?? null );
        if (!$mn) continue;

        $dispCat = $catDisplay($r->category);
        if (!$dispCat) continue;

        // STATUS: first non-null wins (rows sorted newest first)
        if (!empty($r->status) && empty($grid[$mn][$dispCat]['status'])) {
            $grid[$mn][$dispCat]['status'] = $r->status;
        }

        // Start / End via type + value_date (latest wins)
        $t = strtolower((string)$r->type);
        if ($t === 'start' && empty($grid[$mn][$dispCat]['start'])) {
            $grid[$mn][$dispCat]['start'] = $r->value_date ? substr($r->value_date, 0, 10) : null;
        }
        if ($t === 'end' && empty($grid[$mn][$dispCat]['end'])) {
            $grid[$mn][$dispCat]['end'] = $r->value_date ? substr($r->value_date, 0, 10) : null;
        }
    }

    // ===== 6) Pretty labels (keeps your emoji + enables color rules) =====
    $pretty = fn($s) => match ($s) {
        'Artwork'      => 'Artwork 🟨',
        'Installation' => 'Installation 🟥',
        'Renewal'      => 'Renewal 🟥',
        'Completed'    => 'Completed ✅',
        'In Progress'  => 'In Progress 🔵',
        'Hold'         => 'Hold 🟧',
        'Cancelled'    => 'Cancelled ⬜',
        default        => $s, // e.g., "ACTIVE" passes through (no color fill rule)
    };
    foreach ($grid as $mn => $catsRow) {
        foreach ($catsRow as $cat => $cell) {
            if (!empty($cell['status'])) {
                $grid[$mn][$cat]['status'] = $pretty($cell['status']);
            }
        }
    }

    // ===== 7) Export (v2 exporter you already have) =====
    return (new KltgMatrixExport($grid))->download();
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
