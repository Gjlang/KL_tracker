<x-app-layout>
    <div class="min-h-screen bg-gray-50">
        <!-- Main content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header with back button -->
            <div class="mb-8">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Back to Dashboard
                    </a>
                    <div class="text-sm text-gray-500">
                        <span>Company Details</span>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('masterfile.update', $file->id) }}" id="mf-edit-form">
                @csrf
                @method('PUT')

                <!-- Company Header Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8 overflow-hidden">
                    <div class="px-8 py-6">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                            <div class="flex-1">
                                <div class="mb-4">
                                    <input name="company" class="text-3xl font-bold text-gray-900 bg-transparent border-0 p-0 w-full editable read-mode focus:ring-0" value="{{ old('company', $file->company) }}" disabled>
                                </div>
                                <div class="flex flex-wrap items-center gap-3">
                                    <div class="flex items-center">
                                        <span class="text-sm font-medium text-gray-500 mr-2">Status:</span>
                                        <select name="status" class="editable read-mode status-badge px-3 py-1 text-sm font-medium rounded-full border-0 focus:ring-0" disabled>
                                            @foreach(['pending','ongoing','completed'] as $s)
                                                <option value="{{ $s }}" @selected(old('status',$file->status)===$s)>{{ ucfirst($s) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="text-sm font-medium text-gray-500 mr-2">Product:</span>
                                        <input name="product" class="editable read-mode bg-blue-50 text-blue-800 px-3 py-1 text-sm font-medium rounded-full border-0 focus:ring-0" value="{{ old('product',$file->product) }}" disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 lg:gap-6">
                                <div class="text-right text-sm text-gray-500">
                                    <div class="font-medium text-gray-900">ID: #{{ $file->id }}</div>
                                    <div>Created: {{ $file->created_at ? $file->created_at->format('M d, Y') : 'N/A' }}</div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex items-center gap-3">


                                   <a href="{{ route('masterfile.print', ['file' => $file->id]) }}"
                                        class="inline-flex items-center px-3 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">
                                        Download PDF
                                    </a>


                                    <button type="button" id="btnEdit" class="inline-flex items-center px-6 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Edit
                                    </button>
                                    <button type="submit" id="btnSave" class="hidden inline-flex items-center px-6 py-2.5 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-150">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Save
                                    </button>
                                    <button type="button" id="btnCancel" class="hidden inline-flex items-center px-6 py-2.5 bg-gray-200 text-gray-800 text-sm font-medium rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-150">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        Cancel
                                    </button>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Details Cards Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Project Information Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Project Information
                            </h3>
                        </div>
                        <div class="p-6 space-y-6">
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-500">Date</label>
                                <input type="date" name="date" class="editable read-mode w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ old('date', \Illuminate\Support\Str::of($file->date)->substr(0,10)) }}" disabled>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-500">Month</label>
                                <input name="month" class="editable read-mode w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ old('month',$file->month) }}" disabled>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-500">Traffic</label>
                                <input name="traffic" class="editable read-mode w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ old('traffic',$file->traffic) }}" disabled>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-500">Duration</label>
                                <input name="duration" class="editable read-mode w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ old('duration',$file->duration) }}" disabled>
                            </div>
                        </div>
                    </div>

                    <!-- Client & Job Details Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-emerald-50 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                Person In Charge & Job Details
                            </h3>
                        </div>
                        <div class="p-6 space-y-6">
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-500">Client</label>
                                <input name="client" class="editable read-mode w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" value="{{ old('client',$file->client) }}" disabled>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-500">Job Number</label>
                                <input name="job_number" class="editable read-mode w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" value="{{ old('job_number',$file->job_number) }}" disabled>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-500">Date Finish</label>
                                <input type="date" name="date_finish" class="editable read-mode w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" value="{{ old('date_finish', \Illuminate\Support\Str::of($file->date_finish)->substr(0,10)) }}" disabled>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-500">Artwork</label>
                                <input name="artwork" class="editable read-mode w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" value="{{ old('artwork',$file->artwork) }}" disabled>
                            </div>
                        </div>
                    </div>

                    <!-- Invoice Information Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-pink-50 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Invoice Information
                            </h3>
                        </div>
                        <div class="p-6 space-y-6">
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-500">Invoice Date</label>
                                <input type="date" name="invoice_date" class="editable read-mode w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" value="{{ old('invoice_date', \Illuminate\Support\Str::of($file->invoice_date)->substr(0,10)) }}" disabled>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-500">Invoice Number</label>
                                <input name="invoice_number" class="editable read-mode w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" value="{{ old('invoice_number',$file->invoice_number) }}" disabled>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-500">Current Location</label>
                                <input name="location" class="editable read-mode w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" value="{{ old('location',$file->location) }}" disabled>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Enhanced JavaScript -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const btnEdit = document.getElementById('btnEdit');
        const btnSave = document.getElementById('btnSave');
        const btnCancel = document.getElementById('btnCancel');
        const editables = document.querySelectorAll('.editable');
        const statusSelect = document.querySelector('select[name="status"]');

        function updateStatusBadge() {
            const status = statusSelect.value;
            statusSelect.className = statusSelect.className.replace(/bg-\w+-\d+/g, '').replace(/text-\w+-\d+/g, '');

            if (statusSelect.disabled) {
                // Read mode - styled as badge
                statusSelect.classList.add('status-badge');
                if (status === 'completed') {
                    statusSelect.classList.add('bg-green-100', 'text-green-800');
                } else if (status === 'ongoing') {
                    statusSelect.classList.add('bg-yellow-100', 'text-yellow-800');
                } else {
                    statusSelect.classList.add('bg-red-100', 'text-red-800');
                }
            } else {
                // Edit mode - normal select styling
                statusSelect.classList.remove('status-badge');
                statusSelect.classList.add('border-gray-200', 'bg-white');
            }
        }

        const setDisabled = (disabled) => {
            editables.forEach(el => {
                el.disabled = disabled;

                if (disabled) {
                    // Read mode styling
                    el.classList.add('read-mode');
                    if (el.name === 'company') {
                        el.classList.add('bg-transparent', 'border-0');
                    } else if (el.name === 'product') {
                        el.classList.add('bg-blue-50', 'text-blue-800', 'border-0');
                    } else {
                        el.classList.add('bg-gray-50', 'border-gray-100', 'text-gray-700');
                    }
                } else {
                    // Edit mode styling
                    el.classList.remove('read-mode');
                    if (el.name === 'company') {
                        el.classList.remove('bg-transparent', 'border-0');
                        el.classList.add('border-gray-200', 'bg-white');
                    } else if (el.name === 'product') {
                        el.classList.remove('bg-blue-50', 'text-blue-800', 'border-0');
                        el.classList.add('border-gray-200', 'bg-white', 'text-gray-900');
                    } else {
                        el.classList.remove('bg-gray-50', 'border-gray-100', 'text-gray-700');
                        el.classList.add('bg-white', 'border-gray-200', 'text-gray-900');
                    }
                }
            });

            updateStatusBadge();
        };

        btnEdit.addEventListener('click', () => {
            setDisabled(false);
            btnEdit.classList.add('hidden');
            btnSave.classList.remove('hidden');
            btnCancel.classList.remove('hidden');
        });

        btnCancel.addEventListener('click', () => {
            window.location.reload();
        });

        statusSelect.addEventListener('change', updateStatusBadge);

        // Initialize read mode
        setDisabled(true);
    });
    </script>

    <!-- Enhanced Styling -->
    <style>
    .read-mode {
        cursor: default !important;
        pointer-events: none;
    }

    .read-mode:focus {
        outline: none !important;
        ring: 0 !important;
        border-color: inherit !important;
        box-shadow: none !important;
    }

    .status-badge {
        appearance: none;
        cursor: default;
        pointer-events: none;
    }

    .status-badge:focus {
        outline: none;
        box-shadow: none;
    }

    /* Smooth transitions */
    .editable {
        transition: all 0.2s ease-in-out;
    }

    /* Mobile responsiveness */
    @media (max-width: 640px) {
        .grid {
            grid-template-columns: 1fr;
        }

        .flex-col.sm\\:flex-row {
            flex-direction: column;
        }

        .text-3xl {
            font-size: 1.875rem;
        }
    }

    /* Card hover effects */
    .bg-white {
        transition: box-shadow 0.2s ease-in-out;
    }

    .bg-white:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    </style>
</x-app-layout>
