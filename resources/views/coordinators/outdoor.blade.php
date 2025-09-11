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
                        <form method="GET" action="{{ route('coordinator.outdoor.index') }}" class="space-y-4 relative">
                            {{-- Month --}}
                            <div class="relative z-10">
                                <label for="filterMonth" class="block text-sm font-medium text-gray-700 mb-2">Month</label>
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

                            {{-- Year --}}
                            <div>
                                <label for="filterYear" class="block text-sm font-medium text-gray-700 mb-2">Year</label>
                                <input type="number" id="filterYear" name="year"
                                       value="{{ (int)($year ?? now()->year) }}"
                                       min="2000" max="{{ now()->year + 1 }}"
                                       class="w-full rounded-xl border border-gray-200 bg-gray-50 hover:bg-white px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors">
                            </div>

                            {{-- Only active this month --}}
                            <label class="inline-flex items-center gap-2">
                                <input type="checkbox"
                                       id="toggleActive"
                                       name="active"
                                       value="1"
                                       @checked(request('active'))
                                       @disabled(!request('month'))>
                                <span>Show only active this month</span>
                            </label>

                            {{-- Actions --}}
                            <div class="pt-2 z-0 flex gap-3">
                                <button type="submit"
                                        class="flex-1 inline-flex items-center justify-center rounded-xl bg-indigo-600 text-white py-3 text-sm font-medium hover:bg-indigo-700 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L15 12.414V19l-6-3v-3.586L3.293 6.707A1 1 0 013 6V4z" />
                                    </svg>
                                    Apply Filters
                                </button>

                                <a href="{{ route('coordinator.outdoor.index') }}"
                                   class="inline-flex items-center justify-center rounded-xl border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                    Reset
                                </a>
                            </div>
                        </form>
                    </div>

                    {{-- Actions --}}
                   <a
  href="{{ route('coordinator.outdoor.export', [
      'month' => request('month'),
      'year'  => request('year'),
      // ikutkan filter lain kalau ada (search, status, dll)
      'search' => request('search'),
  ]) }}"
  class="inline-flex items-center px-4 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700"
>
  Export XLSX
</a>
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
                                        // Determine scope based on whether month is selected
                                        $isMonth = isset($month) && $month !== '' && (int)$month >= 1 && (int)$month <= 12;
                                        $scope = $isMonth ? 'omd' : 'oct';

                                        // Use Query Builder aliases directly
                                        $trackingId = $row->tracking_id ?? null;

                                        // Define editable columns and date fields
                                        $editableCols = ['payment','material','artwork','received_approval','sent_to_printer','collection_printer','installation','dismantle'];
                                        $dateCols = ['received_approval','sent_to_printer','collection_printer','installation','dismantle'];
                                    @endphp

                                    <tr class="odd:bg-white even:bg-gray-50 hover:bg-blue-50/50 transition-colors"
                                        data-scope="{{ $scope }}"
                                        data-id="{{ $row->tracking_id ?? '' }}"
                                        data-mf="{{ $row->master_file_id }}"
                                        data-oi="{{ $row->outdoor_item_id }}"
                                        data-year="{{ (int)($year ?? now()->year) }}"
                                        data-month="{{ $isMonth ? (int)$month : '' }}"
                                    >
                                        {{-- ID Column --}}
                                        <td class="sticky left-0 z-30 bg-inherit border-r border-gray-200 px-4 py-4 align-middle border-b border-gray-100 text-center font-medium text-gray-900">
                                            {{ $rows->firstItem() + $i }}
                                        </td>

                                        {{-- Company (Read-only) --}}
                                        <td class="sticky left-[80px] z-30 bg-inherit border-r border-gray-200 px-4 py-4 align-middle border-b border-gray-100">
                                            <div class="font-medium text-gray-900 bg-gray-50 rounded-lg px-3 py-2">
                                                {{ $row->company ?? '-' }}
                                            </div>
                                        </td>

                                        {{-- Person In Charge (Read-only) - Use client field or other appropriate field --}}
                                        <td class="sticky left-[280px] z-30 bg-inherit border-r border-gray-200 px-4 py-4 align-middle border-b border-gray-100">
                                            <div class="text-gray-700 bg-gray-50 rounded-lg px-3 py-2">
                                                {{-- Adjust this field name based on your actual data structure --}}
                                                {{ $row->client ?? $row->client ?? '-' }}
                                            </div>
                                        </td>

                                        {{-- Product (Read-only) --}}
                                        <td class="sticky left-[480px] z-30 bg-inherit border-r px-4 py-4 align-middle border-b border-gray-100">
                                            <div class="text-gray-700 bg-gray-50 rounded-lg px-3 py-2">
                                                {{ $row->product ?? '-' }}
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
                                                            data-field="{{ $col }}"
                                                            data-scope="{{ $scope }}" />
                                                    @else
                                                        <input type="text"
                                                            class="w-44 border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 outdoor-field"
                                                            value="{{ $val }}"
                                                            data-id="{{ $trackingId }}"
                                                            data-mf="{{ $row->master_file_id }}"
                                                            data-oi="{{ $row->outdoor_item_id }}"
                                                            data-field="{{ $col }}"
                                                            data-scope="{{ $scope }}"
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

                            @else
                                {{-- Empty State --}}
                                <tr>
                                    <td colspan="13" class="px-6 py-16 text-center text-gray-500">
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
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const token = document.querySelector('meta[name="csrf-token"]')?.content || '';

  function getYM() {
    const y = document.getElementById('ctxYear')?.value ?? document.getElementById('filterYear')?.value ?? '';
    const m = document.getElementById('ctxMonth')?.value ?? document.getElementById('filterMonth')?.value ?? '';
    const year  = Number.parseInt(String(y), 10);
    const month = Number.parseInt(String(m), 10);
    return {
      year:  Number.isFinite(year)  ? year  : null,
      month: Number.isFinite(month) ? month : null,
    };
  }

  function normalizeValue(el) {
    if (el.type === 'checkbox') return el.checked ? 1 : 0;
    if (el.type === 'date' && el.value) return el.value.trim();
    return typeof el.value === 'string' ? el.value.trim() : el.value;
  }

  function findRowContext(el) {
    const tr = el.closest('tr');
    const id  = el.dataset.id || tr?.dataset.id || null;
    const mf  = el.dataset.mf || tr?.dataset.mf || null;
    const oi  = el.dataset.oi || tr?.dataset.oi || null;
    const scopeAttr = el.dataset.scope || tr?.dataset.scope || '';
    return {
      tr,
      id,
      scope: scopeAttr || (getYM().month ? 'omd' : 'oct'), // fallback if missing
      mf: mf ? parseInt(mf,10) : null,
      oi: oi ? parseInt(oi,10) : null
    };
  }

  function showSaveIndicator(element, state) {
    const parent = element.parentElement;
    const map = {
      loading: parent?.querySelector('[data-save-indicator]'),
      success: parent?.querySelector('[data-save-success]'),
      error:   parent?.querySelector('[data-save-error]')
    };
    Object.values(map).forEach(n => n?.classList.add('hidden'));
    if (map[state]) {
      map[state].classList.remove('hidden');
      if (state !== 'loading') setTimeout(() => map[state]?.classList.add('hidden'), 1800);
    }
  }

  const inflight = new Set();

  async function saveField(element) {
    if (!element.classList?.contains('outdoor-field')) return;
    if (inflight.has(element)) return;
    inflight.add(element);

    const { tr, id: trackingId, scope, mf: masterFileId, oi: outdoorItemId } = findRowContext(element);
    const fieldName  = element.dataset.field || element.name || '';
    const fieldValue = normalizeValue(element);
    const { year, month } = getYM();

    if (!fieldName) {
      console.error('Missing field name', element);
      inflight.delete(element);
      return;
    }

    // ---- Build payload by scope ----
    let payload = null;

    if (scope === 'omd') {
      // Month mode: always upsert into OMD using (oi, year, month, field)
      if (!outdoorItemId) {
        console.error('Missing outdoor_item_id (data-oi) for month scope', { element });
        inflight.delete(element);
        return;
      }
      if (month === null || year === null) {
        console.warn('Month scope without concrete month/year.');
        inflight.delete(element);
        return;
      }
      payload = {
        // id is NOT needed for OMD upsert
        master_file_id: masterFileId,    // optional but nice to have
        outdoor_item_id: outdoorItemId,
        year, month,
        field: fieldName,
        value: fieldValue
      };
    } else {
      // Baseline (All Months): write/read from OCT
      if (trackingId) {
        payload = { id: trackingId, field: fieldName, value: fieldValue };
      } else {
        if (!masterFileId) {
          console.error('Missing master_file_id (data-mf) for baseline create', { element });
          inflight.delete(element);
          return;
        }
        // ALLOW create even when All Months (this makes data persist after refresh)
        payload = {
          master_file_id: masterFileId,
          ...(outdoorItemId ? { outdoor_item_id: outdoorItemId } : {}),
          field: fieldName,
          value: fieldValue
        };
      }
    }

    console.log('Payload being sent:', JSON.stringify(payload, null, 2));
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
      let data; try { data = JSON.parse(text); } catch { data = { success:false, error:text }; }

      if (!res.ok || data.success === false) {
        let msg = res.status + ' ' + res.statusText;
        if (data?.error) msg += ` – ${data.error}`;
        console.error('Save failed:', msg, { payload });
        showSaveIndicator(element, 'error');
        return;
      }

      // After CREATE: use returned tracking_id
      const newId = data.data?.tracking_id || data.tracking_id || data.id || null;
      if (!trackingId && newId) {
        tr?.setAttribute('data-id', newId);
        tr?.querySelectorAll('.outdoor-field').forEach(inp => inp.setAttribute('data-id', newId));
        // Ensure scope remains correct
        if (!tr?.dataset.scope) tr?.setAttribute('data-scope', scope);
        console.log('Created tracking id:', newId, 'scope:', scope);
      }

      showSaveIndicator(element, 'success');
    } catch (err) {
      console.error('Save error', err);
      showSaveIndicator(element, 'error');
    } finally {
      inflight.delete(element);
    }
  }

  document.addEventListener('blur',   e => saveField(e.target), true);
  document.addEventListener('change', e => saveField(e.target));

  // Disable "Active this month" when Month = All
  const selMonth = document.getElementById('filterMonth');
  const togActive = document.getElementById('toggleActive');
  function syncActiveToggle() {
    const m = selMonth ? selMonth.value : (document.getElementById('ctxMonth')?.value ?? '');
    const hasMonth = m !== '';
    if (togActive) {
      togActive.disabled = !hasMonth;
      if (!hasMonth) togActive.checked = false;
    }
  }
  selMonth?.addEventListener('change', syncActiveToggle);
  syncActiveToggle();
});
</script>
@endpush
