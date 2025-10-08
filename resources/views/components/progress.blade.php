{{-- resources/views/components/progress.blade.php --}}
@props(['value' => 0, 'color' => 'bg-neutral-400', 'max' => 100])

@php
    $percentage = min(100, max(0, ($value / $max) * 100));
@endphp

<div class="h-2 rounded-full bg-neutral-100 overflow-hidden">
    <div class="h-full {{ $color }} progress-fill transition-all duration-500 ease-out" 
         style="width: {{ $percentage }}%"></div>
</div>