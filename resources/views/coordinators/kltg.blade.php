@push('head')
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style>
    /* --- light-touch cosmetics without changing logic --- */
    /* Make all coordinator inputs same visual width (incl. date) */
    .kltg-input { width: 16rem; }
    @media (max-width: 1280px) { .kltg-input { width: 14rem; } }
    @media (max-width: 1024px) { .kltg-input { width: 12rem; } }

    /* Tidy table visuals */
    .kltg-table th, .kltg-table td { vertical-align: middle; }
    .kltg-table thead th {
      position: sticky; top: 0; z-index: 10;
      background: #F9FAFB; /* gray-50 */
      box-shadow: inset 0 -1px 0 0 rgba(0,0,0,.06);
    }

    /* Subtle cell hover to aid scanning */
    .kltg-row:hover td { background: #FAFAFF; } /* very light */

    /* Compact, consistent input chrome */
    .kltg-input {
      border-radius: .5rem; /* rounded-lg */
      padding: .375rem .5rem; /* ~py-1.5 px-2 */
      border: 1px solid #E5E7EB; /* gray-200 */
      background: #fff;
      outline: none;
      transition: box-shadow .12s ease, border-color .12s ease, background-color .12s ease;
    }
    .kltg-input:focus {
      border-color: #6366F1; /* indigo-500 */
      box-shadow: 0 0 0 3px rgba(99,102,241,.15);
    }

    /* Inline status colors from autosave */
    .bg-yellow-50 { background-color: #FFFBEB !important; }
    .border-yellow-300 { border-color: #FCD34D !important; }
    .bg-green-50 { background-color: #F0FDF4 !important; }
    .border-green-300 { border-color: #86EFAC !important; }
    .bg-red-50 { background-color: #FEF2F2 !important; }
    .border-red-300 { border-color: #FCA5A5 !important; }

    /* Read-only badge look for Edition/Publication */
    .kltg-badge {
      display: inline-block;
      padding: .25rem .5rem;
      border-radius: .5rem;
      background: #F3F4F6; /* gray-100 */
      color: #111827;      /* gray-900 */
      min-width: 8rem;
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

  function cellVal($existing, $id, $key, $type){
      $row = $existing->get($id);
      if (!$row) return '';
      $col = _dbcol($key);
      $v   = $row->{$col} ?? '';

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
  <div class="p-4 md:p-6">
    <h3 class="mb-4 text-lg md:text-xl font-semibold text-gray-800">{{ $periodLabel }}</h3>

    <!-- Top bar: Back + Tabs + Filters -->
    <div class="flex flex-col gap-4">

    <a href="{{ route('dashboard.kltg') }}"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 active:bg-gray-100 transition">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Dashboard
    </a>

      @php $tabs = ['print'=>'KLTG','video'=>'Video','article'=>'Article','lb'=>'LB','em'=>'EM']; @endphp
      <div class="flex flex-wrap gap-2">
        @foreach ($tabs as $key => $label)
            <a href="{{ route('coordinator.kltg.index', array_filter(['tab'=>$key,'month'=>$month,'year'=>$year])) }}"
               class="px-3 py-2 rounded-lg text-sm font-medium border transition
               {{ $activeTab===$key
                  ? 'bg-indigo-600 text-white border-indigo-600 shadow-sm'
                  : 'bg-white text-gray-700 border-gray-200 hover:border-gray-300 hover:bg-gray-50' }}">
              {{ $label }}
            </a>
          @endforeach
        </div>
      </div>

      <!-- Filters -->
      <form method="get"
            class="rounded-xl border border-gray-200 bg-white p-3 md:p-4 shadow-sm">
        <input type="hidden" name="tab" value="{{ $activeTab }}">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
          <div>
            <label class="text-xs font-semibold text-gray-600">Month</label>
            <select name="month" class="mt-1 w-full border-gray-300 rounded-lg px-2 py-2 text-sm focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500">
              <option value="">All</option>
              @for($m=1;$m<=12;$m++)
                <option value="{{ $m }}" @selected($month==$m)>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
              @endfor
            </select>
          </div>
          <div>
            <label class="text-xs font-semibold text-gray-600">Year</label>
            <select name="year" class="mt-1 w-full border-gray-300 rounded-lg px-2 py-2 text-sm focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500">
              <option value="">All</option>
              @for($y=now()->year+1;$y>=now()->year-4;$y--)
                <option value="{{ $y }}" @selected($year==$y)>{{ $y }}</option>
              @endfor
            </select>
          </div>
          <div class="flex items-end">
            <button class="h-10 w-full sm:w-auto px-4 bg-indigo-600 text-white rounded-lg font-medium shadow-sm hover:bg-indigo-700 active:bg-indigo-800 transition">
              Apply
            </button>
          </div>
        </div>
      </form>

      @if($rows->isEmpty())
        <div class="rounded-lg border border-amber-300 bg-amber-50 text-amber-900 px-3 py-2 text-sm">
          No coordinator items found for {{ $periodLabel }}.
        </div>
      @endif
    </div>

    <!-- Table -->
    <div class="mt-4 overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
      <table class="kltg-table min-w-full text-sm">
        <thead>
          <tr>
            <th class="px-3 py-3 text-left font-semibold text-gray-700">No</th>
            <th class="px-3 py-3 text-left font-semibold text-gray-700">Date In</th>
            <th class="px-3 py-3 text-left font-semibold text-gray-700">Company</th>
            <th class="px-3 py-3 text-left font-semibold text-gray-700">Person In Charge</th>
            @foreach ($columns[$activeTab] as $col)
              <th class="px-3 py-3 text-left font-semibold text-gray-700">{{ $col['label'] }}</th>
            @endforeach
          </tr>
        </thead>
        <tbody>
          @foreach ($rows as $i => $r)
            <tr class="kltg-row border-t">
              <td class="px-3 py-3 text-gray-800">{{ $i+1 }}</td>
              <td class="px-3 py-3 text-gray-800">
                {{ optional($r->date ?? null)->format('Y-m-d') ?? optional($r->created_at)->format('Y-m-d') }}
              </td>
              <td class="px-3 py-3 text-gray-800">{{ $r->company_name }}</td>
              <td class="px-3 py-3 text-gray-800">{{ $r->client }}</td>

              @foreach ($columns[$activeTab] as $col)
                @php
                  $key = $col['key'];
                  $type = $col['type'];
                @endphp

                {{-- Edition & Publication are read-only from kltg_monthly_details (controller-injected) --}}
                @if ($key === 'edition')
                  <td class="px-3 py-3">
                    <span class="kltg-badge">{{ $r->edition ?? '—' }}</span>
                  </td>
                @elseif ($key === 'publication')
                  <td class="px-3 py-3">
                    <span class="kltg-badge">{{ $r->publication ?? '—' }}</span>
                  </td>
                @else
                  @php $val = cellVal($existing, $r->id, $key, $type); @endphp
                  <td class="px-3 py-3">
                    @if($type==='date')
                      <input type="date"
                        class="kltg-input"
                        value="{{ $val }}"
                        data-master-file-id="{{ $r->id }}"
                        data-subcategory="{{ $activeTab }}"
                        data-field="{{ $key }}" />
                    @else
                      <input type="text"
                        class="kltg-input"
                        value="{{ $val }}"
                        placeholder=""
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

  {{-- KLTG Configuration (MUST come before autosave script) --}}
  <script>
    window.KLTG = {
      upsertUrl: @json(route('coordinator.kltg.upsert')),
      csrf: @json(csrf_token())
    };
  </script>

  {{-- Improved Autosave Script (unchanged logic) --}}
  <script>
  (async function () {
    const upsertUrl = window.KLTG?.upsertUrl;
    const csrf = window.KLTG?.csrf;

    if (!upsertUrl || !csrf) {
      console.error('[KLTG] Missing upsertUrl or CSRF meta');
      return;
    }

    // NOTE: We don't render inputs for edition/publication, so they won't be selected/saved.
    const inputs = document.querySelectorAll('[data-master-file-id][data-field]');
    console.log(`[KLTG] ✅ Autosave listener attached: ${inputs.length} inputs found`);

    inputs.forEach(el => {
      el.addEventListener('change', () => save(el));
      el.addEventListener('blur',   () => save(el));
    });

    function buildPayload(el) {
      const masterId    = Number(el.dataset.masterFileId);
      const subcategory = el.dataset.subcategory;
      const field       = el.dataset.field;

      if (!masterId || !subcategory || !field) return null;

      const value = (el.type === 'checkbox')
        ? (el.checked ? 1 : 0)
        : (el.value ?? '');

      return { master_file_id: masterId, subcategory, field, value };
    }

    async function save(el) {
      const payload = buildPayload(el);
      if (!payload) return;

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
