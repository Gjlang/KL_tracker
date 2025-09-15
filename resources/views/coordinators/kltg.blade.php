

@push('head')
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;500;600&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
@endpush

@push('styles')
<style>
/* Base Typography & Colors */
.font-serif { font-family: 'EB Garamond', Georgia, serif; }
.font-sans { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; }
.ink { color: #1C1E26; }
.hairline { border-color: #EAEAEA; }
.small-caps {
  text-transform: uppercase;
  letter-spacing: 0.06em;
  font-size: 11px;
  font-weight: 600;
}
.tabular-nums { font-variant-numeric: tabular-nums; }

/* Layout */
.page-canvas {
  background-color: #F7F7F9;
  min-height: 100vh;
}
.surface {
  background-color: #FFFFFF;
  border-radius: 12px;
  border: 1px solid #EAEAEA;
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.02);
}

/* Button System */
.btn-primary {
  background-color: #22255b;
  color: white;
  border-radius: 9999px;
  padding: 8px 16px;
  font-size: 14px;
  font-weight: 500;
  transition: all 150ms;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border: none;
  cursor: pointer;
}
.btn-primary:hover {
  opacity: 0.9;
}
.btn-primary:focus {
  outline: none;
  box-shadow: 0 0 0 2px #4bbbed, 0 0 0 4px rgba(75, 187, 237, 0.2);
}

.btn-ghost {
  border: 1px solid #EAEAEA;
  color: #374151;
  background: white;
  border-radius: 9999px;
  padding: 8px 16px;
  font-size: 14px;
  font-weight: 500;
  transition: all 150ms;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  text-decoration: none;
}
.btn-ghost:hover {
  background-color: #f9fafb;
  color: #374151;
  text-decoration: none;
}
.btn-ghost:focus {
  outline: none;
  box-shadow: 0 0 0 2px #4bbbed, 0 0 0 4px rgba(75, 187, 237, 0.2);
}

/* Filter Chips */
.filter-chip {
  display: inline-flex;
  align-items: center;
  padding: 6px 12px;
  border-radius: 9999px;
  font-size: 12px;
  font-weight: 500;
  background-color: #f3f4f6;
  color: #6b7280;
  border: 1px solid #EAEAEA;
  margin-right: 8px;
}
.filter-chip-close {
  margin-left: 8px;
  color: #9ca3af;
  cursor: pointer;
}
.filter-chip-close:hover {
  color: #6b7280;
}

/* Tab Strip - Fixed */
.tab-container {
  background-color: #FFFFFF;
  border-radius: 12px;
  border: 1px solid #EAEAEA;
  margin-bottom: 24px;
  overflow: hidden;
}
.tab-strip {
  display: flex;
  border-bottom: 1px solid #EAEAEA;
  overflow-x: auto;
  scrollbar-width: none;
  -ms-overflow-style: none;
}
.tab-strip::-webkit-scrollbar {
  display: none;
}
.tab {
  padding: 12px 20px;
  font-size: 14px;
  font-weight: 500;
  transition: all 150ms;
  border-bottom: 2px solid transparent;
  white-space: nowrap;
  position: relative;
  color: #6b7280;
  text-decoration: none;
  min-width: max-content;
}
.tab:not(.active):hover {
  color: #374151;
  background-color: rgba(75, 187, 237, 0.05);
  text-decoration: none;
}
.tab.active {
  color: #1C1E26;
  border-bottom-color: #22255b;
}
.tab:focus-visible {
  outline: none;
  box-shadow: inset 0 0 0 2px #4bbbed;
}

/* Form Controls */
.form-control {
  height: 40px;
  width: 100%;
  border: 1px solid #EAEAEA;
  border-radius: 8px;
  padding: 0 12px;
  font-size: 14px;
  font-family: 'Inter', sans-serif;
  transition: all 150ms;
}
.form-control:focus {
  outline: none;
  border-color: #4bbbed;
  box-shadow: 0 0 0 3px rgba(75, 187, 237, 0.1);
}
.form-label {
  display: block;
  color: #6b7280;
  margin-bottom: 8px;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  font-size: 11px;
  font-weight: 600;
}

/* Data Table - Simplified */
.table-container {
  background-color: #FFFFFF;
  border-radius: 12px;
  border: 1px solid #EAEAEA;
  overflow: hidden;
}
.data-table {
  width: 100%;
  font-size: 14px;
}
.data-table thead th {
  padding: 16px 12px;
  color: #6b7280;
  background-color: rgba(249, 250, 251, 0.8);
  border-bottom: 1px solid #EAEAEA;
  position: sticky;
  top: 0;
  z-index: 10;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  font-size: 11px;
  font-weight: 600;
  text-align: left;
}
.data-table thead th.text-right {
  text-align: right;
}
.data-table tbody td {
  padding: 12px;
  border-bottom: 1px solid #EAEAEA;
  transition: background-color 150ms;
}
.data-table tbody td.text-right {
  text-align: right;
}
.data-table tbody tr:hover {
  background-color: rgba(249, 250, 251, 0.5);
}
.data-table tbody tr:last-child td {
  border-bottom: none;
}

/* Table Inputs */
.table-input {
  height: 36px;
  font-size: 14px;
  padding: 0 10px;
  border-radius: 6px;
  border: 1px solid #d1d5db;
  width: 100%;
  transition: all 150ms;
  min-width: 100px;
}
.table-input:focus {
  outline: none;
  border-color: #4bbbed;
  box-shadow: 0 0 0 3px rgba(75, 187, 237, 0.1);
}
.table-input.text-right {
  text-align: right;
}

/* Status Colors for autosave */
.table-input.saving {
  background-color: #fef3cd;
  border-color: #f59e0b;
}
.table-input.saved {
  background-color: #d1fae5;
  border-color: #10b981;
}
.table-input.error {
  background-color: #fee2e2;
  border-color: #ef4444;
}

/* Column Widths - Responsive */
.col-no { width: 60px; min-width: 60px; }
.col-date { width: 120px; min-width: 120px; }
.col-company { width: 200px; min-width: 180px; }
.col-person { width: 140px; min-width: 120px; }
.col-standard { width: 140px; min-width: 120px; }

/* Badge */
.badge {
  display: inline-flex;
  align-items: center;
  padding: 4px 8px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 500;
  background-color: #f3f4f6;
  color: #374151;
  border: 1px solid #e5e7eb;
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 64px 24px;
}
.placeholder-dash {
  color: #9ca3af;
}

/* Responsive */
@media (max-width: 768px) {
  .col-company {
    width: 160px;
    min-width: 140px;
  }
  .col-standard {
    width: 120px;
    min-width: 100px;
  }
  .data-table thead th,
  .data-table tbody td {
    padding: 8px;
  }
  .tab {
    padding: 12px 16px;
  }
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
  <div class="page-canvas">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

      <!-- Header -->
      <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 mb-8">
        <div class="flex items-center gap-6">
          <a href="{{ route('dashboard.kltg') }}" class="btn-ghost">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back
          </a>

          <div>
            <h1 class="font-serif text-4xl font-medium ink">{{ $periodLabel }}</h1>
            <p class="text-neutral-500 text-sm mt-1">Monthly KLTG Overview</p>
          </div>
        </div>

        <div class="flex items-center gap-3">
          <form method="GET" action="{{ route('coordinator.kltg.export') }}" id="exportForm">
            <input type="hidden" name="subcategory" value="{{ $activeTab }}">
            <input type="hidden" name="month" id="exportMonth" value="{{ $month }}">
            <input type="hidden" name="year" id="exportYear" value="{{ $year }}">
            <input type="hidden" name="working" value="{{ request('working') }}">
            <button type="submit" class="btn-primary">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
              Export
            </button>
          </form>
        </div>
      </div>

      <!-- Filter Panel -->
      <div class="surface p-6 mb-6">
        <!-- Active Filters -->
        @if($month || $year)
          <div class="mb-6 pb-4 border-b hairline">
            <div class="flex flex-wrap gap-2">
              @if($month)
                <span class="filter-chip">
                  {{ date('F', mktime(0,0,0,$month,1)) }}
                  <span class="filter-chip-close" onclick="clearParam('month')">×</span>
                </span>
              @endif
              @if($year)
                <span class="filter-chip">
                  {{ $year }}
                  <span class="filter-chip-close" onclick="clearParam('year')">×</span>
                </span>
              @endif
            </div>
          </div>
        @endif

        <!-- Filter Form -->
        <form method="get">
          <input type="hidden" name="tab" value="{{ $activeTab }}">
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 items-end">
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

            <div>
              <!-- Spacer -->
            </div>

            <div>
              <button type="submit" class="btn-primary w-full">
                Apply Filters
              </button>
            </div>
          </div>
        </form>
      </div>

      <!-- Tabs -->
      @php $tabs = ['print'=>'KLTG','video'=>'Video','article'=>'Article','lb'=>'LB','em'=>'EM']; @endphp
      <div class="tab-container">
        <div class="tab-strip">
          @foreach ($tabs as $key => $label)
            <a href="{{ route('coordinator.kltg.index', array_filter(['tab'=>$key,'month'=>$month,'year'=>$year])) }}"
               class="tab {{ $activeTab===$key ? 'active' : '' }}">
              {{ $label }}
            </a>
          @endforeach
        </div>
      </div>

      <!-- Data Table -->
      @if($rows->isEmpty())
        <div class="surface">
          <div class="empty-state">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-neutral-100 flex items-center justify-center">
              <svg class="w-8 h-8 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
            </div>
            <h3 class="font-serif text-lg font-medium ink mb-2">No entries found</h3>
            <p class="text-neutral-500 text-sm">No coordinator items found for the selected period.</p>
          </div>
        </div>
      @else
        <div class="table-container">
          <div class="overflow-x-auto">
            <table class="data-table">
              <thead>
                <tr>
                  <th class="col-no text-right">No</th>
                  <th class="col-date">Date Created</th>
                  <th class="col-company">Company</th>
                  <th class="col-person">Person In Charge</th>
                  @foreach ($columns[$activeTab] as $col)
                    @php
                      $isNumeric = in_array($col['key'], ['x', 'edition', 'publication', 'em_qty']);
                    @endphp
                    <th class="col-standard {{ $isNumeric ? 'text-right' : '' }}">
                      @if($col['key'] === 'x')
                        Pages
                      @elseif($col['key'] === 'em_qty')
                        Quantity
                      @else
                        {{ $col['label'] }}
                      @endif
                    </th>
                  @endforeach
                </tr>
              </thead>
              <tbody>
                @foreach ($rows as $i => $r)
                  <tr>
                    <td class="col-no text-right text-neutral-500 font-medium tabular-nums">{{ $i+1 }}</td>
                    <td class="col-date ink font-medium tabular-nums">
                      {{ optional($r->date ?? null)->format('Y-m-d') ?? optional($r->created_at)->format('Y-m-d') }}
                    </td>
                    <td class="col-company ink font-medium">
                      <div class="truncate" title="{{ $r->company_name }}">{{ $r->company_name }}</div>
                    </td>
                    <td class="col-person ink">{{ $r->client }}</td>

                    @foreach ($columns[$activeTab] as $col)
                      @php
                        $key = $col['key'];
                        $type = $col['type'];
                        $isNumeric = in_array($key, ['x', 'edition', 'publication', 'em_qty']);
                      @endphp

                      @if ($key === 'edition')
                        <td class="col-standard {{ $isNumeric ? 'text-right' : '' }}">
                          @if($r->edition)
                            <span class="badge">{{ $r->edition }}</span>
                          @else
                            <span class="placeholder-dash">—</span>
                          @endif
                        </td>
                      @elseif ($key === 'publication')
                        <td class="col-standard {{ $isNumeric ? 'text-right' : '' }}">
                          @if($r->publication)
                            <span class="badge">{{ $r->publication }}</span>
                          @else
                            <span class="placeholder-dash">—</span>
                          @endif
                        </td>
                      @else
                        @php $val = cellVal($existing, $r, $key, $type, $activeTab); @endphp
                        <td class="col-standard {{ $isNumeric ? 'text-right' : '' }}">
                          @if($type==='date')
                            <input type="date"
                              class="table-input {{ $isNumeric ? 'text-right' : '' }} tabular-nums"
                              value="{{ $val }}"
                              data-master-file-id="{{ $r->id }}"
                              data-subcategory="{{ $activeTab }}"
                              data-field="{{ $key }}" />
                          @else
                            <input type="text"
                              class="table-input {{ $isNumeric ? 'text-right tabular-nums' : '' }}"
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

  <script>
    // Clear filter parameter
    function clearParam(param) {
      const url = new URL(window.location);
      url.searchParams.delete(param);
      window.location.href = url.toString();
    }

    // Export form sync
    function syncExportForm() {
      const mSel = document.querySelector('select[name="month"]');
      const ySel = document.querySelector('select[name="year"]');
      document.getElementById('exportMonth').value = mSel?.value ?? '';
      document.getElementById('exportYear').value = ySel?.value ?? '';
    }

    document.addEventListener('DOMContentLoaded', function () {
      const mSel = document.querySelector('select[name="month"]');
      const ySel = document.querySelector('select[name="year"]');
      mSel?.addEventListener('change', syncExportForm);
      ySel?.addEventListener('change', syncExportForm);
      syncExportForm();
    });

    window.KLTG = {
      upsertUrl: @json(route('coordinator.kltg.upsert')),
      csrf: @json(csrf_token())
    };

    // Autosave functionality
    (async function () {
      const upsertUrl = window.KLTG?.upsertUrl;
      const csrf = window.KLTG?.csrf;

      if (!upsertUrl || !csrf) {
        console.error('[KLTG] Missing upsertUrl or CSRF');
        return;
      }

      function getYearMonth() {
        const ySel = document.querySelector('select[name="year"]');
        const mSel = document.querySelector('select[name="month"]');
        let year = ySel?.value ?? '';
        let month = mSel?.value ?? '';

        year = String(year ?? '').replace(/[^0-9]/g, '');
        const yNum = Number(year || 0);
        const mNum = Number(month || 0);

        return { year: yNum, month: mNum };
      }

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
        const masterId = Number(el.dataset.masterFileId);
        const subcategory = el.dataset.subcategory;
        const field = el.dataset.field;

        if (!masterId || !subcategory || !field) return null;

        const value = el.value ?? '';
        const ym = requireConcreteMonth();
        if (!ym) return null;

        return {
          master_file_id: masterId,
          subcategory: subcategory,
          year: ym.year,
          month: ym.month,
          field: field,
          column: field,
          value: value
        };
      }

      const inputs = document.querySelectorAll('[data-master-file-id][data-field]');

      inputs.forEach(el => {
        el.addEventListener('change', () => save(el));
        el.addEventListener('blur', () => save(el));
      });

      async function save(el) {
        const payload = buildPayload(el);
        if (!payload) return;

        el.classList.remove('error', 'saved');
        el.classList.add('saving');

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
            throw new Error(`HTTP ${resp.status}`);
          }

          el.classList.remove('saving');
          el.classList.add('saved');

          // Remove saved class after 2 seconds
          setTimeout(() => {
            el.classList.remove('saved');
          }, 2000);

        } catch (e) {
          el.classList.remove('saving');
          el.classList.add('error');
        }
      }
    })();
  </script>

</x-app-layout>
