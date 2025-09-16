<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ClienteleController extends Controller
{
    /**
     * Inline update for cells from master_files.
     * Expected payload: { id, column, value, scope: 'outdoor'|'kltg', outdoor_item_id? }
     */
    public function inlineUpdate(Request $request)
    {
        try {
            // 1) Validate + pull raw value (avoid ConvertEmptyStringsToNull surprise)
            $data = $request->validate([
                'id'     => 'required|integer|min:1',
                'column' => 'required|string',
                'value'  => 'nullable',                 // keep raw
                'scope'  => 'nullable|in:outdoor,kltg',
                'outdoor_item_id' => 'nullable|integer|min:1', // Add validation for outdoor_item_id
            ]);

            $raw = $request->all();
            $col = $data['column'];
            $id  = (int)$data['id'];
            $scope = $data['scope'] ?? null;

            // Log the incoming request for debugging
            Log::info('InlineUpdate Request', [
                'id' => $id,
                'column' => $col,
                'raw_value' => $raw['value'] ?? 'NOT_SET',
                'scope' => $scope,
                'outdoor_item_id' => $raw['outdoor_item_id'] ?? 'NOT_SET'
            ]);

            // ---- WHITELISTS ----
            $allowedBase = [
                'month','date','company','product','product_category','location','traffic','duration',
                'amount','status','remarks','client','sales_person','barter',
                'date_finish','job_number','artwork','invoice_date','invoice_number',
                'contact_number','email',
            ];
            $allowedOutdoor = ['outdoor_size','outdoor_district_council','outdoor_coordinates'];
            $allowedKltg    = [
                'kltg_industry','kltg_x','kltg_edition','kltg_material_cbp','kltg_print',
                'kltg_article','kltg_video','kltg_leaderboard','kltg_qr_code','kltg_blog','kltg_em',
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

            // block empty for NOT NULL columns (adjust list if needed)
            $notNullable = ['product','company','client'];
            if (in_array($col, $notNullable, true) && ($value === null || trim((string)$value) === '')) {
                Log::warning('NOT NULL column cannot be empty', ['column' => $col, 'value' => $value]);
                return response()->json(['ok' => false, 'message' => ucfirst($col).' cannot be empty.'], 422);
            }

            // If value comes as null because of empty string, and the column allows blank, store ''
            if ($value === null && !in_array($col, $notNullable, true)) {
                $value = '';
            }

            // Type helpers
            $ymdCols      = ['date_finish','invoice_date']; // DATE columns
            $freeDateLike = ['date'];                       // VARCHAR date-like
            $amountCols   = ['amount'];

            // Store original value for logging
            $originalValue = $value;

            // FIXED: Better amount handling
            if (in_array($col, $amountCols, true)) {
                if ($value === '' || $value === null) {
                    $value = null; // Allow null for amount
                } else {
                    // Clean the value first (remove commas, spaces)
                    $cleanValue = str_replace([',', ' '], '', (string)$value);
                    if (is_numeric($cleanValue)) {
                        $value = (float)$cleanValue;
                    } else {
                        Log::warning('Invalid amount value', ['column' => $col, 'value' => $value]);
                        return response()->json(['ok' => false, 'message' => 'Amount must be a valid number.'], 422);
                    }
                }
            }

            // FIXED: Better date handling with error catching
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
                    // Keep original value if parsing fails for free date
                }
            }

            Log::info('Value after normalization', [
                'column' => $col,
                'original' => $originalValue,
                'normalized' => $value
            ]);

            // Check if record exists first
            $recordExists = DB::table('master_files')->where('id', $id)->exists();
            if (!$recordExists) {
                Log::warning('Record not found', ['id' => $id]);
                return response()->json(['ok' => false, 'message' => 'Record not found.'], 404);
            }

            // Get current value to compare
            $currentRecord = DB::table('master_files')->where('id', $id)->first([$col]);
            $currentValue = $currentRecord ? $currentRecord->$col : null;

            Log::info('Comparison', [
                'current_value' => $currentValue,
                'new_value' => $value,
                'are_same' => $currentValue === $value
            ]);

            // 3) Route outdoor columns to outdoor_items table
            $outdoorMap = [
                'outdoor_size'             => 'size',
                'outdoor_district_council' => 'district_council',
                'outdoor_coordinates'      => 'coordinates',
            ];

            if ($scope === 'outdoor' && array_key_exists($col, $outdoorMap)) {
                $outdoorColumn = $outdoorMap[$col];

                // New: require explicit outdoor_item_id for OUTDOOR edits
                $oiId = isset($raw['outdoor_item_id']) ? (int)$raw['outdoor_item_id'] : 0;
                if ($oiId <= 0) {
                    Log::warning('Missing outdoor_item_id for OUTDOOR inline update', [
                        'master_file_id' => $id, 'column' => $col,
                    ]);
                    return response()->json(['ok' => false, 'message' => 'Missing outdoor_item_id for OUTDOOR edit.'], 422);
                }

                // Optional sanity check: ensure this outdoor item belongs to the same master_file (defense-in-depth)
                $belongs = DB::table('outdoor_items')
                    ->where('id', $oiId)
                    ->where('master_file_id', $id)
                    ->exists();
                if (!$belongs) {
                    Log::warning('outdoor_item_id does not belong to master_file_id', [
                        'outdoor_item_id' => $oiId, 'master_file_id' => $id,
                    ]);
                    return response()->json(['ok' => false, 'message' => 'Outdoor item mismatch.'], 422);
                }

                $affected = DB::table('outdoor_items')
                    ->where('id', $oiId)
                    ->update([$outdoorColumn => $value, 'updated_at' => now()]);

                Log::info('Updated outdoor_items by id (inline)', [
                    'outdoor_item_id' => $oiId,
                    'column' => $outdoorColumn,
                    'value' => $value,
                    'affected_rows' => $affected
                ]);

                if ($affected === 0) {
                    return response()->json(['ok' => false, 'message' => 'No row changed.'], 200);
                }

                return response()->json([
                    'ok' => true,
                    'affected' => (int)$affected,
                    'message' => 'Updated successfully'
                ]);
            } else {
                // Regular update to master_files table
                $affected = DB::table('master_files')
                    ->where('id', $id)
                    ->update([$col => $value, 'updated_at' => now()]);

                Log::info('Updated master_files', [
                    'id' => $id,
                    'column' => $col,
                    'value' => $value,
                    'affected_rows' => $affected
                ]);
            }

            // If same value → 0 rows affected; still OK
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
                // Outdoor (optional; only for oi.* columns)
                'changes.*.outdoor_item_id' => 'nullable|integer|exists:outdoor_items,id',
            ]);

            // Lists reused from inlineUpdate
            $boolCols   = []; // (none in your KLTG/outdoor set right now; add if you use tinyint flags)
            $amountCols = ['amount'];
            $ymdCols    = ['date_finish','invoice_date'];
            $freeDateLike = ['date','invoice_number_date']; // 'date' is varchar in your schema

            // Define NOT NULL columns
            $notNullable = ['product', 'company', 'client'];

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
                            throw new \RuntimeException(ucfirst($colIn).' cannot be empty.');
                        }

                        // If null, convert to empty string for nullable columns
                        if ($val === null && !in_array($colIn, $notNullable, true)) {
                            $val = '';
                        }

                        // Normalize value like inlineUpdate
                        if (in_array($colIn, $boolCols, true)) {
                            $val = (in_array($val, [1,'1',true,'true','on','yes'], true)) ? 1 : 0;
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
                            try { $val = Carbon::parse($val)->format('n/j/y'); } catch (\Throwable $e) { /* leave as is */ }
                        }

                        if ($scope === 'outdoor' && isset($outdoorMap[$colIn])) {
                            // update outdoor_items
                            $oiId = isset($c['outdoor_item_id']) ? (int)$c['outdoor_item_id'] : 0;
                            if ($oiId <= 0) throw new \RuntimeException('Missing outdoor_item_id for column '.$colIn);

                            $affected = DB::table('outdoor_items')
                                ->where('id', $oiId)
                                ->update([$outdoorMap[$colIn]['col'] => $val, 'updated_at' => now()]);

                            Log::info("Updated outdoor_items", ['id' => $oiId, 'affected' => $affected]);
                        } else {
                            // update master_files
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
