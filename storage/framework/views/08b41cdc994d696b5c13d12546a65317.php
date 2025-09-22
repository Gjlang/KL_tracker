<?php $__env->startSection('head'); ?>
<!-- Google Fonts - Keep these link tags only -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen paper">
  <div class="max-w-[1600px] mx-auto">

    <!-- Top Navigation Bar -->
    <header class="surface border-b hairline">
      <div class="px-8 py-6">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="serif-heading text-3xl ink">Project Overview</h1>
            <p class="muted-ink text-sm mt-1 sans-body">Track and manage outdoor advertising projects</p>
          </div>

          <!-- Completed button -->
          <div class="flex items-center gap-2">
            <a href="<?php echo e(route('outdoor.whiteboard.completed')); ?>"
               class="text-xs px-3 py-1.5 rounded-full border border-neutral-300 hover:bg-neutral-100">
               <span id="completed-badge">Completed (<?php echo e($completedCount ?? 0); ?>)</span>
            </a>
          </div>
        </div>
      </div>
    </header>

    <!-- Filter Card -->
    <div class="px-8 py-6">
      <div class="floating-card p-6">
        <form method="get" class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            
            <div class="md:col-span-2">
              <label class="small-caps text-neutral-600 block mb-2">Search Projects</label>
              <input
                type="text"
                name="q"
                value="<?php echo e($search); ?>"
                placeholder="Company, product, location..."
                class="elegant-input w-full"
              >
            </div>

            
            <div>
              <label class="small-caps text-neutral-600 block mb-2">Sub Product</label>
              <select name="sub" class="elegant-input w-full">
                <?php
                  $subOptions = ['' => 'All', 'BB' => 'BB', 'TB' => 'TB', 'Newspaper' => 'Newspaper', 'Bunting' => 'Bunting', 'Flyers' => 'Flyers', 'Star' => 'Star', 'Signages' => 'Signages'];
                ?>
                <?php $__currentLoopData = $subOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($val); ?>" <?php if(($sub ?? '') === $val): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>

            
            <div class="flex items-end">
              <button type="submit" class="primary-btn w-full">Apply Filters</button>
            </div>

            
            <div class="flex items-end">
              <a href="<?php echo e(route('outdoor.whiteboard.index')); ?>" class="secondary-btn w-full text-center">Clear</a>
            </div>
          </div>

          
          <div class="flex items-center gap-3 pt-2">
            <a
              href="<?php echo e(route('outdoor.whiteboard.export.ledger', ['q' => $search, 'sub' => $sub])); ?>"
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
            <p class="muted-ink text-sm mt-1 sans-body"><?php echo e(count($masterFiles)); ?> projects</p>
          </div>

          <!-- Navigation Controls -->
          <div class="flex items-center gap-6">

            <!-- Row Navigation -->
            <div class="flex items-center gap-2">
              <span class="small-caps text-neutral-600">Rows</span>
              <button type="button" class="pagination-btn" id="prevRows">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
              </button>
              <span class="text-sm text-neutral-600 px-2" id="rowRange">1–15</span>
              <button type="button" class="pagination-btn" id="nextRows">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
              </button>
            </div>
          </div>
        </div>

        <div class="overflow-hidden">
          <div class="overflow-x-auto" id="tableContainer">
            <table class="w-full min-w-[1600px]">
              <thead class="surface border-b hairline">
                <tr id="tableHeader"></tr>
              </thead>
              <tbody class="divide-y hairline" id="tableBody">
  <?php $row = 1; ?>
  <?php $__currentLoopData = $masterFiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mf): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php $__currentLoopData = $mf->outdoorItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <?php
        $wb = $existing->get($item->id);

        $poDate = '';
        if ($wb?->po_date) {
            $poDate = $wb->po_date instanceof \Carbon\Carbon ? $wb->po_date->format('Y-m-d') : $wb->po_date;
        }

        $supplierDate = '';
        if ($wb?->supplier_date) {
            $supplierDate = $wb->supplier_date instanceof \Carbon\Carbon ? $wb->supplier_date->format('Y-m-d') : $wb->supplier_date;
        }

        $storageDate = '';
        if ($wb?->storage_date) {
            $storageDate = $wb->storage_date instanceof \Carbon\Carbon ? $wb->storage_date->format('Y-m-d') : $wb->storage_date;
        }
      ?>

      <tr class="hover:bg-neutral-50 hover:shadow-sm transition-all duration-150 group"
          data-item="<?php echo e($item->id); ?>"
          data-master="<?php echo e($mf->id); ?>"
          data-updated="<?php echo e(optional($wb?->updated_at)->timestamp ?? 0); ?>">

        <!-- 1) No. -->
        <td class="px-4 py-3 text-sm column-data" data-column="1">
          <div class="ink font-medium"><?php echo e($row); ?></div>
        </td>

        <!-- 2) Created -->
        <td class="px-4 py-3 text-sm column-data" data-column="2">
          <div class="ink font-medium"><?php echo e($mf->created_at?->format('m/d/Y')); ?></div>
        </td>

        <!-- 3) INV number -->
        <td class="px-4 py-3 text-sm column-data" data-column="3">
          <div class="ink truncate max-w-[140px]" title="<?php echo e($mf->invoice_number ?? $mf->inv_number); ?>">
            <?php echo e($mf->invoice_number ?? $mf->inv_number); ?>

          </div>
        </td>

        <!-- 4) Purchase Order -->
        <td class="px-4 py-3 text-sm column-data" data-column="4">
          <div class="space-y-2">
            <input type="text" name="po_text" class="wb-field ledger-input w-36" placeholder="PO note..." value="<?php echo e(old('po_text', $wb?->po_text)); ?>">
            <input type="date" name="po_date" class="wb-field ledger-input w-36" value="<?php echo e(old('po_date', $poDate)); ?>">
          </div>
        </td>

        <!-- 5) Company -->
        <td class="px-4 py-3 text-sm column-data" data-column="5">
          <div class="ink font-medium truncate max-w-[180px]" title="<?php echo e($mf->company); ?>"><?php echo e($mf->company); ?></div>
        </td>

        <!-- 6) Product -->
        <td class="px-4 py-3 text-sm column-data" data-column="6">
          <div class="ink truncate max-w-[140px]" title="<?php echo e($mf->product); ?>"><?php echo e($mf->product); ?></div>
        </td>

        <!-- 7) Location -->
        <td class="px-4 py-3 text-sm column-data" data-column="7">
          <div class="ink truncate max-w-[180px]" title="<?php echo e($item->site); ?>"><?php echo e($item->site); ?></div>
        </td>

        <!-- 8) Duration (from master_files) -->
        <td class="px-4 py-3 text-sm column-data" data-column="8">
          <div class="ink">
            <?php if(!empty($mf->duration_text)): ?>
              <?php echo e($mf->duration_text); ?>

            <?php elseif(!empty($mf->duration)): ?>
              <?php echo e(is_numeric($mf->duration) ? ($mf->duration.' days') : $mf->duration); ?>

            <?php elseif(!empty($mf->date) && !empty($mf->date_finish)): ?>
              <?php
                try {
                  $s = \Carbon\Carbon::parse($mf->date);
                  $e = \Carbon\Carbon::parse($mf->date_finish);
                  $dur = $e->greaterThanOrEqualTo($s) ? ($s->diffInDays($e)+1).' days' : '—';
                } catch (\Throwable $th) { $dur = '—'; }
              ?>
              <?php echo e($dur); ?>

            <?php else: ?>
              —
            <?php endif; ?>
          </div>
        </td>

        <!-- 9) Installation -->
        <td class="px-4 py-3 text-sm column-data" data-column="9">
          <div class="ink"><?php echo e($item->start_date?->format('m/d/Y')); ?></div>
        </td>

        <!-- 10) Dismantle -->
        <td class="px-4 py-3 text-sm column-data" data-column="10">
          <div class="ink"><?php echo e($item->end_date?->format('m/d/Y')); ?></div>
        </td>

        <!-- 11) Supplier -->
        <td class="px-4 py-3 text-sm column-data" data-column="11">
          <div class="space-y-2">
            <input type="text" name="supplier_text" class="wb-field ledger-input w-36" placeholder="Supplier note..." value="<?php echo e(old('supplier_text', $wb?->supplier_text)); ?>">
            <input type="date" name="supplier_date" class="wb-field ledger-input w-36" value="<?php echo e(old('supplier_date', $supplierDate)); ?>">
          </div>
        </td>

        <!-- 12) Storage -->
        <td class="px-4 py-3 text-sm column-data" data-column="12">
          <div class="space-y-2">
            <input type="text" name="storage_text" class="wb-field ledger-input w-36" placeholder="Storage note..." value="<?php echo e(old('storage_text', $wb?->storage_text)); ?>">
            <input type="date" name="storage_date" class="wb-field ledger-input w-36" value="<?php echo e(old('storage_date', $storageDate)); ?>">
          </div>
        </td>

        <!-- 13) Actions -->
        <td class="px-4 py-3 text-sm column-data text-center" data-column="13">
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
      <?php $row++; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</tbody>

            </table>
          </div>
        </div>

      </div>
    </div>

    <!-- Pagination -->
    <div class="px-8 pb-6">
      <?php if(isset($masterFiles) && method_exists($masterFiles, 'links')): ?>
        <?php echo e($masterFiles->links()); ?>

      <?php endif; ?>
    </div>

  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  // ===== Column pagination =====
  const COLUMNS_PER_PAGE = 13;
  let currentColumnPage = 1;

  const columnHeaders = [
    { title: 'No.', key: 'no' },
    { title: 'Created', key: 'created' },
    { title: 'INV Number', key: 'inv' },
    { title: 'Purchase Order', key: 'po' },
    { title: 'Company', key: 'company' },
    { title: 'Product', key: 'product' },
    { title: 'Location', key: 'location' },
    { title: 'Duration', key: 'duration' },
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

  // Column navigation event listeners
  const prevColBtn = document.getElementById('prevColumns');
  if (prevColBtn) prevColBtn.addEventListener('click', function () {
    if (currentColumnPage > 1) { currentColumnPage--; updateColumnDisplay(); }
  });

  const nextColBtn = document.getElementById('nextColumns');
  if (nextColBtn) nextColBtn.addEventListener('click', function () {
    const maxPages = Math.ceil(columnHeaders.length / COLUMNS_PER_PAGE);
    if (currentColumnPage < maxPages) { currentColumnPage++; updateColumnDisplay(); }
  });

  // --- Row pagination ---
  const ROWS_PER_PAGE = 15;
  let currentRowPage = 1;

  function getRows() {
    return Array.from(document.querySelectorAll('#tableBody > tr'));
  }

  function renumberAll() {
    const rows = getRows();
    rows.forEach((tr, idx) => {
      const noTd = tr.querySelector('td[data-column="1"]');
      if (!noTd) return;
      const ink = noTd.querySelector('.ink');
      if (ink) ink.textContent = idx + 1;
      else noTd.textContent = idx + 1;
    });
  }

  function updateRowDisplay() {
    const rows = getRows();
    const total = rows.length;
    const maxPages = Math.max(1, Math.ceil(total / ROWS_PER_PAGE));
    if (currentRowPage > maxPages) currentRowPage = maxPages;

    const startIdx = (currentRowPage - 1) * ROWS_PER_PAGE;
    const endIdx = Math.min(startIdx + ROWS_PER_PAGE, total);

    rows.forEach((tr, i) => {
      tr.style.display = (i >= startIdx && i < endIdx) ? '' : 'none';
    });

    const range = document.getElementById('rowRange');
    if (range) range.textContent = `${total ? startIdx + 1 : 0}–${endIdx} of ${total}`;

    const prev = document.getElementById('prevRows');
    const next = document.getElementById('nextRows');
    if (prev) prev.disabled = currentRowPage === 1;
    if (next) next.disabled = currentRowPage >= maxPages;

    renumberAll();
  }

  // Row navigation event listeners
  document.getElementById('prevRows')?.addEventListener('click', () => {
    if (currentRowPage > 1) { currentRowPage--; updateRowDisplay(); }
  });
  document.getElementById('nextRows')?.addEventListener('click', () => {
    const maxPages = Math.max(1, Math.ceil(getRows().length / ROWS_PER_PAGE));
    if (currentRowPage < maxPages) { currentRowPage++; updateRowDisplay(); }
  });

  // Initialize both pagers
  updateColumnDisplay();
  updateRowDisplay();

  // ===== Autosave functionality =====
  const debounce = (fn, ms = 800) => {
    let t;
    return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), ms); };
  };

  function gatherRow(row) {
    const master_file_id = row.getAttribute('data-master');
    const outdoor_item_id = row.getAttribute('data-item');

    const payload = { master_file_id, outdoor_item_id };
    row.querySelectorAll('.wb-field').forEach(el => {
      payload[el.name] = el.value || null;
    });
    return payload;
  }

  async function autosave(row) {
    const stateEl = row.querySelector('.save-state');
    try {
      if (stateEl) stateEl.textContent = 'Saving...';
      const res = await fetch('<?php echo e(route('outdoor.whiteboard.upsert')); ?>', {
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

  // Autosave event listeners
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

  // ===== Mark Completed functionality =====
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
      try {
        await autosave(row);
      } catch (ignore) {
        // Continue even if autosave fails
      }

      const res = await fetch('<?php echo e(route('outdoor.whiteboard.markCompleted')); ?>', {
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

      // Remove row and update display
      row.parentNode.removeChild(row);
      updateRowDisplay();

      // Update completed count
      const completedBadge = document.getElementById('completed-badge');
      if (completedBadge) {
        const currentText = completedBadge.textContent;
        const match = currentText.match(/\((\d+)\)/);
        if (match) {
          const newCount = parseInt(match[1], 10) + 1;
          completedBadge.textContent = currentText.replace(/\(\d+\)/, `(${newCount})`);
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

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Gjlang\kl_guide_tracker\resources\views/outdoor/whiteboard.blade.php ENDPATH**/ ?>