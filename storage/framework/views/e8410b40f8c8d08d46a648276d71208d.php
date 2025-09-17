<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Summary <?php echo e($filters['year']); ?></title>
    <style>
        *{font-family: DejaVu Sans, sans-serif; box-sizing:border-box}
        .wrap{padding:18px}
        .row{display:flex;gap:12px;flex-wrap:wrap}
        .col{flex:1 1 45%;border:1px solid #ddd;border-radius:12px;padding:12px}
        h1{font-size:20px;margin:0 0 6px}
        h2{font-size:14px;margin:0 0 8px}
        .big{font-size:28px;font-weight:700}
        .muted{color:#666;font-size:11px}
        ul{margin:6px 0 0;padding-left:14px}
        li{font-size:12px; margin:2px 0}
        .pair{display:flex;justify-content:space-between;font-size:12px;margin:3px 0}
    </style>
</head>
<body>
<div class="wrap">
    <h1>All-in-One Summary Report</h1>
    <div class="muted">Year: <?php echo e($filters['year']); ?> <?php if($filters['month']): ?> | Month: <?php echo e($filters['month']); ?> <?php endif; ?> <?php if($filters['status']): ?> | Status: <?php echo e($filters['status']); ?> <?php endif; ?></div>

    <div class="row" style="margin-top:10px">
        <div class="col">
            <h2>Master File</h2>
            <div class="big"><?php echo e($master['active_companies'] ?? 0); ?></div>
            <div class="muted">Active companies (distinct in <?php echo e($filters['year']); ?>)</div>

            <h3 style="font-size:12px;margin-top:8px">By Category</h3>
            <ul>
                <?php $__empty_1 = true; $__currentLoopData = $master['by_category'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <li><?php echo e($row['category'] ?? 'Unknown'); ?> — <strong><?php echo e($row['total']); ?></strong></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <li class="muted">No category column found.</li>
                <?php endif; ?>
            </ul>

            <?php $sd = $master['status_dist'] ?? []; $tot = array_sum($sd); ?>
            <h3 style="font-size:12px;margin-top:8px">Status Distribution</h3>
            <div class="pair"><span>Pending</span><span><strong><?php echo e($tot?round(($sd['pending']??0)/$tot*100):0); ?>%</strong></span></div>
            <div class="pair"><span>In-progress</span><span><strong><?php echo e($tot?round(($sd['in-progress']??0)/$tot*100):0); ?>%</strong></span></div>
            <div class="pair"><span>Completed</span><span><strong><?php echo e($tot?round(($sd['completed']??0)/$tot*100):0); ?>%</strong></span></div>
        </div>

        <div class="col">
            <h2>Outdoor</h2>
            <?php if(($outdoor['available'] ?? false)): ?>
                <div class="pair"><span>Active jobs</span><span class="big" style="font-size:22px"><?php echo e($outdoor['active_jobs']); ?></span></div>
                <div class="pair"><span>Completed (period)</span><span><strong><?php echo e($outdoor['completed_this']); ?></strong></span></div>
                <div class="pair"><span>Completion rate</span><span><strong><?php echo e($outdoor['completion_rate']); ?>%</strong></span></div>
                <div class="pair"><span>Issues</span><span><strong><?php echo e($outdoor['issues']); ?></strong></span></div>
            <?php else: ?>
                <div class="muted">Table not found.</div>
            <?php endif; ?>
        </div>

        <div class="col">
            <h2>KLTG</h2>
            <?php if(($kltg['available'] ?? false)): ?>
                <div class="pair"><span>Production progress</span><span class="big" style="font-size:22px"><?php echo e($kltg['production_progress']); ?>%</span></div>
                <div class="pair"><span>Slots filled</span><span><strong><?php echo e($kltg['slots_filled']); ?></strong></span></div>
                <div class="pair"><span>Pending approvals</span><span><strong><?php echo e($kltg['pending_approvals']); ?></strong></span></div>
            <?php else: ?>
                <div class="muted">Table not found.</div>
            <?php endif; ?>
        </div>

        <div class="col">
            <h2>Media Social</h2>
            <?php if(($media['available'] ?? false)): ?>
                <div class="pair"><span>Campaigns</span><span class="big" style="font-size:22px"><?php echo e($media['campaigns']); ?></span></div>
                <div class="pair"><span>Posts (period)</span><span><strong><?php echo e($media['posts']); ?></strong></span></div>
            <?php else: ?>
                <div class="muted">Table not found.</div>
            <?php endif; ?>
        </div>
    </div>

    <div class="muted" style="margin-top:8px">Generated at <?php echo e($generated->timezone(config('app.timezone'))->format('M d, Y H:i')); ?></div>
</div>
</body>
</html>
<?php /**PATH C:\Users\Gjlang\kl_guide_tracker\resources\views/reports/summary-pdf.blade.php ENDPATH**/ ?>