<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;
use Illuminate\Support\Str;

class MediaCoordinatorController extends Controller
{
    /** Tab → table name */
    private const TAB_TO_TABLE = [
        'content'  => 'media_content_calendars',
        'editing'  => 'media_artwork_editings',
        'schedule' => 'media_posting_schedulings',
        'report'   => 'media_reports',
        'valueadd' => 'media_value_adds',
    ];

    /** Tab → editable whitelist */
    private const TAB_WHITELIST = [
        'content'  => ['total_artwork_date','pending_date','draft_wa','approved','remarks'],
        'editing'  => ['total_artwork_date','pending_date','draft_wa','approved','remarks'],
        'schedule' => ['total_artwork_date','crm_date','meta_ads_manager_date','tiktok_ig_draft','remarks'],
        'report'   => ['pending_date','completed_date','remarks'],
        'valueadd' => ['quota','completed','remarks'],
    ];

    /** Month labels */
    private const MONTHS = [1=>'Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

    // ===================== INDEX =====================
    public function index(Request $req)
{
    $activeTab = $req->query('tab', 'content');
    if (!array_key_exists($activeTab, self::TAB_TO_TABLE)) {
        $activeTab = 'content';
    }

    $year  = (int)($req->query('year') ?: now()->year);
    $month = $this->normalizeMonth($req->query('month'));

    // ✅ builder (BUKAN ->get())
    $mastersQ = $this->baseMediaMasters()
        ->select('id as master_file_id', 'company', 'client', 'product', 'product_category');

    $rowsByTab = [
        'content'  => collect(),
        'editing'  => collect(),
        'schedule' => collect(),
        'report'   => collect(),
        'valueadd' => collect(),
    ];

    foreach (array_keys(self::TAB_TO_TABLE) as $tab) {
        // ✅ kirim builder
        $rowsByTab[$tab] = $this->querySectionRowsFromMasters($tab, $mastersQ, $year, $month);
    }

    return view('coordinators.media', [
        'activeTab' => $activeTab,
        'rowsByTab' => $rowsByTab,
        'months'    => self::MONTHS,
        'month'     => $month,
        'year'      => $year,
    ]);
}


    private function querySectionRowsFromMasters(string $section, $mastersQ, int $year, ?int $month)
{
    $table = self::TAB_TO_TABLE[$section] . ' as t';

    $select = [
        'mf.master_file_id', 'mf.company', 'mf.client', 'mf.product', 'mf.product_category',
        DB::raw((int)$year.' as year'),
        DB::raw(($month ?? 0).' as month'),
    ];

    switch ($section) {
        case 'content':
        case 'editing':
            array_push($select, 't.total_artwork_date','t.pending_date','t.draft_wa','t.approved','t.remarks');
            break;
        case 'schedule':
            array_push($select, 't.total_artwork_date','t.crm_date','t.meta_ads_manager_date','t.tiktok_ig_draft','t.remarks');
            break;
        case 'report':
            array_push($select, 't.pending_date','t.completed_date','t.remarks');
            break;
        case 'valueadd':
        default:
            array_push($select, 't.quota','t.completed','t.remarks');
    }

    // ✅ masters sebagai subquery builder
    $mfSub = DB::query()->fromSub($mastersQ, 'mf');

    $query = $mfSub->leftJoin($table, function($join) use ($month, $year) {
                $join->on('t.master_file_id', '=', 'mf.master_file_id')
                     ->where('t.year', '=', $year);
                if ($month !== null) {
                    $join->where('t.month', '=', $month);
                }
            })
            ->select($select)
            ->orderBy('mf.company');

    return $query->get();
}


    private function baseMediaMasters()
    {
        // Aturan: product_category mengandung "media" (ci) ATAU product termasuk daftar produk media (opsional)
        $mediaProducts = ['SOCIAL MEDIA','MEDIA','SM','SMM']; // tambahkan kode produk kamu kalau perlu
        return DB::table('master_files')
            ->where(function ($q) use ($mediaProducts) {
                $q->whereRaw('LOWER(product_category) LIKE ?', ['%media%'])
                  ->orWhereIn(DB::raw('UPPER(product)'), $mediaProducts);
            });
    }


    // ===================== UPSERT =====================
    public function upsert(Request $req)
    {
        $v = Validator::make($req->all(), [
            'section'        => 'required|string|in:content,editing,schedule,report,valueadd',
            'master_file_id' => 'required|integer|min:1',
            'year'           => 'required|integer|min:2000|max:2100',
            'month'          => 'required|integer|min:1|max:12',
            'field'          => 'required|string',
            'value'          => 'nullable',
        ]);

        if ($v->fails()) {
            return response()->json(['ok' => false, 'errors' => $v->errors()], 422);
        }

        $section = $req->input('section');
        $table   = self::TAB_TO_TABLE[$section];
        $field   = $req->input('field');

        if (!in_array($field, self::TAB_WHITELIST[$section], true)) {
            return response()->json(['ok' => false, 'message' => 'Field not allowed'], 422);
        }

        $masterFileId = (int)$req->input('master_file_id');
        $year         = (int)$req->input('year');
        $month        = (int)$req->input('month');
        $rawValue     = $req->input('value');

        $value = $this->normalizeFieldValue($section, $field, $rawValue);

        // Upsert
        $now = now();
        // Laravel upsert: values, uniqueBy, update
        DB::table($table)->upsert(
            [[
                'master_file_id' => $masterFileId,
                'year'           => $year,
                'month'          => $month,
                $field           => $value,
                'created_at'     => $now,
                'updated_at'     => $now,
            ]],
            ['master_file_id','year','month'],
            [$field,'updated_at']  // do not touch created_at on update
        );

        return response()->json(['ok' => true]);
    }

    // ===================== EXPORT =====================
    public function export(Request $req): StreamedResponse
{
    $section = $req->query('tab', 'content');
    if (!array_key_exists($section, self::TAB_TO_TABLE)) {
        $section = 'content';
    }

    $year  = (int)($req->query('year') ?: now()->year);
    $month = $this->normalizeMonth($req->query('month'));

    // ✅ builder (BUKAN ->get())
    $mastersQ = $this->baseMediaMasters()
        ->select('id as master_file_id', 'company', 'client', 'product', 'product_category');

    $rows = $this->querySectionRowsFromMasters($section, $mastersQ, $year, $month);

    $filename = "media_{$section}_{$year}-".str_pad((int)($month ?: 0),2,'0',STR_PAD_LEFT).".csv";
    $headers  = $this->csvHeaders($section);

    return response()->streamDownload(function () use ($rows, $headers, $section) {
        $out = fopen('php://output','w');
        fputcsv($out, $headers);
        $i = 0;
        foreach ($rows as $r) {
            $i++;
            fputcsv($out, $this->csvRow($section, $i, $r));
        }
        fclose($out);
    }, $filename, ['Content-Type' => 'text/csv']);
}


    // ===================== Helpers =====================

    private function normalizeMonth($input): ?int
    {
        if ($input === null || $input === '') return null;
        if (is_numeric($input)) {
            $m = (int)$input;
            return ($m >= 1 && $m <= 12) ? $m : null;
        }
        $map = [
            'jan'=>1,'january'=>1,
            'feb'=>2,'february'=>2,
            'mar'=>3,'march'=>3,
            'apr'=>4,'april'=>4,
            'may'=>5,
            'jun'=>6,'june'=>6,
            'jul'=>7,'july'=>7,
            'aug'=>8,'august'=>8,
            'sep'=>9,'sept'=>9,'september'=>9,
            'oct'=>10,'october'=>10,
            'nov'=>11,'november'=>11,
            'dec'=>12,'december'=>12,
        ];
        $k = strtolower(trim((string)$input));
        return $map[$k] ?? null;
    }

    /** Ambil monthlyIds dari media_monthly_details → fallback monthly_details (UPPER(category)='MEDIA'). */
    private function resolveMonthlyIds(int $year, int $month)
    {
        $ids = collect();

        if (Schema::hasTable('media_monthly_details')) {
            $q = DB::table('media_monthly_details')->select('master_file_id')->whereNotNull('master_file_id');

            if (Schema::hasColumn('media_monthly_details','year') && Schema::hasColumn('media_monthly_details','month')) {
                $q->where('year', $year)->where('month', $month);
            } elseif (Schema::hasColumn('media_monthly_details','date')) {
                $q->whereYear('date', $year)->whereMonth('date', $month);
            } else {
                // kolom waktu tidak jelas → kembali kosong (STRICT)
                return collect();
            }

            $ids = $q->distinct()->pluck('master_file_id');
        }
        else if (Schema::hasTable('monthly_details')) {
            $q = DB::table('monthly_details')->select('master_file_id')
                ->whereNotNull('master_file_id')
                ->whereRaw('UPPER(category) = ?', ['MEDIA']);

            if (Schema::hasColumn('monthly_details','year') && Schema::hasColumn('monthly_details','month')) {
                $q->where('year', $year)->where('month', $month);
            } elseif (Schema::hasColumn('monthly_details','date')) {
                $q->whereYear('date', $year)->whereMonth('date', $month);
            } else {
                return collect();
            }

            $ids = $q->distinct()->pluck('master_file_id');
        }

        return $ids->filter()->values();
    }

    /** Buat semua baris kosong (default) untuk setiap tab. */
    private function autoSyncRows($monthlyIds, int $year, int $month): void
    {
        $now = now();
        DB::transaction(function () use ($monthlyIds, $year, $month, $now) {
            foreach (self::TAB_TO_TABLE as $table) {
                $payload = [];
                foreach ($monthlyIds as $mid) {
                    $payload[] = [
                        'master_file_id' => (int)$mid,
                        'year'           => $year,
                        'month'          => $month,
                        'created_at'     => $now,
                        'updated_at'     => $now,
                    ];
                }
                if (!empty($payload)) {
                    DB::table($table)->upsert($payload, ['master_file_id','year','month'], ['updated_at']);
                }
            }
        });
    }

    /** Ambil rows per section + join master_files */
    private function querySectionRows(string $section, $monthlyIds, int $year, int $month)
    {
        $table = self::TAB_TO_TABLE[$section] . ' as t';

        $selectCommon = [
            't.id', 't.master_file_id', 't.year', 't.month',
            'mf.company', 'mf.client', 'mf.product', 'mf.product_category'
        ];

        switch ($section) {
            case 'content':
            case 'editing':
                $select = array_merge($selectCommon, [
                    't.total_artwork_date','t.pending_date','t.draft_wa','t.approved','t.remarks'
                ]);
                break;
            case 'schedule':
                $select = array_merge($selectCommon, [
                    't.total_artwork_date','t.crm_date','t.meta_ads_manager_date','t.tiktok_ig_draft','t.remarks'
                ]);
                break;
            case 'report':
                $select = array_merge($selectCommon, [
                    't.pending_date','t.completed_date','t.remarks'
                ]);
                break;
            case 'valueadd':
            default:
                $select = array_merge($selectCommon, [
                    't.quota','t.completed','t.remarks'
                ]);
        }

        return DB::table($table)
            ->join('master_files as mf', 'mf.id', '=', 't.master_file_id')
            ->whereIn('t.master_file_id', $monthlyIds)
            ->where('t.year', $year)
            ->where('t.month', $month)
            ->orderBy('mf.company')
            ->select($select)
            ->get();
    }

    /** Field normalizer (date / int / text) */
    private function normalizeFieldValue(string $section, string $field, $raw)
    {
        $dateFields = [
            'content'  => ['total_artwork_date','pending_date'],
            'editing'  => ['total_artwork_date','pending_date'],
            'schedule' => ['total_artwork_date','crm_date','meta_ads_manager_date'],
            'report'   => ['pending_date','completed_date'],
            'valueadd' => [], // none
        ];
        $intFields = [
            'content'  => ['draft_wa','approved'],
            'editing'  => ['draft_wa','approved'],
            'schedule' => ['tiktok_ig_draft'],
            'report'   => [],
            'valueadd' => ['completed'],
        ];

        if (in_array($field, $dateFields[$section] ?? [], true)) {
            $raw = trim((string)$raw);
            if ($raw === '') return null;
            try {
                return Carbon::parse($raw)->format('Y-m-d');
            } catch (\Throwable $e) {
                return null;
            }
        }

        if (in_array($field, $intFields[$section] ?? [], true)) {
            $raw = trim((string)$raw);
            if ($raw === '') return null;
            return max(0, (int)$raw);
        }

        // text fallback
        return $raw === '' ? null : trim((string)$raw);
    }

    private function csvHeaders(string $section): array
    {
        switch ($section) {
            case 'content':
                return ['No','Company','Client name','Package(product)','Total artwork (date)','Pending (date)','Draft Wa','Approved','Remarks'];
            case 'editing':
                return ['No','Company','Client name','Package(product)','Total artwork (date)','Pending (date)','Draft Wa','Approved','Remarks'];
            case 'schedule':
                return ['No','Company','Client name','Package(product)','Total artwork (date)','CRM (date)','Meta/Ads Manager (date)','TikTok/IG Draft','Remarks'];
            case 'report':
                return ['No','Company','Client name','Package(product)','Pending (date)','Completed (date)','Remarks'];
            case 'valueadd':
            default:
                return ['No','Company','Client name','Quota','Completed','Remarks'];
        }
    }

    private function csvRow(string $section, int $no, $r): array
    {
        switch ($section) {
            case 'content':
            case 'editing':
                return [
                    $no, $r->company, $r->client, $r->product,
                    $r->total_artwork_date, $r->pending_date, $r->draft_wa, $r->approved, $r->remarks
                ];
            case 'schedule':
                return [
                    $no, $r->company, $r->client, $r->product,
                    $r->total_artwork_date, $r->crm_date, $r->meta_ads_manager_date, $r->tiktok_ig_draft, $r->remarks
                ];
            case 'report':
                return [
                    $no, $r->company, $r->client, $r->product,
                    $r->pending_date, $r->completed_date, $r->remarks
                ];
            case 'valueadd':
            default:
                return [
                    $no, $r->company, $r->client, $r->quota, $r->completed, $r->remarks
                ];
        }
    }
}
