@props([
    'status' => 'pending',
    'size' => 'default' // 'sm', 'default', 'lg'
])

@php
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
@endphp

<span {{ $attributes->merge([
    'class' => "inline-flex items-center {$sizeClass} font-medium rounded-full border {$config['classes']}"
]) }}>
    <span class="mr-1">{{ $config['icon'] }}</span>
    {{ $config['label'] }}
</span>
