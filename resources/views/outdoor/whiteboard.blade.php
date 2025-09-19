@extends('layouts.app')

@section('content')
<style>
/* Classic Elegant Design System */
.paper { background-color: #F7F7F9; }
.surface { background-color: #FFFFFF; }
.ink { color: #1C1E26; }
.muted-ink { color: #6B7280; }
.hairline { border-color: #EAEAEA; }

/* Typography Scale */
.serif-heading { @apply font-serif font-medium tracking-tight; }
.sans-body { @apply font-sans; }
.small-caps { @apply tracking-[0.06em] uppercase text-[11px] font-medium; }

/* Component System */
.floating-card { @apply bg-white rounded-2xl border border-neutral-200/70 shadow-sm; }
.primary-btn { @apply bg-[#22255b] text-white hover:bg-[#1a1e4a] focus:ring-2 focus:ring-[#4bbbed] focus:ring-offset-2 rounded-full px-4 py-2.5 text-sm font-medium transition-all duration-150 outline-none; }
.ghost-btn { @apply border border-neutral-300 text-neutral-600 hover:bg-neutral-50 focus:ring-2 focus:ring-[#4bbbed] focus:ring-offset-2 rounded-full px-4 py-2.5 text-sm font-medium transition-all duration-150 outline-none; }
.elegant-input { @apply rounded-xl border-neutral-300 focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-all duration-150 outline-none text-sm; }
.ledger-input { @apply border border-neutral-200 rounded-lg px-2.5 py-1.5 text-xs focus:ring-1 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-all duration-150 outline-none bg-white; }
.pagination-btn { @apply p-2 rounded-lg border border-neutral-200 hover:bg-neutral-50 focus:ring-2 focus:ring-[#4bbbed] focus:ring-offset-2 transition-all duration-150 outline-none disabled:opacity-50 disabled:cursor-not-allowed; }
</style>

<div class="min-h-screen paper">
  <div class="max-w-[1600px] mx-auto">

    <!-- Top Navigation Bar -->
    <header class="surface border-b hairline sticky top-0 z-20">
      <div class="px-8 py-6">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="serif-heading text-3xl ink">Project Overview</h1>
            <p class="muted-ink text-sm mt-1 sans-body">Track and manage outdoor advertising projects</p>
          </div>

          <!-- Completed button -->
          <div class="flex items-center gap-2">
            <a href="{{ route('outdoor.whiteboard.completed') }}"
               class="text-xs px-3 py-1.5 rounded-full border border-neutral-300 hover:bg-neutral-100">
               <span id="completed-badge">Completed ({{ $completedCount ?? 0 }})</span>
            </a>
          </div>
        </div>
      </div>
    </header>

    <!-- Filter Card -->
    <div class="px-8 py-6">
      <div class="floating-card p-6">
        <form method="get" class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <label class="small-caps text-neutral-600 block mb-2">Search Projects</label>
              <input type="text" name="q" value="{{ $search }}" placeholder="Company, product, location..." class="elegant-input w-full">
            </div>
            <div class="flex items-end">
              <button type="submit" class="primary-btn w-full">Apply Filters</button>
            </div>
            <a
            href="{{ route('outdoor.whiteboard.export.ledger') }}"
            class="primary-btn inline-flex items-center gap-2"
            >
            Export
            </a>
          </div>
        </form>
      </div>
    </div>

    <!-- Main Data Table -->
    <div class="px-8 py-6">
      <div class="floating-card overflow-hidden">
        <div class="surface border-b hairline px-6 py-4 flex items-center justify-between">
          <div>
            <h2 class="serif-heading text-xl ink">Projects Ledger</h2>
            <p class="muted-ink text-sm mt-1 sans-body">{{ count($masterFiles) }} projects</p>
          </div>

          <!-- Column Navigation -->
          <div class="flex items-center gap-2">
            <span class="small-caps text-neutral-600">Columns</span>
            <button type="button" class="pagination-btn" id="prevColumns">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            <span class="text-sm text-neutral-600 px-2" id="columnRange">1-12</span>
            <button type="button" class="pagination-btn" id="nextColumns">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
          </div>
        </div>

        <div class="overflow-hidden">
          <div class="overflow-x-auto" id="tableContainer">
            <table class="w-full min-w-[1600px]">
              <thead class="surface border-b hairline">
                <tr id="tableHeader"></tr>
              </thead>
              <tbody class="divide-y hairline" id="tableBody">
                @php $row = 1; @endphp
                @foreach ($masterFiles as $mf)
                    @foreach ($mf->outdoorItems as $item)
                        @php
                        // Use outdoor_item_id instead of master_file_id for lookup
                        $wb = $existing->get($item->id);

                        // Safe date formatting - handle both Carbon objects and string dates
                        $poDate = '';
                        if ($wb?->po_date) {
                            $poDate = $wb->po_date instanceof \Carbon\Carbon
                            ? $wb->po_date->format('Y-m-d')
                            : $wb->po_date;
                        }

                        $supplierDate = '';
                        if ($wb?->supplier_date) {
                            $supplierDate = $wb->supplier_date instanceof \Carbon\Carbon
                            ? $wb->supplier_date->format('Y-m-d')
                            : $wb->supplier_date;
                        }

                        $storageDate = '';
                        if ($wb?->storage_date) {
                            $storageDate = $wb->storage_date instanceof \Carbon\Carbon
                            ? $wb->storage_date->format('Y-m-d')
                            : $wb->storage_date;
                        }
                        @endphp

                        <tr class="hover:bg-neutral-50 hover:shadow-sm transition-all duration-150 group"
                        data-item="{{ $item->id }}"
                        data-master="{{ $mf->id }}"
                        data-updated="{{ optional($wb?->updated_at)->timestamp ?? 0 }}">
                        <!-- 1) No. -->
                        <td class="px-4 py-3 text-sm column-data" data-column="1">
                            <div class="ink font-medium">{{ $row }}</div>
                        </td>

                        <!-- 2) Created -->
                        <td class="px-4 py-3 text-sm column-data" data-column="2">
                            <div class="ink font-medium">{{ $mf->created_at?->format('m/d/Y') }}</div>
                        </td>

                        <!-- 3) INV number (from master_files) -->
                        <td class="px-4 py-3 text-sm column-data" data-column="3">
                            <div class="ink truncate max-w-[140px]" title="{{ $mf->invoice_number ?? $mf->inv_number }}">
                            {{ $mf->invoice_number ?? $mf->inv_number }}
                            </div>
                        </td>

                        <!-- 4) PO (stacked) -->
                        <td class="px-4 py-3 text-sm column-data" data-column="4">
                            <div class="space-y-2">
                            <input type="text" name="po_text" class="wb-field ledger-input w-36" placeholder="PO note..." value="{{ old('po_text', $wb?->po_text) }}">
                            <input type="date" name="po_date" class="wb-field ledger-input w-36" value="{{ old('po_date', $poDate) }}">
                            </div>
                        </td>

                        <!-- 5) Product (from master_files) -->
                        <td class="px-4 py-3 text-sm column-data" data-column="5">
                            <div class="ink truncate max-w-[140px]" title="{{ $mf->product }}">{{ $mf->product }}</div>
                        </td>

                        <!-- 6) Company (from master_files) -->
                        <td class="px-4 py-3 text-sm column-data" data-column="6">
                            <div class="ink font-medium truncate max-w-[180px]" title="{{ $mf->company }}">{{ $mf->company }}</div>
                        </td>

                        <!-- 7) Location (site from outdoor_items) -->
                        <td class="px-4 py-3 text-sm column-data" data-column="7">
                            <div class="ink truncate max-w-[180px]" title="{{ $item->site }}">{{ $item->site }}</div>
                        </td>

                        <!-- 8) Installation (start_date from outdoor_items) -->
                        <td class="px-4 py-3 text-sm column-data" data-column="8">
                            <div class="ink">{{ $item->start_date?->format('m/d/Y') }}</div>
                        </td>

                        <!-- 9) Dismantle (end_date from outdoor_items) -->
                        <td class="px-4 py-3 text-sm column-data" data-column="9">
                            <div class="ink">{{ $item->end_date?->format('m/d/Y') }}</div>
                        </td>

                        <!-- 10) Supplier (stacked) -->
                        <td class="px-4 py-3 text-sm column-data" data-column="10">
                            <div class="space-y-2">
                            <input type="text" name="supplier_text" class="wb-field ledger-input w-36" placeholder="Supplier note..." value="{{ old('supplier_text', $wb?->supplier_text) }}">
                            <input type="date" name="supplier_date" class="wb-field ledger-input w-36" value="{{ old('supplier_date', $supplierDate) }}">
                            </div>
                        </td>

                        <!-- 11) Storage (stacked) -->
                        <td class="px-4 py-3 text-sm column-data" data-column="11">
                            <div class="space-y-2">
                            <input type="text" name="storage_text" class="wb-field ledger-input w-36" placeholder="Storage note..." value="{{ old('storage_text', $wb?->storage_text) }}">
                            <input type="date" name="storage_date" class="wb-field ledger-input w-36" value="{{ old('storage_date', $storageDate) }}">
                            </div>
                        </td>

                        <!-- 12) Actions -->
                        <td class="px-4 py-3 text-sm column-data text-center" data-column="12">
                            <div class="space-y-2">
                            <div class="text-xs">
                                <span class="save-state text-neutral-500">Idle</span>
                            </div>
                            <button type="button"
                                class="complete-btn text-xs px-3 py-1.5 rounded-full bg-[#22255b] text-white hover:bg-[#1a1e4a]">
                                Mark Completed
                            </button>
                            </div>
                        </td>
                        </tr>
                        @php $row++; @endphp
                    @endforeach
                @endforeach
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>

    <!-- Pagination -->
    <div class="px-8 pb-6">
      @if(isset($masterFiles) && method_exists($masterFiles, 'links'))
        {{ $masterFiles->links() }}
      @endif
    </div>

  </div>
</div>

{{-- Autosave JS --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
  const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  // ===== Column pagination =====
  const COLUMNS_PER_PAGE = 12;
  let currentColumnPage = 1;

  const columnHeaders = [
    { title: 'No.', key: 'no' },
    { title: 'Created', key: 'created' },
    { title: 'INV Number', key: 'inv' },
    { title: 'Purchase Order', key: 'po' },
    { title: 'Product', key: 'product' },
    { title: 'Company', key: 'company' },
    { title: 'Location', key: 'location' },
    { title: 'Installation', key: 'installation' },
    { title: 'Dismantle', key: 'dismantle' },
    { title: 'Supplier', key: 'supplier' },
    { title: 'Storage', key: 'storage' },
    { title: 'Actions', key: 'actions' },
  ];

  function updateColumnDisplay() {
    const startCol = (currentColumnPage - 1) * COLUMNS_PER_PAGE + 1;
    const endCol = Math.min(currentColumnPage * COLUMNS_PER_PAGE, columnHeaders.length);

    const headerRow = document.getElementById('tableHeader');
    if (headerRow) {
      headerRow.innerHTML = '';
      for (let i = startCol; i <= endCol; i++) {
        const header = columnHeaders[i - 1];
        const th = document.createElement('th');
        th.className = 'px-4 py-3 text-left';
        th.innerHTML = `<span class="small-caps text-neutral-600">${header.title}</span>`;
        headerRow.appendChild(th);
      }
    }

    document.querySelectorAll('.column-data').forEach(cell => {
      const columnNum = parseInt(cell.getAttribute('data-column'));
      cell.style.display = (columnNum >= startCol && columnNum <= endCol) ? 'table-cell' : 'none';
    });

    const rangeEl = document.getElementById('columnRange');
    if (rangeEl) rangeEl.textContent = `${startCol}-${endCol}`;

    const prevBtn = document.getElementById('prevColumns');
    const nextBtn = document.getElementById('nextColumns');
    if (prevBtn) prevBtn.disabled = currentColumnPage === 1;
    if (nextBtn) nextBtn.disabled = endCol >= columnHeaders.length;
  }

  const prevBtn = document.getElementById('prevColumns');
  if (prevBtn) prevBtn.addEventListener('click', function () {
    if (currentColumnPage > 1) { currentColumnPage--; updateColumnDisplay(); }
  });

  const nextBtn = document.getElementById('nextColumns');
  if (nextBtn) nextBtn.addEventListener('click', function () {
    const maxPages = Math.ceil(columnHeaders.length / COLUMNS_PER_PAGE);
    if (currentColumnPage < maxPages) { currentColumnPage++; updateColumnDisplay(); }
  });

  updateColumnDisplay();

  // ===== Autosave =====
  const debounce = (fn, ms = 800) => {
    let t;
    return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), ms); };
  };

  function gatherRow(row) {
    const master_file_id  = row.getAttribute('data-master');
    const outdoor_item_id = row.getAttribute('data-item');

    const payload = { master_file_id, outdoor_item_id };
    row.querySelectorAll('.wb-field').forEach(el => {
      payload[el.name] = el.value || null;
    });
    console.debug('WB payload', payload);
    return payload;
  }

  async function autosave(row) {
    const stateEl = row.querySelector('.save-state');
    try {
      if (stateEl) stateEl.textContent = 'Saving...';
      const res = await fetch('{{ route('outdoor.whiteboard.upsert') }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': token,
        },
        body: JSON.stringify(gatherRow(row))
      });
      const data = await res.json();
      if (!res.ok || !data.ok) throw new Error('Autosave failed');
      if (stateEl) {
        stateEl.textContent = 'Saved';
        setTimeout(() => stateEl.textContent = 'Idle', 1200);
      }
    } catch (e) {
      console.error(e);
      if (stateEl) stateEl.textContent = 'Error';
    }
  }

  const debouncedAutosave = debounce((row) => autosave(row), 600);

  // Delegate input/change for autosave
  document.addEventListener('input', (e) => {
    const el = e.target;
    if (!el.classList?.contains('wb-field')) return;
    const row = el.closest('tr[data-item][data-master]');
    if (row) debouncedAutosave(row);
  });

  document.addEventListener('change', (e) => {
    const el = e.target;
    if (!el.classList?.contains('wb-field')) return;
    const row = el.closest('tr[data-item][data-master]');
    if (row) autosave(row);
  });

  // ===== Mark Completed (delegated) =====
  document.addEventListener('click', async (e) => {
    const btn = e.target.closest('.complete-btn');
    if (!btn) return;

    const row = btn.closest('tr[data-item][data-master]');
    if (!row) return;

    const outdoor_item_id = row.getAttribute('data-item');
    btn.disabled = true;
    const originalText = btn.textContent;
    btn.textContent = 'Completing...';

    try {
      // (Optional) persist any unsaved edits before completing
      try {
        await autosave(row);
      } catch (ignore) {
        // Even if autosave fails, still attempt to mark completed below
      }

      const res = await fetch('{{ route('outdoor.whiteboard.markCompleted') }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': token,
          'Accept': 'application/json'
        },
        body: JSON.stringify({ outdoor_item_id })
      });
      const data = await res.json();
      if (!res.ok || !data.ok) throw new Error('Complete failed');

      // Remove row from main table
      row.parentNode.removeChild(row);

      // Update completed count if element exists (e.g., "Completed (12)")
      const completedLink = document.querySelector('a[href*="completed"]');
      if (completedLink) {
        const currentText = completedLink.textContent;
        const match = currentText.match(/\((\d+)\)/);
        if (match) {
          const newCount = parseInt(match[1], 10) + 1;
          completedLink.textContent = currentText.replace(/\(\d+\)/, `(${newCount})`);
        }
      }
    } catch (err) {
      console.error(err);
      btn.disabled = false;
      btn.textContent = originalText || 'Mark Completed';
    }
  });
});
</script>

@endsection
