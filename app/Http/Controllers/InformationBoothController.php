<?php

// app/Http/Controllers/InformationBoothController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClientFeedBacklog;
use Illuminate\Support\Str;


class InformationBoothController extends Controller
{

    public function events(Request $request)
{
    // FullCalendar passes ?start=YYYY-MM-DD&end=YYYY-MM-DD
    $start = $request->query('start');
    $end   = $request->query('end');

    $q = ClientFeedBacklog::query()
        ->select([
            'id', 'date', 'expected_finish_date', 'servicing', 'product',
            'location', 'company', 'client', 'status'
        ]);

    if ($start && $end) {
        $q->whereBetween('date', [$start, $end]);
    }

    // Optional filters (if you send them from the UI)
    if ($company = $request->query('company')) {
        $q->where('company', 'like', "%{$company}%");
    }
    if ($status = $request->query('status')) {
        $q->where('status', $status);
    }

    $rows = $q->orderBy('date', 'asc')->get();

    // Color scheme by status (Google Calendar vibe)
    $color = fn ($s) => match ($s) {
        'pending'     => '#9aa0a6', // neutral gray
        'in-progress' => '#1a73e8', // blue
        'done'        => '#188038', // green
        'cancelled'   => '#d93025', // red
        default       => '#9aa0a6',
    };

    // Map to FullCalendar events (all-day)
    $events = $rows->map(function ($r) use ($color) {
        $titlePieces = array_filter([
            $r->servicing ?: null,
            $r->company   ?: null,
            $r->client    ?: null,
        ]);
        $title = implode(' — ', $titlePieces);
        if ($title === '') $title = 'Untitled';

        return [
            'id'        => (string)$r->id,
            'title'     => Str::limit($title, 60),
            'start'     => optional($r->date)->toDateString(),   // all-day
            'allDay'    => true,
            'color'     => $color($r->status),
            // Click goes to your edit/show page (adjust route if different)
            'url'       => route('information.booth', ['highlight' => $r->id]),
            // Extra fields for tooltips
            'extendedProps' => [
                'status'   => $r->status,
                'product'  => $r->product,
                'location' => $r->location,
                'expected' => optional($r->expected_finish_date)->toDateString(),
            ],
        ];
    })->values();



    return response()->json($events);
}

    public function calendar()
{
    // Blade page that hosts the calendar UI
    return view('information_booth.calendar');
}

    public function index(Request $request)
    {
        $qStatus = $request->query('status');
        $qClient = $request->query('client');

        $feeds = ClientFeedBacklog::query()
            ->when($qStatus, fn($q) => $q->where('status', $qStatus))
            ->when($qClient, fn($q) => $q->where('client', 'like', "%{$qClient}%"))
            ->orderByDesc('date')
            ->paginate(20);

        $filters = ['status' => $qStatus, 'client' => $qClient];

        return view('information_booth.index', compact('feeds', 'filters'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date'                 => 'required|date',
            'expected_finish_date' => 'nullable|date',         // ← new
            'servicing'            => 'nullable|string|max:255',
            'product'              => 'nullable|string|max:255',
            'location'             => 'nullable|string|max:255',
            'client'               => 'required|string|max:255',
            'company'              => 'nullable|string|max:255', // ← new (make required later if you prefer)
            'status'               => 'required|in:pending,in-progress,done,cancelled',
            'attended_by'          => 'nullable|string|max:255',
            'reasons'              => 'nullable|string',
            'master_file_id'       => 'nullable|integer', // keep if you support linking
        ]);

        ClientFeedBacklog::create($data);

        return redirect()->route('information.booth')->with('ok', 'Entry added!');
    }


    public function update(Request $request, ClientFeedBacklog $feed)
    {
        $data = $request->validate([
            'date'                 => 'sometimes|date',
            'expected_finish_date' => 'sometimes|nullable|date',   // ← new
            'servicing'            => 'sometimes|nullable|string|max:255',
            'product'              => 'sometimes|nullable|string|max:255',
            'location'             => 'sometimes|nullable|string|max:255',
            'client'               => 'sometimes|string|max:255',
            'company'              => 'sometimes|nullable|string|max:255', // ← new
            'status'               => 'sometimes|in:pending,in-progress,done,cancelled',
            'attended_by'          => 'sometimes|nullable|string|max:255',
            'reasons'              => 'sometimes|nullable|string',
            'master_file_id'       => 'sometimes|nullable|integer',
        ]);

        $feed->update($data);

        return redirect()->route('information.booth')->with('ok', 'Entry updated!');
    }


    public function destroy(ClientFeedBacklog $feed)
    {
        $feed->delete();

        return redirect()->route('information.booth')->with('ok', 'Entry deleted!');
    }
}
