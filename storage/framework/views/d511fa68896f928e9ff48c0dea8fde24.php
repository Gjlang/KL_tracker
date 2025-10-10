<?php $__env->startPush('head'); ?>
    
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="mx-auto max-w-7xl p-4">
    <?php echo $__env->make('dashboard.master._tabs', ['active' => $active ?? 'kltg'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <h1 class="text-xl font-semibold mb-3">KLTG MASTER CLIENTELE</h1>

    
    <?php echo $__env->make('dashboard.master._filters', [
        'action'   => route('dashboard.master.kltg'),
        'clearUrl' => route('dashboard.master.kltg'),
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <div class="mb-4">
        <a
            href="<?php echo e(route('dashboard.master.export.kltg', array_merge(request()->only(['q','status','month','year']), ['scope' => 'kltg']))); ?>"
            class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition"
        >
            Export KLTG
        </a>
    </div>

    
    <?php echo $__env->make('dashboard.master._table', [
        'rows'    => $rows,
        'columns' => $columns,

        // ✅ Only these fields are editable, others are read-only
        'editable' => [
            // Base fields
            'barter'           => 'text',           // Barter
            'product_category' => 'text',           // Product Category

            // KLTG specific fields (matching your controller's $allowedKltg)
            'kltg_industry'    => 'text',           // Industry
            'kltg_x'           => 'text',           // KLTG X
            'kltg_edition'     => 'text',           // Edition
            'kltg_material_cbp'=> 'text',           // Material C/BP
            'kltg_print'       => 'text',           // Print
            'kltg_article'     => 'text',           // Article
            'kltg_video'       => 'text',           // Video
            'kltg_leaderboard' => 'text',           // Leaderboard
            'kltg_qr_code'     => 'text',           // QR Code
            'kltg_blog'        => 'text',           // Blog
            'kltg_em'          => 'text',           // EM
            'kltg_remarks'     => 'textarea',       // Remarks (using textarea for longer text)
        ],

        // Inline update endpoint + scope so controller can pick model
        'updateUrl'          => route('clientele.inline.update'),
        'updatePayloadExtra' => ['scope' => 'kltg'],
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Projects\Laravel\KL_tracker\resources\views\dashboard\master\kltg.blade.php ENDPATH**/ ?>