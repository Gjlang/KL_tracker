<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

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

        // Debug cepat
        $mastersCount = (clone $this->baseMediaMasters())->count();
        Log::info("MEDIA INDEX: masters count = {$mastersCount}, year={$year}, month=".($month ?? 'null'));

        $rowsByTab = [];
        foreach (array_keys(self::TAB_TO_TABLE) as $tab) {
            $rowsByTab[$tab] = $this->querySectionRowsFreshMasters($tab, $year, $month);
            Log::info("MEDIA INDEX: tab={$tab}, rows=".$rowsByTab[$tab]->count());
        }

        return view('coordinators.media', [
            'activeTab' => $activeTab,
            'rowsByTab' => $rowsByTab,
            'months'    => self::MONTHS,
            'month'     => $month,
            'year'      => $year,
        ]);
    }

    /**
     * Ambil daftar master_file_id yang punya job di media_monthly_details
     * untuk (year, month) tertentu.
     * Robust ke data yang kadang kosong kolom year/month: fallback ke YEAR/MONTH(value_date).
     */
    private function monthlyMasterIdsFor(int $year, ?int $month): array
    {
        // Base filter by year (kolom year ATAU value_date)
        $q = DB::table('media_monthly_details')
            ->select('master_file_id')
            ->where(function ($w) use ($year) {
                $w->where('year', $year)
                  ->orWhereRaw('YEAR(COALESCE(value_date, DATE(CONCAT(year,"-01-01")))) = ?', [$year]);
            });

        // Jika month dipilih → harus match bulan tsb (kolom month ATAU value_date)
        if ($month !== null) {
            $q->where(function ($w) use ($month) {
                $w->where('month', $month)
                  ->orWhereRaw('MONTH(COALESCE(value_date, DATE(CONCAT(year,"-", LPAD(month,2,"0"), "-01")))) = ?', [$month]);
            });
        }

        $ids = $q->distinct()->pluck('master_file_id')->all();
        Log::info('MEDIA monthlyMasterIdsFor: found '.count($ids).' ids for year='.$year.' month='.($month ?? 'null'));

        return $ids;
    }

    /**
     * Build fresh masters subquery PER CALL (menghindari builder reuse)
     * + RESTRICT masters ke daftar yang ada di media_monthly_details (STRICT by monthly).
     */
    private function querySectionRowsFreshMasters(string $section, int $year, ?int $month)
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

        // Ambil daftar master yang VALID untuk (year, month) dari media_monthly_details
        // STRICT: kalau month dipilih → hanya yang punya entry di bulan tsb
        // Jika month null → tampilkan semua yg punya entry di tahun tsb (minimal satu bulan)
        $validMasterIds = $this->monthlyMasterIdsFor($year, $month);

        // Jika tidak ada satupun → gak usah lanjut (biar view tampil "no data")
        if (empty($validMasterIds)) {
            return collect();
        }

        // FRESH masters yang hanya media + dibatasi valid master ids
        $mastersFresh = (clone $this->baseMediaMasters())
            ->whereIn('id', $validMasterIds)
            ->select('id as master_file_id', 'company', 'client', 'product', 'product_category');

        // Jadikan subquery "mf", lalu LEFT JOIN ke tabel tab (basis mf sudah strict)
        $mfSub = DB::query()->fromSub($mastersFresh, 'mf');

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
        $mediaProducts = [
            'TIKTOK MANAGEMENT',
            'YOUTUBE MANAGEMENT',
            'FB/IG MANAGEMENT',
            'FB SPONSORED ADS',
            'TIKTOK MANAGEMENT BOOST',
            'GIVEAWAYS/ CONTEST MANAGEMENT',
            'XIAOHONGSHU MANAGEMENT',
        ];

        return DB::table('master_files')
            ->where(function ($q) use ($mediaProducts) {
                // 1) match persis product di whitelist
                $q->whereIn(DB::raw('UPPER(product)'), $mediaProducts)

                // 2) atau kategori mengandung "media" (jaga-jaga kalau ada category Media lain)
                ->orWhereRaw('LOWER(product_category) LIKE ?', ['%media%']);
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

    // Use fresh masters for export as well (sudah strict)
    $rows = $this->querySectionRowsFreshMasters($section, $year, $month);

    $filename = "media_{$section}_{$year}-".str_pad((int)($month ?: 0),2,'0',STR_PAD_LEFT).".csv";
    $headers  = $this->csvHeaders($section);

    return response()->streamDownload(function () use ($rows, $headers, $section) {
        $out = fopen('php://output','w');

        // >>> Tambah BOM supaya Excel baca UTF-8 dengan benar
        fwrite($out, "\xEF\xBB\xBF");

        // Header
        fputcsv($out, $headers);

        // Rows
        $i = 0;
        foreach ($rows as $r) {
            $i++;
            fputcsv($out, $this->csvRow($section, $i, $r));
        }
        fclose($out);
    }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
}

// ===== Helpers: formatters untuk CSV =====
private function fmtDate(?string $s): string
{
    if (!$s) return '';
    try { return Carbon::parse($s)->format('d/m/Y'); } catch (\Throwable $e) { return ''; }
}
private function fmtInt($v): string
{
    if ($v === null || $v === '') return '';
    return (string) max(0, (int) $v);
}
private function fmtText($v): string
{
    $t = trim((string)$v);
    return $t === '' ? '' : $t;
}

// ===== Per-tab row builder (pakai formatter di atas) =====
private function csvRow(string $section, int $no, $r): array
{
    switch ($section) {
        case 'content':
        case 'editing':
            return [
                $no,
                $this->fmtText($r->company ?? ''),
                $this->fmtText($r->client ?? ''),
                $this->fmtText($r->product ?? ''),
                $this->fmtDate($r->total_artwork_date ?? null),
                $this->fmtDate($r->pending_date ?? null),
                $this->fmtInt($r->draft_wa ?? null),
                $this->fmtInt($r->approved ?? null),
                $this->fmtText($r->remarks ?? ''),
            ];

        case 'schedule':
            return [
                $no,
                $this->fmtText($r->company ?? ''),
                $this->fmtText($r->client ?? ''),
                $this->fmtText($r->product ?? ''),
                $this->fmtDate($r->total_artwork_date ?? null),
                $this->fmtDate($r->crm_date ?? null),
                $this->fmtDate($r->meta_ads_manager_date ?? null),
                $this->fmtInt($r->tiktok_ig_draft ?? null),
                $this->fmtText($r->remarks ?? ''),
            ];

        case 'report':
            return [
                $no,
                $this->fmtText($r->company ?? ''),
                $this->fmtText($r->client ?? ''),
                $this->fmtText($r->product ?? ''),
                $this->fmtDate($r->pending_date ?? null),
                $this->fmtDate($r->completed_date ?? null),
                $this->fmtText($r->remarks ?? ''),
            ];

        case 'valueadd':
        default:
            return [
                $no,
                $this->fmtText($r->company ?? ''),
                $this->fmtText($r->client ?? ''),
                $this->fmtInt($r->quota ?? null),
                $this->fmtInt($r->completed ?? null),
                $this->fmtText($r->remarks ?? ''),
            ];
    }
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
}
