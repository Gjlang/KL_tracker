@extends('layouts.app')

@section('title', 'All-in-One Summary Report')

@push('styles')
<style>
    body { background-color: #F7F7F9; }
    .ink { color: #1C1E26; }
    .hairline { border-color: #EAEAEA; border-width: 1px; }
    .caps-label { letter-spacing: .06em; text-transform: uppercase; font-size: 11px; color: #6B7280; }
    .tabular-nums { font-variant-numeric: tabular-nums; }

    @media print {
        body { background: #fff !important; }
        .no-print { display: none !important; }
        .card { box-shadow: none !important; border-color: #ddd !important; }
        .page-break { page-break-after: always; }
        * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }

    .progress-fill {
        transition: width 0.3s ease;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen" x-data="{ downloading: false, printing: false }">
    {{-- Top Bar --}}
    <div class="bg-white border-b hairline">
        <div class="max-w-7xl mx-auto px-6 py-6">
            <div class="flex items-start justify-between">
                <div class="space-y-3">
                    <h1 class="text-3xl font-serif font-medium ink">All-in-One Summary Report</h1>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="caps-label">Filters:</span>
                        <x-chip label="Year" value="{{ $filters['year'] }}" />
                        @if(!empty($filters['month']))
                            <x-chip label="Month" value="{{ $filters['month'] }}" />
                        @endif
                        @if(!empty($filters['status']))
                            <x-chip label="Status" value="{{ $filters['status'] }}" />
                        @endif
                    </div>
                </div>

                <div class="flex items-center gap-3 no-print">
                    <a href="{{ route('report.summary.pdf', request()->only(['year','month','status'])) }}"
                       class="inline-flex items-center px-4 py-2 rounded-full text-white text-sm font-medium transition-colors focus:ring-2 focus:ring-[#4bbbed] focus:outline-none"
                       style="background-color: #22255b;"
                       x-on:click="downloading = true"
                       x-bind:aria-busy="downloading"
                       x-bind:disabled="downloading">
                        <span x-show="!downloading">Download PDF</span>
                        <span x-show="downloading" class="flex items-center gap-2">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Downloading...
                        </span>
                    </a>

                    <button onclick="window.print()"
                            class="inline-flex items-center px-4 py-2 rounded-full border border-neutral-300 text-sm font-medium transition-colors hover:bg-neutral-50 focus:ring-2 focus:ring-[#4bbbed] focus:outline-none ink"
                            x-on:click="printing = true; setTimeout(() => printing = false, 1000)"
                            x-bind:aria-busy="printing"
                            x-bind:disabled="printing">
                        <span x-show="!printing">Print Now</span>
                        <span x-show="printing">Printing...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 py-8 space-y-8">
        {{-- KPI Deck --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <x-kpi
                :value="$master['active_companies'] ?? 0"
                label="Active Companies"
                sublabel="distinct in {{ $filters['year'] }}"
            />

            <x-kpi
                :value="$outdoor['active_jobs'] ?? 0"
                label="Outdoor Jobs"
                sublabel="currently active"
                :available="$outdoor['available'] ?? false"
            />

            <x-kpi
                :value="($outdoor['completion_rate'] ?? 0) . '%'"
                label="Completion Rate"
                sublabel="this period"
                :available="$outdoor['available'] ?? false"
            />

            <x-kpi
                :value="$outdoor['issues'] ?? 0"
                label="Pending Issues"
                sublabel="needs attention"
                :available="$outdoor['available'] ?? false"
                accent="text-[#d33831]"
            />
        </div>

        {{-- Two-Column Content --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- Left Column --}}
            <div class="space-y-8">
                {{-- Master File --}}
                <x-card title="Master File" class="space-y-6">
                    <div>
                        <div class="text-4xl font-semibold tabular-nums ink mb-1">{{ $master['active_companies'] ?? 0 }}</div>
                        <p class="text-sm text-neutral-600">Active companies (distinct in {{ $filters['year'] }})</p>
                    </div>

                    <div>
                        <h3 class="font-medium ink mb-3">By Category</h3>
                        <div class="space-y-2">
                            @forelse($master['by_category'] ?? [] as $row)
                                <div class="flex justify-between items-center py-1">
                                    <span class="text-sm text-neutral-700">{{ $row['category'] ?? 'Unknown' }}</span>
                                    <span class="text-sm font-medium tabular-nums ink">{{ $row['total'] }}</span>
                                </div>
                            @empty
                                <p class="text-sm text-neutral-400">No category data available</p>
                            @endforelse
                        </div>
                    </div>

                    <div>
                        <h3 class="font-medium ink mb-3">Status Distribution</h3>
                        @php
                            $sd = $master['status_dist'] ?? [];
                            $total = array_sum($sd);
                            $pending = $sd['pending'] ?? 0;
                            $inProgress = $sd['in-progress'] ?? 0;
                            $completed = $sd['completed'] ?? 0;
                        @endphp

                        @if($total > 0)
                            <div class="space-y-3">
                                <div class="h-2 rounded-full bg-neutral-100 overflow-hidden">
                                    <div class="h-full flex">
                                        <div class="bg-amber-400 progress-fill" style="width: {{ ($pending / $total) * 100 }}%"></div>
                                        <div class="bg-blue-400 progress-fill" style="width: {{ ($inProgress / $total) * 100 }}%"></div>
                                        <div class="bg-green-400 progress-fill" style="width: {{ ($completed / $total) * 100 }}%"></div>
                                    </div>
                                </div>
                                <div class="flex justify-between text-xs">
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 rounded-full bg-amber-400"></div>
                                        <span class="text-neutral-600">Pending ({{ round(($pending / $total) * 100) }}%)</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 rounded-full bg-blue-400"></div>
                                        <span class="text-neutral-600">In-progress ({{ round(($inProgress / $total) * 100) }}%)</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 rounded-full bg-green-400"></div>
                                        <span class="text-neutral-600">Completed ({{ round(($completed / $total) * 100) }}%)</span>
                                    </div>
                                </div>
                            </div>
                        @else
                            <p class="text-sm text-neutral-400">No status data available</p>
                        @endif
                    </div>
                </x-card>

                {{-- KLTG --}}
                <x-card title="KLTG" class="space-y-4">
                    @if(($kltg['available'] ?? false) === false)
                        <p class="text-neutral-400 text-center py-8">Table not found</p>
                    @else
                        <div class="space-y-4">
                            <div>
                                <div class="flex justify-between items-end mb-2">
                                    <span class="text-sm text-neutral-600">Production Progress</span>
                                    <span class="text-2xl font-semibold tabular-nums ink">{{ $kltg['production_progress'] }}%</span>
                                </div>
                                <x-progress :value="$kltg['production_progress']" color="bg-[#4bbbed]" />
                            </div>

                            <div class="grid grid-cols-2 gap-4 pt-2">
                                <div class="text-center">
                                    <div class="text-lg font-medium tabular-nums ink">{{ $kltg['slots_filled'] }}</div>
                                    <div class="text-xs text-neutral-600">Slots Filled</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-lg font-medium tabular-nums ink">{{ $kltg['pending_approvals'] }}</div>
                                    <div class="text-xs text-neutral-600">Pending Approvals</div>
                                </div>
                            </div>
                        </div>
                    @endif
                </x-card>
            </div>

            {{-- Right Column --}}
            <div class="space-y-8">
                {{-- Outdoor --}}
                <x-card title="Outdoor" class="space-y-4">
                    @if(($outdoor['available'] ?? false) === false)
                        <p class="text-neutral-400 text-center py-8">Table not found</p>
                    @else
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-neutral-600">Active Jobs</span>
                                <span class="text-2xl font-semibold tabular-nums ink">{{ $outdoor['active_jobs'] }}</span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-sm text-neutral-600">Completed (period)</span>
                                <span class="font-medium tabular-nums ink">{{ $outdoor['completed_this'] }}</span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-sm text-neutral-600">Completion Rate</span>
                                <span class="font-medium tabular-nums ink">{{ $outdoor['completion_rate'] }}%</span>
                            </div>

                            <div class="flex justify-between items-center pt-2 border-t hairline">
                                <span class="text-sm text-neutral-600">Issues (pending/in-progress)</span>
                                <span class="font-medium tabular-nums text-[#d33831]">{{ $outdoor['issues'] }}</span>
                            </div>
                        </div>
                    @endif
                </x-card>

                {{-- Media Social --}}
                <x-card title="Media Social" class="space-y-4">
                    @if(($media['available'] ?? false) === false)
                        <p class="text-neutral-400 text-center py-8">Table not found</p>
                    @else
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-neutral-600">Campaigns</span>
                                <span class="text-2xl font-semibold tabular-nums ink">{{ $media['campaigns'] }}</span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-sm text-neutral-600">Posts (period)</span>
                                <span class="font-medium tabular-nums ink">{{ $media['posts'] }}</span>
                            </div>
                        </div>
                    @endif
                </x-card>
            </div>
        </div>

        {{-- Footer --}}
        <div class="text-center">
            <p class="text-xs text-neutral-400">
                Generated at {{ $generated->timezone(config('app.timezone'))->format('M d, Y H:i') }}
            </p>
        </div>
    </div>
</div>

@push('scripts')
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush
@endsection
