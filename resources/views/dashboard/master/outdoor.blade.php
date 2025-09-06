{{-- resources/views/dashboard/master/outdoor.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl p-4">
    @include('dashboard.master._tabs', ['active' => $active ?? 'outdoor'])

    <h1 class="text-xl font-semibold mb-3">Outdoor Master Files</h1>

    @include('dashboard.master._table', ['rows' => $rows, 'columns' => $columns])
</div>
@endsection
