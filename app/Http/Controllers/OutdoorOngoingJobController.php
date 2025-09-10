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
    // -------- Inputs --------
    $year   = (int) $request->integer('outdoor_year') ?: (int) now()->year;
    $month  = (int) $request->integer('outdoor_month'); // 0 or 1..12
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
    ->whereIn(
        'outdoor_item_id',
        $rows->pluck('outdoor_item_id')->filter()->unique()->values()
    )
    ->get();

$existing = $details->mapWithKeys(function ($r) {
    return [ $r->outdoor_item_id . ':' . $r->month . ':' . $r->field_key => $r ];
});

    return view('dashboard.outdoor', [
    'rows'           => $rows,
    'availableYears' => $availableYears,
    'year'           => (int) $year,
    'existing'       => $existing,
]);

}

// public function index(Request $request)
// {
//     // -------- Inputs --------
//     $year  = (int) ($request->input('outdoor_year') ?: now()->year);
//     // Accept outdoor_month (preferred). If missing, fall back to 'month'. 0 = "All months".
//     $month = (int) ($request->input('outdoor_month') ?: $request->input('month') ?: 0);
//     if ($month < 0 || $month > 12) {
//         $month = 0;
//     }
//     $search = trim((string) $request->get('search', ''));

//     // -------- Base query: Outdoor-only + sites joined --------
//     $q = DB::table('master_files as mf')
//         ->leftJoin('outdoor_items as oi', 'oi.master_file_id', '=', 'mf.id')
//         ->where(function ($w) {
//             $w->whereRaw('LOWER(mf.product_category) LIKE ?', ['%outdoor%'])
//               ->orWhereRaw('LOWER(mf.product) LIKE ?', ['%outdoor%']);
//         });

//     // -------- Search (company/product/site/coords/district) --------
//     if ($search !== '') {
//         $like = '%' . strtolower($search) . '%';
//         $q->where(function ($w) use ($like) {
//             $w->whereRaw('LOWER(mf.company) LIKE ?', [$like])
//               ->orWhereRaw('LOWER(mf.product) LIKE ?', [$like])
//               ->orWhereRaw('LOWER(oi.site) LIKE ?', [$like])
//               ->orWhereRaw('LOWER(COALESCE(oi.coordinates,"")) LIKE ?', [$like])
//               ->orWhereRaw('LOWER(COALESCE(oi.district_council,"")) LIKE ?', [$like]);
//         });
//     }

//     // -------- Year filter (a row qualifies if any of these dates fall in the year) --------
//     if ($request->filled('outdoor_year')) {
//         $y = (int) $year;
//         $q->where(function ($w) use ($y) {
//             $w->whereYear('mf.date', $y)
//               ->orWhereYear('mf.date_finish', $y)
//               ->orWhereYear('mf.created_at', $y);
//         });
//     }

//     // -------- PER-SITE selection (no GROUP_CONCAT) --------
//     $rows = $q->select([
//             'mf.id',                       // master_file_id
//             'mf.company',
//             'mf.product',
//             'mf.product_category',
//             'mf.date',
//             'mf.date_finish',
//             'mf.month',
//             'mf.created_at',
//             'oi.id  as outdoor_item_id',
//             'oi.site',
//             'oi.size',
//             'oi.coordinates',
//             'oi.district_council',
//         ])
//         ->orderBy('mf.company')
//         ->orderBy('oi.site')
//         ->get();

//     // -------- Available years (union of date sources across outdoor data) --------
//     $years = DB::query()
//         ->fromSub(function ($sub) {
//             $sub->from('master_files as mf')
//                 ->leftJoin('outdoor_items as oi', 'oi.master_file_id', '=', 'mf.id')
//                 ->where(function ($w) {
//                     $w->whereRaw('LOWER(mf.product_category) LIKE ?', ['%outdoor%'])
//                       ->orWhereRaw('LOWER(mf.product) LIKE ?', ['%outdoor%']);
//                 })
//                 ->selectRaw('YEAR(mf.date) as y')->whereNotNull('mf.date')
//                 ->union(
//                     DB::table('master_files as mf')
//                         ->leftJoin('outdoor_items as oi', 'oi.master_file_id', '=', 'mf.id')
//                         ->where(function ($w) {
//                             $w->whereRaw('LOWER(mf.product_category) LIKE ?', ['%outdoor%'])
//                               ->orWhereRaw('LOWER(mf.product) LIKE ?', ['%outdoor%']);
//                         })
//                         ->selectRaw('YEAR(mf.date_finish) as y')->whereNotNull('mf.date_finish')
//                 )
//                 ->union(
//                     DB::table('master_files as mf')
//                         ->leftJoin('outdoor_items as oi', 'oi.master_file_id', '=', 'mf.id')
//                         ->where(function ($w) {
//                             $w->whereRaw('LOWER(mf.product_category) LIKE ?', ['%outdoor%'])
//                               ->orWhereRaw('LOWER(mf.product) LIKE ?', ['%outdoor%']);
//                         })
//                         ->selectRaw('YEAR(mf.created_at) as y')->whereNotNull('mf.created_at')
//                 );
//         }, 'years_union')
//         ->select('y')
//         ->whereNotNull('y')
//         ->distinct()
//         ->orderBy('y', 'desc')
//         ->pluck('y');

//     $availableYears = $years;

//     // -------- Existing monthly detail map (filtered by year and optional month) --------
//     $detailIds = $rows->pluck('outdoor_item_id')->filter()->unique()->values();

//     $detailsQuery = DB::table('outdoor_monthly_details')
//         ->where('year', $year)
//         ->whereIn('outdoor_item_id', $detailIds);

//     if ($month >= 1 && $month <= 12) {
//         $detailsQuery->where('month', $month);
//     }

//     $details = $detailsQuery->get();

//     $existing = $details->mapWithKeys(function ($r) {
//         return [ $r->outdoor_item_id . ':' . $r->month . ':' . $r->field_key => $r ];
//     });

//     return view('dashboard.outdoor', [
//         'rows'           => $rows,
//         'availableYears' => $availableYears,
//         'year'           => (int) $year,
//         'month'          => (int) $month, // so your <select> can stay selected
//         'existing'       => $existing,
//     ]);
// }



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
