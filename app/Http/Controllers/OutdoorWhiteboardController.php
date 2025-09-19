<?php

namespace App\Http\Controllers;

use App\Models\MasterFile;
use App\Models\OutdoorWhiteboard;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OutdoorWhiteboardController extends Controller
{
    /**
     * Main whiteboard view.
     * Supports optional `q` search over master_files (company/product/location).
     * Shows only ACTIVE (not completed) items.
     */
   public function index(Request $request)
    {
         $search = $request->query('q');

        // Get IDs of completed master files to exclude
        $completedIds = OutdoorWhiteboard::whereNotNull('completed_at')
            ->pluck('master_file_id')
            ->toArray();

        $masterFiles = MasterFile::query()
            ->whereNotIn('id', $completedIds) // Exclude completed ones
            ->when($search, function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('company', 'like', "%{$search}%")
                    ->orWhere('product', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
                });
            })
            ->with(['outdoorItems:id,master_file_id,site,start_date,end_date'])
            ->orderByDesc('created_at')
            ->limit(200)
            ->get();

        $existing = OutdoorWhiteboard::whereIn('master_file_id', $masterFiles->pluck('id'))
            ->whereNull('completed_at')
            ->get()
            ->keyBy('master_file_id');

        $completedCount = OutdoorWhiteboard::whereNotNull('completed_at')->count();

        return view('outdoor.whiteboard', compact('masterFiles','existing','search','completedCount'));
    }


    public function completed(Request $request)
    {
        $search = $request->query('q');

        $whiteboards = OutdoorWhiteboard::query()
            ->whereNotNull('completed_at')
            ->leftJoin('master_files', 'master_files.id', '=', 'outdoor_whiteboards.master_file_id')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('master_files.company', 'like', "%{$search}%")
                    ->orWhere('master_files.product', 'like', "%{$search}%")
                    ->orWhere('master_files.location', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('completed_at')
            ->select([
                'outdoor_whiteboards.*',
                'master_files.company',
                'master_files.product',
                'master_files.location',
                'master_files.invoice_number',
                'master_files.inv_number',
                'master_files.purchase_order',
                'master_files.installation',
                'master_files.dismantle',
            ])
            ->paginate(30);

        return view('outdoor.whiteboard-completed', compact('whiteboards','search'));
    }

    /**
     * Back-compat alias.
     */
    public function exportByProduct(Request $request): StreamedResponse
    {
        return $this->exportByProductCsv($request);
    }

    /**
     * FIXED: Export by product using a JOIN to master_files.
     * Supports simple `q` search over company/product/location.
     */
    public function exportByProductCsv(Request $request): StreamedResponse
    {
        $filename = 'outdoor-whiteboard_by-product_' . now()->format('Ymd_His') . '.csv';

        // Base query: join master_files to get product/company/location
        $queryBase = OutdoorWhiteboard::query()
            ->leftJoin('master_files', 'master_files.id', '=', 'outdoor_whiteboards.master_file_id')
            ->when($request->get('q'), function ($q, $term) {
                $q->where(function ($qq) use ($term) {
                    $qq->where('master_files.company', 'like', "%{$term}%")
                       ->orWhere('master_files.location', 'like', "%{$term}%")
                       ->orWhere('master_files.product', 'like', "%{$term}%");
                });
            })
            ->select([
                'outdoor_whiteboards.*',
                'master_files.company',
                'master_files.location',
                'master_files.product',
            ]);

        // Distinct list of products (from master_files)
        $products = (clone $queryBase)
            ->whereNotNull('master_files.product')
            ->distinct()
            ->orderBy('master_files.product')
            ->pluck('master_files.product')
            ->all();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control'       => 'no-store, no-cache',
        ];

        return response()->streamDownload(function () use ($products, $queryBase) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM

            foreach ($products as $product) {
                fputcsv($out, ["Product: {$product}"]);
                // Use the columns that actually exist:
                fputcsv($out, [
                    'No',
                    'Created',
                    'Product',
                    'Company',
                    'Location',
                    'Client Text', 'Client Date',
                    'PO Text', 'PO Date',
                    'Supplier Text', 'Supplier Date',
                    'Storage Text', 'Storage Date',
                    'Notes',
                    'Completed At',
                ]);

                $rows = (clone $queryBase)
                    ->where('master_files.product', $product)
                    ->orderBy('master_files.company')
                    ->orderBy('master_files.location')
                    ->orderBy('outdoor_whiteboards.created_at')
                    ->get();

                $i = 1;
                foreach ($rows as $r) {
                    fputcsv($out, [
                        $i++,
                        optional($r->created_at)->format('Y-m-d'),
                        $r->product,
                        $r->company,
                        $r->location,

                        $r->client_text,
                        optional($r->client_date)->format('Y-m-d'),

                        $r->po_text,
                        optional($r->po_date)->format('Y-m-d'),

                        $r->supplier_text,
                        optional($r->supplier_date)->format('Y-m-d'),

                        $r->storage_text,
                        optional($r->storage_date)->format('Y-m-d'),

                        $r->notes,
                        optional($r->completed_at)->format('Y-m-d H:i:s'),
                    ]);
                }

                fputcsv($out, ['']); // separator line
            }

            fclose($out);
        }, $filename, $headers);
    }

    /**
     * AUTOSAVE endpoint.
     * If the request "Accepts JSON" or is fetch/ajax, we return JSON.
     * Otherwise, we fall back to redirect with flash.
     */
    public function upsert(Request $request)
    {
        $data = $request->validate([
            'master_file_id'  => ['required', Rule::exists('master_files', 'id')],
            'client_text'     => ['nullable', 'string', 'max:255'],
            'client_date'     => ['nullable', 'date'],
            'po_text'         => ['nullable', 'string', 'max:255'],
            'po_date'         => ['nullable', 'date'],
            'supplier_text'   => ['nullable', 'string', 'max:255'],
            'supplier_date'   => ['nullable', 'date'],
            'storage_text'    => ['nullable', 'string', 'max:255'],
            'storage_date'    => ['nullable', 'date'],
            'notes'           => ['nullable', 'string'],
        ]);

        $wb = OutdoorWhiteboard::updateOrCreate(
            ['master_file_id' => $data['master_file_id']],
            collect($data)->except('master_file_id')->toArray()
        );

        if ($request->wantsJson()) {
            return response()->json([
                'ok'         => true,
                'updated_at' => optional($wb->updated_at)->toDateTimeString(),
            ]);
        }

        return back()->with('success', 'Whiteboard saved.');
    }

    /**
     * Mark a row as completed (sets completed_at = now()).
     * Frontend can call via fetch() and then remove the row from the main table.
     *
     * FIXED: Now validates against master_files instead of outdoor_whiteboards
     * to handle cases where the whiteboard row doesn't exist yet.
     */
   public function complete(Request $request)
    {
        $data = $request->validate([
            'master_file_id' => ['required', Rule::exists('master_files', 'id')],
        ]);

        $wb = OutdoorWhiteboard::updateOrCreate(
            ['master_file_id' => $data['master_file_id']],
            ['completed_at' => now()]
        );

        // ← get the latest count from DB (authoritative)
        $completedCount = OutdoorWhiteboard::whereNotNull('completed_at')->count();

        if ($request->wantsJson()) {
            return response()->json([
                'ok'              => true,
                'completed_at'    => optional($wb->completed_at)->toDateTimeString(),
                'completed_count' => $completedCount,   // ← send to UI
            ]);
        }

        return back()->with('success', 'Marked as completed.');
    }


    public function destroy(OutdoorWhiteboard $whiteboard)
    {
        $whiteboard->delete();
        return back()->with('status', 'Outdoor Whiteboard deleted.');
    }
}
