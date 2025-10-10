<form method="GET" action="<?php echo e(route('information.booth.index')); ?>" class="bg-white rounded-2xl border border-neutral-200/70 shadow-sm">
  <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
    <div>
      <label class="block text-xs font-medium text-neutral-600 mb-1">Search Client</label>
      <input type="text" name="q" value="<?php echo e(request('q')); ?>" placeholder="Enter client name..."
             class="w-full rounded-xl border-neutral-300 focus:border-neutral-400 focus:ring-0">
    </div>
    <div>
      <label class="block text-xs font-medium text-neutral-600 mb-1">Status</label>
      <select name="status" class="w-full rounded-xl border-neutral-300 focus:border-neutral-400 focus:ring-0">
        <option value="">All Status</option>
        <?php $__currentLoopData = ['Pending','In Progress','Completed','Cancelled']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($s); ?>" <?php if(request('status')===$s): echo 'selected'; endif; ?>><?php echo e($s); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
    <div class="flex items-end">
      <button class="rounded-xl border border-neutral-300 bg-white px-4 py-2 text-sm font-medium text-neutral-800 hover:bg-neutral-50 shadow-sm">
        Filter
      </button>
    </div>
  </div>
</form>
<?php /**PATH D:\Projects\Laravel\KL_tracker\resources\views\information_booth\_filters.blade.php ENDPATH**/ ?>