{{-- Simplified OUTDOOR Blade Template - Only 3 editable columns --}}
@extends('layouts.app')

@push('head')
    {{-- Needed for inline saves --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="mx-auto max-w-7xl p-4">
    @include('dashboard.master._tabs', ['active' => $active ?? 'outdoor'])

    <h1 class="text-xl font-semibold mb-3">OUTDOOR MASTER CLIENTELE</h1>

    {{-- Filters --}}
    @include('dashboard.master._filters', [
        'action'   => route('dashboard.master.outdoor'),
        'clearUrl' => route('dashboard.master.outdoor'),
    ])

    {{-- Export button (keeps current filters) --}}
    <div class="mb-4">
        <a
            href="{{ route('dashboard.master.export.outdoor', array_merge(request()->only(['q','status','month','year']), ['scope' => 'outdoor'])) }}"
            class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition"
        >
            Export Outdoor
        </a>
    </div>

    {{-- ✅ ONLY 3 editable columns as requested --}}
    @include('dashboard.master._table', [
        'rows'    => $rows,
        'columns' => $columns,

        'editable' => [
            'outdoor_size'             => 'text',
            'outdoor_district_council' => 'text',
            'outdoor_coordinates'      => 'text',
        ],

        // ⬇️ use the new controller route
        'updateUrl'          => route('outdoor.inline.update'),
        'updatePayloadExtra' => ['scope' => 'outdoor'],
    ])
</div>
@endsection
