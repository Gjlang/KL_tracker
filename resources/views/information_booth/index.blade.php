@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#F7F7F9]">
  <div class="max-w-7xl mx-auto px-6 lg:px-8 py-8">

    {{-- Flash Messages --}}
    @if(session('ok'))
      <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">
        {{ session('ok') }}
      </div>
    @endif
    @if($errors->any())
      <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-red-800">
        <ul class="list-disc ml-6 space-y-1">
          @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
      </div>
    @endif

    {{-- Header Bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
      <div>
        <h1 class="text-3xl font-serif text-[#1C1E26] tracking-tight">Information Booth</h1>
        <p class="text-sm text-neutral-500 mt-1 tracking-wide">Master Clientele Dashboard</p>
      </div>
      <div class="flex flex-wrap items-center gap-3">
        <a href="{{ route('information.booth.create') }}"
           class="inline-flex items-center gap-2 bg-[#22255b] text-white hover:opacity-90 rounded-full px-6 py-2.5 text-sm font-medium transition-opacity">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
          Add Entry
        </a>

        {{-- Jump to Calendar --}}
        <a href="#calendar-view"
           class="border border-neutral-300 text-neutral-700 hover:bg-neutral-50 rounded-full px-4 py-2.5 text-sm font-medium transition-colors">
          Calendar View
        </a>

        <a href="{{ route('dashboard') }}"
           class="text-neutral-600 hover:text-neutral-900 hover:bg-neutral-50 rounded-full px-4 py-2.5 text-sm font-medium transition-colors">
          Back to Dashboard
        </a>
      </div>
    </div>

    {{-- Filters (if they exist) --}}
    @includeIf('information_booth._filters')

    {{-- Main Content Layout --}}
    <div class="space-y-8">

      {{-- Table Section (Top) --}}
      <div class="bg-white rounded-2xl border border-neutral-200/70 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-neutral-200">
          <h2 class="text-lg font-serif text-[#1C1E26] tracking-tight">Client Records</h2>
          <p class="text-xs text-neutral-500 mt-1 uppercase tracking-wider">Active Entries & Status Overview</p>
        </div>
        <div class="overflow-auto">
          @include('information_booth._table', ['feeds' => $feeds])
        </div>
      </div>

      {{-- Calendar Section (Bottom) --}}
      <div id="calendar-view" class="bg-white rounded-2xl border border-neutral-200/70 shadow-sm">
        <div class="px-6 py-4 border-b border-neutral-200">
          <h2 class="text-lg font-serif text-[#1C1E26] tracking-tight">Calendar Overview</h2>
          <p class="text-xs text-neutral-500 mt-1 uppercase tracking-wider">Schedule & Timeline View</p>
        </div>
        <div class="p-6">
          @if(View::exists('calendar._fullcalendar_embed'))
            {{-- FullCalendar embed --}}
            @include('calendar._fullcalendar_embed')
          @else
            {{-- Fallback placeholder if the calendar partial is missing --}}
            <div class="aspect-[16/9] bg-[#F7F7F9] rounded-xl border-2 border-dashed border-neutral-300 flex items-center justify-center">
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
      </div>

    </div>

  </div>
</div>

{{-- Optional: light interactions --}}
@push('scripts')
<script>
  // Confirm destructive actions (used by delete forms in table partials)
  function confirmDelete(message = 'Are you sure you want to delete this item?') {
    return confirm(message);
  }

  // Subtle row hover (keeps UI feeling airy without heavy animations)
  document.addEventListener('DOMContentLoaded', function() {
    const rows = document.querySelectorAll('tbody tr:not(.empty-state)');
    rows.forEach(row => {
      row.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-1px)';
        this.style.boxShadow = '0 4px 6px -1px rgb(0 0 0 / 0.1)';
      });
      row.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
        this.style.boxShadow = 'none';
      });
    });
  });
</script>
@endpush
@endsection
