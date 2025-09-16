<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;
use Illuminate\Support\Facades\Log;


class OutdoorInlineController extends Controller
{
    public function update(\Illuminate\Http\Request $request)
{
    try {
        $validated = $request->validate([
            'id'              => ['required','integer'],   // master_file_id
            'outdoor_item_id' => ['required','integer'],   // outdoor_items.id
            'column'          => ['required','string'],
            'value'           => ['nullable','string'],
        ]);

        // UI -> DB map
        $uiToDb = [
            'outdoor_size'             => 'size',
            'outdoor_district_council' => 'district_council',
            'outdoor_coordinates'      => 'coordinates',
        ];
        // Accept DB keys directly too
        $dbKeys = array_values($uiToDb);

        $col   = $validated['column'];
        $value = $validated['value'] ?? null;

        // Determine final DB column
        if (isset($uiToDb[$col])) {
            $dbCol = $uiToDb[$col];              // UI key provided
        } elseif (in_array($col, $dbKeys, true)) {
            $dbCol = $col;                        // DB key provided
        } else {
            $allowed = implode(', ', array_merge(array_keys($uiToDb), $dbKeys));
            return response()->json([
                'ok' => false,
                'message' => "Column not allowed: {$col}. Allowed: {$allowed}",
            ], 422);
        }

        // Ensure the row exists for both IDs
        $exists = DB::table('outdoor_items')
            ->where('master_file_id', $validated['id'])
            ->where('id', $validated['outdoor_item_id'])
            ->exists();

        if (!$exists) {
            return response()->json([
                'ok' => false,
                'message' => "Outdoor item not found for master_file_id={$validated['id']} & outdoor_item_id={$validated['outdoor_item_id']}.",
            ], 422);
        }

        $changed = DB::table('outdoor_items')
            ->where('master_file_id', $validated['id'])
            ->where('id', $validated['outdoor_item_id'])
            ->update([
                $dbCol      => $value,
                'updated_at'=> now(),
            ]);

        return response()->json([
            'ok'      => true,
            'changed' => $changed,
            'message' => $changed ? 'Saved' : 'No row changed',
        ]);
    } catch (\Illuminate\Validation\ValidationException $ve) {
        $msg = collect($ve->errors())->flatten()->first() ?: 'Validation failed';
        return response()->json(['ok' => false, 'message' => $msg], 422);
    } catch (\Throwable $e) {
        Log::error('Outdoor inline update error', ['err' => $e->getMessage(), 'payload' => $request->all()]);
        return response()->json(['ok' => false, 'message' => 'Server error'], 500);
    }
}

}
