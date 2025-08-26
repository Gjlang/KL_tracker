{{-- resources/views/coordinators/media.blade.php --}}
<x-app-layout>
  @push('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
      /* Enhanced sticky positioning */
      .sticky-filter {
        position: sticky;
        top: 0;
        z-index: 50;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
      }

      /* Advanced sticky table implementation */
      @media (min-width: 768px) {
        .sticky-table thead th {
          position: sticky;
          top: 140px;
          z-index: 30;
          background: linear-gradient(to bottom, rgb(248 250 252), rgb(241 245 249));
          backdrop-filter: blur(8px);
          border-bottom: 2px solid rgb(226 232 240);
        }

        .sticky-table tbody td:nth-child(1),
        .sticky-table tbody td:nth-child(2),
        .sticky-table thead th:nth-child(1),
        .sticky-table thead th:nth-child(2) {
          position: sticky;
          background: white;
          z-index: 20;
          box-shadow: 2px 0 4px -2px rgba(0, 0, 0, 0.1);
        }

        .sticky-table thead th:nth-child(1),
        .sticky-table tbody td:nth-child(1) {
          left: 0;
          background: linear-gradient(to right, white, rgb(249 250 251));
        }

        .sticky-table thead th:nth-child(2),
        .sticky-table tbody td:nth-child(2) {
          left: 4rem;
          background: linear-gradient(to right, rgb(249 250 251), white);
        }

        .sticky-table tbody tr:nth-child(even) td:nth-child(1) {
          background: linear-gradient(to right, rgb(248 250 252), rgb(241 245 249));
        }

        .sticky-table tbody tr:nth-child(even) td:nth-child(2) {
          background: linear-gradient(to right, rgb(241 245 249), rgb(248 250 252));
        }

        .sticky-table tbody tr:hover td:nth-child(1),
        .sticky-table tbody tr:hover td:nth-child(2) {
          background: linear-gradient(to right, rgb(239 246 255), rgb(219 234 254));
        }
      }

      /* Premium input styling */
      .input-premium {
        background: white;
        border: 1.5px solid rgb(226 232 240);
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
      }

      .input-premium:focus {
        outline: none;
        border-color: #4bbbed;
        box-shadow: 0 0 0 3px rgba(75, 187, 237, 0.12);
        background: rgb(249 250 251);
      }

      .input-premium:disabled {
        background: rgb(248 250 252);
        border-color: rgb(226 232 240);
        color: rgb(148 163 184);
      }

      /* Enhanced checkbox styling */
      .checkbox-premium {
        appearance: none;
        background: white;
        border: 1.5px solid rgb(203 213 225);
        width: 1.125rem;
        height: 1.125rem;
        border-radius: 0.25rem;
        position: relative;
        cursor: pointer;
        transition: all 0.2s ease;
      }

      .checkbox-premium:checked {
        background: #4bbbed;
        border-color: #4bbbed;
      }

      .checkbox-premium:checked::after {
        content: '';
        position: absolute;
        top: 1px;
        left: 4px;
        width: 6px;
        height: 10px;
        border: solid white;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
      }

      .checkbox-premium:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(75, 187, 237, 0.2);
      }

      .checkbox-premium:disabled {
        background: rgb(248 250 252);
        border-color: rgb(226 232 240);
        cursor: not-allowed;
      }

      /* Smooth animations */
      .fade-in {
        animation: fadeIn 0.3s ease-out;
      }

      @keyframes fadeIn {
        from { opacity: 0; transform: translateY(8px); }
        to { opacity: 1; transform: translateY(0); }
      }
    </style>
  @endpush

  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50/30 to-slate-50"
       x-data="mediaCoordinator({
         postUrl: '{{ route('coordinator.media.upsert') }}',
         initialYear: {{ (int)$year }},
         initialMonth: {{ $month ? (int)$month : 'null' }},
         initialTab: '{{ $activeTab ?? 'content' }}',
         initialScope: '{{ $scope ?? 'month_year' }}',
       })">

    {{-- Premium Sticky Filter Bar --}}
    <div class="sticky-filter bg-white/95 border-b border-slate-200/80 shadow-lg">
      <div class="max-w-7xl mx-auto px-6 py-5">
        <form method="GET" action="{{ route('coordinator.media.index') }}" class="flex flex-col lg:flex-row gap-6 items-start lg:items-end justify-between">
          <div class="flex flex-col sm:flex-row gap-5">
            <div class="space-y-2">
              <label class="block text-sm font-semibold text-slate-700">Year</label>
              <select name="year" class="input-premium h-11 px-4 py-2 rounded-xl text-sm font-medium min-w-[6rem] shadow-sm">
                @for($y = now()->year - 2; $y <= now()->year + 1; $y++)
                  <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
              </select>
            </div>

            <div class="space-y-2">
  <label class="block text-sm font-semibold text-slate-700">Month</label>
  <select name="month"
          class="input-premium h-11 px-4 py-2 rounded-xl text-sm font-medium min-w-[8rem] shadow-sm"
          onchange="
            const p = new URLSearchParams(window.location.search);
            const v = this.value;
            if (v) { p.set('month', v); } else { p.delete('month'); }
            if (!p.has('year'))  p.set('year', '{{ (int)$year }}');
            if (!p.has('tab'))   p.set('tab',  '{{ $activeTab ?? 'content' }}');
            if (!p.has('scope')) p.set('scope','{{ $scope ?? 'month_year' }}');
            window.location = `${location.pathname}?${p.toString()}`;
          ">
    <option value="" {{ $month ? '' : 'selected' }}>All months</option>
    @for($m=1;$m<=12;$m++)
      <option value="{{ $m }}" {{ $month===$m ? 'selected':'' }}>
        {{ \Carbon\Carbon::create()->startOfYear()->month($m)->format('F') }}
      </option>
    @endfor
  </select>
</div>


            <div class="space-y-2">
              <label class="block text-sm font-semibold text-slate-700">Scope</label>
              <select name="scope" class="input-premium h-11 px-4 py-2 rounded-xl text-sm font-medium min-w-[10rem] shadow-sm">
                <option value="month_year" {{ ($scope ?? '')==='month_year' ? 'selected' : '' }}>Month + Year</option>
                <option value="month_only" {{ ($scope ?? '')==='month_only' ? 'selected' : '' }}>Month (All Years)</option>
                <option value="year_only"  {{ ($scope ?? '')==='year_only'  ? 'selected' : '' }}>All Months (Year)</option>
                <option value="all"        {{ ($scope ?? '')==='all'        ? 'selected' : '' }}>All Months (All Years)</option>
              </select>
            </div>

            <button type="submit" class="h-11 px-6 py-2 bg-gradient-to-r from-[#22255b] to-[#1a1d47] text-black rounded-xl hover:from-[#1a1d47] hover:to-[#141729] transition-all duration-200 shadow-lg hover:shadow-xl font-semibold text-sm transform hover:-translate-y-0.5">
              Apply Filters
            </button>
          </div>

          <div class="flex flex-col items-end gap-2 text-right">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-slate-100 rounded-full">
              <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
              <span class="text-sm font-medium text-slate-700">{{ count($masters) }} companies • {{ $periodLabel }}</span>
            </div>
            <template x-if="!selectedMonth">
              <span class="text-xs text-slate-500 bg-amber-50 px-2 py-1 rounded-full border border-amber-200">
                📊 Latest values • Select month to edit
              </span>
            </template>
            <template x-if="selectedMonth">
              <span class="text-xs text-emerald-600 font-semibold bg-emerald-50 px-2 py-1 rounded-full border border-emerald-200">
                ✏️ Editing enabled
              </span>
            </template>
          </div>
        </form>
      </div>
    </div>

    {{-- Empty state / Main Content --}}
    <div class="max-w-7xl mx-auto px-6 py-8 space-y-8">
      @if(count($masters)===0)
        <div class="rounded-xl border border-slate-200 bg-white p-6 text-center text-slate-600">
          No coordinator items found for {{ $periodLabel }}.
        </div>
      @endif

      {{-- Premium Tab Navigation --}}
      <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-slate-200/60 p-2">
        <nav class="flex flex-wrap gap-1" role="tablist">
          <template x-for="t in tabs" :key="t.key">
            <button type="button"
                    @click.prevent="switchTab(t.key)"
                    :class="activeTab===t.key ? 'bg-gradient-to-r from-[#4bbbed] to-[#3da5cc] text-white shadow-lg shadow-blue-200/50 font-bold border-0 transform scale-105' : 'text-slate-600 hover:text-slate-800 hover:bg-slate-50 border border-transparent'"
                    class="px-6 py-3 rounded-xl transition-all duration-300 text-sm whitespace-nowrap font-medium"
                    :aria-selected="activeTab===t.key"
                    role="tab">
              <span x-text="t.label"></span>
            </button>
          </template>
        </nav>
      </div>

      {{-- Enhanced Tab Content --}}
      <div class="space-y-8">
        {{-- Content Calendar --}}
        <div x-show="activeTab==='content'" x-cloak class="fade-in">
          @include('coordinators.partials._tab_table', [
            'title' => 'Content Calendar',
            'description' => 'Track artwork creation and approval workflow',
            'icon' => '📅',
            'section' => 'content',
            'columns' => [
              ['key'=>'total_artwork','label'=>'Total artwork','type'=>'text'],
              ['key'=>'pending','label'=>'Pending','type'=>'text'],
              ['key'=>'draft_wa','label'=>'Draft WA','type'=>'checkbox'],
              ['key'=>'approved','label'=>'Approved','type'=>'checkbox'],
            ],
            'map' => $contentMap,
            'masters' => $masters,
            'year' => $year,
            'month' => $month,
          ])
        </div>

        {{-- Artwork Editing --}}
        <div x-show="activeTab==='editing'" x-cloak class="fade-in">
          @include('coordinators.partials._tab_table', [
            'title' => 'Artwork Editing',
            'description' => 'Monitor editing progress and approvals',
            'icon' => '🎨',
            'section' => 'editing',
            'columns' => [
              ['key'=>'total_artwork','label'=>'Total artwork','type'=>'text'],
              ['key'=>'pending','label'=>'Pending','type'=>'text'],
              ['key'=>'draft_wa','label'=>'Draft WA','type'=>'checkbox'],
              ['key'=>'approved','label'=>'Approved','type'=>'checkbox'],
            ],
            'map' => $editingMap,
            'masters' => $masters,
            'year' => $year,
            'month' => $month,
          ])
        </div>

        {{-- Posting Scheduling --}}
        <div x-show="activeTab==='schedule'" x-cloak class="fade-in">
          @include('coordinators.partials._tab_table', [
            'title' => 'Posting Scheduling',
            'description' => 'Manage post scheduling across platforms',
            'icon' => '📱',
            'section' => 'schedule',
            'columns' => [
              ['key'=>'total_artwork','label'=>'Total artwork','type'=>'text'],
              ['key'=>'crm','label'=>'CRM','type'=>'text'],
              ['key'=>'meta_mgr','label'=>'Meta/Ads Manager','type'=>'text'],
              ['key'=>'tiktok_ig_draft','label'=>'TikTok/IG Draft','type'=>'checkbox'],
            ],
            'map' => $scheduleMap,
            'masters' => $masters,
            'year' => $year,
            'month' => $month,
          ])
        </div>

        {{-- Report --}}
        <div x-show="activeTab==='report'" x-cloak class="fade-in">
          @include('coordinators.partials._tab_table', [
            'title' => 'Report',
            'description' => 'Track reporting deliverables and completion',
            'icon' => '📊',
            'section' => 'report',
            'columns' => [
              ['key'=>'pending','label'=>'Pending','type'=>'text'],
              ['key'=>'completed','label'=>'Completed','type'=>'checkbox'],
            ],
            'map' => $reportMap,
            'masters' => $masters,
            'year' => $year,
            'month' => $month,
          ])
        </div>

        {{-- Value Add --}}
        <div x-show="activeTab==='valueadd'" x-cloak class="fade-in">
          @include('coordinators.partials._tab_table', [
            'title' => 'Value Add',
            'description' => 'Monitor additional services and quota fulfillment',
            'icon' => '💎',
            'section' => 'valueadd',
            'columns' => [
              ['key'=>'quota','label'=>'Quota','type'=>'text'],
              ['key'=>'completed','label'=>'Completed','type'=>'number'],
            ],
            'map' => $valueMap,
            'masters' => $masters,
            'year' => $year,
            'month' => $month,
          ])
        </div>
      </div>
    </div>

    {{-- Alpine component logic --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
<script>
(function(){
  const token = document.querySelector('meta[name="csrf-token"]').content;
  const url = "{{ route('coordinator.media.upsert') }}";

  function payloadFrom(el){
    const section = el.dataset.section;
    const field   = el.dataset.field;
    const master  = parseInt(el.dataset.master, 10);
    const year    = el.dataset.year ? parseInt(el.dataset.year, 10) : null;
    const month   = el.dataset.month ? parseInt(el.dataset.month, 10) : null;
    let value = (el.type === 'checkbox') ? (el.checked ? 1 : 0) : el.value;
    return { section, field, master_file_id: master, year, month, value };
  }

  async function save(el){
    const body = payloadFrom(el);
    const res = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type':'application/json',
        'X-CSRF-TOKEN': token,
        'X-Requested-With': 'XMLHttpRequest',
      },
      body: JSON.stringify(body)
    });
    // optional: tampilkan indikator sukses/gagal
  }

  const dh = (fn, ms=300)=>{ let t; return (...a)=>{clearTimeout(t); t=setTimeout(()=>fn(...a),ms)} };
  const onChange = dh(ev => save(ev.target), 250);

  document.querySelectorAll('.autosave').forEach(el=>{
    el.addEventListener('change', onChange);
    el.addEventListener('blur', onChange);
  });
})();
</script>
  </div>
</x-app-layout>
