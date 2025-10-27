<!DOCTYPE html>
<html lang="en">
<link rel="icon" type="image/x-icon" href="{{ asset('images/bluedale_logo_1.png') }}">
<link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Master File</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        .hairline {
            box-shadow: inset 0 0 0 1px #eaeaea;
        }

        .small-caps {
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .tabular {
            font-variant-numeric: tabular-nums;
        }
    </style>
</head>

<body class="font-sans">

    <x-app-layout>
        <div x-data="{ saving: false }" class="w-screen min-h-screen bg-[#F7F7F9]">

            <!-- Sticky Top Toolbar -->
            <div class="sticky top-0 z-50 bg-white/95 backdrop-blur-sm border-b border-[#EAEAEA]">
                <div class="w-full max-w-none px-6 lg:px-10 xl:px-14 py-4">
                    <div class="flex items-center justify-between">
                        <!-- Left: Back -->
                        <a href="{{ route('dashboard') }}"
                            class="inline-flex items-center text-[#22255b] hover:text-[#4bbbed] text-sm font-medium transition-colors duration-200"
                            title="Back to Dashboard">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back to Dashboard
                        </a>

                        <!-- Center: Title -->
                        <div class="text-center">
                            <h1 class="font-serif text-xl text-[#1C1E26] font-medium">Add New Master File</h1>
                            <p class="text-xs text-neutral-500 small-caps">Create</p>
                        </div>

                        <!-- Right: Actions -->
                        <div class="flex items-center gap-3">
                            <button type="button" onclick="history.back()"
                                class="px-4 py-2 text-sm text-neutral-600 hover:bg-neutral-50 rounded-xl transition-colors duration-200"
                                title="Cancel">
                                Cancel
                            </button>
                            <button type="reset" form="mfForm"
                                class="px-4 py-2 text-sm border border-neutral-200 hover:bg-neutral-50 rounded-xl transition-colors duration-200"
                                title="Reset form">
                                Reset
                            </button>
                            <button type="submit" form="mfForm" :disabled="saving"
                                class="px-6 py-2 bg-[#22255b] text-white text-sm rounded-xl hover:opacity-95 disabled:opacity-60 shadow-sm transition-all duration-200 flex items-center gap-2"
                                title="Save master file">
                                <svg x-show="saving" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                <span x-text="saving ? 'Saving...' : 'Save'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="w-full max-w-none px-6 lg:px-10 xl:px-14 py-8 pb-32">

                <!-- Date Display -->
                <div class="mb-6" x-data="{ today: new Date().toLocaleDateString('en-MY', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) }">
                    <p class="text-sm text-neutral-500">
                        Today: <span x-text="today" class="text-neutral-700"></span>
                    </p>
                </div>

                <form id="mfForm" action="{{ route('masterfile.store') }}" method="POST" @submit="saving = true"
                    x-data="productPicker()" class="space-y-8">
                    @csrf

                    <!-- Basic Information -->
                    <div class="rounded-2xl border border-neutral-200/70 shadow-sm bg-white p-6">
                        <h3 class="text-sm text-neutral-600 small-caps mb-6 font-medium">Basic Information</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div>
                                <label for="month"
                                    class="block text-sm font-medium text-[#1C1E26] mb-2">Month</label>

                                @php
                                    $months = [
                                        'January',
                                        'February',
                                        'March',
                                        'April',
                                        'May',
                                        'June',
                                        'July',
                                        'August',
                                        'September',
                                        'October',
                                        'November',
                                        'December',
                                    ];
                                    $selectedMonth = old('month', now()->format('F'));
                                @endphp

                                <select name="month" id="month"
                                    class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200"
                                    required>
                                    @foreach ($months as $m)
                                        <option value="{{ $m }}" @selected($selectedMonth === $m)>
                                            {{ $m }}</option>
                                    @endforeach
                                </select>

                                @error('month')
                                    <p class="mt-1 text-sm text-[#d33831]">{{ $message }}</p>
                                @enderror
                            </div>


                            <div class="flex flex-col flex-1">
                                <label for="company_id" class="text-sm font-medium text-[#1C1E26] mb-2">
                                    Company
                                </label>
                                <select name="company_id" id="company_id"
                                    class="w-full border border-gray-300 rounded-2xl text-sm h-11 focus:outline-none focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed]"
                                    required>
                                    <option value="">-- Select Company --</option>
                                    @foreach ($companies as $id => $label)
                                        <option value="{{ $id }}">{{ $label }}</option>
                                    @endforeach
                                </select>


                            </div>


                            <style>
                                /* Optional – hide dropdown arrow for datalist inputs */
                                input[list]::-webkit-calendar-picker-indicator {
                                    display: none !important;
                                }
                            </style>

                            <div class="flex flex-col flex-1">
                                <label for="client_id" class="text-sm font-medium text-[#1C1E26] mb-2">
                                    Person In Charge
                                </label>
                                <select name="client_id" id="client_id"
                                    class="w-full border border-gray-300 rounded-2xl text-sm h-11 focus:outline-none focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed]">
                                    <option value="">-- Select Person In Charge --</option>
                                </select>
                            </div>

                            <div>
                                <label for="sales_person" class="block text-sm font-medium text-[#1C1E26] mb-2">Sales
                                    Person</label>
                                <input type="text" name="sales_person" id="sales_person"
                                    value="{{ old('sales_person') }}" placeholder="e.g., Aisyah / Daniel"
                                    class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200">
                                @error('sales_person')
                                    <p class="mt-1 text-sm text-[#d33831]">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="contact_number"
                                    class="block text-sm font-medium text-[#1C1E26] mb-2">Contact Number</label>
                                <input type="text" name="contact_number" id="contact_number"
                                    value="{{ old('contact_number') }}" placeholder="e.g., +60 12-3456789"
                                    class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200">
                                @error('contact_number')
                                    <p class="mt-1 text-sm text-[#d33831]">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email"
                                    class="block text-sm font-medium text-[#1C1E26] mb-2">Email</label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}"
                                    placeholder="e.g., example@email.com"
                                    class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200">
                                @error('email')
                                    <p class="mt-1 text-sm text-[#d33831]">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Product & Traffic Details -->
                    <div class="rounded-2xl border border-neutral-200/70 shadow-sm bg-white p-6">
                        <h3 class="text-sm text-neutral-600 small-caps mb-6 font-medium">Product & Traffic Details</h3>

                        <input type="hidden" name="product_category" :value="selectedCategory">

                        <!-- Product Category Selection -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-[#1C1E26] mb-3">Product Category</label>
                            <div class="flex flex-wrap gap-2">
                                <button type="button" @click="selectCategory('KLTG')"
                                    :class="selectedCategory === 'KLTG'
                                        ?
                                        'bg-[#22255b] text-white' :
                                        'border border-neutral-200 text-neutral-600 hover:bg-neutral-50'"
                                    class="px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200 focus:ring-2 focus:ring-[#4bbbed]">
                                    KLTG
                                </button>
                                <button type="button" @click="selectCategory('Social Media Management')"
                                    :class="selectedCategory === 'Social Media Management'
                                        ?
                                        'bg-[#22255b] text-white' :
                                        'border border-neutral-200 text-neutral-600 hover:bg-neutral-50'"
                                    class="px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200 focus:ring-2 focus:ring-[#4bbbed]">
                                    Social Media Management
                                </button>
                                <button type="button" @click="selectCategory('Outdoor')"
                                    :class="selectedCategory === 'Outdoor'
                                        ?
                                        'bg-[#22255b] text-white' :
                                        'border border-neutral-200 text-neutral-600 hover:bg-neutral-50'"
                                    class="px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200 focus:ring-2 focus:ring-[#4bbbed]">
                                    Outdoor
                                </button>
                            </div>
                        </div>

                        <!-- Product Selection -->
                        <div class="mb-6">
                            <label for="product" class="block text-sm font-medium text-[#1C1E26] mb-2">Product
                                Type</label>
                            <select name="product" x-model="selectedProduct" @change="refreshNumbers()"
                                class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200"
                                required>
                                <option value="">Select a product...</option>
                                <template x-for="product in getCurrentProducts()" :key="product.value">
                                    <option :value="product.value" x-text="product.label"></option>
                                </template>
                            </select>
                            @error('product')
                                <p class="mt-1 text-sm text-[#d33831]">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Other Fields -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div>
                                <label for="traffic"
                                    class="block text-sm font-medium text-[#1C1E26] mb-2">Traffic</label>
                                <input type="text" name="traffic" id="traffic" value="{{ old('traffic') }}"
                                    placeholder="Traffic Details"
                                    class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200"
                                    required>
                                @error('traffic')
                                    <p class="mt-1 text-sm text-[#d33831]">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="amount" class="block text-sm font-medium text-[#1C1E26] mb-2">Amount
                                    (MYR)</label>
                                <input type="number" name="amount" id="amount" step="0.01" min="0"
                                    value="{{ old('amount') }}"
                                    class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200 tabular">
                                @error('amount')
                                    <p class="mt-1 text-sm text-[#d33831]">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="duration"
                                    class="block text-sm font-medium text-[#1C1E26] mb-2">Duration</label>
                                <input type="text" name="duration" id="duration" value="{{ old('duration') }}"
                                    placeholder="e.g., 3 months"
                                    class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200"
                                    required>
                                @error('duration')
                                    <p class="mt-1 text-sm text-[#d33831]">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="date" class="block text-sm font-medium text-[#1C1E26] mb-2">Start
                                    Date</label>
                                <input type="date" name="date" id="date" value="{{ old('date') }}"
                                    class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200"
                                    required>
                                @error('date')
                                    <p class="mt-1 text-sm text-[#d33831]">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="date_finish" class="block text-sm font-medium text-[#1C1E26] mb-2">Date
                                    Finish</label>
                                <input type="date" name="date_finish" id="date_finish"
                                    value="{{ old('date_finish') }}"
                                    class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200"
                                    required>
                                @error('date_finish')
                                    <p class="mt-1 text-sm text-[#d33831]">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="status"
                                    class="block text-sm font-medium text-[#1C1E26] mb-2">Status</label>
                                <select name="status" id="status"
                                    class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200"
                                    required>
                                    <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>
                                        Pending</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-[#d33831]">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="artwork"
                                    class="block text-sm font-medium text-[#1C1E26] mb-2">Artwork</label>
                                <select name="artwork" id="artwork"
                                    class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200"
                                    required>
                                    <option value="BGOC" {{ old('artwork') === 'BGOC' ? 'selected' : '' }}>BGOC
                                    </option>
                                    <option value="Client" {{ old('artwork') === 'Client' ? 'selected' : '' }}>Client
                                    </option>
                                </select>
                                @error('artwork')
                                    <p class="mt-1 text-sm text-[#d33831]">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="job_number" class="block text-sm font-medium text-[#1C1E26] mb-2">Job
                                    Number</label>
                                <input type="text" name="job_number" id="job_number"
                                    value="{{ old('job_number') }}"
                                    class="w-full px-4 py-3 border border-neutral-200 rounded-xl bg-neutral-50 tabular"
                                    readonly>
                            </div>

                            <div>
                                <label for="remarks"
                                    class="block text-sm font-medium text-[#1C1E26] mb-2">Remarks</label>
                                <input type="text" name="remarks" id="remarks" value="{{ old('remarks') }}"
                                    placeholder="Remarks"
                                    class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200"
                                    required>
                                @error('remarks')
                                    <p class="mt-1 text-sm text-[#d33831]">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- KLTG Details -->
                    <div x-show="selectedCategory === 'KLTG'" x-cloak
                        class="rounded-2xl border border-neutral-200/70 shadow-sm bg-white p-6">
                        <h3 class="text-sm text-neutral-600 small-caps mb-6 font-medium">KLTG Details</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div>
                                <label for="kltg_industry"
                                    class="block text-sm font-medium text-[#1C1E26] mb-2">Industry</label>
                                <input type="text" name="kltg_industry" id="kltg_industry"
                                    value="{{ old('kltg_industry') }}"
                                    class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200">
                            </div>

                            <div>
                                <label for="kltg_x" class="block text-sm font-medium text-[#1C1E26] mb-2">X</label>
                                <input type="text" name="kltg_x" id="kltg_x" value="{{ old('kltg_x') }}"
                                    class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200">
                            </div>

                            <div>
                                <label for="kltg_edition"
                                    class="block text-sm font-medium text-[#1C1E26] mb-2">Edition</label>
                                <input type="text" name="kltg_edition" id="kltg_edition"
                                    value="{{ old('kltg_edition') }}"
                                    class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200">
                            </div>

                            <div>
                                <label for="kltg_material_cbp"
                                    class="block text-sm font-medium text-[#1C1E26] mb-2">Material C/BP</label>
                                <input type="text" name="kltg_material_cbp" id="kltg_material_cbp"
                                    value="{{ old('kltg_material_cbp') }}"
                                    class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200">
                            </div>

                            <div>
                                <label for="kltg_print"
                                    class="block text-sm font-medium text-[#1C1E26] mb-2">Print</label>
                                <input type="text" name="kltg_print" id="kltg_print"
                                    value="{{ old('kltg_print') }}"
                                    class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200">
                            </div>

                            <div>
                                <label for="kltg_article"
                                    class="block text-sm font-medium text-[#1C1E26] mb-2">Article</label>
                                <input type="text" name="kltg_article" id="kltg_article"
                                    value="{{ old('kltg_article') }}"
                                    class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200">
                            </div>

                            <div>
                                <label for="kltg_video"
                                    class="block text-sm font-medium text-[#1C1E26] mb-2">Video</label>
                                <input type="text" name="kltg_video" id="kltg_video"
                                    value="{{ old('kltg_video') }}"
                                    class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200">
                            </div>

                            <div>
                                <label for="kltg_leaderboard"
                                    class="block text-sm font-medium text-[#1C1E26] mb-2">Leaderboard</label>
                                <input type="text" name="kltg_leaderboard" id="kltg_leaderboard"
                                    value="{{ old('kltg_leaderboard') }}"
                                    class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200">
                            </div>

                            <div>
                                <label for="kltg_qr_code" class="block text-sm font-medium text-[#1C1E26] mb-2">QR
                                    Code</label>
                                <input type="text" name="kltg_qr_code" id="kltg_qr_code"
                                    value="{{ old('kltg_qr_code') }}"
                                    class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200">
                            </div>

                            <div>
                                <label for="kltg_blog"
                                    class="block text-sm font-medium text-[#1C1E26] mb-2">Blog</label>
                                <input type="text" name="kltg_blog" id="kltg_blog"
                                    value="{{ old('kltg_blog') }}"
                                    class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200">
                            </div>

                            <div>
                                <label for="kltg_em" class="block text-sm font-medium text-[#1C1E26] mb-2">EM</label>
                                <input type="text" name="kltg_em" id="kltg_em" value="{{ old('kltg_em') }}"
                                    class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200">
                            </div>

                            <div>
                                <label for="barter"
                                    class="block text-sm font-medium text-[#1C1E26] mb-2">Barter</label>
                                <input type="text" name="barter" id="barter" value="{{ old('barter') }}"
                                    class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200">
                            </div>

                            <div>
                                <label for="kltg_remarks"
                                    class="block text-sm font-medium text-[#1C1E26] mb-2">Remarks (KLTG)</label>
                                <input type="text" name="kltg_remarks" id="kltg_remarks"
                                    value="{{ old('kltg_remarks') }}"
                                    class="w-full px-4 py-3 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed] transition-colors duration-200">
                            </div>
                        </div>
                    </div>


                    <!-- Outdoor Details -->
                    <div x-show="selectedCategory === 'Outdoor'" x-cloak
                        class="rounded-2xl border border-neutral-200/70 shadow-sm bg-white p-6"
                        x-data="outdoorRepeater(() => selectedProduct)" <!-- pass getter so it always reads current sub-product -->

                        <h3 class="text-sm text-neutral-600 small-caps mb-6 font-medium">Outdoor Details</h3>

                        <!-- Add this after the Outdoor Details heading -->
                        <div x-show="$el.closest('div[x-show]').style.display !== 'none' && $errors.has('locations')"
                            class="mb-4 p-3 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-lg">
                            <p class="text-sm" x-text="$errors.get('locations')"></p>
                        </div>

                        <!-- Add this after the Outdoor Details heading -->
                        @if ($errors->has('locations'))
                            <div class="mb-4 p-3 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-lg">
                                <p class="text-sm">{{ $errors->first('locations') }}</p>
                            </div>
                        @endif

                        <!-- Count selector -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-[#1C1E26] mb-2">How many locations?</label>
                            <div class="flex items-center gap-3">
                                <input type="number" min="1" x-model.number="count" @input="resize()" <!--
                                    react immediately -->
                                <button type="button" @click="addOne()"
                                    class="px-3 py-2 rounded-xl border border-neutral-300 hover:bg-neutral-50">+ Add
                                    1</button>
                                <button type="button" @click="removeOne()"
                                    class="px-3 py-2 rounded-xl border border-neutral-300 hover:bg-neutral-50">− Remove
                                    1</button>
                            </div>

                            <button type="button" @click="copyDatesToAll()"
                                class="px-3 py-2 rounded-xl border border-neutral-300 hover:bg-neutral-50">
                                Copy dates from #1 to all
                            </button>

                            <button type="button" @click="copySizeToAll()"
                                class="px-3 py-2 rounded-xl border border-neutral-300 hover:bg-neutral-50">
                                Copy size from #1 to all
                            </button>

                            <button type="button" @click="copySubToAll()"
                                class="px-3 py-2 rounded-xl border border-neutral-300 hover:bg-neutral-50">
                                Copy sub-product from #1 to all
                            </button>
                            <p class="mt-2 text-xs text-neutral-500">You can also add/remove rows anytime.</p>
                        </div>

                        <!-- Repeater rows -->
                        <template x-for="(row, idx) in rows" :key="idx">
                            <div class="mb-4 p-4 rounded-xl border border-neutral-200/80 bg-neutral-50/40">
                                <div class="flex justify-between items-center mb-2">
                                    <h4 class="text-sm font-medium text-[#1C1E26]">Location <span
                                            x-text="idx+1"></span></h4>
                                    <button type="button" @click="removeAt(idx)"
                                        class="text-xs px-2 py-1 rounded-lg border border-neutral-300 hover:bg-neutral-100">Remove</button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-6 gap-3">
                                    <!-- Sub-product override (optional) -->
                                    <div class="md:col-span-1">
                                        <label class="block text-xs text-neutral-600 mb-1">Sub-Product</label>
                                        <select :name="`locations[${idx}][sub_product]`" x-model="row.sub_product"
                                            class="w-full px-3 py-2 rounded-xl border border-neutral-200 focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed]">
                                            <template x-for="opt in outdoorSubProducts" :key="opt">
                                                <option :value="opt" x-text="opt"></option>
                                            </template>
                                        </select>
                                    </div>

                                    <!-- SITE -->
                                    <div class="md:col-span-2">
                                        <label class="block text-xs text-neutral-600 mb-1">Site</label>
                                        <select :id="`outdoor_site_${idx}`" :name="`locations[${idx}][billboard_id]`"
                                            x-model="row.billboard_id"
                                            placeholder="e.g., TB-WPK-0075 – Persiaran Puncak Jalil"
                                            class="w-full px-3 py-2 rounded-xl border border-neutral-200 focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed]"
                                            x-init="$nextTick(() => initSiteSelect(
                                                $el,
                                                () => row.area_key, // filter Site by Area terpilih
                                                (opt) => { // callback saat Site dipilih
                                                    if (opt) {
                                                        row.size = opt.size ?? '';
                                                        row.council = opt.area ?? '';
                                                        row.coords = opt.coords ?? '';
                                                        row.area_key = opt.area_key ?? row.area_key; // auto-sync area
                                            
                                                        fillDependentSelect(`outdoor_size_${idx}`, row.size, row.size);
                                                        fillDependentSelect(`outdoor_area_${idx}`, row.council, row.council);
                                                        fillDependentSelect(`outdoor_coords_${idx}`, row.coords, row.coords);
                                                    }
                                                }
                                            ))">
                                            <template x-if="row.billboard_id">
                                                <option :value="row.billboard_id" selected>Selected</option>
                                            </template>
                                        </select>
                                    </div>

                                    <!-- AREA -->
                                    <div class="md:col-span-1">
                                        <label class="block text-xs text-neutral-600 mb-1">Area</label>
                                        <select :id="`outdoor_area_${idx}`" :name="`locations[${idx}][council]`"
                                            x-model="row.council" placeholder="AREA"
                                            class="w-full px-3 py-2 rounded-xl border border-neutral-200 focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed]"
                                            x-init="$nextTick(() => initAreaSelect(
                                                $el,
                                                '{{ route('outdoor.areas') }}',
                                                (opt) => { // saat Area dipilih
                                                    row.council = opt?.label ?? '';
                                                    row.area_key = opt?.value ?? '';
                                            
                                                    const siteSel = document.getElementById(`outdoor_site_${idx}`);
                                                    if (siteSel?.tomselect) {
                                                        const ts = siteSel.tomselect;
                                                        ts.clearOptions();
                                                        ts.load(''); // reload sites by area_key
                                                        ts.open(); // <-- langsung buka list supaya keliatan semua
                                            
                                                        // kalau site sebelumnya tidak match area baru, kosongkan
                                                        const prev = ts.getValue();
                                                        const prevOpt = ts.options?.[prev];
                                                        if (!prevOpt || prevOpt.area_key !== row.area_key) {
                                                            ts.clear(true);
                                                            row.size = '';
                                                            row.coords = '';
                                                            fillDependentSelect(`outdoor_size_${idx}`, '', '');
                                                            fillDependentSelect(`outdoor_coords_${idx}`, '', '');
                                                        }
                                                    }
                                                }
                                            ))">
                                            <!-- Penting: value harus pakai area_key, bukan label -->
                                            <template x-if="row.council && row.area_key">
                                                <option :value="row.area_key" x-text="row.council" selected></option>
                                            </template>
                                        </select>

                                        <!-- kirimkan area_key ke server -->
                                        <input type="hidden" :name="`locations[${idx}][area_key]`"
                                            x-model="row.area_key">
                                    </div>


                                    <!-- COORDS -->
                                    <div class="md:col-span-1">
                                        <label class="block text-xs text-neutral-600 mb-1">Coords (lat,lng)</label>
                                        <select :id="`outdoor_coords_${idx}`" :name="`locations[${idx}][coords]`"
                                            x-model="row.coords" placeholder="3.154,101.74"
                                            class="w-full px-3 py-2 rounded-xl border border-neutral-200 focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed]"
                                            x-init="$nextTick(() => initSuggestSelect($el, '{{ url('/outdoor/coords') }}', () => row.sub_product))">
                                            <template x-if="row.coords">
                                                <option :value="row.coords" x-text="row.coords" selected></option>
                                            </template>
                                        </select>
                                    </div>


                                    <script>
                                        function initAreaSelect(selectEl, endpoint, onPicked) {
                                            try {
                                                if (selectEl.tomselect) selectEl.tomselect.destroy();

                                                const ts = new TomSelect(selectEl, {
                                                    create: false, // area TIDAK boleh bikin opsi bebas
                                                    persist: false,
                                                    allowEmptyOption: true,
                                                    maxOptions: 500,
                                                    valueField: 'value', // area_key: "stateId|districtId"
                                                    labelField: 'label', // "KL - Bukit Jalil"
                                                    searchField: ['label'],
                                                    plugins: ['clear_button', 'dropdown_input'],
                                                    load: function(query, callback) {
                                                        // controller pakai ?search=... (bukan ?q=...)
                                                        const url = `${endpoint}${query ? ('?search=' + encodeURIComponent(query)) : ''}`;
                                                        fetch(url)
                                                            .then(r => r.json())
                                                            .then(data => callback(data || []))
                                                            .catch(() => callback());
                                                    },
                                                    render: {
                                                        option: (item, esc) => `<div>${esc(item.label)}</div>`,
                                                        item: (item, esc) => `<div>${esc(item.label)}</div>`,
                                                        no_results: () => `<div class="p-2 text-sm text-neutral-500">No matches.</div>`
                                                    }
                                                });

                                                selectEl.tomselect = ts;
                                                ts.on('change', (val) => {
                                                    const opt = ts.options?.[val];
                                                    onPicked && onPicked(opt || null);
                                                });
                                            } catch (e) {
                                                console.warn('initAreaSelect failed', e);
                                            }
                                        }
                                    </script>
                                    <!-- Dates row -->
                                    <div class="md:col-span-2">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <!-- Start Date (right) -->
                                            <div>
                                                <label class="block text-xs text-neutral-600 mb-1">Start Date</label>
                                                <input type="date" :name="`locations[${idx}][start_date]`"
                                                    x-model="row.start_date"
                                                    class="w-full px-3 py-2 rounded-xl border border-neutral-200 focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed]" />
                                            </div>

                                            <!-- End Date (left) -->
                                            <div>
                                                <label class="block text-xs text-neutral-600 mb-1">End Date</label>
                                                <input type="date" :name="`locations[${idx}][end_date]`"
                                                    x-model="row.end_date"
                                                    class="w-full px-3 py-2 rounded-xl border border-neutral-200 focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed]" />
                                            </div>
                                        </div>
                                    </div>
                                    <!-- outdoor status override (optional) -->
                                    <div class="md:col-span-1">
                                        <label class="block text-xs text-neutral-600 mb-1">Status</label>
                                        <select :name="`locations[${idx}][outdoor_status]`"
                                            x-model="row.outdoor_status"
                                            class="w-full px-3 py-2 rounded-xl border border-neutral-200 focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed]">
                                            <template x-for="opt in outdoorStatus" :key="opt">
                                                <option :value="opt" x-text="opt"></option>
                                            </template>
                                        </select>
                                    </div>

                                    <div class="md:col-span-6">
                                        <label class="block text-xs text-neutral-600 mb-1">Remarks</label>
                                        <input type="text" :name="`locations[${idx}][remarks]`"
                                            x-model="row.remarks" placeholder="Near station"
                                            class="w-full px-3 py-2 rounded-xl border border-neutral-200 focus:ring-2 focus:ring-[#4bbbed] focus:border-[#4bbbed]" />
                                    </div>
                                </div>
                            </div>
                        </template>

                        <input type="hidden" name="input_mode" value="structured" />
                    </div>

                    <script>
                        window.outdoorRepeater = function(selectedProductRef) {
                            return {
                                outdoorSubProducts: ['BB', 'TB', 'Newspaper', 'Bunting', 'Flyers', 'Star', 'Signages'],
                                outdoorStatus: ['pending_payment', 'pending_install', 'ongoing', 'completed', 'dismantle'],

                                count: 1,
                                rows: [],

                                // sync toggles
                                syncDates: false,
                                syncSize: false, // NEW
                                syncSub: false, // NEW
                                syncStatus: false, // NEW

                                init() {
                                    const def = (typeof selectedProductRef === 'function') ?
                                        selectedProductRef() : (selectedProductRef || 'BB');
                                    this.rows = [this.emptyRow(def)];
                                    this.count = this.rows.length;

                                    // === keep others synced when toggled ===
                                    // dates
                                    this.$watch('rows[0].start_date', v => {
                                        if (this.syncDates) this.rows.forEach((r, i) => {
                                            if (i) {
                                                r.start_date = v
                                            }
                                        });
                                    });
                                    this.$watch('rows[0].end_date', v => {
                                        if (this.syncDates) this.rows.forEach((r, i) => {
                                            if (i) {
                                                r.end_date = v
                                            }
                                        });
                                    });

                                    // size
                                    this.$watch('rows[0].size', v => {
                                        if (this.syncSize) this.rows.forEach((r, i) => {
                                            if (i) {
                                                r.size = v
                                            }
                                        });
                                    });

                                    // sub-product
                                    this.$watch('rows[0].sub_product', v => {
                                        if (!this.syncSub) return;
                                        // only set if value is in allowed list
                                        if (!this.outdoorSubProducts.includes(v)) return;
                                        this.rows.forEach((r, i) => {
                                            if (i) {
                                                r.sub_product = v
                                            }
                                        });
                                    });

                                    // status
                                    this.$watch('rows[0].outdoor_status', v => {
                                        if (!this.syncStatus) return;
                                        // only set if value is in allowed list
                                        if (!this.outdoorStatus.includes(v)) return;
                                        this.rows.forEach((r, i) => {
                                            if (i) {
                                                r.outdoor_status = v
                                            }
                                        });
                                    });
                                },

                                emptyRow(defaultSub) {
                                    return {
                                        sub_product: defaultSub || 'BB',
                                        billboard_id: '',
                                        site: '',
                                        size: '',
                                        council: '',
                                        coords: '',
                                        remarks: '',
                                        start_date: '',
                                        end_date: '',
                                        outdoor_status: '',
                                    };
                                },

                                // === one-click copy actions ===
                                copyDatesToAll() {
                                    const s = this.rows[0]?.start_date || '';
                                    const e = this.rows[0]?.end_date || '';
                                    this.rows.forEach((r, i) => {
                                        if (i) {
                                            r.start_date = s;
                                            r.end_date = e;
                                        }
                                    });
                                },
                                copySizeToAll() { // NEW
                                    const v = this.rows[0]?.size || '';
                                    this.rows.forEach((r, i) => {
                                        if (i) {
                                            r.size = v;
                                        }
                                    });
                                },
                                copySubToAll() { // NEW
                                    const v = this.rows[0]?.sub_product || '';
                                    if (!this.outdoorSubProducts.includes(v)) return;
                                    this.rows.forEach((r, i) => {
                                        if (i) {
                                            r.sub_product = v;
                                        }
                                    });
                                },

                                // === row management ===
                                resize() {
                                    const def = this.rows[0]?.sub_product || 'BB';
                                    const target = Math.max(1, parseInt(this.count || 1, 10));
                                    while (this.rows.length < target) {
                                        const r = this.emptyRow(def);
                                        // inherit synced fields for new rows
                                        if (this.syncDates) {
                                            r.start_date = this.rows[0]?.start_date || '';
                                            r.end_date = this.rows[0]?.end_date || '';
                                        }
                                        if (this.syncSize) {
                                            r.size = this.rows[0]?.size || '';
                                        }
                                        if (this.syncSub) {
                                            r.sub_product = this.rows[0]?.sub_product || def;
                                        }
                                        if (this.syncStatus) {
                                            r.status = this.rows[0]?.status || '';
                                        }
                                        this.rows.push(r);
                                    }
                                    while (this.rows.length > target) this.rows.pop();
                                },

                                addOne() {
                                    const last = this.rows[this.rows.length - 1];
                                    const def = last?.sub_product || 'BB';
                                    const r = this.emptyRow(def);
                                    // inherit synced fields for the new row
                                    if (this.syncDates) {
                                        r.start_date = this.rows[0]?.start_date || '';
                                        r.end_date = this.rows[0]?.end_date || '';
                                    }
                                    if (this.syncSize) {
                                        r.size = this.rows[0]?.size || '';
                                    }
                                    if (this.syncSub) {
                                        r.sub_product = this.rows[0]?.sub_product || def;
                                    }
                                    if (this.syncStatus) {
                                        r.outdoor_status = this.rows[0]?.outdoor_status || '';
                                    }
                                    this.rows.push(r);
                                    this.count = this.rows.length;
                                },

                                removeOne() {
                                    if (this.rows.length > 1) {
                                        this.rows.pop();
                                        this.count = this.rows.length;
                                    }
                                },

                                removeAt(i) {
                                    if (this.rows.length > 1) {
                                        this.rows.splice(i, 1);
                                        this.count = this.rows.length;
                                    }
                                }
                            }
                        }
                    </script>


                    <style>
                        [x-cloak] {
                            display: none !important;
                        }
                    </style>
                    <!-- Sticky Bottom Action Bar -->
                    <div
                        class="fixed bottom-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-sm border-t border-[#EAEAEA]">
                        <div class="w-full max-w-none px-6 lg:px-10 xl:px-14 py-4">
                            <div class="flex justify-end items-center gap-3">
                                <button type="button" onclick="history.back()"
                                    class="px-4 py-2 text-sm text-neutral-600 hover:bg-neutral-50 rounded-xl transition-colors duration-200"
                                    title="Cancel">
                                    Cancel
                                </button>
                                <button type="reset" form="mfForm"
                                    class="px-4 py-2 text-sm border border-neutral-200 hover:bg-neutral-50 rounded-xl transition-colors duration-200"
                                    title="Reset form">
                                    Reset
                                </button>
                                <button type="submit" form="mfForm" :disabled="saving"
                                    class="px-6 py-2 bg-[#22255b] text-white text-sm rounded-xl hover:opacity-95 disabled:opacity-60 shadow-sm transition-all duration-200 flex items-center gap-2"
                                    title="Save master file">
                                    <svg x-show="saving" class="w-4 h-4 animate-spin" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    <span x-text="saving ? 'Saving...' : 'Save'"></span>
                                </button>
                            </div>
                        </div>
                    </div>
            </div>
            <script>
                function initSiteSelect(selectEl, getAreaKeyFn, onPicked) {
                    try {
                        if (selectEl.tomselect) selectEl.tomselect.destroy();

                        const ts = new TomSelect(selectEl, {
                            create: true, // boleh ketik bebas kalau perlu
                            persist: false,
                            allowEmptyOption: true,
                            maxOptions: 1000,
                            valueField: 'value', // billboard.id
                            labelField: 'label', // "TB-XXXX — Location" (bersih)
                            searchField: ['label', 'site_number', 'location_name'],
                            plugins: ['clear_button', 'dropdown_input'],
                            load: function(query, callback) {
                                const params = new URLSearchParams();
                                // filter by area_key jika ada
                                const areaKey = (typeof getAreaKeyFn === 'function') ? (getAreaKeyFn() || '') : '';
                                if (areaKey) params.set('area_key', areaKey);

                                // controller pakai ?search=... (bukan ?q=...)
                                if (query) params.set('search', query);

                                const base = `{{ route('outdoor.sites') }}`;
                                const url = params.toString() ? `${base}?${params.toString()}` : base;

                                fetch(url)
                                    .then(r => r.json())
                                    .then(items => callback(items || []))
                                    .catch(() => callback());
                            },
                            render: {
                                option: (item, esc) => `<div>${esc(item.label)}</div>`,
                                item: (item, esc) => `<div>${esc(item.label)}</div>`,
                                no_results: () =>
                                    `<div class="p-2 text-sm text-neutral-500">No matches. Press Enter to use your text.</div>`
                            }
                        });

                        selectEl.tomselect = ts;

                        ts.on('change', (value) => {
                            const opt = ts.options?.[value];
                            // jika user pilih opsi valid (id numerik), kirim detailnya
                            if (opt && /^\d+$/.test(String(value))) {
                                onPicked && onPicked(opt);
                            } else {
                                onPicked && onPicked(null);
                            }
                        });
                    } catch (e) {
                        console.warn('initSiteSelect failed', e);
                    }
                }


                // Push a value into another TomSelect (Size/Area/Coords), adding it if needed
                function fillDependentSelect(htmlId, value, label) {
                    const el = document.getElementById(htmlId);
                    if (!el || value == null || value === '') return;
                    const ts = el.tomselect;
                    if (!ts) {
                        el.value = value;
                        return;
                    }
                    // add option if it doesn't exist, then set
                    if (!ts.options[value]) {
                        ts.addOption({
                            value: value,
                            label: label ?? String(value)
                        });
                    }
                    ts.setValue(String(value), true); // silent = true
                }

                document.addEventListener('DOMContentLoaded', () => {
                    // Data PIC ter-group by company_id: { "1":[{id,name,phone,email},...], ... }
                    const CLIENTS_BY_COMPANY = @json($clientsByCompany ?? []);

                    // Company: boleh tambah baru, tetapi value "baru" diprefix 'new:'
                    const tsCompany = new TomSelect('#company_id', {
                        create: (input) => ({
                            value: `new:${input}`,
                            text: input
                        }),
                        persist: false,
                        allowEmptyOption: true,
                        placeholder: '-- Select Company --',
                        plugins: ['dropdown_input', 'clear_button'],
                        maxOptions: 1000,
                    });

                    // PIC: dependent + searchable + boleh tambah baru
                    const tsClient = new TomSelect('#client_id', {
                        create: (input) => ({
                            value: `new:${input}`,
                            text: input
                        }),
                        persist: false,
                        allowEmptyOption: true,
                        placeholder: '-- Select Person In Charge --',
                        plugins: ['dropdown_input', 'clear_button'],
                        maxOptions: 1000,
                        onInitialize() {
                            this.disable();
                        }
                    });

                    // Listen to PIC changes and auto-fill contact & email
                    tsClient.on('change', (val) => {
                        // free-typed new PIC → clear
                        if (!val || String(val).startsWith('new:')) {
                            fillContactEmail('', '');
                            return;
                        }
                        const opt = tsClient.options[val];
                        fillContactEmail(opt?.phone || '', opt?.email || '');
                    });

                    // Helper to fill contact & email inputs
                    function fillContactEmail(phone, email) {
                        const phoneEl = document.getElementById('contact_number');
                        const mailEl = document.getElementById('email');
                        if (phoneEl) phoneEl.value = phone || '';
                        if (mailEl) mailEl.value = email || '';
                    }

                    function loadClients(companyId) {
                        tsClient.clear();
                        tsClient.clearOptions();

                        const keyStr = String(companyId);
                        const keyNum = Number(companyId);
                        const list = CLIENTS_BY_COMPANY[keyStr] || CLIENTS_BY_COMPANY[keyNum] || [];

                        if (!companyId || !list.length) {
                            tsClient.addOption({
                                value: '',
                                text: 'No PIC found for this company'
                            });
                            tsClient.setValue('');
                            tsClient.disable();
                            tsClient.refreshOptions(false);
                            // clear dependent fields
                            fillContactEmail('', '');
                            return;
                        }

                        // ⬇️ include phone & email on each option
                        list.forEach(c => tsClient.addOption({
                            value: String(c.id),
                            text: c.name,
                            phone: c.phone || '',
                            email: c.email || ''
                        }));

                        tsClient.enable();
                        tsClient.refreshOptions(false);

                        // (optional) auto-select when only one PIC
                        if (list.length === 1) {
                            tsClient.setValue(String(list[0].id), true);
                            // trigger fill for single PIC
                            const singleOpt = tsClient.options[String(list[0].id)];
                            fillContactEmail(singleOpt?.phone || '', singleOpt?.email || '');
                        }
                    }

                    tsCompany.on('change', (val) => {
                        // kalau user mengetik company baru (value diawali 'new:'), jangan load PIC
                        if (!val || String(val).startsWith('new:')) {
                            tsClient.clear();
                            tsClient.clearOptions();
                            tsClient.addOption({
                                value: '',
                                text: 'No PIC found for this company'
                            });
                            tsClient.setValue('');
                            tsClient.disable();
                            tsClient.refreshOptions(false);
                            // clear dependent fields
                            fillContactEmail('', '');
                            return;
                        }
                        // existing company → load PIC
                        loadClients(val);
                    });

                    // Restore old() kalau ada
                    const oldCompany = "{{ old('company_id') }}";
                    const oldClient = "{{ old('client_id') }}";
                    if (oldCompany) {
                        tsCompany.setValue(String(oldCompany), true);
                        if (!String(oldCompany).startsWith('new:')) {
                            loadClients(String(oldCompany));
                            if (oldClient) {
                                tsClient.setValue(String(oldClient), true);
                                // Also fill contact/email if restoring old client
                                const opt = tsClient.options[String(oldClient)];
                                if (opt) {
                                    fillContactEmail(opt.phone || '', opt.email || '');
                                }
                            }
                        }
                    }
                });

                function productPicker() {
                    return {
                        // ===== Data =====
                        categories: {
                            'KLTG': [{
                                    label: 'THE GUIDE',
                                    value: 'THE GUIDE'
                                },
                                {
                                    label: 'KLTG listing',
                                    value: 'KLTG listing'
                                },
                                {
                                    label: 'KLTG Quarter Page',
                                    value: 'KLTG Quarter Page'
                                },
                            ],
                            'Social Media Management': [{
                                    label: 'TikTok Management',
                                    value: 'TikTok Management'
                                },
                                {
                                    label: 'YouTube Management',
                                    value: 'YouTube Management'
                                },
                                {
                                    label: 'FB/IG Management',
                                    value: 'FB/IG Management'
                                },
                                {
                                    label: 'FB Sponsored Ads',
                                    value: 'FB Sponsored Ads'
                                }, // was "FB IG Ad"
                                {
                                    label: 'TikTok Management Boost',
                                    value: 'TikTok Management Boost'
                                },
                                {
                                    label: 'Giveaways/ Contest Management',
                                    value: 'Giveaways/ Contest Management'
                                },
                                {
                                    label: 'Xiaohongshu Management',
                                    value: 'Xiaohongshu Management'
                                },
                            ],
                            'Outdoor': [{
                                    label: 'TB - Tempboard',
                                    value: 'TB'
                                },
                                {
                                    label: 'BB - Billboard',
                                    value: 'BB'
                                },
                                {
                                    label: 'Newspaper',
                                    value: 'Newspaper'
                                },
                                {
                                    label: 'Bunting',
                                    value: 'Bunting'
                                },
                                {
                                    label: 'Flyers',
                                    value: 'Flyers'
                                },
                                {
                                    label: 'Star',
                                    value: 'Star'
                                },
                                {
                                    label: 'Signages',
                                    value: 'Signages'
                                },
                            ],
                        },

                        selectedCategory: '',
                        selectedProduct: @json(old('product', '')),

                        // ===== Lifecycle =====
                        init() {
                            // Detect category from old product or default to KLTG
                            if (this.selectedProduct) this.detectCategoryFromProduct(this.selectedProduct);
                            if (!this.selectedCategory) this.selectedCategory = 'KLTG';

                            // Keep hidden input in sync
                            this._syncHiddenCategory();

                            // Expose refreshNumbers for legacy @change="refreshNumbers()"
                            window.refreshNumbers = this.refreshNumbers.bind(this);

                            // Auto-refresh when product/date changes
                            const dateEl = document.querySelector('input[name="date"]');
                            if (dateEl) dateEl.addEventListener('change', () => this.refreshNumbers());
                            this.$watch('selectedProduct', () => this.refreshNumbers());

                            // Initial attempt
                            this.refreshNumbers();
                        },

                        // ===== Actions =====
                        selectCategory(category) {
                            this.selectedCategory = category;
                            // Clear product if it doesn't belong to new category
                            if (!this.isProductInCategory(this.selectedProduct, category)) {
                                this.selectedProduct = '';
                            }
                            this._syncHiddenCategory();
                        },

                        detectCategoryFromProduct(productValue) {
                            for (const [category, products] of Object.entries(this.categories)) {
                                if (products.some(p => p.value === productValue)) {
                                    this.selectedCategory = category;
                                    return;
                                }
                            }
                        },

                        isProductInCategory(productValue, category) {
                            return !!(this.categories[category] && this.categories[category].some(p => p.value === productValue));
                        },

                        getCurrentProducts() {
                            return this.categories[this.selectedCategory] || [];
                        },

                        _syncHiddenCategory() {
                            const hidden = document.querySelector('input[name="product_category"]');
                            if (hidden) hidden.value = this.selectedCategory;
                        },

                        // ===== Job number preview =====
                        async refreshNumbers() {
                            const dateEl = document.querySelector('input[name="date"]');
                            const prodEl = document.querySelector('select[name="product"]');
                            const jobEl = document.getElementById('job_number');

                            const date = dateEl?.value || '';
                            const product = prodEl?.value || this.selectedProduct || '';

                            if (!date || !product || !jobEl) return;

                            try {
                                // {{-- TODO: Replace with actual route if it exists --}}
                                const url = new URL('/serials/preview', window.location.origin);
                                url.searchParams.set('date', date);
                                url.searchParams.set('product', product);

                                const res = await fetch(url.toString(), {
                                    headers: {
                                        'Accept': 'application/json'
                                    }
                                });
                                if (!res.ok) return; // don't block the form if backend errs

                                const data = await res.json();
                                if (data?.job_number) jobEl.value = data.job_number;
                            } catch (e) {
                                // Silent fail to keep UX smooth
                                console.warn('refreshNumbers failed:', e);
                            }
                        },
                    };
                }
            </script>
    </x-app-layout>
    <!-- Error Popup Modal -->
    <div id="errorModal"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50 hidden">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-red-500 mr-3 mt-1" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01M12 17h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Booking Conflict</h3>
                    <p class="text-sm text-gray-700 mb-4" id="errorMessage">
                        {{ session('error') }}
                    </p>
                    <div class="flex justify-end">
                        <button type="button" onclick="closeErrorModal()"
                            class="px-4 py-2 bg-[#22255b] text-white text-sm rounded-xl hover:opacity-95 transition-colors duration-200">
                            OK
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function closeErrorModal() {
            document.getElementById('errorModal').classList.add('hidden');
        }

        // Show the modal if there's an error message
        document.addEventListener('DOMContentLoaded', function() {
            const errorMessage = "{{ session('error') }}";
            if (errorMessage) {
                document.getElementById('errorModal').classList.remove('hidden');
            }
        });
    </script>
</body>

</html>
