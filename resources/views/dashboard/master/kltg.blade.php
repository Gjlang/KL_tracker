@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl p-4">
    @include('dashboard.master._tabs', ['active' => $active ?? 'kltg'])

    <h1 class="text-xl font-semibold mb-3">KLTG Master Files</h1>

    <!-- Export button -->
    <div class="mb-4">
        <a
            href="{{ route('dashboard.master.export.kltg', array_merge(request()->only(['search','status','month','year']), ['scope' => 'kltg'])) }}"
            class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition"
        >
            Export KLTG
        </a>
    </div>

    @include('dashboard.master._table', ['rows' => $rows, 'columns' => $columns])
</div>
@endsection
