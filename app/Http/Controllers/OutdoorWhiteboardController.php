<?php

namespace App\Http\Controllers;

use App\Models\MasterFile;
use App\Models\OutdoorWhiteboard;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
