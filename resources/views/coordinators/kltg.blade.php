@push('head')
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;500;600&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

  <style>
    /* Typography & Base Styles */
    .font-serif { font-family: 'EB Garamond', Georgia, serif; }
    .font-sans { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; }
    .ink { color: #1C1E26; }
    .muted { color: #6B7280; }
    .tabular-nums { font-variant-numeric: tabular-nums; }
    .small-caps { font-variant: small-caps; letter-spacing: 0.06em; }

    /* Layout Components */
    .page-bg { background-color: #F7F7F9; min-height: 100vh; }
    .card {
      @apply bg-white rounded-2xl border border-neutral-200/70 shadow-sm;
    }
    .hairline { border-color: #EAEAEA; }

    /* Button System */
    .btn-primary {
      @apply bg-[#22255b] text-white hover:opacity-90 focus:ring-2 focus:ring-[#4bbbed] focus:border-transparent rounded-full px-6 py-2.5 font-medium transition-all duration-150 inline-flex items-center justify-center;
    }
    .btn-secondary {
      @apply border border-neutral-300 text-neutral-800 hover:bg-neutral-50 hover:border-neutral-400 focus:ring-2 focus:ring-[#4bbbed] focus:border-transparent rounded-full px-5 py-2 font-medium transition-all duration-150 inline-flex items-center justify-center;
    }
    .btn-ghost {
      @apply text-neutral-700 hover:bg-neutral-50 focus:ring-2 focus:ring-[#4bbbed] rounded-full px-4 py-2 font-medium transition-all duration-150 inline-flex items-center justify-center;
    }
    .btn-export {
      @apply bg-emerald-700 text-white hover:bg-emerald-800 focus:ring-2 focus:ring-emerald-300 focus:border-transparent rounded-full px-5 py-2 font-medium transition-all duration-150 inline-flex items-center justify-center;
    }

    /* Tab System */
    .tab-strip {
      @apply border-b hairline overflow-x-auto;
      scrollbar-width: none;
      -ms-overflow-style: none;
    }
    .tab-strip::-webkit-scrollbar { display: none; }
    .tab {
      @apply px-6 py-4 text-sm font-medium transition-all duration-150 border-b-2 border-transparent whitespace-nowrap;
    }
    .tab.active {
      @apply ink border-[#22255b];
    }
    .tab:not(.active) {
      @apply text-neutral-600 hover:text-neutral-800 hover:bg-[#4bbbed]/5;
    }

    /* Form Controls */
    .form-control {
      @apply h-11 w-full border border-neutral-300 rounded-xl px-4 text-sm font-sans focus:ring-2 focus:ring-[#4bbbed] focus:border-transparent transition-all duration-150;
    }
    .form-label {
      @apply block text-xs font-semibold text-neutral-600 mb-2 small-caps;
    }

    /* Data Table */
    .data-table {
      @apply w-full text-sm;
    }
    .data-table thead th {
      @apply px-6 py-4 text-left font-semibold text-neutral-600 bg-neutral-50 border-b hairline small-caps;
      position: sticky;
      top: 0;
      z-index: 10;
    }
    .data-table tbody td {
      @apply px-6 py-4 border-b hairline;
    }
    .data-table tbody tr {
      @apply transition-all duration-150;
    }
    .data-table tbody tr:hover {
      @apply bg-neutral-50 shadow-sm;
    }
    .data-table tbody tr:last-child td {
      @apply border-b-0;
    }

    /* Input Styles for Table */
    .table-input {
      @apply w-full px-3 py-2 border border-neutral-200 rounded-lg text-sm transition-all duration-150 tabular-nums;
      min-width: 120px;
    }
    .table-input:focus {
      @apply border-[#4bbbed] ring-2 ring-[#4bbbed]/20 outline-none;
    }

    /* Status Indicators */
    .bg-yellow-50 { background-color: #FFFBEB !important; }
    .border-yellow-300 { border-color: #FCD34D !important; }
    .bg-green-50 { background-color: #F0FDF4 !important; }
    .border-green-300 { border-color: #86EFAC !important; }
    .bg-red-50 { background-color: #FEF2F2 !important; }
    .border-red-300 { border-color: #FCA5A5 !important; }

    /* Badge Styles */
    .badge {
      @apply inline-flex items-center justify-center px-3 py-1.5 rounded-full text-xs font-medium bg-neutral-100 text-neutral-700 border border-neutral-200;
      min-width: 80px;
    }

    /* Empty State */
    .empty-state {
      @apply text-center py-16 px-6;
    }

    /* Responsive adjustments */
    @media (max-width: 1024px) {
      .table-input { min-width: 100px; }
      .data-table thead th,
      .data-table tbody td { @apply px-4; }
    }
    @media (max-width: 768px) {
      .table-input { min-width: 80px; }
      .data-table thead th,
      .data-table tbody td { @apply px-3 py-3; }
    }
  </style>
@endpush

@php
  /** @var \Illuminate\Support\Collection $rows */
  /** @var \Illuminate\Support\Collection $existing */

  function _dbcol($k){
    static $map = [
      // umum
      'title'                   => 'title_snapshot',
      'company'                 => 'company_snapshot',
      'client_bp'               => 'client_bp',
      'x'                       => 'x',
      'edition'                 => 'edition',
      'publication'             => 'publication',
      'remarks'                 => 'remarks',
      'artwork_party'           => 'artwork_bp_client',

      // KLTG/Print dates (di DB disimpan tanpa _date)
      'artwork_reminder_date'   => 'artwork_reminder',
      'material_received_date'  => 'material_record',
      'artwork_done_date'       => 'artwork_done',
      'send_chop_sign_date'     => 'send_chop_sign',
      'chop_sign_approval_date' => 'chop_sign_approval',
      'park_in_server_date'     => 'park_in_file_server',

      // Video/LB/Article
      'material_reminder_text'  => 'material_reminder_text',
      'video_done_date'         => 'video_done',
      'pending_approval_date'   => 'pending_approval',
      'video_approved_date'     => 'video_approved',
      'video_scheduled_date'    => 'video_scheduled',
      'video_posted_date'       => 'video_posted',
      'article_done_date'       => 'article_done',
      'article_approved_date'   => 'article_approved',
      'article_scheduled_date'  => 'article_scheduled',
      'article_posted_date'     => 'article_posted',
      'post_link'               => 'post_link',

      // EM
      'em_date_write'           => 'em_date_write',
      'em_date_to_post'         => 'em_date_to_post',
      'em_post_date'            => 'em_post_date',
      'em_qty'                  => 'em_qty',
      'blog_link'               => 'blog_link',
    ];
    return $map[$k] ?? $k;
  }

  function cellVal($existing, $row, $key, $type, $activeTab){
    // Build composite key: master_file_id_subcategory_year_month
    $masterId = $row->master_file_id ?? $row->id;
    $year = $row->activity_year;
    $month = $row->activity_month;

    // Map activeTab to stored subcategory (same as your tabToStored logic)
    $subcategoryMap = [
        'print' => 'KLTG',
        'video' => 'Video',
        'article' => 'Article',
        'lb' => 'LB',
        'em' => 'EM'
    ];
    $subcategory = $subcategoryMap[$activeTab] ?? strtoupper($activeTab);

    $compositeKey = $masterId . '_' . $subcategory . '_' . $year . '_' . $month;
    $record = $existing->get($compositeKey);

    if (!$record) return '';

    $col = _dbcol($key);
    $v = $record->{$col} ?? '';

    // Normalize date values for HTML <input type="date">
    if ($type === 'date') {
        // If it's a Carbon/DateTime object, format it
        if (is_object($v) && method_exists($v, 'format')) {
            return $v->format('Y-m-d');
        }
        // If it's a string, trim time or parse
        if (is_string($v) && $v !== '') {
            // Common case: "YYYY-MM-DD HH:MM:SS" -> "YYYY-MM-DD"
            if (preg_match('/^\d{4}-\d{2}-\d{2}/', $v, $m)) {
                return $m[0];
            }
            try {
                return \Carbon\Carbon::parse($v)->format('Y-m-d');
            } catch (\Throwable $e) {
                return '';
            }
        }
        return '';
    }

    // Non-date: return as-is (string/number/bool)
    return $v ?? '';
  }
@endphp

<x-app-layout>
  <div class="page-bg">
    <div class="w-full px-4 md:px-10 py-8">

      <!-- Header Bar -->
      <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 mb-8">
        <div class="flex items-center gap-8">
          <a href="{{ route('dashboard.kltg') }}" class="btn-ghost">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back
          </a>

          <div>
            <h1 class="font-serif text-4xl lg:text-5xl font-semibold ink">{{ $periodLabel }}</h1>
            <p class="muted text-sm mt-2 small-caps font-medium">Monthly KLTG Overview</p>
          </div>
        </div>

        <form method="GET" action="{{ route('coordinator.kltg.export') }}" class="inline-flex" id="exportForm">
          <input type="hidden" name="subcategory" value="{{ $activeTab }}">
          <input type="hidden" name="month" id="exportMonth">
          <input type="hidden" name="year" id="exportYear">
          <input type="hidden" name="working" value="{{ request('working') }}">
          <button type="submit" class="btn-export" onclick="syncExportForm()">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Export
          </button>
        </form>
      </div>

      <!-- Tab Strip -->
      @php $tabs = ['print'=>'KLTG','video'=>'Video','article'=>'Article','lb'=>'LB','em'=>'EM']; @endphp
      <div class="tab-strip mb-8">
        <div class="flex">
          @foreach ($tabs as $key => $label)
            <a href="{{ route('coordinator.kltg.index', array_filter(['tab'=>$key,'month'=>$month,'year'=>$year])) }}"
               class="tab {{ $activeTab===$key ? 'active' : '' }}">
              {{ $label }}
            </a>
          @endforeach
        </div>
      </div>

      <!-- Filter Panel -->
      <form method="get" class="card p-8 mb-8">
        <input type="hidden" name="tab" value="{{ $activeTab }}">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          <div>
            <label class="form-label">Month</label>
            <select name="month" class="form-control">
              <option value="">All Months</option>
              @for($m=1;$m<=12;$m++)
                <option value="{{ $m }}" @selected($month==$m)>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
              @endfor
            </select>
          </div>

          <div>
            <label class="form-label">Year</label>
            <select name="year" class="form-control">
              <option value="">All Years</option>
              @for($y=now()->year+1;$y>=now()->year-4;$y--)
                <option value="{{ $y }}" @selected($year==$y)>{{ $y }}</option>
              @endfor
            </select>
          </div>

          <div class="sm:col-span-2 lg:col-span-2 flex items-end">
            <button type="submit" class="btn-primary w-full lg:w-auto px-10">
              Apply Filters
            </button>
          </div>
        </div>
      </form>

      <!-- Data Table -->
      @if($rows->isEmpty())
        <div class="card">
          <div class="empty-state">
            <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-neutral-100 flex items-center justify-center">
              <svg class="w-10 h-10 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
            </div>
            <h3 class="font-serif text-xl font-medium ink mb-3">No Entries Found</h3>
            <p class="muted text-sm">No coordinator items found for {{ $periodLabel }}.</p>
          </div>
        </div>
      @else
        <div class="card">
          <div class="overflow-x-auto">
            <table class="data-table">
              <thead>
                <tr>
                  <th class="w-16">No</th>
                  <th class="min-w-[140px]">Date Created</th>
                  <th class="min-w-[180px]">Company</th>
                  <th class="min-w-[160px]">Person In Charge</th>
                  @foreach ($columns[$activeTab] as $col)
                    <th class="min-w-[160px]">{{ $col['label'] }}</th>
                  @endforeach
                </tr>
              </thead>
              <tbody>
                @foreach ($rows as $i => $r)
                  <tr>
                    <td class="text-neutral-500 font-medium tabular-nums text-right">{{ $i+1 }}</td>
                    <td class="ink tabular-nums font-medium">
                      {{ optional($r->date ?? null)->format('Y-m-d') ?? optional($r->created_at)->format('Y-m-d') }}
                    </td>
                    <td class="ink font-medium">{{ $r->company_name }}</td>
                    <td class="ink">{{ $r->client }}</td>

                    @foreach ($columns[$activeTab] as $col)
                      @php
                        $key = $col['key'];
                        $type = $col['type'];
                      @endphp

                      {{-- Edition & Publication are read-only from kltg_monthly_details (controller-injected) --}}
                      @if ($key === 'edition')
                        <td>
                          <span class="badge">{{ $r->edition ?? '—' }}</span>
                        </td>
                      @elseif ($key === 'publication')
                        <td>
                          <span class="badge">{{ $r->publication ?? '—' }}</span>
                        </td>
                      @else
                       @php $val = cellVal($existing, $r, $key, $type, $activeTab); @endphp
                        <td>
                          @if($type==='date')
                            <input type="date"
                              class="table-input"
                              value="{{ $val }}"
                              data-master-file-id="{{ $r->id }}"
                              data-subcategory="{{ $activeTab }}"
                              data-field="{{ $key }}" />
                          @else
                            <input type="text"
                              class="table-input"
                              value="{{ $val }}"
                              placeholder="—"
                              data-master-file-id="{{ $r->id }}"
                              data-subcategory="{{ $activeTab }}"
                              data-field="{{ $key }}" />
                          @endif
                        </td>
                      @endif
                    @endforeach
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      @endif

    </div>
  </div>

  {{-- KLTG Configuration (MUST come before autosave script) --}}
<script>
  // --- Export form sync (biarkan seperti punyamu, aku pertahankan) ---
  function syncExportForm() {
    const mSel = document.querySelector('select[name="month"]');
    const ySel = document.querySelector('select[name="year"]');
    document.getElementById('exportMonth').value = mSel?.value ?? '';
    document.getElementById('exportYear').value  = ySel?.value ?? '';
  }
  document.addEventListener('DOMContentLoaded', function () {
    const mSel = document.querySelector('select[name="month"]');
    const ySel = document.querySelector('select[name="year"]');
    mSel?.addEventListener('change', () => { document.getElementById('exportMonth').value = mSel.value; });
    ySel?.addEventListener('change', () => { document.getElementById('exportYear').value  = ySel.value; });
    syncExportForm();
  });

  window.KLTG = {
    upsertUrl: @json(route('coordinator.kltg.upsert')),
    csrf: @json(csrf_token())
  };
</script>

<!-- ===== Improved Autosave: month-aware & year normalized ===== -->
<script>
(async function () {
  const upsertUrl = window.KLTG?.upsertUrl;
  const csrf      = window.KLTG?.csrf;

  if (!upsertUrl || !csrf) {
    console.error('[KLTG] Missing upsertUrl or CSRF');
    return;
  }

  // ----- Helpers -----
  function getYearMonth() {
    // 1) Prefer dropdowns
    const ySel = document.querySelector('select[name="year"]');
    const mSel = document.querySelector('select[name="month"]');
    let year  = ySel?.value ?? '';
    let month = mSel?.value ?? '';

    // 2) Fallback hidden ctx (optional if you have them)
    if (!year)  year  = document.getElementById('ctxYear')?.value  ?? '';
    if (!month) month = document.getElementById('ctxMonth')?.value ?? '';

    // Normalize: remove non-digits from year (avoid "2,025")
    year = String(year ?? '').replace(/[^0-9]/g, '');
    const yNum = Number(year || 0);
    const mNum = Number(month || 0);

    return { year: yNum, month: mNum, rawMonth: month };
  }

  // Warn when trying to edit without a concrete month
  function requireConcreteMonth() {
    const { year, month } = getYearMonth();
    if (!month || month < 1 || month > 12) {
      alert('Pilih bulan dulu (bukan "All Months") sebelum mengedit.');
      return null;
    }
    if (!year || year < 1900) {
      alert('Tahun tidak valid. Pilih tahun yang benar.');
      return null;
    }
    return { year, month };
  }

  function buildPayload(el) {
    const masterId    = Number(el.dataset.masterFileId);
    const subcategory = el.dataset.subcategory;
    const field       = el.dataset.field;   // nama kolom di DB yang akan diubah

    if (!masterId || !subcategory || !field) return null;

    const value = (el.type === 'checkbox')
      ? (el.checked ? 1 : 0)
      : (el.value ?? '');

    // Include year/month (W-A-J-I-B)
    const ym = requireConcreteMonth();
    if (!ym) return null;

    const payload = {
      master_file_id: masterId,
      subcategory: subcategory,
      year: ym.year,          // number
      month: ym.month,        // number 1..12
      field: field,           // kompatibel dgn controller kamu sekarang
      column: field,          // safety kalau backend expect 'column'
      value: value
    };

    return payload;
  }

  // ----- Attach listeners -----
  const inputs = document.querySelectorAll('[data-master-file-id][data-field]');
  console.log(`[KLTG] ✅ Autosave listener attached: ${inputs.length} inputs found`);

  inputs.forEach(el => {
    el.addEventListener('change', () => save(el));
    el.addEventListener('blur',   () => save(el));
  });

  async function save(el) {
    const payload = buildPayload(el);
    if (!payload) return;

    // UI state
    el.classList.remove('bg-red-50','border-red-300','bg-green-50','border-green-300');
    el.classList.add('bg-yellow-50','border-yellow-300');

    try {
      const resp = await fetch(upsertUrl, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf,
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(payload)
      });

      if (!resp.ok) {
        const text = await resp.text().catch(() => '');
        console.error('[KLTG] ❌ SAVE ERR', resp.status, text);
        throw new Error(`HTTP ${resp.status}`);
      }

      el.classList.remove('bg-yellow-50','border-yellow-300');
      el.classList.add('bg-green-50','border-green-300');

    } catch (e) {
      el.classList.remove('bg-yellow-50','border-yellow-300');
      el.classList.add('bg-red-50','border-red-300');
    }
  }
})();
</script>

</x-app-layout>
