<?php

namespace App\Http\Controllers;

use App\Models\MasterFile;
use App\Models\OutdoorWhiteboard;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Log;
use App\Models\OutdoorItem;



class OutdoorWhiteboardController extends Controller
{
    /**
     * Main whiteboard view.
     * Supports optional `q` search over master_files (company/product/location).
     * Shows only ACTIVE (not completed) items.
     */
   public function index(Request $request)
    {
        $searchRaw = (string) $request->query('q', '');
        // Escape % and _ in LIKE to avoid wildcard surprises
        $search = str_replace(['\\',   '%',  '_'], ['\\\\', '\%', '\_'], $searchRaw);

        // Base MasterFile query with optional LIKE filters
        $mfQuery = MasterFile::query()
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('company',  'like', "%{$search}%")
                    ->orWhere('product', 'like', "%{$search}%")
                    ->orWhere('location','like', "%{$search}%");
                });
            })
            ->with(['outdoorItems' => function ($q) {
                $q->select('id','master_file_id','site','start_date','end_date');
            }])
            ->orderByDesc('created_at');

        // If you list master files on this page, paginate; if not, you can keep it capped
        // $masterFiles = $mfQuery->limit(200)->get();
        $masterFiles = $mfQuery->paginate(50)->withQueryString();

        // Collect all item IDs actually rendered on the page
        $itemIds = $masterFiles->getCollection()
            ->pluck('outdoorItems')->flatten()->pluck('id')->unique()->values();

        // Map existing active whiteboards by outdoor_item_id for prefill
        $existing = collect();
        if ($itemIds->isNotEmpty()) {
            $existing = OutdoorWhiteboard::query()
                ->whereIn('outdoor_item_id', $itemIds)
                ->whereNull('completed_at')
                ->get()
                ->keyBy('outdoor_item_id');
        }

        // Completed badge (global); if you want it to follow the same search scope, apply similar joins/filters
        $completedCount = OutdoorWhiteboard::whereNotNull('completed_at')->count();

        // Table of active whiteboards (joined view) — paginate and avoid column overwrite
        $whiteboards = OutdoorWhiteboard::query()
            ->whereNull('completed_at')
            ->leftJoin('outdoor_items', 'outdoor_items.id', '=', 'outdoor_whiteboards.outdoor_item_id')
            ->leftJoin('master_files', 'master_files.id', '=', 'outdoor_items.master_file_id')
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('master_files.company',  'like', "%{$search}%")
                    ->orWhere('master_files.product', 'like', "%{$search}%")
                    ->orWhere('master_files.location','like', "%{$search}%")
                    ->orWhere('outdoor_items.site',    'like', "%{$search}%");
                });
            })
            ->select([
                'outdoor_whiteboards.*',
                // IMPORTANT: avoid overwriting outdoor_whiteboards.outdoor_item_id
                'outdoor_items.id as oi_id',
                'outdoor_items.site',
                'master_files.company',
                'master_files.product',
                'master_files.location',
            ])
            ->orderByDesc('outdoor_whiteboards.created_at')
            ->paginate(100)
            ->withQueryString();

        // Optional debug logs (won’t explode with pagination)
        Log::info('MF page count: '.$masterFiles->count().' / total: '.$masterFiles->total());
        Log::info('Rendered item IDs: '.$itemIds->count());
        Log::info('Existing WB (active) map: '.$existing->count());
        Log::info('WB table page count: '.$whiteboards->count().' / total: '.$whiteboards->total());

        return view('outdoor.whiteboard', compact(
            'masterFiles',
            'existing',
            'searchRaw',
            'completedCount',
            'whiteboards'
        ));
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
            'outdoor_item_id'  => ['required', Rule::exists('outdoor_items','id')],
            'master_file_id'   => ['nullable', Rule::exists('master_files','id')],
            'client_text'      => ['nullable', 'string', 'max:255'],
            'client_date'      => ['nullable', 'date'],
            'po_text'          => ['nullable', 'string', 'max:255'],
            'po_date'          => ['nullable', 'date'],
            'supplier_text'    => ['nullable', 'string', 'max:255'],
            'supplier_date'    => ['nullable', 'date'],
            'storage_text'     => ['nullable', 'string', 'max:255'],
            'storage_date'     => ['nullable', 'date'],
            'notes'             => ['nullable', 'string'],
        ]);

        if (empty($data['master_file_id'])) {
            $data['master_file_id'] = OutdoorItem::where('id',$data['outdoor_item_id'])
                ->value('master_file_id');
        }

        $wb = OutdoorWhiteboard::updateOrCreate(
            ['outdoor_item_id' => $data['outdoor_item_id']],
            collect($data)->except('outdoor_item_id')->toArray()
        );

        if ($request->wantsJson()) {
            return response()->json([
                'ok' => true,
                'updated_at' => optional($wb->updated_at)->toDateTimeString(),
            ]);
        }

        return back()->with('success','Whiteboard saved.');
    }


    /**
     * Mark a row as completed (sets completed_at = now()).
     * Frontend can call via fetch() and then remove the row from the main table.
     *
     * FIXED: Now validates against master_files instead of outdoor_whiteboards
     * to handle cases where the whiteboard row doesn't exist yet.
     */

    public function completed(Request $request)
    {
        $search = $request->query('q');

        $whiteboards = OutdoorWhiteboard::query()
            ->whereNotNull('outdoor_whiteboards.completed_at')
            ->leftJoin('outdoor_items', 'outdoor_items.id', '=', 'outdoor_whiteboards.outdoor_item_id')
            ->leftJoin('master_files', 'master_files.id', '=', 'outdoor_items.master_file_id')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $like = "%{$search}%";
                    $qq->where('master_files.company', 'like', $like)
                    ->orWhere('master_files.product', 'like', $like)
                    ->orWhere('master_files.location', 'like', $like)
                    ->orWhere('outdoor_items.site', 'like', $like);
                });
            })
            ->orderByDesc('outdoor_whiteboards.completed_at')
            ->select([
                'outdoor_whiteboards.*',
                'outdoor_items.id as outdoor_item_id',
                'outdoor_items.site',
                'outdoor_items.start_date',
                'outdoor_items.end_date',
                'master_files.company',
                'master_files.product',
                'master_files.location',
            ])
            ->paginate(30)
            ->withQueryString(); // keep ?q=... on pagination links

        // (Optional) quick debug
        // Log::info('Completed count page:', ['count' => $whiteboards->count(), 'q' => $search]);

        return view('outdoor.whiteboard-completed', compact('whiteboards', 'search'));
    }


    public function destroy(OutdoorWhiteboard $whiteboard)
    {
        $whiteboard->delete();
        return back()->with('status', 'Outdoor Whiteboard deleted.');
    }
}
