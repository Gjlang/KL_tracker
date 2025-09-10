<x-app-layout>
  <div class="flex h-screen bg-gray-50">

    {{-- Sidebar (slides in on mobile, fixed on md+) --}}
    @include('partials.sidebar')

    <!-- Main content -->
    <div class="flex flex-col w-0 flex-1 overflow-hidden">

      <!-- Mobile menu button / header -->
      <div class="relative z-10 flex-shrink-0 flex h-16 bg-white shadow md:hidden">
        <button type="button"
                class="border-r border-gray-200 px-4 text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500 md:hidden"
                id="open-sidebar">
          <span class="sr-only">Open sidebar</span>
          <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
          </svg>
        </button>
        <div class="flex-1 px-4 flex justify-between items-center">
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
          </h2>
        </div>
      </div>

      {{-- Page content --}}
      <main class="flex-1 overflow-y-auto">
        <div class="p-4 md:p-6">

          <!-- Header for desktop -->
          <div class="hidden md:block mb-8">
            <h2 class="font-semibold text-2xl text-gray-900 leading-tight">
              {{ __('Dashboard') }}
            </h2>
          </div>

          <!-- Master File Data Table -->
          <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
            <div class="p-6">
              <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-6 gap-4">
                <!-- Header Information -->
                <div>
                  <h3 class="text-2xl font-semibold text-gray-900">Master Proposal Confirmation</h3>
                  <p class="text-sm text-gray-600 mt-1">
                    @if(isset($masterFiles) && $masterFiles->count() > 0)
                      Showing {{ $masterFiles->count() }} records
                    @else
                      No records found
                    @endif
                  </p>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-3">

                    <a href="{{ route('masterfile.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add New
                  </a>
                  <a href="{{ route('calendar.index') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 shadow-md transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    📅 Calendar View
                  </a>

                  <!-- Import Button -->
                  <button type="button" onclick="testImportModal()"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3"></path>
                    </svg>
                    Import Data
                  </button>

                  <a href="{{ route('masterfile.exportXlsx', request()->query()) }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    Export All Data
                  </a>

                  <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 shadow-md transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Logout
                        </button>
                    </form>
                  <!-- Add New Button -->
                  <a href="{{ route('information.booth') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 shadow-md transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        🧾 Information Booth
                    </a>
                </div>
              </div>

              <!-- Filter Form -->
              <form method="GET" action="{{ route('dashboard') }}" class="mb-8 bg-gray-50 p-6 rounded-lg border border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                  <!-- Search Input -->
                  <div class="lg:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                      placeholder="Company, product, status, client, month..."
                      class="w-full border-gray-300 rounded-md px-4 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                  </div>

                  <!-- Status Filter -->
                  <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" id="status" class="w-full border-gray-300 rounded-md px-4 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                      <option value="">All Status</option>
                      <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                      <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                      <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                  </div>

                  <!-- Month Filter -->
                  <div>
                    <label for="month" class="block text-sm font-medium text-gray-700 mb-2">Month</label>
                    <select name="month" id="month" class="w-full border-gray-300 rounded-md px-4 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                      <option value="">All Months</option>
                      @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $m)
                        <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>{{ $m }}</option>
                      @endforeach
                    </select>
                  </div>

                  <!-- Product Category Filter -->
                  <div>
                    <label for="product_category" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select name="product_category" id="product_category" class="w-full border-gray-300 rounded-md px-4 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                      <option value="">All Categories</option>
                      <option value="Outdoor" {{ request('product_category') == 'Outdoor' ? 'selected' : '' }}>Outdoor</option>
                      <option value="Media" {{ request('product_category') == 'Media' ? 'selected' : '' }}>Media</option>
                      <option value="KLTG" {{ request('product_category') == 'KLTG' ? 'selected' : '' }}>KLTG</option>
                    </select>
                  </div>

                  <!-- Action Buttons -->
                  <div class="flex space-x-3 lg:col-span-4">
                    <button type="submit" class="flex-1 px-6 py-2 text-sm bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 transition-colors">
                      Apply Filters
                    </button>
                    @if(request('search') || request('status') || request('month') || request('product_category'))
                      <a href="{{ route('dashboard') }}" class="px-6 py-2 text-sm bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                        Clear Filters
                      </a>
                    @endif
                  </div>
                </div>

                @include('dashboard.master._tabs', ['active' => $active ?? ''])

                <!-- Active Filters Display -->
                @if(request('search') || request('status') || request('month') || request('product_category'))
                  <div class="mt-4 text-sm">
                    <span class="font-medium">Active filters:</span>
                    @if(request('search'))<span class="ml-2 px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">Search: "{{ request('search') }}"</span>@endif
                    @if(request('status'))<span class="ml-2 px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Status: {{ request('status') }}</span>@endif
                    @if(request('month'))<span class="ml-2 px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs">Month: {{ request('month') }}</span>@endif
                    @if(request('product_category'))<span class="ml-2 px-2 py-1 bg-orange-100 text-orange-800 rounded text-xs">Category: {{ request('product_category') }}</span>@endif
                  </div>
                @endif
              </form>

              <!-- Enhanced Master File Table -->
              <div class="shadow-sm rounded-lg border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                  <div style="max-height: 600px; overflow-y: auto;">
                    <table class="min-w-full table-auto divide-y divide-gray-200">
                      <thead class="bg-gray-100 sticky top-0 z-10">
                        <tr>
                          <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-300 min-w-[120px] whitespace-nowrap">Date Created</th>
                          <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-300 min-w-[220px] whitespace-nowrap">Sales Person</th>
                          <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-300 min-w-[220px] whitespace-nowrap">Company Name</th>
                          <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-300 min-w-[170px] whitespace-nowrap">Person In Charge</th>
                          <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-300 min-w-[220px] whitespace-nowrap">Email</th>
                          <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-300 min-w-[220px] whitespace-nowrap">Amount</th>
                          <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-300 min-w-[170px] whitespace-nowrap">Contact Number</th>
                          <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-300 min-w-[120px] whitespace-nowrap">Product</th>
                          <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-300 min-w-[120px] whitespace-nowrap">Month</th>
                          <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-300 min-w-[120px] whitespace-nowrap">Start Date</th>
                          <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-300 min-w-[120px] whitespace-nowrap">End Date</th>
                          <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-300 min-w-[120px] whitespace-nowrap">Duration</th>
                          <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-300 min-w-[120px] whitespace-nowrap">Status</th>
                          <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-300 min-w-[120px] whitespace-nowrap">Job</th>
                          <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-300 min-w-[120px] whitespace-nowrap">Artwork</th>
                          <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-300 min-w-[100px] whitespace-nowrap">Traffic</th>
                          <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-300 min-w-[120px] whitespace-nowrap">Invoice Date</th>
                          <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-300 min-w-[160px] whitespace-nowrap">Invoice Number</th>
                          <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-300 min-w-[120px] whitespace-nowrap">Remarks</th>

                        </tr>
                      </thead>
                      <tbody class="bg-white divide-y divide-gray-200">
                        @if(isset($masterFiles) && $masterFiles->count() > 0)
                          @foreach($masterFiles as $file)
                            <tr class="{{ $loop->iteration % 2 === 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-gray-100 transition-colors duration-200">
                              <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $file->created_at ? $file->created_at->format('M d, Y') : '-' }}</td>
                              <td class="px-6 py-4 text-sm text-gray-900">{{ $file->sales_person ?? '-' }}</td>
                              <td class="px-6 py-4 text-sm text-gray-900">
                                <a href="{{ route('masterfile.show', $file->id) }}" class="text-blue-600 hover:text-blue-800 hover:underline transition-colors duration-150 font-medium">
                                  <div class="max-w-[200px] truncate" title="{{ $file->company }}">{{ $file->company }}</div>
                                </a>
                              </td>
                              <td class="px-6 py-4 text-sm text-gray-900">{{ $file->client ?? '-' }}</td>
                              <td class="px-6 py-4 text-sm text-gray-900">{{ $file->email ?? '-' }}</td>
                              <td class="px-6 py-4 text-sm text-gray-900">{{ $file->amount ?? '-' }}</td>
                              <td class="px-6 py-4 text-sm text-gray-900">{{ $file->contact_number ?? '-' }}</td>
                              <td class="px-6 py-4 text-sm text-gray-900">{{ $file->product }}</td>
                              <td class="px-6 py-4 text-sm text-gray-900">{{ $file->month }}</td>
                              <td class="px-6 py-4 text-sm text-gray-900">{{ \Carbon\Carbon::parse($file->date)->format('M d, Y') }}</td>
                              <td class="px-6 py-4 text-sm text-gray-900">{{ \Carbon\Carbon::parse($file->end_date)->format('M d, Y') }}</td>
                              <td class="px-6 py-4 text-sm text-gray-900">{{ $file->duration }}</td>
                              <td class="px-6 py-4 text-sm text-gray-900">
                                <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full
                                  {{ $file->status === 'completed' ? 'bg-green-100 text-green-800' : ($file->status === 'ongoing' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                  {{ ucfirst($file->status) }}
                                </span>
                              </td>
                              <td class="px-6 py-4 text-sm text-gray-900">{{ $file->job_number ?? '-' }}</td>
                              <td class="px-6 py-4 text-sm text-gray-900">{{ $file->artwork ?? '-' }}</td>
                              <td class="px-6 py-4 text-sm text-gray-900">{{ $file->traffic ?? '-' }}</td>
                              <td class="px-6 py-4 text-sm text-gray-900">{{ $file->invoice_date ?? '-' }}</td>
                              <td class="px-6 py-4 text-sm text-gray-900">{{ $file->invoice_number ?? '-' }}</td>
                              <td class="px-6 py-4 text-sm text-gray-900">{{ $file->remarks ?? '-' }}</td>

                            </tr>
                          @endforeach
                        @else
                          <tr>
                            <td colspan="14" class="px-6 py-16 text-center text-gray-500">
                              <div class="flex flex-col items-center">
                                <svg class="w-20 h-20 text-gray-400 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <h4 class="text-lg font-medium text-gray-900 mb-2">No master file data found</h4>
                                <p class="text-gray-600 mb-6">Get started by importing data or adding a new record.</p>
                                <div class="flex space-x-3">
                                  <button onclick="testImportModal()" class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 text-sm transition-colors">
                                    Import Data
                                  </button>
                                  <a href="{{ route('masterfile.create') }}" class="bg-indigo-600 text-white px-6 py-3 rounded-md hover:bg-indigo-700 text-sm transition-colors">
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
              </div>

              <!-- Pagination -->
              @if(isset($masterFiles) && method_exists($masterFiles, 'links'))
                <div class="mt-6">
                  {{ $masterFiles->links() }}
                </div>
              @endif
            </div>
          </div>

         <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quick Navigation</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
        <body class="bg-gray-100 p-8">
            <!-- Quick Navigation - Dark Theme -->
            <div class="mt-8 rounded-lg shadow-lg border border-slate-800 bg-slate-900 p-6">
                <div class="text-center mb-6">
                    <h3 class="text-xl font-semibold text-white">Quick Navigation</h3>
                    <p class="text-sm text-slate-400">Navigate to different Mothly Job Sections</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- KLTG Jobs -->
                    <a href="{{ route('dashboard.kltg') }}"
                    class="group flex flex-col items-center justify-center p-6 bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-xl shadow-lg hover:from-blue-600 hover:to-blue-700 hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                        <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center mb-3 group-hover:bg-white/30 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <h4 class="font-semibold text-lg mb-1 text-white">KLTG Monthly Jobs</h4>
                        <p class="text-sm text-white/90 text-center">Manage KLTG monthly jobs and tasks</p>
                    </a>

                    <!-- Media Jobs -->
                    <a href="{{ route('dashboard.media') }}"
                    class="group flex flex-col items-center justify-center p-6 bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-xl shadow-lg hover:from-purple-600 hover:to-purple-700 hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                        <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center mb-3 group-hover:bg-white/30 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h4 class="font-semibold text-lg mb-1 text-white">Social Media Monthly Jobs</h4>
                        <p class="text-sm text-white/90 text-center">Handle media monthly jobs and campaigns</p>
                    </a>

                    <!-- Outdoor Jobs -->
                    <a href="{{ route('dashboard.outdoor') }}"
                    class="group flex flex-col items-center justify-center p-6 bg-gradient-to-br from-green-500 to-green-600 text-white rounded-xl shadow-lg hover:from-green-600 hover:to-green-700 hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                        <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center mb-3 group-hover:bg-white/30 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
                            </svg>
                        </div>
                        <h4 class="font-semibold text-lg mb-1 text-white">Outdoor Monthly Jobs</h4>
                        <p class="text-sm text-white/90 text-center">Manage outdoor monthly advertising jobs</p>
                    </a>
                </div>
            </div>
        </div>
      </main>
    </div>
  </div>

  <!-- Enhanced Import Modal -->
  <div id="importModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
      <div class="bg-white rounded-lg p-6 w-full max-w-lg mx-4 max-h-screen overflow-y-auto">
          <div class="flex justify-between items-center mb-6">
              <h3 class="text-xl font-semibold text-gray-900">Import Master File Data</h3>
              <button onclick="closeImportModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
              </button>
          </div>

          <!-- Import Form -->
          <form action="{{ route('masterfile.import') }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="importForm">
              @csrf

              <!-- Display any validation errors -->
              @if ($errors->any())
                  <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                      <div class="flex">
                          <svg class="w-5 h-5 text-red-400 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                          </svg>
                          <div class="text-sm text-red-800">
                              <h4 class="font-semibold mb-2">Import Error:</h4>
                              <ul class="list-disc list-inside space-y-1">
                                  @foreach ($errors->all() as $error)
                                      <li>{{ $error }}</li>
                                  @endforeach
                              </ul>
                          </div>
                      </div>
                  </div>
              @endif

              <!-- File Upload -->
              <div>
                  <label for="importFile" class="block text-sm font-medium text-gray-700 mb-3">Choose File to Import</label>
                  <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition-colors"
                       ondrop="handleDrop(event)" ondragover="handleDragOver(event)" ondragenter="handleDragEnter(event)" ondragleave="handleDragLeave(event)">
                      <div class="space-y-1 text-center">
                          <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                              <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                          </svg>
                          <div class="flex text-sm text-gray-600">
                              <label for="importFile" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                  <span>Upload a file</span>
                                  <input id="importFile" name="file" type="file" class="sr-only" accept=".csv,.xlsx,.xls" required onchange="handleFileSelect(this)">
                              </label>
                              <p class="pl-1">or drag and drop</p>
                          </div>
                          <p class="text-xs text-gray-500">CSV, XLSX, XLS up to 10MB</p>
                      </div>
                  </div>
                  <div id="selectedFileName" class="mt-2 text-sm text-gray-600 hidden"></div>
              </div>

              <!-- Buttons -->
              <div class="flex space-x-3 pt-4">
                  <button type="button" onclick="closeImportModal()" class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500">
                      Cancel
                  </button>
                  <button type="submit" id="importSubmitBtn" class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500">
                      Import Data
                  </button>
              </div>
          </form>
      </div>
  </div>

  {{-- Optional: nice smooth scroll for anchor jumps --}}
  <style>
    html {
      scroll-behavior: smooth;
    }

    /* Drag and drop styles */
    .drag-over {
        border-color: #4F46E5 !important;
        background-color: #EEF2FF !important;
    }

    /* Custom focus rings for better accessibility */
    button:focus, select:focus, input:focus {
        outline: 2px solid rgba(59, 130, 246, 0.5);
        outline-offset: 2px;
    }

    /* Hide scrollbar but keep functionality */
    .overflow-y-auto::-webkit-scrollbar {
        width: 4px;
    }

    .overflow-y-auto::-webkit-scrollbar-track {
        background: transparent;
    }

    .overflow-y-auto::-webkit-scrollbar-thumb {
        background: rgba(71, 85, 105, 0.5);
        border-radius: 2px;
    }

    .overflow-y-auto::-webkit-scrollbar-thumb:hover {
        background: rgba(71, 85, 105, 0.7);
    }

    /* Better responsive table handling */
    @media (max-width: 768px) {
        .min-w-\[220px\] {
            min-width: 180px;
        }
        .min-w-\[170px\] {
            min-width: 140px;
        }
        .min-w-\[160px\] {
            min-width: 130px;
        }
    }
  </style>

  {{-- JavaScript Functions --}}
  <script>
      // Debug function to test if JavaScript is working
      function testImportModal() {

          const modal = document.getElementById('importModal');
          console.log('🔵 Modal element:', modal);

          if (modal) {
              console.log('🟢 Modal found, showing modal');
              modal.classList.remove('hidden');
              modal.style.display = 'flex'; // Force display
          } else {
              console.log('🔴 Modal NOT found!');
              alert('Error: Modal element not found in DOM');
          }
      }

      // Original import modal function
      function openImportModal() {
          console.log('🔵 openImportModal called');
          const modal = document.getElementById('importModal');
          console.log('🔵 Modal element:', modal);

          if (modal) {
              console.log('🟢 Modal found, showing modal');
              modal.classList.remove('hidden');
              modal.style.display = 'flex';
              // Focus on the file input for better UX
              setTimeout(() => {
                  const fileInput = document.getElementById('importFile');
                  if (fileInput) {
                      fileInput.focus();
                  }
              }, 100);
          } else {
              console.log('🔴 Modal NOT found!');
              console.error('Import modal not found');
              alert('Error: Import modal not found in DOM');
          }
      }

      function closeImportModal() {
          console.log('🔵 closeImportModal called');
          const modal = document.getElementById('importModal');
          if (modal) {
              modal.classList.add('hidden');
              modal.style.display = 'none';
              // Reset the form
              const form = document.getElementById('importForm');
              if (form) {
                  form.reset();
                  const fileLabel = document.getElementById('selectedFileName');
                  if (fileLabel) {
                      fileLabel.classList.add('hidden');
                      fileLabel.textContent = '';
                  }
                  // Reset submit button
                  const submitBtn = document.getElementById('importSubmitBtn');
                  if (submitBtn) {
                      submitBtn.disabled = false;
                      submitBtn.textContent = 'Import Data';
                      submitBtn.classList.remove('opacity-75');
                  }
              }
          }
      }

      // Test all elements when page loads
      document.addEventListener('DOMContentLoaded', function() {
          console.log('🟡 DOM loaded, checking elements...');

          const modal = document.getElementById('importModal');
          const form = document.getElementById('importForm');
          const fileInput = document.getElementById('importFile');

          console.log('🔍 Modal:', modal ? '✅ Found' : '❌ Not found');
          console.log('🔍 Form:', form ? '✅ Found' : '❌ Not found');
          console.log('🔍 File Input:', fileInput ? '✅ Found' : '❌ Not found');

          if (!modal) {
              console.error('🔴 CRITICAL: Import modal not found!');
          }

          // Check if there are multiple modals
          const allModals = document.querySelectorAll('#importModal');
          console.log('🔍 Number of modals found:', allModals.length);
      });

      // File selection handler
      function handleFileSelect(input) {
          const fileName = input.files[0]?.name;
          const fileLabel = document.getElementById('selectedFileName');
          if (fileName && fileLabel) {
              fileLabel.classList.remove('hidden');
              fileLabel.textContent = `Selected file: ${fileName}`;

              // Validate file type
              const allowedTypes = ['.csv', '.xlsx', '.xls'];
              const fileExtension = '.' + fileName.split('.').pop().toLowerCase();

              if (!allowedTypes.includes(fileExtension)) {
                  alert('Please select a valid file type: CSV, XLSX, or XLS');
                  input.value = '';
                  fileLabel.classList.add('hidden');
                  fileLabel.textContent = '';
                  return;
              }

              // Validate file size (10MB limit)
              const maxSize = 10 * 1024 * 1024; // 10MB in bytes
              if (input.files[0].size > maxSize) {
                  alert('File size must be less than 10MB');
                  input.value = '';
                  fileLabel.classList.add('hidden');
                  fileLabel.textContent = '';
                  return;
              }
          } else if (fileLabel) {
              fileLabel.classList.add('hidden');
              fileLabel.textContent = '';
          }
      }

      // Drag and drop handlers
      function handleDragOver(e) {
          e.preventDefault();
          e.stopPropagation();
      }

      function handleDragEnter(e) {
          e.preventDefault();
          e.stopPropagation();
          e.currentTarget.classList.add('drag-over');
      }

      function handleDragLeave(e) {
          e.preventDefault();
          e.stopPropagation();
          e.currentTarget.classList.remove('drag-over');
      }

      function handleDrop(e) {
          e.preventDefault();
          e.stopPropagation();
          e.currentTarget.classList.remove('drag-over');

          const files = e.dataTransfer.files;
          if (files.length > 0) {
              const fileInput = document.getElementById('importFile');
              if (fileInput) {
                  fileInput.files = files;
                  handleFileSelect(fileInput);
              }
          }
      }

      // Form submission handler with validation
      document.addEventListener('DOMContentLoaded', function() {
          const importForm = document.getElementById('importForm');
          if (importForm) {
              importForm.addEventListener('submit', function(e) {
                  const fileInput = document.getElementById('importFile');
                  if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
                      e.preventDefault();
                      alert('Please select a file to import.');
                      return false;
                  }
              });
          }
      });

      function autoSaveOutdoor(element) {
      const data = {
          id: element.getAttribute('data-id'),
          field: element.getAttribute('data-field'),
          value: element.value,
          _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      };

      console.log('Sending data:', data);

      fetch(OUTDOOR_UPDATE_URL, {
          method: 'POST',
          headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': data._token
          },
          body: JSON.stringify({ id: data.id, field: data.field, value: data.value })
      })
      .then(async (res) => {
          if (!res.ok) {
          const txt = await res.text();
          console.error('Save failed', res.status, txt.slice(0,200));
          throw new Error('HTTP ' + res.status);
          }
          return res.json();
      })
      .then((result) => {
          console.log('Result:', result);
          if (!result.success && result.ok !== true) {
          alert('Update failed: ' + (result.message || 'unknown error'));
          }
      })
      .catch((err) => {
          console.error('Error:', err);
          alert('Update failed!');
      });
      }

      // Close modal when clicking outside
      document.addEventListener('DOMContentLoaded', function() {
          const modal = document.getElementById('importModal');
          if (modal) {
              modal.addEventListener('click', function(e) {
                  if (e.target === this) {
                      closeImportModal();
                  }
              });
          }
      });

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

      // Add CSRF token to head if not already present
      if (!document.querySelector('meta[name="csrf-token"]')) {
          const csrfMeta = document.createElement('meta');
          csrfMeta.name = 'csrf-token';
          csrfMeta.content = '{{ csrf_token() }}';
          document.head.appendChild(csrfMeta);
      }

      console.log("✅ Dashboard with improved UI loaded successfully!");
  </script>

</x-app-layout>
