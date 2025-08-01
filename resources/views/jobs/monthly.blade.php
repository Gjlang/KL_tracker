<x-app-layout>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Monthly Jobs View') }}
            </h2>
            <div class="flex items-center space-x-4">
                <!-- Month/Year Selector -->
                <form method="GET" action="{{ route('jobs.monthly') }}" class="flex items-center space-x-2">
                    <select name="month" onchange="this.form.submit()" class="border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ ($selectedMonth ?? date('n')) == $i ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                            </option>
                        @endfor
                    </select>
                    <select name="year" onchange="this.form.submit()" class="border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        @for($year = date('Y') - 2; $year <= date('Y') + 2; $year++)
                            <option value="{{ $year }}" {{ ($selectedYear ?? date('Y')) == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endfor
                    </select>
                </form>
                <button onclick="openModal('createJobModal')" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Job
                </button>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Month Summary -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">
                        {{ date('F Y', mktime(0, 0, 0, $selectedMonth ?? date('n'), 1, $selectedYear ?? date('Y'))) }} Summary
                    </h3>
                    <div class="text-sm text-gray-500">
                        Total: {{ $monthlyStats['total'] ?? 0 }} jobs
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v6a2 2 0 002 2h6a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <div class="text-2xl font-bold text-blue-600">{{ $monthlyStats['total'] ?? 0 }}</div>
                                <div class="text-sm text-blue-800">Total Jobs</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-green-50 p-4 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <div class="text-2xl font-bold text-green-600">{{ $monthlyStats['completed'] ?? 0 }}</div>
                                <div class="text-sm text-green-800">Completed</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-yellow-50 p-4 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <div class="text-2xl font-bold text-yellow-600">{{ $monthlyStats['ongoing'] ?? 0 }}</div>
                                <div class="text-sm text-yellow-800">Ongoing</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-red-50 p-4 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <div class="text-2xl font-bold text-red-600">{{ $monthlyStats['pending'] ?? 0 }}</div>
                                <div class="text-sm text-red-800">Pending</div>
                            </div>
                        </div>
                    </div>
                </div>

                @if(($monthlyStats['total'] ?? 0) > 0)
                    <div class="mt-6">
                        <div class="flex justify-between text-sm text-gray-600 mb-2">
                            <span>Monthly Completion Rate</span>
                            <span>{{ round((($monthlyStats['completed'] ?? 0) / ($monthlyStats['total'] ?? 1)) * 100, 1) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-green-600 h-3 rounded-full" style="width: {{ (($monthlyStats['completed'] ?? 0) / ($monthlyStats['total'] ?? 1)) * 100 }}%"></div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Jobs by Week -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            @for($week = 1; $week <= 4; $week++)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4">
                        <h4 class="text-md font-medium text-gray-900 mb-4">Week {{ $week }}</h4>

                        @php
                            $weekJobs = isset($jobsByWeek[$week]) ? $jobsByWeek[$week] : collect();
                        @endphp

                        @if($weekJobs->count() > 0)
                            <div class="space-y-3">
                                @foreach($weekJobs as $job)
                                    <div class="border border-gray-200 rounded-lg p-3">
                                        <div class="flex justify-between items-start mb-2">
                                            <div class="flex-1">
                                                <h5 class="text-sm font-medium text-gray-900">{{ $job->company_name ?? 'Company' }}</h5>
                                                <p class="text-xs text-gray-500">{{ $job->site_name ?? 'Site' }}</p>
                                            </div>
                                            <x-status-badge :status="$job->status ?? 'pending'" size="sm" />
                                        </div>

                                        <p class="text-xs text-gray-600 mb-2">{{ $job->product ?? 'Product' }}</p>

                                        @php
                                            $progress = (($job->design ?? 0) + ($job->client_approval ?? 0) + ($job->printing ?? 0) + ($job->installation ?? 0)) * 25;
                                        @endphp

                                        <div class="flex items-center">
                                            <div class="flex-1 bg-gray-200 rounded-full h-1.5 mr-2">
                                                <div class="bg-indigo-600 h-1.5 rounded-full" style="width: {{ $progress }}%"></div>
                                            </div>
                                            <span class="text-xs text-gray-500">{{ $progress }}%</span>
                                        </div>

                                        <div class="mt-2 text-xs text-gray-500">
                                            @if($job->start_date)
                                                Start: {{ \Carbon\Carbon::parse($job->start_date)->format('M d') }}
                                            @endif
                                            @if($job->end_date)
                                                | End: {{ \Carbon\Carbon::parse($job->end_date)->format('M d') }}
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-6">
                                <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v6a2 2 0 002 2h6a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <p class="mt-2 text-xs text-gray-500">No jobs this week</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endfor
        </div>

        <!-- Detailed Monthly List -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">All Jobs This Month</h3>
                    <div class="flex space-x-2">
                        <select id="statusFilter" onchange="filterJobs()" class="text-sm border-gray-300 rounded-md">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="ongoing">Ongoing</option>
                            <option value="completed">Completed</option>
                        </select>
                        <select id="sortBy" onchange="sortJobs()" class="text-sm border-gray-300 rounded-md">
                            <option value="start_date">Sort by Start Date</option>
                            <option value="end_date">Sort by End Date</option>
                            <option value="company_name">Sort by Company</option>
                            <option value="status">Sort by Status</option>
                        </select>
                    </div>
                </div>

                @if(isset($monthlyJobs) && $monthlyJobs->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200" id="monthlyJobsTable">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company & Product</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Timeline</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Checklist</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($monthlyJobs as $job)
                                    <tr class="job-row hover:bg-gray-50" data-status="{{ $job->status ?? 'pending' }}" data-company="{{ $job->company_name ?? '' }}" data-start="{{ $job->start_date ?? '' }}" data-end="{{ $job->end_date ?? '' }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $job->company_name ?? 'N/A' }}</div>
                                                <div class="text-sm text-gray-500">{{ $job->site_name ?? 'N/A' }}</div>
                                                <div class="text-sm text-gray-600">{{ $job->product ?? 'N/A' }}</div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div>Start: {{ $job->start_date ? \Carbon\Carbon::parse($job->start_date)->format('M d, Y') : 'Not set' }}</div>
                                            <div>End: {{ $job->end_date ? \Carbon\Carbon::parse($job->end_date)->format('M d, Y') : 'Not set' }}</div>
                                            @if($job->start_date && $job->end_date)
                                                <div class="text-xs text-gray-400">
                                                    {{ \Carbon\Carbon::parse($job->start_date)->diffInDays(\Carbon\Carbon::parse($job->end_date)) }} days
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <x-status-badge :status="$job->status ?? 'pending'" />
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $progress = (($job->design ?? 0) + ($job->client_approval ?? 0) + ($job->printing ?? 0) + ($job->installation ?? 0)) * 25;
                                            @endphp
                                            <div class="flex items-center">
                                                <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                                    <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $progress }}%"></div>
                                                </div>
                                                <span class="text-sm text-gray-600">{{ $progress }}%</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex space-x-1 text-xs">
                                                <span class="{{ ($job->design ?? 0) ? 'text-green-600' : 'text-gray-400' }}">
                                                    {{ ($job->design ?? 0) ? '✅' : '❌' }} D
                                                </span>
                                                <span class="{{ ($job->client_approval ?? 0) ? 'text-green-600' : 'text-gray-400' }}">
                                                    {{ ($job->client_approval ?? 0) ? '✅' : '❌' }} CA
                                                </span>
                                                <span class="{{ ($job->printing ?? 0) ? 'text-green-600' : 'text-gray-400' }}">
                                                    {{ ($job->printing ?? 0) ? '✅' : '❌' }} P
                                                </span>
                                                <span class="{{ ($job->installation ?? 0) ? 'text-green-600' : 'text-gray-400' }}">
                                                    {{ ($job->installation ?? 0) ? '✅' : '❌' }} I
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                @if(Route::has('jobs.show'))
                                                    <a href="{{ route('jobs.show', $job->id) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                                @endif
                                                <button onclick="editJob({{ $job->id }})" class="text-yellow-600 hover:text-yellow-900">Edit</button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No jobs this month</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by creating your first job for this month.</p>
                        <div class="mt-6">
                            <button onclick="openModal('createJobModal')" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                Add New Job
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Create Job Modal (reuse from jobs.index) -->
    <x-modal id="createJobModal" title="Create New Job">
        <form method="POST" action="{{ route('jobs.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="company_name" class="block text-sm font-medium text-gray-700">Company Name</label>
                    <input type="text" name="company_name" id="company_name" required class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>
                <div>
                    <label for="site_name" class="block text-sm font-medium text-gray-700">Site Name</label>
                    <input type="text" name="site_name" id="site_name" required class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>
                <div>
                    <label for="product" class="block text-sm font-medium text-gray-700">Product</label>
                    <input type="text" name="product" id="product" required class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="pending">Pending</option>
                        <option value="ongoing">Ongoing</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                    <input type="date" name="start_date" id="start_date" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                    <input type="date" name="end_date" id="end_date" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">Internal Checklist</label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="flex items-center">
                        <input type="checkbox" name="design" id="design" value="1" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                        <label for="design" class="ml-2 block text-sm text-gray-900">Design</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="client_approval" id="client_approval" value="1" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                        <label for="client_approval" class="ml-2 block text-sm text-gray-900">Client Approval</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="printing" id="printing" value="1" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                        <label for="printing" class="ml-2 block text-sm text-gray-900">Printing</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="installation" id="installation" value="1" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                        <label for="installation" class="ml-2 block text-sm text-gray-900">Installation</label>
                    </div>
                </div>
            </div>

            <div>
                <label for="remarks" class="block text-sm font-medium text-gray-700">Remarks</label>
                <textarea name="remarks" id="remarks" rows="3" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="closeModal('createJobModal')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    Create Job
                </button>
            </div>
        </form>
    </x-modal>

    <script>
        function filterJobs() {
            const statusFilter = document.getElementById('statusFilter').value;
            const rows = document.querySelectorAll('.job-row');

            rows.forEach(row => {
                if (statusFilter === '' || row.dataset.status === statusFilter) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function sortJobs() {
            const sortBy = document.getElementById('sortBy').value;
            const tbody = document.querySelector('#monthlyJobsTable tbody');
            const rows = Array.from(tbody.querySelectorAll('.job-row'));

            rows.sort((a, b) => {
                let aValue, bValue;

                switch(sortBy) {
                    case 'company_name':
                        aValue = a.dataset.company.toLowerCase();
                        bValue = b.dataset.company.toLowerCase();
                        return aValue.localeCompare(bValue);
                    case 'start_date':
                        aValue = new Date(a.dataset.start || '1900-01-01');
                        bValue = new Date(b.dataset.start || '1900-01-01');
                        return aValue - bValue;
                    case 'end_date':
                        aValue = new Date(a.dataset.end || '1900-01-01');
                        bValue = new Date(b.dataset.end || '1900-01-01');
                        return aValue - bValue;
                    case 'status':
                        const statusOrder = { 'pending': 0, 'ongoing': 1, 'completed': 2 };
                        aValue = statusOrder[a.dataset.status] || 0;
                        bValue = statusOrder[b.dataset.status] || 0;
                        return aValue - bValue;
                    default:
                        return 0;
                }
            });

            rows.forEach(row => tbody.appendChild(row));
        }

        function editJob(jobId) {
            // This function would be similar to the one in jobs.index
            // For brevity, redirecting to edit page or opening modal
            window.location.href = `/jobs/${jobId}/edit`;
        }
    </script>
</x-app-layout>


