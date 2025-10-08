{{-- resources/views/components/card.blade.php --}}
@props(['title'])

<div class="bg-white rounded-2xl border border-neutral-200/70 shadow-sm p-6">
    <h2 class="text-xl font-serif font-medium ink mb-4">{{ $title }}</h2>
    <div {{ $attributes->merge(['class' => '']) }}>
        {{ $slot }}
    </div>
</div>
