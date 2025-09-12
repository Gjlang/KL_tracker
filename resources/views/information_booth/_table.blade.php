@php
  $rows = $feeds ?? collect();
@endphp

{{-- Desktop Table View --}}
<div class="hidden lg:block w-full overflow-x-hidden">
  <table class="w-full table-fixed text-sm border-collapse">
    <thead class="bg-neutral-50/50">
      <tr class="border-b hairline border-neutral-200">
        <th class="w-[8%] px-3 py-4 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider font-sans">Date</th>
        <th class="w-[8%] px-3 py-4 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider font-sans">Expected Finish</th>
        <th class="w-[12%] px-3 py-4 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider font-sans">Company</th>
        <th class="w-[10%] px-3 py-4 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider font-sans">Client</th>
        <th class="w-[10%] px-3 py-4 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider font-sans">Product</th>
        <th class="w-[8%] px-3 py-4 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider font-sans">Servicing</th>
        <th class="w-[10%] px-3 py-4 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider font-sans">Location</th>
        <th class="w-[8%] px-3 py-4 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider font-sans">Status</th>
        <th class="w-[10%] px-3 py-4 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider font-sans">Attended By</th>
        <th class="w-[12%] px-3 py-4 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider font-sans">Reasons</th>
        <th class="w-[6%] px-3 py-4 text-right text-xs font-medium text-neutral-600 uppercase tracking-wider font-sans">Actions</th>
      </tr>
    </thead>
    <tbody id="ib-rows" class="bg-white divide-y hairline divide-neutral-200">
      @forelse($rows as $r)
        <tr class="transition-all duration-200 hover:bg-neutral-50 hover:shadow-sm group">
          <td class="px-3 py-4 whitespace-nowrap tabular-nums ink text-[#1C1E26]">
            {{ optional($r->date)->format('d/m/Y') ?? '—' }}
          </td>
          <td class="px-3 py-4 whitespace-nowrap tabular-nums text-neutral-700">
            {{ optional($r->expected_finish_date)->format('d/m/Y') ?? '—' }}
          </td>
          <td class="px-3 py-4 text-neutral-800 font-medium">
            <div class="truncate w-full" title="{{ $r->company ?? 'N/A' }}">
              {{ $r->company ?? '—' }}
            </div>
          </td>
          <td class="px-3 py-4 text-neutral-700">
            <div class="truncate w-full" title="{{ $r->client ?? 'N/A' }}">
              {{ $r->client ?? '—' }}
            </div>
          </td>
          <td class="px-3 py-4 text-neutral-700">
            <div class="truncate w-full" title="{{ $r->product ?? 'N/A' }}">
              {{ $r->product ?? '—' }}
            </div>
          </td>
          <td class="px-3 py-4 text-neutral-700">
            <div class="truncate w-full" title="{{ $r->servicing ?? 'N/A' }}">
              {{ $r->servicing ?? '—' }}
            </div>
          </td>
          <td class="px-3 py-4 text-neutral-700">
            <div class="truncate w-full" title="{{ $r->location ?? 'N/A' }}">
              {{ $r->location ?? '—' }}
            </div>
          </td>
          <td class="px-3 py-4 whitespace-nowrap">
            @php
              $statusMap = [
                'pending'     => 'bg-amber-100 text-amber-800 border-amber-200',
                'in-progress' => 'bg-[#4bbbed]/10 text-[#22255b] border-[#4bbbed]/20',
                'Completed'        => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                'cancelled'   => 'bg-red-100 text-[#d33831] border-red-200',
              ];
              $statusClass = $statusMap[$r->status] ?? 'bg-neutral-100 text-neutral-700 border-neutral-200';
            @endphp
            <span class="inline-flex items-center rounded-full border px-2 py-1 text-xs font-medium {{ $statusClass }}">
              {{ ucfirst(str_replace('-', ' ', $r->status)) }}
            </span>
          </td>
          <td class="px-3 py-4 text-neutral-700">
            <div class="truncate w-full" title="{{ $r->attended_by ?? 'N/A' }}">
              {{ $r->attended_by ?? '—' }}
            </div>
          </td>
          <td class="px-3 py-4 text-neutral-600 text-xs">
            <div class="truncate w-full" title="{{ $r->reasons ?? 'N/A' }}">
              {{ $r->reasons ?? '—' }}
            </div>
          </td>
          <td class="px-3 py-4 whitespace-nowrap text-right text-sm">
            <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity duration-150">
              @if(isset($r->id))
                <a href="{{ route('information.booth.edit', $r->id) }}"
                   class="text-[#4bbbed] hover:text-[#22255b] transition-colors duration-150"
                   title="Edit">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                  </svg>
                </a>
                <form method="POST" action="{{ route('information.booth.destroy', $r->id) }}" class="inline">
                  @csrf
                  @method('DELETE')
                  <button type="submit"
                          onclick="return confirmDelete('Are you sure you want to delete this entry?')"
                          class="text-[#d33831] hover:text-red-700 transition-colors duration-150"
                          title="Delete">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                  </button>
                </form>
              @endif
            </div>
          </td>
        </tr>
      @empty
        <tr class="empty-state">
          <td colspan="11" class="px-6 py-12 text-center">
            <div class="flex flex-col items-center">
              <svg class="w-12 h-12 text-neutral-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
              <p class="text-neutral-500 font-medium mb-1">No records found</p>
              <p class="text-neutral-400 text-sm mb-4">Get started by adding your first entry</p>
              <a href="{{ route('information.booth.create') }}"
                 class="inline-flex items-center gap-2 text-[#4bbbed] hover:text-[#22255b] text-sm font-medium transition-colors duration-150">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Entry
              </a>
            </div>
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>

{{-- Mobile Card View --}}
<div class="lg:hidden space-y-4 px-4 py-2">
  @forelse($rows as $r)
    <div class="bg-white border hairline border-neutral-200 rounded-xl p-4 shadow-sm">
      <div class="flex items-start justify-between mb-3">
        <div class="flex-1 min-w-0 mr-3">
          <p class="font-medium text-[#1C1E26] ink truncate">{{ $r->company ?? 'Unknown Company' }}</p>
          <p class="text-sm text-neutral-600 truncate">{{ $r->client ?? 'No client specified' }}</p>
        </div>
        <div class="flex-shrink-0">
          @php
            $statusMap = [
              'pending'     => 'bg-amber-100 text-amber-800 border-amber-200',
              'in-progress' => 'bg-[#4bbbed]/10 text-[#22255b] border-[#4bbbed]/20',
              'completed'        => 'bg-emerald-100 text-emerald-800 border-emerald-200',
              'cancelled'   => 'bg-red-100 text-[#d33831] border-red-200',
            ];
            $statusClass = $statusMap[$r->status] ?? 'bg-neutral-100 text-neutral-700 border-neutral-200';
          @endphp
          <span class="inline-flex items-center rounded-full border px-2 py-1 text-xs font-medium {{ $statusClass }}">
            {{ ucfirst(str_replace('-', ' ', $r->status)) }}
          </span>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-3 text-sm mb-3">
        <div>
          <p class="text-xs text-neutral-500 uppercase tracking-wide">Date</p>
          <p class="text-neutral-800 tabular-nums">{{ optional($r->date)->format('d/m/Y') ?? '—' }}</p>
        </div>
        <div>
          <p class="text-xs text-neutral-500 uppercase tracking-wide">Expected Finish</p>
          <p class="text-neutral-800 tabular-nums">{{ optional($r->expected_finish_date)->format('d/m/Y') ?? '—' }}</p>
        </div>
        @if($r->product)
        <div>
          <p class="text-xs text-neutral-500 uppercase tracking-wide">Product</p>
          <p class="text-neutral-700 truncate">{{ $r->product }}</p>
        </div>
        @endif
        @if($r->location)
        <div>
          <p class="text-xs text-neutral-500 uppercase tracking-wide">Location</p>
          <p class="text-neutral-700 truncate">{{ $r->location }}</p>
        </div>
        @endif
      </div>

      @if($r->attended_by || $r->reasons)
      <div class="pt-3 border-t hairline border-neutral-200">
        @if($r->attended_by)
          <p class="text-xs text-neutral-500 mb-1">Attended by: <span class="text-neutral-700">{{ $r->attended_by }}</span></p>
        @endif
        @if($r->reasons)
          <p class="text-xs text-neutral-500">Notes: <span class="text-neutral-700">{{ \Illuminate\Support\Str::limit($r->reasons, 50) }}</span></p>
        @endif
      </div>
      @endif

      @if(isset($r->id))
      <div class="flex items-center justify-end gap-3 mt-4 pt-3 border-t hairline border-neutral-200">
        <a href="{{ route('information.booth.edit', $r->id) }}"
           class="text-[#4bbbed] hover:text-[#22255b] transition-colors duration-150 text-sm">
          Edit
        </a>
        <form method="POST" action="{{ route('information.booth.destroy', $r->id) }}" class="inline">
          @csrf
          @method('DELETE')
          <button type="submit"
                  onclick="return confirmDelete('Are you sure you want to delete this entry?')"
                  class="text-[#d33831] hover:text-red-700 transition-colors duration-150 text-sm">
            Delete
          </button>
        </form>
      </div>
      @endif
    </div>
  @empty
    <div class="text-center py-12">
      <svg class="w-12 h-12 text-neutral-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
      </svg>
      <p class="text-neutral-500 font-medium mb-1">No records found</p>
      <p class="text-neutral-400 text-sm mb-4">Get started by adding your first entry</p>
      <a href="{{ route('information.booth.create') }}"
         class="inline-flex items-center gap-2 text-[#4bbbed] hover:text-[#22255b] text-sm font-medium transition-colors duration-150">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Add Entry
      </a>
    </div>
  @endforelse
</div>

{{-- Pagination --}}
@if(method_exists($rows, 'links') && $rows->hasPages())
  <div class="px-6 py-4 bg-neutral-50/50 border-t hairline border-neutral-200">
    <div class="flex items-center justify-between">
      <div class="text-sm text-neutral-600">
        Showing {{ $rows->firstItem() ?? 0 }} to {{ $rows->lastItem() ?? 0 }} of {{ $rows->total() ?? 0 }} results
      </div>
      <div class="pagination-wrapper">
        {{ $rows->links() }}
      </div>
    </div>
  </div>
@endif

{{-- Auto-Rotation Script --}}
@push('scripts')
<style>
  /* Prevent horizontal scroll */
  html, body {
    overflow-x: hidden;
  }

  /* Full bleed utility for this component */
  .bleed-x {
    margin-left: calc(50% - 50vw);
    margin-right: calc(50% - 50vw);
    width: 100vw;
  }
</style>
<script>
// Confirm delete function
window.confirmDelete = function(message = 'Are you sure you want to delete this item?') {
  return confirm(message);
};
// Confirm delete function - needed by delete buttons
window.confirmDelete = function(message = 'Are you sure you want to delete this item?') {
  return confirm(message);
};

// Auto-rotation script only if more than PAGE_SIZE rows
(function() {
  const PAGE_SIZE = 7;
  const INTERVAL_MS = 5000;
  const FADE_MS = 250;

  const tbody = document.getElementById('ib-rows');
  if (!tbody) return;

  const allRows = Array.from(tbody.querySelectorAll('tr')).filter(tr => !tr.classList.contains('empty-state'));
  if (allRows.length <= PAGE_SIZE) return;

  function chunk(arr, size) {
    const out = [];
    for (let i = 0; i < arr.length; i += size) out.push(arr.slice(i, i + size));
    return out;
  }

  const pages = chunk(allRows, PAGE_SIZE);
  let pageIndex = 0;

  tbody.style.transition = `opacity ${FADE_MS}ms ease`;
  tbody.style.willChange = 'opacity';

  function renderPage(idx) {
    tbody.style.opacity = '0';
    setTimeout(() => {
      while (tbody.firstChild) tbody.removeChild(tbody.firstChild);
      pages[idx].forEach(tr => {
        tr.style.display = '';
        tbody.appendChild(tr);
      });
      tbody.style.opacity = '1';
    }, FADE_MS);
  }

  allRows.forEach(tr => tr.style.display = 'none');
  renderPage(pageIndex);

  let timer = setInterval(() => {
    pageIndex = (pageIndex + 1) % pages.length;
    renderPage(pageIndex);
  }, INTERVAL_MS);

  const tableCard = tbody.closest('.hidden.lg\\:block') || tbody.closest('div');
  if (tableCard) {
    tableCard.addEventListener('mouseenter', () => clearInterval(timer));
    tableCard.addEventListener('mouseleave', () => {
      clearInterval(timer);
      timer = setInterval(() => {
        pageIndex = (pageIndex + 1) % pages.length;
        renderPage(pageIndex);
      }, INTERVAL_MS);
    });
  }
})();
</script>
@endpush
