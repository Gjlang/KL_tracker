<?php

namespace App\Http\Controllers;

use App\Models\ClientFeedBacklog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class InformationBoothController extends Controller
{
    /**
     * JSON feed for FullCalendar
     * Accepts ?start=YYYY-MM-DD&end=YYYY-MM-DD plus optional ?company=&status=
     */
    public function events(Request $request)
    {
        // FullCalendar sends ISO date ranges
        $start = Carbon::parse($request->query('start', now()->startOfMonth()));
        $end   = Carbon::parse($request->query('end', now()->endOfMonth()));

        $qStatus = $request->query('status');
        $qClient = $request->query('client');

        $rows = ClientFeedBacklog::query()
            ->when($qStatus, fn($q) => $q->where('status', $qStatus))
            ->when($qClient, fn($q) => $q->where('client', 'like', "%{$qClient}%"))
            ->whereDate('date', '<=', $end->toDateString())
            ->where(function ($q) use ($start) {
                // Show if it starts before range ends and finishes after range starts
                $q->whereNull('expected_finish_date')
                ->orWhereDate('expected_finish_date', '>=', $start->toDateString());
            })
            ->orderBy('date')
            ->get();

        // Map to FullCalendar events
        $events = $rows->map(function ($r) {
            // FullCalendar expects end to be EXCLUSIVE. If you have a finish date,
            // add +1 day; otherwise use start+1 day so it renders as an all-day block.
            $start = Carbon::parse($r->date)->toDateString();
            $end   = $r->expected_finish_date
                    ? Carbon::parse($r->expected_finish_date)->addDay()->toDateString()
                    : Carbon::parse($r->date)->addDay()->toDateString();

            // Build a clear title. Adjust to your preference.
            $titleBits = array_filter([$r->company, $r->product, $r->client]);
            $title = implode(' — ', $titleBits) ?: 'Backlog';

            // Color by status (keeps same palette vibe as the table)
            $statusColors = [
                'pending'     => ['bg' => '#FDE68A', 'text' => '#92400E', 'border' => '#FDE68A'],
                'in-progress' => ['bg' => '#E6F6FD', 'text' => '#22255b', 'border' => '#BAE6FD'],
                'done'        => ['bg' => '#D1FAE5', 'text' => '#065F46', 'border' => '#A7F3D0'],
                'cancelled'   => ['bg' => '#FEE2E2', 'text' => '#d33831', 'border' => '#FECACA'],
            ];
            $c = $statusColors[$r->status] ?? ['bg' => '#E5E7EB', 'text' => '#374151', 'border' => '#E5E7EB'];

            return [
                'id'    => (string)$r->id,
                'title' => $title,
                'start' => $start,
                'end'   => $end,
                'allDay' => true, // your data is date-only; keep it all-day
                'url'   => route('information.booth.edit', $r->id),

                // styling
                'backgroundColor' => $c['bg'],
                'borderColor'     => $c['border'],
                'textColor'       => $c['text'],

                // extra fields if you need later
                'extendedProps' => [
                    'status'   => $r->status,
                    'company'  => $r->company,
                    'client'   => $r->client,
                    'product'  => $r->product,
                    'location' => $r->location,
                ],
            ];
        });

        return response()->json($events);
    }

    public function move(Request $request, ClientFeedBacklog $feed)
    {
        // Handle drag/drop and resize coming from FullCalendar
        $validated = $request->validate([
            'start' => 'required|date',     // new start (YYYY-MM-DD)
            'end'   => 'nullable|date',     // exclusive end (YYYY-MM-DD)
        ]);

        $start = Carbon::parse($validated['start'])->toDateString();

        // If 'end' is provided, convert EXCLUSIVE -> INCLUSIVE for your DB column.
        // If not provided (single day drag), keep expected_finish_date = start.
        if (!empty($validated['end'])) {
            $incEnd = Carbon::parse($validated['end'])->subDay()->toDateString();
        } else {
            $incEnd = $start;
        }

        $feed->update([
            'date'                 => $start,
            'expected_finish_date' => $incEnd,
        ]);

        return response()->json(['ok' => true]);
    }

        public function edit(ClientFeedBacklog $feed)
        {
            // Kirim data ke view form edit
            return view('information_booth.edit', compact('feed'));
        }

    /**
     * Optional: dedicated calendar page (kept for compatibility).
     * If you don't use it anymore, you can remove this action + its route/view.
     */
    public function calendar()
    {
        return view('information_booth.calendar');
    }

    /**
     * LIST page: Calendar + Filters + Table
     */
    public function index(Request $request)
    {
        $qStatus = $request->query('status');
        $qClient = $request->query('client');

        $feeds = ClientFeedBacklog::query()
            ->when($qStatus, fn ($q) => $q->where('status', $qStatus))
            ->when($qClient, fn ($q) => $q->where('client', 'like', "%{$qClient}%"))
            ->orderByDesc('date')
            ->paginate(20) // pagination size; UI is full-width on desktop
            ->withQueryString();

        $filters = ['status' => $qStatus, 'client' => $qClient];

        return view('information_booth.index', compact('feeds', 'filters'));
    }

    /**
     * CREATE page: Add Backlog Entry form
     */
    public function create()
    {
        return view('information_booth.create');
    }

    /**
     * Save new entry → redirect to LIST with flash message
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'date'                 => 'required|date',
            'expected_finish_date' => 'nullable|date',
            'servicing'            => 'nullable|string|max:255',
            'product'              => 'nullable|string|max:255',
            'location'             => 'nullable|string|max:255',
            'client'               => 'required|string|max:255',
            'company'              => 'nullable|string|max:255',
            'status'               => 'required|in:pending,in-progress,done,cancelled',
            'attended_by'          => 'nullable|string|max:255',
            'reasons'              => 'nullable|string',
            'master_file_id'       => 'nullable|integer',
        ]);

        ClientFeedBacklog::create($data);

        return redirect()
            ->route('information.booth.index')
            ->with('ok', 'Entry added!');
    }

    /**
     * Update an entry → redirect to LIST
     */
    public function update(Request $request, ClientFeedBacklog $feed)
    {
        $data = $request->validate([
            'date'                 => 'sometimes|date',
            'expected_finish_date' => 'sometimes|nullable|date',
            'servicing'            => 'sometimes|nullable|string|max:255',
            'product'              => 'sometimes|nullable|string|max:255',
            'location'             => 'sometimes|nullable|string|max:255',
            'client'               => 'sometimes|string|max:255',
            'company'              => 'sometimes|nullable|string|max:255',
            'status'               => 'sometimes|in:pending,in-progress,done,cancelled',
            'attended_by'          => 'sometimes|nullable|string|max:255',
            'reasons'              => 'sometimes|nullable|string',
            'master_file_id'       => 'sometimes|nullable|integer',
        ]);

        $feed->update($data);

        return redirect()
            ->route('information.booth.index')
            ->with('ok', 'Entry updated!');
    }

    /**
     * Delete an entry → redirect to LIST
     */
    public function destroy(ClientFeedBacklog $feed)
    {
        $feed->delete();

        return redirect()
            ->route('information.booth.index')
            ->with('ok', 'Entry deleted!');
    }
}
