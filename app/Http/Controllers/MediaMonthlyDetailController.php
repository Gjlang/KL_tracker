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
