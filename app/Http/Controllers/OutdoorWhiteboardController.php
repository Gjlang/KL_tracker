<?php

namespace App\Http\Controllers;

use App\Models\MasterFile;
use App\Models\OutdoorWhiteboard;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;



class OutdoorWhiteboardController extends Controller
{
   public function index(Request $request)
    {
        $search = $request->query('q');

        $masterFiles = MasterFile::query()
            ->when($search, function ($q) use ($search) {
                $q->where('company', 'like', "%{$search}%")
                  ->orWhere('product', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            })
            // ambil outdoor_items minimal kolom penting:
            ->with(['outdoorItems:id,master_file_id,site,start_date,end_date'])
            ->orderByDesc('created_at')
            ->limit(200)
            ->get();

        // map whiteboard existing
        $existing = OutdoorWhiteboard::whereIn('master_file_id', $masterFiles->pluck('id'))
            ->get()
            ->keyBy('master_file_id');

        return view('outdoor.whiteboard', compact('masterFiles', 'existing', 'search'));
    }

    public function exportByProduct(Request $request): StreamedResponse
{
    return $this->exportByProductCsv($request);
}


public function exportByProductCsv(Request $request): StreamedResponse
{
    $filename = 'outdoor-whiteboard_by-product_'.now()->format('Ymd_His').'.csv';

    $queryBase = \App\Models\OutdoorWhiteboard::query()
        ->when($request->get('q'), function ($q, $term) {
            $q->where(function ($qq) use ($term) {
                $qq->where('company', 'like', "%{$term}%")
                   ->orWhere('location', 'like', "%{$term}%")
                   ->orWhere('inv_number', 'like', "%{$term}%")
                   ->orWhere('purchase_order', 'like', "%{$term}%")
                   ->orWhere('product', 'like', "%{$term}%");
            });
        });

    $products = (clone $queryBase)
        ->select('product')->whereNotNull('product')->distinct()->orderBy('product')->pluck('product')->all();

    $headers = [
        'Content-Type'        => 'text/csv; charset=UTF-8',
        'Content-Disposition' => "attachment; filename=\"$filename\"",
        'Cache-Control'       => 'no-store, no-cache',
    ];

    return response()->streamDownload(function () use ($products, $queryBase) {
        $out = fopen('php://output', 'w');
        fwrite($out, "\xEF\xBB\xBF"); // BOM

        foreach ($products as $product) {
            fputcsv($out, ["Product: $product"]);
            fputcsv($out, ['No','Created','INV Number','Purchase Order','Product','Company','Location','Installation','Dismantle','Supplier','Storage']);

            $rows = (clone $queryBase)->where('product', $product)
                ->orderBy('company')->orderBy('location')->orderBy('created_at')
                ->get(['created_at','inv_number','purchase_order','product','company','location','installation','dismantle','supplier','storage']);

            $i = 1;
            foreach ($rows as $r) {
                fputcsv($out, [
                    $i++,
                    optional($r->created_at)->format('Y-m-d'),
                    $r->inv_number,
                    $r->purchase_order,
                    $r->product,
                    $r->company,
                    $r->location,
                    $r->installation,
                    $r->dismantle,
                    $r->supplier,
                    $r->storage,
                ]);
            }

            fputcsv($out, ['']); // separator
        }

        fclose($out);
    }, $filename, $headers);
}

    // Simpan / Update (idempotent per master_file_id)
    public function upsert(Request $request)
    {
        $data = $request->validate([
            'master_file_id' => ['required','exists:master_files,id'],

            'client_text'    => ['nullable','string','max:255'],
            'client_date'    => ['nullable','date'],

            'po_text'        => ['nullable','string','max:255'],
            'po_date'        => ['nullable','date'],

            'supplier_text'  => ['nullable','string','max:255'],
            'supplier_date'  => ['nullable','date'],

            'storage_text'   => ['nullable','string','max:255'],
            'storage_date'   => ['nullable','date'],

            'notes'          => ['nullable','string'],
        ]);

        // unik per master_file_id ⇒ updateOrCreate
        $wb = OutdoorWhiteboard::updateOrCreate(
            ['master_file_id' => $data['master_file_id']],
            $data
        );

        return back()->with('status', 'Outdoor Whiteboard saved.');
    }

    // Hapus (opsional)
    public function destroy(OutdoorWhiteboard $whiteboard)
    {
        $whiteboard->delete();
        return back()->with('status', 'Outdoor Whiteboard deleted.');
    }
}
