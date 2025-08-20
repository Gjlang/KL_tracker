<?php

// app/Http/Controllers/OutdoorOngoingJobController.php
namespace App\Http\Controllers;

use App\Models\MasterFile;                // your existing model
use App\Models\OutdoorMonthlyDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OutdoorOngoingJobController extends Controller
{

     private function baseOutdoor($q)
    {
        // AGGRESSIVE Outdoor filtering - explicit inclusion and exclusion
        return $q->where(function($subQuery) {
            $subQuery->whereRaw("LOWER(TRIM(COALESCE(product_category, ''))) = 'outdoor'")
                     ->orWhereRaw("LOWER(TRIM(COALESCE(product_category, ''))) = 'outdoor advertising'")
                     ->orWhereRaw("LOWER(TRIM(COALESCE(product_category, ''))) LIKE 'outdoor %'")
                     ->orWhereRaw("LOWER(TRIM(COALESCE(product_category, ''))) LIKE '% outdoor'")
                     // Also check if product_category is null but product field contains outdoor
                     ->orWhere(function($nullCheck) {
                         $nullCheck->whereNull('product_category')
                                   ->whereRaw("LOWER(TRIM(COALESCE(product, ''))) LIKE '%outdoor%'");
                     });
        })
        // EXPLICITLY exclude KLTG and Media to prevent any leakage
        ->whereRaw("LOWER(TRIM(COALESCE(product_category, ''))) NOT LIKE '%kltg%'")
        ->whereRaw("LOWER(TRIM(COALESCE(product_category, ''))) NOT LIKE '%media%'")
        ->whereRaw("LOWER(TRIM(COALESCE(product_category, ''))) != 'kltg'")
        ->whereRaw("LOWER(TRIM(COALESCE(product_category, ''))) != 'media'");
    }
    public function index(Request $req)
    {
        // 1) Year filter from dropdown: name="outdoor_year"
        $year = (int)($req->input('outdoor_year') ?: now()->year);

        // 2) Year options — from master_files (so dropdown works even if details empty)
        $availableYears = $this->baseOutdoor(MasterFile::query())
            ->selectRaw('YEAR(COALESCE(`date`, `created_at`)) as y')
            ->distinct()
            ->orderBy('y', 'desc')
            ->pluck('y');

        if (!$availableYears->contains($year)) {
            $availableYears = $availableYears->prepend($year)->unique()->sortDesc()->values();
        }

        // 3) GRID SOURCE = master_files (this is the key)
        //    Filter by the selected year using COALESCE(date, created_at)
        $rows = $this->baseOutdoor(MasterFile::query())
            ->whereRaw('YEAR(COALESCE(`date`, `created_at`)) = ?', [$year])
            ->orderByRaw('COALESCE(`date`, `created_at`) DESC')
            ->get();

        // Fallback: if still empty, show recent 50 so the page never looks blank
        if ($rows->isEmpty()) {
            $rows = MasterFile::query()
                ->orderByRaw('COALESCE(`date`, `created_at`) DESC')
                ->limit(50)->get();
        }

        // 4) Hydrate details for the chosen year (latest per slot)
        $ids = $rows->pluck('id')->all();

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
                  ->on('d.field_key','=','mx.field_key')
                  ->on(DB::raw('COALESCE(d.updated_at,d.created_at)'),'=','mx.ts');
            })
            ->get([
                'd.master_file_id','d.year','d.month',
                DB::raw('LOWER(d.field_key) as field_key'),
                'd.field_type','d.value_text','d.value_date',
            ]);

        // Map for omd($existing, $id, $month, $key, $type)
        $existing = $details->mapWithKeys(function ($d) {
            $key = "{$d->master_file_id}:{$d->month}:{$d->field_key}";
            return [$key => (object)[
                'field_type' => $d->field_type,
                'value_text' => $d->value_text,
                'value_date' => $d->value_date ? Carbon::parse($d->value_date) : null,
            ]];
        });

        Log::info('OUTDOOR index snapshot', [
            'filter_year'  => $year,
            'rows_count'   => $rows->count(),
            'details_cnt'  => $details->count(),
        ]);

        return view('dashboard.outdoor', [
            'year'           => $year,
            'rows'           => $rows,            // <— Blade must loop THIS
            'existing'       => $existing,
            'availableYears' => $availableYears,
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
        // Validate input coming from your JS - FIXED: use lowercase field keys
        $data = $req->validate([
            'master_file_id' => 'required|integer|exists:master_files,id',
            'year'           => 'required|integer|min:2000|max:2100',
            'month'          => 'required|integer|min:1|max:12',
            'field_key'      => 'required|string|in:remark,installed_on,material_received,approved_on,status', // <-- ADDED: status
            'field_type'     => 'required|string|in:text,date',
            'value_text'     => 'nullable|string',
            'value_date'     => 'nullable|date',
        ]);

        // Normalize: write to only one column depending on type
        $values = ($data['field_type'] === 'date')
            ? ['value_text' => null, 'value_date' => $data['value_date'] ?? null]
            : ['value_text' => $data['value_text'] ?? '', 'value_date' => null];

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
            Log::error('Outdoor monthly upsert failed', ['err' => $e->getMessage(), 'payload' => $data]);
            return response()->json(['ok' => false, 'message' => 'Server error'], 500);
        }
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
