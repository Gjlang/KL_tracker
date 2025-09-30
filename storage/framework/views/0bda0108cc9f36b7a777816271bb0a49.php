
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['value', 'label', 'sublabel' => '', 'available' => true, 'accent' => 'ink']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['value', 'label', 'sublabel' => '', 'available' => true, 'accent' => 'ink']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="bg-white rounded-2xl border border-neutral-200/70 shadow-sm p-5">
    <?php if($available): ?>
        <div class="flex items-start justify-between">
            <div class="space-y-1">
                <div class="caps-label"><?php echo e($label); ?></div>
                <?php if($sublabel): ?>
                    <div class="text-xs text-neutral-500"><?php echo e($sublabel); ?></div>
                <?php endif; ?>
            </div>
            <div class="text-3xl font-semibold tabular-nums <?php echo e($accent); ?>"><?php echo e($value); ?></div>
        </div>
    <?php else: ?>
        <div class="flex items-start justify-between">
            <div class="space-y-1">
                <div class="caps-label"><?php echo e($label); ?></div>
                <?php if($sublabel): ?>
                    <div class="text-xs text-neutral-500"><?php echo e($sublabel); ?></div>
                <?php endif; ?>
            </div>
            <div class="text-3xl font-semibold tabular-nums text-neutral-300">—</div>
        </div>
        <div class="mt-2 text-xs text-neutral-400">Data unavailable</div>
    <?php endif; ?>
</div>
<?php /**PATH D:\Projects\Laravel\KL_tracker\resources\views\components\kpi.blade.php ENDPATH**/ ?>