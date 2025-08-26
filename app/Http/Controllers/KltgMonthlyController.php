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
use PhpOffice\PhpSpreadsheet\Style\Fill;
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
    $year = (int)($req->input('year') ?: now('Asia/Kuala_Lumpur')->year);

    // 1. Ambil semua detail KLTG
    $detailRows = DB::table('kltg_monthly_details')
        ->where('year', $year)
        ->get();

    $mfIds = $detailRows->pluck('master_file_id')->unique()->values();

    // 2. Ambil master_files
    $masters = DB::table('master_files')
        ->whereIn('id', $mfIds)
        ->get([
            'id','month','date','date_finish',
            'company','product','status','created_at'
        ])->keyBy('id');

    // 3. Siapkan label kategori & bulan
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
                    fn($k)=>[$k=>['status'=>null,'start'=>null,'end'=>null]]
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

    // 4. Group detail per master_file_id
    $detailsByMaster = $detailRows->groupBy('master_file_id');
    $records = [];
    $no=1;

    foreach($detailsByMaster as $mfId=>$rows){
        $mrow = $masters->get($mfId);
        if(!$mrow) continue;

        // ambil publication & edition dari detail
        $publication = optional($rows->first(fn($x)=>$x->field_type==='text' && strtolower($x->type)==='publication'))->value_text;
        $edition     = optional($rows->first(fn($x)=>$x->field_type==='text' && strtolower($x->type)==='edition'))->value_text;

        // summary kiri
        $summary = [
            'no'          => $no++,
            'month'       => $mrow->month,
            'created_at'  => $mrow->created_at ? Carbon::parse($mrow->created_at)->format('Y-m-d') : '',
            'company'     => $mrow->company,
            'product'     => $mrow->product,
            'publication' => $publication ?? '',
            'edition'     => $edition ?? '',
            'status' => (function($rows){
                $txt = optional($rows->first(function($x){
                    return $x->field_type === 'text'
                        && (strtolower((string)$x->type) === 'status' || $x->type === null || $x->type === '');
                }))->value_text;
                if (!empty($txt)) return $txt;
                return optional($rows->first(fn($x)=>!empty($x->status)))->status ?? '';
            })($rows),
            'start'       => $mrow->date ?? null,
            'end'         => $mrow->date_finish ?? null,
        ];

        // matrix untuk master ini
        $matrix = $makeEmptyMatrix();
        foreach($rows as $r){
            $mn = $parseMonth($r->month);
            if(!$mn || !isset($matrix[$mn])) continue;
            $key = strtoupper((string)$r->category);
            if(!in_array($key,$catKeys,true)) continue;

            // 1) TEXT row from dropdown ⇒ value_text holds the label (Artwork / Installation / ...)
            if ($r->field_type === 'text') {
                $t = strtolower((string)$r->type);
                // treat empty / 'status' / even category names as the status label row
                if ($t === '' || $t === 'status' || in_array(strtoupper($r->type ?? ''), $catKeys, true)) {
                    if (!empty($r->value_text)) {
                        $matrix[$mn]['cats'][$key]['status'] = $r->value_text;
                    }
                }
            }
            // 2) Fallback to the legacy 'status' column only if we still don't have a label
            if (empty($matrix[$mn]['cats'][$key]['status']) && !empty($r->status)) {
                $matrix[$mn]['cats'][$key]['status'] = $r->status;
            }
            // 3) Dates stay the same
            if ($r->field_type === 'date' && $r->value_date) {
                $t = strtolower((string)$r->type);
                if ($t === 'start') $matrix[$mn]['cats'][$key]['start'] = $r->value_date;
                if ($t === 'end')   $matrix[$mn]['cats'][$key]['end']   = $r->value_date;
            }
        }

        $records[]=['summary'=>$summary,'matrix'=>array_values($matrix)];
    }


    // 5. Export
    $export = new KltgMatrixExport($records,$catLabels,$catKeys);
    $fileName = 'export_matrix_masterfile_'.now('Asia/Kuala_Lumpur')->format('Ymd').'.xlsx';
    return $export->download($fileName);
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
