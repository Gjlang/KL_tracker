{{-- resources/views/coordinators/media.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    {{-- Header --}}
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Media Coordinator</h1>
      <p class="mt-2 text-gray-600">Manage content, editing, schedule, reports, and value-add activities</p>
    </div>

    {{-- Filter Section --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
      <h2 class="text-lg font-semibold text-gray-900 mb-4">Filters</h2>
      <form method="GET" action="{{ route('coordinator.media.index') }}" class="flex flex-wrap items-end gap-4">
        <input type="hidden" name="tab" value="{{ $activeTab }}">

        <div class="min-w-0 flex-1 sm:max-w-xs">
          <label class="block text-sm font-medium text-gray-700 mb-2">Month</label>
          <select name="month" class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
            <option value="">-- Select Month --</option>
            @foreach ($months as $mNum => $mName)
              <option value="{{ $mNum }}" {{ (int)($month ?? 0) === (int)$mNum ? 'selected' : '' }}>
                {{ str_pad($mNum, 2, '0', STR_PAD_LEFT) }} - {{ $mName }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="min-w-0 flex-1 sm:max-w-xs">
          <label class="block text-sm font-medium text-gray-700 mb-2">Year</label>
          <input type="number" name="year" value="{{ $year }}"
                 class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                 min="2020" max="2030">
        </div>

        <div class="flex gap-2">
          <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
            </svg>
            Apply Filter
          </button>

          @if(request()->has('month') || request()->has('year'))
            <a href="{{ route('coordinator.media.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white font-medium rounded-md hover:bg-gray-700 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
              Clear
            </a>
          @endif
        </div>
      </form>
    </div>

    {{-- Tabs Section --}}
    @php
      $tabs = [
        'content'  => ['label' => 'Content Calendar', 'icon' => 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z'],
        'editing'  => ['label' => 'Artwork Editing', 'icon' => 'M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z'],
        'schedule' => ['label' => 'Posting Scheduling', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
        'report'   => ['label' => 'Report', 'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        'valueadd' => ['label' => 'Value Add', 'icon' => 'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4'],
      ];
    @endphp

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
      {{-- Tab Navigation --}}
      <div class="border-b border-gray-200 bg-gray-50">
        <div class="flex items-center justify-between p-4">
          <div class="flex space-x-1">
            @foreach ($tabs as $key => $tab)
              <a href="{{ route('coordinator.media.index', array_merge(request()->query(), ['tab' => $key])) }}"
                 class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-md transition-colors {{ $activeTab === $key
                   ? 'bg-indigo-100 text-indigo-700 border-indigo-300'
                   : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tab['icon'] }}"></path>
                </svg>
                {{ $tab['label'] }}
              </a>
            @endforeach
          </div>

          {{-- Export Button --}}
          <a href="{{ route('coordinator.media.export', array_merge(request()->query(), ['tab' => $activeTab])) }}"
             class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
            </svg>
            Export CSV
          </a>
        </div>
      </div>

      {{-- Tab Content --}}
      <div class="p-6">
        @php
          $rows = $rowsByTab[$activeTab] ?? collect();
        @endphp
        @include('coordinators.partials._tab_table', [
          'activeTab' => $activeTab,
          'rows'      => $rows,
          'year'      => $year,
          'month'     => $month,
        ])
      </div>
    </div>
  </div>
</div>

{{-- CSRF Token for AJAX --}}
<meta name="csrf-token" content="{{ csrf_token() }}">

<script>
  window.mediaUpsert = async function ({section, master_file_id, year, month, field, value}) {
    try {
      const res = await fetch("{{ route('coordinator.media.upsert') }}", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({ section, master_file_id, year, month, field, value })
      });
      if (!res.ok) return false;
      const data = await res.json();
      return !!data.ok;
    } catch (e) {
      return false;
    }
  }
</script>
@endsection
