<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-4 md:p-8">
        <!-- Mobile Header -->
        <div class="relative z-10 flex-shrink-0 flex h-16 bg-white rounded-2xl shadow-sm border border-gray-100 mb-4 md:hidden">
            <button type="button" class="border-r border-gray-200 px-4 text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500 md:hidden">
                <span class="sr-only">Open sidebar</span>
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                </svg>
            </button>
            <div class="flex-1 px-4 flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Outdoor Coordinator') }}
                </h2>
            </div>
        </div>

        <div class="max-w-full mx-auto">
            <!-- Desktop Header -->
            <div class="hidden md:flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Outdoor Coordinator</h1>
                    <p class="text-gray-600 mt-1">Manage and track your outdoor advertising projects</p>
                </div>
                <a href="{{ route('dashboard.outdoor') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-xl shadow-sm hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Monthly
                </a>
            </div>

            <!-- Success Messages -->
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('info'))
                <div class="bg-blue-50 border border-blue-200 text-blue-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ session('info') }}
                </div>
            @endif

            <!-- Filters and Actions -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                    <!-- Filters -->
                    <div class="flex-1">
                        <form method="GET" action="{{ url()->current() }}" class="space-y-4 relative">
                        {{-- Month --}}
                        <div class="relative z-10"> {{-- z-10 keeps the native dropdown above the button bar --}}
                            <label class="block text-sm font-medium text-gray-700 mb-2">Month</label>
                            <select name="month"
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 hover:bg-white px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors">
                            <option value="">All Months</option>
                            @foreach(($months ?? []) as $m)
                                <option value="{{ $m['value'] }}"
                                @selected( (int)($month ?? 0) === (int)$m['value'] )>
                                {{ $m['label'] }}
                                </option>
                            @endforeach
                            </select>
                        </div>

                        {{-- Apply bar (put it below and with lower z so it doesn't overlap the dropdown) --}}
                        <div class="pt-2 z-0">
                            <button type="submit"
                                    class="w-full inline-flex items-center justify-center rounded-xl bg-indigo-600 text-white py-3 text-sm font-medium hover:bg-indigo-700 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L15 12.414V19l-6-3v-3.586L3.293 6.707A1 1 0 013 6V4z" />
                            </svg>
                            Apply Filters
                            </button>
                        </div>
                        </form>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-3">
                        <button onclick="exportOutdoorData()"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-xl shadow-sm hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3"></path>
                            </svg>
                            Export CSV
                        </button>
                    </div>
                </div>
            </div>

            <!-- Enhanced Table -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900">Coordinator List — Outdoor</h3>
                            <p class="text-sm text-gray-600 mt-1">Track progress across all outdoor advertising projects</p>
                        </div>
                        <div class="text-sm text-gray-500">
                            Auto-save enabled
                            <span class="inline-block w-2 h-2 bg-green-400 rounded-full ml-2"></span>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="sticky top-0 z-20 bg-gradient-to-r from-gray-50 to-gray-100">
                            <tr>
                                <th class="sticky left-0 z-30 bg-inherit border-r border-gray-200 px-4 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wide border-b border-gray-200 min-w-[80px] w-[80px] text-center">ID</th>
                                <th class="sticky left-[80px] z-30 bg-inherit border-r border-gray-200 px-4 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wide border-b border-gray-200 min-w-[200px]">Company</th>
                                <th class="sticky left-[280px] z-30 bg-inherit border-r border-gray-200 px-4 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wide border-b border-gray-200 min-w-[200px]">Person In Charge</th>
                                <th class="sticky left-[480px] z-30 bg-inherit border-r border-gray-200 px-4 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wide border-b border-gray-200 min-w-[180px]">Product</th>
                                <th class="sticky left-[480px] z-30 bg-inherit border-r border-gray-200 px-4 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wide border-b border-gray-200 min-w-[180px]">Site</th>
                                <th class="px-4 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wide border-b border-gray-200 min-w-[180px]">Site</th>
                                <th class="px-4 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wide border-b border-gray-200 min-w-[160px]">Payment</th>
                                <th class="px-4 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wide border-b border-gray-200 min-w-[160px]">Material</th>
                                <th class="px-4 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wide border-b border-gray-200 min-w-[160px]">Artwork</th>
                                <th class="px-4 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wide border-b border-gray-200 min-w-[160px]">Approval</th>
                                <th class="px-4 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wide border-b border-gray-200 min-w-[160px]">Sent</th>
                                <th class="px-4 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wide border-b border-gray-200 min-w-[160px]">Collected</th>
                                <th class="px-4 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wide border-b border-gray-200 min-w-[160px]">Install</th>
                                <th class="px-4 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wide border-b border-gray-200 min-w-[160px]">Dismantle</th>
                            </tr>
                        </thead>
                        <tbody>
                        @if(isset($rows) && $rows->count() > 0)
                            @foreach($rows as $i => $row)
                                @php $mf = $row->masterFile; @endphp
                                <tr class="odd:bg-white even:bg-gray-50 hover:bg-blue-50/50 transition-colors">
                                    <td class="sticky left-0 z-30 bg-inherit border-r border-gray-200 px-4 py-4 align-middle border-b border-gray-100 text-center font-medium text-gray-900">{{ $rows->firstItem() + $i }}</td>

                                    {{-- Company (read-only) --}}
                                    <td class="sticky left-[80px] z-30 bg-inherit border-r border-gray-200 px-4 py-4 align-middle border-b border-gray-100">
                                        <div class="font-medium text-gray-900 bg-gray-50 rounded-lg px-3 py-2">
                                            {{ $mf?->company ?? $row->company_snapshot }}
                                        </div>
                                    </td>

                                    {{-- Client (read-only from master_files) --}}
                                    <td class="sticky left-[280px] z-30 bg-inherit border-r border-gray-200 px-4 py-4 align-middle border-b border-gray-100">
                                        <div class="text-gray-700 bg-gray-50 rounded-lg px-3 py-2">
                                            {{ $mf?->client }}
                                        </div>
                                    </td>

                                    {{-- Product (read-only) --}}
                                    <td class="sticky left-[480px] z-30 bg-inherit border-r px-4 py-4 align-middle border-b border-gray-100">
                                        <div class="text-gray-700 bg-gray-50 rounded-lg px-3 py-2">
                                            {{ $mf?->product ?? $row->product_snapshot }}
                                        </div>
                                    </td>

                                     {{-- Replace your input field loop section with this fixed version --}}
@foreach (['site','payment','material','artwork','received_approval','sent_to_printer','collection_printer','installation','dismantle','status'] as $col)
    <td class="px-4 py-4 align-middle border-b border-gray-100">
        <div class="relative">
            @php
                // Ensure we have a tracking record ID
                $trackingId = $row->id;

                // If no tracking record exists, we need to create one first
                if (!$trackingId && isset($row->master_file_id)) {
                    // Create tracking record on-the-fly
                    $trackingRecord = \App\Models\OutdoorCoordinatorTracking::firstOrCreate(
    [
        'master_file_id'   => $row->master_file_id,
        'outdoor_item_id'  => $row->outdoor_item_id,  // << new
    ],
    [
        'status' => 'pending',
        'site'   => $row->site, // keep a snapshot if you still store it in OCT
    ]
);
                }
            @endphp

            <input type="{{ in_array($col, ['received_approval','sent_to_printer','collection_printer','installation','dismantle']) ? 'date' : 'text' }}"
                   class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 hover:border-gray-300"
                   value="{{ $row->$col ?? '' }}"
                   data-id="{{ $trackingId }}"
                   data-master-id="{{ $row->master_file_id ?? $row->masterFile?->id }}"
                   name="{{ $col }}"
                   placeholder="{{ ucfirst(str_replace('_', ' ', $col)) }}">

            {{-- Save indicators --}}
            <div class="absolute right-2 top-1/2 transform -translate-y-1/2 hidden" data-save-indicator>
                <svg class="animate-spin h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <div class="absolute right-2 top-1/2 transform -translate-y-1/2 hidden" data-save-success>
                <svg class="h-4 w-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <div class="absolute right-2 top-1/2 transform -translate-y-1/2 hidden" data-save-error>
                <svg class="h-4 w-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
        </div>
    </td>
@endforeach
                                    {{-- <td class="px-4 py-4 align-middle border-b border-gray-100">
                                        <form method="post" action="{{ route('coordinator.outdoor.destroy',$row->id) }}"
                                              onsubmit="return confirm('Delete this row?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-red-700 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 hover:border-red-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Delete
                                            </button>
                                        </form>
                                    </td> --}}
                                </tr>
                            @endforeach
                        @elseif(isset($outdoorJobs) && $outdoorJobs->count() > 0)
                            @foreach($outdoorJobs as $i => $row)
                                @php $mf = $row->masterFile; @endphp
                                <tr class="odd:bg-white even:bg-gray-50 hover:bg-blue-50/50 transition-colors duration-200">
                                    <td class="sticky left-0 z-30 bg-inherit border-r border-gray-200 px-4 py-4 align-middle border-b border-gray-100 text-center font-medium text-gray-900">{{ $i + 1 }}</td>

                                    {{-- Company (read-only) --}}
                                    <td class="sticky left-[80px] z-30 bg-inherit border-r border-gray-200 px-4 py-4 align-middle border-b border-gray-100">
                                        <div class="font-medium text-gray-900 bg-gray-50 rounded-lg px-3 py-2">
                                            {{ $mf?->company ?? $row->company_snapshot ?? '-' }}
                                        </div>
                                    </td>

                                    {{-- Client (read-only from master_files) --}}
                                    <td class="sticky left-[280px] z-30 bg-inherit border-r border-gray-200 px-4 py-4 align-middle border-b border-gray-100">
                                        <div class="text-gray-700 bg-gray-50 rounded-lg px-3 py-2">
                                            {{ $mf?->client ?? '-' }}
                                        </div>
                                    </td>

                                    {{-- Product (read-only) --}}
                                    <td class="sticky left-[480px] z-30 bg-inherit border-r border-gray-200 px-4 py-4 align-middle border-b border-gray-100">
                                        <div class="text-gray-700 bg-gray-50 rounded-lg px-3 py-2">
                                            {{ $mf?->product ?? $row->product_snapshot ?? '-' }}
                                        </div>
                                    </td>

                                    @foreach (['site','payment','material','artwork','received_approval','sent_to_printer','collection_printer','installation','dismantle','status'] as $col)
                                        <td class="px-4 py-4 align-middle border-b border-gray-100">
                                            <div class="relative">
                                                <input type="text"
                                                       class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 hover:border-gray-300"
                                                       value="{{ $row->$col ?? '' }}"
                                                       data-id="{{ $row->id }}" name="{{ $col }}">
                                                <div class="absolute right-2 top-1/2 transform -translate-y-1/2 hidden" data-save-indicator>
                                                    <svg class="animate-spin h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                </div>
                                                <div class="absolute right-2 top-1/2 transform -translate-y-1/2 hidden" data-save-success>
                                                    <svg class="h-4 w-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                                <div class="absolute right-2 top-1/2 transform -translate-y-1/2 hidden" data-save-error>
                                                    <svg class="h-4 w-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                        </td>
                                    @endforeach

                                    <td class="px-4 py-4 align-middle border-b border-gray-100">
                                        <form method="post" action="{{ route('coordinator.outdoor.destroy',$row->id) }}"
                                              onsubmit="return confirm('Delete this row?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-red-700 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 hover:border-red-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="15" class="px-6 py-16 text-center text-gray-500">
                                    <div class="flex flex-col items-center max-w-sm mx-auto">
                                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                        </div>
                                        <h4 class="text-lg font-semibold text-gray-900 mb-2">No tracking records found</h4>
                                    </div>
                                </td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if(isset($rows) && method_exists($rows, 'links') && $rows->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $rows->links() }}
                    </div>
                @elseif(isset($outdoorJobs) && method_exists($outdoorJobs, 'links') && $outdoorJobs->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $outdoorJobs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Enhanced Autosave with Visual Feedback --}}
<script>

    async function saveCell(mfId, field, value) {
  const year  = parseInt(document.querySelector('#filterYear').value, 10);
  const month = parseInt(document.querySelector('#filterMonth').value, 10);

  const res = await fetch('/coordinator/outdoor/upsert', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
      master_file_id: mfId,
      year, month,
      field, value
    })
  });

  const json = await res.json();
  if (!res.ok) {
    console.error('Save failed', json);
    // tampilkan error kecil di UI kalau perlu
  }
}

(function(){
    const token = '{{ csrf_token() }}';
    let timers = {};

    function showIndicator(input, type) {
        const parent = input.parentElement;
        const indicators = parent.querySelectorAll('[data-save-indicator], [data-save-success], [data-save-error]');
        indicators.forEach(el => el.classList.add('hidden'));

        const indicator = parent.querySelector(`[data-save-${type}]`);
        if (indicator) {
            indicator.classList.remove('hidden');
        }
    }

    function hideAllIndicators(input) {
        const parent = input.parentElement;
        const indicators = parent.querySelectorAll('[data-save-indicator], [data-save-success], [data-save-error]');
        indicators.forEach(el => el.classList.add('hidden'));
    }

    // Enhanced save function with better error handling
    function saveFieldData(trackingId, fieldName, fieldValue) {
        console.log('Saving field:', { trackingId, fieldName, fieldValue });

        return fetch('{{ route("coordinator.outdoor.upsert") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                id: trackingId,        // The tracking record ID
                field: fieldName,      // Field name to update
                value: fieldValue      // New value
            })
        })
        .then(async response => {
            const responseText = await response.text();
            console.log('Response status:', response.status);
            console.log('Response text:', responseText);

            let responseData;
            try {
                responseData = JSON.parse(responseText);
            } catch (e) {
                console.error('Failed to parse JSON response:', responseText);
                throw new Error('Invalid server response');
            }

            if (!response.ok) {
                console.error('HTTP Error:', response.status, responseData);
                if (responseData.errors) {
                    console.error('Validation errors:', responseData.errors);
                    console.error('Debug info:', responseData.debug_info);
                }
                throw new Error(responseData.message || `HTTP ${response.status}`);
            }

            return responseData;
        });
    }

    document.addEventListener('input', function(e){
        const el = e.target;
        if (!el.matches('input[name]')) return;

        const trackingId = el.dataset.id;
        const fieldName = el.name;
        const fieldValue = el.value;
        const key = `${trackingId}-${fieldName}`;

        // Validate required data
        if (!trackingId) {
            console.error('Missing tracking ID for field:', fieldName);
            showIndicator(el, 'error');
            return;
        }

        if (!fieldName) {
            console.error('Missing field name for tracking ID:', trackingId);
            showIndicator(el, 'error');
            return;
        }

        console.log('Input detected:', { trackingId, fieldName, fieldValue });

        // Show loading indicator immediately
        showIndicator(el, 'indicator');

        // Clear existing timer for this field
        if (timers[key]) {
            clearTimeout(timers[key]);
        }

        // Set new timer with longer delay for better UX
        timers[key] = setTimeout(async () => {
            try {
                const result = await saveFieldData(trackingId, fieldName, fieldValue);
                console.log('Save successful:', result);

                showIndicator(el, 'success');

                // Update status indicator if status was auto-updated
                if (result.status && fieldName !== 'status') {
                    const row = el.closest('tr');
                    const statusCell = row?.querySelector('[data-field="status"]');
                    if (statusCell && statusCell.textContent !== result.status) {
                        statusCell.textContent = result.status;
                        statusCell.className = `px-2 py-1 text-xs rounded-full status-${result.status}`;

                        // Flash the status cell to show it updated
                        statusCell.style.backgroundColor = '#dcfce7';
                        setTimeout(() => {
                            statusCell.style.backgroundColor = '';
                        }, 1000);
                    }
                }

                // Hide success indicator after 1.5 seconds
                setTimeout(() => hideAllIndicators(el), 1500);

            } catch (error) {
                console.error('Save failed:', error);
                showIndicator(el, 'error');

                // Keep error indicator visible longer
                setTimeout(() => hideAllIndicators(el), 3000);

                // Optionally show user-friendly error message
                if (error.message) {
                    // You could show a toast notification here
                    console.warn('User-friendly error:', error.message);
                }
            }

            // Clean up timer reference
            delete timers[key];
        }, 600); // Slightly longer delay to reduce server requests
    }, {passive:true});

    // Export function - moved here to ensure it's available immediately
    window.exportOutdoorData = function() {
        const form = document.querySelector('form[method="GET"]');
        const month = form ? (form.querySelector('select[name="month"]')?.value || '') : '';

        let exportUrl = '{{ route("coordinator.outdoor.export") }}';
        if (month) {
            exportUrl += `?month=${encodeURIComponent(month)}`;
        }
        console.log('Exporting with URL:', exportUrl);
        window.location.href = exportUrl;
    };

    // Additional export function for outdoor projects
    window.exportOutdoorProjects = function() {
        @if(Route::has('masterfile.export'))
            window.location.href = '{{ route("masterfile.export") }}?category=outdoor';
        @else
            alert('Export functionality is not yet implemented.');
        @endif
    };

    // Initialize on DOM load
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Enhanced Outdoor Coordinator Dashboard loaded successfully');

        // Debug: Log all input fields with their data attributes
        const inputs = document.querySelectorAll('input[name]');
        console.log('Found input fields:', inputs.length);

        inputs.forEach((input, index) => {
            const trackingId = input.dataset.id;
            const fieldName = input.name;

            if (!trackingId) {
                console.warn(`Input ${index} missing data-id:`, input);
            }
            if (!fieldName) {
                console.warn(`Input ${index} missing name:`, input);
            }
        });

        // Initialize table enhancements
        const table = document.querySelector('table');
        if (table) {
            table.parentElement.style.scrollBehavior = 'smooth';
        }

        // Add global error handler for unhandled promise rejections
        window.addEventListener('unhandledrejection', function(event) {
            console.error('Unhandled promise rejection:', event.reason);
        });
    });
})();
</script>

@push('scripts')
<script>
    // Additional debugging and utility functions
    window.debugOutdoorData = function() {
        const inputs = document.querySelectorAll('input[name]');
        console.log('Current form data:');
        inputs.forEach(input => {
            console.log({
                id: input.dataset.id,
                name: input.name,
                value: input.value,
                element: input
            });
        });
    };

    // Call this in browser console if you need to debug: debugOutdoorData()
</script>
    @endpush
</x-app-layout>
