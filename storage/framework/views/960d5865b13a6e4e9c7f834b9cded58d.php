<?php $__env->startSection('content'); ?>
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
              <input type="text" name="q" value="<?php echo e($search); ?>" placeholder="Company, product, location..." class="elegant-input w-full">
            </div>
            <div class="flex items-end">
              <button type="submit" class="primary-btn w-full">Apply Filters</button>
            </div>

            <a href="<?php echo e(route('outdoor.whiteboard.export.byProduct', request()->only('q'))); ?>"
   class="primary-btn inline-flex items-center gap-2">
  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
          d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V4"/>
  </svg>
  Export (Grouped by Product)
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
                <?php $row = 1; ?>
                <?php $__currentLoopData = $masterFiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mf): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <?php $__currentLoopData = $mf->outdoorItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $wb = $existing[$mf->id] ?? null; ?>

                    <tr class="hover:bg-neutral-50 hover:shadow-sm transition-all duration-150 group" data-row-id="<?php echo e($mf->id); ?>">
                      <form action="<?php echo e(route('outdoor.whiteboard.upsert')); ?>" method="post" class="contents">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="master_file_id" value="<?php echo e($mf->id); ?>">

                        <!-- 1) No. -->
                        <td class="px-4 py-3 text-sm column-data" data-column="1">
                          <div class="ink font-medium"><?php echo e($row); ?></div>
                        </td>

                        <!-- 2) Created -->
                        <td class="px-4 py-3 text-sm column-data" data-column="2">
                          <div class="ink font-medium"><?php echo e($mf->created_at?->format('m/d/Y')); ?></div>
                        </td>

                        <!-- 3) INV number (from master_files) -->
                        <td class="px-4 py-3 text-sm column-data" data-column="3">
                          <div class="ink truncate max-w-[140px]" title="<?php echo e($mf->invoice_number ?? $mf->inv_number); ?>">
                            <?php echo e($mf->invoice_number ?? $mf->inv_number); ?>

                          </div>
                        </td>

                        <!-- 4) PO (stacked) -->
                        <td class="px-4 py-3 text-sm column-data" data-column="4">
                          <div class="space-y-2">
                            <input type="text" name="po_text" class="ledger-input w-36" placeholder="PO note..." value="<?php echo e(old('po_text', $wb?->po_text)); ?>">
                            <input type="date" name="po_date" class="ledger-input w-36" value="<?php echo e(old('po_date', $wb?->po_date?->format('Y-m-d'))); ?>">
                          </div>
                        </td>

                        <!-- 5) Product (from master_files) -->
                        <td class="px-4 py-3 text-sm column-data" data-column="5">
                          <div class="ink truncate max-w-[140px]" title="<?php echo e($mf->product); ?>"><?php echo e($mf->product); ?></div>
                        </td>

                        <!-- 6) Company (from master_files) -->
                        <td class="px-4 py-3 text-sm column-data" data-column="6">
                          <div class="ink font-medium truncate max-w-[180px]" title="<?php echo e($mf->company); ?>"><?php echo e($mf->company); ?></div>
                        </td>

                        <!-- 7) Location (site from outdoor_items) -->
                        <td class="px-4 py-3 text-sm column-data" data-column="7">
                          <div class="ink truncate max-w-[180px]" title="<?php echo e($item->site); ?>"><?php echo e($item->site); ?></div>
                        </td>

                        <!-- 8) Installation (start_date from outdoor_items) -->
                        <td class="px-4 py-3 text-sm column-data" data-column="8">
                          <div class="ink"><?php echo e($item->start_date?->format('m/d/Y')); ?></div>
                        </td>

                        <!-- 9) Dismantle (end_date from outdoor_items) -->
                        <td class="px-4 py-3 text-sm column-data" data-column="9">
                          <div class="ink"><?php echo e($item->end_date?->format('m/d/Y')); ?></div>
                        </td>

                        <!-- 10) Supplier (stacked) -->
                        <td class="px-4 py-3 text-sm column-data" data-column="10">
                          <div class="space-y-2">
                            <input type="text" name="supplier_text" class="ledger-input w-36" placeholder="Supplier note..." value="<?php echo e(old('supplier_text', $wb?->supplier_text)); ?>">
                            <input type="date" name="supplier_date" class="ledger-input w-36" value="<?php echo e(old('supplier_date', $wb?->supplier_date?->format('Y-m-d'))); ?>">
                          </div>
                        </td>

                        <!-- 11) Storage (stacked) -->
                        <td class="px-4 py-3 text-sm column-data" data-column="11">
                          <div class="space-y-2">
                            <input type="text" name="storage_text" class="ledger-input w-36" placeholder="Storage note..." value="<?php echo e(old('storage_text', $wb?->storage_text)); ?>">
                            <input type="date" name="storage_date" class="ledger-input w-36" value="<?php echo e(old('storage_date', $wb?->storage_date?->format('Y-m-d'))); ?>">
                          </div>
                        </td>

                        <!-- 12) Actions -->
                        <td class="px-4 py-3 text-sm column-data text-center" data-column="12">
                          <button type="submit" class="primary-btn text-xs px-3 py-1.5 opacity-0 group-hover:opacity-100 transition-opacity duration-150">Save</button>
                        </td>
                      </form>
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

  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Column pagination system
  const COLUMNS_PER_PAGE = 12; // total columns shown per page
  let currentColumnPage = 1;

  // Header titles in the EXACT order we render cells:
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

    // Update header
    const headerRow = document.getElementById('tableHeader');
    headerRow.innerHTML = '';
    for (let i = startCol; i <= endCol; i++) {
      const header = columnHeaders[i - 1];
      const th = document.createElement('th');
      th.className = 'px-4 py-3 text-left';
      th.innerHTML = `<span class="small-caps text-neutral-600">${header.title}</span>`;
      headerRow.appendChild(th);
    }

    // Show/hide body cells
    document.querySelectorAll('.column-data').forEach(cell => {
      const columnNum = parseInt(cell.getAttribute('data-column'));
      cell.style.display = (columnNum >= startCol && columnNum <= endCol) ? 'table-cell' : 'none';
    });

    // Update pager state
    document.getElementById('columnRange').textContent = `${startCol}-${endCol}`;
    document.getElementById('prevColumns').disabled = currentColumnPage === 1;
    document.getElementById('nextColumns').disabled = endCol >= columnHeaders.length;
  }

  // Column navigation
  document.getElementById('prevColumns').addEventListener('click', function() {
    if (currentColumnPage > 1) { currentColumnPage--; updateColumnDisplay(); }
  });
  document.getElementById('nextColumns').addEventListener('click', function() {
    const maxPages = Math.ceil(columnHeaders.length / COLUMNS_PER_PAGE);
    if (currentColumnPage < maxPages) { currentColumnPage++; updateColumnDisplay(); }
  });

  // Initialize
  updateColumnDisplay();
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Gjlang\kl_guide_tracker\resources\views\outdoor\whiteboard.blade.php ENDPATH**/ ?>