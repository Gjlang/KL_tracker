<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ClienteleController extends Controller
{
    /**
     * Display outdoor listing with proper JOIN logic
     */
    public function outdoor(Request $request)
    {
        try {
            $query = DB::table('outdoor_items as oi')
                ->leftJoin('master_files as mf', 'mf.id', '=', 'oi.master_file_id')
                ->leftJoin('billboards as bb', 'bb.id', '=', 'oi.billboard_id')
                ->leftJoin('locations as loc', 'loc.id', '=', 'bb.location_id')
                ->leftJoin('districts as d', 'd.id', '=', 'loc.district_id')
                ->leftJoin('states as s', 's.id', '=', 'd.state_id')
                ->select([
                    // IDs needed for inline updates
                    'oi.id as outdoor_item_id',
                    'mf.id as master_file_id',
                    'oi.billboard_id',

                    // Master file fields
                    'mf.company',
                    'mf.product',
                    'mf.product_category',
                    'oi.start_date as start',
                    'oi.end_date as end',
                    'mf.duration',
                    'mf.amount',
                    'mf.status',
                    'mf.remarks',
                    'mf.client',
                    'mf.sales_person',
                    'mf.month',

                    // Outdoor size from outdoor_items
                    'oi.size as outdoor_size',

                    // LOCATION: bb.site_number + loc.name
                    DB::raw("
                    CASE
                        WHEN bb.site_number IS NOT NULL AND loc.name IS NOT NULL
                            THEN CONCAT(bb.site_number, ' - ', loc.name)
                        WHEN bb.site_number IS NOT NULL THEN bb.site_number
                        WHEN loc.name IS NOT NULL THEN loc.name
                        ELSE NULL
                    END as location
                "),

                    // AREA: STATE_ABBR - district
                    DB::raw("
                    CASE
                        WHEN s.name IS NOT NULL AND d.name IS NOT NULL THEN CONCAT(
                            CASE
                                WHEN s.name = 'Kuala Lumpur' THEN 'KL'
                                WHEN s.name = 'Selangor' THEN 'SEL'
                                WHEN s.name = 'Negeri Sembilan' THEN 'N9'
                                WHEN s.name = 'Melaka' THEN 'MLK'
                                WHEN s.name = 'Johor' THEN 'JHR'
                                WHEN s.name = 'Perak' THEN 'PRK'
                                WHEN s.name = 'Pahang' THEN 'PHG'
                                WHEN s.name = 'Terengganu' THEN 'TRG'
                                WHEN s.name = 'Kelantan' THEN 'KTN'
                                WHEN s.name = 'Perlis' THEN 'PLS'
                                WHEN s.name = 'Kedah' THEN 'KDH'
                                WHEN s.name = 'Penang' THEN 'PNG'
                                WHEN s.name = 'Sarawak' THEN 'SWK'
                                WHEN s.name = 'Sabah' THEN 'SBH'
                                WHEN s.name = 'Labuan' THEN 'LBN'
                                WHEN s.name = 'Putrajaya' THEN 'PJY'
                                ELSE s.name
                            END, ' - ', d.name
                        )
                        ELSE NULL
                    END as area
                "),

                    // COORDINATES: bb.gps_latitude/longitude (fallback to oi.coordinates)
                    DB::raw("
                    CASE
                        WHEN bb.gps_latitude IS NOT NULL AND bb.gps_longitude IS NOT NULL
                            THEN CONCAT(bb.gps_latitude, ', ', bb.gps_longitude)
                        ELSE oi.coordinates
                    END as outdoor_coordinates
                "),
                ]);

            // Filters
            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('mf.company', 'like', "%{$search}%")
                        ->orWhere('mf.product', 'like', "%{$search}%")
                        ->orWhere('mf.client', 'like', "%{$search}%")
                        ->orWhere('loc.name', 'like', "%{$search}%")
                        ->orWhere('bb.site_number', 'like', "%{$search}%");
                });
            }

            if ($request->filled('company')) {
                $query->where('mf.company', $request->input('company'));
            }

            if ($request->filled('status')) {
                $query->where('mf.status', $request->input('status'));
            }

            if ($request->filled('month')) {
                $query->where('mf.month', $request->input('month'));
            }

            // Ordering (MariaDB-safe: no NULLS LAST)
            $query->orderByRaw('(mf.company IS NULL), LOWER(mf.company) ASC')
                ->orderBy('mf.date', 'desc');

            $rows = $query->get();

            // Column definitions
            $columns = [
                'location'            => 'LOCATION',
                'area'                => 'AREA',
                'duration'            => 'DURATION',
                'start'               => 'DATE',
                'end'                 => 'DATE FINISH',
                'outdoor_size'        => 'OUTDOOR SIZE',
                'outdoor_coordinates' => 'OUTDOOR COORDINATES',
            ];

            return view('outdoor', compact('rows', 'columns'));
        } catch (\Throwable $e) {
            Log::error('Error in outdoor listing', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return back()->with('error', 'Failed to load outdoor data.');
        }
    }
    /**
     * Inline update for cells from master_files and outdoor_items.
     * Expected payload: { id, column, value, scope: 'outdoor'|'kltg', outdoor_item_id? }
     */
    public function inlineUpdate(Request $request)
    {
        try {
            // 1) Validate + pull raw value
            $data = $request->validate([
                'id'     => 'required|integer|min:1',
                'column' => 'required|string',
                'value'  => 'nullable',
                'scope'  => 'nullable|in:outdoor,kltg',
                'outdoor_item_id' => 'nullable|integer|min:1',
            ]);

            $raw = $request->all();
            $col = $data['column'];
            $id  = (int)$data['id'];
            $scope = $data['scope'] ?? null;

            Log::info('InlineUpdate Request', [
                'id' => $id,
                'column' => $col,
                'raw_value' => $raw['value'] ?? 'NOT_SET',
                'scope' => $scope,
                'outdoor_item_id' => $raw['outdoor_item_id'] ?? 'NOT_SET'
            ]);

            // ---- WHITELISTS ----
            $allowedBase = [
                'month',
                'date',
                'company',
                'product',
                'product_category',
                'traffic',
                'duration',
                'amount',
                'status',
                'remarks',
                'client',
                'sales_person',
                'barter',
                'date_finish',
                'job_number',
                'artwork',
                'invoice_date',
                'invoice_number',
                'contact_number',
                'email',
            ];
            // ✅ FIXED: Only allow fields that are actually editable
            $allowedOutdoor = ['size', 'outdoor_coordinates']; // 'size' not 'outdoor_size'
            $allowedKltg    = [
                'kltg_industry',
                'kltg_x',
                'kltg_edition',
                'kltg_material_cbp',
                'kltg_print',
                'kltg_article',
                'kltg_video',
                'kltg_leaderboard',
                'kltg_qr_code',
                'kltg_blog',
                'kltg_em',
                'kltg_remarks',
            ];

            $allowed = $allowedBase;
            if ($scope === 'outdoor') $allowed = array_merge($allowed, $allowedOutdoor);
            if ($scope === 'kltg')    $allowed = array_merge($allowed, $allowedKltg);

            if (!in_array($col, $allowed, true)) {
                Log::warning('Column not allowed', ['column' => $col, 'allowed' => $allowed]);
                return response()->json(['ok' => false, 'message' => 'Column not editable.'], 422);
            }

            // 2) Normalize value carefully
            $value = array_key_exists('value', $raw) ? $raw['value'] : null;

            // Block empty for NOT NULL columns
            $notNullable = ['product', 'company', 'client'];
            if (in_array($col, $notNullable, true) && ($value === null || trim((string)$value) === '')) {
                Log::warning('NOT NULL column cannot be empty', ['column' => $col, 'value' => $value]);
                return response()->json(['ok' => false, 'message' => ucfirst($col) . ' cannot be empty.'], 422);
            }

            // If value comes as null and column allows blank, store ''
            if ($value === null && !in_array($col, $notNullable, true)) {
                $value = '';
            }

            // Type helpers
            $ymdCols      = ['date_finish', 'invoice_date'];
            $freeDateLike = ['date'];
            $amountCols   = ['amount'];

            $originalValue = $value;

            // Amount handling
            if (in_array($col, $amountCols, true)) {
                if ($value === '' || $value === null) {
                    $value = null;
                } else {
                    $cleanValue = str_replace([',', ' '], '', (string)$value);
                    if (is_numeric($cleanValue)) {
                        $value = (float)$cleanValue;
                    } else {
                        Log::warning('Invalid amount value', ['column' => $col, 'value' => $value]);
                        return response()->json(['ok' => false, 'message' => 'Amount must be a valid number.'], 422);
                    }
                }
            }

            // Date handling
            if (in_array($col, $ymdCols, true)) {
                if ($value === '' || $value === null) {
                    $value = null;
                } else {
                    try {
                        $value = Carbon::parse($value)->format('Y-m-d');
                    } catch (\Throwable $e) {
                        Log::warning('Invalid date format', ['column' => $col, 'value' => $value, 'error' => $e->getMessage()]);
                        return response()->json(['ok' => false, 'message' => 'Invalid date format for ' . $col], 422);
                    }
                }
            }

            if (in_array($col, $freeDateLike, true) && $value) {
                try {
                    $value = Carbon::parse($value)->format('n/j/y');
                } catch (\Throwable $e) {
                    Log::warning('Could not parse free date', ['column' => $col, 'value' => $value]);
                }
            }

            Log::info('Value after normalization', [
                'column' => $col,
                'original' => $originalValue,
                'normalized' => $value
            ]);

            // --- SPECIAL HANDLING FOR OUTDOOR FIELDS ---

            // ✅ 1) size -> outdoor_items.size
            if ($scope === 'outdoor' && $col === 'size') {
                $oiId = isset($raw['outdoor_item_id']) ? (int)$raw['outdoor_item_id'] : 0;
                if ($oiId <= 0) {
                    Log::warning('Missing outdoor_item_id for size', ['master_file_id' => $id]);
                    return response()->json(['ok' => false, 'message' => 'Missing outdoor_item_id for size edit.'], 422);
                }

                // Verify ownership
                $belongs = DB::table('outdoor_items')
                    ->where('id', $oiId)
                    ->where('master_file_id', $id)
                    ->exists();
                if (!$belongs) {
                    return response()->json(['ok' => false, 'message' => 'Outdoor item mismatch.'], 422);
                }

                $affected = DB::table('outdoor_items')
                    ->where('id', $oiId)
                    ->update(['size' => $value, 'updated_at' => now()]);

                Log::info('Updated outdoor_items.size', [
                    'outdoor_item_id' => $oiId,
                    'value' => $value,
                    'affected_rows' => $affected
                ]);

                return response()->json([
                    'ok' => true,
                    'affected' => (int)$affected,
                    'message' => $affected > 0 ? 'Size updated successfully' : 'No change needed'
                ]);
            }

            // ✅ 2) outdoor_coordinates -> billboards.gps_latitude + gps_longitude
            if ($scope === 'outdoor' && $col === 'outdoor_coordinates') {
                $oiId = isset($raw['outdoor_item_id']) ? (int)$raw['outdoor_item_id'] : 0;
                if ($oiId <= 0) {
                    return response()->json(['ok' => false, 'message' => 'Missing outdoor_item_id for coordinates edit.'], 422);
                }

                // ✅ Allow empty coordinates (clear them)
                if ($value === '' || $value === null) {
                    // Find the billboard
                    $oi = DB::table('outdoor_items')->where('id', $oiId)->first(['billboard_id', 'master_file_id']);
                    if (!$oi) {
                        return response()->json(['ok' => false, 'message' => 'Outdoor item not found.'], 404);
                    }

                    if (!$oi->billboard_id) {
                        return response()->json(['ok' => false, 'message' => 'No billboard linked to this outdoor item.'], 422);
                    }

                    // Verify ownership
                    if ($oi->master_file_id != $id) {
                        return response()->json(['ok' => false, 'message' => 'Outdoor item mismatch.'], 422);
                    }

                    // Clear coordinates
                    $affected = DB::table('billboards')
                        ->where('id', $oi->billboard_id)
                        ->update([
                            'gps_latitude'  => null,
                            'gps_longitude' => null,
                            'updated_at' => now()
                        ]);

                    Log::info('Cleared billboards GPS coordinates', [
                        'billboard_id' => $oi->billboard_id,
                        'affected_rows' => $affected
                    ]);

                    return response()->json([
                        'ok' => true,
                        'affected' => (int)$affected,
                        'message' => 'Coordinates cleared successfully'
                    ]);
                }

                // Parse "lat, long" format
                $parts = array_map('trim', explode(',', (string)$value));
                if (count($parts) !== 2) {
                    return response()->json(['ok' => false, 'message' => 'Coordinates must be in "latitude, longitude" format (e.g., 3.1234, 101.5678).'], 422);
                }

                [$lat, $lng] = $parts;

                // Validate numeric
                if (!is_numeric($lat) || !is_numeric($lng)) {
                    return response()->json(['ok' => false, 'message' => 'Coordinates must be numeric values.'], 422);
                }

                // Validate range
                if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
                    return response()->json(['ok' => false, 'message' => 'Coordinates out of valid range (lat: -90 to 90, lng: -180 to 180).'], 422);
                }

                // Find the billboard linked to this outdoor_item
                $oi = DB::table('outdoor_items')->where('id', $oiId)->first(['billboard_id', 'master_file_id']);
                if (!$oi) {
                    return response()->json(['ok' => false, 'message' => 'Outdoor item not found.'], 404);
                }

                if (!$oi->billboard_id) {
                    return response()->json(['ok' => false, 'message' => 'No billboard linked to this outdoor item. Please link a billboard first.'], 422);
                }

                // Verify ownership
                if ($oi->master_file_id != $id) {
                    return response()->json(['ok' => false, 'message' => 'Outdoor item mismatch.'], 422);
                }

                // Update billboards table
                $affected = DB::table('billboards')
                    ->where('id', $oi->billboard_id)
                    ->update([
                        'gps_latitude'  => (string)$lat,
                        'gps_longitude' => (string)$lng,
                        'updated_at' => now()
                    ]);

                Log::info('Updated billboards GPS coordinates', [
                    'billboard_id' => $oi->billboard_id,
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'affected_rows' => $affected
                ]);

                return response()->json([
                    'ok' => true,
                    'affected' => (int)$affected,
                    'message' => 'Coordinates updated successfully'
                ]);
            }

            // ✅ 3) Regular master_files update
            $recordExists = DB::table('master_files')->where('id', $id)->exists();
            if (!$recordExists) {
                Log::warning('Record not found', ['id' => $id]);
                return response()->json(['ok' => false, 'message' => 'Record not found.'], 404);
            }

            $affected = DB::table('master_files')
                ->where('id', $id)
                ->update([$col => $value, 'updated_at' => now()]);

            Log::info('Updated master_files', [
                'id' => $id,
                'column' => $col,
                'value' => $value,
                'affected_rows' => $affected
            ]);

            return response()->json([
                'ok' => true,
                'affected' => (int)$affected,
                'message' => $affected > 0 ? 'Updated successfully' : 'No change needed (same value)'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error', ['errors' => $e->errors()]);
            return response()->json(['ok' => false, 'message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Throwable $e) {
            Log::error('Unexpected error in inlineUpdate', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request' => $request->all()
            ]);
            return response()->json(['ok' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    public function batchUpdate(Request $request)
    {
        try {
            $data = $request->validate([
                'scope'            => 'required|string|in:kltg,outdoor',
                'changes'          => 'required|array|min:1',
                'changes.*.id'     => 'required|integer|exists:master_files,id',
                'changes.*.column' => 'required|string',
                'changes.*.value'  => 'nullable',
                'changes.*.outdoor_item_id' => 'nullable|integer|exists:outdoor_items,id',
            ]);

            $boolCols   = [];
            $amountCols = ['amount'];
            $ymdCols    = ['date_finish', 'invoice_date'];
            $freeDateLike = ['date', 'invoice_number_date'];
            $notNullable = ['product', 'company', 'client'];

            $changes  = $data['changes'];
            $scope    = $data['scope'];
            $ok = 0;
            $failed = [];

            Log::info('BatchUpdate started', ['scope' => $scope, 'changes_count' => count($changes)]);

            DB::beginTransaction();
            try {
                foreach ($changes as $index => $c) {
                    try {
                        $id    = (int)$c['id'];
                        $colIn = (string)$c['column'];
                        $val   = $c['value'] ?? null;

                        Log::info("Processing change $index", ['id' => $id, 'column' => $colIn, 'value' => $val]);

                        // Check NOT NULL constraints
                        if (in_array($colIn, $notNullable, true) && ($val === null || trim((string)$val) === '')) {
                            throw new \RuntimeException(ucfirst($colIn) . ' cannot be empty.');
                        }

                        if ($val === null && !in_array($colIn, $notNullable, true)) {
                            $val = '';
                        }

                        // Normalize value
                        if (in_array($colIn, $boolCols, true)) {
                            $val = (in_array($val, [1, '1', true, 'true', 'on', 'yes'], true)) ? 1 : 0;
                        }

                        if (in_array($colIn, $amountCols, true)) {
                            if ($val === '' || $val === null) {
                                $val = null;
                            } else {
                                $cleanValue = str_replace([',', ' '], '', (string)$val);
                                if (is_numeric($cleanValue)) {
                                    $val = (float)$cleanValue;
                                } else {
                                    throw new \RuntimeException('Amount must be a valid number.');
                                }
                            }
                        }

                        if (in_array($colIn, $ymdCols, true)) {
                            if ($val === '' || $val === null) {
                                $val = null;
                            } else {
                                $val = Carbon::parse($val)->format('Y-m-d');
                            }
                        }

                        if (in_array($colIn, $freeDateLike, true) && $val) {
                            try {
                                $val = Carbon::parse($val)->format('n/j/y');
                            } catch (\Throwable $e) {
                            }
                        }

                        // Special handling for outdoor fields
                        if ($scope === 'outdoor' && $colIn === 'outdoor_size') {
                            $oiId = isset($c['outdoor_item_id']) ? (int)$c['outdoor_item_id'] : 0;
                            if ($oiId <= 0) throw new \RuntimeException('Missing outdoor_item_id for size');

                            $affected = DB::table('outdoor_items')
                                ->where('id', $oiId)
                                ->update(['size' => $val, 'updated_at' => now()]);

                            Log::info("Updated outdoor_items.size", ['id' => $oiId, 'affected' => $affected]);
                        } elseif ($scope === 'outdoor' && $colIn === 'outdoor_coordinates') {
                            $oiId = isset($c['outdoor_item_id']) ? (int)$c['outdoor_item_id'] : 0;
                            if ($oiId <= 0) throw new \RuntimeException('Missing outdoor_item_id for coordinates');

                            // Parse coordinates
                            $parts = array_map('trim', explode(',', (string)$val));
                            if (count($parts) !== 2) throw new \RuntimeException('Invalid coordinates format');

                            [$lat, $lng] = $parts;
                            if (!is_numeric($lat) || !is_numeric($lng)) throw new \RuntimeException('Coordinates must be numeric');
                            if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) throw new \RuntimeException('Coordinates out of range');

                            $oi = DB::table('outdoor_items')->where('id', $oiId)->first(['billboard_id']);
                            if (!$oi || !$oi->billboard_id) throw new \RuntimeException('No billboard linked');

                            $affected = DB::table('billboards')
                                ->where('id', $oi->billboard_id)
                                ->update([
                                    'gps_latitude' => (string)$lat,
                                    'gps_longitude' => (string)$lng,
                                    'updated_at' => now()
                                ]);

                            Log::info("Updated billboards GPS", ['billboard_id' => $oi->billboard_id, 'affected' => $affected]);
                        } else {
                            // Regular master_files update
                            $affected = DB::table('master_files')
                                ->where('id', $id)
                                ->update([$colIn => $val, 'updated_at' => now()]);

                            Log::info("Updated master_files", ['id' => $id, 'affected' => $affected]);
                        }

                        $ok++;
                    } catch (\Throwable $e) {
                        Log::error("Failed to process change $index", [
                            'change' => $c,
                            'error' => $e->getMessage()
                        ]);

                        $failed[] = [
                            'id' => $c['id'],
                            'column' => $c['column'],
                            'error' => $e->getMessage(),
                        ];
                    }
                }

                DB::commit();
                Log::info('BatchUpdate completed', ['successful' => $ok, 'failed' => count($failed)]);
            } catch (\Throwable $e) {
                DB::rollBack();
                Log::error('BatchUpdate transaction failed', ['error' => $e->getMessage()]);
                return response()->json(['ok' => false, 'message' => $e->getMessage()], 500);
            }

            return response()->json(['ok' => true, 'saved' => $ok, 'failed' => $failed]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('BatchUpdate validation error', ['errors' => $e->errors()]);
            return response()->json(['ok' => false, 'message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Throwable $e) {
            Log::error('Unexpected error in batchUpdate', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request' => $request->all()
            ]);
            return response()->json(['ok' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }
}
