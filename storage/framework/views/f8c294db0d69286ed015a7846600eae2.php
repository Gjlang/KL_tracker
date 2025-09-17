<?php $__env->startSection('container_class', 'w-screen max-w-none px-0'); ?>



<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-[#F7F7F9]">
  
  <div class="w-screen max-w-none px-0 py-8">

    
    <?php if(session('ok')): ?>
      <div class="mb-6 mx-6 bg-emerald-50 px-4 py-3 text-emerald-800">
        <?php echo e(session('ok')); ?>

      </div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
      <div class="mb-6 mx-6 bg-red-50 px-4 py-3 text-red-800">
        <ul class="list-disc ml-6 space-y-1">
          <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
      </div>
    <?php endif; ?>

    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8 px-6">
      <div>
        <h1 class="text-3xl font-serif text-[#1C1E26] tracking-tight">Information Hub</h1>
      </div>
      <div class="flex flex-wrap items-center gap-3">
        <a href="<?php echo e(route('information.booth.create')); ?>"
           class="inline-flex items-center gap-2 bg-[#22255b] text-white hover:opacity-90 px-6 py-2.5 text-sm font-medium transition-opacity">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
          Add Entry
        </a>

        <a href="#calendar-view"
           class="text-neutral-700 hover:bg-neutral-50 px-4 py-2.5 text-sm font-medium transition-colors">
          Calendar View
        </a>

        <a href="<?php echo e(route('dashboard')); ?>"
           class="text-neutral-600 hover:text-neutral-900 hover:bg-neutral-50 px-4 py-2.5 text-sm font-medium transition-colors">
          Back to Dashboard
        </a>
      </div>
    </div>

    
    <?php if ($__env->exists('information_booth._filters')) echo $__env->make('information_booth._filters', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <div class="space-y-12">

      
      <section class="bg-white">
        <div class="px-6 pt-6">
          <div class="flex items-center justify-between gap-3">
            <div>
              <h2 class="text-lg font-serif text-[#1C1E26] tracking-tight">Client Records</h2>
              <p class="text-xs text-neutral-500 mt-1 uppercase tracking-wider">Active Entries & Status Overview</p>
            </div>
            <div class="flex items-center gap-2">
              <button id="ib-completed-main"
                      type="button"
                      class="inline-flex items-center gap-1 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-medium text-emerald-700 hover:bg-emerald-100 active:scale-[.99]">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="currentColor">
                  <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                </svg>
                completed
              </button>
              <button id="ib-open-all"
                      type="button"
                      class="inline-flex items-center gap-1 rounded-lg border border-neutral-200 px-3 py-1.5 text-xs font-medium text-neutral-700 hover:bg-neutral-50 active:scale-[.99]">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-3.5 h-3.5" fill="currentColor"><path d="M4 6h16v2H4V6Zm0 5h16v2H4v-2Zm0 5h16v2H4v-2Z"/></svg>
                See all
              </button>
            </div>
          </div>
        </div>
        <div>
          <?php echo $__env->make('information_booth._table', ['feeds' => $feeds], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
      </section>

      
      <section id="calendar-view" class="bg-white">
        <div class="px-6 py-4">
          <h2 class="text-lg font-serif text-[#1C1E26] tracking-tight">Calendar Overview</h2>
          <p class="text-xs text-neutral-500 mt-1 uppercase tracking-wider">Schedule & Timeline View</p>
        </div>
        <div class="p-6">
          <?php if(View::exists('calendar._fullcalendar_embed')): ?>
            <?php echo $__env->make('calendar._fullcalendar_embed', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
          <?php else: ?>
            <div class="aspect-[16/9] bg-[#F7F7F9] flex items-center justify-center">
              <div class="text-center">
                <svg class="w-12 h-12 text-neutral-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-neutral-500 text-sm font-medium">Calendar Component</p>
                <p class="text-neutral-400 text-xs mt-1">Create <code>resources/views/calendar/_fullcalendar_embed.blade.php</code></p>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </section>

    </div>

    
    <div id="ib-modal"
         class="fixed inset-0 z-[70] hidden"
         aria-hidden="true">
      <!-- Backdrop -->
      <div id="ib-backdrop" class="absolute inset-0 bg-black/40"></div>

      <!-- Panel -->
      <div class="absolute inset-x-0 top-10 mx-auto w-[min(1100px,92vw)] rounded-2xl bg-white shadow-2xl border border-neutral-200/70">
        <div class="flex items-center justify-between gap-3 px-5 py-4 border-b hairline">
          <div>
            <h3 class="text-base font-serif font-semibold text-[#1C1E26]">All Records — Quick Find</h3>
            <p class="text-xs text-neutral-500 mt-0.5 uppercase tracking-wider">Type to filter by Company, Client, Product, Location, Status</p>
          </div>
          <div class="flex items-center gap-2">
            <input id="ib-search" type="text" placeholder="Search all…"
                   class="w-72 rounded-xl border-neutral-300 focus:border-[#4bbbed] focus:ring-[#4bbbed] text-sm">
            <span id="ib-count" class="text-xs text-neutral-500">0 matches</span>
            <button id="ib-completed-modal" type="button"
                    class="inline-flex items-center gap-1 rounded-lg border border-emerald-200 bg-emerald-50 px-2 py-1.5 text-xs text-emerald-700 hover:bg-emerald-100 active:scale-[.99]">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-3 h-3" fill="currentColor">
                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
              </svg>
              Completed only
            </button>
            <button id="ib-close" type="button"
                    class="inline-flex items-center rounded-lg px-2 py-1.5 text-xs text-neutral-600 hover:bg-neutral-100">
              Close ✕
            </button>
          </div>
        </div>

        <div class="max-h-[68vh] overflow-auto">
          <table id="ib-all-table" class="w-full text-sm border-collapse">
            <thead class="bg-neutral-50/60 sticky top-0 z-10">
              <tr class="border-b hairline border-neutral-200">
                <th class="px-3 py-3 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider w-[10%]">Date</th>
                <th class="px-3 py-3 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider w-[14%]">Company</th>
                <th class="px-3 py-3 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider w-[14%]">Client</th>
                <th class="px-3 py-3 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider w-[18%]">Product</th>
                <th class="px-3 py-3 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider w-[14%]">Location</th>
                <th class="px-3 py-3 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider w-[12%]">Status</th>
                <th class="px-3 py-3 text-right text-xs font-medium text-neutral-600 uppercase tracking-wider w-[8%]">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $statusMap = [
                  'pending'     => 'bg-amber-100 text-amber-800 border-amber-200',
                  'in-progress' => 'bg-[#4bbbed]/10 text-[#22255b] border-[#4bbbed]/20',
                  'completed'        => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                  'cancelled'   => 'bg-red-100 text-[#d33831] border-red-200',
                ];
              ?>
              <?php $__currentLoopData = $feeds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                  $hay = strtolower(implode(' ', array_filter([
                    optional($r->date)->format('Y-m-d'),
                    $r->company, $r->client, $r->product, $r->location, $r->status, $r->servicing
                  ])));
                ?>
                <tr data-haystack="<?php echo e($hay); ?>" data-status="<?php echo e($r->status); ?>" class="border-b hairline border-neutral-200 hover:bg-neutral-50">
                  <td class="px-3 py-3 tabular-nums"><?php echo e(optional($r->date)->format('d/m/Y') ?? '—'); ?></td>
                  <td class="px-3 py-3"><div class="truncate" title="<?php echo e($r->company ?? '—'); ?>"><?php echo e($r->company ?? '—'); ?></div></td>
                  <td class="px-3 py-3"><div class="truncate" title="<?php echo e($r->client ?? '—'); ?>"><?php echo e($r->client ?? '—'); ?></div></td>
                  <td class="px-3 py-3"><div class="truncate" title="<?php echo e($r->product ?? '—'); ?>"><?php echo e($r->product ?? '—'); ?></div></td>
                  <td class="px-3 py-3"><div class="truncate" title="<?php echo e($r->location ?? '—'); ?>"><?php echo e($r->location ?? '—'); ?></div></td>
                  <td class="px-3 py-3">
                    <?php $statusClass = $statusMap[$r->status] ?? 'bg-neutral-100 text-neutral-700 border-neutral-200'; ?>
                    <span class="inline-flex items-center rounded-full border px-2 py-1 text-xs font-medium <?php echo e($statusClass); ?>">
                      <?php echo e(ucfirst(str_replace('-', ' ', $r->status ?? '—'))); ?>

                    </span>
                  </td>
                  <td class="px-3 py-3 text-right">
                    <a href="<?php echo e(route('information.booth.edit', $r->id)); ?>" class="text-[#4bbbed] hover:text-[#22255b] text-xs font-medium">Edit</a>
                  </td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              <?php if(!count($feeds)): ?>
                <tr><td colspan="7" class="px-6 py-10 text-center text-neutral-500">No records</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    

  </div>
</div>

<?php $__env->startPush('head'); ?>
<style>
  /* Full-bleed: no horizontal scroll */
  html, body { overflow-x: hidden; }

  /* Remove ALL borders from FullCalendar */
  .fc-theme-standard td,
  .fc-theme-standard th,
  .fc .fc-scrollgrid,
  .fc .fc-scrollgrid-section > *,
  .fc .fc-scrollgrid-sync-table,
  .fc .fc-daygrid-body,
  .fc .fc-daygrid-day,
  .fc .fc-col-header,
  .fc .fc-timegrid-slot,
  .fc .fc-timegrid-axis,
  .fc .fc-timegrid-divider {
    border: 0 !important;
    box-shadow: none !important;
  }
  .fc .fc-toolbar,
  .fc .fc-view-harness,
  .fc .fc-view,
  .fc .fc-daygrid,
  .fc .fc-timegrid {
    border: 0 !important;
    box-shadow: none !important;
    background: transparent;
  }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
(function() {
  // Modal elements
  const openBtn = document.getElementById('ib-open-all');
  const modal   = document.getElementById('ib-modal');
  const backdrop= document.getElementById('ib-backdrop');
  const closeBtn= document.getElementById('ib-close');
  const input   = document.getElementById('ib-search');
  const tbody   = document.querySelector('#ib-all-table tbody');
  const rows    = tbody ? Array.from(tbody.querySelectorAll('tr[data-haystack]')) : [];
  const countEl = document.getElementById('ib-count');

  // Filter buttons
  const completedMainBtn = document.getElementById('ib-completed-main');
  const completedModalBtn = document.getElementById('ib-completed-modal');

  // Filter state
  let mainCompletedMode = false;
  let modalCompletedMode = false;

  // Modal functions
  function openModal() {
    if (!modal) return;
    modal.classList.remove('hidden');
    setTimeout(() => { input && input.focus(); }, 50);
    // Reset modal filters when opening
    modalCompletedMode = false;
    updateCompletedButtonState(completedModalBtn, modalCompletedMode);
    input && (input.value = '');
    filterModalRows('', modalCompletedMode);
    document.dispatchEvent(new CustomEvent('ib:pause-rotator', { detail: true }));
  }

  function closeModal() {
    if (!modal) return;
    modal.classList.add('hidden');
    input && (input.value = '');
    filterModalRows('', false);
    document.dispatchEvent(new CustomEvent('ib:pause-rotator', { detail: false }));
  }

  function filterModalRows(q, completedOnly = false) {
    let shown = 0;
    const v = (q || '').trim().toLowerCase();
    rows.forEach(tr => {
      const hay = tr.getAttribute('data-haystack') || '';
      const status = tr.getAttribute('data-status') || '';
      const matchesSearch = !v || hay.includes(v);
      const matchesStatus = !completedOnly || status === 'completed';
      const ok = matchesSearch && matchesStatus;
      tr.style.display = ok ? '' : 'none';
      if (ok) shown++;
    });
    if (countEl) countEl.textContent = shown + ' matches';
  }

  function updateCompletedButtonState(btn, isActive) {
    if (!btn) return;
    if (isActive) {
      btn.classList.add('bg-emerald-100', 'border-emerald-300');
      btn.classList.remove('bg-emerald-50', 'border-emerald-200');
    } else {
      btn.classList.add('bg-emerald-50', 'border-emerald-200');
      btn.classList.remove('bg-emerald-100', 'border-emerald-300');
    }
  }

  function filterMainTable(completedOnly = false) {
    // This function would work with your main table
    // You'll need to add data-status attributes to your main table rows too
    const mainTableRows = document.querySelectorAll('.main-table tr[data-status]'); // Adjust selector
    mainTableRows.forEach(tr => {
      const status = tr.getAttribute('data-status') || '';
      const ok = !completedOnly || status === 'completed';
      tr.style.display = ok ? '' : 'none';
    });
  }

  // Event listeners
  openBtn && openBtn.addEventListener('click', openModal);
  closeBtn && closeBtn.addEventListener('click', closeModal);
  backdrop && backdrop.addEventListener('click', closeModal);
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeModal(); });

  input && input.addEventListener('input', (e) => filterModalRows(e.target.value, modalCompletedMode));

  // Main table Completed filter
  if (completedMainBtn) {
    completedMainBtn.addEventListener('click', () => {
      mainCompletedMode = !mainCompletedMode;
      updateCompletedButtonState(completedMainBtn, mainCompletedMode);
      filterMainTable(mainCompletedMode);

      // Broadcast event for main table component
      document.dispatchEvent(new CustomEvent('ib:main-filter', {
        detail: { completedOnly: mainCompletedMode }
      }));
    });
  }

  // Modal Completed filter
  if (completedModalBtn) {
    completedModalBtn.addEventListener('click', () => {
      modalCompletedMode = !modalCompletedMode;
      updateCompletedButtonState(completedModalBtn, modalCompletedMode);
      filterModalRows(input ? input.value : '', modalCompletedMode);
    });
  }

  // Initialize
  filterModalRows('', modalCompletedMode);
})();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Gjlang\kl_guide_tracker\resources\views\information_booth\index.blade.php ENDPATH**/ ?>