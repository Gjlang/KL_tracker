<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class CalendarController extends Controller
{


    public function index()
    {
        // return the Blade that contains your FullCalendar div + JS
        return view('calendar.index'); // adjust to your actual blade path
    }
    // ... keep imports (DB, Log, Schema) and class header

    public function events(Request $request)
    {
        try {
            $start = $request->query('start'); // YYYY-MM-DD
            $end   = $request->query('end');   // YYYY-MM-DD

            $events = $this->buildSingleDayEvents($start, $end);

            return response()->json($events);
        } catch (\Throwable $e) {
            Log::error('Calendar events error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([], 500);
        }
    }

    /**
     * Download visible events as .ics (single-day, SUMMARY only).
     * GET /calendar/export?start=YYYY-MM-DD&end=YYYY-MM-DD
     */
    public function exportIcs(Request $request)
    {
        $start = $request->query('start');
        $end   = $request->query('end');

        $events = $this->buildSingleDayEvents($start, $end);

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//BGOC//Calendar//EN',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
        ];

        $nowUtc = now()->utc()->format('Ymd\THis\Z');

        foreach ($events as $e) {
            if (empty($e['start']) || empty($e['title'])) {
                continue;
            }
            $dt = \Carbon\Carbon::parse($e['start'])->format('Ymd');
            $summary = preg_replace("/[\r\n]+/", ' ', $e['title']);
            // Escape commas/semicolons/backslashes per RFC 5545
            $summary = addcslashes($summary, ",;\\");
            $uid = md5(($e['id'] ?? $e['title'].$dt).'-ics').'@bgoc';

            $lines[] = 'BEGIN:VEVENT';
            $lines[] = "UID:$uid";
            $lines[] = "DTSTAMP:$nowUtc";
            $lines[] = "DTSTART;VALUE=DATE:$dt";
            $lines[] = "SUMMARY:$summary";
            $lines[] = 'END:VEVENT';
        }

        $lines[] = 'END:VCALENDAR';
        $ics = implode("\r\n", $lines) . "\r\n";

        $filename = 'calendar_'.now()->format('Ym').'.ics';
        return response($ics, 200, [
            'Content-Type'        => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    /**
     * Core loader used by both events() and exportIcs().
     * Returns only single-day, allDay events with titles sourced from the detail table itself.
     */
    private function buildSingleDayEvents(?string $start, ?string $end): array
    {
        $inWindow = function ($query, $column) use ($start, $end) {
            if ($start) $query->whereDate($column, '>=', $start);
            if ($end)   $query->whereDate($column, '<=', $end);
        };

        $events = [];

        // ============ KLTG (value_date present only; title from detail table) ============
        if (\Illuminate\Support\Facades\Schema::hasColumn('kltg_monthly_details', 'master_file_id')) {
            $kltg = DB::table('kltg_monthly_details as d')
                ->leftJoin('master_files as m', 'm.id', '=', 'd.master_file_id')
                ->selectRaw("
                    d.id, d.value_date as start_date,
                    COALESCE(NULLIF(TRIM(d.value_text), ''), d.category, d.type) as label,
                    d.master_file_id, m.company, m.product, m.client
                ")
                ->whereNotNull('d.value_date');
            $inWindow($kltg, 'd.value_date');

            foreach ($kltg->get() as $r) {
                $parts = array_filter([$r->company, ($r->label ?: 'KLTG')]);
                $events[] = [
                    'id'    => "kltg:{$r->id}",
                    'title' => implode(' — ', $parts),
                    'start' => $r->start_date,
                    'allDay'=> true,
                    'color' => '#8b5cf6',
                    'extendedProps' => [
                        'type' => 'kltg',
                        'master_file_id' => $r->master_file_id,
                        'company' => $r->company,
                        'product' => $r->product,
                        'client'  => $r->client,
                    ],
                ];
            }
        }

        // ============ Media (value_date present only; title from detail table) ============
        $media = DB::table('media_monthly_details as d')
            ->leftJoin('master_files as m', 'm.id', '=', 'd.master_file_id')
            ->selectRaw("
                d.id, d.value_date as start_date,
                COALESCE(NULLIF(TRIM(d.value_text), ''), d.subcategory) as label,
                d.master_file_id, m.company, m.product, m.client
            ")
            ->whereNotNull('d.value_date');
        $inWindow($media, 'd.value_date');

        foreach ($media->get() as $r) {
            $label = $r->label ?: 'Media';
            $parts = array_filter([$r->company, $label]);
            $events[] = [
                'id'    => "media:{$r->id}",
                'title' => implode(' — ', $parts),
                'start' => $r->start_date,
                'allDay'=> true,
                'color' => '#3b82f6',
                'extendedProps' => [
                    'type' => 'media',
                    'master_file_id' => $r->master_file_id,
                    'company' => $r->company,
                    'product' => $r->product,
                    'client'  => $r->client,
                ],
            ];
        }

        // -------- OUTDOOR: show date rows, but title from sibling text row if available --------
        $outdoor = DB::table('outdoor_monthly_details as d')
            ->leftJoin('master_files as m', 'm.id', '=', 'd.master_file_id')
            ->selectRaw("
                d.id,
                d.value_date as start_date,
                d.field_key,
                d.field_type,
                -- text on the same job/month to use as label
                (
                SELECT t.value_text
                FROM outdoor_monthly_details t
                WHERE t.master_file_id = d.master_file_id
                    AND t.year = d.year
                    AND t.month = d.month
                    AND t.field_type = 'text'
                    AND t.value_text IS NOT NULL
                ORDER BY t.id DESC
                LIMIT 1
                ) AS sibling_text,
                NULLIF(TRIM(d.value_text), '') as self_label,
                d.master_file_id,
                m.company, m.product, m.client
            ")
            ->where('d.field_type', '=', 'date')
            ->whereNotNull('d.value_date');

        $inWindow($outdoor, 'd.value_date');

        foreach ($outdoor->get() as $r) {
            $label = $r->self_label ?: ($r->sibling_text ?: ucwords(strtolower(str_replace('_',' ', (string)$r->field_key))));
            $parts = array_filter([$r->company, $r->product, $label]);
            $events[] = [
                'id'    => "outdoor:{$r->id}",
                'title' => implode(' — ', $parts),
                'start' => $r->start_date,
                'allDay'=> true,
                'color' => '#10b981',
                'extendedProps' => [
                    'type'            => 'outdoor',
                    'master_file_id'  => $r->master_file_id,
                    'field_key'       => $r->field_key,
                    'company'         => $r->company,
                    'product'         => $r->product,
                    'client'          => $r->client,
                ],
            ];
        }


        // ============ Master Files — END DATE ONLY (single-day) ============
        $master = DB::table('master_files as m')
            ->selectRaw("
                m.id,
                m.company, m.product, m.client,
                m.`date_finish` as end_date
            ")
            ->whereNotNull('m.date_finish');
        $inWindow($master, 'm.date_finish');

        foreach ($master->get() as $r) {
            $events[] = [
                'id'    => "master:{$r->id}:end",
                'title' => "{$r->company} — {$r->product} (END)",
                'start' => $r->end_date,
                'allDay'=> true,
                'color' => '#6b7280',
                'extendedProps' => [
                    'type' => 'master',
                    'master_file_id' => $r->id,
                    'company' => $r->company,
                    'product' => $r->product,
                    'client'  => $r->client,
                ],
            ];
        }

        return $events;
    }

}
