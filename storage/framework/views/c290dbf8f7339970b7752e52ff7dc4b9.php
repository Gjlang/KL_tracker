<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50 py-6">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Social Media Coordinator List</h1>
    </div>

    <a href="<?php echo e(route('dashboard.media')); ?>"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 active:bg-gray-100 transition">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Monthly KLTG
    </a>

    
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
      <h2 class="text-lg font-semibold text-gray-900 mb-4">Filters</h2>
      <form method="GET" action="<?php echo e(route('coordinator.media.index')); ?>" class="flex flex-wrap items-end gap-4">
        <input type="hidden" name="tab" value="<?php echo e($activeTab); ?>">

        <div class="min-w-0 flex-1 sm:max-w-xs">
          <label class="block text-sm font-medium text-gray-700 mb-2">Month</label>
          <select name="month" class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
            <option value="">-- Select Month --</option>
            <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mNum => $mName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($mNum); ?>" <?php echo e((int)($month ?? 0) === (int)$mNum ? 'selected' : ''); ?>>
                <?php echo e(str_pad($mNum, 2, '0', STR_PAD_LEFT)); ?> - <?php echo e($mName); ?>

              </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>

        <div class="min-w-0 flex-1 sm:max-w-xs">
          <label class="block text-sm font-medium text-gray-700 mb-2">Year</label>
          <input type="number" name="year" value="<?php echo e($year); ?>"
                 class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                 min="2020" max="2030">
        </div>

        <div class="flex gap-2">
          <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
            </svg>
            Apply Filter
          </button>

          <?php if(request()->has('month') || request()->has('year')): ?>
            <a href="<?php echo e(route('coordinator.media.index')); ?>" class="inline-flex items-center px-4 py-2 bg-gray-600 text-black font-medium rounded-md hover:bg-gray-700 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
              Clear
            </a>
          <?php endif; ?>
        </div>
      </form>
    </div>

    
    <?php
      $tabs = [
        'content'  => ['label' => 'Content Calendar', 'icon' => 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z'],
        'editing'  => ['label' => 'Artwork Editing', 'icon' => 'M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z'],
        'schedule' => ['label' => 'Posting Scheduling', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
        'report'   => ['label' => 'Report', 'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        'valueadd' => ['label' => 'Value Add', 'icon' => 'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4'],
      ];
    ?>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
      
      <div class="border-b border-gray-200 bg-gray-50">
        <div class="flex items-center justify-between p-4">
          <div class="flex space-x-1">
            <?php $__currentLoopData = $tabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $tab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <a href="<?php echo e(route('coordinator.media.index', array_merge(request()->query(), ['tab' => $key]))); ?>"
                 class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-md transition-colors <?php echo e($activeTab === $key
                   ? 'bg-indigo-100 text-indigo-700 border-indigo-300'
                   : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'); ?>">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?php echo e($tab['icon']); ?>"></path>
                </svg>
                <?php echo e($tab['label']); ?>

              </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </div>

          
          <a href="<?php echo e(route('coordinator.media.export', array_merge(request()->query(), ['tab' => $activeTab]))); ?>"
             class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
            </svg>
            Export CSV
          </a>
        </div>
      </div>

      
      <div class="p-6">
        <?php
          $rows = $rowsByTab[$activeTab] ?? collect();
        ?>
        <?php echo $__env->make('coordinators.partials._tab_table', [
          'activeTab' => $activeTab,
          'rows'      => $rows,
          'year'      => $year,
          'month'     => $month,
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
      </div>
    </div>
  </div>
</div>


<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

<script>
  // === define first, attach to window ===
  window.mediaUpsert = async function ({section, master_file_id, year, month, field, value}) {
    try {
      const res = await fetch("<?php echo e(route('coordinator.media.upsert')); ?>", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({ section, master_file_id, year, month, field, value })
      });
      if (!res.ok) {
        console.error('Upsert HTTP not ok', res.status, await res.text());
        return false;
      }
      const data = await res.json();
      return !!data.ok;
    } catch (e) {
      console.error('Upsert error', e);
      return false;
    }
  };

  // === only attach listeners after DOM ready ===
  function attachMediaListeners() {
    // Avoid double-binding
    if (window.__mediaUpsertBound) return;
    window.__mediaUpsertBound = true;

    document.addEventListener('change', async (e) => {
      const el = e.target;
      if (!el.matches('[data-upsert]')) return;

      const payload = {
        section: el.getAttribute('data-section'),
        master_file_id: parseInt(el.getAttribute('data-master') || '0', 10),
        year: parseInt(el.getAttribute('data-year') || '0', 10),
        month: parseInt(el.getAttribute('data-month') || '0', 10),
        field: el.getAttribute('data-field'),
        value: el.value
      };

      // Guards
      if (!payload.section || !payload.master_file_id || !payload.year || !payload.month || !payload.field) {
        console.warn('Invalid upsert payload', payload);
        return;
      }
      if (typeof window.mediaUpsert !== 'function') {
        console.error('mediaUpsert missing on window');
        return;
      }

      // visual feedback
      el.disabled = true;
      const ok = await window.mediaUpsert(payload);
      el.disabled = false;

      if (ok) {
        el.classList.add('ring-2','ring-green-400');
        setTimeout(() => el.classList.remove('ring-2','ring-green-400'), 500);
      } else {
        el.classList.add('ring-2','ring-red-400');
        setTimeout(() => el.classList.remove('ring-2','ring-red-400'), 900);
        alert('Save failed. Please try again.');
      }
    }, false);
  }

  // DOM ready (handles Blade/Livewire/Turbo re-renders safely)
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', attachMediaListeners);
  } else {
    attachMediaListeners();
  }

  // (Optional) If you use Livewire/Turbo/Alpine that re-renders the DOM, re-attach here:
  document.addEventListener('livewire:navigated', attachMediaListeners);
  document.addEventListener('turbo:load', attachMediaListeners);
  document.addEventListener('alpine:init', attachMediaListeners);
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Projects\Laravel\KL_tracker\resources\views/coordinators/media.blade.php ENDPATH**/ ?>