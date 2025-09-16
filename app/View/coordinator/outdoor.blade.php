<x-app-layout>
    <div class="flex h-screen bg-gray-50">
        <!-- Main content -->
        <div class="flex flex-col w-0 flex-1 overflow-hidden">
            <!-- Header -->
            <div class="relative z-10 flex-shrink-0 flex h-16 bg-white shadow md:hidden">
                <button type="button" class="border-r border-gray-200 px-4 text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500 md:hidden">
                    <span class="sr-only">Open sidebar</span>
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                    </svg>
                </button>
                <div class="flex-1 px-4 flex justify-between">
                    <div class="flex-1 flex">
                        <div class="w-full flex md:ml-0">
                            <div class="relative w-full text-gray-400 focus-within:text-gray-600">
                                <div class="absolute inset-y-0 left-0 flex items-center pointer-events-none">
                                    <h2 class="font-semibold text-xl text-gray-800 leading-tight ml-3">
                                        {{ __('Outdoor Coordinator') }}
                                    </h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content area -->
            <main class="flex-1 relative overflow-y-auto focus:outline-none">
                <div class="py-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <!-- Header for desktop -->
                        <div class="hidden md:block mb-8">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h2 class="font-semibold text-3xl text-gray-900 leading-tight flex items-center">
                                        🏗️ Outdoor Coordinator Dashboard
                                    </h2>
                                    <p class="text-gray-600 mt-2">Manage outdoor advertising projects and installations</p>
                                </div>
                                <a href="{{ route('dashboard') }}"
                                   class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                    </svg>
                                    Back to Dashboard
                                </a>
                            </div>
                        </div>

                        <!-- Stats Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                            <!-- Total Outdoor Jobs -->
                            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                                <div class="p-6">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <div class="flex items-center justify-center h-10 w-10 bg-yellow-500 rounded-md">
                                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="ml-5 w-0 flex-1">
                                            <dl>
                                                <dt class="text-sm font-medium text-gray-500 truncate">Total Outdoor</dt>
                                                <dd class="text-lg font-medium text-gray-900">{{ $totalOutdoor ?? 0 }}</dd>
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Completed -->
                            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                                <div class="p-6">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <div class="flex items-center justify-center h-10 w-10 bg-green-500 rounded-md">
                                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="ml-5 w-0 flex-1">
                                            <dl>
                                                <dt class="text-sm font-medium text-gray-500 truncate">Completed</dt>
                                                <dd class="text-lg font-medium text-gray-900">{{ $completedOutdoor ?? 0 }}</dd>
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Ongoing -->
                            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                                <div class="p-6">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <div class="flex items-center justify-center h-10 w-10 bg-orange-500 rounded-md">
                                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="ml-5 w-0 flex-1">
                                            <dl>
                                                <dt class="text-sm font-medium text-gray-500 truncate">Ongoing</dt>
                                                <dd class="text-lg font-medium text-gray-900">{{ $ongoingOutdoor ?? 0 }}</dd>
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Pending -->
                            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                                <div class="p-6">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <div class="flex items-center justify-center h-10 w-10 bg-red-500 rounded-md">
                                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="ml-5 w-0 flex-1">
                                            <dl>
                                                <dt class="text-sm font-medium text-gray-500 truncate">Pending</dt>
                                                <dd class="text-lg font-medium text-gray-900">{{ $pendingOutdoor ?? 0 }}</dd>
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Outdoor Jobs Table -->
                        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                            <div class="p-6">
                                <div class="flex justify-between items-center mb-6">
                                    <div>
                                        <h3 class="text-xl font-semibold text-gray-900">Outdoor Projects</h3>
                                        <p class="text-sm text-gray-600 mt-1">
                                            HM, TB, TTM, BB, Star, Flyers, Bunting, Signages, Newspaper
                                        </p>
                                    </div>
                                    <div class="flex space-x-3">
                                        <button onclick="exportOutdoorData()"
                                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3"></path>
                                            </svg>
                                            Export CSV
                                        </button>
                                        <a href="{{ route('masterfile.create') }}"
                                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 transition-colors">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            Add New Project
                                        </a>
                                    </div>
                                </div>

                                <!-- Table -->
                                <div class="shadow-sm rounded-lg border border-gray-200 overflow-hidden">
                                    <div class="overflow-x-auto">
                                        <div style="max-height: 600px; overflow-y: auto;">
                                            <table class="min-w-full table-auto divide-y divide-gray-200">
                                                <thead class="bg-yellow-50 sticky top-0 z-10">
                                                    <tr>
                                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-b border-gray-300">ID</th>
                                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-b border-gray-300">Date</th>
                                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-b border-gray-300">Company</th>
                                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-b border-gray-300">Product</th>
                                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-b border-gray-300">Location</th>
                                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-b border-gray-300">Duration</th>
                                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-b border-gray-300">Status</th>
                                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-b border-gray-300">Client</th>
                                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-b border-gray-300">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white divide-y divide-gray-200">
                                                    @if(isset($outdoorJobs) && $outdoorJobs->count() > 0)
                                                        @foreach($outdoorJobs as $index => $job)
                                                            <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-yellow-50 transition-colors duration-200">
                                                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $job->id }}</td>
                                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $job->date ? \Carbon\Carbon::parse($job->date)->format('M d, Y') : '-' }}</td>
                                                                <td class="px-4 py-3 text-sm text-gray-900">
                                                                    <a href="{{ route('masterfile.show', $job->id) }}" class="text-yellow-600 hover:text-yellow-800 hover:underline transition-colors duration-150 font-medium">
                                                                        <div class="max-w-[200px] truncate" title="{{ $job->company }}">{{ $job->company }}</div>
                                                                    </a>
                                                                </td>
                                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                                                    <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                                                        {{ $job->product }}
                                                                    </span>
                                                                </td>
                                                                <td class="px-4 py-3 text-sm text-gray-900">
                                                                    <div class="max-w-[150px] truncate" title="{{ $job->location }}">{{ $job->location ?? '-' }}</div>
                                                                </td>
                                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $job->duration ?? '-' }}</td>
                                                                <td class="px-4 py-3 whitespace-nowrap">
                                                                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full
                                                                        {{ $job->status === 'completed' ? 'bg-green-100 text-green-800' :
                                                                           ($job->status === 'ongoing' ? 'bg-orange-100 text-orange-800' : 'bg-red-100 text-red-800') }}">
                                                                        {{ ucfirst($job->status) }}
                                                                    </span>
                                                                </td>
                                                                <td class="px-4 py-3 text-sm text-gray-900">
                                                                    <div class="max-w-[120px] truncate" title="{{ $job->client }}">{{ $job->client ?? '-' }}</div>
                                                                </td>
                                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                                                    <div class="flex space-x-2">
                                                                        <a href="{{ route('masterfile.show', $job->id) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                                                        <span class="text-gray-300">|</span>
                                                                        <a href="{{ route('masterfile.show', $job->id) }}" class="text-yellow-600 hover:text-yellow-900">Edit</a>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td colspan="9" class="px-6 py-16 text-center text-gray-500">
                                                                <div class="flex flex-col items-center">
                                                                    <svg class="w-20 h-20 text-gray-400 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                                    </svg>
                                                                    <h4 class="text-lg font-medium text-gray-900 mb-2">No outdoor projects found</h4>
                                                                    <p class="text-gray-600 mb-6">Get started by adding your first outdoor advertising project.</p>
                                                                    <div class="flex space-x-3">
                                                                        <a href="{{ route('masterfile.create') }}" class="bg-yellow-600 text-white px-6 py-3 rounded-md hover:bg-yellow-700 text-sm transition-colors">
                                                                            Add New Project
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Pagination -->
                                @if(isset($outdoorJobs) && method_exists($outdoorJobs, 'links'))
                                    <div class="mt-6">
                                        {{ $outdoorJobs->links() }}
                                    </div>
                                @endif

                                <!-- Table Info -->
                                @if(isset($outdoorJobs) && $outdoorJobs->count() > 0)
                                    <div class="mt-6 flex justify-between items-center text-sm text-gray-600 bg-yellow-50 px-6 py-4 rounded-lg border border-yellow-200">
                                        <div class="flex items-center space-x-6">
                                            <span>Showing <strong>{{ $outdoorJobs->count() }}</strong> of <strong>{{ method_exists($outdoorJobs, 'total') ? $outdoorJobs->total() : $outdoorJobs->count() }}</strong> outdoor projects</span>
                                            @if(method_exists($outdoorJobs, 'currentPage') && method_exists($outdoorJobs, 'lastPage'))
                                                <span>•</span>
                                                <span>Page <strong>{{ $outdoorJobs->currentPage() }}</strong> of <strong>{{ $outdoorJobs->lastPage() }}</strong></span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            Outdoor advertising projects
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Progress Overview -->
                        <div class="mt-8 bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Outdoor Progress Overview</h3>

                                @if($totalOutdoor > 0)
                                    <div class="space-y-4">
                                        <!-- Progress Bar -->
                                        <div>
                                            <div class="flex justify-between text-sm text-gray-600 mb-1">
                                                <span>Completion Rate</span>
                                                <span>{{ round(($completedOutdoor / $totalOutdoor) * 100, 1) }}%</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ ($completedOutdoor / $totalOutdoor) * 100 }}%"></div>
                                            </div>
                                        </div>

                                        <!-- Status Breakdown Chart -->
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div class="text-center p-4 bg-green-50 rounded-lg border border-green-200">
                                                <div class="text-3xl font-bold text-green-600">{{ $completedOutdoor }}</div>
                                                <div class="text-sm text-green-800 font-medium">Completed Projects</div>
                                                <div class="text-xs text-green-600 mt-1">{{ $totalOutdoor > 0 ? round(($completedOutdoor / $totalOutdoor) * 100, 1) : 0 }}%</div>
                                            </div>
                                            <div class="text-center p-4 bg-orange-50 rounded-lg border border-orange-200">
                                                <div class="text-3xl font-bold text-orange-600">{{ $ongoingOutdoor }}</div>
                                                <div class="text-sm text-orange-800 font-medium">In Progress</div>
                                                <div class="text-xs text-orange-600 mt-1">{{ $totalOutdoor > 0 ? round(($ongoingOutdoor / $totalOutdoor) * 100, 1) : 0 }}%</div>
                                            </div>
                                            <div class="text-center p-4 bg-red-50 rounded-lg border border-red-200">
                                                <div class="text-3xl font-bold text-red-600">{{ $pendingOutdoor }}</div>
                                                <div class="text-sm text-red-800 font-medium">Pending</div>
                                                <div class="text-xs text-red-600 mt-1">{{ $totalOutdoor > 0 ? round(($pendingOutdoor / $totalOutdoor) * 100, 1) : 0 }}%</div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center py-8">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900">No outdoor projects yet</h3>
                                        <p class="mt-1 text-sm text-gray-500">Get started by creating your first outdoor advertising project.</p>
                                        <div class="mt-6">
                                            <a href="{{ route('masterfile.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700">
                                                Add New Project
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    @push('scripts')
    <script>
        function exportOutdoorData() {
            // Create a simple CSV export for outdoor data
            window.location.href = '{{ route("masterfile.export") }}?category=outdoor';
        }
    </script>
    @endpush
</x-app-layout>
