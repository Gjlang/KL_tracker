<?php $__env->startSection('content'); ?>
<div class="flex items-center justify-between mb-4">
  <h1 class="text-lg font-semibold">Completed Whiteboards</h1>
  <a href="<?php echo e(route('outdoor.whiteboard.index')); ?>"
     class="text-xs px-3 py-1.5 rounded-full border border-neutral-300 hover:bg-neutral-100">
     Back to Active
  </a>
</div>

<?php
  // Helper to format dates consistently (mm/dd/yyyy); falls back gracefully
  $fmt = function ($d) {
      if (empty($d)) return '-';
      try { return \Illuminate\Support\Carbon::parse($d)->format('m/d/Y'); }
      catch (\Throwable $e) { return (string) $d; }
  };
  $fmtDT = function ($d) {
      if (empty($d)) return '-';
      try { return \Illuminate\Support\Carbon::parse($d)->format('Y-m-d H:i'); }
      catch (\Throwable $e) { return (string) $d; }
  };
?>

<table class="w-full text-sm border-separate border-spacing-0">
  <thead>
    <tr class="bg-neutral-50 text-neutral-700">
      <th class="px-3 py-2 text-left">No.</th>
      <th class="px-3 py-2 text-left">Created</th>
      <th class="px-3 py-2 text-left">INV Number</th>
      <th class="px-3 py-2 text-left">Purchase Order</th>
      <th class="px-3 py-2 text-left">Product</th>
      <th class="px-3 py-2 text-left">Company</th>
      <th class="px-3 py-2 text-left">Location</th>
      <th class="px-3 py-2 text-left">Installation</th>
      <th class="px-3 py-2 text-left">Dismantle</th>
      <th class="px-3 py-2 text-left">Supplier</th>
      <th class="px-3 py-2 text-left">Storage</th>
      <th class="px-3 py-2 text-left">Completed At</th>
      <th class="px-3 py-2 text-left">Actions</th>
    </tr>
  </thead>

  <tbody>
    <?php $__empty_1 = true; $__currentLoopData = $whiteboards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $wb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <tr class="border-b">
        <td class="px-3 py-2"><?php echo e($index + $whiteboards->firstItem()); ?></td>

        
        <td class="px-3 py-2">
          <?php echo e($fmt(optional($wb->created_at)->toDateTimeString() ?? $wb->created_at)); ?>

        </td>

        
        <td class="px-3 py-2"><?php echo e($wb->inv_number ?? '-'); ?></td>

        
        <td class="px-3 py-2"><?php echo e($wb->po_text ?? '-'); ?></td>

        
        <td class="px-3 py-2"><?php echo e($wb->product ?? '-'); ?></td>
        <td class="px-3 py-2"><?php echo e($wb->company ?? '-'); ?></td>
        <td class="px-3 py-2"><?php echo e($wb->location ?? '-'); ?></td>

        
        <td class="px-3 py-2"><?php echo e($fmt($wb->installation_date)); ?></td>
        <td class="px-3 py-2"><?php echo e($fmt($wb->dismantle_date)); ?></td>

        
        <td class="px-3 py-2"><?php echo e($wb->supplier_text ?? 'None'); ?></td>
        <td class="px-3 py-2"><?php echo e($wb->storage_text ?? 'None'); ?></td>

        <td class="px-3 py-2"><?php echo e($fmtDT($wb->completed_at)); ?></td>

        <td class="px-3 py-2 text-center">
  <form method="POST" action="<?php echo e(route('outdoor.whiteboard.restore')); ?>">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="outdoor_item_id" value="<?php echo e($wb->outdoor_item_id); ?>">
    <button class="text-xs px-3 py-1.5 rounded-full bg-emerald-600 text-white hover:bg-emerald-700">
      Restore to Active
    </button>
  </form>
</td>

      </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <tr>
        <td colspan="13" class="px-3 py-4 text-center text-neutral-500">
          No completed whiteboards yet.
        </td>
      </tr>
    <?php endif; ?>
  </tbody>
</table>

<div class="mt-4">
  <?php echo e($whiteboards->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Projects\Laravel\KL_tracker\resources\views\outdoor\whiteboard-completed.blade.php ENDPATH**/ ?>