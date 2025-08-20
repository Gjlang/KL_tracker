<?php

namespace App\Http\Controllers;

use App\Models\MasterFile;
use App\Models\MediaMonthlyDetail;
use App\Models\MediaOngoingJob;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MediaMonthlyDetailController extends Controller
{
    public function index(Request $request)
    {
        Log::info('MEDIA INDEX HIT');

        // 1) Resolve year (fallback to current)
        $year = (int)($request->get('year') ?: now()->year);

        // 2) Base rows = Media masters (case-insensitive)
        $rows = MasterFile::query()
            ->where(function ($q) {
                $q->where('product_category', 'Media')
                  ->orWhereRaw('LOWER(product_category) LIKE ?', ['%media%']);
            })
            ->orderByDesc('created_at')
            ->orderByRaw('COALESCE(`date`, `created_at`) DESC')
            ->orderByDesc('id')
            ->get();

        Log::info('Found ' . $rows->count() . ' media master files');

        // 3) Pull monthly details
        $ids = $rows->pluck('id')->all();
        $rawDetails = collect();

        if (!empty($ids)) {
            $latestPerSlot = DB::table('media_monthly_details')
                ->select([
                    'master_file_id',
                    'year',
                    'month',
                    DB::raw('MAX(COALESCE(updated_at, created_at)) as ts'),
                ])
                ->whereIn('master_file_id', $ids)
                ->where('year', $year)
                ->groupBy('master_file_id', 'year', 'month');

            $rawDetails = collect(
                DB::table('media_monthly_details as d')
                    ->joinSub($latestPerSlot, 'mx', function ($j) {
                        $j->on('d.master_file_id', '=', 'mx.master_file_id')
                          ->on('d.year', '=', 'mx.year')
                          ->on('d.month', '=', 'mx.month')
                          ->on(DB::raw('COALESCE(d.updated_at, d.created_at)'), '=', 'mx.ts');
                    })
                    ->get([
                        'd.master_file_id', 'd.year', 'd.month',
                        'd.value_text', 'd.value_date',
                        'd.updated_at', 'd.created_at',
                    ])
            );
        }

        Log::info('Found ' . $rawDetails->count() . ' monthly details');

        // 4) Build detailsMap
        $detailsMap = [];
        foreach ($rawDetails as $r) {
            $mid = (int)$r->master_file_id;
            $yr  = (int)$r->year;
            $mon = (int)$r->month;

            $detailsMap[$mid][$yr][$mon] = [
                'value_text' => $r->value_text ?? '',
                'value_date' => $r->value_date ?? null,
            ];
        }

        // 5) Transform to match Blade expectations
        $mediaJobs = $rows->map(function ($masterFile) use ($detailsMap, $year) {
            $job = (object) [
                'id' => $masterFile->id,
                'date' => $masterFile->date,
                'company' => $masterFile->company ?? $masterFile->client ?? '',
                'product' => $masterFile->product ?? '',
                'product_category' => $masterFile->product_category ?? 'Media',
                'location' => $masterFile->location ?? '',
                'platform' => $masterFile->platform ?? $masterFile->location ?? '',
                'date_finish' => $masterFile->date_finish ?? $masterFile->end_date ?? null,
                'remarks' => $masterFile->remarks ?? '',
                'start_date' => $masterFile->start_date ?? $masterFile->date ?? null,
                'end_date' => $masterFile->date_finish ?? $masterFile->end_date ?? null,
            ];

            // Add monthly check fields
            $monthNames = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'];
            foreach ($monthNames as $monthIndex => $monthName) {
                $monthNumber = $monthIndex + 1;
                $monthData = $detailsMap[$masterFile->id][$year][$monthNumber] ?? null;
                $job->{"check_$monthName"} = $monthData['value_text'] ?? '';
            }

            return $job;
        });

        Log::info('Transformed ' . $mediaJobs->count() . ' jobs for blade');

        // 6) Return in expected format
        return view('dashboard.media', [
            'monthlyByCategory' => [
                'Media' => $mediaJobs
            ],
            'year' => $year,
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
                $detail->subcategory = 'General';
            }

            if ($data['kind'] === 'text') {
                $detail->value_text = $data['value'] ?? null;
            }

            if ($data['kind'] === 'date') {
                $detail->value_date = $data['value'] ? date('Y-m-d', strtotime($data['value'])) : null;
            }

            $detail->save();

            Log::info('Saved media monthly detail:', $detail->toArray());

            return response()->json(['ok' => true, 'message' => 'Updated successfully']);

        } catch (\Exception $e) {
            Log::error('Media upsert error: ' . $e->getMessage());
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
