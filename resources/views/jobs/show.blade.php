<x-app-layout>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Job Details
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    {{ $job->company_name ?? 'Company Name' }} - {{ $job->site_name ?? 'Site Name' }}
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('jobs.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back to Jobs
                </a>
                <button onclick="editJob({{ $job->id ?? 1 }})" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Job
                </button>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Job Overview -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Basic Information -->
                    <div class="lg:col-span-2">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Job Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Company Name</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $job->company_name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Site Name</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $job->site_name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Product</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $job->product ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Status</label>
                                <div class="mt-1">
                                    <x-status-badge :status="$job->status ?? 'pending'" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Start Date</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $job->start_date ? \Carbon\Carbon::parse($job->start_date)->format('M d, Y') : 'Not set' }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">End Date</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $job->end_date ? \Carbon\Carbon::parse($job->end_date)->format('M d, Y') : 'Not set' }}
                                </p>
                            </div>
                        </div>

                        @if($job->remarks ?? '')
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700">Remarks</label>
                                <div class="mt-1 p-3 bg-gray-50 rounded-md">
                                    <p class="text-sm text-gray-900">{{ $job->remarks }}</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Progress Summary -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Progress Summary</h3>
                        @php
                            $design = $job->design ?? 0;
                            $clientApproval = $job->client_approval ?? 0;
                            $printing = $job->printing ?? 0;
                            $installation = $job->installation ?? 0;
                            $totalProgress = ($design + $clientApproval + $printing + $installation) * 25;
                            $completedTasks = $design + $clientApproval + $printing + $installation;
                        @endphp

                        <div class="text-center mb-4">
                            <div class="text-3xl font-bold text-indigo-600">{{ $totalProgress }}%</div>
                            <div class="text-sm text-gray-500">Overall Progress</div>
                            <div class="text-xs text-gray-400">{{ $completedTasks }}/4 tasks completed</div>
                        </div>

                        <div class="w-full bg-gray-200 rounded-full h-3 mb-4">
                            <div class="bg-indigo-600 h-3 rounded-full transition-all duration-300" style="width: {{ $totalProgress }}%"></div>
                        </div>

                        <!-- Timeline estimate -->
                        @if($job->start_date && $job->end_date)
                            <div class="text-center">
                                @php
                                    $startDate = \Carbon\Carbon::parse($job->start_date);
                                    $endDate = \Carbon\Carbon::parse($job->end_date);
                                    $totalDays = $startDate->diffInDays($endDate);
                                    $daysPassed = $startDate->diffInDays(\Carbon\Carbon::now());
                                    $timeProgress = $totalDays > 0 ? min(100, ($daysPassed / $totalDays) * 100) : 0;
                                @endphp
                                <div class="text-sm text-gray-600 mb-2">Timeline Progress</div>
                                <div class="w-full bg-gray-200 rounded-full h-2 mb-1">
                                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ $timeProgress }}%"></div>
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ round($timeProgress, 1) }}% of time elapsed
                                    @if($totalDays > 0)
                                        ({{ max(0, $totalDays - $daysPassed) }} days remaining)
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Checklist -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-6">Internal Checklist Progress</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Design -->
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full {{ $design ? 'bg-green-100' : 'bg-gray-100' }} mb-4">
                            @if($design)
                                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            @else
                                <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 12a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 12a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path>
                                </svg>
                            @endif
                        </div>
                        <h4 class="text-sm font-medium text-gray-900">Design</h4>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $design ? 'Completed' : 'Pending' }}
                        </p>
                        @if($design)
                            <div class="mt-2">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    ✓ Done
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- Client Approval -->
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full {{ $clientApproval ? 'bg-green-100' : 'bg-gray-100' }} mb-4">
                            @if($clientApproval)
                                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            @else
                                <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            @endif
                        </div>
                        <h4 class="text-sm font-medium text-gray-900">Client Approval</h4>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $clientApproval ? 'Approved' : 'Awaiting' }}
                        </p>
                        @if($clientApproval)
                            <div class="mt-2">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    ✓ Approved
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- Printing -->
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full {{ $printing ? 'bg-green-100' : 'bg-gray-100' }} mb-4">
                            @if($printing)
                                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            @else
                                <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                </svg>
                            @endif
                        </div>
                        <h4 class="text-sm font-medium text-gray-900">Printing</h4>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $printing ? 'Completed' : 'Pending' }}
                        </p>
                        @if($printing)
                            <div class="mt-2">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    ✓ Printed
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- Installation -->
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full {{ $installation ? 'bg-green-100' : 'bg-gray-100' }} mb-4">
                            @if($installation)
                                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            @else
                                <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                                </svg>
                            @endif
                        </div>
                        <h4 class="text-sm font-medium text-gray-900">Installation</h4>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $installation ? 'Completed' : 'Pending' }}
                        </p>
                        @if($installation)
                            <div class="mt-2">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    ✓ Installed
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Progress Flow -->
                <div class="mt-8">
                    <h4 class="text-sm font-medium text-gray-900 mb-4">Workflow Progress</h4>
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-8 h-8 {{ $design ? 'bg-green-500' : 'bg-gray-300' }} rounded-full flex items-center justify-center">
                                        <span class="text-white text-xs font-medium">1</span>
                                    </div>
                                    <div class="flex-1 h-1 {{ $clientApproval ? 'bg-green-500' : 'bg-gray-300' }} mx-2"></div>
                                </div>
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-8 h-8 {{ $clientApproval ? 'bg-green-500' : 'bg-gray-300' }} rounded-full flex items-center justify-center">
                                        <span class="text-white text-xs font-medium">2</span>
                                    </div>
                                    <div class="flex-1 h-1 {{ $printing ? 'bg-green-500' : 'bg-gray-300' }} mx-2"></div>
                                </div>
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-8 h-8 {{ $printing ? 'bg-green-500' : 'bg-gray-300' }} rounded-full flex items-center justify-center">
                                        <span class="text-white text-xs font-medium">3</span>
                                    </div>
                                    <div class="flex-1 h-1 {{ $installation ? 'bg-green-500' : 'bg-gray-300' }} mx-2"></div>
                                </div>
                                <div class="flex-shrink-0 w-8 h-8 {{ $installation ? 'bg-green-500' : 'bg-gray-300' }} rounded-full flex items-center justify-center">
                                    <span class="text-white text-xs font-medium">4</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-between mt-2 text-xs text-gray-500">
                        <span>Design</span>
                        <span>Approval</span>
                        <span>Printing</span>
                        <span>Installation</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status History / Timeline -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Status History</h3>

                <div class="flow-root">
                    <ul class="-mb-8">
                        <!-- Job Created -->
                        <li>
                            <div class="relative pb-8">
                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                            <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                        <div>
                                            <p class="text-sm text-gray-500">Job created</p>
                                        </div>
                                        <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                            {{ $job->created_at ? $job->created_at->format('M d, Y H:i') : 'Today' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <!-- Design Status -->
                        @if($design)
                        <li>
                            <div class="relative pb-8">
                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                        <div>
                                            <p class="text-sm text-gray-500">Design completed</p>
                                        </div>
                                        <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                            {{ $job->updated_at ? $job->updated_at->format('M d, Y H:i') : 'Recently' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        @endif

                        <!-- Client Approval Status -->
                        @if($clientApproval)
                        <li>
                            <div class="relative pb-8">
                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                        <div>
                                            <p class="text-sm text-gray-500">Client approval received</p>
                                        </div>
                                        <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                            {{ $job->updated_at ? $job->updated_at->format('M d, Y H:i') : 'Recently' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        @endif

                        <!-- Printing Status -->
                        @if($printing)
                        <li>
                            <div class="relative pb-8">
                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                        <div>
                                            <p class="text-sm text-gray-500">Printing completed</p>
                                        </div>
                                        <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                            {{ $job->updated_at ? $job->updated_at->format('M d, Y H:i') : 'Recently' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        @endif

                        <!-- Installation Status -->
                        @if($installation)
                        <li>
                            <div class="relative">
                                <div class="relative flex space-x-3">
                                    <div>
</x-app-layout>


