{{-- resources/views/coordinators/partials/_tab_table.blade.php --}}
<div class="bg-white/90 backdrop-blur-sm rounded-3xl shadow-2xl border border-slate-200/60 overflow-hidden">
  {{-- Premium Header --}}
  <div class="bg-gradient-to-r from-slate-50 to-blue-50/50 px-6 py-6 border-b border-slate-200/80">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div class="flex items-center gap-3">
        @if(isset($icon))
          <div class="w-12 h-12 bg-gradient-to-br from-[#4bbbed] to-[#3da5cc] rounded-2xl flex items-center justify-center text-white text-xl shadow-lg">
            {{ $icon }}
          </div>
        @endif
        <div>
          <h3 class="text-xl font-bold text-slate-800 tracking-tight">{{ $title }}</h3>
          @if(isset($description))
            <p class="text-sm text-slate-600 mt-1 font-medium">{{ $description }}</p>
          @endif
        </div>
      </div>
      <div class="flex items-center gap-3">
        <div class="inline-flex items-center gap-2 px-4 py-2 bg-white rounded-full shadow-sm border border-slate-200/60">
          <div class="w-2 h-2 bg-gradient-to-r from-emerald-400 to-emerald-500 rounded-full"></div>
          <span class="text-sm font-semibold text-slate-700">{{ count($masters) }}</span>
          <span class="text-xs text-slate-500 font-medium">companies</span>
        </div>
      </div>
    </div>
  </div>

  {{-- Enhanced Table --}}
  @if(count($masters) > 0)
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm sticky-table">
        <thead class="bg-gradient-to-r from-slate-50 to-blue-50/50 text-slate-700 border-b-2 border-slate-200">
          <tr>
            <th class="px-4 py-4 text-left font-bold w-16 text-xs uppercase tracking-wide">No</th>
            <th class="px-4 py-4 text-left font-bold min-w-[10rem] text-xs uppercase tracking-wide">Company</th>
            <th class="px-4 py-4 text-left font-bold min-w-[8rem] hidden sm:table-cell text-xs uppercase tracking-wide">Client</th>
            <th class="px-4 py-4 text-left font-bold min-w-[10rem] text-xs uppercase tracking-wide">Package</th>
            @foreach($columns as $col)
              <th class="px-4 py-4 font-bold text-xs uppercase tracking-wide whitespace-nowrap
                @if($col['type'] === 'checkbox') text-center min-w-[7rem]
                @elseif($col['type'] === 'number') text-right min-w-[7rem]
                @else text-left min-w-[10rem] @endif
                @if(in_array($col['key'], ['meta_mgr'])) hidden lg:table-cell @endif">
                {{ $col['label'] }}
              </th>
            @endforeach
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-200/60">
          @foreach ($masters as $i => $m)
            @php
              $row = ($map[$m->id] ?? null);
              $company = $m->company ?? $m->company_name ?? '';
              $client  = $m->client ?? '';
              $pkg     = $m->product ?? '';
            @endphp
            <tr class="group hover:bg-gradient-to-r hover:from-blue-50/50 hover:to-slate-50 transition-all duration-300
              @if($i % 2 === 1) bg-slate-50/40 @endif">
              <td class="px-4 py-4">
                <div class="w-8 h-8 bg-gradient-to-br from-slate-200 to-slate-300 rounded-xl flex items-center justify-center text-xs font-bold text-slate-600 group-hover:from-blue-200 group-hover:to-blue-300 group-hover:text-blue-700 transition-all duration-300">
                  {{ $i+1 }}
                </div>
              </td>
              <td class="px-4 py-4">
                <div class="font-bold text-slate-800 text-base truncate group-hover:text-blue-800 transition-colors">{{ $company }}</div>
              </td>
              <td class="px-4 py-4 hidden sm:table-cell">
                <span class="text-slate-600 font-medium truncate">{{ $client ?: '—' }}</span>
              </td>
              <td class="px-4 py-4">
                <span class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-slate-100 to-slate-200 text-slate-700 rounded-xl text-xs font-bold border border-slate-300/60 shadow-sm">
                  {{ $pkg ?: 'No package' }}
                </span>
              </td>

              @foreach($columns as $col)
                @php
                   $key = $col['key'];
                   $type = $col['type'];
                   $value = $row ? ($row->$key ?? ($type==='checkbox' ? false : '')) : ($type==='checkbox' ? false : '');
                @endphp
                <td class="px-4 py-4
                  @if($type === 'checkbox') text-center
                  @elseif($type === 'number') text-right
                  @else text-left @endif
                  @if(in_array($key, ['meta_mgr'])) hidden lg:table-cell @endif">

                  @if($type==='checkbox')
                    <div class="flex items-center justify-center gap-2">
                      <input type="checkbox"
                             class="checkbox-premium"
                             @checked((bool)$value)
                             :disabled="!selectedMonthEnabled"
                             data-section="{{ $section }}"
                             data-field="{{ $key }}"
                             data-master="{{ $m->id }}"
                             data-year="{{ $year }}"
                             x-on:change.debounce.300ms="save($event.target)">
                      <span class="cell-status text-xs font-medium text-transparent transition-all duration-200">.</span>
                    </div>
                  @elseif($type==='number')
                    <div class="flex items-center justify-end gap-3">
                      <input type="number" step="1" min="0"
                             value="{{ $value }}"
                             class="input-premium h-10 w-24 px-3 py-2 text-right text-sm font-semibold rounded-xl shadow-sm"
                             :disabled="!selectedMonthEnabled"
                             data-section="{{ $section }}"
                             data-field="{{ $key }}"
                             data-master="{{ $m->id }}"
                             data-year="{{ $year }}"
                             x-on:blur.debounce.300ms="save($event.target)"
                             x-on:keydown.enter.prevent="save($event.target)">
                      <span class="cell-status text-xs font-medium text-transparent transition-all duration-200">.</span>
                    </div>
                  @else
                    <div class="flex items-center gap-3">
                      <input type="text"
                             value="{{ $value }}"
                             class="input-premium h-10 px-3 py-2 text-sm font-medium rounded-xl shadow-sm
                             @if(in_array($key, ['total_artwork', 'pending', 'crm'])) w-32
                             @else w-40 @endif"
                             :disabled="!selectedMonthEnabled"
                             data-section="{{ $section }}"
                             data-field="{{ $key }}"
                             data-master="{{ $m->id }}"
                             data-year="{{ $year }}"
                             x-on:blur.debounce.300ms="save($event.target)"
                             x-on:keydown.enter.prevent="save($event.target)">
                      <span class="cell-status text-xs font-medium text-transparent transition-all duration-200">.</span>
                    </div>
                  @endif
                </td>
              @endforeach
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @else
    <div class="px-6 py-16 text-center">
      <div class="mx-auto w-20 h-20 bg-gradient-to-br from-slate-200 to-slate-300 rounded-3xl flex items-center justify-center mb-6 shadow-lg">
        <svg class="w-10 h-10 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
      </div>
      <h4 class="text-xl font-bold text-slate-800 mb-3">No Data Available</h4>
      <p class="text-slate-600 max-w-md mx-auto text-sm leading-relaxed">
        No Social Media master files found for the selected filters.
        <br>Try adjusting your <span class="font-semibold text-slate-800">year and month</span> selection.
      </p>
      <div class="mt-6">
        <button class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-slate-200 to-slate-300 text-slate-700 rounded-xl font-medium text-sm hover:from-slate-300 hover:to-slate-400 transition-all duration-200">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
          </svg>
          Refresh Data
        </button>
      </div>
    </div>
  @endif
</div>
