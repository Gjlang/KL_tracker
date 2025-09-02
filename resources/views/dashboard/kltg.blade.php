<x-app-layout>
  @push('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
  @endpush

<!-- Header & Actions -->
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
  <!-- Title -->
  <h2 class="text-2xl font-bold flex items-center gap-2">
    📊 <span>MONTHLY Ongoing Job – KL The Guide</span>
  </h2>

  <!-- Action Buttons -->
  <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
    <!-- Back to Dashboard -->
    <a href="{{ route('dashboard') }}"
       class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition-colors shadow-sm">
      <span class="ml-2">Dashboard</span>
    </a>

    <!-- Open KLTG Coordinator -->
    <a href="{{ route('coordinator.kltg.index') }}"
       class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition-colors shadow-sm">
      🗂️ <span class="ml-2">Open KLTG Coordinator</span>
    </a>
  </div>
</div>
    {{-- Enhanced Filter Section with Month/Year --}}
<div class="mb-6 p-6 bg-gradient-to-r from-white to-gray-50 rounded-xl shadow-lg border border-gray-200">
  <div class="flex items-center justify-between mb-5">
    <h4 class="text-xl font-bold text-gray-800 flex items-center gap-2">
      <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
      </svg>
      Advanced Filters
    </h4>

    <div class="flex items-center gap-4">

      {{-- Export XLSX (keeps active filters) --}}
      <a
        href="{{ route('kltg.exportMatrix', array_filter(request()->only([
            // keep whatever your page uses
            'year','filter_year','month','filter_month',
            'q','search','status',
            'start','end','date_from','date_to'
        ]))) }}"
        class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-white shadow hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
      >
        {{-- download icon --}}
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4"/>
        </svg>
        <span>Export Excel</span>
      </a>
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
        <th class="sticky left-[680px] z-30 bg-yellow-100 border border-gray-400 px-3 py-2 text-left font-bold text-gray-700 whitespace-nowrap">Edition</th>
        <th class="sticky left-[830px] z-30 bg-yellow-100 border border-gray-400 px-3 py-2 text-left font-bold text-gray-700 whitespace-nowrap">Status</th>
        <th class="border border-gray-400 px-3 py-2 text-left font-bold text-gray-700 whitespace-nowrap">Start</th>
        <th class="border border-gray-400 px-3 py-2 text-left font-bold text-gray-700 whitespace-nowrap">End</th>

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

            {{-- 3a) Publication input (left sticky column) - FIXED with correct data attributes --}}
            <td class="sticky left-[530px] z-10 bg-inherit border border-gray-300 px-3 py-2 align-top">
              <input
                class="w-32 border rounded px-2 py-1 text-sm auto-save-input"
                value="{{ $r['publication'] ?? '' }}"
                data-master="{{ $r['id'] ?? '' }}"
                data-year="{{ $year ?? date('Y') }}"
                data-category="KLTG"
                data-type="PUBLICATION"
                data-field="publication"
                oninput="debouncedSave(this)"
                placeholder="Type name…">
            </td>

            {{-- 3b) Add the Edition input (it's missing) --}}
            <td class="sticky left-[680px] z-10 bg-inherit border border-gray-300 px-3 py-2 align-top">
              <input
                class="w-32 border rounded px-2 py-1 text-sm auto-save-input"
                value="{{ $r['edition'] ?? '' }}"
                data-master="{{ $r['id'] ?? '' }}"
                data-year="{{ $year ?? date('Y') }}"
                data-category="KLTG"
                data-type="EDITION"
                data-field="edition"
                oninput="debouncedSave(this)"
                placeholder="Type name…">
            </td>

            {{-- Status with enhanced styling --}}
            <td class="sticky left-[830px] z-10 bg-inherit border border-gray-300 px-3 py-2 align-top">
              <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                  {{ strtolower($r['status'] ?? '') === 'completed' ? 'bg-green-100 text-green-800 border border-green-200' :
                     (strtolower($r['status'] ?? '') === 'ongoing' ? 'bg-yellow-100 text-yellow-800 border border-yellow-200' :
                      'bg-white-100 text-rose-800 border border-rose-200') }}">
                {{ $r['status'] ?? 'Pending' }}
              </span>
            </td>

            <td class="border border-gray-300 px-3 py-2 align-top text-gray-900">{{ $r['start'] ?? '' }}</td>
            <td class="border border-gray-300 px-3 py-2 align-top text-gray-900">{{ $r['end'] ?? '' }}</td>

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
                          <!-- Status select with data-type="STATUS" -->
                          @php
                            $gridKey = sprintf('%02d_%s', $m, $c['code']);
                          @endphp

                          <select
                            class="border border-gray-300 rounded px-2 py-2 text-xs w-full bg-white focus:ring-1 focus:ring-blue-400 focus:border-blue-400"
                            data-input="text"
                            data-master="{{ $r['id'] ?? '' }}"
                            data-year="{{ $year ?? date('Y') }}"
                            data-month="{{ $m }}"
                            data-category="{{ $c['code'] }}"
                            data-type="STATUS"
                            onchange="saveCell(this); setDropdownColor(this);">
                                <option value=""></option>
                                <option value="Installation" style="color:red;" {{ ($r['grid'][$gridKey]['status'] ?? '') == 'Installation' ? 'selected' : '' }}>Installation</option>
                                <option value="Dismentel" style="color:red;" {{ ($r['grid'][$gridKey]['status'] ?? '') == 'Dismentel' ? 'selected' : '' }}>Dismentel</option>
                                <option value="Artwork" style="color:orange;" {{ ($r['grid'][$gridKey]['status'] ?? '') == 'Artwork' ? 'selected' : '' }}>Artwork</option>
                                <option value="Payment" style="color:red;" {{ ($r['grid'][$gridKey]['status'] ?? '') == 'Payment' ? 'selected' : '' }}>Payment</option>
                                <option value="Ongoing" style="color:lightblue;" {{ ($r['grid'][$gridKey]['status'] ?? '') == 'Ongoing' ? 'selected' : '' }}>Ongoing</option>
                                <option value="Renewal" style="color:red;" {{ ($r['grid'][$gridKey]['status'] ?? '') == 'Renewal' ? 'selected' : '' }}>Renewal</option>
                                <option value="Completed" style="color:green;" {{ ($r['grid'][$gridKey]['status'] ?? '') == 'Completed' ? 'selected' : '' }}>Completed</option>
                                <option value="Material" style="color:orange;" {{ ($r['grid'][$gridKey]['status'] ?? '') == 'Material' ? 'selected' : '' }}>Material</option>
                                <option value="Whatsapp" style="color:green;" {{ ($r['grid'][$gridKey]['status'] ?? '') == 'Whatsapp' ? 'selected' : '' }}>Whatsapp</option>
                                <option value="Posted" style="color:green;" {{ ($r['grid'][$gridKey]['status'] ?? '') == 'Posted' ? 'selected' : '' }}>Posted</option>
                          </select>

                          <!-- 3c) Split date inputs into START and END -->
                          @php
                            $inputIdStart = "date-start-y{$year}-m{$m}-{$c['code']}-{$r['id']}-" . uniqid();
                            $inputIdEnd = "date-end-y{$year}-m{$m}-{$c['code']}-{$r['id']}-" . uniqid();
                          @endphp

                          <!-- START date input -->
                          <div class="flex items-center gap-1">
                            <input
                              id="{{ $inputIdStart }}"
                              type="date"
                              class="border rounded px-2 py-1 w-full text-xs"
                              value="{{ $r['grid'][$gridKey]['start'] ?? '' }}"
                              data-input="date"
                              data-master="{{ $r['id'] ?? '' }}"
                              data-year="{{ $year }}"
                              data-month="{{ $m }}"
                              data-category="{{ $c['code'] }}"
                              data-type="START"
                              onchange="saveCell(this)">
                            <button type="button"
                              class="p-1 border rounded text-xs hover:bg-gray-100 flex-shrink-0"
                              onclick="document.getElementById('{{ $inputIdStart }}').showPicker()"
                              title="Start date">📅</button>
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
          <td colspan="22" class="border border-gray-300 px-6 py-12 text-center text-gray-500">
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
  // Publication & Edition should NOT go to saveCell()
  const f = (el.dataset.field || '').toLowerCase();
  const t = (el.dataset.type || '').toUpperCase();

  if (f === 'publication' || t === 'PUBLICATION' || f === 'edition' || t === 'EDITION') {
    return savePublicationField(el); // this sets a fixed month internally
  }
  return saveCell(el);
}

// ✅ SPECIALIZED FUNCTION FOR PUBLICATION FIELD - FIXED
function savePublicationField(el) {
  const csrfToken = getCSRFToken();
  const master = parseInt(el.dataset.master, 10);
  const year   = parseInt(el.dataset.year, 10);
  const value  = (el.value || '').trim();

  // Use provided category (KLTG) and mark the field type explicitly
  const category = (el.dataset.category || 'KLTG').toUpperCase();
  const type     = (el.dataset.type || 'PUBLICATION').toUpperCase();

  // ✅ FIX: Use different sentinel months to avoid unique constraint violation
  let sentinelMonth;
  if (type === 'PUBLICATION') {
    sentinelMonth = 0;  // Publication uses month 0
  } else if (type === 'EDITION') {
    sentinelMonth = 0;  // Edition uses month 0
  } else {
    sentinelMonth = 1;  // Default fallback
  }

  const payload = {
    master_file_id: master,
    year: year,
    month: sentinelMonth,      // ✅ Different months prevent constraint violation
    category: category,
    type: type,
    field_type: 'text',
    value: value || null
  };

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
  .then(r => r.json().then(j => (r.ok ? j : Promise.reject(j))))
  .then(() => {
    el.classList.add('ring-2','ring-green-400');
    setTimeout(() => el.classList.remove('ring-2','ring-green-400'), 800);
  })
  .catch(err => {
    console.error(err);
    el.classList.add('ring-2','ring-red-400');
    setTimeout(() => el.classList.remove('ring-2','ring-red-400'), 1200);
    alert(err?.message || 'Save failed');
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

  // 0) Route Publication & Edition to their specialized saver
  const field  = (el.dataset.field || '').toLowerCase();
  const tHint  = (el.dataset.type  || '').toUpperCase();
  if (
    field === 'publication' || tHint === 'PUBLICATION' ||
    field === 'edition'     || tHint === 'EDITION'
  ) {
    return savePublicationField(el); // uses month=1 sentinel
  }

  // 1) CSRF
  const csrfToken = getCSRFToken();
  if (!csrfToken) {
    console.error('❌ CSRF token not found');
    alert('CSRF token missing. Please refresh the page.');
    return;
  }

  // 2) Resolve master id
  let master = parseInt(el.dataset.master || (el.closest('tr')?.dataset.master ?? ''), 10);
  if (!Number.isInteger(master)) {
    console.error('❌ Missing data-master attribute');
    alert('Error: Could not find master file ID. Please refresh the page.');
    return;
  }

  // 3) Resolve year (fallbacks to a global holder or current year)
  let year = parseInt(
    el.dataset.year ||
    document.querySelector('[data-active-year]')?.dataset.activeYear ||
    new Date().getFullYear(),
    10
  );

  // 4) Resolve month (look up the tree if not on the element)
  const monthRaw =
    el.dataset.month ||
    el.closest('[data-month]')?.dataset.month ||
    el.closest('td')?.dataset.month ||
    el.closest('div[data-month]')?.dataset.month;

  let month = parseInt(monthRaw, 10);

  // 5) Resolve category (fallback from closest container if needed)
  let category = (el.dataset.category ||
                  el.closest('[data-category]')?.dataset.category ||
                  '').toUpperCase();

  // 6) Value + type
  const isDate = (el.dataset.input === 'date') || (el.type === 'date');
  let value    = (el.value ?? '').trim();
  if (value === '') value = null;

  // DEFAULT type if not explicitly provided on the element
  let type = (tHint || (isDate ? 'START' : 'STATUS')).toUpperCase();

  // Normalize date to YYYY-MM-DD (in case browser/localization returns something else)
  if (isDate && value) {
    if (!/^\d{4}-\d{2}-\d{2}$/.test(value)) {
      const d = new Date(value);
      if (!isNaN(d.getTime())) value = d.toISOString().slice(0, 10);
    }
  }

  console.log('🔍 Form data collected:', { master, year, month, category, isDate, type, value });

  // 7) Validate requireds for month-based cells
  if (!Number.isInteger(year) || !Number.isInteger(month) || month < 1 || month > 12 || !category) {
    console.error('❌ Missing required data:', { year, month, category });
    alert('Error: Missing required data (year/month/category). Please refresh the page.');
    return;
  }

  const payload = {
    master_file_id: master,
    year,
    month,
    category,                        // KLTG/VIDEO/ARTICLE/LB/EM
    type,                            // STATUS | START | END (or explicit via data-type)
    field_type: isDate ? 'date' : 'text',
    value
  };

  console.log('🚀 Payload being sent:', payload);

  // UI: loading state
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
  .then(response => response.text().then(text => {
    let data;
    try { data = JSON.parse(text); } catch (e) {
      console.error('❌ Failed to parse JSON:', e, text);
      throw new Error('Invalid JSON response');
    }
    if (!response.ok) throw new Error(data.message || `HTTP ${response.status}`);
    return data;
  }))
  .then(data => {
    console.log('✅ Success response:', data);
    el.classList.add('ring-2', 'ring-green-400');
    setTimeout(() => el.classList.remove('ring-2', 'ring-green-400'), 1000);
  })
  .catch(error => {
    console.error('❌ Fetch error:', error);
    alert('Save failed: ' + error.message);
    el.classList.add('ring-2', 'ring-red-400');
    setTimeout(() => el.classList.remove('ring-2', 'ring-red-400'), 2000);
  })
  .finally(() => {
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
