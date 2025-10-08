{{-- resources/views/components/chip.blade.php --}}
@props(['label', 'value'])

<div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full border hairline bg-neutral-50">
    <span class="text-xs text-neutral-600">{{ $label }}:</span>
    <span class="text-xs font-medium ink">{{ $value }}</span>
</div>
