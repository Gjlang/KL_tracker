@push('head')
  <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@php
  /** @var \Illuminate\Support\Collection $rows */
  /** @var \Illuminate\Support\Collection $existing */

  // Display key (yang dipakai di $columns) -> nama kolom di DB
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

  // BACA nilai yang sudah disimpan (existing di-keyBy master_file_id)
  function cellVal($existing, $id, $key, $type){
      $row = $existing->get($id);
      if (!$row) return '';

      $col = _dbcol($key);
      $v   = $row->{$col} ?? '';

      // Jika kolom berupa Carbon/DateTime, format. Jika string 'Y-m-d', biarkan.
      if ($v && is_object($v) && method_exists($v, 'format')) {
          $v = $v->format('Y-m-d');
      }
      return $v ?? '';
  }
@endphp

<x-app-layout>
  <div class="p-4 md:p-6">
    {{-- Tabs --}}
    @php
      $tabs = ['print' => 'KLTG','video'=>'Video','article'=>'Article','lb'=>'LB','em'=>'EM'];
    @endphp
    <div class="flex gap-2 mb-4">
      @foreach ($tabs as $key => $label)
        <a href="{{ route('coordinator.kltg.index', array_filter(['tab'=>$key,'month'=>$month,'year'=>$year])) }}"
           class="px-3 py-2 rounded-lg text-sm font-medium border {{ $activeTab===$key ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-700 border-gray-200 hover:border-gray-300' }}">
          {{ $label }}
        </a>
      @endforeach
    </div>

    {{-- Filters (optional) --}}
    <form method="get" class="mb-4 flex gap-3 items-end">
      <input type="hidden" name="tab" value="{{ $activeTab }}">
      <div>
        <label class="text-xs font-semibold text-gray-600">Month</label>
        <select name="month" class="mt-1 border rounded px-2 py-1">
          <option value="">All</option>
          @for($m=1;$m<=12;$m++)
            <option value="{{ $m }}" @selected($month==$m)>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
          @endfor
        </select>
      </div>
      <div>
        <label class="text-xs font-semibold text-gray-600">Year</label>
        <select name="year" class="mt-1 border rounded px-2 py-1">
          <option value="">All</option>
          @for($y=now()->year+1;$y>=now()->year-4;$y--)
            <option value="{{ $y }}" @selected($year==$y)>{{ $y }}</option>
          @endfor
        </select>
      </div>
      <button class="h-9 px-4 bg-indigo-600 text-white rounded-lg">Apply</button>
    </form>

    {{-- Table --}}
    <div class="overflow-x-auto border rounded-lg">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-3 py-2 text-left">No</th>
            <th class="px-3 py-2 text-left">Date In</th>
            <th class="px-3 py-2 text-left">Company</th>
            <th class="px-3 py-2 text-left">Client</th>
            @foreach ($columns[$activeTab] as $col)
              <th class="px-3 py-2 text-left">{{ $col['label'] }}</th>
            @endforeach
          </tr>
        </thead>
        <tbody>
          @foreach ($rows as $i => $r)
            <tr class="border-t">
              <td class="px-3 py-2">{{ $i+1 }}</td>
              <td class="px-3 py-2">{{ optional($r->date ?? null)->format('Y-m-d') ?? optional($r->created_at)->format('Y-m-d') }}</td>
              <td class="px-3 py-2">{{ $r->company_name }}</td>
              <td class="px-3 py-2">{{ $r->client }}</td>

              @foreach ($columns[$activeTab] as $col)
                @php $val = cellVal($existing, $r->id, $col['key'], $col['type']); @endphp
                <td class="px-3 py-2">
                  @if($col['type']==='date')
                    <input type="date"
                      class="kltg-input w-44 border rounded px-2 py-1"
                      value="{{ $val }}"
                      data-master-file-id="{{ $r->id }}"
                      data-subcategory="{{ $activeTab }}"
                      data-field="{{ $col['key'] }}" />
                  @else
                    <input type="text"
                      class="kltg-input w-56 border rounded px-2 py-1"
                      value="{{ $val }}"
                      placeholder=""
                      data-master-file-id="{{ $r->id }}"
                      data-subcategory="{{ $activeTab }}"
                      data-field="{{ $col['key'] }}" />
                  @endif
                </td>
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

{{-- Improved Autosave Script --}}
<script>
(async function () {
  const upsertUrl = window.KLTG?.upsertUrl;
  const csrf = window.KLTG?.csrf;

  if (!upsertUrl || !csrf) {
    console.error('[KLTG] Missing upsertUrl or CSRF meta');
    return;
  }

  // Attach once to all inputs with data-master-file-id + data-field
  const inputs = document.querySelectorAll('[data-master-file-id][data-field]');
  console.log(`[KLTG] ✅ Autosave listener attached: ${inputs.length} inputs found`);

  inputs.forEach(el => {
    el.addEventListener('change', () => save(el));
    el.addEventListener('blur',   () => save(el));
  });
function buildPayload(el) {
  const masterId    = Number(el.dataset.masterFileId);
  const subcategory = el.dataset.subcategory;   // "print" | "video" | "article" | "lb" | "em"
  const field       = el.dataset.field;         // e.g. "title", "artwork_reminder", etc.

  if (!masterId || !subcategory || !field) return null;

  const value = (el.type === 'checkbox')
    ? (el.checked ? 1 : 0)
    : (el.value ?? '');

  return {
    master_file_id: masterId,
    subcategory,
    field,
    value
  };
}

  async function save(el) {
    const payload = buildPayload(el);
    if (!payload) return;

    // inline feedback
    el.classList.remove('bg-red-50','border-red-300','bg-green-50','border-green-300');
    el.classList.add('bg-yellow-50','border-yellow-300');

    try {
      const resp = await fetch(upsertUrl, {
        method: 'POST',
        credentials: 'same-origin',              // IMPORTANT: send session cookie
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf,                  // IMPORTANT: token header
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(payload)
      });

      if (!resp.ok) {
        const text = await resp.text().catch(() => '');
        console.error('[KLTG] ❌ SAVE ERR', resp.status, text);
        throw new Error(`HTTP ${resp.status}`);
      }

      // success
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
