<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto p-6">
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">OUTDOOR Whiteboard</h1>

    <form method="get" class="flex gap-2">
      <input type="text" name="q" value="<?php echo e($search); ?>" placeholder="Search company/location/product"
             class="border rounded px-3 py-2 w-64">
      <button class="px-4 py-2 rounded bg-gray-800 text-white">Search</button>
    </form>
  </div>

  <?php if(session('status')): ?>
    <div class="mb-4 px-4 py-2 bg-green-50 border border-green-200 rounded text-green-800">
      <?php echo e(session('status')); ?>

    </div>
  <?php endif; ?>

  <div class="overflow-x-auto border rounded">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-3 py-2 text-left">Date (created)</th>
          <th class="px-3 py-2 text-left">PO (text)</th>
          <th class="px-3 py-2 text-left">PO (date)</th>

          <th class="px-3 py-2 text-left">Client (text)</th>
          <th class="px-3 py-2 text-left">Client (date)</th>

          <th class="px-3 py-2 text-left">Location</th>
          <th class="px-3 py-2 text-left">Start date</th>
          <th class="px-3 py-2 text-left">End date</th>

          <th class="px-3 py-2 text-left">Supplier (text)</th>
          <th class="px-3 py-2 text-left">Supplier (date)</th>

          <th class="px-3 py-2 text-left">Storage (text)</th>
          <th class="px-3 py-2 text-left">Storage (date)</th>

          <th class="px-3 py-2 text-left">Notes</th>
          <th class="px-3 py-2">Save</th>
        </tr>
      </thead>
      <tbody>
        <?php $__currentLoopData = $masterFiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mf): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php
            $wb = $existing[$mf->id] ?? null;
          ?>
          <tr class="border-t">
            <td class="px-3 py-2 whitespace-nowrap">
              <?php echo e(optional($mf->created_at)->format('Y-m-d')); ?>

            </td>

            <form action="<?php echo e(route('outdoor.whiteboard.upsert')); ?>" method="post">
              <?php echo csrf_field(); ?>
              <input type="hidden" name="master_file_id" value="<?php echo e($mf->id); ?>">

              <td class="px-3 py-2">
                <input type="text" name="po_text" value="<?php echo e(old('po_text', $wb->po_text ?? '')); ?>"
                       class="border rounded px-2 py-1 w-48">
              </td>
              <td class="px-3 py-2">
                <input type="date" name="po_date" value="<?php echo e(old('po_date', optional($wb->po_date)->format('Y-m-d'))); ?>"
                       class="border rounded px-2 py-1">
              </td>

              <td class="px-3 py-2">
                <input type="text" name="client_text" value="<?php echo e(old('client_text', $wb->client_text ?? '')); ?>"
                       class="border rounded px-2 py-1 w-48">
              </td>
              <td class="px-3 py-2">
                <input type="date" name="client_date" value="<?php echo e(old('client_date', optional($wb->client_date)->format('Y-m-d'))); ?>"
                       class="border rounded px-2 py-1">
              </td>

            </form>

            <td class="px-3 py-2"><?php echo e($mf->location); ?></td>
            <td class="px-3 py-2"><?php echo e(optional($mf->start_date)->format('Y-m-d')); ?></td>
            <td class="px-3 py-2"><?php echo e(optional($mf->end_date)->format('Y-m-d')); ?></td>

            <form action="<?php echo e(route('outdoor.whiteboard.upsert')); ?>" method="post">
              <?php echo csrf_field(); ?>
              <input type="hidden" name="master_file_id" value="<?php echo e($mf->id); ?>">

              <td class="px-3 py-2">
                <input type="text" name="supplier_text" value="<?php echo e(old('supplier_text', $wb->supplier_text ?? '')); ?>"
                       class="border rounded px-2 py-1 w-48">
              </td>
              <td class="px-3 py-2">
                <input type="date" name="supplier_date" value="<?php echo e(old('supplier_date', optional($wb->supplier_date)->format('Y-m-d'))); ?>"
                       class="border rounded px-2 py-1">
              </td>

              <td class="px-3 py-2">
                <input type="text" name="storage_text" value="<?php echo e(old('storage_text', $wb->storage_text ?? '')); ?>"
                       class="border rounded px-2 py-1 w-48">
              </td>
              <td class="px-3 py-2">
                <input type="date" name="storage_date" value="<?php echo e(old('storage_date', optional($wb->storage_date)->format('Y-m-d'))); ?>"
                       class="border rounded px-2 py-1">
              </td>

              <td class="px-3 py-2">
                <input type="text" name="notes" value="<?php echo e(old('notes', $wb->notes ?? '')); ?>"
                       class="border rounded px-2 py-1 w-56">
              </td>

              <td class="px-3 py-2">
                <button class="px-3 py-1 rounded bg-[#22255b] text-white">Save</button>
              </td>
            </form>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tbody>
    </table>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Gjlang\kl_guide_tracker\resources\views\outdoor\whiteboard.blade.php ENDPATH**/ ?>