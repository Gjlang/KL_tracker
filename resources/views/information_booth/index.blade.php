@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl p-4 space-y-8">

    {{-- Alerts --}}
    @if(session('ok'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-2 text-green-800">
            {{ session('ok') }}
        </div>
    @endif
    @if($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-2 text-red-800">
            <ul class="list-disc ml-5">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Information Booth</h1>
        <div class="text-sm text-gray-500">Calendar + Client Feed Backlog</div>
    </div>

    {{-- Layout: Calendar (left) + Backlog (right) --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Calendar Column --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <h2 class="text-lg font-medium mb-3">Calendar</h2>

            {{-- If you already have a reusable calendar partial/JS, include it here --}}
            {{-- Example: --}}
            @includeIf('calendar._fullcalendar_embed')
            {{-- Or render your existing calendar component here --}}
            {{-- Make sure the partial uses your existing events route (e.g. billboard.calendar.events) --}}
        </div>

        {{-- Client Feed Backlog Column --}}
        <div class="space-y-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                <h2 class="text-lg font-medium mb-4">Add Client Feed Backlog</h2>

                <form method="POST" action="{{ route('information.booth.feeds.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf

                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Date</label>
                        <input type="date" name="date" class="w-full rounded-lg border-gray-300" required>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Client</label>
                        <input type="text" name="client" class="w-full rounded-lg border-gray-300" required>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Servicing</label>
                        <input type="text" name="servicing" class="w-full rounded-lg border-gray-300">
                    </div>

                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Product</label>
                        <input type="text" name="product" class="w-full rounded-lg border-gray-300">
                    </div>

                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Location</label>
                        <input type="text" name="location" class="w-full rounded-lg border-gray-300">
                    </div>

                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Status</label>
                        <select name="status" class="w-full rounded-lg border-gray-300">
                            <option value="pending">Pending</option>
                            <option value="in-progress">In progress</option>
                            <option value="done">Done</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Attend by</label>
                        <input type="text" name="attended_by" class="w-full rounded-lg border-gray-300" placeholder="PIC / team">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm text-gray-600 mb-1">Reasons</label>
                        <textarea name="reasons" rows="3" class="w-full rounded-lg border-gray-300" placeholder="Notes / follow-up reasons"></textarea>
                    </div>

                    {{-- Optional: tie to a master file --}}
                    {{-- <div>
                        <label class="block text-sm text-gray-600 mb-1">Master File</label>
                        <input type="number" name="master_file_id" class="w-full rounded-lg border-gray-300" placeholder="Master File ID">
                    </div> --}}

                    <div class="md:col-span-2 flex justify-end">
                        <button type="submit" class="px-5 py-2.5 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">
                            Save
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-4 flex items-center justify-between">
                    <h2 class="text-lg font-medium">Client Feed Backlog</h2>
                    <form method="GET" class="flex gap-2">
                        <input name="client" value="{{ $filters['client'] }}" placeholder="Search client…" class="rounded-lg border-gray-300">
                        <select name="status" class="rounded-lg border-gray-300">
                            <option value="">All Status</option>
                            @foreach(['pending','in-progress','done','cancelled'] as $st)
                                <option value="{{ $st }}" @selected($filters['status']===$st)>{{ ucfirst($st) }}</option>
                            @endforeach
                        </select>
                        <button class="px-3 py-2 rounded-lg border border-gray-300 hover:bg-gray-50">Filter</button>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left font-medium text-gray-600">Date</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-600">Servicing</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-600">Product</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-600">Location</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-600">Client</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-600">Status</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-600">Attend by</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-600">Reasons</th>
                                <th class="px-4 py-2"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse($feeds as $f)
                                <tr>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ optional($f->date)->format('Y-m-d') }}</td>
                                    <td class="px-4 py-2">{{ $f->servicing }}</td>
                                    <td class="px-4 py-2">{{ $f->product }}</td>
                                    <td class="px-4 py-2">{{ $f->location }}</td>
                                    <td class="px-4 py-2">{{ $f->client }}</td>
                                    <td class="px-4 py-2">
                                        @php
                                            $badge = match($f->status){
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'in-progress' => 'bg-blue-100 text-blue-800',
                                                'done' => 'bg-green-100 text-green-800',
                                                'cancelled' => 'bg-gray-200 text-gray-700',
                                            };
                                        @endphp
                                        <span class="px-2 py-1 rounded-full text-xs {{ $badge }}">{{ ucfirst($f->status) }}</span>
                                    </td>
                                    <td class="px-4 py-2">{{ $f->attended_by }}</td>
                                    <td class="px-4 py-2 max-w-[280px]">
                                        <div class="line-clamp-2">{{ $f->reasons }}</div>
                                    </td>
                                    <td class="px-4 py-2 text-right whitespace-nowrap">
                                        <form method="POST" action="{{ route('information.booth.feeds.destroy', $f) }}" onsubmit="return confirm('Delete this entry?')">
                                            @csrf @method('DELETE')
                                            <button class="px-3 py-1.5 rounded-lg border border-gray-300 hover:bg-gray-50">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="9" class="px-4 py-6 text-center text-gray-500">No backlog yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4">
                    {{ $feeds->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
