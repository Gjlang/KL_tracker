<?php

namespace App\Http\Controllers;

use App\Models\ClientFeedBacklog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class InformationBoothController extends Controller
{

    public function events(Request $request)
{
    $start = Carbon::parse($request->query('start', now()->startOfMonth()));
    $end   = Carbon::parse($request->query('end',   now()->endOfMonth()));

    $qStatus = $request->query('status');
    $qClient = $request->query('client');

    $rows = ClientFeedBacklog::query()
        ->when($qStatus, fn($q) => $q->where('status', $qStatus))
        ->when($qClient, fn($q) => $q->where('client', 'like', "%{$qClient}%"))
        // HANYA event yang punya expected_finish_date di dalam window
        ->whereNotNull('expected_finish_date')
        ->whereDate('expected_finish_date', '>=', $start->toDateString())
        ->whereDate('expected_finish_date', '<=', $end->toDateString())
        ->orderBy('expected_finish_date')
        ->get();

    $today = now()->startOfDay();

    $statusColors = [
        'pending'     => ['bg' => '#FDE68A', 'text' => '#92400E', 'border' => '#FDE68A'], // kuning
        'in-progress' => ['bg' => '#E6F6FD', 'text' => '#22255b', 'border' => '#BAE6FD'], // biru muda
        'Completed'        => ['bg' => '#D1FAE5', 'text' => '#065F46', 'border' => '#A7F3D0'], // hijau
        'cancelled'   => ['bg' => '#FEE2E2', 'text' => '#d33831', 'border' => '#FECACA'], // merah muda
    ];

    $events = $rows->map(function ($r) use ($today, $statusColors) {
        $finish = optional($r->expected_finish_date)->toDateString();
        $overdue = $finish && (Carbon::parse($finish)->lt($today)) && !in_array($r->status, ['completed','cancelled'], true);

        // warna default berdasar status; override merah jika overdue
        $c = $statusColors[$r->status] ?? ['bg' => '#E5E7EB', 'text' => '#374151', 'border' => '#E5E7EB'];
        if ($overdue) {
            $c = ['bg' => '#FEE4E2', 'text' => '#7A271A', 'border' => '#FCD5CE']; // merah "DELAY"
        }

        $titleBits = array_filter([$r->company, $r->product]);
        $title = implode(' — ', $titleBits) ?: 'Expected Finish';

        return [
            'id'    => (string) $r->id,
            'title' => $title,
            'start' => $finish,     // <-- hanya 1 hari: expected_finish_date
            'allDay' => true,
            'url'   => route('information.booth.edit', $r->id),

            'backgroundColor' => $c['bg'],
            'borderColor'     => $c['border'],
            'textColor'       => $c['text'],

            'extendedProps' => [
                'status'   => $r->status,
                'overdue'  => $overdue,
                'client'   => $r->client,
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

    public function calendar()
    {
        return view('information_booth.calendar');
    }

    public function index(Request $request)
    {
        $qStatus = $request->query('status');
        $qClient = $request->query('client');

        $feeds = ClientFeedBacklog::query()
        ->when($qStatus, fn ($q) => $q->where('status', $qStatus))
        ->when($qClient, fn ($q) => $q->where('client', 'like', "%{$qClient}%"))
        ->orderByDesc('date')
        ->get();

        $filters = ['status' => $qStatus, 'client' => $qClient];

        return view('information_booth.index', compact('feeds', 'filters'));
    }

    public function create()
    {
        return view('information_booth.create');
    }

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
            'status'               => 'required|in:pending,in-progress,completed,cancelled',
            'attended_by'          => 'nullable|string|max:255',
            'reasons'              => 'nullable|string',
            'master_file_id'       => 'nullable|integer',
        ]);

        ClientFeedBacklog::create($data);

        return redirect()
            ->route('information.booth.index')
            ->with('ok', 'Entry added!');
    }

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
            'status'               => 'sometimes|in:pending,in-progress,completed,cancelled',
            'attended_by'          => 'sometimes|nullable|string|max:255',
            'reasons'              => 'sometimes|nullable|string',
            'master_file_id'       => 'sometimes|nullable|integer',
        ]);

        $feed->update($data);

        return redirect()
            ->route('information.booth.index')
            ->with('ok', 'Entry updated!');
    }

    public function destroy(ClientFeedBacklog $feed)
    {
        $feed->delete();

        return redirect()
            ->route('information.booth.index')
            ->with('ok', 'Entry deleted!');
    }
}
