<x-app-layout>
  {{-- ✅ TAMBAHKAN CSRF TOKEN META TAG INI --}}
  @push('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
  @endpush

  {{-- Header & action --}}
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
  <h2 class="text-2xl font-bold">📊 MONTHLY Ongoing Job – KL The Guide</h2>

  {{-- Use a plain anchor to avoid form submission to the same page --}}
  <a href="{{ route('coordinator.kltg.index') }}"
     class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition shadow-sm">
    🗂️ Open KLTG Coordinator
  </a>
</div>
    {{-- Enhanced Filter Section with Month/Year --}}
    <div class="mb-6 p-6 bg-gradient-to-r from-white to-gray-50 rounded-xl shadow-lg border border-gray-200">
      <div class="flex items-center justify-between mb-5">
        <h4 class="text-xl font-bold text-gray-800 flex items-center gap-2">
          <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
          </svg>
          Advanced Filters
        </h4>
        <div class="text-sm text-gray-500">
          <span id="filter-count">All records visible</span>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">

        <!-- Month Filter -->
        <div class="space-y-2">
          <label for="filter-month" class="block text-sm font-semibold text-gray-700">Month</label>
          <select id="filter-month" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white shadow-sm transition-all duration-200">
            <option value="">All Months</option>
            <option value="January">January</option>
            <option value="February">February</option>
            <option value="March">March</option>
            <option value="April">April</option>
            <option value="May">May</option>
            <option value="June">June</option>
            <option value="July">July</option>
            <option value="August">August</option>
            <option value="September">September</option>
            <option value="October">October</option>
            <option value="November">November</option>
            <option value="December">December</option>
          </select>
        </div>

        <!-- Year Filter -->
        <div class="space-y-2">
          <label for="filter-year" class="block text-sm font-semibold text-gray-700">Filter Year</label>
          <select id="filter-year" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white shadow-sm transition-all duration-200">
            <option value="">All Years</option>
            <option value="2024">2024</option>
            <option value="2025">2025</option>
            <option value="2026">2026</option>
          </select>
        </div>

        <!-- Clear Filters -->
        <div class="flex items-end">
          <button id="clear-filters" class="w-full px-4 py-2.5 bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 rounded-lg hover:from-gray-200 hover:to-gray-300 transition-all duration-200 font-semibold shadow-sm border border-gray-300 flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            Clear All
          </button>
        </div>
      </div>

      <!-- Filter Summary -->
      <div id="filter-summary" class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg hidden">
        <div class="text-sm text-blue-800 font-medium">
          Active Filters: <span id="active-filters"></span>
        </div>
      </div>
    </div>

    {{-- Enhanced Table Section with Fixed Header Alignment and Column Issues --}}
<div class="overflow-x-auto border rounded-lg bg-white shadow-sm" style="max-height: 80vh;">
  <table class="min-w-[5500px] w-full text-sm border-collapse">
    <thead class="sticky top-0 z-20 bg-white">
      {{-- First Header Row with FIXED sticky offsets --}}
      <tr class="bg-yellow-100">
        <th class="sticky left-0 z-30 bg-yellow-100 border border-gray-400 px-3 py-2 text-left font-bold text-gray-700 whitespace-nowrap">No</th>
        {{-- ✅ Fixed: Month header with correct offset --}}
        <th class="sticky left-[60px] z-30 bg-yellow-100 border border-gray-400 px-3 py-2 text-left font-bold text-gray-700 whitespace-nowrap">Month</th>
        {{-- ✅ Fixed: Created At header with different offset --}}
        <th class="sticky left-[140px] z-30 bg-yellow-100 border border-gray-400 px-3 py-2 text-left font-bold text-gray-700 whitespace-nowrap">Created At</th>
        <th class="sticky left-[280px] z-30 bg-yellow-100 border border-gray-400 px-3 py-2 text-left font-bold text-gray-700 whitespace-nowrap">Company</th>
        <th class="sticky left-[430px] z-30 bg-yellow-100 border border-gray-400 px-3 py-2 text-left font-bold text-gray-700 whitespace-nowrap">Product</th>
        <th class="sticky left-[530px] z-30 bg-yellow-100 border border-gray-400 px-3 py-2 text-left font-bold text-gray-700 whitespace-nowrap">Publication</th>
        <th class="sticky left-[680px] z-30 bg-yellow-100 border border-gray-400 px-3 py-2 text-left font-bold text-gray-700 whitespace-nowrap">Status</th>
        <th class="border border-gray-400 px-3 py-2 text-left font-bold text-gray-700 whitespace-nowrap">Start</th>
        <th class="border border-gray-400 px-3 py-2 text-left font-bold text-gray-700 whitespace-nowrap">End</th>
        <th class="border border-gray-400 px-3 py-2 text-left font-bold text-gray-700 whitespace-nowrap">Edition</th>

        @for ($m=1; $m<=12; $m++)
          <th class="px-3 py-2 text-center border-l bg-gray-100 font-bold text-gray-700 min-w-[900px]">
            <!-- Empty header, month name will be shown inside each cell -->
          </th>
        @endfor
      </tr>
    </thead>

    <tbody>
      @if(isset($rows) && count($rows) > 0)
        @foreach ($rows as $i => $r)
          <tr class="border-t table-row {{ $i % 2 === 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-green-50 transition-colors duration-200"
            data-master="{{ $r['id'] ?? '' }}"
            data-status="{{ strtolower($r['status'] ?? '') }}"
            data-company="{{ strtolower($r['company'] ?? '') }}"
            data-product="{{ strtolower($r['product'] ?? '') }}"
            data-year="{{ $year ?? date('Y') }}"
            data-month="{{ $r['month_name'] ?? '' }}"
            data-created-date="{{ $r['created_at'] ?? '' }}">
            {{-- Fixed Columns with matching offsets --}}
            <td class="sticky left-0 z-10 bg-inherit border border-gray-300 px-3 py-2 align-top text-gray-900">{{ $i+1 }}</td>

            {{-- ✅ Fixed: Month cell shows month_name, not created_at --}}
            <td class="sticky left-[60px] z-10 bg-inherit border border-gray-300 px-3 py-2 align-top text-gray-900">{{ $r['month_name'] ?? '' }}</td>

            {{-- ✅ Fixed: Created At cell with correct offset --}}
            <td class="sticky left-[140px] z-10 bg-inherit border border-gray-300 px-3 py-2 align-top text-gray-900">{{ $r['created_at'] ?? '' }}</td>

            <td class="sticky left-[280px] z-10 bg-inherit border border-gray-300 px-3 py-2 align-top text-gray-900" style="max-width:150px;">
              <div class="truncate pr-1" title="{{ $r['company'] ?? '' }}">{{ $r['company'] ?? 'N/A' }}</div>
            </td>

            <td class="sticky left-[430px] z-10 bg-inherit border border-gray-300 px-3 py-2 align-top">
              <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium">
                {{ $r['product'] ?? 'N/A' }}
              </span>
            </td>

            {{-- Publication (always editable with enhanced styling) --}}
            <td class="sticky left-[530px] z-10 bg-inherit border border-gray-300 px-3 py-2 align-top">
              <input
                class="w-32 border rounded px-2 py-1 text-sm auto-save-input"
                value="{{ $r['publication'] ?? '' }}"
                data-master="{{ $r['id'] ?? '' }}"
                data-year="{{ $year ?? date('Y') }}"
                data-field="publication"
                oninput="debouncedSave(this)"
                placeholder="Type name…">
            </td>

            {{-- Status with enhanced styling --}}
            <td class="sticky left-[680px] z-10 bg-inherit border border-gray-300 px-3 py-2 align-top">
              <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                  {{ strtolower($r['status'] ?? '') === 'completed' ? 'bg-green-100 text-green-800 border border-green-200' :
                     (strtolower($r['status'] ?? '') === 'ongoing' ? 'bg-yellow-100 text-yellow-800 border border-yellow-200' :
                      'bg-white-100 text-rose-800 border border-rose-200') }}">
                {{ $r['status'] ?? 'Pending' }}
              </span>
            </td>

            <td class="border border-gray-300 px-3 py-2 align-top text-gray-900">{{ $r['start'] ?? '' }}</td>
            <td class="border border-gray-300 px-3 py-2 align-top text-gray-900">{{ $r['end'] ?? '' }}</td>
            <td class="sticky left-[530px] z-10 bg-inherit border border-gray-300 px-3 py-2 align-top">
              <input
                class="w-32 border rounded px-2 py-1 text-sm auto-save-input"
                value="{{ $r['publication'] ?? '' }}"
                data-master="{{ $r['id'] ?? '' }}"
                data-year="{{ $year ?? date('Y') }}"
                data-field="publication"
                oninput="debouncedSave(this)"
                placeholder="Type name…">
            </td>

            {{-- Monthly Category Input Cells --}}
            @for ($m=1; $m<=12; $m++)
              @php
                $cats = [
                  ['code' => 'KLTG',   'label' => 'KLTG'],
                  ['code' => 'VIDEO',  'label' => 'Video'],
                  ['code' => 'ARTICLE','label' => 'Article'],
                  ['code' => 'LB',     'label' => 'LB'],
                  ['code' => 'EM',     'label' => 'EM'],
                ];
              @endphp

              <td class="px-1 py-1 align-top border border-gray-300 month-cell" data-month="{{ $m }}">
                <div class="min-w-[900px] border border-gray-400 rounded bg-white">
                  <!-- Month header -->
                  <div class="text-center py-2 border-b border-gray-400 bg-gray-200">
                    <h4 class="font-bold text-sm text-gray-800">{{ \Carbon\Carbon::create()->startOfYear()->month($m)->format('F') }}</h4>
                  </div>

                  <!-- Categories in horizontal layout -->
                  <div class="flex h-full">
                    @foreach($cats as $index => $c)
                      <div class="flex-1 flex flex-col {{ $index < count($cats) - 1 ? 'border-r border-gray-400' : '' }}">
                        <!-- Category header -->
                        <div class="text-center py-2 bg-gray-100 border-b border-gray-400 flex-shrink-0">
                          <div class="text-xs font-semibold text-gray-700">{{ $c['label'] }}</div>
                        </div>

                        <!-- Input container -->
                        <div class="flex flex-col flex-1 p-2 space-y-2">
                          <!-- Text input -->
                          @php
                            $gridKey = sprintf('%02d_%s', $m, $c['code']);
                          @endphp

                          <!-- ✅ Fixed: Text input uses correct category code, not "PUBLICATION" -->
                          <select
                            class="border border-gray-300 rounded px-2 py-2 text-xs w-full bg-white focus:ring-1 focus:ring-blue-400 focus:border-blue-400"
                            data-input="text"
                            data-master="{{ $r['id'] ?? '' }}"
                            data-year="{{ $year ?? date('Y') }}"
                            data-month="{{ $m }}"
                            data-category="{{ $c['code'] }}"
                             onchange="saveCell(this); setDropdownColor(this);"
                                >
                                    <option value=""></option>
                                    <option value="Installation" style="color:red;" {{ ($r['grid'][$gridKey]['text'] ?? '') == 'Installation' ? 'selected' : '' }}>Installation</option>
                                    <option value="Dismentel" style="color:red;" {{ ($r['grid'][$gridKey]['text'] ?? '') == 'Dismentel' ? 'selected' : '' }}>Dismentel</option>
                                    <option value="Artwork" style="color:orange;" {{ ($r['grid'][$gridKey]['text'] ?? '') == 'Artwork' ? 'selected' : '' }}>Artwork</option>
                                    <option value="Payment" style="color:red;" {{ ($r['grid'][$gridKey]['text'] ?? '') == 'Payment' ? 'selected' : '' }}>Payment</option>
                                    <option value="Ongoing" style="color:lightblue;" {{ ($r['grid'][$gridKey]['text'] ?? '') == 'Ongoing' ? 'selected' : '' }}>Ongoing</option>
                                    <option value="Renewal" style="color:red;" {{ ($r['grid'][$gridKey]['text'] ?? '') == 'Renewal' ? 'selected' : '' }}>Renewal</option>
                                    <option value="Completed" style="color:green;" {{ ($r['grid'][$gridKey]['text'] ?? '') == 'Completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="Material" style="color:orange;" {{ ($r['grid'][$gridKey]['text'] ?? '') == 'Material' ? 'selected' : '' }}>Material</option>
                                    <option value="Whatsapp" style="color:green;" {{ ($r['grid'][$gridKey]['text'] ?? '') == 'Whatsapp' ? 'selected' : '' }}>Whatsapp</option>
                                    <option value="Posted" style="color:green;" {{ ($r['grid'][$gridKey]['text'] ?? '') == 'Posted' ? 'selected' : '' }}>Posted</option>
                                </select>
                          <!-- Date input - STAYS FILLED after save -->
                          @php
                            $inputId = "date-y{$year}-m{$m}-{$c['code']}-{$r['id']}-" . uniqid();
                          @endphp

                          <div class="flex items-center gap-1">
                            <input
                              id="{{ $inputId }}"
                              type="date"
                              class="border rounded px-2 py-1 w-full text-xs"
                              value="{{ $r['grid'][$gridKey]['date'] ?? '' }}"
                              data-input="date"
                              data-master="{{ $r['id'] ?? '' }}"
                              data-year="{{ $year }}"
                              data-month="{{ $m }}"
                              data-category="{{ $c['code'] }}"
                              onchange="saveCell(this)">
                            <button type="button"
                              class="p-1 border rounded text-xs hover:bg-gray-100 flex-shrink-0"
                              onclick="document.getElementById('{{ $inputId }}').showPicker()"
                              title="Open calendar">📅</button>
                          </div>
                        </div>
                      </div>
                    @endforeach
                  </div>
                </div>
              </td>
            @endfor
          </tr>
        @endforeach
      @else
        <tr>
          <td colspan="21" class="border border-gray-300 px-6 py-12 text-center text-gray-500">
            <div class="flex flex-col items-center">
              <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
              </svg>
              <h4 class="text-lg font-medium text-gray-900 mb-2">No data found</h4>
              <p class="text-gray-600">No master files available to display in the table.</p>
            </div>
          </td>
        </tr>
      @endif
    </tbody>
  </table>
</div>

<script>
    const UPDATE_URL = "{{ route('kltg.details.upsert') }}";

// ✅ FIXED CSRF TOKEN HANDLING
function getCSRFToken() {
  // Try multiple ways to get CSRF token
  let token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

  if (!token) {
    // Try from Laravel global if available
    token = window.Laravel?.csrfToken || "{{ csrf_token() }}";
  }

  console.log('🔍 CSRF Token found:', token ? 'YES' : 'NO');
  return token;
}

// ✅ DEBOUNCED SAVE FOR ALL FIELDS INCLUDING PUBLICATION
let saveTimeout;
function debouncedSave(el) {
    clearTimeout(saveTimeout);
    saveTimeout = setTimeout(() => {
        saveCell(el);
    }, 500);
}

// ✅ SPECIALIZED FUNCTION FOR PUBLICATION FIELD
function savePublicationField(el) {
    console.log('🔍 savePublicationField called for element:', el);

    // Get CSRF Token
    const csrfToken = getCSRFToken();
    if (!csrfToken) {
        console.error('❌ CSRF token not found');
        alert('CSRF token missing. Please refresh the page.');
        return;
    }

    // Get master file ID
    let master = parseInt(el.dataset.master, 10);
    if (!master || isNaN(master)) {
        const tr = el.closest('tr');
        master = parseInt(tr?.dataset.master, 10);
    }

    console.log('🔍 Master file ID found:', master);

    if (!master || isNaN(master)) {
        console.error('❌ Missing data-master attribute');
        alert('Error: Could not find master file ID. Please refresh the page.');
        return;
    }

    const year = parseInt(el.dataset.year, 10);
    const field = el.dataset.field; // Should be 'publication'
    const value = (el.value || '').trim();

    console.log('🔍 Publication data:', { master, year, field, value });

    if (!year || !field) {
        console.error('❌ Missing required data:', { year, field });
        alert('Error: Missing required data. Please refresh the page.');
        return;
    }

    // For publication field, we use a special category
    const payload = {
        master_file_id: master,
        year: year,
        month: 1, // Publication is not month-specific, but backend requires it
        category: 'PUBLICATION',
        field_type: 'text',
        value: value || null
    };

    console.log('🚀 Publication payload being sent:', payload);

    // Show loading
    el.classList.add('opacity-50');
    el.disabled = true;

    fetch(UPDATE_URL, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify(payload),
    })
    .then(response => {
        console.log('📡 Response status:', response.status);
        return response.text().then(text => {
            console.log('📡 Raw response:', text);
            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                console.error('❌ Failed to parse JSON:', e);
                throw new Error('Invalid JSON response: ' + text.slice(0, 200));
            }

            if (!response.ok) {
                throw new Error(data.message || `HTTP ${response.status}: ${text}`);
            }

            return data;
        });
    })
    .then(data => {
        console.log('✅ Publication saved successfully:', data);

        // Success feedback with green ring
        el.classList.add('ring-2', 'ring-green-400');
        setTimeout(() => el.classList.remove('ring-2', 'ring-green-400'), 1000);
    })
    .catch(error => {
        console.error('❌ Publication save error:', error);
        alert('Save failed: ' + error.message);

        // Error feedback
        el.classList.add('ring-2', 'ring-red-400');
        setTimeout(() => el.classList.remove('ring-2', 'ring-red-400'), 2000);
    })
    .finally(() => {
        // Remove loading state
        el.classList.remove('opacity-50');
        el.disabled = false;
    });
}

function normalizeDate(v, year) {
    const s = (v || '').trim();
    if (/^\d{4}-\d{2}-\d{2}$/.test(s)) return s;
    const m = s.match(/^(\d{1,2})\/(\d{1,2})(?:\/(\d{2,4}))?$/);
    if (!m) return null;
    const d = String(m[1]).padStart(2,'0');
    const mo= String(m[2]).padStart(2,'0');
    let yy  = m[3] ? String(m[3]) : String(year);
    if (yy.length===2) yy='20'+yy;
    return `${yy}-${mo}-${d}`;
}

function saveCell(el) {
    console.log('🔍 saveCell called for element:', el);

    // Special handling for publication field
    if (el.dataset.field === 'publication') {
        return savePublicationField(el);
    }

    // Get CSRF Token
    const csrfToken = getCSRFToken();
    if (!csrfToken) {
        console.error('❌ CSRF token not found');
        alert('CSRF token missing. Please refresh the page.');
        return;
    }

    // Get master file ID
    let master = parseInt(el.dataset.master, 10);
    if (!master || isNaN(master)) {
        const tr = el.closest('tr');
        master = parseInt(tr?.dataset.master, 10);
    }

    console.log('🔍 Master file ID found:', master);

    if (!master || isNaN(master)) {
        console.error('❌ Missing data-master attribute');
        console.log('Element:', el);
        console.log('Closest TR:', el.closest('tr'));
        alert('Error: Could not find master file ID. Please refresh the page.');
        return;
    }

    const year = parseInt(el.dataset.year, 10);
    const month = parseInt(el.dataset.month, 10);
    const category = (el.dataset.category || '').toUpperCase();
    const isDate = el.dataset.input === 'date';
    const raw = (el.value || '').trim();

    console.log('🔍 Form data collected:', { master, year, month, category, isDate, raw });

    if (!year || !month || !category) {
        console.error('❌ Missing required data:', { year, month, category });
        alert('Error: Missing required data. Please refresh the page.');
        return;
    }

    const payload = {
        master_file_id: master,
        year,
        month,
        category,
        field_type: isDate ? 'date' : 'text',
        value: raw || null
    };

    console.log('🚀 Payload being sent:', payload);

    // Show loading
    el.classList.add('opacity-50');
    el.disabled = true;

    fetch(UPDATE_URL, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify(payload),
    })
    .then(response => {
        console.log('📡 Response status:', response.status);
        console.log('📡 Response URL:', response.url);

        return response.text().then(text => {
            console.log('📡 Raw response:', text);

            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                console.error('❌ Failed to parse JSON:', e);
                throw new Error('Invalid JSON response: ' + text.slice(0, 200));
            }

            if (!response.ok) {
                throw new Error(data.message || `HTTP ${response.status}: ${text}`);
            }

            return data;
        });
    })
    .then(data => {
        console.log('✅ Success response:', data);

        // ✅ DATA PERSISTENCE - Keep values in both text and date fields
        console.log('✅ Data saved successfully - input value stays:', el.value);

        // Success feedback with green ring
        el.classList.add('ring-2', 'ring-green-400');
        setTimeout(() => el.classList.remove('ring-2', 'ring-green-400'), 1000);
    })
    .catch(error => {
        console.error('❌ Fetch error:', error);
        alert('Save failed: ' + error.message);

        // Error feedback
        el.classList.add('ring-2', 'ring-red-400');
        setTimeout(() => el.classList.remove('ring-2', 'ring-red-400'), 2000);
    })
    .finally(() => {
        // Remove loading state
        el.classList.remove('opacity-50');
        el.disabled = false;
    });
}


function setDropdownColor(selectEl) {
    const colors = {
        'Installation': 'red',
        'Dismentel': 'red',
        'Artwork': 'yellow',
        'Payment': 'red',
        'Ongoing': 'lightblue',
        'Renewal': 'red',
        'Completed': 'green',
        'Material': 'yellow',
        'Whatsapp': 'green',
        'Posted': 'green'
    };
    const selected = selectEl.value;
    selectEl.style.backgroundColor = colors[selected] || '';
}

// Initialize all dropdowns on page load
document.querySelectorAll('select[data-input="text"]').forEach(sel => {
    setDropdownColor(sel);
    sel.addEventListener('change', function() {
        setDropdownColor(this);
    });
});



// ✅ FIXED MONTH FILTERING FUNCTIONALITY

document.addEventListener('DOMContentLoaded', function () {
  // --- Get filter elements
  const monthFilter    = document.getElementById('filter-month');
  const yearFilter     = document.getElementById('filter-year');
  const clearFiltersBtn= document.getElementById('clear-filters');
  const filterCount    = document.getElementById('filter-count');
  const filterSummary  = document.getElementById('filter-summary');
  const activeFilters  = document.getElementById('active-filters');

  function updateFilterSummary() {
    if (!filterSummary || !activeFilters) return;
    const parts = [];

    if (monthFilter && monthFilter.value) {
      parts.push(`Month: ${monthFilter.value}`);
    }
    if (yearFilter && yearFilter.value) parts.push(`Year: ${yearFilter.value}`);

    if (parts.length > 0) {
      filterSummary.classList.remove('hidden');
      activeFilters.textContent = parts.join(', ');
    } else {
      filterSummary.classList.add('hidden');
      activeFilters.textContent = '';
    }
  }

  function filterTable() {
    const rows = document.querySelectorAll('tbody tr.table-row');
    const mVal = (monthFilter?.value || '').trim();   // Month name like "September"
    const yVal = (yearFilter?.value || '').trim();

    let visibleCount = 0;

    rows.forEach(row => {
      const rowYear    = (row.dataset.year || '').trim();
      const rowMonth   = (row.dataset.month || '').trim(); // This is the actual month from data

      const yearOK     = !yVal || rowYear === yVal;

      // ✅ FIXED: Direct comparison with the stored month name
      const monthOK    = !mVal || rowMonth === mVal;

      const show = yearOK && monthOK;
      row.style.display = show ? '' : 'none';
      if (show) visibleCount++;
    });

    // Update counter
    if (filterCount) {
      const total = rows.length;
      if (visibleCount === total) {
        filterCount.textContent = 'All records visible';
        filterCount.className = 'text-sm text-gray-500';
      } else {
        filterCount.textContent = `${visibleCount} of ${total} records visible`;
        filterCount.className = 'text-sm text-blue-600 font-medium';
      }
    }

    updateFilterSummary();
  }

  // Event listeners
  if (monthFilter)   monthFilter.addEventListener('change',   filterTable);
  if (yearFilter)    yearFilter.addEventListener('change',    filterTable);
  if (clearFiltersBtn) {
    clearFiltersBtn.addEventListener('click', () => {
      if (monthFilter)   monthFilter.value   = '';
      if (yearFilter)    yearFilter.value    = '';
      filterTable();
    });
  }

  // Initial filter run
  filterTable();
});
</script>

</x-app-layout>
