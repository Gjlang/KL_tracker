<?php

// app/Http/Controllers/InformationBoothController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClientFeedBacklog;

class InformationBoothController extends Controller
{
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
            'date'        => 'required|date',
            'servicing'   => 'nullable|string|max:255',
            'product'     => 'nullable|string|max:255',
            'location'    => 'nullable|string|max:255',
            'client'      => 'required|string|max:255',
            'status'      => 'required|in:pending,in-progress,done,cancelled',
            'attended_by' => 'nullable|string|max:255',
            'reasons'     => 'nullable|string',
        ]);

        ClientFeedBacklog::create($data);

        // keep route() — see notes below if your editor still complains
        return redirect()->route('information.booth')->with('ok', 'Entry added!');
    }

    public function update(Request $request, ClientFeedBacklog $feed)
    {
        $data = $request->validate([
            'date'        => 'sometimes|date',
            'servicing'   => 'sometimes|nullable|string|max:255',
            'product'     => 'sometimes|nullable|string|max:255',
            'location'    => 'sometimes|nullable|string|max:255',
            'client'      => 'sometimes|string|max:255',
            'status'      => 'sometimes|in:pending,in-progress,done,cancelled',
            'attended_by' => 'sometimes|nullable|string|max:255',
            'reasons'     => 'sometimes|nullable|string',
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
