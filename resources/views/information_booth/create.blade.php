@extends('layouts.app')

@section('content')
<div class="w-screen min-h-screen bg-[#F7F7F9]">
  <div class="w-full max-w-none px-6 lg:px-10 xl:px-14 py-6 space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-semibold tracking-tight text-neutral-900">Add Backlog Entry</h1>
        <p class="text-sm text-neutral-500">Create a new client feed/backlog item</p>
      </div>
      <a href="{{ route('information.booth.index') }}"
         class="rounded-xl border border-neutral-300 bg-white px-4 py-2 text-sm font-medium text-neutral-800 hover:bg-neutral-50 shadow-sm">
        Back to List
      </a>
    </div>

    <div class="bg-white rounded-2xl border border-neutral-200/70 shadow-sm">
      <div class="p-6">
        @include('information_booth._form', [
          'action' => route('information.booth.store'),
          'method' => 'POST',
          'submitLabel' => 'Save Entry'
        ])
      </div>
    </div>

  </div>
</div>
@endsection
