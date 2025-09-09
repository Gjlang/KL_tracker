@extends('layouts.app')

{{-- Add CSRF meta to page head --}}
@push('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-4 md:p-8">
        {{-- Mobile Header --}}
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
            {{-- Desktop Header --}}
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

            {{-- Success Messages --}}
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

            {{-- Filters and Actions --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                    {{-- Filters --}}
                    <div class="flex-1">
                        <form method="GET" action="{{ url()->current() }}" class="space-y-4 relative">
                            {{-- Month Filter --}}
                            <div class="relative z-10">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Month</label>
                                <select name="month" id="filterMonth"
                                        class="w-full rounded-xl border border-gray-200 bg-gray-50 hover:bg-white px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors">
                                    <option value="">All Months</option>
                                    @foreach(($months ?? []) as $m)
                                        <option value="{{ $m['value'] }}" @selected((int)($month ?? 0) === (int)$m['value'])>
                                            {{ $m['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Year Filter (hidden input for script reference) --}}
                            <input type="hidden" id="filterYear" value="{{ now()->year }}">

                            {{-- Apply Button --}}
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

                    {{-- Actions --}}
                    <div class="flex items-center gap-3">
                        <button onclick="window.location.href='{{ route('coordinator.outdoor.export') }}'"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-xl shadow-sm hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3"></path>
                            </svg>
                            Export CSV
                        </button>
                    </div>
                </div>
            </div>

            {{-- Enhanced Table --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow overflow-hidden">
                {{-- Table Header --}}
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

                {{-- Table Content --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        {{-- Table Headers --}}
                        <thead class="sticky top-0 z-20 bg-gradient-to-r from-gray-50 to-gray-100">
                            <tr>
                                <th class="sticky left-0 z-30 bg-inherit border-r border-gray-200 px-4 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wide border-b border-gray-200 min-w-[80px] w-[80px] text-center">ID</th>
                                <th class="sticky left-[80px] z-30 bg-inherit border-r border-gray-200 px-4 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wide border-b border-gray-200 min-w-[200px]">Company</th>
                                <th class="sticky left-[280px] z-30 bg-inherit border-r border-gray-200 px-4 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wide border-b border-gray-200 min-w-[200px]">Person In Charge</th>
                                <th class="sticky left-[480px] z-30 bg-inherit border-r border-gray-200 px-4 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wide border-b border-gray-200 min-w-[180px]">Product</th>
                                <th class="sticky left-[480px] z-30 bg-inherit border-r border-gray-200 px-4 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wide border-b border-gray-200 min-w-[180px]">Site</th>
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

                        {{-- Table Body --}}
                        <tbody>
                            @if(isset($rows) && $rows->count() > 0)
                                @foreach($rows as $i => $row)
                                    @php
                                        $mf = $row->masterFile;
                                        $trackingId = $row->id ?? null;

                                        // Auto-create tracking record if needed
                                        if (!$trackingId && isset($row->master_file_id)) {
                                            $trackingRecord = \App\Models\OutdoorCoordinatorTracking::firstOrCreate(
                                                [
                                                    'master_file_id'  => $row->master_file_id,
                                                    'outdoor_item_id' => $row->outdoor_item_id,
                                                ],
                                                [
                                                    'status' => 'pending',
                                                    'site'   => $row->site,
                                                ]
                                            );
                                            $trackingId = $trackingRecord->id;
                                        }

                                        // Define editable columns and date fields
                                        $editableCols = ['payment','material','artwork','received_approval','sent_to_printer','collection_printer','installation','dismantle'];
                                        $dateCols = ['received_approval','sent_to_printer','collection_printer','installation','dismantle'];
                                    @endphp

                                    <tr class="odd:bg-white even:bg-gray-50 hover:bg-blue-50/50 transition-colors">
                                        {{-- ID Column --}}
                                        <td class="sticky left-0 z-30 bg-inherit border-r border-gray-200 px-4 py-4 align-middle border-b border-gray-100 text-center font-medium text-gray-900">
                                            {{ $rows->firstItem() + $i }}
                                        </td>

                                        {{-- Company (Read-only) --}}
                                        <td class="sticky left-[80px] z-30 bg-inherit border-r border-gray-200 px-4 py-4 align-middle border-b border-gray-100">
                                            <div class="font-medium text-gray-900 bg-gray-50 rounded-lg px-3 py-2">
                                                {{ $mf?->company ?? $row->company_snapshot }}
                                            </div>
                                        </td>

                                        {{-- Client (Read-only) --}}
                                        <td class="sticky left-[280px] z-30 bg-inherit border-r border-gray-200 px-4 py-4 align-middle border-b border-gray-100">
                                            <div class="text-gray-700 bg-gray-50 rounded-lg px-3 py-2">
                                                {{ $mf?->client }}
                                            </div>
                                        </td>

                                        {{-- Product (Read-only) --}}
                                        <td class="sticky left-[480px] z-30 bg-inherit border-r px-4 py-4 align-middle border-b border-gray-100">
                                            <div class="text-gray-700 bg-gray-50 rounded-lg px-3 py-2">
                                                {{ $mf?->product ?? $row->product_snapshot }}
                                            </div>
                                        </td>

                                        {{-- Site (Read-only) --}}
                                        <td class="sticky left-[640px] z-30 bg-inherit border-r px-4 py-4 align-middle border-b border-gray-100">
                                            <div class="text-gray-700 bg-gray-50 rounded-lg px-3 py-2">
                                                {{ $row->site ?? '-' }}
                                            </div>
                                        </td>

                                        {{-- Editable Fields --}}
                                        @foreach ($editableCols as $col)
                                            @php
                                                $val = $row->{$col} ?? '';
                                                $isDate = in_array($col, $dateCols, true);
                                            @endphp
                                            <td class="px-4 py-4 align-middle border-b border-gray-100">
                                                <div class="relative">
                                                    @if ($isDate)
                                                        <input type="date"
                                                            class="w-44 border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 outdoor-field"
                                                            value="{{ $val }}"
                                                            data-id="{{ $trackingId }}"
                                                            data-mf="{{ $row->master_file_id }}"
                                                            data-oi="{{ $row->outdoor_item_id }}"
                                                            data-field="{{ $col }}" />
                                                    @else
                                                        <input type="text"
                                                            class="w-44 border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 outdoor-field"
                                                            value="{{ $val }}"
                                                            data-id="{{ $trackingId }}"
                                                            data-mf="{{ $row->master_file_id }}"
                                                            data-oi="{{ $row->outdoor_item_id }}"
                                                            data-field="{{ $col }}"
                                                            autocomplete="off" />
                                                    @endif

                                                    {{-- Save Indicators --}}
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
                                    </tr>
                                @endforeach

                            @elseif(isset($outdoorJobs) && $outdoorJobs->count() > 0)
                                {{-- Alternative data source --}}
                                @foreach($outdoorJobs as $i => $row)
                                    @php $mf = $row->masterFile; @endphp
                                    <tr class="odd:bg-white even:bg-gray-50 hover:bg-blue-50/50 transition-colors duration-200">
                                        <td class="sticky left-0 z-30 bg-inherit border-r border-gray-200 px-4 py-4 align-middle border-b border-gray-100 text-center font-medium text-gray-900">
                                            {{ $i + 1 }}
                                        </td>

                                        {{-- Company (read-only) --}}
                                        <td class="sticky left-[80px] z-30 bg-inherit border-r border-gray-200 px-4 py-4 align-middle border-b border-gray-100">
                                            <div class="font-medium text-gray-900 bg-gray-50 rounded-lg px-3 py-2">
                                                {{ $mf?->company ?? $row->company_snapshot ?? '-' }}
                                            </div>
                                        </td>

                                        {{-- Client (read-only) --}}
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

                                        {{-- Editable fields --}}
                                        @foreach (['site','payment','material','artwork','received_approval','sent_to_printer','collection_printer','installation','dismantle','status'] as $col)
                                            <td class="px-4 py-4 align-middle border-b border-gray-100">
                                                <div class="relative">
                                                    <input type="text"
                                                           class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 hover:border-gray-300"
                                                           value="{{ $row->$col ?? '' }}"
                                                           data-id="{{ $row->id }}"
                                                           data-mf="{{ $row->master_file_id }}"
                                                           data-field="{{ $col }}"
                                                           onblur="saveFieldData(this.dataset.id || null, this.dataset.field, this.value, this.dataset.mf)">

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

                                        {{-- Delete Action --}}
                                        <td class="px-4 py-4 align-middle border-b border-gray-100">
                                            <form method="post" action="{{ route('coordinator.outdoor.destroy',$row->id) }}"
                                                  onsubmit="return confirm('Delete this row?')">
                                                @csrf
                                                @method('DELETE')
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
                                {{-- Empty State --}}
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

                {{-- Pagination --}}
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
@endsection

@push('scripts')
<script>
// PINDAHKAN KE ATAS - DILUAR DOMContentLoaded
function exportOutdoorData() {
  window.location.href = "{{ route('coordinator.outdoor.export') }}";
}

document.addEventListener('DOMContentLoaded', function () {
  const token = document.querySelector('meta[name="csrf-token"]')?.content || '';

  // Save on blur (textareas/inputs)…
  document.addEventListener('blur', async (e) => {
    const el = e.target;
    if (!el.classList?.contains('outdoor-field')) return;
    await saveField(el);
  }, true);

  // …and on any change (date, select, checkbox, etc.)
  document.addEventListener('change', async (e) => {
    const el = e.target;
    if (!el.classList?.contains('outdoor-field')) return;
    await saveField(el);
  });

  function normalizeValue(el) {
    if (el.type === 'checkbox') {
      return el.checked ? 1 : 0;
    }
    if (el.type === 'date' && el.value) {
      // Ensure YYYY-MM-DD (browser date inputs already do this)
      return el.value.trim();
    }
    return typeof el.value === 'string' ? el.value.trim() : el.value;
  }

  let inflight = new Set();

  async function saveField(element) {
    // Prevent double-fire on the same element
    if (inflight.has(element)) return;
    inflight.add(element);

    const tr = element.closest('tr');
    let trackingId = tr?.dataset?.id || element.dataset.id || null;

    // FIX 1: Use name as fallback if data-field is not present
    const fieldName = element.dataset.field || element.name;
    const mfIdRaw   = element.dataset.mf;
    const oiIdRaw   = element.dataset.oi;

    if (!fieldName) {
      console.error('Missing field name on element', element);
      inflight.delete(element);
      return;
    }

    const mfId = mfIdRaw ? parseInt(mfIdRaw, 10) : null;
    const oiId = oiIdRaw ? parseInt(oiIdRaw, 10) : null;

    // FIX 2: Only require master_file_id for CREATE, outdoor_item_id is optional
    if (!trackingId && !mfId) {
      console.error('Missing data-mf for CREATE', { mfId, element });
      inflight.delete(element);
      return;
    }

    const year  = parseInt(document.getElementById('filterYear')?.value ?? '{{ now()->year }}', 10);
    const month = parseInt(document.getElementById('filterMonth')?.value ?? '{{ now()->month }}', 10);
    const fieldValue = normalizeValue(element);

    // Build payload - include outdoor_item_id only if present
    const payload = trackingId
      ? { id: trackingId, field: fieldName, value: fieldValue }
      : {
          master_file_id: mfId,
          ...(oiId ? { outdoor_item_id: oiId } : {}), // Only include if present
          year: Number.isFinite(year) ? year : undefined,
          month: Number.isFinite(month) ? month : undefined,
          field: fieldName,
          value: fieldValue
        };

    showSaveIndicator(element, 'loading');

    try {
      const res = await fetch(`{{ route('coordinator.outdoor.upsert') }}`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': token,
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(payload)
      });

      const text = await res.text();
      let data;
      try { data = JSON.parse(text); } catch { data = { ok: false, message: text }; }

      if (!res.ok || data.ok === false) {
        console.error('Save failed', { status: res.status, data, payload });
        showSaveIndicator(element, 'error');
        inflight.delete(element);
        return;
      }

      // Persist the new id so next edits go through UPDATE path
      if (!trackingId && data.id) {
        const newId = data.id;
        if (tr) tr.setAttribute('data-id', newId);
        tr?.querySelectorAll('input.outdoor-field, select.outdoor-field, textarea.outdoor-field')
          .forEach(inp => inp.setAttribute('data-id', newId));
        console.log('Created tracking id:', newId);
      }

      showSaveIndicator(element, 'success');
    } catch (err) {
      console.error('Save error', err);
      showSaveIndicator(element, 'error');
    } finally {
      inflight.delete(element);
    }
  }

  function showSaveIndicator(element, state) {
    const parent = element.parentElement;
    const indicators = {
      loading: parent?.querySelector('[data-save-indicator]'),
      success: parent?.querySelector('[data-save-success]'),
      error: parent?.querySelector('[data-save-error]')
    };

    Object.values(indicators).forEach(el => el?.classList.add('hidden'));
    if (indicators[state]) {
      indicators[state].classList.remove('hidden');
      if (state !== 'loading') {
        setTimeout(() => indicators[state]?.classList.add('hidden'), 2000);
      }
    }
  }
});
</script>
@endpush
