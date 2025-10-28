@extends('layouts.app')

@section('title')
    Outdoor Coordinator List
@endsection

@section('title', 'Outdoor Coordinator')

@push('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- ... other head content ... -->
    <title>@yield('title', 'Default Title')</title> <!-- Example -->
    <style>
        /* Style Tokens */
        .ink {
            color: #1C1E26;
        }

        .card {
            @apply bg-white rounded-2xl border border-neutral-200/70 shadow-sm;
        }

        .hairline {
            border-color: #EAEAEA;
        }

        .btn-primary {
            @apply bg-[#22255b] text-white hover:opacity-90 focus:ring-2 focus:ring-[#4bbbed] rounded-full px-5 py-2 transition-all duration-150;
        }

        .btn-secondary {
            @apply border border-neutral-300 text-neutral-800 hover:bg-neutral-50 rounded-full px-5 py-2 transition-all duration-150;
        }

        .btn-ghost {
            @apply text-neutral-700 hover:bg-neutral-50 rounded-full px-4 py-2 transition-all duration-150;
        }

        .chip {
            @apply bg-neutral-100 text-neutral-700 px-3 py-1 rounded-full text-xs flex items-center gap-1;
        }

        .input {
            @apply h-11 rounded-xl border-neutral-300 focus:ring-2 focus:ring-[#4bbbed] focus:border-transparent transition-all duration-150;
        }

        .tabular-nums {
            font-variant-numeric: tabular-nums;
        }

        /* Typography */
        .serif {
            font-family: 'Playfair Display', 'EB Garamond', serif;
        }

        .sans {
            font-family: 'Inter', 'Proxima Nova', sans-serif;
        }

        /* Table headers with small caps */
        .table-header {
            @apply text-xs uppercase tracking-wider font-medium;
            color: #6B7280;
            letter-spacing: 0.05em;
        }

        /* Hover effects */
        .hover-lift:hover {
            @apply shadow-sm;
            transform: translateY(-1px);
        }

        /* Status indicator */
        .status-dot {
            @apply w-2 h-2 rounded-full bg-green-400;
            animation: pulse 2s infinite;
        }

        /* Input field styles */
        .field-input {
            @apply h-10 rounded-lg border border-neutral-300 focus:ring-2 focus:ring-[#4bbbed] focus:border-transparent px-3 py-2 text-sm transition-all duration-150;
        }

        .field-input:hover {
            @apply ring-1 ring-[#4bbbed]/20;
        }

        .field-readonly {
            @apply bg-neutral-50 text-neutral-700 rounded-lg px-3 py-2 text-sm;
        }
    </style>
@endpush

@section('content')
    <div class="min-h-screen bg-[#F7F7F9]">
        <div class="p-6 md:p-8 max-w-full mx-auto">

            {{-- Header Section --}}
            <div class="mb-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                    <div>
                        <h1 class="serif text-4xl font-light ink mb-2">Outdoor Coordinator</h1>
                        <p class="sans text-neutral-600">Track progress across all outdoor advertising projects</p>
                    </div>
                    <div>
                        <a href="{{ route('dashboard.outdoor') }}" class="btn-ghost">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back to Monthly
                        </a>
                    </div>
                </div>

                {{-- Flash Messages --}}
                @if (session('success'))
                    <div class="mb-6 p-4 card bg-green-50 border-green-200 text-green-800">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ session('success') }}
                        </div>
                    </div>
                @endif

                @if (session('info'))
                    <div class="mb-6 p-4 card bg-blue-50 border-blue-200 text-blue-800">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ session('info') }}
                        </div>
                    </div>
                @endif
            </div>

            {{-- Filter Panel --}}
            <div class="mb-6 card">
                <div class="p-6">
                    <form method="GET" action="{{ route('coordinator.outdoor.index') }}">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                            {{-- Left Column: Filters --}}
                            <div class="md:col-span-2 space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                    {{-- Month Filter --}}
                                    <div class="space-y-2">
                                        <label for="filterMonth" class="sans table-header">Month</label>
                                        <select name="month" id="filterMonth" class="w-full input sans">
                                            <option value="">All Months</option>
                                            @foreach ($months ?? [] as $m)
                                                <option value="{{ $m['value'] }}" @selected((int) ($month ?? 0) === (int) $m['value'])>
                                                    {{ $m['label'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    {{-- Year Filter --}}
                                    <div class="space-y-2">
                                        <label for="filterYear" class="sans table-header">Year</label>
                                        <input type="number" id="filterYear" name="year"
                                            value="{{ (int) ($year ?? now()->year) }}" min="2000"
                                            max="{{ now()->year + 1 }}" class="w-full input sans tabular-nums">
                                    </div>

                                    {{-- Product Filter --}}
                                    <div class="space-y-2">
                                        <label for="filterProduct" class="sans table-header">Product</label>
                                        <select name="product_filter" id="filterProduct" class="w-full input sans">
                                            <option value="">All Products</option>
                                            <option value="BB" @selected(($productFilter ?? '') === 'BB')>BB (Billboard)</option>
                                            <option value="TB" @selected(($productFilter ?? '') === 'TB')>TB</option>
                                            <option value="SIGNAGES" @selected(($productFilter ?? '') === 'SIGNAGES')>Signages</option>
                                            <option value="BUNTING" @selected(($productFilter ?? '') === 'BUNTING')>Bunting</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Show only active this month --}}
                                <div class="flex items-center space-x-2">
                                    <input type="checkbox" id="toggleActive" name="active" value="1"
                                        @checked(request('active')) @disabled(!request('month'))
                                        class="rounded border-neutral-300 text-[#22255b] focus:ring-[#4bbbed]">
                                    <label for="toggleActive" class="sans text-sm text-neutral-700">
                                        Show only active this month
                                    </label>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="flex flex-col sm:flex-row gap-3 pt-2">
                                    <button type="submit" class="btn-primary flex-1 sm:flex-none">
                                        Apply Filters
                                    </button>
                                    <a href="{{ route('coordinator.outdoor.index') }}"
                                        class="btn-secondary flex-1 sm:flex-none text-center">
                                        Reset
                                    </a>
                                </div>
                            </div>

                            {{-- Right Column: Export --}}
                            <div class="flex flex-col justify-end">
                                <a href="{{ route('coordinator.outdoor.export', [
                                    'month' => request('month'),
                                    'year' => request('year'),
                                    'search' => request('search'),
                                ]) }}"
                                    class="btn-secondary text-center">
                                    Export XLSX
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Table Section --}}
            <div class="card overflow-hidden">

                {{-- Table Header --}}
                <div class="px-6 py-4 hairline border-b">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <h3 class="serif text-xl font-medium ink">Coordinator List — Outdoor</h3>
                            <p class="sans text-sm text-neutral-600 mt-1">Track progress across all outdoor advertising
                                projects</p>
                        </div>
                        <div class="flex items-center gap-2 sans text-sm text-neutral-600">
                            Auto-save enabled
                            <span class="status-dot"></span>
                        </div>
                    </div>
                </div>

                {{-- Mini pager (shows only if >15 rows) --}}
                @if (($rows ?? collect())->count() > 15)
                    <div class="flex items-center justify-between px-4 py-2 border-b border-neutral-200 bg-neutral-50/50">
                        <span class="text-xs text-neutral-600 sans">
                            Showing <span id="rowsPerPage">15</span> rows per page
                        </span>
                        <div class="flex items-center gap-2">
                            <button id="prevPage" type="button"
                                class="px-3 py-1.5 text-xs rounded-full border border-neutral-300 text-neutral-700 hover:bg-neutral-50 disabled:opacity-40 disabled:cursor-not-allowed transition-all duration-150"
                                disabled>Prev</button>
                            <span id="pageInfo" class="text-xs text-neutral-600 sans tabular-nums">1 / 1</span>
                            <button id="nextPage" type="button"
                                class="px-3 py-1.5 text-xs rounded-full border border-neutral-300 text-neutral-700 hover:bg-neutral-50 disabled:opacity-40 disabled:cursor-not-allowed transition-all duration-150">
                                Next
                            </button>
                        </div>
                    </div>
                @endif

                {{-- Table Content --}}
                <div class="overflow-x-auto">
                    <table id="outdoorTable" class="min-w-full">
                        {{-- Table Headers --}}
                        <thead class="bg-neutral-50 sticky top-0 z-10">
                            <tr class="hairline border-b">
                                <th class="px-4 py-4 table-header text-center min-w-[80px] w-[80px]">NO</th>
                                <th class="px-4 py-4 table-header min-w-[200px] w-[200px]">Company</th>
                                <th class="px-4 py-4 table-header min-w-[200px] w-[200px]">Person In Charge</th>
                                <th class="px-4 py-4 table-header min-w-[180px] w-[180px]">Product</th>
                                <th class="px-4 py-4 table-header min-w-[180px] w-[180px]">Site</th>
                                <th class="px-4 py-4 table-header min-w-[160px]">Payment</th>
                                <th class="px-4 py-4 table-header min-w-[160px]">Material Received</th>
                                <th class="px-4 py-4 table-header min-w-[160px]">Artwork Done</th>
                                <th class="px-4 py-4 table-header min-w-[160px]">Received Approval</th>
                                <th class="px-4 py-4 table-header min-w-[160px]">Sent to Printer</th>
                                <th class="px-4 py-4 table-header min-w-[160px]">Collection to Printer</th>
                                <th class="px-4 py-4 table-header min-w-[160px]">Installation</th>
                                <th class="px-4 py-4 table-header min-w-[160px]">Dismantle</th>
                            </tr>
                        </thead>

                        {{-- Table Body --}}
                        <tbody id="outdoorTbody" class="bg-white divide-y divide-neutral-200">
                            @if (isset($rows) && $rows->count() > 0)
                                @foreach ($rows as $i => $row)
                                    @php
                                        // Determine scope based on whether month is selected
                                        $isMonth =
                                            isset($month) && $month !== '' && (int) $month >= 1 && (int) $month <= 12;
                                        $scope = $isMonth ? 'omd' : 'oct';

                                        // Use Query Builder aliases directly
                                        $trackingId = $row->tracking_id ?? null;

                                        // Define editable columns and date fields
                                        $editableCols = [
                                            'payment',
                                            'material',
                                            'artwork',
                                            'received_approval',
                                            'sent_to_printer',
                                            'collection_printer',
                                            'installation',
                                            'dismantle',
                                        ];
                                        $dateCols = [
                                            'received_approval',
                                            'sent_to_printer',
                                            'collection_printer',
                                            'installation',
                                            'dismantle',
                                        ];
                                    @endphp

                                    <tr class="hover:bg-neutral-50 hover-lift transition-all duration-150"
                                        data-idx="{{ $loop->index }}" data-scope="{{ $scope }}"
                                        data-id="{{ $row->tracking_id ?? '' }}" data-mf="{{ $row->master_file_id }}"
                                        data-oi="{{ $row->outdoor_item_id }}"
                                        data-year="{{ (int) ($year ?? now()->year) }}"
                                        data-month="{{ $isMonth ? (int) $month : '' }}">

                                        {{-- ID --}}
                                        <td
                                            class="bg-white hairline border-r px-4 py-4 text-center ink font-medium tabular-nums min-w-[80px] w-[80px]">
                                            {{ $rows->firstItem() + $i }}
                                        </td>

                                        {{-- Company --}}
                                        <td class="bg-white hairline border-r px-4 py-4 min-w-[200px] w-[200px]">
                                            <div class="field-readonly font-medium truncate">
                                                {{ $row->company ?? '—' }}
                                            </div>
                                        </td>

                                        {{-- Person In Charge --}}
                                        <td class="bg-white hairline border-r px-4 py-4 min-w-[200px] w-[200px]">
                                            <div class="field-readonly truncate">
                                                {{ $row->client ?? '—' }}
                                            </div>
                                        </td>

                                        {{-- Product --}}
                                        <td class="bg-white hairline border-r px-4 py-4 min-w-[180px] w-[180px]">
                                            <div class="field-readonly truncate">
                                                {{ $row->product ?? '—' }}
                                            </div>
                                        </td>

                                        {{-- Site (format: CODE - District, fallback ke road/district_council) --}}
                                        @php
                                            $siteCode = strtoupper(trim((string) ($row->site_code ?? '')));
                                            $road = trim((string) ($row->site ?? ''));
                                            $district = trim((string) ($row->district ?? ''));

                                            $normalize = function ($s) {
                                                return preg_replace('/[^a-z0-9]/i', '', strtolower((string) $s));
                                            };

                                            // Pilih primary (lebih prefer code)
                                            $primary = $siteCode !== '' ? $siteCode : $road;

                                            // Hindari duplikat kalau primary == district
                                            $isDupPrimaryDistrict =
                                                $primary !== '' &&
                                                $district !== '' &&
                                                $normalize($primary) === $normalize($district);

                                            $parts = [];
                                            if ($primary !== '') {
                                                $parts[] = $primary;
                                            }
                                            if ($district !== '' && !$isDupPrimaryDistrict) {
                                                $parts[] = $district;
                                            }

                                            $locationDisplay = $parts ? implode(' - ', $parts) : '—';
                                        @endphp

                                        <td class="bg-white hairline border-r px-4 py-4 min-w-[180px] w-[180px]">
                                            <div class="field-readonly truncate" title="{{ $locationDisplay }}">
                                                {{ $locationDisplay }}
                                            </div>
                                        </td>


                                        {{-- PAYMENT (text + date) --}}
                                        <td class="px-4 py-4 hairline border-b align-top">
                                            <div class="space-y-2 w-44">
                                                {{-- text --}}
                                                <input type="text" class="field-input w-44 outdoor-field"
                                                    value="{{ $row->payment ?? '' }}" data-id="{{ $trackingId }}"
                                                    data-mf="{{ $row->master_file_id }}"
                                                    data-oi="{{ $row->outdoor_item_id }}" data-field="payment"
                                                    data-scope="{{ $scope }}" placeholder="note..." />
                                                {{-- date --}}
                                                <input type="date" class="field-input w-44 tabular-nums outdoor-field"
                                                    value="{{ $row->payment_date ?? '' }}" data-id="{{ $trackingId }}"
                                                    data-mf="{{ $row->master_file_id }}"
                                                    data-oi="{{ $row->outdoor_item_id }}" data-field="payment_date"
                                                    data-scope="{{ $scope }}" />
                                            </div>
                                        </td>

                                        {{-- MATERIAL (text + date) --}}
                                        <td class="px-4 py-4 hairline border-b align-top">
                                            <div class="space-y-2 w-44">
                                                <input type="text" class="field-input w-44 outdoor-field"
                                                    value="{{ $row->material ?? '' }}" data-id="{{ $trackingId }}"
                                                    data-mf="{{ $row->master_file_id }}"
                                                    data-oi="{{ $row->outdoor_item_id }}" data-field="material"
                                                    data-scope="{{ $scope }}" placeholder="note..." />
                                                <input type="date" class="field-input w-44 tabular-nums outdoor-field"
                                                    value="{{ $row->material_date ?? '' }}"
                                                    data-id="{{ $trackingId }}" data-mf="{{ $row->master_file_id }}"
                                                    data-oi="{{ $row->outdoor_item_id }}" data-field="material_date"
                                                    data-scope="{{ $scope }}" />
                                            </div>
                                        </td>

                                        {{-- ARTWORK (text + date) --}}
                                        <td class="px-4 py-4 hairline border-b align-top">
                                            <div class="space-y-2 w-44">
                                                <input type="text" class="field-input w-44 outdoor-field"
                                                    value="{{ $row->artwork ?? '' }}" data-id="{{ $trackingId }}"
                                                    data-mf="{{ $row->master_file_id }}"
                                                    data-oi="{{ $row->outdoor_item_id }}" data-field="artwork"
                                                    data-scope="{{ $scope }}" placeholder="note..." />
                                                <input type="date" class="field-input w-44 tabular-nums outdoor-field"
                                                    value="{{ $row->artwork_date ?? '' }}" data-id="{{ $trackingId }}"
                                                    data-mf="{{ $row->master_file_id }}"
                                                    data-oi="{{ $row->outdoor_item_id }}" data-field="artwork_date"
                                                    data-scope="{{ $scope }}" />
                                            </div>
                                        </td>

                                        {{-- APPROVAL (note + date) --}}
                                        <td class="px-4 py-4 hairline border-b align-top">
                                            <div class="space-y-2 w-44">
                                                <input type="text" class="field-input w-44 outdoor-field"
                                                    value="{{ $row->received_approval_note ?? '' }}"
                                                    data-id="{{ $trackingId }}" data-mf="{{ $row->master_file_id }}"
                                                    data-oi="{{ $row->outdoor_item_id }}"
                                                    data-field="received_approval_note" data-scope="{{ $scope }}"
                                                    placeholder="note..." />
                                                <input type="date" class="field-input w-44 tabular-nums outdoor-field"
                                                    value="{{ $row->received_approval ?? '' }}"
                                                    data-id="{{ $trackingId }}" data-mf="{{ $row->master_file_id }}"
                                                    data-oi="{{ $row->outdoor_item_id }}" data-field="received_approval"
                                                    data-scope="{{ $scope }}" />
                                            </div>
                                        </td>

                                        {{-- SENT (note + date) --}}
                                        <td class="px-4 py-4 hairline border-b align-top">
                                            <div class="space-y-2 w-44">
                                                <input type="text" class="field-input w-44 outdoor-field"
                                                    value="{{ $row->sent_to_printer_note ?? '' }}"
                                                    data-id="{{ $trackingId }}" data-mf="{{ $row->master_file_id }}"
                                                    data-oi="{{ $row->outdoor_item_id }}"
                                                    data-field="sent_to_printer_note" data-scope="{{ $scope }}"
                                                    placeholder="note..." />
                                                <input type="date" class="field-input w-44 tabular-nums outdoor-field"
                                                    value="{{ $row->sent_to_printer ?? '' }}"
                                                    data-id="{{ $trackingId }}" data-mf="{{ $row->master_file_id }}"
                                                    data-oi="{{ $row->outdoor_item_id }}" data-field="sent_to_printer"
                                                    data-scope="{{ $scope }}" />
                                            </div>
                                        </td>

                                        {{-- COLLECTED (note + date) --}}
                                        <td class="px-4 py-4 hairline border-b align-top">
                                            <div class="space-y-2 w-44">
                                                <input type="text" class="field-input w-44 outdoor-field"
                                                    value="{{ $row->collection_printer_note ?? '' }}"
                                                    data-id="{{ $trackingId }}" data-mf="{{ $row->master_file_id }}"
                                                    data-oi="{{ $row->outdoor_item_id }}"
                                                    data-field="collection_printer_note" data-scope="{{ $scope }}"
                                                    placeholder="note..." />
                                                <input type="date" class="field-input w-44 tabular-nums outdoor-field"
                                                    value="{{ $row->collection_printer ?? '' }}"
                                                    data-id="{{ $trackingId }}" data-mf="{{ $row->master_file_id }}"
                                                    data-oi="{{ $row->outdoor_item_id }}" data-field="collection_printer"
                                                    data-scope="{{ $scope }}" />
                                            </div>
                                        </td>

                                        {{-- INSTALL (note + date) --}}
                                        <td class="px-4 py-4 hairline border-b align-top">
                                            <div class="space-y-2 w-44">
                                                <input type="text" class="field-input w-44 outdoor-field"
                                                    value="{{ $row->installation_note ?? '' }}"
                                                    data-id="{{ $trackingId }}" data-mf="{{ $row->master_file_id }}"
                                                    data-oi="{{ $row->outdoor_item_id }}" data-field="installation_note"
                                                    data-scope="{{ $scope }}" placeholder="note..." />
                                                <input type="date" class="field-input w-44 tabular-nums outdoor-field"
                                                    value="{{ $row->installation ?? '' }}" data-id="{{ $trackingId }}"
                                                    data-mf="{{ $row->master_file_id }}"
                                                    data-oi="{{ $row->outdoor_item_id }}" data-field="installation"
                                                    data-scope="{{ $scope }}" />
                                            </div>
                                        </td>

                                        {{-- DISMANTLE (note + date) --}}
                                        <td class="px-4 py-4 hairline border-b align-top">
                                            <div class="space-y-2 w-44">
                                                <input type="text" class="field-input w-44 outdoor-field"
                                                    value="{{ $row->dismantle_note ?? '' }}"
                                                    data-id="{{ $trackingId }}" data-mf="{{ $row->master_file_id }}"
                                                    data-oi="{{ $row->outdoor_item_id }}" data-field="dismantle_note"
                                                    data-scope="{{ $scope }}" placeholder="note..." />
                                                <input type="date" class="field-input w-44 tabular-nums outdoor-field"
                                                    value="{{ $row->dismantle ?? '' }}" data-id="{{ $trackingId }}"
                                                    data-mf="{{ $row->master_file_id }}"
                                                    data-oi="{{ $row->outdoor_item_id }}" data-field="dismantle"
                                                    data-scope="{{ $scope }}" />
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                {{-- Empty State --}}
                                <tr>
                                    <td colspan="13" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center max-w-sm mx-auto">
                                            <svg class="w-12 h-12 text-neutral-300 mb-4" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                            <p class="serif text-lg text-neutral-600 mb-2">No tracking records found</p>
                                            <p class="sans text-sm text-neutral-500">Try adjusting your filters or check
                                                back later</p>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if (isset($rows) && method_exists($rows, 'links') && $rows->hasPages())
                    <div class="px-6 py-4 hairline border-t">
                        {{ $rows->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const token = document.querySelector('meta[name="csrf-token"]')?.content || '';

            function getYM() {
                const y = document.getElementById('ctxYear')?.value ?? document.getElementById('filterYear')
                    ?.value ?? '';
                const m = document.getElementById('ctxMonth')?.value ?? document.getElementById('filterMonth')
                    ?.value ?? '';
                const year = Number.parseInt(String(y), 10);
                const month = Number.parseInt(String(m), 10);
                return {
                    year: Number.isFinite(year) ? year : null,
                    month: Number.isFinite(month) ? month : null,
                };
            }

            function normalizeValue(el) {
                if (el.type === 'checkbox') return el.checked ? 1 : 0;
                if (el.type === 'date' && el.value) return el.value.trim();
                return typeof el.value === 'string' ? el.value.trim() : el.value;
            }

            function findRowContext(el) {
                const tr = el.closest('tr');
                const id = el.dataset.id || tr?.dataset.id || null;
                const mf = el.dataset.mf || tr?.dataset.mf || null;
                const oi = el.dataset.oi || tr?.dataset.oi || null;
                const scopeAttr = el.dataset.scope || tr?.dataset.scope || '';

                // FIXED: More conservative scope detection
                // Only use 'omd' if explicitly set AND we have valid month/year/outdoor_item_id
                const {
                    year,
                    month
                } = getYM();
                let actualScope = 'oct'; // Default to baseline mode

                if (scopeAttr === 'omd' && month && year && oi) {
                    actualScope = 'omd';
                }

                return {
                    tr,
                    id,
                    scope: actualScope,
                    mf: mf ? parseInt(mf, 10) : null,
                    oi: oi ? parseInt(oi, 10) : null
                };
            }

            function showSaveIndicator(element, state) {
                const parent = element.parentElement;
                const map = {
                    loading: parent?.querySelector('[data-save-indicator]'),
                    success: parent?.querySelector('[data-save-success]'),
                    error: parent?.querySelector('[data-save-error]')
                };
                Object.values(map).forEach(n => n?.classList.add('hidden'));
                if (map[state]) {
                    map[state].classList.remove('hidden');
                    if (state !== 'loading') {
                        setTimeout(() => map[state]?.classList.add('hidden'), 1800);
                    }
                }
            }

            const inflight = new Set();

            async function saveField(element) {
                if (!element.classList?.contains('outdoor-field')) return;
                if (inflight.has(element)) return;
                inflight.add(element);

                const {
                    tr,
                    id: trackingId,
                    scope,
                    mf: masterFileId,
                    oi: outdoorItemId
                } = findRowContext(element);
                const fieldName = element.dataset.field || element.name || '';
                const fieldValue = normalizeValue(element);
                const {
                    year,
                    month
                } = getYM();

                if (!fieldName) {
                    console.error('Missing field name', element);
                    inflight.delete(element);
                    return;
                }

                console.log('Save attempt:', {
                    scope,
                    trackingId,
                    masterFileId,
                    outdoorItemId,
                    fieldName,
                    fieldValue
                });

                // Add subtle visual feedback
                element.classList.add('ring-1', 'ring-[#4bbbed]/20');

                // Build payload by scope
                let payload = null;

                if (scope === 'omd') {
                    // Month mode: always upsert into OMD using (oi, year, month, field)
                    if (!outdoorItemId) {
                        console.error('Missing outdoor_item_id (data-oi) for month scope', {
                            element
                        });
                        inflight.delete(element);
                        return;
                    }
                    if (month === null || year === null) {
                        console.warn('Month scope without concrete month/year, falling back to baseline');
                        // Fall back to baseline mode
                        if (trackingId) {
                            payload = {
                                id: trackingId,
                                field: fieldName,
                                value: fieldValue
                            };
                        } else if (masterFileId) {
                            payload = {
                                master_file_id: masterFileId,
                                ...(outdoorItemId ? {
                                    outdoor_item_id: outdoorItemId
                                } : {}),
                                field: fieldName,
                                value: fieldValue
                            };
                        } else {
                            console.error('Cannot create baseline record without master_file_id');
                            inflight.delete(element);
                            return;
                        }
                    } else {
                        payload = {
                            master_file_id: masterFileId,
                            outdoor_item_id: outdoorItemId,
                            year,
                            month,
                            field: fieldName,
                            value: fieldValue
                        };
                    }
                } else {
                    // Baseline (All Months): write/read from OCT
                    if (trackingId) {
                        payload = {
                            id: trackingId,
                            field: fieldName,
                            value: fieldValue
                        };
                    } else {
                        if (!masterFileId) {
                            console.error('Missing master_file_id (data-mf) for baseline create', {
                                element
                            });
                            inflight.delete(element);
                            return;
                        }
                        payload = {
                            master_file_id: masterFileId,
                            ...(outdoorItemId ? {
                                outdoor_item_id: outdoorItemId
                            } : {}),
                            field: fieldName,
                            value: fieldValue
                        };
                    }
                }

                showSaveIndicator(element, 'loading');

                try {
                    const res = await fetch(`{{ route('coordinator.outdoor.upsert') }}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify(payload)
                    });

                    const text = await res.text();
                    let data;
                    try {
                        data = JSON.parse(text);
                    } catch {
                        data = {
                            success: false,
                            error: text
                        };
                    }

                    if (!res.ok || data.success === false) {
                        let msg = res.status + ' ' + res.statusText;
                        if (data?.error) msg += ` – ${data.error}`;
                        console.error('Save failed:', msg, {
                            payload
                        });
                        showSaveIndicator(element, 'error');
                        element.classList.add('border-[#d33831]', 'bg-red-50');
                        setTimeout(() => {
                            element.classList.remove('border-[#d33831]', 'bg-red-50');
                        }, 2000);
                        return;
                    }

                    // Success feedback
                    const newId = data.data?.tracking_id || data.tracking_id || data.id || null;
                    if (!trackingId && newId) {
                        tr?.setAttribute('data-id', newId);
                        tr?.querySelectorAll('.outdoor-field').forEach(inp => inp.setAttribute('data-id',
                            newId));
                        if (!tr?.dataset.scope) tr?.setAttribute('data-scope', scope);
                    }

                    showSaveIndicator(element, 'success');
                    element.classList.add('border-green-400', 'bg-green-50');
                    setTimeout(() => {
                        element.classList.remove('border-green-400', 'bg-green-50');
                    }, 1500);

                } catch (err) {
                    console.error('Save error', err);
                    showSaveIndicator(element, 'error');
                    element.classList.add('border-[#d33831]', 'bg-red-50');
                    setTimeout(() => {
                        element.classList.remove('border-[#d33831]', 'bg-red-50');
                    }, 2000);
                } finally {
                    element.classList.remove('ring-1', 'ring-[#4bbbed]/20');
                    inflight.delete(element);
                }
            }

            document.addEventListener('blur', e => saveField(e.target), true);
            document.addEventListener('change', e => saveField(e.target));

            // Disable "Active this month" when Month = All
            const selMonth = document.getElementById('filterMonth');
            const togActive = document.getElementById('toggleActive');

            function syncActiveToggle() {
                const m = selMonth ? selMonth.value : (document.getElementById('ctxMonth')?.value ?? '');
                const hasMonth = m !== '';
                if (togActive) {
                    togActive.disabled = !hasMonth;
                    if (!hasMonth) togActive.checked = false;
                }
            }
            selMonth?.addEventListener('change', syncActiveToggle);
            syncActiveToggle();

            // Add subtle hover effects to input fields
            document.querySelectorAll('.field-input').forEach(input => {
                input.addEventListener('focus', function() {
                    this.style.transform = 'translateY(-1px)';
                    this.style.boxShadow = '0 4px 6px -1px rgba(0, 0, 0, 0.1)';
                });

                input.addEventListener('blur', function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = 'none';
                });
            });

            // Mini Pagination Script
            (function() {
                const PAGE_SIZE = 15; // change to 20/25 if you prefer
                const tbody = document.getElementById('outdoorTbody');
                if (!tbody) return;

                const rows = Array.from(tbody.querySelectorAll('tr[data-idx]'));
                const totalRows = rows.length;
                const totalPages = Math.max(1, Math.ceil(totalRows / PAGE_SIZE));

                const prevBtn = document.getElementById('prevPage');
                const nextBtn = document.getElementById('nextPage');
                const pageInfo = document.getElementById('pageInfo');
                const rowsPerEl = document.getElementById('rowsPerPage');

                if (rowsPerEl) rowsPerEl.textContent = String(PAGE_SIZE);

                let page = 1;

                function render() {
                    const start = (page - 1) * PAGE_SIZE;
                    const end = start + PAGE_SIZE;

                    for (let i = 0; i < rows.length; i++) {
                        rows[i].style.display = (i >= start && i < end) ? '' : 'none';
                    }

                    if (pageInfo) pageInfo.textContent = `${page} / ${totalPages}`;
                    if (prevBtn) prevBtn.disabled = page <= 1;
                    if (nextBtn) nextBtn.disabled = page >= totalPages;
                }

                prevBtn?.addEventListener('click', () => {
                    if (page > 1) {
                        page--;
                        render();
                    }
                });

                nextBtn?.addEventListener('click', () => {
                    if (page < totalPages) {
                        page++;
                        render();
                    }
                });

                // Initial paint
                render();
            })();
        });
    </script>
@endpush
