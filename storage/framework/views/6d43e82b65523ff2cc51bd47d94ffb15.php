<?php $__env->startPush('head'); ?>
    
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="mx-auto max-w-7xl p-4">
    <?php echo $__env->make('dashboard.master._tabs', ['active' => $active ?? 'outdoor'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <h1 class="text-xl font-semibold mb-3">OUTDOOR MASTER CLIENTELE</h1>

    
    <?php echo $__env->make('dashboard.master._filters', [
        'action'   => route('dashboard.master.outdoor'),
        'clearUrl' => route('dashboard.master.outdoor'),
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <div class="mb-4">
        <a
            href="<?php echo e(route('dashboard.master.export.outdoor', array_merge(request()->only(['q','status','month','year']), ['scope' => 'outdoor']))); ?>"
            class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition"
        >
            Export Outdoor
        </a>
    </div>

    
    <?php echo $__env->make('dashboard.master._table', [
        'rows'    => $rows,
        'columns' => $columns,

        'editable' => [
            'outdoor_size'             => 'text',
            'outdoor_district_council' => 'text',
            'outdoor_coordinates'      => 'text',
        ],

        // ⬇️ use the new controller route
        'updateUrl'          => route('outdoor.inline.update'),
        'updatePayloadExtra' => ['scope' => 'outdoor'],
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Gjlang\kl_guide_tracker\resources\views\dashboard\master\outdoor.blade.php ENDPATH**/ ?>