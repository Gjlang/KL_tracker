<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'status' => 'pending',
    'size' => 'default' // 'sm', 'default', 'lg'
]));

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

foreach (array_filter(([
    'status' => 'pending',
    'size' => 'default' // 'sm', 'default', 'lg'
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $statusConfig = [
        'completed' => [
            'label' => 'Completed',
            'icon' => '✅',
            'classes' => 'bg-green-100 text-green-800 border-green-200'
        ],
        'ongoing' => [
            'label' => 'Ongoing',
            'icon' => '🔄',
            'classes' => 'bg-yellow-100 text-yellow-800 border-yellow-200'
        ],
        'pending' => [
            'label' => 'Pending',
            'icon' => '⏳',
            'classes' => 'bg-red-100 text-red-800 border-red-200'
        ]
    ];

    $sizeClasses = [
        'sm' => 'px-2 py-1 text-xs',
        'default' => 'px-2.5 py-0.5 text-xs',
        'lg' => 'px-3 py-1 text-sm'
    ];

    $config = $statusConfig[$status] ?? $statusConfig['pending'];
    $sizeClass = $sizeClasses[$size] ?? $sizeClasses['default'];
?>

<span <?php echo e($attributes->merge([
    'class' => "inline-flex items-center {$sizeClass} font-medium rounded-full border {$config['classes']}"
])); ?>>
    <span class="mr-1"><?php echo e($config['icon']); ?></span>
    <?php echo e($config['label']); ?>

</span>
<?php /**PATH C:\Users\Gjlang\kl_guide_tracker\resources\views\components\status-badge.blade.php ENDPATH**/ ?>