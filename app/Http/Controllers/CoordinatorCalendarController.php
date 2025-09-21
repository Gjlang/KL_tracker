<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
        // FullCalendar typically sends ISO dates like 2025-09-01
        $start = $request->query('start'); // optional
        $end   = $request->query('end');   // optional

        $module    = strtolower((string)$request->query('module', '')); // 'outdoor'|'media'|'kltg' or ''
        $year      = trim((string)$request->query('year', ''));
        $month     = trim((string)$request->query('month', '')); // 1..12
        $companyQ  = trim((string)$request->query('company', '')); // company/client filter (contains)
        $milestone = trim((string)$request->query('milestone', '')); // filter by date column name

        $events = [];

        // MEDIA coordinator trackings (your big schema; table name assumed):
        if ($module === '' || $module === 'media') {
            $events = array_merge($events, $this->fetchMediaCoordinatorEvents(
                $start, $end, $year, $month, $companyQ, $milestone
            ));
        }

        // KLTG coordinator lists:
        if ($module === '' || $module === 'kltg') {
            $events = array_merge($events, $this->fetchKltgEvents(
                $start, $end, $year, $month, $companyQ, $milestone
            ));
        }

        // OUTDOOR coordinator trackings:
        if ($module === '' || $module === 'outdoor') {
            $events = array_merge($events, $this->fetchOutdoorEvents(
                $start, $end, $year, $month, $companyQ, $milestone
            ));
        }

        return response()->json($events);
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

        if ($companyQ !== '') {
            $q->where(function ($w) use ($companyQ) {
                $w->where('m.company_snapshot', 'like', '%' . addcslashes($companyQ, '%_\\') . '%');
            });
        }

        // Limit to rows that have at least one date in range if start/end supplied
        $dateCols = self::MEDIA_DATE_COLS;
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
            ->select('k.*')
            ->when($year !== '', fn($qq) => $qq->where('k.year', (int)$year))
            ->when($month !== '', fn($qq) => $qq->where('k.month', (int)$month));

        if ($companyQ !== '') {
            $q->where(function ($w) use ($companyQ) {
                $w->where('k.client', 'like', '%' . addcslashes($companyQ, '%_\\') . '%');
            });
        }

        $dateCols = self::KLTG_DATE_COLS;
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

                $title    = $this->pickFirst([$row->product ?? null, $row->site ?? null], 'KLTG');
                $company  = $row->client ?? '—';
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
            $like = '%' . addcslashes($companyQ, '%_\\') . '%';
            $q->where(function ($w) use ($like) {
                $w->where('mf.company', 'like', $like);
            });
        }

        $dateCols = self::OUTDOOR_DATE_COLS;
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
