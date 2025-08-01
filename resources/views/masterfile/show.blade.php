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
                                        Company Details
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
                        <!-- Header for desktop with back button -->
                        <div class="hidden md:block mb-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 transition-colors duration-150">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                        </svg>
                                        Back to Dashboard
                                    </a>
                                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                                        Company Details
                                    </h2>
                                </div>
                            </div>
                        </div>

                        <!-- Company Information Card -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                            <div class="p-6">
                                <div class="flex justify-between items-start mb-6">
                                    <div>
                                        <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $file->company }}</h3>
                                        <div class="flex items-center space-x-4">
                                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                                                {{ $file->status === 'completed' ? 'bg-green-100 text-green-800' :
                                                   ($file->status === 'ongoing' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                {{ ucfirst($file->status) }}
                                            </span>
                                            <span class="inline-flex px-3 py-1 text-sm font-medium rounded-full bg-blue-100 text-blue-800">
                                                {{ $file->product }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="text-right text-sm text-gray-500">
                                        <div>ID: #{{ $file->id }}</div>
                                        <div>Created: {{ $file->created_at ? $file->created_at->format('M d, Y') : 'N/A' }}</div>
                                    </div>
                                </div>

                                <!-- Company Details Grid -->
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    <div class="space-y-4">
                                        <h4 class="font-semibold text-gray-900 border-b pb-2">Project Information</h4>
                                        <div class="space-y-3">
                                            <div>
                                                <span class="text-sm font-medium text-gray-500">Date:</span>
                                                <div class="text-sm text-gray-900">{{ $file->date }}</div>
                                            </div>
                                            <div>
                                                <span class="text-sm font-medium text-gray-500">Month:</span>
                                                <div class="text-sm text-gray-900">{{ $file->month }}</div>
                                            </div>
                                            <div>
                                                <span class="text-sm font-medium text-gray-500">Traffic:</span>
                                                <div class="text-sm text-gray-900">{{ $file->traffic }}</div>
                                            </div>
                                            <div>
                                                <span class="text-sm font-medium text-gray-500">Duration:</span>
                                                <div class="text-sm text-gray-900">{{ $file->duration }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="space-y-4">
                                        <h4 class="font-semibold text-gray-900 border-b pb-2">Client & Job Details</h4>
                                        <div class="space-y-3">
                                            <div>
                                                <span class="text-sm font-medium text-gray-500">Client:</span>
                                                <div class="text-sm text-gray-900">{{ $file->client }}</div>
                                            </div>
                                            <div>
                                                <span class="text-sm font-medium text-gray-500">Job Number:</span>
                                                <div class="text-sm text-gray-900">{{ $file->job_number ?? '-' }}</div>
                                            </div>
                                            <div>
                                                <span class="text-sm font-medium text-gray-500">Date Finish:</span>
                                                <div class="text-sm text-gray-900">{{ $file->date_finish ?? '-' }}</div>
                                            </div>
                                            <div>
                                                <span class="text-sm font-medium text-gray-500">Artwork:</span>
                                                <div class="text-sm text-gray-900">{{ $file->artwork ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="space-y-4">
                                        <h4 class="font-semibold text-gray-900 border-b pb-2">Invoice Information</h4>
                                        <div class="space-y-3">
                                            <div>
                                                <span class="text-sm font-medium text-gray-500">Invoice Date:</span>
                                                <div class="text-sm text-gray-900">{{ $file->invoice_date ?? '-' }}</div>
                                            </div>
                                            <div>
                                                <span class="text-sm font-medium text-gray-500">Invoice Number:</span>
                                                <div class="text-sm text-gray-900">{{ $file->invoice_number ?? '-' }}</div>
                                            </div>
                                            <div>
                                                <span class="text-sm font-medium text-gray-500">Current Location:</span>
                                                <div class="text-sm text-gray-900">{{ $file->location ?? 'Not specified' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Editable Fields Card -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Edit Information</h3>

                                @if(session('success'))
                                    <div class="mb-4 bg-green-50 border border-green-200 rounded-md p-4">
                                        <div class="flex">
                                            <svg class="w-5 h-5 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            <div class="text-sm text-green-800">{{ session('success') }}</div>
                                        </div>
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('confirmation.update', $file->id) }}" class="space-y-6">
                                    @csrf

                                    <!-- Remarks Section -->
                                    <div>
                                        <label for="remarks" class="block text-sm font-medium text-gray-700 mb-2">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                            </svg>
                                            Remarks
                                        </label>
                                        <textarea name="remarks" id="remarks" rows="4"
                                                  class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-150"
                                                  placeholder="Add any remarks or notes...">{{ $file->remarks }}</textarea>
                                    </div>

                                    <!-- Location Section -->
                                    <div>
                                        <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            Location
                                        </label>
                                        <input type="text" name="location" id="location" value="{{ $file->location }}"
                                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-150"
                                               placeholder="Enter current location..." />
                                    </div>

                                    <!-- Monthly Checkboxes Section -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-3">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2z"></path>
                                            </svg>
                                            Monthly Tracking
                                        </label>
                                        <div class="bg-gray-50 rounded-lg p-4">
                                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                                @foreach(['jan' => 'January', 'feb' => 'February', 'mar' => 'March', 'apr' => 'April', 'may' => 'May', 'jun' => 'June', 'jul' => 'July', 'aug' => 'August', 'sep' => 'September', 'oct' => 'October', 'nov' => 'November', 'dec' => 'December'] as $monthCode => $monthName)
                                                    <label class="flex items-center space-x-2 p-2 rounded-md hover:bg-white transition-colors duration-150 cursor-pointer">
                                                        <input type="checkbox" name="check_{{ $monthCode }}"
                                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                                               {{ $file["check_$monthCode"] ? 'checked' : '' }}>
                                                        <span class="text-sm text-gray-700">{{ $monthName }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    <h2 class="text-lg font-semibold mt-8">Timeline</h2>

                                    <form method="POST" action="{{ route('masterfile.timeline.update', $file->id) }}">
                                        @csrf

                                        @foreach(['product','site','client','payment','material_received','artwork','approval','sent_to_printer','installation','dismantle'] as $field)
                                            <div class="flex items-center mb-2">
                                                <input type="checkbox" id="{{ $field }}" name="{{ $field }}"
                                                    {{ optional($file->timeline)->$field ? 'checked disabled' : '' }}>
                                                <label for="{{ $field }}" class="ml-2 capitalize">{{ str_replace('_', ' ', $field) }}</label>
                                                @if(optional($file->timeline)->$field)
                                                    <span class="ml-4 text-sm text-gray-600">{{ \Carbon\Carbon::parse($file->timeline->$field)->format('M d, Y H:i') }}</span>
                                                @endif
                                            </div>
                                        @endforeach

                                        <div class="mt-4">
                                            <label>Remarks:</label>
                                            <textarea name="remarks" class="w-full border rounded-md p-2">{{ old('remarks', optional($file->timeline)->remarks) }}</textarea>
                                        </div>

                                        <div class="mt-2">
                                            <label>Next Follow Up:</label>
                                            <input type="text" name="next_follow_up" class="w-full border rounded-md p-2"
                                                value="{{ old('next_follow_up', optional($file->timeline)->next_follow_up) }}">
                                        </div>

                                        <button class="mt-4 bg-indigo-600 text-white px-4 py-2 rounded">Update Timeline</button>
                                    </form>


                                    <!-- Action Buttons -->
                                    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                            </svg>
                                            Back to Dashboard
                                        </a>

                                        <button type="submit" class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Update Information
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Mobile sidebar functionality (if needed)
        const openSidebarBtn = document.getElementById('open-sidebar');
        if (openSidebarBtn) {
            openSidebarBtn.addEventListener('click', function() {
                // Add your sidebar functionality here
            });
        }

        // Success message auto-hide
        window.addEventListener('load', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('success')) {
                const message = document.createElement('div');
                message.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
                message.textContent = 'Information updated successfully!';
                document.body.appendChild(message);

                setTimeout(() => {
                    message.remove();
                }, 3000);
            }
        });

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const submitButton = this.querySelector('button[type="submit"]');
            submitButton.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Updating...
            `;
            submitButton.disabled = true;
        });
    </script>
</x-app-layout>
