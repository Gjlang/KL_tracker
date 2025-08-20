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
              <div class="flex justify-between items-center mb-6">
                <!-- Header Information -->
                <div>
                  <h3 class="text-2xl font-semibold text-gray-900">Master File Data</h3>
                  <p class="text-sm text-gray-600 mt-1">
                    @if(isset($masterFiles) && $masterFiles->count() > 0)
                      Showing {{ $masterFiles->count() }} records
                    @else
                      No records found
                    @endif
                  </p>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-3">
                  <a href="{{ route('calendar.index') }}" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 shadow-md transition-colors flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    📅 Calendar View
                  </a>

                  <!-- Import Button -->
                  <button type="button" onclick="openImportModal()"
                    class="inline-flex items-center px-6 py-3 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3"></path>
                    </svg>
                    Import Data
                  </button>

                  <a href="{{ route('masterfile.exportCsv', request()->query()) }}"
                    class="inline-flex items-center px-6 py-3 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    Export All Data
                  </a>

                  <!-- Add New Button -->
                  <a href="{{ route('masterfile.create') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add New
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
                          <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-300 min-w-[220px] whitespace-nowrap">Company Name</th>
                          <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-300 min-w-[170px] whitespace-nowrap">Client</th>
                          <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-300 min-w-[120px] whitespace-nowrap">Product</th>
                          <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-300 min-w-[120px] whitespace-nowrap">Month</th>
                          <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-300 min-w-[120px] whitespace-nowrap">Start Date</th>
                          <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-300 min-w-[120px] whitespace-nowrap">End Date</th>
                          <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-300 min-w-[120px] whitespace-nowrap">Duration</th>
                          <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-300 min-w-[120px] whitespace-nowrap">Status</th>
                          <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-300 min-w-[100px] whitespace-nowrap">Traffic</th>
                          <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-300 min-w-[120px] whitespace-nowrap">Job</th>
                          <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-300 min-w-[120px] whitespace-nowrap">Artwork</th>
                          <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-300 min-w-[120px] whitespace-nowrap">Invoice Date</th>
                          <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-300 min-w-[160px] whitespace-nowrap">Invoice Number</th>
                        </tr>
                      </thead>
                      <tbody class="bg-white divide-y divide-gray-200">
                        @if(isset($masterFiles) && $masterFiles->count() > 0)
                          @foreach($masterFiles as $file)
                            <tr class="{{ $loop->iteration % 2 === 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-gray-100 transition-colors duration-200">
                              <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $file->created_at ? $file->created_at->format('M d, Y') : '-' }}</td>
                              <td class="px-6 py-4 text-sm text-gray-900">
                                <a href="{{ route('masterfile.show', $file->id) }}" class="text-blue-600 hover:text-blue-800 hover:underline transition-colors duration-150 font-medium">
                                  <div class="max-w-[200px] truncate" title="{{ $file->company }}">{{ $file->company }}</div>
                                </a>
                              </td>
                              <td class="px-6 py-4 text-sm text-gray-900">{{ $file->client ?? '-' }}</td>
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
                              <td class="px-6 py-4 text-sm text-gray-900">{{ $file->traffic ?? '-' }}</td>
                              <td class="px-6 py-4 text-sm text-gray-900">{{ $file->job_number ?? '-' }}</td>
                              <td class="px-6 py-4 text-sm text-gray-900">{{ $file->artwork ?? '-' }}</td>
                              <td class="px-6 py-4 text-sm text-gray-900">{{ $file->invoice_date ? \Carbon\Carbon::parse($file->invoice_date)->format('M d, Y') : '-' }}</td>
                              <td class="px-6 py-4 text-sm text-gray-900">{{ $file->invoice_number ?? '-' }}</td>
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
                                  <button onclick="openImportModal()" class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 text-sm transition-colors">
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

              <!-- Table Info -->
              @if(isset($masterFiles) && $masterFiles->count() > 0)
                <div class="mt-6 flex justify-between items-center text-sm text-gray-600 bg-gray-50 px-6 py-4 rounded-lg border border-gray-200">
                  <div class="flex items-center space-x-6">
                    <span>Showing <strong>{{ $masterFiles->count() }}</strong> of <strong>{{ method_exists($masterFiles, 'total') ? $masterFiles->total() : $masterFiles->count() }}</strong> records</span>
                    @if(method_exists($masterFiles, 'currentPage') && method_exists($masterFiles, 'lastPage'))
                      <span>•</span>
                      <span>Page <strong>{{ $masterFiles->currentPage() }}</strong> of <strong>{{ $masterFiles->lastPage() }}</strong></span>
                    @endif
                    @if($masterFiles->count() > 0)
                      <span>•</span>
                      <span>Last Updated: <strong>{{ $masterFiles->first()->updated_at ? $masterFiles->first()->updated_at->format('M d, Y H:i') : 'N/A' }}</strong></span>
                    @endif
                  </div>
                  <div class="text-xs text-gray-500">
                    Scroll horizontally and vertically to view all data
                  </div>
                </div>
              @endif
            </div>
          </div>

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
              <div class="flex justify-between items-center mb-6">
                <!-- Header Information -->
                <div>
                  <h3 class="text-2xl font-semibold text-gray-900">Master File Data</h3>
                  <p class="text-sm text-gray-600 mt-1">
                    @if(isset($masterFiles) && $masterFiles->count() > 0)
                      Showing {{ $masterFiles->count() }} records
                    @else
                      No records found
                    @endif
                  </p>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-3">
                  <a href="{{ route('calendar.index') }}" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 shadow-md transition-colors flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    📅 Calendar View
                  </a>

                  <!-- Import Button -->
                  <button type="button" onclick="openImportModal()"
                    class="inline-flex items-center px-6 py-3 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3"></path>
                    </svg>
                    Import Data
                  </button>

                  <a href="{{ route('masterfile.exportCsv', request()->query()) }}"
                    class="inline-flex items-center px-6 py-3 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    Export All Data
                  </a>

                  <!-- Add New Button -->
                  <a href="{{ route('masterfile.create') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add New
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
                          <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-300 min-w-[220px] whitespace-nowrap">Company Name</th>
                          <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-300 min-w-[170px] whitespace-nowrap">Client</th>
                          <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-300 min-w-[120px] whitespace-nowrap">Product</th>
                          <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-300 min-w-[120px] whitespace-nowrap">Month</th>
                          <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-300 min-w-[120px] whitespace-nowrap">Start Date</th>
                          <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-300 min-w-[120px] whitespace-nowrap">End Date</th>
                          <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-300 min-w-[120px] whitespace-nowrap">Duration</th>
                          <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-300 min-w-[120px] whitespace-nowrap">Status</th>
                          <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-300 min-w-[100px] whitespace-nowrap">Traffic</th>
                          <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-300 min-w-[120px] whitespace-nowrap">Job</th>
                          <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-300 min-w-[120px] whitespace-nowrap">Artwork</th>
                          <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-300 min-w-[120px] whitespace-nowrap">Invoice Date</th>
                          <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-b border-gray-300 min-w-[160px] whitespace-nowrap">Invoice Number</th>
                        </tr>
                      </thead>
                      <tbody class="bg-white divide-y divide-gray-200">
                        @if(isset($masterFiles) && $masterFiles->count() > 0)
                          @foreach($masterFiles as $file)
                            <tr class="{{ $loop->iteration % 2 === 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-gray-100 transition-colors duration-200">
                              <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $file->created_at ? $file->created_at->format('M d, Y') : '-' }}</td>
                              <td class="px-6 py-4 text-sm text-gray-900">
                                <a href="{{ route('masterfile.show', $file->id) }}" class="text-blue-600 hover:text-blue-800 hover:underline transition-colors duration-150 font-medium">
                                  <div class="max-w-[200px] truncate" title="{{ $file->company }}">{{ $file->company }}</div>
                                </a>
                              </td>
                              <td class="px-6 py-4 text-sm text-gray-900">{{ $file->client ?? '-' }}</td>
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
                              <td class="px-6 py-4 text-sm text-gray-900">{{ $file->traffic ?? '-' }}</td>
                              <td class="px-6 py-4 text-sm text-gray-900">{{ $file->job_number ?? '-' }}</td>
                              <td class="px-6 py-4 text-sm text-gray-900">{{ $file->artwork ?? '-' }}</td>
                              <td class="px-6 py-4 text-sm text-gray-900">{{ $file->invoice_date ? \Carbon\Carbon::parse($file->invoice_date)->format('M d, Y') : '-' }}</td>
                              <td class="px-6 py-4 text-sm text-gray-900">{{ $file->invoice_number ?? '-' }}</td>
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
                                  <button onclick="openImportModal()" class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 text-sm transition-colors">
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

              <!-- Table Info -->
              @if(isset($masterFiles) && $masterFiles->count() > 0)
                <div class="mt-6 flex justify-between items-center text-sm text-gray-600 bg-gray-50 px-6 py-4 rounded-lg border border-gray-200">
                  <div class="flex items-center space-x-6">
                    <span>Showing <strong>{{ $masterFiles->count() }}</strong> of <strong>{{ method_exists($masterFiles, 'total') ? $masterFiles->total() : $masterFiles->count() }}</strong> records</span>
                    @if(method_exists($masterFiles, 'currentPage') && method_exists($masterFiles, 'lastPage'))
                      <span>•</span>
                      <span>Page <strong>{{ $masterFiles->currentPage() }}</strong> of <strong>{{ $masterFiles->lastPage() }}</strong></span>
                    @endif
                    @if($masterFiles->count() > 0)
                      <span>•</span>
                      <span>Last Updated: <strong>{{ $masterFiles->first()->updated_at ? $masterFiles->first()->updated_at->format('M d, Y H:i') : 'N/A' }}</strong></span>
                    @endif
                  </div>
                  <div class="text-xs text-gray-500">
                    Scroll horizontally and vertically to view all data
                  </div>
                </div>
              @endif
            </div>
          </div>

          {{-- ==== KLTG SECTION ==== --}}
          <section id="kltg" class="mt-12 scroll-mt-24">
            <!-- Monthly Ongoing Job Section - KL The Guide Style -->
            <div class="mt-10 bg-white p-8 rounded-lg shadow-sm border border-gray-200">
              <h3 class="text-2xl font-bold mb-8 text-gray-900 flex items-center gap-2">
                📊 MONTHLY Ongoing Job – KL The Guide
              </h3>

              <!-- Filter Section -->
              <div class="mb-6 p-6 bg-gray-50 rounded-lg border border-gray-200">
                <h4 class="text-lg font-semibold text-gray-700 mb-4">Filters</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                  <!-- Status Filter -->
                  <div>
                    <label for="filter-status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="filter-status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                      <option value="">All Status</option>
                      <option value="pending">Pending</option>
                      <option value="ongoing">Ongoing</option>
                      <option value="completed">Completed</option>
                    </select>
                  </div>

                  <!-- Year Filter -->
                  <div>
                    <label for="filter-year" class="block text-sm font-medium text-gray-700 mb-2">Year</label>
                    <select id="filter-year" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                      <option value="">All Years</option>
                      @for($year = date('Y'); $year >= date('Y') - 5; $year--)
                        <option value="{{ $year }}" @if($year == date('Y')) selected @endif>{{ $year }}</option>
                      @endfor
                    </select>
                  </div>

                  <!-- Company Filter -->
                  <div>
                    <label for="filter-company" class="block text-sm font-medium text-gray-700 mb-2">Company</label>
                    <select id="filter-company" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                      <option value="">All Companies</option>
                      @if(isset($masterFiles))
                        @foreach($masterFiles->pluck('company')->unique()->sort() as $company)
                          <option value="{{ $company }}">{{ $company }}</option>
                        @endforeach
                      @endif
                    </select>
                  </div>

                  <!-- Product Filter -->
                  <div>
                    <label for="filter-product" class="block text-sm font-medium text-gray-700 mb-2">Product</label>
                    <select id="filter-product" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                      <option value="">All Products</option>
                      <option value="KLTG">KLTG</option>
                      <option value="KLTG listing">KLTG listing</option>
                      <option value="KLTG quarter page">KLTG quarter page</option>
                    </select>
                  </div>
                </div>

                <!-- Clear Filters Button -->
                <div class="mt-4">
                  <button id="clear-filters" class="px-4 py-2 bg-gray-600 text-black rounded-md hover:bg-gray-700 transition-colors duration-150">
                    Clear All Filters
                  </button>
                </div>
                <a href="{{ route('coordinator.kltg.index') }}" class="px-6 py-3 bg-purple-600 text-black rounded-lg hover:bg-purple-700 shadow-md transition-colors flex items-center">
                  📖 KLTG Coordinator List
                </a>
              </div>

              <!-- Main KLTG Matrix Table -->
              <div class="overflow-hidden shadow-sm rounded-lg border border-gray-300 relative">
                <div class="overflow-x-auto">
                  <table class="min-w-full table-auto border-collapse" style="min-width: 2000px;">
                    <thead class="sticky top-0 z-20 bg-white">
                      <!-- First Header Row (Columns) -->
                      <tr class="bg-yellow-100">
                        <th class="sticky left-0 z-30 bg-yellow-100 border border-gray-400 px-4 py-3 text-left text-xs font-bold text-gray-700 whitespace-nowrap" style="min-width: 60px;">No</th>
                        <th class="sticky left-[60px] z-30 bg-yellow-100 border border-gray-400 px-4 py-3 text-left text-xs font-bold text-gray-700 whitespace-nowrap" style="min-width: 150px;">Created at</th>
                        <th class="sticky left-[210px] z-30 bg-yellow-100 border border-gray-400 px-4 py-3 text-left text-xs font-bold text-gray-700 whitespace-nowrap" style="min-width: 100px;">Month</th>
                        <th class="sticky left-[310px] z-30 bg-yellow-100 border border-gray-400 px-4 py-3 text-left text-xs font-bold text-gray-700 whitespace-nowrap" style="min-width: 220px;">Company</th>
                        <th class="sticky left-[530px] z-30 bg-yellow-100 border border-gray-400 px-4 py-3 text-left text-xs font-bold text-gray-700 whitespace-nowrap" style="min-width: 140px;">Product</th>
                        <th class="sticky left-[670px] z-30 bg-yellow-100 border border-gray-400 px-4 py-3 text-left text-xs font-bold text-gray-700 whitespace-nowrap" style="min-width: 120px;">PIC</th>
                        <th class="sticky left-[790px] z-30 bg-yellow-100 border border-gray-400 px-4 py-3 text-center text-xs font-bold text-gray-700 whitespace-nowrap" style="min-width: 120px;">Status</th>
                        <th class="border border-gray-400 px-4 py-3 text-center text-xs font-bold text-gray-700 whitespace-nowrap" style="min-width: 120px;">Start</th>
                        <th class="border border-gray-400 px-4 py-3 text-center text-xs font-bold text-gray-700 whitespace-nowrap" style="min-width: 120px;">End</th>
                        <th class="border border-gray-400 px-4 py-3 text-center text-xs font-bold text-gray-700 whitespace-nowrap" style="min-width: 120px;">Duration</th>

                        @foreach(['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'] as $m)
                          <th class="border border-gray-400 px-3 py-3 text-center text-xs font-bold text-gray-700 bg-gray-100 whitespace-nowrap" colspan="5" style="min-width: 500px;">{{ $m }}</th>
                        @endforeach
                      </tr>

                      <!-- Second Header Row (Types) -->
                      <tr class="bg-gray-50 sticky top-[49px] z-20">
                        <th class="sticky left-0 z-30 bg-gray-50 border border-gray-300 px-4 py-2"></th>
                        <th class="sticky left-[60px] z-30 bg-gray-50 border border-gray-300 px-4 py-2"></th>
                        <th class="sticky left-[210px] z-30 bg-gray-50 border border-gray-300 px-4 py-2"></th>
                        <th class="sticky left-[310px] z-30 bg-gray-50 border border-gray-300 px-4 py-2"></th>
                        <th class="sticky left-[530px] z-30 bg-gray-50 border border-gray-300 px-4 py-2"></th>
                        <th class="sticky left-[670px] z-30 bg-gray-50 border border-gray-300 px-4 py-2"></th>
                        <th class="sticky left-[790px] z-30 bg-gray-50 border border-gray-300 px-4 py-2"></th>
                        @for($i = 7; $i < 10; $i++)
                          <th class="border border-gray-300 px-4 py-2"></th>
                        @endfor

                        @foreach(range(1, 12) as $i)
                          <th class="border border-gray-300 px-3 py-2 text-center text-xs font-semibold bg-purple-50 whitespace-nowrap" style="min-width: 100px;">KLTG</th>
                          <th class="border border-gray-300 px-3 py-2 text-center text-xs font-semibold whitespace-nowrap" style="min-width: 100px;">VIDEO</th>
                          <th class="border border-gray-300 px-3 py-2 text-center text-xs font-semibold whitespace-nowrap" style="min-width: 100px;">ARTICLE</th>
                          <th class="border border-gray-300 px-3 py-2 text-center text-xs font-semibold whitespace-nowrap" style="min-width: 100px;">LB</th>
                          <th class="border border-gray-300 px-3 py-2 text-center text-xs font-semibold whitespace-nowrap" style="min-width: 100px;">EM</th>
                        @endforeach
                      </tr>
                    </thead>

                    <tbody>
                      @php $rowNumber = 1; @endphp
                      @if(isset($masterFiles) && $masterFiles->count() > 0)
                        @foreach($masterFiles as $index => $masterFile)
                          <!-- Filter for KLTG-related products only -->
                          @if(in_array($masterFile->product, ['KLTG', 'KLTG listing', 'KLTG quarter page']))
                            <tr class="table-row {{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-green-50 transition-colors duration-200"
                                data-status="{{ $masterFile->status }}"
                                data-company="{{ $masterFile->company }}"
                                data-product="{{ $masterFile->product }}"
                                data-year="{{ $masterFile->created_at ? $masterFile->created_at->format('Y') : '' }}">

                              <!-- No -->
                              <td class="sticky left-0 z-10 bg-inherit border border-gray-300 px-4 py-3 text-sm text-gray-900 whitespace-nowrap">
                                {{ $rowNumber++ }}
                              </td>

                              <!-- Created at -->
                              <td class="sticky left-[60px] z-10 bg-inherit border border-gray-300 px-4 py-3 text-sm text-gray-900 whitespace-nowrap">
                                {{ $masterFile->created_at ? $masterFile->created_at->format('d/m/y') : '-' }}
                              </td>

                              <!-- Month -->
                              <td class="sticky left-[210px] z-10 bg-inherit border border-gray-300 px-4 py-3 text-sm text-gray-900 whitespace-nowrap">
                                {{ $masterFile->month ?? 'N/A' }}
                              </td>

                              <!-- Company -->
                              <td class="sticky left-[310px] z-10 bg-inherit border border-gray-300 px-4 py-3 text-sm text-gray-900" style="max-width: 220px;">
                                <div class="truncate pr-2" title="{{ $masterFile->company }}">
                                  {{ $masterFile->company ?? 'N/A' }}
                                </div>
                              </td>

                              <!-- Product -->
                              <td class="sticky left-[530px] z-10 bg-inherit border border-gray-300 px-4 py-3 text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                  @if($masterFile->product === 'KLTG') bg-purple-100 text-purple-800
                                  @elseif(in_array($masterFile->product, ['HM', 'TB', 'TTM', 'BB'])) bg-green-100 text-green-800
                                  @elseif($masterFile->product === 'FB IG Ad') bg-blue-100 text-blue-800
                                  @else bg-gray-100 text-gray-800
                                  @endif">
                                  {{ $masterFile->product ?? 'N/A' }}
                                </span>
                              </td>

                              <!-- PIC (Client field) -->
                              <td class="sticky left-[670px] z-10 bg-inherit border border-gray-300 px-4 py-3 text-sm text-gray-900">
                                {{ $masterFile->client ?? 'N/A' }}
                              </td>

                              <!-- Status -->
                              <td class="sticky left-[790px] z-10 bg-inherit border border-gray-300 px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                  @if($masterFile->status === 'completed') bg-green-100 text-green-800
                                  @elseif($masterFile->status === 'ongoing') bg-yellow-100 text-yellow-800
                                  @elseif($masterFile->status === 'pending') bg-red-100 text-red-800
                                  @else bg-gray-100 text-gray-800
                                  @endif">
                                  {{ ucfirst($masterFile->status ?? 'pending') }}
                                </span>
                              </td>

                              <!-- Start Date -->
                              <td class="border border-gray-300 px-4 py-3 text-center text-sm text-gray-900 whitespace-nowrap">
                                {{ $masterFile->date ? $masterFile->date->format('M d') : '-' }}
                              </td>

                              <!-- End Date -->
                              <td class="border border-gray-300 px-4 py-3 text-center text-sm text-gray-900 whitespace-nowrap">
                                {{ $masterFile->date_finish ? $masterFile->date_finish->format('M d') : '-' }}
                              </td>

                              <!-- Duration -->
                              <td class="border border-gray-300 px-4 py-3 text-center text-sm text-gray-900 whitespace-nowrap">
                                {{ $masterFile->duration ?? 'N/A' }}
                              </td>

                              <!-- Monthly KLTG Matrix Columns -->
                              @foreach(['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'] as $monthKey)
                                @php
                                  $currentMonth   = strtolower(now()->format('M'));
                                  $isCurrentMonth = $monthKey === $currentMonth;

                                  // nama kolom di DB
                                  $kltgField   = "check_{$monthKey}_kltg";
                                  $videoField  = "check_{$monthKey}_video";
                                  $articleField= "check_{$monthKey}_article";
                                  $lbField     = "check_{$monthKey}_lb";
                                  $emField     = "check_{$monthKey}_em";
                                @endphp

                                <!-- KLTG Column -->
                                <td class="border border-gray-300 px-2 py-2 text-center {{ $isCurrentMonth ? 'bg-yellow-50' : 'bg-purple-50' }}" style="min-width: 100px;">
                                  <input type="text"
                                      class="w-full text-center border-none bg-transparent focus:outline-none focus:bg-blue-50"
                                      data-field="{{ $kltgField }}"
                                      data-id="{{ $masterFile->id }}"
                                      value="{{ $masterFile->$kltgField ?? '' }}">
                                </td>

                                <!-- VIDEO Column -->
                                <td class="border border-gray-300 px-2 py-2 text-center {{ $isCurrentMonth ? 'bg-yellow-50' : 'bg-gray-50' }}" style="min-width: 100px;">
                                  <input type="text"
                                      class="w-full text-center border-none bg-transparent focus:outline-none focus:bg-blue-50"
                                      data-field="{{ $videoField }}"
                                      data-id="{{ $masterFile->id }}"
                                      value="{{ $masterFile->$videoField ?? '' }}">
                                </td>

                                <!-- ARTICLE Column -->
                                <td class="border border-gray-300 px-2 py-2 text-center {{ $isCurrentMonth ? 'bg-yellow-50' : 'bg-gray-50' }}" style="min-width: 100px;">
                                  <input type="text"
                                      class="w-full text-center border-none bg-transparent focus:outline-none focus:bg-blue-50"
                                      data-field="{{ $articleField }}"
                                      data-id="{{ $masterFile->id }}"
                                      value="{{ $masterFile->$articleField ?? '' }}">
                                </td>

                                <!-- LB Column -->
                                <td class="border border-gray-300 px-2 py-2 text-center {{ $isCurrentMonth ? 'bg-yellow-50' : 'bg-gray-50' }}" style="min-width: 100px;">
                                  <input type="text"
                                      class="w-full text-center border-none bg-transparent focus:outline-none focus:bg-blue-50"
                                      data-field="{{ $lbField }}"
                                      data-id="{{ $masterFile->id }}"
                                      value="{{ $masterFile->$lbField ?? '' }}">
                                </td>

                                <!-- EM Column -->
                                <td class="border border-gray-300 px-2 py-2 text-center {{ $isCurrentMonth ? 'bg-yellow-50' : 'bg-gray-50' }}" style="min-width: 100px;">
                                  <input type="text"
                                      class="w-full text-center border-none bg-transparent focus:outline-none focus:bg-blue-50"
                                      data-field="{{ $emField }}"
                                      data-id="{{ $masterFile->id }}"
                                      value="{{ $masterFile->$emField ?? '' }}">
                                </td>
                              @endforeach
                            </tr>
                          @endif
                        @endforeach
                      @else
                        <tr>
                          <td colspan="70" class="border border-gray-300 px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                              <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                              </svg>
                              <h4 class="text-lg font-medium text-gray-900 mb-2">No master files found</h4>
                              <p class="text-gray-600 mb-4">No data available to display in the monthly ongoing job table.</p>
                            </div>
                          </td>
                        </tr>
                      @endif
                    </tbody>
                  </table>
                </div>
              </div>

              <!-- JavaScript for Filters and Auto-save -->
              <script>
                document.addEventListener('DOMContentLoaded', function() {
                  // Filter functionality
                  const filterStatus = document.getElementById('filter-status');
                  const filterYear = document.getElementById('filter-year');
                  const filterCompany = document.getElementById('filter-company');
                  const filterProduct = document.getElementById('filter-product');
                  const clearFilters = document.getElementById('clear-filters');
                  const tableRows = document.querySelectorAll('.table-row');

                  function applyFilters() {
                    const statusValue = filterStatus.value.toLowerCase();
                    const yearValue = filterYear.value;
                    const companyValue = filterCompany.value.toLowerCase();
                    const productValue = filterProduct.value.toLowerCase();

                    tableRows.forEach(row => {
                      const rowStatus = row.dataset.status.toLowerCase();
                      const rowYear = row.dataset.year;
                      const rowCompany = row.dataset.company.toLowerCase();
                      const rowProduct = row.dataset.product.toLowerCase();

                      const statusMatch = !statusValue || rowStatus === statusValue;
                      const yearMatch = !yearValue || rowYear === yearValue;
                      const companyMatch = !companyValue || rowCompany.includes(companyValue);
                      const productMatch = !productValue || rowProduct === productValue;

                      if (statusMatch && yearMatch && companyMatch && productMatch) {
                        row.style.display = '';
                      } else {
                        row.style.display = 'none';
                      }
                    });
                  }

                  // Add event listeners for filters
                  [filterStatus, filterYear, filterCompany, filterProduct].forEach(filter => {
                    filter.addEventListener('change', applyFilters);
                  });

                  // Clear filters
                  clearFilters.addEventListener('click', function() {
                    [filterStatus, filterYear, filterCompany, filterProduct].forEach(filter => {
                      filter.value = '';
                    });
                    applyFilters();
                  });

                  // Auto-save functionality for checkboxes
                  const autoSaveCheckboxes = document.querySelectorAll('.auto-save-checkbox');

                  autoSaveCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                      const field = this.dataset.field;
                      const id = this.dataset.id;
                      const value = this.checked ? 1 : 0;

                      // Check if this is a KLTG checkbox
                      const match = field.match(/^check_(\w+)_(kltg|video|article|lb|em)$/i);
                      const updateUrl = match ? `/masterfile/${id}/kltg-matrix/update` : `/masterfile/${id}`;

                      // Show loading state
                      this.disabled = true;
                      this.style.opacity = '0.5';

                      // Get CSRF token
                      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                                      document.querySelector('input[name="_token"]')?.value;

                      if (match) {
                        const shortMonth = match[1];   // e.g. "jan"
                        const type = match[2];         // e.g. "kltg"
                        fetch(updateUrl, {
                          method: 'POST',
                          headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                          },
                          body: JSON.stringify({
                            months: {
                              [shortMonth]: { [type]: !!value }
                            }
                          })
                        })
                        .then(r => r.json())
                        .then(/* success handler */)
                        .catch(/* error handler */);
                        return; // stop here, don't fall through to the FormData path
                      }

                      // Make AJAX request
                      const formData = new FormData();
                      formData.append(field, value);
                      formData.append('_token', csrfToken);
                      formData.append('_method', 'PUT');

                      fetch(updateUrl, {
                        method: 'POST', // Using POST with _method override
                        headers: {
                          'X-CSRF-TOKEN': csrfToken,
                          'Accept': 'application/json'
                        },
                        body: formData
                      })
                      .then(response => response.json())
                      .then(data => {
                        if (data.success) {
                          // Show success feedback
                          this.style.backgroundColor = '#10b981';
                          setTimeout(() => {
                            this.style.backgroundColor = '';
                          }, 500);
                        } else {
                          // Revert checkbox state on error
                          this.checked = !this.checked;
                          alert('Error updating data. Please try again.');
                        }
                      })
                      .catch(error => {
                        console.error('Error:', error);
                        // Revert checkbox state on error
                        this.checked = !this.checked;
                        alert('Error updating data. Please try again.');
                      })
                      .finally(() => {
                        // Remove loading state
                        this.disabled = false;
                        this.style.opacity = '1';
                      });
                    });
                  });
                });
              </script>

              <!-- Additional CSS for better sticky positioning -->
              <style>
                .table-row.bg-white .sticky {
                  background-color: white !important;
                }

                .table-row.bg-gray-50 .sticky {
                  background-color: rgb(249 250 251) !important;
                }

                .table-row:hover .sticky {
                  background-color: rgb(240 253 244) !important;
                }

                /* Ensure proper z-index layering */
                .sticky {
                  position: -webkit-sticky;
                  position: sticky;
                }
              </style>

              <!-- Outdoor Section Footer -->
              @if(isset($outdoorJobs) && $outdoorJobs->count() > 0)
                <div class="mt-6 flex justify-between items-center text-sm text-gray-600 bg-green-50 px-6 py-4 rounded-lg border border-green-200">
                  <div class="flex items-center space-x-6">
                    <span>Total Outdoor Jobs: <strong>{{ $outdoorJobs->count() }}</strong></span>
                    <span>•</span>
                    <span class="flex items-center">
                      <span class="text-green-600 font-bold text-lg mr-1">✓</span> = Completed
                    </span>
                    <span>•</span>
                    <span>Year: <strong>{{ request('outdoor_year', now()->year) }}</strong></span>
                  </div>
                  <div class="flex space-x-3">
                    @if(Route::has('coordinator.outdoor.export'))
                      <a href="{{ route('coordinator.outdoor.export') }}" class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-semibold transition-colors duration-200 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3"></path>
                        </svg>
                        Export Outdoor Data
                      </a>
                    @endif
                    @if(Route::has('coordinator.outdoor.index'))
                    @endif
                  </div>
                </div>
              @endif
            </div>
          </section>

          {{-- ==== MEDIA SECTION ==== --}}
          <section id="media" class="mt-12 scroll-mt-24">
            <!-- MEDIA Section (ENHANCED WITH INLINE EDITING) -->
            @if(isset($monthlyByCategory['Media']) && count($monthlyByCategory['Media']))
              <h4 class="text-lg font-semibold text-blue-700 mt-12 mb-6 border-b-2 border-blue-200 pb-2">
                📹 MEDIA Ongoing Job – KL The Guide
              </h4>

              <a href="/coordinator/media" class="px-6 py-3 bg-blue-600 text-black rounded-lg hover:bg-blue-700 shadow-md transition-colors flex items-center mb-4">
                📱 Media Coordinator List
              </a>

              <!-- Filter Section (Keep existing filters and add more as needed) -->
              <div class="mb-6 p-4 bg-gray-50 rounded-lg border">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                  <!-- Add your additional filters here as requested -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Company</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                      <option value="">All Companies</option>
                      <!-- Add company options dynamically -->
                    </select>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Platform</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                      <option value="">All Platforms</option>
                      <!-- Add platform options dynamically -->
                    </select>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Category</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                      <option value="">All Categories</option>
                      <!-- Add category options dynamically -->
                    </select>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Month</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                      <option value="">All Months</option>
                      <option value="jan">January</option>
                      <option value="feb">February</option>
                      <option value="mar">March</option>
                      <option value="apr">April</option>
                      <option value="may">May</option>
                      <option value="jun">June</option>
                      <option value="jul">July</option>
                      <option value="aug">August</option>
                      <option value="sep">September</option>
                      <option value="oct">October</option>
                      <option value="nov">November</option>
                      <option value="dec">December</option>
                    </select>
                  </div>
                </div>
              </div>

              <!-- Enhanced Table with Wider Columns and Horizontal Scroll -->
              <div class="overflow-x-auto mb-8 shadow-sm rounded-lg border border-gray-200" style="max-width: 100%; overflow-x: scroll;">
                <table class="table-auto text-sm text-left divide-y divide-gray-200" style="min-width: 2000px; width: max-content;">
                  <thead class="bg-blue-50 sticky top-0 z-10">
                    <tr>
                      <th class="px-6 py-4 text-xs font-bold text-gray-700 uppercase tracking-wider whitespace-nowrap sticky left-0 bg-blue-50 z-20" style="min-width: 140px; width: 140px;">Date</th>
                      <th class="px-6 py-4 text-xs font-bold text-gray-700 uppercase tracking-wider sticky left-140 bg-blue-50 z-20" style="min-width: 250px; width: 250px;">Company</th>
                      <th class="px-6 py-4 text-xs font-bold text-gray-700 uppercase tracking-wider whitespace-nowrap" style="min-width: 150px; width: 150px;">Product</th>
                      <th class="px-6 py-4 text-xs font-bold text-gray-700 uppercase tracking-wider whitespace-nowrap" style="min-width: 150px; width: 150px;">Category</th>
                      <th class="px-6 py-4 text-xs font-bold text-gray-700 uppercase tracking-wider whitespace-nowrap" style="min-width: 180px; width: 180px;">Platform</th>
                      <th class="px-6 py-4 text-xs font-bold text-gray-700 uppercase tracking-wider whitespace-nowrap" style="min-width: 120px; width: 120px;">Start</th>
                      <th class="px-6 py-4 text-xs font-bold text-gray-700 uppercase tracking-wider whitespace-nowrap" style="min-width: 120px; width: 120px;">End</th>
                      @foreach(['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'] as $month)
                        <th class="px-6 py-4 text-xs font-bold text-gray-700 uppercase tracking-wider text-center bg-white" style="min-width: 100px; width: 100px;">{{ strtoupper($month) }}</th>
                      @endforeach
                      <th class="px-6 py-4 text-xs font-bold text-gray-700 uppercase tracking-wider whitespace-nowrap" style="min-width: 250px; width: 250px;">Remarks</th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-100">
                    @foreach($monthlyByCategory['Media'] as $index => $job)
                      <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-blue-50 transition-colors duration-200">
                        <td class="px-6 py-4 text-gray-700 whitespace-nowrap sticky left-0 bg-inherit z-10" style="min-width: 140px; width: 140px;">{{ $job->date?->format('Y-m-d') }}</td>
                        <td class="px-6 py-4 text-gray-700 sticky left-140 bg-inherit z-10" style="min-width: 250px; width: 250px;">
                          <div class="max-w-[230px] truncate" title="{{ $job->company }}">{{ $job->company }}</div>
                        </td>
                        <td class="px-6 py-4 text-gray-700 whitespace-nowrap" style="min-width: 150px; width: 150px;">
                          <div class="max-w-[130px] truncate" title="{{ $job->product }}">{{ $job->product }}</div>
                        </td>
                        <td class="px-6 py-4 text-gray-700 whitespace-nowrap" style="min-width: 150px; width: 150px;">
                          <div class="max-w-[130px] truncate" title="{{ $job->product_category }}">{{ $job->product_category }}</div>
                        </td>
                        <td class="px-6 py-4 text-gray-700" style="min-width: 180px; width: 180px;">
                          <div class="max-w-[160px] truncate" title="{{ $job->location ?? $job->platform ?? 'N/A' }}">{{ $job->location ?? $job->platform ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 text-gray-700 whitespace-nowrap" style="min-width: 120px; width: 120px;">{{ $job->date?->format('M d') }}</td>
                        <td class="px-6 py-4 text-gray-700 whitespace-nowrap" style="min-width: 120px; width: 120px;">{{ optional($job->date_finish)?->format('M d') }}</td>
                        @foreach(['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'] as $m)
                          <td class="px-6 py-4 text-center bg-blue-50 text-gray-800 whitespace-nowrap" style="min-width: 100px; width: 100px;">
                            <input
                                type="text"
                                value="{{ $job["check_$m"] ?? '' }}"
                                data-id="{{ $job->id }}"
                                data-type="master"
                                data-field="check_{{ $m }}"
                                onblur="autoSave(this)"
                                class="w-full bg-transparent border-0 text-center focus:outline-none focus:ring-1 focus:ring-blue-300 focus:bg-blue-100 px-2 py-1 rounded transition-all duration-150"
                                placeholder=""
                                style="min-width: 80px;"
                            />
                          </td>
                        @endforeach
                        <td class="px-6 py-4 text-gray-600" style="min-width: 250px; width: 250px;">
                          <input
                              type="text"
                              value="{{ $job->remarks }}"
                              data-id="{{ $job->id }}"
                              data-type="master"
                              data-field="remarks"
                              onblur="autoSave(this)"
                              class="w-full bg-transparent border-0 focus:outline-none focus:ring-1 focus:ring-blue-300 focus:bg-blue-50 px-2 py-1 rounded transition-all duration-150"
                              placeholder="Add remarks..."
                              style="min-width: 230px;"
                          />
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>

              <!-- Additional CSS for sticky columns (add this to your CSS file or in a <style> tag) -->
              <style>
                .sticky.left-140 {
                  left: 140px;
                }

                /* Ensure proper z-index layering for sticky elements */
                .sticky {
                  position: sticky;
                }

                /* Custom scrollbar for better UX */
                .overflow-x-auto::-webkit-scrollbar {
                  height: 8px;
                }

                .overflow-x-auto::-webkit-scrollbar-track {
                  background: #f1f1f1;
                  border-radius: 4px;
                }

                .overflow-x-auto::-webkit-scrollbar-thumb {
                  background: #c1c1c1;
                  border-radius: 4px;
                }

                .overflow-x-auto::-webkit-scrollbar-thumb:hover {
                  background: #a8a8a8;
                }
              </style>
            @endif
          </section>

          {{-- ==== OUTDOOR SECTION ==== --}}
          <section id="outdoor" class="mt-12 scroll-mt-24">
            <!-- 🏞️ NEW: OUTDOOR COORDINATOR - MONTHLY ONGOING SECTION -->
            <div class="mt-10 bg-white p-8 rounded-lg shadow-sm border border-gray-200">
              <h3 class="text-2xl font-bold mb-8 text-green-700 flex items-center gap-2">
                🏞️ OUTDOOR Monthly Ongoing Job
              </h3>

              <!-- Outdoor Filters -->
              <form method="GET" action="{{ route('dashboard') }}" class="mb-8 bg-green-50 p-6 rounded-lg border border-green-200">
                <!-- Preserve existing filters -->
                <input type="hidden" name="search" value="{{ request('search') }}">
                <input type="hidden" name="status" value="{{ request('status') }}">
                <input type="hidden" name="month" value="{{ request('month') }}">
                <input type="hidden" name="product_category" value="{{ request('product_category') }}">

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                  <!-- Year Filter -->
                  <div>
                    <label for="outdoor_year" class="block text-xs font-medium text-gray-700 mb-2">Year</label>
                    <select name="outdoor_year" id="outdoor_year" class="w-full border-gray-300 rounded-md px-4 py-2 text-sm focus:ring-green-500 focus:border-green-500 transition-colors">
                      <option value="">All Years</option>
                      @foreach($availableYears->unique() as $year)
                        <option value="{{ $year }}" {{ request('outdoor_year') == $year ? 'selected' : (request('outdoor_year') == '' && $year == now()->year ? 'selected' : '') }}>
                          {{ $year }}
                        </option>
                      @endforeach
                    </select>
                  </div>

                  <!-- Client Filter -->
                  <div>
                    <label for="outdoor_client" class="block text-xs font-medium text-gray-700 mb-2">Client</label>
                    <select name="outdoor_client" id="outdoor_client" class="w-full border-gray-300 rounded-md px-4 py-2 text-sm focus:ring-green-500 focus:border-green-500 transition-colors">
                      <option value="">All Clients</option>
                      @foreach($outdoorClients as $client)
                        <option value="{{ $client }}" {{ request('outdoor_client') == $client ? 'selected' : '' }}>
                          {{ $client }}
                        </option>
                      @endforeach
                    </select>
                  </div>

                  <!-- State/Location Filter -->
                  <div>
                    <label for="outdoor_state" class="block text-xs font-medium text-gray-700 mb-2">State/Area</label>
                    <select name="outdoor_state" id="outdoor_state" class="w-full border-gray-300 rounded-md px-4 py-2 text-sm focus:ring-green-500 focus:border-green-500 transition-colors">
                      <option value="">All States</option>
                      @foreach($outdoorStates as $state)
                        <option value="{{ $state }}" {{ request('outdoor_state') == $state ? 'selected' : '' }}>
                          {{ $state }}
                        </option>
                      @endforeach
                    </select>
                  </div>

                  <!-- Status Filter -->
                  <div>
                    <label for="outdoor_status" class="block text-xs font-medium text-gray-700 mb-2">Status</label>
                    <select name="outdoor_status" id="outdoor_status" class="w-full border-gray-300 rounded-md px-4 py-2 text-sm focus:ring-green-500 focus:border-green-500 transition-colors">
                      <option value="">All Status</option>
                      <option value="pending" {{ request('outdoor_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                      <option value="ongoing" {{ request('outdoor_status') == 'ongoing' ? 'selected' : '' }}>In Progress</option>
                      <option value="completed" {{ request('outdoor_status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                  </div>

                  <a href="{{ route('coordinator.outdoor.index') }}" class="px-6 py-3 bg-yellow-600 text-black rounded-lg hover:bg-yellow-700 shadow-md transition-colors flex items-center">
                    🏞️ Outdoor Coordinator List
                  </a>

                  <!-- Action Buttons -->
                  <div class="flex space-x-2">
                    <button type="submit" class="flex-1 px-4 py-2 text-sm bg-green-600 text-white rounded-md hover:bg-green-700 focus:ring-2 focus:ring-green-500 transition-colors">
                      Filter
                    </button>

                    @if(request('outdoor_year') || request('outdoor_client') || request('outdoor_state') || request('outdoor_status'))
                      <a href="{{ route('dashboard') }}" class="px-4 py-2 text-sm bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                        Clear
                      </a>
                    @endif
                  </div>
                </div>

                <!-- Active filters display -->
                @if(request('outdoor_year') || request('outdoor_client') || request('outdoor_state') || request('outdoor_status'))
                  <div class="mt-4 flex items-center justify-between text-sm">
                    <div class="text-gray-600">
                      <span class="font-medium">Active outdoor filters:</span>
                      @if(request('outdoor_year'))<span class="ml-2 px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Year: {{ request('outdoor_year') }}</span>@endif
                      @if(request('outdoor_client'))<span class="ml-2 px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">Client: {{ request('outdoor_client') }}</span>@endif
                      @if(request('outdoor_state'))<span class="ml-2 px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs">State: {{ request('outdoor_state') }}</span>@endif
                      @if(request('outdoor_status'))<span class="ml-2 px-2 py-1 bg-orange-100 text-orange-800 rounded text-xs">Status: {{ request('outdoor_status') }}</span>@endif
                    </div>
                    <div class="text-gray-500">
                      {{ $outdoorJobs->count() }} records found
                    </div>
                  </div>
                @endif
              </form>

              <!-- Outdoor Monthly Tracking Table -->
              <div class="overflow-x-auto mb-8 shadow-sm rounded-lg border border-gray-300">
                <table class="min-w-full table-fixed border-collapse"> <!-- CHANGED: table-auto to table-fixed -->
                  <thead>
                    <!-- Header Row -->
                    <tr class="bg-green-100">
                      <th class="border border-gray-400 px-4 py-3 text-left text-xs font-bold text-gray-700 w-[100px] whitespace-nowrap">Date</th>
                      <th class="border border-gray-400 px-4 py-3 text-left text-xs font-bold text-gray-700 w-[60px] whitespace-nowrap">No</th>
                      <th class="border border-gray-400 px-4 py-3 text-left text-xs font-bold text-gray-700 w-[200px] whitespace-nowrap">Client</th>
                      <th class="border border-gray-400 px-4 py-3 text-left text-xs font-bold text-gray-700 w-[120px] whitespace-nowrap">Product</th>
                      <th class="border border-gray-400 px-4 py-3 text-center text-xs font-bold text-gray-700 w-[150px] whitespace-nowrap">Site/Location</th>
                      <th class="border border-gray-400 px-4 py-3 text-center text-xs font-bold text-gray-700 w-[100px] whitespace-nowrap">Start</th>
                      <th class="border border-gray-400 px-4 py-3 text-center text-xs font-bold text-gray-700 w-[100px] whitespace-nowrap">End</th>

                      <!-- Monthly Progress Columns - FIXED WIDTH -->
                      @foreach(['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'] as $month)
                        <th class="border border-gray-400 px-3 py-3 text-center text-xs font-bold text-gray-700 bg-gray-100 w-[140px]">{{ $month }}</th>
                      @endforeach

                      <th class="border border-gray-400 px-4 py-3 text-center text-xs font-bold text-gray-700 w-[120px] whitespace-nowrap">Status</th>
                      <th class="border border-gray-400 px-4 py-3 text-center text-xs font-bold text-gray-700 w-[200px] whitespace-nowrap">Remarks</th>
                    </tr>
                  </thead>

                  <tbody>
                    @php $rowNumber = 1; @endphp
                    @if($outdoorJobs->count() > 0)
                      @foreach($outdoorJobs as $job)
                        <tr class="{{ $loop->iteration % 2 === 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-green-50 transition-colors duration-200">
                          <!-- Date -->
                          <td class="border border-gray-300 px-4 py-3 text-sm text-gray-900 w-[100px] whitespace-nowrap">
                            {{ $job->date ? \Carbon\Carbon::parse($job->date)->format('d/m/y') : '-' }}
                          </td>

                          <!-- Row No -->
                          <td class="border border-gray-300 px-4 py-3 text-center text-sm text-gray-900 w-[60px] whitespace-nowrap">
                            {{ $rowNumber++ }}
                          </td>

                          <!-- Client -->
                          <td class="border border-gray-300 px-4 py-3 text-sm text-gray-900 w-[200px]">
                            <div class="max-w-[180px] truncate" title="{{ $job->client }}">{{ $job->client ?? 'N/A' }}</div>
                          </td>

                          <!-- Product -->
                          <td class="border border-gray-300 px-4 py-3 text-sm text-gray-900 w-[120px] whitespace-nowrap">
                            {{ $job->product ?? 'N/A' }}
                          </td>

                          <!-- Site/Location (editable) -->
                          <td class="border border-gray-300 px-4 py-3 text-center w-[150px]">
                            <input
                                type="text"
                                value="{{ $job->site }}"
                                data-id="{{ $job->id }}"
                                data-type="outdoor"
                                data-field="site"
                                onblur="autoSaveOutdoor(this)"
                                class="w-full bg-transparent border-0 text-center focus:outline-none focus:ring-1 focus:ring-green-300 focus:bg-green-50 px-2 py-1 rounded transition-all duration-150"
                                placeholder="Location..."
                            />
                          </td>

                          <!-- Start Date -->
                          <td class="border border-gray-300 px-4 py-3 text-center text-sm text-gray-900 w-[100px] whitespace-nowrap">
                            {{ $job->date ? \Carbon\Carbon::parse($job->date)->format('M d') : '-' }}
                          </td>

                          <!-- End Date -->
                          <td class="border border-gray-300 px-4 py-3 text-center text-sm text-gray-900 w-[100px] whitespace-nowrap">
                            {{ $job->date_finish ? \Carbon\Carbon::parse($job->date_finish)->format('M d') : '-' }}
                          </td>

                          {{-- Monthly Status Columns (free text) --}}
                          @foreach(['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'] as $monthKey)
                            @php
                              $fieldName = 'check_' . $monthKey;
                              $currentMonth = strtolower(now()->format('M'));
                              $isCurrentMonth = $monthKey === $currentMonth;
                              $bgColor = $isCurrentMonth ? 'bg-yellow-50' : 'bg-gray-50';
                            @endphp
                            <td class="border border-gray-300 px-2 py-2 text-center w-[140px]">
                              <input
                                  type="text"
                                  value="{{ $job[$fieldName] }}"
                                  data-id="{{ $job->id }}"
                                  data-type="outdoor"
                                  data-field="{{ $fieldName }}"
                                  onblur="autoSaveOutdoor(this)"
                                  onkeydown="if (event.key === 'Enter') { event.preventDefault(); autoSaveOutdoor(this); this.blur(); }"
                                  class="w-full bg-transparent border-0 text-center focus:outline-none focus:ring-1 focus:ring-green-300 focus:bg-green-100 px-1 py-1 rounded text-xs {{ $bgColor }}"
                                  style="width: 130px !important;"
                                  list="status-suggestions"  {{-- opsional: saran status --}}
                                  placeholder="Enter"
                              />
                            </td>
                          @endforeach

                          <!-- Status Column -->
                          <td class="border border-gray-300 px-4 py-3 text-center w-[120px] whitespace-nowrap">
                            <input
                                type="text"
                                value="{{ $job->status }}"
                                data-id="{{ $job->id }}"
                                data-type="outdoor"
                                data-field="status"
                                onblur="autoSaveOutdoor(this)"
                                onkeydown="if (event.key === 'Enter') { event.preventDefault(); autoSaveOutdoor(this); this.blur(); }"
                                class="w-full bg-transparent border-0 text-center focus:outline-none focus:ring-1 focus:ring-green-300 focus:bg-green-100 px-2 py-1 rounded transition-all duration-150"
                                list="status-suggestions"
                                placeholder="ketik status…"
                            />
                          </td>

                          <!-- Remarks -->
                          <td class="border border-gray-300 px-4 py-3 text-gray-600 w-[200px]">
                            <input
                                type="text"
                                value="{{ $job->remarks }}"
                                data-id="{{ $job->id }}"
                                data-type="outdoor"
                                data-field="remarks"
                                onblur="autoSaveOutdoor(this)"
                                class="w-full bg-transparent border-0 focus:outline-none focus:ring-1 focus:ring-green-300 focus:bg-green-50 px-2 py-1 rounded transition-all duration-150"
                                placeholder="Add remarks..."
                            />
                          </td>
                        </tr>
                      @endforeach
                    @else
                      <tr>
                        <td colspan="20" class="border border-gray-300 px-6 py-12 text-center text-gray-500">
                          <div class="flex flex-col items-center">
                            <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <h4 class="text-lg font-medium text-gray-900 mb-2">No outdoor jobs found</h4>
                            <p class="text-gray-600 mb-4">
                              @if(request('outdoor_year') || request('outdoor_client') || request('outdoor_state') || request('outdoor_status'))
                                No outdoor jobs match your current filters. Try adjusting your search criteria.
                              @else
                                No outdoor coordinator tracking records available for the current year.
                              @endif
                            </p>
                            <div class="flex space-x-3">
                              @if(request('outdoor_year') || request('outdoor_client') || request('outdoor_state') || request('outdoor_status'))
                                <a href="{{ route('dashboard') }}" class="bg-gray-600 text-white px-6 py-3 rounded-md hover:bg-gray-700 text-sm transition-colors">
                                  Clear Filters
                                </a>
                              @endif
                              <a href="{{ route('coordinator.outdoor.index') }}" class="bg-green-600 text-white px-6 py-3 rounded-md hover:bg-green-700 text-sm transition-colors">
                                Go to Outdoor Coordinator
                              </a>
                            </div>
                          </div>
                        </td>
                      </tr>
                    @endif
                  </tbody>
                </table>
              </div>

              <!-- Outdoor Section Footer -->
              @if($outdoorJobs->count() > 0)
                <div class="mt-6 flex justify-between items-center text-sm text-gray-600 bg-green-50 px-6 py-4 rounded-lg border border-green-200">
                  <div class="flex items-center space-x-6">
                    <span>Total Outdoor Jobs: <strong>{{ $outdoorJobs->count() }}</strong></span>
                    <span>•</span>
                    <span class="flex items-center">
                      <span class="text-green-600 font-bold text-lg mr-1">✓</span> = Completed
                    </span>
                    <span>•</span>
                    <span>Year: <strong>{{ request('outdoor_year', now()->year) }}</strong></span>
                  </div>
                  <div class="flex space-x-3">
                    <a href="{{ route('coordinator.outdoor.export') }}" class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-semibold transition-colors duration-200 shadow-sm">
                      <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3"></path>
                      </svg>
                      Export Outdoor Data
                    </a>
                  </div>
                </div>
              @endif
            </div>

            <!-- Export Button -->
            <div class="mt-8">
              <a href="{{ route('export.monthly.ongoing') }}"
                class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-md text-sm font-semibold transition-colors duration-200 shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3"></path>
                </svg>
                Export to CSV
              </a>
            </div>
          </section>

        </div>
      </main>
    </div>
  </div>

  {{-- Import Modal --}}
  <div id="importModal" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black/40" onclick="closeImportModal()"></div>

    <!-- Panel -->
    <div class="relative mx-auto mt-24 w-full max-w-lg rounded-xl bg-white shadow-xl">
      <div class="px-6 py-4 border-b flex items-center justify-between">
        <h3 class="text-lg font-semibold">Import Master File Data</h3>
        <button type="button" onclick="closeImportModal()" class="p-2 text-gray-500 hover:text-gray-700">✕</button>
      </div>

      <form id="importForm" action="{{ route('masterfile.import') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
        @csrf

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Upload file (.xlsx, .xls, .csv)</label>
          <input type="file" name="file" accept=".xlsx,.xls,.csv" required
            class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" />
          <p class="mt-2 text-xs text-gray-500">
            Tips: Use the same column order as your export. "Start Date" = <code>date</code>, "End Date" = <code>date_finish</code>.
          </p>
        </div>

        @if ($errors->any())
          <div class="text-red-600 text-sm">{{ $errors->first() }}</div>
        @endif
        @if (session('success'))
          <div class="text-green-600 text-sm">{{ session('success') }}</div>
        @endif

        <div class="flex items-center justify-end gap-3 pt-2">
          <button type="button" onclick="closeImportModal()" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700">
            Cancel
          </button>
          <button id="importSubmit" type="submit"
            class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 disabled:opacity-60">
            Upload
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
  </style>

  {{-- JavaScript Functions --}}
  <script>
    function openImportModal() {
      document.getElementById('importModal').classList.remove('hidden');
    }

    function closeImportModal() {
      document.getElementById('importModal').classList.add('hidden');
    }
  </script>

</x-app-layout>

  {{-- Import Modal --}}
  <div id="importModal" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black/40" onclick="closeImportModal()"></div>

    <!-- Panel -->
    <div class="relative mx-auto mt-24 w-full max-w-lg rounded-xl bg-white shadow-xl">
      <div class="px-6 py-4 border-b flex items-center justify-between">
        <h3 class="text-lg font-semibold">Import Master File Data</h3>
        <button type="button" onclick="closeImportModal()" class="p-2 text-gray-500 hover:text-gray-700">✕</button>
      </div>

      <form id="importForm" action="{{ route('masterfile.import') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
        @csrf

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Upload file (.xlsx, .xls, .csv)</label>
          <input type="file" name="file" accept=".xlsx,.xls,.csv" required
            class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" />
          <p class="mt-2 text-xs text-gray-500">
            Tips: Use the same column order as your export. "Start Date" = <code>date</code>, "End Date" = <code>date_finish</code>.
          </p>
        </div>

        @if ($errors->any())
          <div class="text-red-600 text-sm">{{ $errors->first() }}</div>
        @endif
        @if (session('success'))
          <div class="text-green-600 text-sm">{{ session('success') }}</div>
        @endif

        <div class="flex items-center justify-end gap-3 pt-2">
          <button type="button" onclick="closeImportModal()" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700">
            Cancel
          </button>
          <button id="importSubmit" type="submit"
            class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 disabled:opacity-60">
            Upload
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
  </style>

  {{-- JavaScript Functions --}}
  <script>
    function openImportModal() {
      document.getElementById('importModal').classList.remove('hidden');
    }

    function closeImportModal() {
      document.getElementById('importModal').classList.add('hidden');
    }
  </script>









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

                <!-- File Upload -->
                <div>
                    <label for="importFile" class="block text-sm font-medium text-gray-700 mb-3">Choose File to Import</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition-colors">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="importFile" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500">
                                    <span>Upload a file</span>
                                    <input id="importFile" name="file" type="file" class="sr-only" accept=".csv,.xlsx,.xls" required onchange="handleFileSelect(this)">
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">CSV, XLSX, XLS up to 2MB</p>
                        </div>
                    </div>
                    <div id="selectedFileName" class="mt-2 text-sm text-gray-600 hidden"></div>
                </div>

                <!-- Instructions -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex">
                        <svg class="w-5 h-5 text-blue-400 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="text-sm text-blue-800">
                            <h4 class="font-semibold mb-2">Import Instructions:</h4>
                            <ul class="space-y-1 list-disc list-inside text-xs">
                                <li><strong>Download the template first</strong> to see the required format</li>
                                <li>Required columns: month, date, company, product, status</li>
                                <li>Date format: YYYY-MM-DD</li>
                                <li>Status options: pending, ongoing, completed</li>
                                <li>Product options: HM, TB, TTM, BB, Star, KLTG, Flyers, Bunting, KLTG listing, KLTG quarter page, Signages, FB IG Ad</li>
                                <li>Optional fields: traffic, duration, client, date_finish, job_number, artwork, invoice_date, invoice_number</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Download Template -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">Need the template?</h4>
                            <p class="text-xs text-gray-600">Download the CSV template with sample data and instructions</p>
                        </div>
                        <a href="{{ route('masterfile.template') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Download Template
                        </a>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex space-x-3 pt-4">
                    <button type="button" onclick="closeImportModal()" class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">Cancel</button>
                    <button type="submit" class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Import</button>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript for Modal and AJAX functionality -->
    @push('scripts')
    <script>
        // 🔥 Universal Auto-Save Function for all inline edits (ENHANCED)
        function autoSave(el) {
            // Visual feedback - show saving state
            el.style.backgroundColor = '#fef3c7'; // yellow-100 (saving)
            el.disabled = true;

            // Determine the correct route and data
            let url, data;

            if (el.dataset.masterId) {
                // KLTG Matrix update (use existing function)
                return updateKltgMatrix(el);
            } else {
                // General inline update
                url = "{{ route('monthly.job.update') }}";
                data = {
                    id: el.dataset.id,
                    field: el.dataset.field,
                    value: el.value,
                    type: el.dataset.type || 'media'
                };
            }

            fetch(url, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(data)
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    // Success feedback
                    el.style.backgroundColor = '#d1fae5'; // green-100
                    setTimeout(() => {
                        el.style.backgroundColor = '';
                        el.disabled = false;
                    }, 1000);
                } else {
                    // Error feedback
                    el.style.backgroundColor = '#fee2e2'; // red-100
                    setTimeout(() => {
                        el.style.backgroundColor = '';
                        el.disabled = false;
                    }, 2000);
                    console.error('Update failed:', data.message || data.error);
                }
            })
            .catch(error => {
                // Network error feedback
                el.style.backgroundColor = '#fee2e2'; // red-100
                setTimeout(() => {
                    el.style.backgroundColor = '';
                    el.disabled = false;
                }, 2000);
                console.error('Network error:', error);
            });
        }

        // KLTG Matrix update function (keep existing)
        function updateKltgMatrix(input) {
            const masterId = input.dataset.masterId;
            const month = input.dataset.month;
            const type = input.dataset.type;
            const status = input.value;

            // Visual feedback
            input.style.backgroundColor = '#fef3c7';

            fetch(`/masterfile/${masterId}/kltg-matrix/update`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    months: {
                        [month]: {
                            [type]: status
                        }
                    }
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    input.style.backgroundColor = '#d1fae5'; // green
                    setTimeout(() => input.style.backgroundColor = '', 1000);
                } else {
                    input.style.backgroundColor = '#fee2e2'; // red
                    setTimeout(() => input.style.backgroundColor = '', 2000);
                    alert('⚠️ Update failed.');
                }
            })
            .catch(err => {
                input.style.backgroundColor = '#fee2e2'; // red
                setTimeout(() => input.style.backgroundColor = '', 2000);
                console.error('❌ Save error:', err);
                alert('❌ Could not save.');
            });
        }

        // Import modal functions
        function openImportModal() {
            document.getElementById('importModal').classList.remove('hidden');
        }
        function closeImportModal() {
            document.getElementById('importModal').classList.add('hidden');
        }

        // optional: disable button while submitting
        const importForm = document.getElementById('importForm');
        if (importForm) {
            importForm.addEventListener('submit', function () {
            const btn = document.getElementById('importSubmit');
            if (btn) { btn.disabled = true; btn.textContent = 'Uploading...'; }
            });
        }


        function closeImportModal() {
            const modal = document.getElementById('importModal');
            if (modal) {
                modal.classList.add('hidden');
            }
        }

        // File selection handler
        function handleFileSelect(input) {
            const fileName = input.files[0]?.name;
            const fileLabel = document.getElementById('selectedFileName');
            if (fileName) {
                fileLabel.classList.remove('hidden');
                fileLabel.textContent = `Selected file: ${fileName}`;
            } else {
                fileLabel.classList.add('hidden');
                fileLabel.textContent = '';
            }
        }


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

        // Add CSRF token to head if not already present
        if (!document.querySelector('meta[name="csrf-token"]')) {
            const csrfMeta = document.createElement('meta');
            csrfMeta.name = 'csrf-token';
            csrfMeta.content = '{{ csrf_token() }}';
            document.head.appendChild(csrfMeta);
        }

        console.log("✅ Dashboard with inline editing loaded successfully!");
    </script>
    @endpush
    @stack('scripts')

</x-app-layout>
