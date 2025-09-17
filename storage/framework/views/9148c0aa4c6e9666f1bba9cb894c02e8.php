 

<?php $__env->startSection('title', 'All-in-One Summary Report'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    :root { --ink:#111827; --muted:#6B7280; --blue:#4bbbed; --red:#d33831; --navy:#22255b; }
    @media print {
        .no-print { display:none !important; }
        .page-break { page-break-before: always; }
        body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }
    .kpi-card{border:1px solid #E5E7EB;border-radius:16px;padding:16px}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-6xl mx-auto px-4 py-6">
    <div class="flex items-start justify-between no-print">
        <div>
            <h1 class="text-3xl font-semibold" style="color:var(--navy)">All-in-One Summary Report</h1>
            <p class="text-sm text-gray-500">
                Filters:
                Year: <strong><?php echo e($filters['year']); ?></strong>
                <?php if(!empty($filters['month'])): ?> | Month: <strong><?php echo e($filters['month']); ?></strong><?php endif; ?>
                <?php if(!empty($filters['status'])): ?> | Status: <strong><?php echo e($filters['status']); ?></strong><?php endif; ?>
            </p>
        </div>
        <div class="flex gap-2">
            <a href="<?php echo e(route('report.summary.pdf', request()->only(['year','month','status']))); ?>"
               class="no-print inline-flex items-center px-4 py-2 rounded-lg text-white"
               style="background:var(--navy)">Download PDF</a>
            <button onclick="window.print()"
               class="no-print inline-flex items-center px-4 py-2 rounded-lg text-white"
               style="background:var(--blue)">Print Now</button>
        </div>
    </div>

    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">

        
        <section class="kpi-card">
            <h2 class="text-xl font-semibold mb-3">Master File</h2>
            <div class="text-4xl font-bold"><?php echo e($master['active_companies'] ?? 0); ?></div>
            <p class="text-sm text-gray-500 mb-4">Active companies (distinct in <?php echo e($filters['year']); ?>)</p>

            <h3 class="font-medium mb-1">By Category</h3>
            <ul class="text-sm space-y-1 mb-4">
                <?php $__empty_1 = true; $__currentLoopData = $master['by_category'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <li class="flex justify-between">
                        <span><?php echo e($row['category'] ?? 'Unknown'); ?></span>
                        <span class="font-medium"><?php echo e($row['total']); ?></span>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <li class="text-gray-400">No category column found.</li>
                <?php endif; ?>
            </ul>

            <h3 class="font-medium mb-1">Status Distribution</h3>
            <?php
                $sd = $master['status_dist'] ?? [];
                $tot = array_sum($sd);
                $pct = fn($k)=> $tot ? round(($sd[$k]??0)/$tot*100) : 0;
            ?>
            <div class="text-sm grid grid-cols-3 gap-3">
                <div><span class="inline-block px-2 py-1 rounded" style="background:#E0F2FE">Pending</span><div><?php echo e($pct('pending')); ?>%</div></div>
                <div><span class="inline-block px-2 py-1 rounded" style="background:#EDE9FE">In-progress</span><div><?php echo e($pct('in-progress')); ?>%</div></div>
                <div><span class="inline-block px-2 py-1 rounded" style="background:#DCFCE7">Completed</span><div><?php echo e($pct('completed')); ?>%</div></div>
            </div>
        </section>

        
        <section class="kpi-card">
            <h2 class="text-xl font-semibold mb-3">Outdoor</h2>
            <?php if(($outdoor['available'] ?? false) === false): ?>
                <p class="text-gray-400">Table not found.</p>
            <?php else: ?>
                <div class="flex justify-between text-sm mb-2">
                    <span>Active jobs</span><span class="text-2xl font-bold"><?php echo e($outdoor['active_jobs']); ?></span>
                </div>
                <div class="flex justify-between text-sm mb-2">
                    <span>Completed (period)</span><span class="font-semibold"><?php echo e($outdoor['completed_this']); ?></span>
                </div>
                <div class="flex justify-between text-sm mb-2">
                    <span>Completion rate</span><span class="font-semibold"><?php echo e($outdoor['completion_rate']); ?>%</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span>Issues (pending/in-progress)</span><span class="font-semibold"><?php echo e($outdoor['issues']); ?></span>
                </div>
            <?php endif; ?>
        </section>

        
        <section class="kpi-card">
            <h2 class="text-xl font-semibold mb-3">KLTG</h2>
            <?php if(($kltg['available'] ?? false) === false): ?>
                <p class="text-gray-400">Table not found.</p>
            <?php else: ?>
                <div class="flex justify-between text-sm mb-2">
                    <span>Production progress</span><span class="text-2xl font-bold"><?php echo e($kltg['production_progress']); ?>%</span>
                </div>
                <div class="flex justify-between text-sm mb-2">
                    <span>Slots filled</span><span class="font-semibold"><?php echo e($kltg['slots_filled']); ?></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span>Pending approvals</span><span class="font-semibold"><?php echo e($kltg['pending_approvals']); ?></span>
                </div>
            <?php endif; ?>
        </section>

        
        <section class="kpi-card">
            <h2 class="text-xl font-semibold mb-3">Media Social</h2>
            <?php if(($media['available'] ?? false) === false): ?>
                <p class="text-gray-400">Table not found.</p>
            <?php else: ?>
                <div class="flex justify-between text-sm mb-2">
                    <span>Campaigns</span><span class="text-2xl font-bold"><?php echo e($media['campaigns']); ?></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span>Posts (period)</span><span class="font-semibold"><?php echo e($media['posts']); ?></span>
                </div>
            <?php endif; ?>
        </section>
    </div>

    <p class="text-xs text-gray-400 mt-6">Generated at <?php echo e($generated->timezone(config('app.timezone'))->format('M d, Y H:i')); ?></p>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Gjlang\kl_guide_tracker\resources\views/reports/summary.blade.php ENDPATH**/ ?>