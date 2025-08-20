<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterFile;
use App\Models\KltgCoordinatorList;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class KltgCoordinatorController extends Controller
{

    public function store(Request $request)
    {
        // Backward-compat: route('coordinator.kltg.store') will behave like upsert
        return $this->upsert($request);
    }
    /** Canonical subcategories stored in the SAME table */
    private const SUBCATS = ['KLTG','Video','Article','LB','EM'];

    /** Normalize any input (video/VIDEO) → Canonical ("Video") */
    private function normalizeSubcat(?string $s): string
    {
        $map = [
            'kltg' => 'KLTG', 'video' => 'Video', 'article' => 'Article',
            'lb'   => 'LB',   'em'    => 'EM',
        ];
        $k = strtolower(trim((string)($s ?? '')));
        return $map[$k] ?? 'KLTG';
    }

    /** UI tab token -> stored value in DB */
    private function tabToStored(string $tab): string
{
    // Your database ENUM is: 'print','video','article','lb','em'
    // So we store exactly these values
    return match(strtolower(trim($tab))) {
        'print' => 'print',
        'video' => 'video',
        'article' => 'article',
        'lb' => 'lb',
        'em' => 'em',
        default => 'print',
    };
}

    /** Stored value in DB -> UI tab token */
    private function storedToTab(string $stored): string
{
    // Since DB stores the same as UI tabs, just return as-is
    return match(trim($stored)) {
        'print' => 'print',
        'video' => 'video',
        'article' => 'article',
        'lb' => 'lb',
        'em' => 'em',
        default => 'print',
    };
}


     public function index(Request $request)
    {
        $activeTab = $request->get('tab', 'print'); // default KLTG/Print
        $month     = $request->get('month');        // 1..12 or null
        $year      = $request->get('year');         // 4-digit or null

        $rows = MasterFile::query()
            ->where(function ($q) {
                $q->where('product_category', 'KLTG')
                  ->orWhereRaw('LOWER(product_category) LIKE ?', ['%kltg%']);
            })
            ->when($month, function ($q) use ($month) {
                $q->whereMonth(DB::raw('COALESCE(`date`, `created_at`)'), $month);
            })
            ->when($year, function ($q) use ($year) {
                $q->whereYear(DB::raw('COALESCE(`date`, `created_at`)'), $year);
            })
            ->select([
                'id',
                'date',
                'company as company_name',
                'client',
                DB::raw("COALESCE(NULLIF(product,''), '') as mf_title")
            ])
            ->orderByRaw('COALESCE(`date`, `created_at`) DESC')
            ->get();

        $ids = $rows->pluck('id')->all();

        $existing = KltgCoordinatorList::query()
            ->whereIn('master_file_id', $ids)
            ->get()
            ->keyBy(fn($m) => "{$m->master_file_id}|".$this->storedToTab($m->subcategory));

        // column metadata per tab
        $columns = [
            'print' => [
                ['key'=>'title','label'=>'Title','type'=>'text'],
                ['key'=>'client_bp','label'=>'Client/BP','type'=>'text'],
                ['key'=>'x','label'=>'X (text)','type'=>'text'],
                ['key'=>'edition','label'=>'Edition','type'=>'text'],
                ['key'=>'publication','label'=>'Publication','type'=>'text'],
                ['key'=>'artwork_party','label'=>'Artwork (BP/Client)','type'=>'text'],
                ['key'=>'artwork_reminder_date','label'=>'Artwork Reminder','type'=>'date'],
                ['key'=>'material_received_date','label'=>'Material Received','type'=>'date'],
                ['key'=>'artwork_done_date','label'=>'Artwork Done','type'=>'date'],
                ['key'=>'send_chop_sign_date','label'=>'Send Chop & Sign','type'=>'date'],
                ['key'=>'chop_sign_approval_date','label'=>'Chop & Sign Approval','type'=>'date'],
                ['key'=>'park_in_server_date','label'=>'Park in file server','type'=>'date'],
            ],
            'video' => [
                ['key'=>'title','label'=>'Title','type'=>'text'],
                ['key'=>'client_bp','label'=>'Client/BP','type'=>'text'],
                ['key'=>'x','label'=>'X (text)','type'=>'text'],
                ['key'=>'material_reminder_text','label'=>'Material Reminder','type'=>'text'],
                ['key'=>'material_received_date','label'=>'Material Received','type'=>'date'],
                ['key'=>'video_done_date','label'=>'Video Done','type'=>'date'],
                ['key'=>'pending_approval_date','label'=>'Pending Approval','type'=>'date'],
                ['key'=>'video_scheduled_date','label'=>'Video Scheduled','type'=>'date'],
                ['key'=>'video_posted_date','label'=>'Video Posted','type'=>'date'],
                ['key'=>'post_link','label'=>'Post Link','type'=>'text'],
            ],
            'lb' => [
                ['key'=>'title','label'=>'Title','type'=>'text'],
                ['key'=>'client_bp','label'=>'Client/BP','type'=>'text'],
                ['key'=>'x','label'=>'X (text)','type'=>'text'],
                ['key'=>'material_reminder_text','label'=>'Material Reminder','type'=>'text'],
                ['key'=>'material_received_date','label'=>'Material Received','type'=>'date'],
                ['key'=>'video_done_date','label'=>'Video Done','type'=>'date'],
                ['key'=>'pending_approval_date','label'=>'Pending Approval','type'=>'date'],
                ['key'=>'video_approved_date','label'=>'Video Approved','type'=>'date'],
                ['key'=>'video_scheduled_date','label'=>'Video Scheduled','type'=>'date'],
                ['key'=>'video_posted_date','label'=>'Video Posted','type'=>'date'],
                ['key'=>'post_link','label'=>'Post Link','type'=>'text'],
            ],
            'article' => [
                ['key'=>'title','label'=>'Title','type'=>'text'],
                ['key'=>'client_bp','label'=>'Client/BP','type'=>'text'],
                ['key'=>'x','label'=>'X (text)','type'=>'text'],
                ['key'=>'material_reminder_text','label'=>'Material Reminder','type'=>'text'],
                ['key'=>'material_received_date','label'=>'Material Received','type'=>'date'],
                ['key'=>'article_done_date','label'=>'Article Done','type'=>'date'],
                ['key'=>'pending_approval_date','label'=>'Pending Approval','type'=>'date'],
                ['key'=>'article_approved_date','label'=>'Article Approved','type'=>'date'],
                ['key'=>'article_scheduled_date','label'=>'Article Scheduled','type'=>'date'],
                ['key'=>'article_posted_date','label'=>'Article Posted','type'=>'date'],
                ['key'=>'post_link','label'=>'Post Link','type'=>'text'],
            ],
            'em' => [
                ['key'=>'client_bp','label'=>'Clients (from master_files)','type'=>'text'],
                ['key'=>'em_date_write','label'=>'Date Write','type'=>'date'],
                ['key'=>'em_date_to_post','label'=>'Date to post','type'=>'date'],
                ['key'=>'em_post_date','label'=>'Post date','type'=>'date'],
                ['key'=>'em_qty','label'=>'EM-qty','type'=>'text'],
                ['key'=>'blog_link','label'=>'Blog Link','type'=>'text'],
            ],
        ];

        return view('coordinators.kltg', compact('rows','existing','columns','activeTab','month','year'));
    }

    /**
     * UPSERT: create or update ONE row per (master_file_id, subcategory)
     * Accepts many fields at once (your current pattern).
     */
     public function upsert(Request $request)
{
    Log::info('KLTG Upsert Request:', $request->all());

    $tab = strtolower($request->input('subcategory'));
    $storedSub = $this->tabToStored($tab); // Now stores: 'print', 'video', etc.

    $validated = $request->validate([
        'master_file_id' => ['required','integer', Rule::exists('master_files','id')],
        'subcategory'    => ['required', Rule::in(['print','video','article','lb','em'])],
        'field'          => ['required','string'],
        'value'          => ['nullable'],
    ]);

    // MATCH YOUR ACTUAL DATABASE COLUMN NAMES
    $aliases = [
        // Map UI field names to your actual DB column names
        'artwork_party' => 'artwork_party',  // DB column exists
        'artwork_reminder_date' => 'artwork_reminder_date',  // DB column exists
        'material_received_date' => 'material_received_date',  // DB column exists
        'artwork_done_date' => 'artwork_done_date',  // DB column exists
        'send_chop_sign_date' => 'send_chop_sign_date',  // DB column exists
        'chop_sign_approval_date' => 'chop_sign_approval_date',  // DB column exists
        'park_in_server_date' => 'park_in_server_date',  // DB column exists

        // Add any other aliases you need
    ];

    $field = $validated['field'];
    if (isset($aliases[$field])) {
        $field = $aliases[$field];
    }

    // ALLOW FIELDS THAT ACTUALLY EXIST IN YOUR DATABASE
    $allow = [
        'print' => [
            'title','client_bp','x','edition','publication','artwork_party',
            'artwork_reminder_date','artwork_done_date','send_chop_sign_date',
            'chop_sign_approval_date','park_in_server_date',
        ],
        'video' => [
            'title','client_bp','x','material_reminder_text','material_received_date',
            'video_done_date','pending_approval_date','video_scheduled_date',
            'video_posted_date','post_link'
        ],
        'lb' => [
            'title','client_bp','x','material_reminder_text','material_received_date',
            'video_done_date','pending_approval_date','video_approved_date',
            'video_scheduled_date','video_posted_date','post_link'
        ],
        'article' => [
            'title','client_bp','x','material_reminder_text','material_received_date',
            'article_done_date','pending_approval_date','article_approved_date',
            'article_scheduled_date','article_posted_date','post_link'
        ],
        'em' => [
            'client_bp','em_date_write','em_date_to_post','em_post_date',
            'em_qty','blog_link'
        ],
    ];

    $sub = $validated['subcategory'];

    if (!in_array($field, $allow[$sub], true)) {
        Log::error("Invalid field for subcategory", [
            'field' => $field,
            'subcategory' => $sub,
            'allowed' => $allow[$sub]
        ]);
        abort(422, "The selected field key '{$field}' is invalid for subcategory '{$sub}'");
    }

    $value = $validated['value'];

    // Handle dates - your DB columns are already DATE type
    $dateFields = [
        'material_received_date','video_done_date','pending_approval_date',
        'video_approved_date','video_scheduled_date','video_posted_date',
        'article_done_date','article_approved_date','article_scheduled_date',
        'article_posted_date','artwork_reminder_date','artwork_done_date',
        'send_chop_sign_date','chop_sign_approval_date','park_in_server_date',
        'em_date_write','em_date_to_post','em_post_date',
    ];

    if (in_array($field, $dateFields, true)) {
        if ($value && $value !== '') {
            try {
                $value = date('Y-m-d', strtotime($value));
            } catch (\Exception $e) {
                $value = null;
            }
        } else {
            $value = null;
        }
    }

    if ($value === '') $value = null;

    Log::info('About to save:', [
        'master_file_id' => (int)$validated['master_file_id'],
        'subcategory' => $storedSub,  // Now: 'print', 'video', etc.
        'field' => $field,
        'value' => $value
    ]);

    try {
        $row = KltgCoordinatorList::updateOrCreate(
            [
                'master_file_id' => (int)$validated['master_file_id'],
                'subcategory' => $storedSub  // Store: 'print', 'video', etc.
            ],
            [$field => $value]
        );

        Log::info('Successfully saved:', ['id' => $row->id]);

        return response()->json([
            'ok' => true,
            'id' => $row->id,
            'updated_at' => $row->updated_at?->toDateTimeString(),
            'stored' => compact('field','value')
        ]);

    } catch (\Exception $e) {
        Log::error('Failed to save KLTG data:', [
            'error' => $e->getMessage(),
            'master_file_id' => $validated['master_file_id'],
            'subcategory' => $storedSub,
            'field' => $field,
            'value' => $value
        ]);

        return response()->json([
            'ok' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}

    /** Masters that DON'T yet have a coordinator row for the selected subcategory */
    public function getEligibleMasterFiles(Request $request)
    {
        $activeSubcat = $this->normalizeSubcat($request->get('subcategory'));

        $eligible = MasterFile::query()
            ->where(function($q) {
                $q->where('product_category', 'KLTG')
                  ->orWhere('product_category', 'like', '%KLTG%');
            })
            ->whereNotExists(function ($q) use ($activeSubcat) {
                $q->select(DB::raw(1))
                  ->from('kltg_coordinator_lists as k')
                  ->whereColumn('k.master_file_id', 'master_files.id')
                  ->where('k.subcategory', $activeSubcat);
            })
            ->orderBy('created_at','desc')
            ->limit(100)
            ->get(['id','company','product','month','created_at']);

        return response()->json(['eligible' => $eligible, 'subcategory' => $activeSubcat]);
    }

    /** CSV export scoped by month + working/completed + subcategory */
    public function export(Request $request)
    {
        $month      = $request->get('month');
        $working    = $request->get('working'); // 'working'|'completed'|null
        $activeSubcat = $this->normalizeSubcat($request->get('subcategory'));

        $query = KltgCoordinatorList::with('masterFile')
            ->where('subcategory', $activeSubcat);

        if ($month) {
            $query->whereHas('masterFile', function($q) use ($month) {
                $q->where('month', $month);
            });
        }

        if ($working === 'working') {
            $query->where(function($q) {
                $q->whereNull('park_in_file_server')
                  ->orWhere('park_in_file_server', '');
            });
        } elseif ($working === 'completed') {
            $query->whereNotNull('park_in_file_server')
                  ->where('park_in_file_server', '!=', '');
        }

        $lists = $query->orderBy('created_at', 'desc')->get();

        $filename = 'kltg_coordinator_' . strtolower($activeSubcat) . '_' . now()->format('Y_m_d_H_i_s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($lists) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'ID','Subcategory','Company','Title','X','Edition','Publication','Artwork BP/Client',
                'Artwork Reminder','Material Record','Send Chop & Sign','Chop & Sign Approval',
                'Park in File Server','Created At','Updated At'
            ]);

            foreach ($lists as $list) {
                fputcsv($file, [
                    $list->id,
                    $list->subcategory,
                    $list->masterFile->company ?? $list->company_snapshot,
                    $list->masterFile->product ?? $list->title_snapshot,
                    $list->x,
                    $list->edition,
                    $list->publication,
                    $list->artwork_bp_client,
                    optional($list->artwork_reminder)->format('Y-m-d'),
                    optional($list->material_record)->format('Y-m-d'),
                    optional($list->send_chop_sign)->format('Y-m-d'),
                    optional($list->chop_sign_approval)->format('Y-m-d'),
                    optional($list->park_in_file_server)->format('Y-m-d'),
                    $list->created_at,
                    $list->updated_at,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
