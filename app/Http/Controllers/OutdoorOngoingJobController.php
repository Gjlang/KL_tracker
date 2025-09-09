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

private function ensureOutdoorSlots(array $masterIds, int $year): void
{
    if (empty($masterIds)) return;

    $now = now();
    $rows = [];
    foreach ($masterIds as $mfId) {
        for ($m=1; $m<=12; $m++) {
            foreach ([['status','text'], ['installed_on','date']] as [$key,$type]) {
                $rows[] = [
                    'master_file_id' => $mfId,
                    'year'           => $year,
                    'month'          => $m,
                    'field_key'      => $key,
                    'field_type'     => $type,
                    'value_text'     => null,
                    'value_date'     => null,
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ];
            }
        }
    }

    DB::table('outdoor_monthly_details')->upsert(
        $rows,
        ['master_file_id','year','month','field_key'],
        ['updated_at']
    );
}


   public function index(Request $request)
{
    // -------- Inputs --------
    $year = (string) $request->get('outdoor_year', now()->year);
    $search = trim((string) $request->get('search', ''));

    // -------- Base query: Outdoor-only + sites joined --------
    $q = DB::table('master_files as mf')
        ->leftJoin('outdoor_items as oi', 'oi.master_file_id', '=', 'mf.id')
        ->where(function ($w) {
            $w->whereRaw('LOWER(mf.product_category) LIKE ?', ['%outdoor%'])
              ->orWhereRaw('LOWER(mf.product) LIKE ?', ['%outdoor%']);
        });

    // -------- Search (company/product/site/coords/district) --------
    if ($search !== '') {
        $like = '%' . strtolower($search) . '%';
        $q->where(function ($w) use ($like) {
            $w->whereRaw('LOWER(mf.company) LIKE ?', [$like])
              ->orWhereRaw('LOWER(mf.product) LIKE ?', [$like])
              ->orWhereRaw('LOWER(oi.site) LIKE ?', [$like])
              ->orWhereRaw('LOWER(COALESCE(oi.coordinates,"")) LIKE ?', [$like])
              ->orWhereRaw('LOWER(COALESCE(oi.district_council,"")) LIKE ?', [$like]);
        });
    }

    // -------- Year filter (works for any year that exists) --------
    // A row qualifies if any of these dates fall within the chosen year
    if ($request->filled('outdoor_year')) {
        $y = (int) $year;
        $q->where(function ($w) use ($y) {
            $w->whereYear('mf.date', $y)
              ->orWhereYear('mf.date_finish', $y)
              ->orWhereYear('mf.created_at', $y);
        });
    }

    // -------- Select one row per master file + aggregated sites --------
    // -------- PER-SITE selection (no GROUP_CONCAT) --------
$rows = $q->select([
        'mf.id',                       // master_file_id
        'mf.company',
        'mf.product',
        'mf.product_category',
        'mf.date',
        'mf.date_finish',
        'mf.month',
        'mf.created_at',
        'oi.id  as outdoor_item_id',   // <— penting
        'oi.site',
        'oi.size',
        'oi.coordinates',
        'oi.district_council',
    ])
    ->orderBy('mf.company')
    ->orderBy('oi.site')
    ->get();


    // -------- Available years (union of all date sources across outdoor data) --------
    $years = DB::query()
        ->fromSub(function ($sub) {
            $sub->from('master_files as mf')
                ->leftJoin('outdoor_items as oi', 'oi.master_file_id', '=', 'mf.id')
                ->where(function ($w) {
                    $w->whereRaw('LOWER(mf.product_category) LIKE ?', ['%outdoor%'])
                      ->orWhereRaw('LOWER(mf.product) LIKE ?', ['%outdoor%']);
                })
                ->selectRaw('YEAR(mf.date) as y')->whereNotNull('mf.date')
                ->union(
                    DB::table('master_files as mf')
                        ->leftJoin('outdoor_items as oi', 'oi.master_file_id', '=', 'mf.id')
                        ->where(function ($w) {
                            $w->whereRaw('LOWER(mf.product_category) LIKE ?', ['%outdoor%'])
                              ->orWhereRaw('LOWER(mf.product) LIKE ?', ['%outdoor%']);
                        })
                        ->selectRaw('YEAR(mf.date_finish) as y')->whereNotNull('mf.date_finish')
                )
                ->union(
                    DB::table('master_files as mf')
                        ->leftJoin('outdoor_items as oi', 'oi.master_file_id', '=', 'mf.id')
                        ->where(function ($w) {
                            $w->whereRaw('LOWER(mf.product_category) LIKE ?', ['%outdoor%'])
                              ->orWhereRaw('LOWER(mf.product) LIKE ?', ['%outdoor%']);
                        })
                        ->selectRaw('YEAR(mf.created_at) as y')->whereNotNull('mf.created_at')
                );
        }, 'years_union')
        ->select('y')
        ->whereNotNull('y')
        ->distinct()
        ->orderBy('y', 'desc')
        ->pluck('y');

    $availableYears = $years;  // used by your <select>

    // -------- Existing monthly detail map (keyed by master_file_id:month:key) --------
    $details = DB::table('outdoor_monthly_details')
        ->where('year', (int) $year)
        ->whereIn('master_file_id', $rows->pluck('id'))
        ->get();

    $existing = $details->mapWithKeys(function ($r) {
        return [ $r->master_file_id . ':' . $r->month . ':' . $r->field_key => $r ];
    });

    return view('dashboard.outdoor', [
    'rows'           => $rows,
    'availableYears' => $availableYears,
    'year'           => (int) $year,
    'existing'       => $existing,
]);

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

    public function upsertMonthlyDetail(Request $req)
{
    $data = $req->validate([
        'master_file_id' => 'required|integer|exists:master_files,id',
        'year'           => 'required|integer|min:2000|max:2100',
        'month'          => 'required|integer|min:1|max:12',
        'field_key'      => 'required|string|in:remark,installed_on,material_received,approved_on,status',
        'field_type'     => 'required|string|in:text,date',
        'value'          => 'nullable|string', // raw from input (select/date/text)
    ]);

    $key   = strtolower($data['field_key']);
    $type  = $data['field_type'];
    $value = $data['value'];

    // Normalize
    $payload = ['field_type' => $type, 'value_text' => null, 'value_date' => null];

    if ($type === 'date') {
        $payload['value_date'] = $this->parseDateFlexible($value); // returns Y-m-d or null
    } else {
        $payload['value_text'] = $value ?? '';
    }

    try {
        $saved = OutdoorMonthlyDetail::updateOrCreate(
            [
                'master_file_id' => (int)$data['master_file_id'],
                'year'           => (int)$data['year'],
                'month'          => (int)$data['month'],
                'field_key'      => $key,
            ],
            $payload
        );

        return response()->json([
            'ok'    => true,
            'saved' => [
                'master_file_id' => $saved->master_file_id,
                'year'           => $saved->year,
                'month'          => $saved->month,
                'field_key'      => $saved->field_key,
                'field_type'     => $saved->field_type,
                'value_text'     => $saved->value_text,
                'value_date'     => optional($saved->value_date)->format('Y-m-d'),
            ]
        ]);
    } catch (\Throwable $e) {
        Log::error('Outdoor monthly upsert failed', ['err' => $e->getMessage(), 'payload' => $data]);
        return response()->json(['ok' => false, 'message' => 'Server error'], 500);
    }
}

private function parseDateFlexible(?string $raw): ?string
{
    if (!$raw) return null;
    $raw = trim($raw);
    $fmts = ['Y-m-d','m/d/Y','d/m/Y'];
    foreach ($fmts as $fmt) {
        try { return Carbon::createFromFormat($fmt, $raw)->format('Y-m-d'); } catch (\Throwable $e) {}
    }
    try { return Carbon::parse($raw)->format('Y-m-d'); } catch (\Throwable $e) { return null; }
}



public function exportMatrix(Request $req)
{
    $tz      = 'Asia/Kuala_Lumpur';
    $year    = (int)($req->input('year') ?: now($tz)->year);
    $product = trim((string)$req->input('product', ''));

    // 1) Ambil details PER-MASTER-FILE untuk tahun tsb (tidak ada outdoor_item_id di schema)
    $details = DB::table('outdoor_monthly_details as omd')
        ->join('master_files as mf', 'mf.id', '=', 'omd.master_file_id')
        ->where('omd.year', $year)
        ->when($product !== '', function ($q) use ($product) {
            $q->where('mf.product', $product);
        })
        ->orderBy('omd.master_file_id')
        ->orderBy('omd.month')
        ->get([
            'omd.master_file_id',
            'omd.month',
            'omd.field_key',
            'omd.field_type',
            'omd.value_text',
            'omd.value_date',
        ]);

    if ($details->isEmpty()) {
        return back()->with('warning','No data to export.');
    }

    // 2) Ambil baris PER-SITE (join mf × outdoor_items); filter optional by product & year (pakai date/date_finish/created_at)
    $sitesQ = DB::table('master_files as mf')
        ->join('outdoor_items as oi', 'oi.master_file_id', '=', 'mf.id')
        ->when($product !== '', function ($q) use ($product) {
            $q->where('mf.product', $product);
        })
        ->where(function ($w) use ($year) {
            $w->whereYear('mf.date', $year)
              ->orWhereYear('mf.date_finish', $year)
              ->orWhereYear('mf.created_at', $year);
        })
        ->orderBy('mf.company')
        ->orderBy('oi.site');

    $siteRows = $sitesQ->get([
        'mf.id        as master_file_id',
        'mf.company',
        'mf.product',
        'mf.product_category',
        'mf.date      as ui_date',
        'mf.date      as start',
        'mf.date_finish as end',
        'oi.site',
    ]);

    if ($siteRows->isEmpty()) {
        // fallback: tetap export tapi kosong
        return (new OutdoorMatrixExport([]))
            ->download('outdoor_coordinator_'.$year.($product ? '_'.Str::slug($product) : '').'.xlsx');
    }

    // 3) Bentuk peta months per master_file_id dari details (status/date)
    //    Karena schema kamu belum per-site, nanti akan di-apply ke semua site milik master_file tsb.
    $monthsByMf = []; // mfId => [1..12 => ['status'=>..,'date'=>..]]
    foreach ($details->groupBy('master_file_id') as $mfId => $rows) {
        $months = [];
        for ($m = 1; $m <= 12; $m++) $months[$m] = ['status' => '', 'date' => null];

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
        $monthsByMf[$mfId] = $months;
    }

    // 4) Build records per-SITE, months diambil dari monthsByMf[mfId]
    $records = [];
    foreach ($siteRows as $r) {
        $mfId   = $r->master_file_id;
        $months = $monthsByMf[$mfId] ?? (function () {
            $m = [];
            for ($i=1;$i<=12;$i++) $m[$i] = ['status'=>'','date'=>null];
            return $m;
        })();

        $records[] = [
            // exporter OutdoorMatrixExport kamu support 'summary' shape
            'summary' => [
                'date'     => $r->ui_date ?: $r->start, // aman kalo null, exporter handle
                'company'  => $r->company,
                'product'  => $r->product,
                'site'     => $r->site,                      // ← kolom Site KEISI
                'category' => $r->product_category ?: 'Outdoor',
                'start'    => $r->start,
                'end'      => $r->end,
            ],
            'months' => $months,
        ];
    }

    $file = 'outdoor_coordinator_'.$year.($product ? '_'.Str::slug($product) : '').'.xlsx';
    return (new OutdoorMatrixExport($records))->download($file);
}


    public function upsert(Request $req)
    {
        $data = $req->validate([
            'master_file_id' => 'required|integer|exists:master_files,id',
            'year'           => 'required|integer|min:2000|max:2100',
            'month'          => 'required|integer|min:1|max:12',
            'field_key'      => 'required|string|in:remark,installed_on,material_received,approved_on,status', // ADDED: status
            'field_type'     => 'required|string|in:text,date',

            // One of these must be present depending on field_type
            'value_text'     => 'nullable|string',
            'value_date'     => 'nullable|date',
        ]);

        // Normalize values to avoid cross-over writes
        if ($data['field_type'] === 'text') {
            $values = ['value_text' => $data['value_text'] ?? '', 'value_date' => null];
        } else {
            $values = ['value_text' => null, 'value_date' => $data['value_date'] ?? null];
        }

        try {
            $saved = OutdoorMonthlyDetail::updateOrCreate(
                [
                    'master_file_id' => $data['master_file_id'],
                    'year'           => $data['year'],
                    'month'          => $data['month'],
                    'field_key'      => strtolower($data['field_key']), // Store as lowercase
                ],
                array_merge(['field_type' => $data['field_type']], $values)
            );

            return response()->json(['ok' => true, 'saved' => $saved], 200);
        } catch (\Throwable $e) {
            Log::error('Outdoor upsert failed', ['err' => $e->getMessage(), 'in' => $data]);
            return response()->json([
                'ok' => false,
                'message' => 'Failed to save. Check server logs.',
            ], 500);
        }
    }
}
