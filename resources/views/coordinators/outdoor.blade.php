<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-4 md:p-8">
        <!-- Mobile Header -->
        <div class="relative z-10 flex-shrink-0 flex h-16 bg-white rounded-2xl shadow-sm border border-gray-100 mb-4 md:hidden">
            <button type="button" class="border-r border-gray-200 px-4 text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500 md:hidden">
                <span class="sr-only">Open sidebar</span>
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                </svg>
            </button>
            <div class="flex-1 px-4 flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Outdoor Coordinator') }}
                </h2>
            </div>
        </div>

        <div class="max-w-full mx-auto">
            <!-- Desktop Header -->
            <div class="hidden md:flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Outdoor Coordinator</h1>
                    <p class="text-gray-600 mt-1">Manage and track your outdoor advertising projects</p>
                </div>
                <a href="{{ route('dashboard.outdoor') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-xl shadow-sm hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Monthly
                </a>
            </div>

            <!-- Success Messages -->
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('info'))
                <div class="bg-blue-50 border border-blue-200 text-blue-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ session('info') }}
                </div>
            @endif

            <!-- Filters and Actions -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                    <!-- Filters -->
                    <div class="flex-1">
                        <form method="get" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Month</label>
                                <select name="month" class="w-full rounded-xl border border-gray-200 bg-gray-50 hover:bg-white px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors">
                                    <option value="">All Months</option>
                                    @if(isset($months))
                                        @foreach($months as $m)
                                            <option value="{{ $m['value'] }}" @selected(($month ?? '')==$m['value'])>{{ $m['label'] }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select name="status" class="w-full rounded-xl border border-gray-200 bg-gray-50 hover:bg-white px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors">
                                    <option value="">All Status</option>
                                    <option value="Pending" @selected(($status ?? '')==='Pending')>Pending</option>
                                    <option value="Ongoing" @selected(($status ?? '')==='Ongoing')>Ongoing</option>
                                    <option value="Completed" @selected(($status ?? '')==='Completed')>Completed</option>
                                </select>
                            </div>

                            <div class="flex items-end">
                                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 text-white px-6 py-3 text-sm font-medium shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                                    </svg>
                                    Apply Filters
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-3">
                        <button onclick="exportOutdoorData()"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-xl shadow-sm hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3"></path>
                            </svg>
                            Export CSV
                        </button>
                    </div>
                </div>
            </div>

            <!-- Enhanced Table -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900">Coordinator List — Outdoor</h3>
                            <p class="text-sm text-gray-600 mt-1">Track progress across all outdoor advertising projects</p>
                        </div>
                        <div class="text-sm text-gray-500">
                            Auto-save enabled
                            <span class="inline-block w-2 h-2 bg-green-400 rounded-full ml-2"></span>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="sticky top-0 z-20 bg-gradient-to-r from-gray-50 to-gray-100">
                            <tr>
                                <th class="sticky left-0 z-30 bg-inherit border-r border-gray-200 px-4 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wide border-b border-gray-200 min-w-[80px] w-[80px] text-center">ID</th>
                                <th class="sticky left-[80px] z-30 bg-inherit border-r border-gray-200 px-4 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wide border-b border-gray-200 min-w-[200px]">Company</th>
                                <th class="sticky left-[280px] z-30 bg-inherit border-r border-gray-200 px-4 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wide border-b border-gray-200 min-w-[200px]">Person In Charge</th>
                                <th class="sticky left-[480px] z-30 bg-inherit border-r border-gray-200 px-4 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wide border-b border-gray-200 min-w-[180px]">Product</th>
                                <th class="px-4 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wide border-b border-gray-200 min-w-[180px]">Site</th>
                                <th class="px-4 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wide border-b border-gray-200 min-w-[160px]">Payment</th>
                                <th class="px-4 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wide border-b border-gray-200 min-w-[160px]">Material</th>
                                <th class="px-4 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wide border-b border-gray-200 min-w-[160px]">Artwork</th>
                                <th class="px-4 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wide border-b border-gray-200 min-w-[160px]">Approval</th>
                                <th class="px-4 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wide border-b border-gray-200 min-w-[160px]">Sent</th>
                                <th class="px-4 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wide border-b border-gray-200 min-w-[160px]">Collected</th>
                                <th class="px-4 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wide border-b border-gray-200 min-w-[160px]">Install</th>
                                <th class="px-4 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wide border-b border-gray-200 min-w-[160px]">Dismantle</th>
                                <th class="px-4 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wide border-b border-gray-200 min-w-[160px]">Status</th>
                                <th class="px-4 py-4 text-xs font-semibold text-gray-700 uppercase tracking-wide border-b border-gray-200 min-w-[120px]">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @if(isset($rows) && $rows->count() > 0)
                            @foreach($rows as $i => $row)
                                @php $mf = $row->masterFile; @endphp
                                <tr class="odd:bg-white even:bg-gray-50 hover:bg-blue-50/50 transition-colors">
                                    <td class="sticky left-0 z-30 bg-inherit border-r border-gray-200 px-4 py-4 align-middle border-b border-gray-100 text-center font-medium text-gray-900">{{ $rows->firstItem() + $i }}</td>

                                    {{-- Company (read-only) --}}
                                    <td class="sticky left-[80px] z-30 bg-inherit border-r border-gray-200 px-4 py-4 align-middle border-b border-gray-100">
                                        <div class="font-medium text-gray-900 bg-gray-50 rounded-lg px-3 py-2">
                                            {{ $mf?->company ?? $row->company_snapshot }}
                                        </div>
                                    </td>

                                    {{-- Client (read-only from master_files) --}}
                                    <td class="sticky left-[280px] z-30 bg-inherit border-r border-gray-200 px-4 py-4 align-middle border-b border-gray-100">
                                        <div class="text-gray-700 bg-gray-50 rounded-lg px-3 py-2">
                                            {{ $mf?->client }}
                                        </div>
                                    </td>

                                    {{-- Product (read-only) --}}
                                    <td class="sticky left-[480px] z-30 bg-inherit border-r border-gray-200 px-4 py-4 align-middle border-b border-gray-100">
                                        <div class="text-gray-700 bg-gray-50 rounded-lg px-3 py-2">
                                            {{ $mf?->product ?? $row->product_snapshot }}
                                        </div>
                                    </td>

                                    @foreach (['site','payment','material','artwork','approval','sent','collected','install','dismantle','status'] as $col)
                                        <td class="px-4 py-4 align-middle border-b border-gray-100">
                                            <div class="relative">
                                                <input type="text"
                                                       class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 hover:border-gray-300"
                                                       value="{{ $row->$col }}"
                                                       data-id="{{ $row->id }}" name="{{ $col }}">
                                                <div class="absolute right-2 top-1/2 transform -translate-y-1/2 hidden" data-save-indicator>
                                                    <svg class="animate-spin h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                </div>
                                                <div class="absolute right-2 top-1/2 transform -translate-y-1/2 hidden" data-save-success>
                                                    <svg class="h-4 w-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                                <div class="absolute right-2 top-1/2 transform -translate-y-1/2 hidden" data-save-error>
                                                    <svg class="h-4 w-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                        </td>
                                    @endforeach

                                    <td class="px-4 py-4 align-middle border-b border-gray-100">
                                        <form method="post" action="{{ route('coordinator.outdoor.destroy',$row->id) }}"
                                              onsubmit="return confirm('Delete this row?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-red-700 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 hover:border-red-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        @elseif(isset($outdoorJobs) && $outdoorJobs->count() > 0)
                            @foreach($outdoorJobs as $i => $row)
                                @php $mf = $row->masterFile; @endphp
                                <tr class="odd:bg-white even:bg-gray-50 hover:bg-blue-50/50 transition-colors duration-200">
                                    <td class="sticky left-0 z-30 bg-inherit border-r border-gray-200 px-4 py-4 align-middle border-b border-gray-100 text-center font-medium text-gray-900">{{ $i + 1 }}</td>

                                    {{-- Company (read-only) --}}
                                    <td class="sticky left-[80px] z-30 bg-inherit border-r border-gray-200 px-4 py-4 align-middle border-b border-gray-100">
                                        <div class="font-medium text-gray-900 bg-gray-50 rounded-lg px-3 py-2">
                                            {{ $mf?->company ?? $row->company_snapshot ?? '-' }}
                                        </div>
                                    </td>

                                    {{-- Client (read-only from master_files) --}}
                                    <td class="sticky left-[280px] z-30 bg-inherit border-r border-gray-200 px-4 py-4 align-middle border-b border-gray-100">
                                        <div class="text-gray-700 bg-gray-50 rounded-lg px-3 py-2">
                                            {{ $mf?->client ?? '-' }}
                                        </div>
                                    </td>

                                    {{-- Product (read-only) --}}
                                    <td class="sticky left-[480px] z-30 bg-inherit border-r border-gray-200 px-4 py-4 align-middle border-b border-gray-100">
                                        <div class="text-gray-700 bg-gray-50 rounded-lg px-3 py-2">
                                            {{ $mf?->product ?? $row->product_snapshot ?? '-' }}
                                        </div>
                                    </td>

                                    @foreach (['site','payment','material','artwork','approval','sent','collected','install','dismantle','status'] as $col)
                                        <td class="px-4 py-4 align-middle border-b border-gray-100">
                                            <div class="relative">
                                                <input type="text"
                                                       class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 hover:border-gray-300"
                                                       value="{{ $row->$col ?? '' }}"
                                                       data-id="{{ $row->id }}" name="{{ $col }}">
                                                <div class="absolute right-2 top-1/2 transform -translate-y-1/2 hidden" data-save-indicator>
                                                    <svg class="animate-spin h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                </div>
                                                <div class="absolute right-2 top-1/2 transform -translate-y-1/2 hidden" data-save-success>
                                                    <svg class="h-4 w-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                                <div class="absolute right-2 top-1/2 transform -translate-y-1/2 hidden" data-save-error>
                                                    <svg class="h-4 w-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                        </td>
                                    @endforeach

                                    <td class="px-4 py-4 align-middle border-b border-gray-100">
                                        <form method="post" action="{{ route('coordinator.outdoor.destroy',$row->id) }}"
                                              onsubmit="return confirm('Delete this row?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-red-700 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 hover:border-red-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="15" class="px-6 py-16 text-center text-gray-500">
                                    <div class="flex flex-col items-center max-w-sm mx-auto">
                                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                        </div>
                                        <h4 class="text-lg font-semibold text-gray-900 mb-2">No tracking records found</h4>
                                        <p class="text-gray-600 mb-8 text-center">Start tracking your outdoor advertising projects to monitor progress and deadlines.</p>
                                        <div class="flex flex-col sm:flex-row gap-3">
                                            <a href="{{ route('coordinator.outdoor.create') }}" class="inline-flex items-center justify-center gap-2 bg-indigo-600 text-white px-6 py-3 rounded-xl hover:bg-indigo-700 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                                Add New Tracking
                                            </a>
                                            @if(Route::has('coordinator.outdoor.seed'))
                                                <form method="POST" action="{{ route('coordinator.outdoor.seed') }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center justify-center gap-2 bg-green-600 text-white px-6 py-3 rounded-xl hover:bg-green-700 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                        </svg>
                                                        Sync from Master Files
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if(isset($rows) && method_exists($rows, 'links') && $rows->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $rows->links() }}
                    </div>
                @elseif(isset($outdoorJobs) && method_exists($outdoorJobs, 'links') && $outdoorJobs->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $outdoorJobs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Enhanced Autosave with Visual Feedback --}}
    <script>
    (function(){
        const token = '{{ csrf_token() }}';
        let timers = {};

        function showIndicator(input, type) {
            const parent = input.parentElement;
            const indicators = parent.querySelectorAll('[data-save-indicator], [data-save-success], [data-save-error]');
            indicators.forEach(el => el.classList.add('hidden'));

            const indicator = parent.querySelector(`[data-save-${type}]`);
            if (indicator) {
                indicator.classList.remove('hidden');
            }
        }

        function hideAllIndicators(input) {
            const parent = input.parentElement;
            const indicators = parent.querySelectorAll('[data-save-indicator], [data-save-success], [data-save-error]');
            indicators.forEach(el => el.classList.add('hidden'));
        }

        document.addEventListener('input', function(e){
            const el = e.target;
            if (!el.matches('input[name]')) return;

            const id = el.dataset.id;
            const fieldName = el.name;
            const key = `${id}-${fieldName}`;

            // Show loading indicator immediately
            showIndicator(el, 'indicator');

            // Clear existing timer for this field
            if (timers[key]) {
                clearTimeout(timers[key]);
            }

            // Set new timer
            timers[key] = setTimeout(() => {
                fetch(`{{ url('/coordinator/outdoor') }}/${id}`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ [el.name]: el.value })
                }).then(r => {
                    if(r.ok) {
                        showIndicator(el, 'success');
                        // Hide success indicator after 1.2 seconds
                        setTimeout(() => hideAllIndicators(el), 1200);
                    } else {
                        showIndicator(el, 'error');
                        console.error('Save failed', r.status);
                    }
                }).catch(err => {
                    showIndicator(el, 'error');
                    console.error('Save error:', err);
                });

                // Clean up timer reference
                delete timers[key];
            }, 400);
        }, {passive:true});
    })();
    </script>

    @push('scripts')
    <script>
        function exportOutdoorData() {
            // Check if export route exists
            @if(Route::has('coordinator.outdoor.export'))
                window.location.href = '{{ route("coordinator.outdoor.export") }}';
            @else
                alert('Export functionality is not yet implemented.');
            @endif
        }

        function exportOutdoorProjects() {
            // Check if export route exists
            @if(Route::has('masterfile.export'))
                window.location.href = '{{ route("masterfile.export") }}?category=outdoor';
            @else
                alert('Export functionality is not yet implemented.');
            @endif
        }

        // Auto-refresh functionality (optional)
        document.addEventListener('DOMContentLoaded', function() {
            // Add any additional JavaScript functionality here
            console.log('Enhanced Outdoor Coordinator Dashboard loaded successfully');

            // Initialize table enhancements
            const table = document.querySelector('table');
            if (table) {
                // Add smooth scrolling for horizontal scroll
                table.parentElement.style.scrollBehavior = 'smooth';
            }
        });
    </script>
    @endpush
</x-app-layout>
