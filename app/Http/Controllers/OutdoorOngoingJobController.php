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
    $allowed = ['TB','BB','Newspaper','Bunting','Flyers','Star','Signages'];
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


     public function index(Request $req)
{
    $year = (int) ($req->input('outdoor_year') ?: now()->year);

    // Normalisasi product code dari dropdown (mis. "BB - Billboard" -> "BB")
    $rawProduct = trim((string) $req->input('outdoor_product', ''));
    $product = $rawProduct !== '' ? strtoupper(preg_split('/\s*-+\s*/', $rawProduct)[0]) : '';

    // ==== pakai effective year ====
    $yearExpr = "CASE
        WHEN `date` IS NULL OR YEAR(`date`) < 2000 OR YEAR(`date`) > 2100
            THEN YEAR(`created_at`)
        ELSE YEAR(`date`)
    END";

    // Tahun tersedia (Outdoor only)
    $availableYears = $this->baseOutdoor(MasterFile::query())
        ->selectRaw("$yearExpr as y")
        ->distinct()
        ->orderBy('y', 'desc')
        ->pluck('y');

    if (!$availableYears->contains($year)) {
        $availableYears = $availableYears->prepend($year)->unique()->sortDesc()->values();
    }

    // Rows grid (Outdoor only + filter tahun + optional product)
    $rows = $this->baseOutdoor(MasterFile::query())
        ->when($product !== '', fn($q) => $q->where('product', $product))
        ->whereRaw("$yearExpr = ?", [$year])
        ->orderByRaw('COALESCE(`date`, `created_at`) DESC')
        ->get();

    if ($rows->isEmpty()) {
        $rows = $this->baseOutdoor(MasterFile::query())
            ->when($product !== '', fn($q) => $q->where('product', $product))
            ->orderByRaw('COALESCE(`date`, `created_at`) DESC')
            ->limit(50)
            ->get();
    }

    // Seed slot detail (biar cell selalu ada)
    $ids = $rows->pluck('id')->all();
    if (!empty($ids)) $this->ensureOutdoorSlots($ids, $year);

    // Ambil nilai terakhir per slot (tanpa bug join 'mx.ts')
    $existing = collect();
    if (!empty($ids)) {
        $latestPerSlot = DB::table('outdoor_monthly_details')
            ->select([
                'master_file_id','year','month','field_key',
                DB::raw('MAX(COALESCE(updated_at, created_at)) as ts'),
            ])
            ->whereIn('master_file_id', $ids)
            ->where('year', $year)
            ->groupBy('master_file_id','year','month','field_key');

        $details = DB::table('outdoor_monthly_details as d')
            ->joinSub($latestPerSlot, 'mx', function ($j) {
                $j->on('d.master_file_id','=','mx.master_file_id')
                  ->on('d.year','=','mx.year')
                  ->on('d.month','=','mx.month')
                  ->on('d.field_key','=','mx.field_key');
            })
            ->whereRaw('COALESCE(d.updated_at,d.created_at) = mx.ts')
            ->get([
                'd.master_file_id','d.year','d.month',
                DB::raw('LOWER(d.field_key) as field_key'),
                'd.field_type','d.value_text','d.value_date',
            ]);

        $existing = $details->mapWithKeys(function ($d) {
            $key = "{$d->master_file_id}:{$d->month}:{$d->field_key}";
            return [$key => (object)[
                'field_type' => $d->field_type,
                'value_text' => $d->value_text,
                'value_date' => $d->value_date ? \Carbon\Carbon::parse($d->value_date) : null,
            ]];
        });
    }

    $outdoorProducts = ['TB','BB','Newspaper','Bunting','Flyers','Star','Signages'];

    return view('dashboard.outdoor', compact(
        'year','rows','existing','availableYears','outdoorProducts','product'
    ));
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
    $year    = (int)($req->input('year') ?: now('Asia/Kuala_Lumpur')->year);
    $product = trim((string)$req->input('product', ''));

    // ambil detail
    $details = DB::table('outdoor_monthly_details')
        ->where('year', $year)
        ->when($product !== '', function($q) use($product){
            $q->whereIn('master_file_id', function($qq) use($product){
                $qq->select('id')->from('master_files')->where('product', $product);
            });
        })
        ->orderBy('master_file_id')
        ->orderBy('month')
        ->get(['master_file_id','month','field_key','field_type','value_text','value_date']);

    if ($details->isEmpty()) return back()->with('warning','No data to export.');

    $masters = DB::table('master_files')
        ->whereIn('id', $details->pluck('master_file_id')->unique())
        ->get(['id','date as ui_date','company','product','product_category','date as start','date_finish as end'])
        ->keyBy('id');

    // build records
    $records = [];
    foreach ($details->groupBy('master_file_id') as $mfId => $rows) {
        $m = $masters->get($mfId);
        if (!$m) continue;

        // init 12 bulan
        $months = [];
        for ($i=1;$i<=12;$i++) $months[$i] = ['status'=>'','date'=>null];

        foreach ($rows as $r) {
            $mn = (int)$r->month; if ($mn<1 || $mn>12) continue;
            $fk = strtolower((string)$r->field_key);
            if ($r->field_type === 'text' && $fk === 'status' && $r->value_text) {
                $months[$mn]['status'] = $r->value_text;
            }
            if ($r->field_type === 'date' && $r->value_date) {
                // kalau mau prioritas tertentu, atur di sini
                $months[$mn]['date'] = $r->value_date;
            }
        }

        $records[] = [
            'summary' => [
                'date'     => $m->ui_date,
                'company'  => $m->company,
                'product'  => $m->product,
                'category' => $m->product_category ?: 'Outdoor',
                'start'    => $m->start,
                'end'      => $m->end,
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
