<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ClienteleController extends Controller
{
    /**
     * Inline update for cells from master_files.
     * Expected payload: { id, column, value, scope: 'outdoor'|'kltg' }
     */
    public function inlineUpdate(Request $request)
    {
        $data = $request->validate([
            'id'     => ['required','integer'],
            'column' => ['required','string'],
            'value'  => ['nullable'],
            'scope'  => ['nullable','in:outdoor,kltg'], // optional; defaults to base
        ]);

        $scope = $data['scope'] ?? 'base';

        // ---- WHITELISTS (edit only what makes sense) ----
        $allowedBase = [
            'month','date','company','product','product_category','location','traffic','duration',
            'amount','status','remarks','client','sales_person','barter',
            'date_finish','job_number','artwork','invoice_date','invoice_number',
            'contact_number','email',
        ];

        $allowedOutdoor = [
            'outdoor_size','outdoor_district_council','outdoor_coordinates',

        ];

        $allowedKltg = [
            'kltg_industry','kltg_x','kltg_edition','kltg_material_cbp','kltg_print',
            'kltg_article','kltg_video','kltg_leaderboard','kltg_qr_code','kltg_blog','kltg_em',
            'kltg_remarks',

        ];

        $allowed = $allowedBase;
        if ($scope === 'outdoor') $allowed = array_merge($allowed, $allowedOutdoor);
        if ($scope === 'kltg')    $allowed = array_merge($allowed, $allowedKltg);

        // Guard: only allow known columns
        if (! in_array($data['column'], $allowed, true)) {
            return response()->json(['ok' => false, 'message' => 'Column not editable.'], 422);
        }

        // ---- NORMALIZERS ----
        $dateCols   = ['date_finish','invoice_date']; // real DATE columns
        $ymdCols    = $dateCols;                      // save as Y-m-d
        $freeDateLike = ['date'];                     // stored as varchar; preserve input if parse fails
        $amountCols = ['amount'];

        // KLTG boolean ticks (tinyint 0/1)
        $boolCols = array_filter(array_merge($allowedKltg), fn($c) => str_starts_with($c, 'check_') && !in_array($c, [
            // exclude string check_* (outdoor monthly strings) from bool set
            'check_jan','check_feb','check_mar','check_apr','check_may','check_jun',
            'check_jul','check_aug','check_sep','check_oct','check_nov','check_dec',
        ]));

        $col   = $data['column'];
        $value = $data['value'];

        // Normalize boolean flags for tinyint(1)
        if (in_array($col, $boolCols, true)) {
            $value = (in_array($value, [1, '1', true, 'true', 'on', 'yes'], true)) ? 1 : 0;
        }

        // Normalize amount
        if (in_array($col, $amountCols, true)) {
            $value = ($value === '' || $value === null) ? null : (float) str_replace([','], [''], (string)$value);
        }

        // Normalize true DATE columns
        if (in_array($col, $ymdCols, true)) {
            $value = ($value === '' || $value === null) ? null : Carbon::parse($value)->format('Y-m-d');
        }

        // Keep 'date' (varchar) user-friendly: try parse, else store as-is
        if (in_array($col, $freeDateLike, true) && $value) {
            try {
                $value = Carbon::parse($value)->format('n/j/y'); // match your display format
            } catch (\Throwable $e) {
                // leave raw string
            }
        }

        // ---- UPDATE DB ----
        $affected = DB::table('master_files')
            ->where('id', $data['id'])
            ->update([$col => $value, 'updated_at' => now()]);

        if ($affected === 0) {
            return response()->json(['ok' => false, 'message' => 'Row not found or no change.'], 404);
        }

        return response()->json(['ok' => true]);
    }

    public function batchUpdate(Request $request)
{
    $data = $request->validate([
        'scope'            => 'required|string|in:kltg,outdoor',
        'changes'          => 'required|array|min:1',
        'changes.*.id'     => 'required|integer|exists:master_files,id',
        'changes.*.column' => 'required|string',
        'changes.*.value'  => 'nullable',
        // Outdoor (optional; only for oi.* columns)
        'changes.*.outdoor_item_id' => 'nullable|integer|exists:outdoor_items,id',
    ]);

    // Lists reused from inlineUpdate
    $boolCols   = []; // (none in your KLTG/outdoor set right now; add if you use tinyint flags)
    $amountCols = ['amount'];
    $ymdCols    = ['date_finish','invoice_date'];
    $freeDateLike = ['date','invoice_number_date']; // 'date' is varchar in your schema

    // Column routing rules for OUTDOOR scope
    $outdoorMap = [
        'location'                 => ['table' => 'outdoor_items', 'col' => 'site'],
        'outdoor_size'             => ['table' => 'outdoor_items', 'col' => 'size'],
        'outdoor_district_council' => ['table' => 'outdoor_items', 'col' => 'district_council'],
        'outdoor_coordinates'      => ['table' => 'outdoor_items', 'col' => 'coordinates'],
        // everything else -> master_files as-is
    ];

    $changes  = $data['changes'];
    $scope    = $data['scope'];
    $ok = 0; $failed = [];

    DB::beginTransaction();
    try {
        foreach ($changes as $c) {
            try {
                $id    = (int)$c['id'];
                $colIn = (string)$c['column'];
                $val   = $c['value'] ?? null;

                // Normalize value like inlineUpdate
                if (in_array($colIn, $boolCols, true)) {
                    $val = (in_array($val, [1,'1',true,'true','on','yes'], true)) ? 1 : 0;
                }
                if (in_array($colIn, $amountCols, true)) {
                    $val = ($val === '' || $val === null) ? null : (float) str_replace([','], [''], (string)$val);
                }
                if (in_array($colIn, $ymdCols, true)) {
                    $val = ($val === '' || $val === null) ? null : \Carbon\Carbon::parse($val)->format('Y-m-d');
                }
                if (in_array($colIn, $freeDateLike, true) && $val) {
                    try { $val = \Carbon\Carbon::parse($val)->format('n/j/y'); } catch (\Throwable $e) { /* leave as is */ }
                }

                if ($scope === 'outdoor' && isset($outdoorMap[$colIn])) {
                    // update outdoor_items
                    $oiId = isset($c['outdoor_item_id']) ? (int)$c['outdoor_item_id'] : 0;
                    if ($oiId <= 0) throw new \RuntimeException('Missing outdoor_item_id for column '.$colIn);

                    $affected = DB::table('outdoor_items')
                        ->where('id', $oiId)
                        ->update([$outdoorMap[$colIn]['col'] => $val, 'updated_at' => now()]);
                    if ($affected === 0) throw new \RuntimeException('No change for outdoor_items row');
                } else {
                    // update master_files
                    $affected = DB::table('master_files')
                        ->where('id', $id)
                        ->update([$colIn => $val, 'updated_at' => now()]);
                    if ($affected === 0) throw new \RuntimeException('No change for master_files row');
                }

                $ok++;
            } catch (\Throwable $e) {
                $failed[] = [
                    'id' => $c['id'],
                    'column' => $c['column'],
                    'error' => $e->getMessage(),
                ];
            }
        }

        DB::commit();
    } catch (\Throwable $e) {
        DB::rollBack();
        return response()->json(['ok' => false, 'message' => $e->getMessage()], 500);
    }

    return response()->json(['ok' => true, 'saved' => $ok, 'failed' => $failed]);
}

}
