{{-- resources/views/components/kpi.blade.php --}}
@props(['value', 'label', 'sublabel' => '', 'available' => true, 'accent' => 'ink'])

<div class="bg-white rounded-2xl border border-neutral-200/70 shadow-sm p-5">
    @if($available)
        <div class="flex items-start justify-between">
            <div class="space-y-1">
                <div class="caps-label">{{ $label }}</div>
                @if($sublabel)
                    <div class="text-xs text-neutral-500">{{ $sublabel }}</div>
                @endif
            </div>
            <div class="text-3xl font-semibold tabular-nums {{ $accent }}">{{ $value }}</div>
        </div>
    @else
        <div class="flex items-start justify-between">
            <div class="space-y-1">
                <div class="caps-label">{{ $label }}</div>
                @if($sublabel)
                    <div class="text-xs text-neutral-500">{{ $sublabel }}</div>
                @endif
            </div>
            <div class="text-3xl font-semibold tabular-nums text-neutral-300">—</div>
        </div>
        <div class="mt-2 text-xs text-neutral-400">Data unavailable</div>
    @endif
</div>
