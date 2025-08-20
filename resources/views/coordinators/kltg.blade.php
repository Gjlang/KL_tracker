{{-- resources/views/coordinators/kltg.blade.php --}}
@php
  /** @var \Illuminate\Support\Collection $rows */
  /** @var \Illuminate\Support\Collection $existing */
  function cellVal($existing, $id, $sub, $key, $type){
      $m = $existing->get("{$id}|{$sub}");
      if (!$m) return '';
      if ($type === 'date') return optional($m->{$key})->format('Y-m-d');
      return $m->{$key} ?? '';
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
                @php $val = cellVal($existing, $r->id, $activeTab, $col['key'], $col['type']); @endphp
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

{{-- Improved Autosave Script --}}
<script>
(function () {
  const meta = document.querySelector('meta[name="csrf-token"]');
  const CSRF = meta ? meta.getAttribute('content') : '';

  const ENDPOINT = "{{ route('coordinator.kltg.upsert') }}";

  function log(...a){ try { console.log('[KLTG]', ...a); } catch(_){} }

  async function save(el) {
    if (!el || !el.classList.contains('kltg-input')) return;

    const mf  = el.dataset.masterFileId;
    const sub = el.dataset.subcategory;
    const fld = el.dataset.field;

    if (!mf || !sub || !fld) {
      log('❌ Missing data-*', {mf, sub, fld});
      return;
    }

    const body = new URLSearchParams({
      master_file_id: mf,
      subcategory: sub,   // UI token: print|video|article|lb|em
      field: fld,
      value: el.value ?? '',
      _token: CSRF
    });

    // Visual feedback
    el.classList.remove('bg-red-50','ring-red-300','bg-green-50','ring-green-300');
    el.classList.add('bg-yellow-50','ring-2','ring-yellow-300');

    log('➡️ POST', Object.fromEntries(body));

    try {
      const res = await fetch(ENDPOINT, {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': CSRF,
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body
      });
      const txt = await res.text(); // keep raw to see 419/422 body
      log('⬅️ RESP', res.status, txt);

      if (!res.ok) throw new Error('HTTP '+res.status);

      el.classList.remove('bg-yellow-50','ring-yellow-300');
      el.classList.add('bg-green-50','ring-2','ring-green-300');
      setTimeout(()=>el.classList.remove('bg-green-50','ring-2','ring-green-300'), 800);
    } catch (e) {
      el.classList.remove('bg-yellow-50','ring-yellow-300');
      el.classList.add('bg-red-50','ring-2','ring-red-300');
      log('❌ SAVE ERR', e);
    }
  }

  // Event delegation so it works for all inputs
  const handler = (e) => {
    const el = e.target.closest('.kltg-input');
    if (!el) return;
    // debounce per-input
    clearTimeout(el._t);
    el._t = setTimeout(()=>save(el), 350);
  };

  document.addEventListener('input', handler);
  log('✅ Autosave listener attached:', document.querySelectorAll('.kltg-input').length, 'inputs found');
})();
</script>

</x-app-layout>
