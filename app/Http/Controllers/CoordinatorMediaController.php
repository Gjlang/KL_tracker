<?php

// app/Http/Controllers/CoordinatorMediaController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\MasterFile;

class CoordinatorMediaController extends Controller
{
    public function index(Request $request)
    {
        $rows = MasterFile::query()
            ->select([
                'id as master_file_id',
                'company',
                'client',
                'product',
                'status',
                // Make sure we always have a start_date for the table to show
                DB::raw('COALESCE(`date`, `created_at`) as start_date'),
                // If you have a separate column for finish, map it here; else NULL
                DB::raw('NULL as end_date'),
                // Include if your table has it; otherwise remove this line
                DB::raw('NULL as location')
            ])
            ->where(function ($q) {
                $q->where('product_category', 'Media')
                  ->orWhereRaw('LOWER(product_category) LIKE ?', ['%media%'])
                  ->orWhere('product', 'like', '%FB%')
                  ->orWhere('product', 'like', '%IG%');
            })
            ->orderByRaw('COALESCE(`date`, `created_at`) DESC')
            ->get();

        return view('coordinators.media', compact('rows'));
    }
}
