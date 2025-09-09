@extends('layouts.app')

@push('head')
    {{-- Needed for inline saves --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="mx-auto max-w-7xl p-4">
    @include('dashboard.master._tabs', ['active' => $active ?? 'kltg'])

    <h1 class="text-xl font-semibold mb-3">KLTG MASTER CLIENTELE</h1>

    {{-- Filters --}}
    @include('dashboard.master._filters', [
        'action'   => route('dashboard.master.kltg'),
        'clearUrl' => route('dashboard.master.kltg'),
    ])

    {{-- Export button (keeps current filters) --}}
    <div class="mb-4">
        <a
            href="{{ route('dashboard.master.export.kltg', array_merge(request()->only(['q','status','month','year']), ['scope' => 'kltg'])) }}"
            class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition"
        >
            Export KLTG
        </a>
    </div>

    {{-- Editable table: choose only the columns you want to allow edits on --}}

    @include('dashboard.master._table', [
        'rows'    => $rows,
        'columns' => $columns,

        // Make specific columns editable (key => input type)
        // adjust to match your $columns keys
        'editable' => [
            'company_name' => 'text',
            'product'      => 'text',
            'category'     => 'text',
            'industry'     => 'text',
            'kltg_x'       => 'text',
            'edition'      => 'text',
            'material_cbp' => 'text',
            'print'        => 'text',
            'start_date'   => 'date',
            'end_date'     => 'date',
            'barter'       => 'text',
        ],

        // Inline update endpoint + scope so controller can pick model
        'updateUrl'          => route('clientele.inline.update'),
        'updatePayloadExtra' => ['scope' => 'kltg'],
    ])
</div>
@endsection
