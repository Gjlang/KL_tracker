<x-app-layout>
  @push('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
  @endpush

  <!-- Page Container -->
  <div class="min-h-screen bg-[#F7F7F9]">

    <!-- Sticky Top Bar -->
    <div class="sticky top-0 z-40 bg-white border-b hairline shadow-sm">
      <div class="px-6 py-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
          <!-- Title Section -->
          <div>
            <h1 class="font-serif text-2xl ink font-semibold tracking-tight">
              MONTHLY Ongoing Job – KL The Guide
            </h1>
            <p class="text-sm text-neutral-600 mt-1">Inline updates enabled</p>
          </div>

          <!-- Action Button Group -->
          <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('kltg.exportMatrix', array_filter(request()->only([
                'year','filter_year','month','filter_month',
                'q','search','status',
                'start','end','date_from','date_to'
            ]))) }}"
               class="btn-primary">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4"/>
              </svg>
              Export Excel
            </a>

            <a href="{{ route('coordinator.kltg.index') }}" class="btn-secondary">
              Open KLTG Coordinator
            </a>

            <a href="{{ route('dashboard') }}" class="btn-ghost">
              Dashboard
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="px-6 py-6 space-y-6">

      <!-- Advanced Filters Card -->
      <div class="bg-white rounded-2xl border border-neutral-200/70 shadow-sm">
        <div class="p-6">
          <div class="flex items-center justify-between mb-6">
            <div>
              <h3 class="font-serif text-lg ink font-medium">Advanced Filters</h3>
              <p class="text-sm text-neutral-600 mt-1">Refine your view with precision</p>
            </div>
            <button id="clear-filters" class="btn-ghost text-sm">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
              Clear All
            </button>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <!-- Month Filter -->
            <div class="space-y-2">
              <label for="filter-month" class="header-label">Month</label>
              <select id="filter-month" class="form-input">
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
              <label for="filter-year" class="header-label">Filter Year</label>
              <select id="filter-year" class="form-input">
                <option value="">All Years</option>
                <option value="2024">2024</option>
                <option value="2025">2025</option>
                <option value="2026">2026</option>
              </select>
            </div>
          </div>

          <!-- Active Filter Chips -->
          <div id="filter-summary" class="mt-4 hidden">
            <div class="flex flex-wrap items-center gap-2">
              <span class="header-label">Active:</span>
              <div id="active-filters-chips" class="flex flex-wrap gap-2"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Data Table Card -->
<div class="bg-white rounded-2xl border border-neutral-200/70 shadow-sm overflow-hidden">
  <!-- Table Container -->
  <div class="overflow-x-auto" style="max-height: 75vh;">
    <table class="min-w-[5500px] w-full text-sm border-collapse">
      <!-- Sticky Header (atas saja) -->
      <thead class="sticky top-0 z-20 bg-white">
        <tr class="bg-neutral-50/80">
          <th class="hairline px-4 py-3 text-left header-label whitespace-nowrap">No</th>
          <th class="hairline px-4 py-3 text-left header-label whitespace-nowrap">Month</th>
          <th class="hairline px-4 py-3 text-left header-label whitespace-nowrap">Created At</th>
          <th class="hairline px-4 py-3 text-left header-label whitespace-nowrap">Company</th>
          <th class="hairline px-4 py-3 text-left header-label whitespace-nowrap">Product</th>
          <th class="hairline px-4 py-3 text-left header-label whitespace-nowrap">Publication</th>
          <th class="hairline px-4 py-3 text-left header-label whitespace-nowrap">Edition</th>
          <th class="hairline px-4 py-3 text-left header-label whitespace-nowrap">Status</th>
          <th class="hairline px-4 py-3 text-left header-label whitespace-nowrap">Start</th>
          <th class="hairline px-4 py-3 text-left header-label whitespace-nowrap">End</th>

          @for ($m=1; $m<=12; $m++)
            <th class="px-4 py-3 text-center hairline bg-neutral-50/60 header-label min-w-[900px]">
              {{ \Carbon\Carbon::create()->startOfYear()->month($m)->format('F') }}
            </th>
          @endfor
        </tr>
      </thead>

      <tbody>
        @if(isset($rows) && count($rows) > 0)
          @foreach ($rows as $i => $r)
            <tr class="table-row transition-all duration-150 hover:bg-neutral-50 hover:shadow-[inset_0_0_0_1px_rgba(0,0,0,0.03)]"
                data-master="{{ $r['id'] ?? '' }}"
                data-status="{{ strtolower($r['status'] ?? '') }}"
                data-company="{{ strtolower($r['company'] ?? '') }}"
                data-product="{{ strtolower($r['product'] ?? '') }}"
                data-year="{{ $year ?? date('Y') }}"
                data-month="{{ $r['month_name'] ?? '' }}"
                data-created-date="{{ $r['created_at'] ?? '' }}">

              <!-- Kolom awal (tidak sticky) -->
              <td class="hairline px-4 py-3 align-top ink tabular-nums">{{ $i+1 }}</td>
              <td class="hairline px-4 py-3 align-top ink">{{ $r['month_name'] ?? '' }}</td>
              <td class="hairline px-4 py-3 align-top ink tabular-nums">{{ $r['created_at'] ?? '' }}</td>

              <td class="hairline px-4 py-3 align-top ink" style="max-width:150px;">
                <div class="truncate pr-1" title="{{ $r['company'] ?? '' }}">{{ $r['company'] ?? 'N/A' }}</div>
              </td>

              <td class="hairline px-4 py-3 align-top">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-neutral-100 text-neutral-800">
                  {{ $r['product'] ?? 'N/A' }}
                </span>
              </td>

              <!-- Publication Input -->
              <td class="hairline px-4 py-3 align-top">
                <input
                  class="form-input auto-save-input w-32"
                  value="{{ $r['publication'] ?? '' }}"
                  data-master="{{ $r['id'] ?? '' }}"
                  data-year="{{ $year ?? date('Y') }}"
                  data-category="KLTG"
                  data-type="PUBLICATION"
                  data-field="publication"
                  oninput="debouncedSave(this)"
                  placeholder="Type name…">
              </td>

              <!-- Edition Input -->
              <td class="hairline px-4 py-3 align-top">
                <input
                  class="form-input auto-save-input w-32"
                  value="{{ $r['edition'] ?? '' }}"
                  data-master="{{ $r['id'] ?? '' }}"
                  data-year="{{ $year ?? date('Y') }}"
                  data-category="KLTG"
                  data-type="EDITION"
                  data-field="edition"
                  oninput="debouncedSave(this)"
                  placeholder="Type name…">
              </td>

              <!-- Status Badge -->
              <td class="hairline px-4 py-3 align-top">
                <span class="badge-{{ strtolower($r['status'] ?? 'pending') }}">
                  {{ $r['status'] ?? 'Pending' }}
                </span>
              </td>

              <td class="hairline px-4 py-3 align-top ink tabular-nums">{{ $r['start'] ?? '' }}</td>
              <td class="hairline px-4 py-3 align-top ink tabular-nums">{{ $r['end'] ?? '' }}</td>

              <!-- Monthly Category Input Cells -->
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

                <td class="px-2 py-2 align-top hairline month-cell" data-month="{{ $m }}">
                  <div class="min-w-[900px] border border-neutral-200 rounded-xl bg-white shadow-sm">
                    <!-- Month Header -->
                    <div class="text-center py-2 border-b border-neutral-200 bg-neutral-50/50">
                      <h4 class="font-serif font-medium text-sm ink">
                        {{ \Carbon\Carbon::create()->startOfYear()->month($m)->format('F') }}
                      </h4>
                    </div>

                    <div class="flex h-full">
                      @foreach($cats as $index => $c)
                        <div class="flex-1 flex flex-col {{ $index < count($cats) - 1 ? 'border-r border-neutral-200' : '' }}">
                          <!-- Category Header -->
                          <div class="text-center py-2 bg-neutral-50/30 border-b border-neutral-200 flex-shrink-0">
                            <div class="header-label text-neutral-700">{{ $c['label'] }}</div>
                          </div>

                          <!-- Input Container -->
                          <div class="flex flex-col flex-1 p-3 space-y-2">
                            @php
                              $gridKey = sprintf('%02d_%s', $m, $c['code']);
                            @endphp

                            <!-- Status Select -->
                            <select
                              class="form-input text-xs status-select"
                              data-input="text"
                              data-master="{{ $r['id'] ?? '' }}"
                              data-year="{{ $year ?? date('Y') }}"
                              data-month="{{ $m }}"
                              data-category="{{ $c['code'] }}"
                              data-type="STATUS"
                              onchange="saveCell(this); setDropdownColor(this);">
                                <option value=""></option>
                                <option value="Installation" {{ ($r['grid'][$gridKey]['status'] ?? '') == 'Installation' ? 'selected' : '' }}>Installation</option>
                                <option value="Dismantle" {{ ($r['grid'][$gridKey]['status'] ?? '') == 'Dismantle' ? 'selected' : '' }}>Dismantle</option>
                                <option value="Artwork" {{ ($r['grid'][$gridKey]['status'] ?? '') == 'Artwork' ? 'selected' : '' }}>Artwork</option>
                                <option value="Payment" {{ ($r['grid'][$gridKey]['status'] ?? '') == 'Payment' ? 'selected' : '' }}>Payment</option>
                                <option value="Ongoing" {{ ($r['grid'][$gridKey]['status'] ?? '') == 'Ongoing' ? 'selected' : '' }}>Ongoing</option>
                                <option value="Renewal" {{ ($r['grid'][$gridKey]['status'] ?? '') == 'Renewal' ? 'selected' : '' }}>Renewal</option>
                                <option value="Completed" {{ ($r['grid'][$gridKey]['status'] ?? '') == 'Completed' ? 'selected' : '' }}>Completed</option>
                                <option value="Material" {{ ($r['grid'][$gridKey]['status'] ?? '') == 'Material' ? 'selected' : '' }}>Material</option>
                                <option value="Whatsapp" {{ ($r['grid'][$gridKey]['status'] ?? '') == 'Whatsapp' ? 'selected' : '' }}>Whatsapp</option>
                                <option value="Posted" {{ ($r['grid'][$gridKey]['status'] ?? '') == 'Posted' ? 'selected' : '' }}>Posted</option>
                            </select>

                            @php
                              $inputIdStart = "date-start-y{$year}-m{$m}-{$c['code']}-{$r['id']}-" . uniqid();
                            @endphp

                            <div class="flex items-center gap-2">
                              <input
                                id="{{ $inputIdStart }}"
                                type="date"
                                class="form-input text-xs flex-1"
                                value="{{ $r['grid'][$gridKey]['start'] ?? '' }}"
                                data-input="date"
                                data-master="{{ $r['id'] ?? '' }}"
                                data-year="{{ $year }}"
                                data-month="{{ $m }}"
                                data-category="{{ $c['code'] }}"
                                data-type="START"
                                onchange="saveCell(this)">
                              <button type="button"
                                class="p-2 text-neutral-500 hover:text-neutral-700 transition-colors"
                                onclick="document.getElementById('{{ $inputIdStart }}').showPicker()"
                                title="Start date">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                              </button>
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
          <!-- Empty State -->
          <tr>
            <td colspan="22" class="hairline px-6 py-16 text-center">
              <div class="flex flex-col items-center">
                <svg class="w-12 h-12 text-neutral-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="font-serif text-lg ink mb-2">No ongoing jobs found</h3>
                <p class="text-neutral-600 mb-4">Try adjusting your filters or add new entries.</p>
                <a href="{{ route('coordinator.kltg.index') }}" class="btn-secondary">
                  Open KLTG Coordinator
                </a>
              </div>
            </td>
          </tr>
        @endif
      </tbody>
    </table>
  </div>
</div>

      </div>
    </div>
  </div>

  <!-- JavaScript (preserve all existing logic) -->
  <script>
    const UPDATE_URL = "{{ route('kltg.details.upsert') }}";

    // CSRF Token handling
    function getCSRFToken() {
      let token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      if (!token) {
        token = window.Laravel?.csrfToken || "{{ csrf_token() }}";
      }
      return token;
    }

    // Debounced save for all fields
    let saveTimeout;
    function debouncedSave(el) {
      const f = (el.dataset.field || '').toLowerCase();
      const t = (el.dataset.type || '').toUpperCase();

      if (f === 'publication' || t === 'PUBLICATION' || f === 'edition' || t === 'EDITION') {
        return savePublicationField(el);
      }
      return saveCell(el);
    }

    // Publication field save
    function savePublicationField(el) {
      const csrfToken = getCSRFToken();
      const master = parseInt(el.dataset.master, 10);
      const year   = parseInt(el.dataset.year, 10);
      const value  = (el.value || '').trim();
      const category = (el.dataset.category || 'KLTG').toUpperCase();
      const type     = (el.dataset.type || 'PUBLICATION').toUpperCase();

      let sentinelMonth;
      if (type === 'PUBLICATION') {
        sentinelMonth = 0;
      } else if (type === 'EDITION') {
        sentinelMonth = 0;
      } else {
        sentinelMonth = 1;
      }

      const payload = {
        master_file_id: master,
        year: year,
        month: sentinelMonth,
        category: category,
        type: type,
        field_type: 'text',
        value: value || null
      };

      // Add saving visual feedback
      el.classList.add('opacity-50');

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
        el.classList.remove('opacity-50');
        el.classList.add('ring-2','ring-[#4bbbed]');
        setTimeout(() => el.classList.remove('ring-2','ring-[#4bbbed]'), 800);
      })
      .catch(err => {
        console.error(err);
        el.classList.remove('opacity-50');
        el.classList.add('ring-2','ring-[#d33831]');
        setTimeout(() => el.classList.remove('ring-2','ring-[#d33831]'), 1200);
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
      const field  = (el.dataset.field || '').toLowerCase();
      const tHint  = (el.dataset.type  || '').toUpperCase();

      if (field === 'publication' || tHint === 'PUBLICATION' || field === 'edition' || tHint === 'EDITION') {
        return savePublicationField(el);
      }

      const csrfToken = getCSRFToken();
      if (!csrfToken) {
        alert('CSRF token missing. Please refresh the page.');
        return;
      }

      let master = parseInt(el.dataset.master || (el.closest('tr')?.dataset.master ?? ''), 10);
      if (!Number.isInteger(master)) {
        alert('Error: Could not find master file ID. Please refresh the page.');
        return;
      }

      let year = parseInt(
        el.dataset.year ||
        document.querySelector('[data-active-year]')?.dataset.activeYear ||
        new Date().getFullYear(),
        10
      );

      const monthRaw = el.dataset.month || el.closest('[data-month]')?.dataset.month || el.closest('td')?.dataset.month || el.closest('div[data-month]')?.dataset.month;
      let month = parseInt(monthRaw, 10);

      let category = (el.dataset.category || el.closest('[data-category]')?.dataset.category || '').toUpperCase();

      const isDate = (el.dataset.input === 'date') || (el.type === 'date');
      let value    = (el.value ?? '').trim();
      if (value === '') value = null;

      let type = (tHint || (isDate ? 'START' : 'STATUS')).toUpperCase();

      if (isDate && value) {
        if (!/^\d{4}-\d{2}-\d{2}$/.test(value)) {
          const d = new Date(value);
          if (!isNaN(d.getTime())) value = d.toISOString().slice(0, 10);
        }
      }

      if (!Number.isInteger(year) || !Number.isInteger(month) || month < 1 || month > 12 || !category) {
        alert('Error: Missing required data (year/month/category). Please refresh the page.');
        return;
      }

      const payload = {
        master_file_id: master,
        year,
        month,
        category,
        type,
        field_type: isDate ? 'date' : 'text',
        value
      };

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
          throw new Error('Invalid JSON response');
        }
        if (!response.ok) throw new Error(data.message || `HTTP ${response.status}`);
        return data;
      }))
      .then(data => {
        el.classList.add('ring-2', 'ring-[#4bbbed]');
        setTimeout(() => el.classList.remove('ring-2', 'ring-[#4bbbed]'), 1000);
      })
      .catch(error => {
        alert('Save failed: ' + error.message);
        el.classList.add('ring-2', 'ring-[#d33831]');
        setTimeout(() => el.classList.remove('ring-2', 'ring-[#d33831]'), 2000);
      })
      .finally(() => {
        el.classList.remove('opacity-50');
        el.disabled = false;
      });
    }

    function setDropdownColor(selectEl) {
      const colors = {
        'Installation': '#fecaca',
        'Dismantle': '#fecaca',
        'Artwork': '#fef3c7',
        'Payment': '#fecaca',
        'Ongoing': '#bfdbfe',
        'Renewal': '#fecaca',
        'Completed': '#bbf7d0',
        'Material': '#fef3c7',
        'Whatsapp': '#bbf7d0',
        'Posted': '#bbf7d0'
      };
      const selected = selectEl.value;
      selectEl.style.backgroundColor = colors[selected] || '';
    }

    // Initialize dropdowns
    document.querySelectorAll('select[data-input="text"]').forEach(sel => {
      setDropdownColor(sel);
    });

    // Filter functionality
    document.addEventListener('DOMContentLoaded', function () {
      const monthFilter = document.getElementById('filter-month');
      const yearFilter = document.getElementById('filter-year');
      const clearFiltersBtn = document.getElementById('clear-filters');
      const filterSummary = document.getElementById('filter-summary');
      const activeFiltersChips = document.getElementById('active-filters-chips');

      function createChip(label, onRemove) {
        const chip = document.createElement('div');
        chip.className = 'chip';
        chip.innerHTML = `
          <span>${label}</span>
          <button class="ml-2 hover:text-[#d33831] transition-colors" onclick="${onRemove}">×</button>
        `;
        return chip;
      }

      function updateFilterSummary() {
        if (!filterSummary || !activeFiltersChips) return;

        activeFiltersChips.innerHTML = '';
        const hasFilters = (monthFilter?.value || yearFilter?.value);

        if (hasFilters) {
          filterSummary.classList.remove('hidden');

          if (monthFilter?.value) {
            const chip = createChip(`MONTH: ${monthFilter.value.toUpperCase()}`, `document.getElementById('filter-month').value = ''; filterTable();`);
            activeFiltersChips.appendChild(chip);
          }

          if (yearFilter?.value) {
            const chip = createChip(`YEAR: ${yearFilter.value}`, `document.getElementById('filter-year').value = ''; filterTable();`);
            activeFiltersChips.appendChild(chip);
          }
        } else {
          filterSummary.classList.add('hidden');
        }
      }

      function filterTable() {
        const rows = document.querySelectorAll('tbody tr.table-row');
        const mVal = (monthFilter?.value || '').trim();
        const yVal = (yearFilter?.value || '').trim();

        let visibleCount = 0;

        rows.forEach(row => {
          const rowYear = (row.dataset.year || '').trim();
          const rowMonth = (row.dataset.month || '').trim();

          const yearOK = !yVal || rowYear === yVal;
          const monthOK = !mVal || rowMonth === mVal;

          const show = yearOK && monthOK;
          row.style.display = show ? '' : 'none';
          if (show) visibleCount++;
        });

        updateFilterSummary();
      }

      // Event listeners
      if (monthFilter) monthFilter.addEventListener('change', filterTable);
      if (yearFilter) yearFilter.addEventListener('change', filterTable);
      if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', () => {
          if (monthFilter) monthFilter.value = '';
          if (yearFilter) yearFilter.value = '';
          filterTable();
        });
      }

      // Initial filter run
      filterTable();
    });
  </script>

</x-app-layout>
