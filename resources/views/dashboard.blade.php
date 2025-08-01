<x-app-layout>
    <div class="flex h-screen bg-gray-100">
        <!-- Main content -->
        <div class="flex flex-col w-0 flex-1 overflow-hidden">
            <!-- Mobile menu button -->
            <div class="relative z-10 flex-shrink-0 flex h-16 bg-white shadow md:hidden">
                <button type="button" class="border-r border-gray-200 px-4 text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500 md:hidden" id="open-sidebar">
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
                                        {{ __('Dashboard') }}
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
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                        <!-- Header for desktop -->
                        <div class="hidden md:block mb-6">
                            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                                {{ __('Dashboard') }}
                            </h2>
                        </div>

                        <!-- Original dashboard content -->
                        <div class="space-y-6">
                            <!-- Stats Cards -->
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                <!-- Total Jobs -->
                                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                    <div class="p-6">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <div class="flex items-center justify-center h-8 w-8 bg-indigo-500 rounded-md">
                                                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v6a2 2 0 002 2h6a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="ml-5 w-0 flex-1">
                                                <dl>
                                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Jobs</dt>
                                                    <dd class="text-lg font-medium text-gray-900">{{ $totalJobs ?? 0 }}</dd>
                                                </dl>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Completed Jobs -->
                                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                    <div class="p-6">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <div class="flex items-center justify-center h-8 w-8 bg-green-500 rounded-md">
                                                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="ml-5 w-0 flex-1">
                                                <dl>
                                                    <dt class="text-sm font-medium text-gray-500 truncate">Completed</dt>
                                                    <dd class="text-lg font-medium text-gray-900">{{ $completedJobs ?? 0 }}</dd>
                                                </dl>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Ongoing Jobs -->
                                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                    <div class="p-6">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <div class="flex items-center justify-center h-8 w-8 bg-yellow-500 rounded-md">
                                                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="ml-5 w-0 flex-1">
                                                <dl>
                                                    <dt class="text-sm font-medium text-gray-500 truncate">Ongoing</dt>
                                                    <dd class="text-lg font-medium text-gray-900">{{ $ongoingJobs ?? 0 }}</dd>
                                                </dl>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Pending Jobs -->
                                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                    <div class="p-6">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <div class="flex items-center justify-center h-8 w-8 bg-red-500 rounded-md">
                                                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="ml-5 w-0 flex-1">
                                                <dl>
                                                    <dt class="text-sm font-medium text-gray-500 truncate">Pending</dt>
                                                    <dd class="text-lg font-medium text-gray-900">{{ $pendingJobs ?? 0 }}</dd>
                                                </dl>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <!-- MasterFile Data - Large Table with All Records -->
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                <div class="p-6">
                                    <div class="flex justify-between items-center mb-6">
                                        <div>
                                            <h3 class="text-xl font-semibold text-gray-900">Master File Data</h3>
                                            <p class="text-sm text-gray-600 mt-1">
                                                @if(isset($masterFiles) && $masterFiles->count() > 0)
                                                    Showing {{ $masterFiles->count() }} records
                                                @else
                                                    No records found
                                                @endif
                                            </p>
                                        </div>
                                        <div class="flex space-x-3">
                                            <button onclick="document.getElementById('importModal').classList.remove('hidden')"
                                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3"></path>
                                                </svg>
                                                Import Data
                                            </button>

                                            <a href="{{ route('masterfile.export') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3"></path>
                                                </svg>
                                                Export All Data
                                            </a>
                                            <a href="{{ route('masterfile.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                                Add New
                                            </a>
                                        </div>
                                    </div>

                                    <!-- 🔍 Filter Form -->
                                    <form method="GET" action="{{ route('dashboard') }}" class="mb-6 bg-gray-50 p-4 rounded-lg">
                                        <div class="flex flex-wrap items-center space-x-4 space-y-2">
                                            <div class="flex-1 min-w-64">
                                                <input type="text" name="search" value="{{ request('search') }}"
                                                       placeholder="Search company, product, status, client, month..."
                                                       class="w-full border-gray-300 rounded-md px-4 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            </div>

                                            <select name="status" class="border-gray-300 rounded-md px-3 py-2 text-sm">
                                                <option value="">All Status</option>
                                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                                <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                            </select>

                                            <select name="month" class="border-gray-300 rounded-md px-3 py-2 text-sm">
                                                <option value="">All Months</option>
                                                @foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $m)
                                                    <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>{{ $m }}</option>
                                                @endforeach
                                            </select>

                                            <button type="submit" class="px-4 py-2 text-sm bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500">
                                                Filter
                                            </button>

                                            @if(request('search') || request('status') || request('month'))
                                                <a href="{{ route('dashboard') }}" class="px-4 py-2 text-sm bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                                    Clear
                                                </a>
                                            @endif
                                        </div>

                                        @if(request('search') || request('status') || request('month'))
                                            <div class="mt-2 text-sm text-gray-600">
                                                Showing filtered results
                                                @if(isset($masterFiles) && method_exists($masterFiles, 'total'))
                                                    ({{ $masterFiles->total() }} total)
                                                @endif
                                            </div>
                                        @endif
                                    </form>

                                    <!-- Large Scrollable Table Container -->
                                    <div class="border border-gray-200 rounded-lg">
                                        <div class="overflow-x-auto" style="max-height: 600px; overflow-y: auto;">
                                            <table class="min-w-full divide-y divide-gray-200">
                                                <thead class="bg-gray-50 sticky top-0 z-10">
                                                    <tr>
                                                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">ID</th>
                                                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Month</th>
                                                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Date</th>
                                                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Company</th>
                                                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Product</th>
                                                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Traffic</th>
                                                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Duration</th>
                                                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Status</th>
                                                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Client</th>
                                                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Date Finish</th>
                                                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Job Number</th>
                                                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Artwork</th>
                                                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Invoice Date</th>
                                                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Invoice Number</th>
                                                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Created</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white divide-y divide-gray-200">
                                                    @if(isset($masterFiles) && $masterFiles->count() > 0)
                                                        @foreach($masterFiles as $file)
                                                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $file->id }}</td>
                                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $file->month }}</td>
                                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $file->date }}</td>
                                                                <td class="px-6 py-4 text-sm text-gray-900" style="max-width: 200px;">
                                                                    <a href="{{ route('masterfile.show', $file->id) }}" class="text-blue-600 hover:text-blue-800 hover:underline transition-colors duration-150">
                                                                        <div class="truncate" title="{{ $file->company }}">{{ $file->company }}</div>
                                                                    </a>
                                                                </td>
                                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                                                        {{ $file->product }}
                                                                    </span>
                                                                </td>
                                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $file->traffic }}</td>
                                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $file->duration }}</td>
                                                                <td class="px-6 py-4 whitespace-nowrap">
                                                                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full
                                                                        {{ $file->status === 'completed' ? 'bg-green-100 text-green-800' :
                                                                           ($file->status === 'ongoing' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                                        {{ ucfirst($file->status) }}
                                                                    </span>
                                                                </td>
                                                                <td class="px-6 py-4 text-sm text-gray-900" style="max-width: 150px;">
                                                                    <div class="truncate" title="{{ $file->client }}">{{ $file->client }}</div>
                                                                </td>
                                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                                    {{ $file->date_finish ? $file->date_finish : '-' }}
                                                                </td>
                                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                                    {{ $file->job_number ? $file->job_number : '-' }}
                                                                </td>
                                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                                    {{ $file->artwork ? $file->artwork : '-' }}
                                                                </td>
                                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                                    {{ $file->invoice_date ? $file->invoice_date : '-' }}
                                                                </td>
                                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                                    {{ $file->invoice_number ? $file->invoice_number : '-' }}
                                                                </td>
                                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                                    {{ $file->created_at ? $file->created_at->format('M d, Y') : '-' }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td colspan="15" class="px-6 py-12 text-center text-gray-500">
                                                                <div class="flex flex-col items-center">
                                                                    <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                                    </svg>
                                                                    <h4 class="text-lg font-medium text-gray-900 mb-2">No master file data yet</h4>
                                                                    <p class="text-gray-600 mb-4">Get started by importing data or adding a new record</p>
                                                                    <div class="flex space-x-3">
                                                                        <button onclick="openImportModal()" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 text-sm">
                                                                            Import Data
                                                                        </button>
                                                                        <a href="{{ route('masterfile.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 text-sm">
                                                                            Add New Record
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

                                    <!-- Pagination (moved after table) -->
                                    @if(isset($masterFiles) && method_exists($masterFiles, 'links'))
                                        <div class="mt-4">
                                            {{ $masterFiles->links() }}
                                        </div>
                                    @endif

                                    <!-- Table Info -->
                                    @if(isset($masterFiles) && $masterFiles->count() > 0)
                                        <div class="mt-4 flex justify-between items-center text-sm text-gray-600">
                                            <div class="flex items-center space-x-4">
                                                <span>Total Records: <strong>{{ $masterFiles->count() }}</strong></span>
                                                <span>•</span>
                                                <span>Last Updated: <strong>{{ $masterFiles->first()->updated_at ? $masterFiles->first()->updated_at->format('M d, Y H:i') : 'N/A' }}</strong></span>
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                Scroll horizontally and vertically to view all data
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Progress Overview -->
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                <div class="p-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Monthly Progress</h3>

                                    @if($totalJobs > 0)
                                        <div class="space-y-4">
                                            <!-- Progress Bar -->
                                            <div>
                                                <div class="flex justify-between text-sm text-gray-600 mb-1">
                                                    <span>Completion Rate</span>
                                                    <span>{{ round(($completedJobs / $totalJobs) * 100, 1) }}%</span>
                                                </div>
                                                <div class="w-full bg-gray-200 rounded-full h-2">
                                                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ ($completedJobs / $totalJobs) * 100 }}%"></div>
                                                </div>
                                            </div>

                                            <!-- Status Breakdown -->
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                <div class="text-center p-3 bg-green-50 rounded-lg">
                                                    <div class="text-2xl font-bold text-green-600">{{ $completedJobs }}</div>
                                                    <div class="text-sm text-green-800">Completed</div>
                                                </div>
                                                <div class="text-center p-3 bg-yellow-50 rounded-lg">
                                                    <div class="text-2xl font-bold text-yellow-600">{{ $ongoingJobs }}</div>
                                                    <div class="text-sm text-yellow-800">In Progress</div>
                                                </div>
                                                <div class="text-center p-3 bg-red-50 rounded-lg">
                                                    <div class="text-2xl font-bold text-red-600">{{ $pendingJobs }}</div>
                                                    <div class="text-sm text-red-800">Pending</div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-center py-8">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v6a2 2 0 002 2h6a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                            <h3 class="mt-2 text-sm font-medium text-gray-900">No jobs yet</h3>
                                            <p class="mt-1 text-sm text-gray-500">Get started by creating your first job.</p>
                                            <div class="mt-6">
                                                <a href="{{ route('jobs.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                                    Add New Job
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- <!-- Recent Jobs -->
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                <div class="p-6">
                                    <div class="flex justify-between items-center mb-4">
                                        <h3 class="text-lg font-medium text-gray-900">Recent Jobs</h3>
                                        <a href="{{ route('jobs.index') }}" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                                            View all →
                                        </a>
                                    </div>

                                    @if(isset($recentJobs) && $recentJobs->count() > 0)
                                        <div class="space-y-3">
                                            @foreach($recentJobs as $job)
                                                <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                                                    <div class="flex-1">
                                                        <div class="flex items-center space-x-3">
                                                            <div>
                                                                <p class="text-sm font-medium text-gray-900">{{ $job->company_name ?? 'Company Name' }}</p>
                                                                <p class="text-sm text-gray-500">{{ $job->site_name ?? 'Site Name' }} - {{ $job->product ?? 'Product' }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center space-x-3">
                                                        <x-status-badge :status="$job->status ?? 'pending'" />
                                                        <span class="text-sm text-gray-500">{{ $job->created_at ? $job->created_at->format('M d') : 'Today' }}</span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <p class="text-sm text-gray-500">No recent jobs to display</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Quick Actions -->
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                <div class="p-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <a href="{{ route('jobs.index') }}" class="group relative bg-white p-4 border-2 border-gray-300 border-dashed rounded-lg hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            <div>
                                                <span class="rounded-lg inline-flex p-3 bg-indigo-50 text-indigo-600 group-hover:bg-indigo-100">
                                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="mt-4">
                                                <h3 class="text-lg font-medium text-gray-900">Add New Job</h3>
                                                <p class="mt-2 text-sm text-gray-500">Create a new job entry in the system</p>
                                            </div>
                                        </a>

                                        <a href="{{ route('calendar') }}" class="group relative bg-white p-4 border-2 border-gray-300 border-dashed rounded-lg hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            <div>
                                                <span class="rounded-lg inline-flex p-3 bg-green-50 text-green-600 group-hover:bg-green-100">
                                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2z" />
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="mt-4">
                                                <h3 class="text-lg font-medium text-gray-900">View Calendar</h3>
                                                <p class="mt-2 text-sm text-gray-500">See all jobs scheduled by date</p>
                                            </div>
                                        </a>

                                        <a href="{{ route('jobs.monthly') }}" class="group relative bg-white p-4 border-2 border-gray-300 border-dashed rounded-lg hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            <div>
                                                <span class="rounded-lg inline-flex p-3 bg-yellow-50 text-yellow-600 group-hover:bg-yellow-100">
                                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="mt-4">
                                                <h3 class="text-lg font-medium text-gray-900">Monthly Report</h3>
                                                <p class="mt-2 text-sm text-gray-500">View jobs filtered by month</p>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div> --}}

    <!-- Import Modal -->
    <div id="importModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Import Data</h3>
                <button onclick="closeImportModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form action="{{ route('masterfile.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label for="importFile" class="block text-sm font-medium text-gray-700 mb-2">Choose CSV/Excel File</label>
                    <input type="file" name="file" id="importFile" accept=".csv,.xlsx,.xls"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
                    <p class="mt-1 text-xs text-gray-500">Supported formats: CSV, XLSX, XLS (Max: 2MB)</p>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-3">
                    <div class="flex">
                        <svg class="w-5 h-5 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="text-sm text-yellow-700">
                            <p class="font-medium">Before importing:</p>
                            <ul class="mt-1 list-disc list-inside text-xs">
                                <li>Download the template to see required columns</li>
                                <li>Ensure your data matches the template format</li>
                                <li>Check that dates are in YYYY-MM-DD format</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="flex space-x-3">
                    <button type="button" onclick="closeImportModal()" class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        Import Data
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Mobile sidebar functionality
        const openSidebarBtn = document.getElementById('open-sidebar');
        const closeSidebarBtn = document.getElementById('close-sidebar');
        const mobileSidebar = document.getElementById('mobile-sidebar');
        const mobileSidebarOverlay = document.getElementById('mobile-sidebar-overlay');

        if (openSidebarBtn) {
            openSidebarBtn.addEventListener('click', function() {
                if (mobileSidebar) {
                    mobileSidebar.style.display = 'flex';
                }
            });
        }

        if (closeSidebarBtn) {
            closeSidebarBtn.addEventListener('click', function() {
                if (mobileSidebar) {
                    mobileSidebar.style.display = 'none';
                }
            });
        }

        if (mobileSidebarOverlay) {
            mobileSidebarOverlay.addEventListener('click', function() {
                if (mobileSidebar) {
                    mobileSidebar.style.display = 'none';
                }
            });
        }

        // Import modal functionality
        function openImportModal() {
            document.getElementById('importModal').classList.remove('hidden');
        }

        function closeImportModal() {
            document.getElementById('importModal').classList.add('hidden');
        }

        // Toggle confirmation section
        function toggleConfirmationSection() {
            const content = document.getElementById('confirmationContent');
            const toggleText = document.getElementById('toggleText');

            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                toggleText.textContent = 'Collapse';
            } else {
                content.classList.add('hidden');
                toggleText.textContent = 'Expand';
            }
        }

        // Close modal when clicking outside
        document.getElementById('importModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeImportModal();
            }
        });

        // Auto-save forms with debouncing
        const forms = document.querySelectorAll('.inline-form');
        forms.forEach(form => {
            const inputs = form.querySelectorAll('input[type="text"]');
            inputs.forEach(input => {
                let timeout;
                input.addEventListener('input', function() {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => {
                        // Add visual feedback
                        input.style.borderColor = '#f59e0b';
                        setTimeout(() => {
                            input.style.borderColor = '';
                        }, 1000);
                    }, 500);
                });
            });
        });

        // Success message for form submissions
        window.addEventListener('load', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('success')) {
                const message = document.createElement('div');
                message.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
                message.textContent = 'Record updated successfully!';
                document.body.appendChild(message);

                setTimeout(() => {
                    message.remove();
                }, 3000);
            }
        });
    </script>

</x-app-layout>
