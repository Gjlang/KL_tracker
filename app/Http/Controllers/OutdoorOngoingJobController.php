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

    // subquery: ada detail di tahun terpilih?
    $d = DB::table('outdoor_monthly_details')
        ->select('outdoor_item_id')
        ->where('year', $year)
        ->distinct();

    $q = DB::table('master_files as mf')
        ->leftJoin('outdoor_items as oi', 'oi.master_file_id', '=', 'mf.id')
        ->leftJoinSub($d, 'd', function($j){
            $j->on('d.outdoor_item_id','=','oi.id');
        })
        ->where(function ($w) {
            $w->whereRaw('LOWER(mf.product_category) LIKE ?', ['%outdoor%'])
              ->orWhereRaw('LOWER(mf.product) LIKE ?', ['%outdoor%']);
        });

    // Search opsional
    if ($search !== '') {
        $like = '%'.strtolower($search).'%';
        $q->where(function ($w) use ($like) {
            $w->whereRaw('LOWER(mf.company) LIKE ?', [$like])
              ->orWhereRaw('LOWER(mf.product) LIKE ?', [$like])
              ->orWhereRaw('LOWER(oi.site) LIKE ?', [$like])
              ->orWhereRaw('LOWER(COALESCE(oi.coordinates,"")) LIKE ?', [$like])
              ->orWhereRaw('LOWER(COALESCE(oi.district_council,"")) LIKE ?', [$like]);
        });
    }

    // === TAMPILKAN HANYA YANG RELEVAN KE TAHUN TERPILIH ===
    $q->where(function ($w) use ($year) {
        // finish-year duluan
        $w->whereYear('mf.date_finish', $year)
          // kalau nggak ada finish, boleh date/created_at
          ->orWhere(function ($x) use ($year) {
              $x->whereNull('mf.date_finish')
                ->where(function ($yq) use ($year) {
                    $yq->whereYear('mf.date', $year)
                       ->orWhereYear('mf.created_at', $year);
                });
          })
          // atau ada detail untuk tahun ini
          ->orWhereNotNull('d.outdoor_item_id');
    });

    $rows = $q->select([
            'mf.id',
            'mf.company','mf.product','mf.product_category',
            'mf.date','mf.date_finish','mf.month','mf.created_at',
            'oi.id as outdoor_item_id','oi.site','oi.size','oi.coordinates','oi.district_council',
        ])
        ->whereNotNull('oi.id')
        ->orderBy('mf.company')->orderBy('oi.site')
        ->get();

    // ambil detail KHUSUS tahun terpilih (+ opsional filter bulan)
    $details = DB::table('outdoor_monthly_details')
        ->where('year', $year)
        ->when($month > 0, fn($qq) => $qq->where('month',$month))
        ->whereIn('outdoor_item_id', $rows->pluck('outdoor_item_id')->unique()->values())
        ->get();

    $existing = $details->mapWithKeys(fn($r) => [
        $r->outdoor_item_id.':'.$r->month.':'.$r->field_key => $r
    ]);

    // years list untuk dropdown (gabungan masters + details)
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

    // months list (untuk select)
    $months = collect(range(1,12))->map(fn($m)=>[
        'value'=>$m,'label'=>\Carbon\Carbon::create(null,$m,1)->format('F')
    ])->all();

    return view('dashboard.outdoor', [
        'rows'     => $rows,
        'years'    => $years,
        'year'     => $year,
        'months'   => $months,
        'month'    => $month,
        'existing' => $existing,
        'search'   => $search,
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
    $tz      = 'Asia/Kuala_Lumpur';
    $year    = (int)($req->input('year') ?: now($tz)->year);
    $product = trim((string)$req->input('product', ''));

    // 1) Ambil baris PER-SITE (mf × oi), include oi.id sebagai outdoor_item_id
    $sitesQ = DB::table('master_files as mf')
        ->join('outdoor_items as oi', 'oi.master_file_id', '=', 'mf.id')
        ->when($product !== '', function ($q) use ($product) {
            $q->where('mf.product', $product);
        })
        // batasi ke tahun yang dipilih (sesuai logika lama kamu)
        ->where(function ($w) use ($year) {
            $w->whereYear('mf.date', $year)
              ->orWhereYear('mf.date_finish', $year)
              ->orWhereYear('mf.created_at', $year);
        })
        ->orderBy('mf.company')
        ->orderBy('oi.site');

    $siteRows = $sitesQ->get([
        'mf.id           as master_file_id',
        'oi.id           as outdoor_item_id', // 🔑 WAJIB
        'mf.company',
        'mf.product',
        'mf.product_category',
        'mf.date         as ui_date',
        'mf.date         as start',
        'mf.date_finish  as end',
        'oi.site',
    ]);

    if ($siteRows->isEmpty()) {
        return (new \App\Exports\OutdoorMatrixExport([]))
            ->download('outdoor_coordinator_'.$year.($product ? '_'.\Illuminate\Support\Str::slug($product) : '').'.xlsx');
    }

    // 2) Ambil monthly details HANYA untuk daftar site di atas (per-site by outdoor_item_id)
    $siteIds = $siteRows->pluck('outdoor_item_id')->filter()->unique()->values();
    $details = DB::table('outdoor_monthly_details as omd')
        ->where('omd.year', $year)
        ->whereIn('omd.outdoor_item_id', $siteIds)
        ->orderBy('omd.outdoor_item_id')
        ->orderBy('omd.month')
        ->get([
            'omd.outdoor_item_id', // 🔑
            'omd.month',
            'omd.field_key',
            'omd.field_type',
            'omd.value_text',
            'omd.value_date',
        ]);

    // 3) Bangun peta months per OUTDOOR ITEM (bukan per master)
    //    itemId => [1..12 => ['status'=>..,'date'=>..]]
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

    // 4) Build records per-SITE, ambil months dari monthsByItem[outdoor_item_id]
    $records = [];
    foreach ($siteRows as $r) {
        $itemId = (int)$r->outdoor_item_id;
        $months = $monthsByItem[$itemId] ?? (function () {
            $m = [];
            for ($i=1; $i<=12; $i++) $m[$i] = ['status'=>'','date'=>null];
            return $m;
        })();

        $records[] = [
            'summary' => [
                'date'     => $r->ui_date ?: $r->start,
                'company'  => $r->company,
                'product'  => $r->product,
                'site'     => $r->site,
                'category' => $r->product_category ?: 'Outdoor',
                'start'    => $r->start,
                'end'      => $r->end,
            ],
            'months' => $months,
        ];
    }

    $file = 'outdoor_coordinator_'.$year.($product ? '_'.\Illuminate\Support\Str::slug($product) : '').'.xlsx';
    return (new \App\Exports\OutdoorMatrixExport($records))->download($file);
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
