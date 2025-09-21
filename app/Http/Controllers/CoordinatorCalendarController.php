<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;


class CoordinatorCalendarController extends Controller
{
    // Colors per module
    private const COLORS = [
        'outdoor' => '#22255b',
        'media'   => '#4bbbed',
        'kltg'    => '#d33831',
    ];

    // Date columns to emit as events (all non-null)
    private const MEDIA_DATE_COLS = [
        'artwork_reminder','material_record','artwork_done','send_chop_sign','chop_sign_approval',
        'em_date_write','em_date_to_post','em_post_date',
        'video_done','pending_approval','video_approved','video_scheduled','video_posted',
        'article_done','article_approved','article_scheduled','article_posted',
    ];

    private const KLTG_DATE_COLS = [
        'site_date','payment_date','material_date','artwork_date',
        'received_approval','sent_to_printer','collection_printer',
        'installation','dismantle','next_follow_up',
    ];

    private const OUTDOOR_DATE_COLS = [
        'total_artwork_date','pending_date',
    ];

    public function index(Request $request)
    {
        // Simple Blade that mounts FullCalendar and filter controls.
        return view('calendar.coordinators');
    }

    public function events(Request $request)
    {
        try {
            // FullCalendar passes ISO timestamps. Convert to DATE strings.
            $startRaw = $request->query('start');
            $endRaw   = $request->query('end');

            $start = $this->toDateString($startRaw); // 'YYYY-MM-DD' or null
            $end   = $this->toDateString($endRaw);

            $module    = strtolower((string)$request->query('module', '')); // '', 'outdoor','media','kltg'
            $year      = trim((string)$request->query('year', ''));
            $month     = trim((string)$request->query('month', '')); // 1..12
            $companyQ  = trim((string)$request->query('company', ''));
            $milestone = trim((string)$request->query('milestone', ''));

            $events = [];

            if ($module === '' || $module === 'media') {
                $events = array_merge($events, $this->fetchMediaCoordinatorEvents(
                    $start, $end, $year, $month, $companyQ, $milestone
                ));
            }
            if ($module === '' || $module === 'kltg') {
                $events = array_merge($events, $this->fetchKltgEvents(
                    $start, $end, $year, $month, $companyQ, $milestone
                ));
            }
            if ($module === '' || $module === 'outdoor') {
                $events = array_merge($events, $this->fetchOutdoorEvents(
                    $start, $end, $year, $month, $companyQ, $milestone
                ));
            }

            return response()->json($events);
        } catch (\Throwable $e) {
            // Return helpful payload instead of a silent 500
            return response()->json([
                'error' => 'Failed to load coordinator events',
                'message' => $e->getMessage(),
                'type' => class_basename($e),
                'line' => $e->getLine(),
            ], 500);
        }
    }

    /** Return all DATE/DATETIME/TIMESTAMP columns for a table, minus a blacklist */
    private function dateColumns(string $table, array $blacklist = []): array
    {
        try {
            $rows = DB::select("
                SELECT COLUMN_NAME
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = ?
                  AND DATA_TYPE IN ('date','datetime','timestamp')
            ", [$table]);
            $cols = array_map(fn($r) => $r->COLUMN_NAME, $rows);
            return array_values(array_diff($cols, $blacklist));
        } catch (\Throwable $e) {
            // Fallback: if INFORMATION_SCHEMA is restricted, just use what we can
            $cols = Schema::getColumnListing($table);
            // crude filter: keep names that look like dates
            $guess = array_filter($cols, fn($c) => str_contains($c,'date') || str_contains($c,'_at'));
            return array_values(array_diff($guess, $blacklist));
        }
    }

    /** Safer LIKE input */
    private function escapeLike(string $s): string
    {
        return addcslashes($s, '%_\\');
    }

    /** Return only the columns that really exist on $table */
    private function existingCols(string $table, array $candidates): array
    {
        try {
            $cols = Schema::getColumnListing($table);
            return array_values(array_intersect($candidates, $cols));
        } catch (\Throwable $e) {
            // If listing fails (e.g., no permission), keep original to avoid hard crash
            return $candidates;
        }
    }

    /** Check if a single column exists (safe) */
    private function colExists(string $table, string $col): bool
    {
        try { return Schema::hasColumn($table, $col); }
        catch (\Throwable $e) { return true; } // fail open
    }

    private function toDateString($iso)
    {
        if (!$iso) return null;
        // Accept '2025-09-01', or '2025-09-01T00:00:00Z', etc.
        if (preg_match('/^\d{4}-\d{2}-\d{2}/', $iso, $m)) {
            return $m[0]; // first 10 chars YYYY-MM-DD
        }
        try {
            return \Illuminate\Support\Carbon::parse($iso)->toDateString();
        } catch (\Throwable $e) {
            return null;
        }
    }

    /** MEDIA */
    private function fetchMediaCoordinatorEvents($start, $end, $year, $month, $companyQ, $milestone)
    {
        // If your table name differs, adjust here:
        $tbl = 'media_coordinator_trackings';

        $q = DB::table($tbl . ' as m')
            ->select('m.*')
            ->when($year !== '', fn($qq) => $qq->where('m.year', (int)$year))
            ->when($month !== '', fn($qq) => $qq->where('m.month', (int)$month));

        if ($companyQ !== '' && $this->colExists($tbl, 'company_snapshot')) {
            $like = '%' . $this->escapeLike($companyQ) . '%';
            $q->where(function ($w) use ($like) {
                $w->where('m.company_snapshot', 'like', $like);
            });
        }

        // Limit to rows that have at least one date in range if start/end supplied
        $dateCols = $this->dateColumns('media_coordinator_trackings', ['created_at','updated_at']);
        Log::info("media_coordinator_trackings date columns used", $dateCols);
        $q->when($start && $end, function ($qq) use ($dateCols, $start, $end) {
            $qq->where(function ($or) use ($dateCols, $start, $end) {
                foreach ($dateCols as $col) {
                    $or->orWhereBetween("m.$col", [$start, $end]);
                }
            });
        });

        $rows = $q->limit(5000)->get(); // safety cap

        $events = [];
        foreach ($rows as $row) {
            foreach ($dateCols as $col) {
                if ($milestone !== '' && $milestone !== $col) continue;

                $val = $row->{$col} ?? null;
                if (!$val) continue;

                if ($start && $end && !($val >= $start && $val <= $end)) continue;

                $title = $this->pickFirst([
                    $row->title_snapshot ?? null,
                    $row->publication ?? null,
                    $row->edition ?? null,
                    $row->x ?? null,
                    'Media' // fallback
                ]);

                $company   = $row->company_snapshot ?? '—';
                $objective = $this->pickFirst([
                    $row->subcategory ?? null,
                    $row->artwork_bp_client ?? null,
                ], '—');

                $events[] = [
                    'id'    => "media:{$row->id}:{$col}",
                    'title' => "{$title} – {$company} – {$this->humanize($objective)}",
                    'start' => $val, // single-day
                    'color' => self::COLORS['media'],
                    'allDay' => true,
                    'extendedProps' => [
                        'module'        => 'media',
                        'table'         => $tbl,
                        'milestone'     => $this->milestoneLabel($col),
                        'company'       => $company,
                        'objective_raw' => $objective,
                        'title_raw'     => $title,
                        'master_file_id'=> (int)$row->master_file_id,
                        'year'          => $row->year,
                        'month'         => $row->month,
                        'row_id'        => (int)$row->id,
                    ],
                ];
            }
        }

        return $events;
    }

    /** KLTG */
    private function fetchKltgEvents($start, $end, $year, $month, $companyQ, $milestone)
{
    $tbl = 'kltg_coordinator_lists';

    $q = DB::table($tbl . ' as k')
        ->leftJoin('master_files as mf', 'mf.id', '=', 'k.master_file_id')
        ->select('k.*', 'mf.company as mf_company', 'mf.product as mf_product')
        ->when($year !== '', fn($qq) => $qq->where('k.year', (int)$year))
        ->when($month !== '', fn($qq) => $qq->where('k.month', (int)$month));

    if ($companyQ !== '') {
        $like = '%'.$this->escapeLike($companyQ).'%';
        $q->where(function ($w) use ($like) {
            $w->orWhere('k.client', 'like', $like)
              ->orWhere('mf.company', 'like', $like);
        });
    }

    // Use all real DATE/DATETIME/TIMESTAMP columns from KLTG table
    $dateCols = $this->dateColumns($tbl, ['created_at','updated_at','masterfile_created_at']);

    // Restrict by calendar visible range if provided
    $q->when($start && $end, function ($qq) use ($dateCols, $start, $end) {
        $qq->where(function ($or) use ($dateCols, $start, $end) {
            foreach ($dateCols as $col) {
                $or->orWhereBetween("k.$col", [$start, $end]);
            }
        });
    });

    $rows = $q->limit(5000)->get();

    $events = [];
    foreach ($rows as $row) {
        foreach ($dateCols as $col) {
            if ($milestone !== '' && $milestone !== $col) continue;

            $val = $row->{$col} ?? null;
            if (!$val) continue;
            if ($start && $end && !($val >= $start && $val <= $end)) continue;

            // Title / Company / Objective with master_files fallbacks
            $title    = $this->pickFirst([$row->product ?? null, $row->site ?? null, $row->mf_product ?? null], 'KLTG');
            $company  = $this->pickFirst([$row->client ?? null, $row->mf_company ?? null], '—');
            $objective= $this->pickFirst([$row->status ?? null, $row->next_follow_up_note ?? null], 'KLTG');

            $events[] = [
                'id'    => "kltg:{$row->id}:{$col}",
                'title' => "{$title} – {$company} – {$this->humanize($objective)}",
                'start' => $val,
                'color' => self::COLORS['kltg'],
                'allDay' => true,
                'extendedProps' => [
                    'module'        => 'kltg',
                    'table'         => $tbl,
                    'milestone'     => $this->milestoneLabel($col),
                    'company'       => $company,
                    'objective_raw' => $objective,
                    'title_raw'     => $title,
                    'master_file_id'=> (int)$row->master_file_id,
                    'year'          => $row->year,
                    'month'         => $row->month,
                    'row_id'        => (int)$row->id,
                    'site'          => $row->site ?? null,
                ],
            ];
        }
    }

    return $events;
}


    /** OUTDOOR */
    private function fetchOutdoorEvents($start, $end, $year, $month, $companyQ, $milestone)
    {
        $tbl = 'outdoor_coordinator_trackings';

        // Join to master_files to get company & product
        $q = DB::table($tbl . ' as o')
            ->join('master_files as mf', 'mf.id', '=', 'o.master_file_id')
            ->select('o.*', 'mf.company as mf_company', 'mf.product as mf_product')
            ->when($year !== '', fn($qq) => $qq->where('o.year', (int)$year))
            ->when($month !== '', fn($qq) => $qq->where('o.month', (int)$month));

        if ($companyQ !== '') {
            $like = '%' . $this->escapeLike($companyQ) . '%';
            $q->where(function ($w) use ($like) {
                $w->where('mf.company', 'like', $like);
            });
        }

        $dateCols = $this->dateColumns('outdoor_coordinator_trackings', ['created_at','updated_at']);
        Log::info("outdoor_coordinator_trackings date columns used", $dateCols);
        $q->when($start && $end, function ($qq) use ($dateCols, $start, $end) {
            $qq->where(function ($or) use ($dateCols, $start, $end) {
                foreach ($dateCols as $col) {
                    $or->orWhereBetween("o.$col", [$start, $end]);
                }
            });
        });

        $rows = $q->limit(5000)->get();

        $events = [];
        foreach ($rows as $row) {
            foreach ($dateCols as $col) {
                if ($milestone !== '' && $milestone !== $col) continue;

                $val = $row->{$col} ?? null;
                if (!$val) continue;
                if ($start && $end && !($val >= $start && $val <= $end)) continue;

                $title    = $this->pickFirst([$row->mf_product ?? null], 'Outdoor');
                $company  = $row->mf_company ?? '—';
                $objective= $this->pickFirst([$row->remarks ?? null], 'Outdoor');

                $events[] = [
                    'id'    => "outdoor:{$row->id}:{$col}",
                    'title' => "{$title} – {$company} – {$this->humanize($objective)}",
                    'start' => $val,
                    'color' => self::COLORS['outdoor'],
                    'allDay' => true,
                    'extendedProps' => [
                        'module'        => 'outdoor',
                        'table'         => $tbl,
                        'milestone'     => $this->milestoneLabel($col),
                        'company'       => $company,
                        'objective_raw' => $objective,
                        'title_raw'     => $title,
                        'master_file_id'=> (int)$row->master_file_id,
                        'year'          => $row->year,
                        'month'         => $row->month,
                        'row_id'        => (int)$row->id,
                    ],
                ];
            }
        }

        return $events;
    }

    private function pickFirst(array $candidates, $fallback = '—')
    {
        foreach ($candidates as $c) {
            $v = is_string($c) ? trim($c) : $c;
            if (!empty($v)) return $v;
        }
        return $fallback;
    }

    private function humanize($text)
    {
        if (!is_string($text) || $text === '') return '—';
        $t = str_replace(['_', '-'], ' ', $text);
        $t = preg_replace('/\s+/', ' ', $t);
        return ucfirst(trim($t));
    }

    private function milestoneLabel(string $col): string
    {
        // Turn snake_case into Title Case with some nicer names
        $map = [
            'em_date_write'      => 'EM Date Write',
            'em_date_to_post'    => 'EM Date To Post',
            'em_post_date'       => 'EM Post Date',
            'site_date'          => 'Site Date',
            'payment_date'       => 'Payment Date',
            'material_date'      => 'Material Date',
            'artwork_date'       => 'Artwork Date',
            'received_approval'  => 'Received Approval',
            'sent_to_printer'    => 'Sent to Printer',
            'collection_printer' => 'Collection (Printer)',
            'next_follow_up'     => 'Next Follow Up',
            'pending_date'       => 'Pending Date',
            'total_artwork_date' => 'Total Artwork Date',
        ];
        if (isset($map[$col])) return $map[$col];

        $t = str_replace(['_', '-'], ' ', $col);
        $t = preg_replace('/\s+/', ' ', $t);
        return ucwords($t);
    }
}
