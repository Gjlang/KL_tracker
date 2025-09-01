@php
    // Ensure $existing is always a Collection, even if not passed
    /** @var \Illuminate\Support\Collection $existing */
    $existing = isset($existing) && $existing ? collect($existing) : collect();

    // Helper to read saved values without throwing errors
    function omd($existing, $id, $m, $key, $type) {
        $row = $existing->get("{$id}:{$m}:{$key}");
        if (!$row) return '';
        return $type === 'date'
            ? optional($row->value_date)->format('Y-m-d')
            : ($row->value_text ?? '');
    }
@endphp

{{-- at the top of the file, make sure you have CSRF meta if your layout doesn't --}}
@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

<x-app-layout>
  <div class="flex min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-50">
    @include('partials.sidebar')
    <main class="flex-1 overflow-y-auto">
      <div class="p-4 md:p-8 max-w-[100vw]">


        <!-- Back to Dashboard -->
                    <button onclick="window.history.back()" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Dashboard
                    </button>

        {{-- Header Section --}}
        <div class="mb-8">
          <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <div>
              <h1 class="text-3xl font-bold text-gray-900 mb-2">🏞️ Outdoor Monthly Ongoing Jobs</h1>
              <p class="text-gray-600">Manage and track outdoor advertising campaigns across all locations</p>
            </div>
          </div>

          {{-- Flash Messages --}}
          @if(session('status'))
            <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 shadow-sm">
              <div class="flex items-center">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('status') }}
              </div>
            </div>
          @endif
        </div>

        {{-- Filters Panel --}}
        <div class="mb-8 p-6 bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-200">
          <form method="GET" action="{{ route('dashboard') }}">
            <!-- Preserve existing filters -->
            <input type="hidden" name="search" value="{{ request('search') }}">
            <input type="hidden" name="status" value="{{ request('status') }}">
            <input type="hidden" name="month" value="{{ request('month') }}">
            <input type="hidden" name="product_category" value="{{ request('product_category') }}">

            {{-- Outdoor page is hard-locked to Outdoor --}}
            <input type="hidden" id="category" name="category" value="Outdoor">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-4">
              <!-- Category Display (locked to Outdoor) -->
              <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">🏷️ Category</label>
                <span class="inline-flex items-center px-3 py-2 text-sm font-semibold rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-700">
                  🏞️ Outdoor Only
                </span>
              </div>

              <!-- Year Filter -->
              <div class="space-y-2">
                <label for="outdoor_year" class="block text-sm font-semibold text-gray-700">📅 Year</label>
                <select name="outdoor_year" id="outdoor_year" class="w-full rounded-xl border border-gray-200 bg-gray-50 hover:bg-white px-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200">
                  <option value="">All Years</option>
                  @foreach($availableYears->unique() as $y)
                    <option value="{{ $y }}" {{ (string)request('outdoor_year', $year) === (string)$y ? 'selected' : '' }}>
                      {{ $y }}
                    </option>
                  @endforeach
                </select>
              </div>
            </div>

            <!-- Active filters display -->
            @if(request('outdoor_year') || request('outdoor_client') || request('outdoor_state') || request('outdoor_status'))
              <div class="mt-6 p-4 bg-gradient-to-r from-emerald-50 to-green-50 rounded-xl border border-emerald-100">
                <div class="flex items-center justify-between text-sm">
                  <div class="flex flex-wrap items-center gap-2">
                    <span class="font-semibold text-emerald-800">Active filters:</span>
                    <span class="inline-flex items-center px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full text-xs font-medium ring-1 ring-emerald-200">
                      Category: Outdoor
                    </span>
                    @if(request('outdoor_year'))
                      <span class="inline-flex items-center px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full text-xs font-medium ring-1 ring-emerald-200">
                        Year: {{ request('outdoor_year') }}
                      </span>
                    @endif
                    @if(request('outdoor_client'))
                      <span class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium ring-1 ring-blue-200">
                        Client: {{ request('outdoor_client') }}
                      </span>
                    @endif
                    @if(request('outdoor_state'))
                      <span class="inline-flex items-center px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-medium ring-1 ring-purple-200">
                        State: {{ request('outdoor_state') }}
                      </span>
                    @endif
                    @if(request('outdoor_status'))
                      <span class="inline-flex items-center px-3 py-1 bg-orange-100 text-orange-800 rounded-full text-xs font-medium ring-1 ring-orange-200">
                        Status: {{ request('outdoor_status') }}
                      </span>
                    @endif
                  </div>
                  <div class="text-emerald-700 font-medium">
                    {{ isset($rows) ? $rows->count() : 0 }} outdoor records found
                  </div>
                </div>
              </div>
            @endif
          </form>

          <!-- Quick Action Button -->
          <div class="mt-6 pt-4 border-t border-gray-100">
            <a href="{{ route('coordinator.outdoor.index') }}"
               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-yellow-500 to-yellow-600 text-yellow-900 font-semibold rounded-xl hover:from-yellow-600 hover:to-yellow-700 shadow-sm hover:shadow-md transition-all duration-200 transform hover:-translate-y-0.5">
              🏞️ Outdoor Coordinator List
            </a>
          </div>
        </div>

        {{-- Main Data Table --}}
        <div class="rounded-2xl bg-white shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-200 overflow-hidden">
          <div class="overflow-x-auto">
            <table class="min-w-[3250px] w-full text-sm">
              <thead class="sticky top-0 z-10 bg-gradient-to-r from-gray-50 to-gray-100">
                <tr>
                  <th class="px-4 py-4 text-xs font-bold text-gray-700 uppercase tracking-wider border-b border-gray-200" style="min-width:110px;width:110px;">
                    📅 Date
                  </th>
                  <th class="px-4 py-4 text-xs font-bold text-gray-700 uppercase tracking-wider border-b border-gray-200" style="min-width:220px;width:220px;">
                    🏢 Company
                  </th>
                  <th class="px-4 py-4 text-xs font-bold text-gray-700 uppercase tracking-wider border-b border-gray-200" style="min-width:160px;width:160px;">
                    📦 Product
                  </th>
                  <th class="px-4 py-4 text-xs font-bold text-gray-700 uppercase tracking-wider border-b border-gray-200" style="min-width:140px;width:140px;">
                    🏷️ Category
                  </th>
                  <th class="px-4 py-4 text-xs font-bold text-gray-700 uppercase tracking-wider border-b border-gray-200" style="min-width:120px;width:120px;">
                    ▶️ Start
                  </th>
                  <th class="px-4 py-4 text-xs font-bold text-gray-700 uppercase tracking-wider border-b border-gray-200" style="min-width:120px;width:120px;">
                    ⏹️ End
                  </th>
                  @php
                      $months = [1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',
                                 7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December'];
                  @endphp
                  @foreach($months as $mNum => $mName)
                    <th class="px-3 py-4 text-xs font-bold text-gray-700 uppercase tracking-wider bg-gradient-to-b from-blue-50 to-blue-100 border-b border-gray-200" style="min-width:180px;width:180px;">
                      {{ $mName }}
                    </th>
                  @endforeach
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-100">
                {{-- rows already Outdoor-scoped in controller --}}
                @forelse($rows ?? [] as $index => $row)
                  @php
                      $company = $row->company ?? $row->client;
                      $start   = $row->start_date ?? $row->date ?? null;
                      $end     = $row->date_finish ?? $row->end_date ?? null;

                      // Check if all monthly fields have values (using existing structure)
                      $monthFields = ['check_jan','check_feb','check_mar','check_apr','check_may','check_jun',
                                    'check_jul','check_aug','check_sep','check_oct','check_nov','check_dec'];
                      $complete = $start && $end && collect($monthFields)->every(fn($field) => !empty($row->$field));
                  @endphp
                  <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-blue-50/60 transition-colors duration-150">
                    <td class="px-4 py-3 align-top border-b border-gray-100 text-gray-600 font-medium">
                      {{ optional($row->date ?? null)->format('Y-m-d') ?? \Illuminate\Support\Carbon::parse($row->created_at)->format('Y-m-d') }}
                    </td>
                    <td class="px-4 py-3 align-top border-b border-gray-100 font-semibold text-gray-900">
                      {{ $company }}
                    </td>
                    <td class="px-4 py-3 align-top border-b border-gray-100 text-gray-700">
                      {{ $row->product }}
                    </td>
                    <td class="px-4 py-3 align-top border-b border-gray-100">
                      <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200">
                        {{ $row->product_category ?? 'Outdoor' }}
                      </span>
                    </td>
                    <td class="px-4 py-3 align-top border-b border-gray-100 text-gray-700 font-medium">
                      {{ $start }}
                    </td>
                    <td class="px-4 py-3 align-top border-b border-gray-100 text-red-600 font-medium">
                      {{ $end }}
                    </td>

                    {{-- Month cells (STATUS DROPDOWN + DATE) --}}
                    @foreach($months as $mNum => $mName)
                      @php
                        $savedStatus = omd($existing, $row->id, $mNum, 'status', 'text');        // '' or 'Installation' | ...
                        $savedDate   = omd($existing, $row->id, $mNum, 'installed_on', 'date'); // '' or 'YYYY-MM-DD'
                      @endphp
                      <td class="px-3 py-3 align-top border-b border-gray-100 bg-blue-50/30">
                        <div class="space-y-2">
                          <!-- Status dropdown -->
                          <select
                            class="status-dropdown w-full text-xs font-semibold rounded-lg border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:outline-none bg-white hover:bg-gray-50 shadow-sm transition-all duration-200"
                            data-master="{{ $row->id }}"
                            data-year="{{ $year }}"
                            data-month="{{ $mNum }}"
                            data-kind="text"
                            name="status_{{ $row->id }}_{{ $year }}_{{ $mNum }}"
                            onchange="saveOutdoorCell(this); setDropdownColor(this);">
                              <option value=""></option>
                              <option value="Installation" {{ $savedStatus==='Installation' ? 'selected' : '' }}>🔧 Installation</option>
                              <option value="Dismentel"   {{ $savedStatus==='Dismentel'   ? 'selected' : '' }}>🔨 Dismentel</option>
                              <option value="Artwork"     {{ $savedStatus==='Artwork'     ? 'selected' : '' }}>🎨 Artwork</option>
                              <option value="Payment"     {{ $savedStatus==='Payment'     ? 'selected' : '' }}>💳 Payment</option>
                              <option value="Ongoing"     {{ $savedStatus==='Ongoing'     ? 'selected' : '' }}>⚡ Ongoing</option>
                              <option value="Renewal"     {{ $savedStatus==='Renewal'     ? 'selected' : '' }}>🔄 Renewal</option>
                              <option value="Completed"   {{ $savedStatus==='Completed'   ? 'selected' : '' }}>✅ Completed</option>
                              <option value="Material"    {{ $savedStatus==='Material'    ? 'selected' : '' }}>📦 Material</option>
                          </select>

                          <!-- Date input -->
                          <input
                            type="date"
                            value="{{ $savedDate }}"
                            class="w-full text-xs rounded-lg border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:outline-none bg-white hover:bg-gray-50 shadow-sm transition-all duration-200"
                            data-master="{{ $row->id }}"
                            data-year="{{ $year }}"
                            data-month="{{ $mNum }}"
                            data-kind="date"
                            name="date_{{ $row->id }}_{{ $year }}_{{ $mNum }}"
                            onblur="saveOutdoorCell(this)">

                          <!-- Status indicators -->
                          <div class="flex items-center justify-between">
                            <small class="hidden text-emerald-600 font-medium" data-saved>✓ Saved</small>
                            <small class="hidden text-red-600 font-medium" data-error></small>
                          </div>
                        </div>
                      </td>
                    @endforeach
                  </tr>
                @empty
                  <tr>
                    <td colspan="21" class="px-6 py-16 text-center">
                      <div class="flex flex-col items-center">
                        <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-gray-500 text-lg font-medium">No outdoor jobs found</p>
                        <p class="text-gray-400 text-sm mt-1">Try adjusting your filters or add some outdoor jobs</p>
                      </div>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>

        <a
  href="{{ route('coordinator.outdoor.exportMatrix', ['year' => $year, 'product' => $product]) }}"
  class="btn btn-primary">
  Export CSV
</a>

      </div>
    </main>
  </div>
</x-app-layout>

<script>
// Outdoor autosave function
async function saveOutdoorCell(el) {
  const master_file_id = Number(el.dataset.master);
  const year  = Number(el.dataset.year);
  const month = Number(el.dataset.month);
  const kind  = el.dataset.kind; // "text" | "date"

  const payload = {
    master_file_id,
    year,
    month,
    field_key: kind === 'date' ? 'installed_on' : 'status',
    field_type: kind, // 'text' or 'date'
  };
  if (kind === 'date') payload.value_date = el.value || null;
  else payload.value_text = (el.value ?? '').toString();

  try {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || "{{ csrf_token() }}";
    const res = await fetch("{{ route('outdoor.monthly.upsert') }}", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": csrfToken,
        "Accept": "application/json",
      },
      body: JSON.stringify(payload),
    });

    if (!res.ok) throw await res.json().catch(() => ({ message: res.statusText }));

    // Success feedback
    const td = el.closest('td');
    const saved = td?.querySelector('[data-saved]');
    saved?.classList.remove('hidden');
    setTimeout(() => saved?.classList.add('hidden'), 2000);

    // Visual feedback
    el.classList.remove('border-red-500', 'border-gray-300');
    el.classList.add('border-emerald-500', 'bg-emerald-50');
    setTimeout(() => {
      el.classList.remove('border-emerald-500', 'bg-emerald-50');
      el.classList.add('border-gray-300');
    }, 1500);
  } catch (e) {
    // Error feedback
    const td = el.closest('td');
    const err = td?.querySelector('[data-error]');
    el.classList.remove('border-gray-300');
    el.classList.add('border-red-500', 'bg-red-50');
    if (err) {
      err.textContent = (e?.message) || 'Save failed';
      err.classList.remove('hidden');
    }
    setTimeout(() => {
      el.classList.remove('bg-red-50');
    }, 3000);
  }
}

// Status dropdown color mapping (exact matches)
function setDropdownColor(selectEl) {
  const map = {
  'Installation': { bg:'#dc2626', color:'#fff', border:'#991b1b' }, // 🔴 red-600
  'Dismentel':    { bg:'#dc2626', color:'#fff', border:'#991b1b' }, // 🔴 red
  'Payment':      { bg:'#dc2626', color:'#fff', border:'#991b1b' }, // 🔴 red
  'Renewal':      { bg:'#dc2626', color:'#fff', border:'#991b1b' }, // 🔴 red

  'Completed':    { bg:'#16a34a', color:'#fff', border:'#166534' }, // ✅ green-600

  'Artwork':      { bg:'#f97316', color:'#fff', border:'#c2410c' }, // 🟠 orange-500
  'Material':     { bg:'#f97316', color:'#fff', border:'#c2410c' }, // 🟠 orange

  'Ongoing':      { bg:'#38bdf8', color:'#111827', border:'#0ea5e9' } // 🔵 light blue (sky-400/500)
};


  // base classes (keep your sizing/focus styles)
  selectEl.className =
    'status-dropdown w-full text-xs font-semibold rounded-lg border px-3 py-2 ' +
    'focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:outline-none ' +
    'shadow-sm transition-all duration-200';

  const s = map[selectEl.value];
  if (s) {
    selectEl.style.backgroundColor = s.bg;
    selectEl.style.color = s.color;
    selectEl.style.borderColor = s.border;
    selectEl.classList.add('shadow-md');
  } else {
    // default/empty
    selectEl.style.backgroundColor = '#ffffff';
    selectEl.style.color = '#111827';
    selectEl.style.borderColor = '#d1d5db';
  }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
  // Call once on load to style preselected dropdowns
  document.querySelectorAll('.status-dropdown').forEach(selectEl => {
    setDropdownColor(selectEl);

    // Add change event listener to apply colors when dropdown changes
    selectEl.addEventListener('change', function() {
      setDropdownColor(this);
    });
  });
});

// Enhanced move to ongoing function
function moveToOngoing(id) {
    if (confirm('🚀 Move this job to ongoing status?\n\nThis action cannot be undone.')) {
        // Add loading state
        const button = event.target;
        const originalText = button.textContent;
        button.textContent = 'Moving...';
        button.disabled = true;

        setTimeout(() => {
            button.textContent = originalText;
            button.disabled = false;
            alert('✅ Move to ongoing functionality will be implemented soon!');
        }, 1000);
    }
}
</script>
