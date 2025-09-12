@section('container_class', 'w-screen max-w-none px-0')

@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#F7F7F9]">
  {{-- FULL BLEED CONTAINER --}}
  <div class="w-screen max-w-none px-0 py-8">

    {{-- Flash Messages --}}
    @if(session('ok'))
      <div class="mb-6 mx-6 bg-emerald-50 px-4 py-3 text-emerald-800">
        {{ session('ok') }}
      </div>
    @endif
    @if($errors->any())
      <div class="mb-6 mx-6 bg-red-50 px-4 py-3 text-red-800">
        <ul class="list-disc ml-6 space-y-1">
          @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
      </div>
    @endif

    {{-- Header Bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8 px-6">
      <div>
        <h1 class="text-3xl font-serif text-[#1C1E26] tracking-tight">Information Booth</h1>
      </div>
      <div class="flex flex-wrap items-center gap-3">
        <a href="{{ route('information.booth.create') }}"
           class="inline-flex items-center gap-2 bg-[#22255b] text-white hover:opacity-90 px-6 py-2.5 text-sm font-medium transition-opacity">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
          Add Entry
        </a>

        <a href="#calendar-view"
           class="text-neutral-700 hover:bg-neutral-50 px-4 py-2.5 text-sm font-medium transition-colors">
          Calendar View
        </a>

        <a href="{{ route('dashboard') }}"
           class="text-neutral-600 hover:text-neutral-900 hover:bg-neutral-50 px-4 py-2.5 text-sm font-medium transition-colors">
          Back to Dashboard
        </a>
      </div>
    </div>

    {{-- Filters (if any) --}}
    @includeIf('information_booth._filters')

    {{-- Main Content --}}
    <div class="space-y-12">

      {{-- Table Section (flat, no borders) --}}
      <section class="bg-white">
        <div class="px-6 py-4">
          <h2 class="text-lg font-serif text-[#1C1E26] tracking-tight">Client Records</h2>
          <p class="text-xs text-neutral-500 mt-1 uppercase tracking-wider">Active Entries & Status Overview</p>
        </div>
        <div>
          @include('information_booth._table', ['feeds' => $feeds])
        </div>
      </section>

      {{-- Calendar Section (flat, no borders) --}}
      <section id="calendar-view" class="bg-white">
        <div class="px-6 py-4">
          <h2 class="text-lg font-serif text-[#1C1E26] tracking-tight">Calendar Overview</h2>
          <p class="text-xs text-neutral-500 mt-1 uppercase tracking-wider">Schedule & Timeline View</p>
        </div>
        <div class="p-6">
          @if(View::exists('calendar._fullcalendar_embed'))
            @include('calendar._fullcalendar_embed')
          @else
            <div class="aspect-[16/9] bg-[#F7F7F9] flex items-center justify-center">
              <div class="text-center">
                <svg class="w-12 h-12 text-neutral-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-neutral-500 text-sm font-medium">Calendar Component</p>
                <p class="text-neutral-400 text-xs mt-1">Create <code>resources/views/calendar/_fullcalendar_embed.blade.php</code></p>
              </div>
            </div>
          @endif
        </div>
      </section>

    </div>

  </div>
</div>

@push('scripts')
<style>
  /* Full-bleed: no horizontal scroll */
  html, body { overflow-x: hidden; }

  /* Remove ALL borders from FullCalendar */
  .fc-theme-standard td,
  .fc-theme-standard th,
  .fc .fc-scrollgrid,
  .fc .fc-scrollgrid-section > *,
  .fc .fc-scrollgrid-sync-table,
  .fc .fc-daygrid-body,
  .fc .fc-daygrid-day,
  .fc .fc-col-header,
  .fc .fc-timegrid-slot,
  .fc .fc-timegrid-axis,
  .fc .fc-timegrid-divider {
    border: 0 !important;
    box-shadow: none !important;
  }
  .fc .fc-toolbar,
  .fc .fc-view-harness,
  .fc .fc-view,
  .fc .fc-daygrid,
  .fc .fc-timegrid {
    border: 0 !important;
    box-shadow: none !important;
    background: transparent;
  }
</style>
@endpush
@endsection
