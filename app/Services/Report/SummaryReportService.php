<?php

namespace App\Services\Report;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Query\Builder;

class SummaryReportService
{
    public function get(array $filters): array
    {
        $year   = (int)($filters['year'] ?? now()->year);
        $month  = $filters['month'] ?? null;    // 1..12 atau null
        $status = $filters['status'] ?? null;   // 'pending','in-progress','completed','cancelled' atau null

        $cacheKey = "summary:v1:year={$year}:month=".($month ?: 'all').":status=".($status ?: 'all');
        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($year, $month, $status) {
            return [
                'filters'   => compact('year','month','status'),
                'master'    => $this->masterFileCard($year, $month, $status),
                'outdoor'   => $this->outdoorCard($year, $month, $status),
                'kltg'      => $this->kltgCard($year, $month, $status),
                'media'     => $this->mediaCard($year, $month, $status),
                'generated' => now(),
            ];
        });
    }

    /**
     * Get date columns for tables that don't have year/month columns
     */
    private function dateColsFor(string $table): array
    {
        // Peta kolom tanggal per tabel yang tidak punya year/month
        $map = [
            'kltg_coordinator_trackings' => [
                'material_received_date','video_done_date','pending_approval_date','video_approved_date',
                'video_scheduled_date','video_posted_date','article_done_date','article_approved_date',
                'article_scheduled_date','article_posted_date','artwork_reminder_date','artwork_done_date',
                'send_chop_sign_date','chop_sign_approval_date','park_in_server_date',
                'em_date_write','em_date_to_post','em_post_date','created_at',
            ],
            // Tambahkan tabel lain bila perlu...
        ];

        $cols = $map[$table] ?? [];
        // Saring hanya yang benar-benar ada di DB
        return array_values(array_filter($cols, fn($c) => Schema::hasColumn($table, $c)));
    }

    /**
     * Terapkan filter tahun/bulan secara fleksibel:
     * - Jika ada year/month → pakai itu
     * - Jika tidak ada → OR ke semua kolom tanggal (YEAR/MONTH)
     */
    private function applyPeriodFiltersFlexible(Builder $q, string $table, ?int $year, ?int $month): void
    {
        if (!$year && !$month) return;

        if (Schema::hasColumn($table, 'year')) {
            if ($year)  { $q->where("$table.year", $year); }
            if ($month && Schema::hasColumn($table, 'month')) {
                $q->where("$table.month", $month);
            } elseif ($month) {
                // Jika ada year tapi tak ada month, coba created_at
                if (Schema::hasColumn($table, 'created_at')) {
                    $q->whereMonth("$table.created_at", $month);
                }
            }
            return;
        }

        // Tidak ada kolom year → bangun OR di semua kolom tanggal
        $cols = $this->dateColsFor($table);
        if (empty($cols)) {
            // Fallback terakhir: created_at bila ada
            if (Schema::hasColumn($table, 'created_at')) {
                if ($year)  { $q->whereYear("$table.created_at", $year); }
                if ($month) { $q->whereMonth("$table.created_at", $month); }
            }
            return;
        }

        $q->where(function (Builder $or) use ($table, $cols, $year, $month) {
            foreach ($cols as $c) {
                $or->orWhere(function (Builder $b) use ($table, $c, $year, $month) {
                    if ($year)  { $b->whereYear("$table.$c", $year); }
                    if ($month) { $b->whereMonth("$table.$c", $month); }
                });
            }
        });
    }

    private function applyStatusFilter(Builder $q, string $table, ?string $status): void
    {
        if ($status && Schema::hasColumn($table, 'status')) {
            $q->where("$table.status", $status);
        }
    }

    /** ---------- Master File ---------- */
    protected function masterFileCard(int $year, ?int $month, ?string $status): array
    {
        $hasMF = Schema::hasTable('master_files');

        // Cari kolom kategori yang tersedia
        $catCol = null;
        foreach (['category','category_product','product_category','category_type'] as $c) {
            if ($hasMF && Schema::hasColumn('master_files', $c)) { $catCol = $c; break; }
        }

        // "Active companies" = distinct master_file_id yang punya catatan pada salah satu tracking table di tahun tsb
        $activeIds = collect();

        // OUTDOOR
        if (Schema::hasTable('outdoor_coordinator_trackings')) {
            $tbl = 'outdoor_coordinator_trackings';
            $q = DB::table($tbl)->select('master_file_id');
            $this->applyPeriodFiltersFlexible($q, $tbl, $year, $month);
            $this->applyStatusFilter($q, $tbl, $status);
            $activeIds = $activeIds->merge($q->pluck('master_file_id'));
        }

        // KLTG
        if (Schema::hasTable('kltg_coordinator_trackings')) {
            $tbl = 'kltg_coordinator_trackings';
            $q = DB::table($tbl)->select('master_file_id');
            $this->applyPeriodFiltersFlexible($q, $tbl, $year, $month);
            $this->applyStatusFilter($q, $tbl, $status);
            $activeIds = $activeIds->merge($q->pluck('master_file_id'));
        } elseif (Schema::hasTable('kltg_monthly_details')) {
            $tbl = 'kltg_monthly_details';
            $q = DB::table($tbl)->select('master_file_id');
            $this->applyPeriodFiltersFlexible($q, $tbl, $year, $month);
            $activeIds = $activeIds->merge($q->pluck('master_file_id'));
        }

        // MEDIA
        if (Schema::hasTable('media_coordinator_trackings')) {
            $tbl = 'media_coordinator_trackings';
            $q = DB::table($tbl)->select('master_file_id');
            $this->applyPeriodFiltersFlexible($q, $tbl, $year, $month);
            $this->applyStatusFilter($q, $tbl, $status);
            $activeIds = $activeIds->merge($q->pluck('master_file_id'));
        } elseif (Schema::hasTable('media_monthly_details')) {
            $tbl = 'media_monthly_details';
            $q = DB::table($tbl)->select('master_file_id');
            $this->applyPeriodFiltersFlexible($q, $tbl, $year, $month);
            $activeIds = $activeIds->merge($q->pluck('master_file_id'));
        }

        $activeCount = $activeIds->unique()->filter()->count();

        // Breakdown kategori dari master_files (hanya untuk list kategori, tidak filter year)
        $byCategory = [];
        if ($hasMF && $catCol) {
            $byCategory = DB::table('master_files')
                ->select(DB::raw("$catCol as category"), DB::raw('COUNT(*) as total'))
                ->groupBy($catCol)
                ->orderByDesc('total')
                ->get()
                ->map(fn($r)=>['category'=>$r->category ?? 'Unknown', 'total'=>(int)$r->total])
                ->toArray();
        }

        // Distribusi status (gabungan semua tracking yang ada)
        $statusMap = ['pending'=>0,'in-progress'=>0,'completed'=>0,'cancelled'=>0];
        foreach ([
            'outdoor_coordinator_trackings',
            'kltg_coordinator_trackings',
            'kltg_monthly_details',
            'media_coordinator_trackings',
            'media_monthly_details',
        ] as $tbl) {
            if (!Schema::hasTable($tbl) || !Schema::hasColumn($tbl,'status')) continue;
            $q = DB::table($tbl)->select('status', DB::raw('COUNT(*) as c'))->groupBy('status');
            $this->applyPeriodFiltersFlexible($q, $tbl, $year, $month);
            $q->get()->each(function($r) use (&$statusMap) {
                $key = (string)$r->status;
                if (isset($statusMap[$key])) $statusMap[$key] += (int)$r->c;
            });
        }

        return [
            'active_companies' => $activeCount,
            'by_category'      => $byCategory,
            'status_dist'      => $statusMap,
        ];
    }

    /** ---------- Outdoor ---------- */
    protected function outdoorCard(int $year, ?int $month, ?string $status): array
    {
        if (!Schema::hasTable('outdoor_coordinator_trackings')) {
            return ['available'=>false];
        }

        $tbl  = 'outdoor_coordinator_trackings';
        $base = DB::table($tbl);
        $this->applyPeriodFiltersFlexible($base, $tbl, $year, $month);
        $this->applyStatusFilter($base, $tbl, $status);

        $total     = (clone $base)->count();
        $completed = (clone $base)->where('status','completed')->count();

        // Completion rate (bukan on-time)
        $rate = $total ? round($completed / $total * 100) : 0;

        // Issues sederhana: pending + in-progress
        $issues = (clone $base)->whereIn('status',['pending','in-progress'])->count();

        return [
            'available'       => true,
            'active_jobs'     => $total,
            'completed_this'  => $completed,
            'completion_rate' => $rate,
            'issues'          => $issues,
        ];
    }

    /** ---------- KLTG ---------- */
   protected function kltgCard(int $year, ?int $month, ?string $status): array
{
    $table = Schema::hasTable('kltg_coordinator_trackings') ? 'kltg_coordinator_trackings'
           : (Schema::hasTable('kltg_monthly_details') ? 'kltg_monthly_details' : null);

    if (!$table) return ['available'=>false];

    // base query + filter periode & status (status hanya jika kolomnya ada)
    $base = DB::table($table);
    $this->applyPeriodFiltersFlexible($base, $table, $year, $month);
    $this->applyStatusFilter($base, $table, $status);

    $total = (clone $base)->count();

    // ===== Completed =====
    $hasStatus = Schema::hasColumn($table, 'status');
    if ($hasStatus) {
        $completed = (clone $base)->where("$table.status", 'completed')->count();
    } else {
        // Tidak ada kolom status → anggap completed jika ada salah satu tanggal "posted/done"
        $doneCols = array_values(array_filter([
            Schema::hasColumn($table,'video_posted_date')    ? 'video_posted_date'    : null,
            Schema::hasColumn($table,'article_posted_date')  ? 'article_posted_date'  : null,
            Schema::hasColumn($table,'artwork_done_date')    ? 'artwork_done_date'    : null,
            Schema::hasColumn($table,'video_done_date')      ? 'video_done_date'      : null,
            Schema::hasColumn($table,'article_done_date')    ? 'article_done_date'    : null,
            Schema::hasColumn($table,'em_post_date')         ? 'em_post_date'         : null,
        ]));
        if (!empty($doneCols)) {
            $q = (clone $base)->where(function($qq) use ($table, $doneCols) {
                foreach ($doneCols as $c) {
                    $qq->orWhereNotNull("$table.$c");
                }
            });
            $completed = $q->count();
        } else {
            $completed = 0;
        }
    }

    $progress = $total ? round($completed / $total * 100) : 0;

    // ===== Slots filled =====
    $slots = (clone $base)->distinct()->count('master_file_id');

    // ===== Pending approvals =====
    if ($hasStatus) {
        $pending = (clone $base)->whereIn("$table.status", ['pending','in-progress'])->count();
    } else {
        // Heuristik: ada "pending_approval_date" → dianggap pending approval
        if (Schema::hasColumn($table, 'pending_approval_date')) {
            $pending = (clone $base)->whereNotNull("$table.pending_approval_date")->count();
        } else {
            $pending = 0;
        }
    }

    return [
        'available'           => true,
        'production_progress' => $progress,
        'slots_filled'        => $slots,
        'pending_approvals'   => $pending,
    ];
}


    /** ---------- Media Social ---------- */
    protected function mediaCard(int $year, ?int $month, ?string $status): array
    {
        $table = Schema::hasTable('media_coordinator_trackings') ? 'media_coordinator_trackings'
               : (Schema::hasTable('media_monthly_details') ? 'media_monthly_details' : null);

        if (!$table) return ['available'=>false];

        $base = DB::table($table);
        $this->applyPeriodFiltersFlexible($base, $table, $year, $month);
        $this->applyStatusFilter($base, $table, $status);

        $campaigns = (clone $base)->distinct()->count('master_file_id');

        // "Posts this month": kalau ada kolom 'post_count' gunakan sum, kalau tidak pakai total baris
        $posts = 0;
        if (Schema::hasColumn($table,'post_count')) {
            $posts = (clone $base)->sum('post_count');
        } else {
            $posts = (clone $base)->count();
        }

        return [
            'available' => true,
            'campaigns' => $campaigns,
            'posts'     => (int)$posts,
        ];
    }
}
