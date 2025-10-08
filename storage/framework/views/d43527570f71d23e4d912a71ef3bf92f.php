
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['value' => 0, 'color' => 'bg-neutral-400', 'max' => 100]));

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

foreach (array_filter((['value' => 0, 'color' => 'bg-neutral-400', 'max' => 100]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $percentage = min(100, max(0, ($value / $max) * 100));
?>

<div class="h-2 rounded-full bg-neutral-100 overflow-hidden">
    <div class="h-full <?php echo e($color); ?> progress-fill transition-all duration-500 ease-out" 
         style="width: <?php echo e($percentage); ?>%"></div>
</div><?php /**PATH C:\Users\Gjlang\kl_guide_tracker\resources\views\components\progress.blade.php ENDPATH**/ ?>