<?php $__env->startSection('content'); ?>
<div class="flex items-center justify-between mb-4">
  <h1 class="text-lg font-semibold">Completed Whiteboards</h1>
  <a href="<?php echo e(route('outdoor.whiteboard.index')); ?>"
     class="text-xs px-3 py-1.5 rounded-full border border-neutral-300 hover:bg-neutral-100">
     Back to Active
  </a>
</div>

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
        <td class="px-3 py-2"><?php echo e(optional($wb->created_at)->format('Y-m-d')); ?></td>
        <td class="px-3 py-2"><?php echo e($wb->inv_number ?? '-'); ?></td>
        <td class="px-3 py-2"><?php echo e($wb->purchase_order ?? '-'); ?></td>
        <td class="px-3 py-2"><?php echo e($wb->product ?? '-'); ?></td>
        <td class="px-3 py-2"><?php echo e($wb->company ?? '-'); ?></td>
        <td class="px-3 py-2"><?php echo e($wb->location ?? '-'); ?></td>
        <td class="px-3 py-2"><?php echo e($wb->installation ?? '-'); ?></td>
        <td class="px-3 py-2"><?php echo e($wb->dismantle ?? '-'); ?></td>
        <td class="px-3 py-2"><?php echo e($wb->supplier_text ?? '-'); ?></td>
        <td class="px-3 py-2"><?php echo e($wb->storage_text ?? '-'); ?></td>
        <td class="px-3 py-2"><?php echo e(optional($wb->completed_at)->format('Y-m-d H:i')); ?></td>

        <td class="px-3 py-2 text-center">
          
          <form method="POST" action="<?php echo e(route('outdoor.whiteboard.destroy', $wb->id)); ?>"
                onsubmit="return confirm('Delete this completed record?')">
            <?php echo csrf_field(); ?>
            <?php echo method_field('DELETE'); ?>
            <button class="text-xs px-3 py-1.5 rounded-full bg-red-600 text-white hover:bg-red-700">
              Delete
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Gjlang\kl_guide_tracker\resources\views/outdoor/whiteboard-completed.blade.php ENDPATH**/ ?>