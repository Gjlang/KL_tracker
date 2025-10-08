<?php $__env->startSection('content'); ?>
<div class="w-screen min-h-screen bg-[#F7F7F9]">
  <div class="w-full max-w-none px-6 lg:px-10 xl:px-14 py-6 space-y-6">

    
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-semibold tracking-tight text-neutral-900">Add Backlog Entry</h1>
        <p class="text-sm text-neutral-500">Create a new client feed/backlog item</p>
      </div>
      <a href="<?php echo e(route('information.booth.index')); ?>"
         class="rounded-xl border border-neutral-300 bg-white px-4 py-2 text-sm font-medium text-neutral-800 hover:bg-neutral-50 shadow-sm">
        Back to List
      </a>
    </div>

    <div class="bg-white rounded-2xl border border-neutral-200/70 shadow-sm">
      <div class="p-6">
        <?php echo $__env->make('information_booth._form', [
          'action' => route('information.booth.store'),
          'method' => 'POST',
          'submitLabel' => 'Save Entry'
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
      </div>
    </div>

  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Gjlang\kl_guide_tracker\resources\views\information_booth\create.blade.php ENDPATH**/ ?>