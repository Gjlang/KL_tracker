{{-- resources/views/dashboard/master/kltg.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl p-4">
    @include('dashboard.master._tabs', ['active' => $active ?? 'kltg'])

    <h1 class="text-xl font-semibold mb-3">KLTG Master Files</h1>

    @include('dashboard.master._table', ['rows' => $rows, 'columns' => $columns])
</div>
@endsection
