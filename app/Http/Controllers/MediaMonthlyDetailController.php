<?php

namespace App\Http\Controllers;

use App\Models\MasterFile;
use App\Models\MediaMonthlyDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MediaMonthlyDetailController extends Controller
{
    /**
     * Known media products (lowercased match)
     */
    private const MEDIA_PRODUCTS = [
        'tiktok management',
        'tiktok management boost',
        'youtube management',
        'fb/ig management',
        'fb sponsored ads',
        'giveaways/ contest management',
        'xiaohongshu management',
    ];

    public function index(Request $request)
    {
        Log::info('MEDIA INDEX HIT');

        // 1) Resolve year (fallback to current)
        $year = (int)($request->get('year') ?: now()->year);

        // 2) Base rows = Media masters (case-insensitive)
        $rows = MasterFile::query()
            ->select(['id','company','client','product','product_category','date','created_at','date_finish', 'month'])
            ->where(function ($q) {
                $q->whereRaw('LOWER(COALESCE(product_category, "")) LIKE ?', ['%media%'])
                  ->orWhereIn(
                      DB::raw('LOWER(COALESCE(product, ""))'),
                      array_map('strtolower', self::MEDIA_PRODUCTS)
                  );
            })
            ->orderByRaw('COALESCE(`date`, `created_at`) DESC')
            ->orderByDesc('id')
            ->get();

        Log::info('Found ' . $rows->count() . ' media master files');

        // 3) Pull monthly details (robust to missing month/year by deriving from value_date)
        $ids = $rows->pluck('id')->all();
        $rawDetails = collect();

        if (!empty($ids)) {
            $rawDetails = DB::table('media_monthly_details')
                ->whereIn('master_file_id', $ids)
                ->where(function ($w) use ($year) {
                    $w->where('year', $year)
                      ->orWhereRaw('YEAR(value_date) = ?', [$year]);
                })
                ->select([
                    'master_file_id',
                    DB::raw('COALESCE(`year`, YEAR(value_date)) as yr'),
                    DB::raw('COALESCE(`month`, MONTH(value_date)) as mon'),
                    'value_text',
                    'value_date',
                    DB::raw('COALESCE(updated_at, created_at) as ts'),
                ])
                ->orderByDesc('ts') // latest first; we’ll keep first per (id,yr,mon)
                ->get();
        }

        Log::info('Found ' . $rawDetails->count() . ' monthly details (pre-dedup)');

        // 4) Deduplicate: latest row per (master_file_id, yr, mon)
        $picked = [];
        foreach ($rawDetails as $r) {
            $m = (int)$r->mon;
            $y = (int)$r->yr;
            if ($m < 1 || $m > 12) { continue; }
            $key = $r->master_file_id.'|'.$y.'|'.$m;
            if (!isset($picked[$key])) {
                $picked[$key] = [
                    'master_file_id' => (int)$r->master_file_id,
                    'year'           => $y,
                    'month'          => $m,
                    'value_text'     => $r->value_text ?? '',
                    'value_date'     => $r->value_date ? date('Y-m-d', strtotime($r->value_date)) : null,
                ];
            }
        }

        // 5) Build detailsMap: [master_file_id][year][month] => ['value_text','value_date']
        $detailsMap = [];
        foreach ($picked as $p) {
            $detailsMap[$p['master_file_id']][$p['year']][$p['month']] = [
                'value_text' => $p['value_text'],
                'value_date' => $p['value_date'],
            ];
        }

        // 6) Transform to match Blade expectations (expose both status + date per month)
        $mediaJobs = $rows->map(function ($mf) use ($detailsMap, $year) {
            // Normalisasi month -> nama
            $rawMonth = $mf->month; // bisa "8", "Aug", "August", dll
            $monthName = $rawMonth;

            // kalau numeric 1..12, convert ke nama
            if (is_numeric($rawMonth)) {
                $num = (int)$rawMonth;
                if ($num >= 1 && $num <= 12) {
                    $monthName = \Carbon\Carbon::create()->month($num)->format('F'); // August
                }
            }

            $job = (object) [
                'id'               => $mf->id,
                'date'             => $mf->date,
                'company'          => $mf->company ?? $mf->client ?? '',
                'product'          => $mf->product ?? '',
                'product_category' => $mf->product_category ?? 'Media',
                'location'         => $mf->location ?? '',
                'platform'         => $mf->platform ?? ($mf->location ?? ''),
                'date_finish'      => $mf->date_finish ?? $mf->end_date ?? null,
                'remarks'          => $mf->remarks ?? '',
                'start_date'       => $mf->start_date ?? $mf->date ?? null,
                'end_date'         => $mf->date_finish ?? $mf->end_date ?? null,

                // 🔥 tambahkan ini
                'month'            => $rawMonth,
                'month_name'       => $monthName,
            ];

            // Month fields (status + date)
            $names = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'];
            foreach ($names as $i => $name) {
                $mon = $i + 1;
                $cell = $detailsMap[$mf->id][$year][$mon] ?? null;
                $job->{"check_{$name}"} = $cell['value_text'] ?? '';
                $job->{"date_{$name}"}  = $cell['value_date'] ?? null;
            }

            return $job;
        });

        Log::info('Transformed ' . $mediaJobs->count() . ' jobs for blade');

        // 7) Return view
        return view('dashboard.media', [
            'monthlyByCategory' => [
                'Media' => $mediaJobs,
            ],
            'year' => $year,
            'detailsMap'  => $detailsMap,
        ]);
    }


public function export(\Illuminate\Http\Request $request)
{
    // Reuse the same year default as index()
    $year = (int)($request->get('year') ?: now()->year);

    // ===== 1) Base Media rows (same as index) =====
    $rows = \App\Models\MasterFile::query()
        ->select(['id','company','client','product','product_category','date','created_at','date_finish','month'])
        ->where(function ($q) {
            $q->whereRaw('LOWER(COALESCE(product_category, "")) LIKE ?', ['%media%'])
              ->orWhereIn(
                  \Illuminate\Support\Facades\DB::raw('LOWER(COALESCE(product, ""))'),
                  array_map('strtolower', [
                      'tiktok management','tiktok management boost','youtube management',
                      'fb/ig management','fb sponsored ads','giveaways/ contest management',
                      'xiaohongshu management',
                  ])
              );
        })
        ->orderByRaw('COALESCE(`date`, `created_at`) DESC')
        ->orderByDesc('id')
        ->get();

    $ids = $rows->pluck('id')->all();

    // ===== 2) Pull & dedupe monthly details (same normalization as index) =====
    $rawDetails = collect();
    if (!empty($ids)) {
        $rawDetails = \Illuminate\Support\Facades\DB::table('media_monthly_details')
            ->whereIn('master_file_id', $ids)
            ->where(function ($w) use ($year) {
                $w->where('year', $year)
                  ->orWhereRaw('YEAR(value_date) = ?', [$year]);
            })
            ->select([
                'master_file_id',
                \Illuminate\Support\Facades\DB::raw('COALESCE(`year`, YEAR(value_date)) as yr'),
                \Illuminate\Support\Facades\DB::raw('COALESCE(`month`, MONTH(value_date)) as mon'),
                'value_text',
                'value_date',
                \Illuminate\Support\Facades\DB::raw('COALESCE(updated_at, created_at) as ts'),
            ])
            ->orderByDesc('ts')
            ->get();
    }

    $picked = [];
    foreach ($rawDetails as $r) {
        $m = (int)$r->mon;
        $y = (int)$r->yr;
        if ($m < 1 || $m > 12) { continue; }
        $key = $r->master_file_id.'|'.$y.'|'.$m;
        if (!isset($picked[$key])) {
            $picked[$key] = [
                'master_file_id' => (int)$r->master_file_id,
                'year'           => $y,
                'month'          => $m,
                'value_text'     => $r->value_text ?? '',
                'value_date'     => $r->value_date ? date('Y-m-d', strtotime($r->value_date)) : null,
            ];
        }
    }

    // Map: [master_file_id][year][month] => ['value_text','value_date']
    $detailsMap = [];
    foreach ($picked as $p) {
        $detailsMap[$p['master_file_id']][$p['year']][$p['month']] = [
            'value_text' => $p['value_text'],
            'value_date' => $p['value_date'],
        ];
    }

    // ===== 3) Build CSV rows =====
    $months = [1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December'];

    // Header
    $header = [
        'No','Company','Product','Start','End','Master Month','Master Month (Name)'
    ];
    foreach ($months as $mNum => $mName) {
        $header[] = $mName.' Status';
        $header[] = $mName.' Date';
    }

    $csvRows = [];
    $csvRows[] = $header;

    foreach ($rows as $i => $mf) {
        // human month name from master file
        $rawMonth = $mf->month;
        $monthName = $rawMonth;
        if (is_numeric($rawMonth)) {
            $num = (int)$rawMonth;
            if ($num >= 1 && $num <= 12) {
                $monthName = \Carbon\Carbon::create()->month($num)->format('F');
            }
        }

        $line = [
            $i + 1,
            $mf->company ?? $mf->client ?? '',
            $mf->product ?? '',
            $mf->date ? date('Y-m-d', strtotime($mf->date)) : '',
            $mf->date_finish ? date('Y-m-d', strtotime($mf->date_finish)) : '',
            (string)$rawMonth,
            (string)$monthName,
        ];

        for ($m = 1; $m <= 12; $m++) {
            $cell = $detailsMap[$mf->id][$year][$m] ?? ['value_text'=>null,'value_date'=>null];
            $line[] = (string)($cell['value_text'] ?? '');
            $line[] = $cell['value_date'] ? date('Y-m-d', strtotime($cell['value_date'])) : '';
        }

        $csvRows[] = $line;
    }

    // ===== 4) Stream CSV (UTF-8 BOM) =====
    $filename = 'media_monthly_'.$year.'_'.now()->format('Ymd_His').'.csv';

    $callback = function () use ($csvRows) {
        $out = fopen('php://output', 'w');
        // UTF-8 BOM so Excel displays properly
        fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
        foreach ($csvRows as $row) {
            fputcsv($out, $row);
        }
        fclose($out);
    };

    return response()->streamDownload($callback, $filename, [
        'Content-Type' => 'text/csv; charset=UTF-8',
    ]);
}



    public function upsert(Request $req)
    {
        try {
            $data = $req->validate([
                'master_file_id' => 'required|exists:master_files,id',
                'year'           => 'required|integer|min:2000|max:2100',
                'month'          => 'required|integer|min:1|max:12',
                'kind'           => 'required|in:text,date',
                'value'          => 'nullable|string',
            ]);

            Log::info('Media upsert data:', $data);

            $detail = MediaMonthlyDetail::firstOrNew([
                'master_file_id' => $data['master_file_id'],
                'year'           => $data['year'],
                'month'          => $data['month'],
            ]);

            if (!$detail->exists) {
                // optional default
                $detail->subcategory = $detail->subcategory ?? 'General';
            }

            if ($data['kind'] === 'text') {
                $detail->value_text = $data['value'] ?: null;
            } else {
                $detail->value_date = $data['value'] ? date('Y-m-d', strtotime($data['value'])) : null;
            }

            $detail->save();

            Log::info('Saved media monthly detail', ['id' => $detail->id]);

            return response()->json(['ok' => true, 'message' => 'Updated successfully']);
        } catch (\Throwable $e) {
            Log::error('Media upsert error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
