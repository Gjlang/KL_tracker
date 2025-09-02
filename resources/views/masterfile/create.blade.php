{{--
    Product Picker Implementation Notes:
    - Two-step selection: Category (KLTG/Social Media Management/Outdoor) → Product
    - Maintains original storage: posts single 'product' field with leaf value
    - Auto-detects category from old('product') on validation errors
    - Uses Alpine.js for reactive category/product switching
--}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-3xl text-gray-800 leading-tight">
            🚀 Add New Master File
        </h2>
        <p class="mt-1 text-gray-500 text-sm">Fill out the form below to add a new entry to the masterfile database.</p>
    </x-slot>

     <div class="max-w-6xl mx-auto py-12 px-6">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 text-sm font-semibold">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5"></path>
                </svg>
                Back to Dashboard
            </a>
        </div>

    <div class="max-w-6xl mx-auto py-12 px-6">
        <div class="bg-gradient-to-br from-blue-50 via-blue-100 to-indigo-200 border border-gray-200 rounded-2xl shadow-xl p-10">
            <form action="{{ route('masterfile.store') }}" method="POST" class="space-y-10">
                @csrf

                <!-- Section: Basic Info -->
<div class="bg-white p-6 rounded-xl shadow-lg">
    <h3 class="text-xl font-semibold text-indigo-700 mb-4">📋 Basic Information</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="month" class="text-gray-700 font-medium mb-1 block">Month</label>
            <input type="text" name="month" id="month" placeholder="e.g., July" value="{{ old('month') }}"
                   class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 shadow-md hover:shadow-lg transition duration-300 ease-in-out"
                   required>
        </div>

        <div>
            <label for="date" class="text-gray-700 font-medium mb-1 block">Date</label>
            <input type="date" name="date" id="date" value="{{ old('date') }}"
                   class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 shadow-md hover:shadow-lg transition duration-300 ease-in-out"
                   required>
        </div>

        <div>
            <label for="company" class="text-gray-700 font-medium mb-1 block">Company</label>
            <input type="text" name="company" id="company" placeholder="Company Name" value="{{ old('company') }}"
                   class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 shadow-md hover:shadow-lg transition duration-300 ease-in-out"
                   required>
        </div>

        <div>
            <label for="client" class="text-gray-700 font-medium mb-1 block">Person In Charge</label>
            <input type="text" name="client" id="client" placeholder="PIC Name" value="{{ old('client') }}"
                   class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 shadow-md hover:shadow-lg transition duration-300 ease-in-out"
                   required>
        </div>

        <!-- ✅ New: Contact Number -->
        <div>
            <label for="contact_number" class="text-gray-700 font-medium mb-1 block">Contact Number</label>
            <input type="text" name="contact_number" id="contact_number" placeholder="e.g., +60 12-3456789"
                   value="{{ old('contact_number') }}"
                   class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 shadow-md hover:shadow-lg transition duration-300 ease-in-out">
        </div>

        <!-- ✅ New: Email -->
        <div>
            <label for="email" class="text-gray-700 font-medium mb-1 block">Email</label>
            <input type="email" name="email" id="email" placeholder="e.g., example@email.com"
                   value="{{ old('email') }}"
                   class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 shadow-md hover:shadow-lg transition duration-300 ease-in-out">
        </div>
    </div>
</div>

                <!-- Section: Product Info -->
                <div class="bg-white p-6 rounded-xl shadow-lg mt-8" x-data="productPicker()">
                    <h3 class="text-xl font-semibold text-indigo-700 mb-4">📦 Product & Traffic Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Product Category & Selection -->
                        <div class="md:col-span-2">
                            <label class="text-gray-700 font-medium mb-3 block">Product Category & Type</label>

                            <!-- Category Tabs -->
                            <div class="flex flex-wrap gap-2 mb-4">
                                <button type="button"
                                        @click="selectCategory('KLTG')"
                                        :class="selectedCategory === 'KLTG'
                                            ? 'bg-indigo-600 text-white shadow-lg'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                        class="px-4 py-2 rounded-lg font-medium text-sm transition duration-200 ease-in-out focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                    KLTG
                                </button>
                                <button type="button"
                                        @click="selectCategory('Social Media Management')"
                                        :class="selectedCategory === 'Social Media Management'
                                            ? 'bg-indigo-600 text-white shadow-lg'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                        class="px-4 py-2 rounded-lg font-medium text-sm transition duration-200 ease-in-out focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                    Social Media Management
                                </button>
                                <button type="button"
                                        @click="selectCategory('Outdoor')"
                                        :class="selectedCategory === 'Outdoor'
                                            ? 'bg-indigo-600 text-white shadow-lg'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                        class="px-4 py-2 rounded-lg font-medium text-sm transition duration-200 ease-in-out focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                    Outdoor
                                </button>
                            </div>

                            <!-- Product Select -->
                            <select name="product"
                                    x-model="selectedProduct"
                                    @change="refreshNumbers()"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 shadow-md hover:shadow-lg transition duration-300 ease-in-out"
                                    required>
                                <option value="">Select a product...</option>
                                <template x-for="product in getCurrentProducts()" :key="product.value">
                                    <option :value="product.value" x-text="product.label"></option>
                                </template>
                            </select>

                            <!-- Debug info (remove in production) -->
                            <div x-show="true" class="text-xs text-gray-500 mt-1">
                                Selected Category: <span x-text="selectedCategory"></span> |
                                Products Available: <span x-text="getCurrentProducts().length"></span>
                            </div>

                            @error('product')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="traffic" class="text-gray-700 font-medium mb-1 block">Traffic</label>
                            <input type="text" name="traffic" id="traffic" placeholder="Traffic Details" value="{{ old('traffic') }}" class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 shadow-md hover:shadow-lg transition duration-300 ease-in-out" required>
                        </div>

                        <div>
                            <label for="duration" class="text-gray-700 font-medium mb-1 block">Duration</label>
                            <input type="text" name="duration" id="duration" placeholder="e.g., 3 months" value="{{ old('duration') }}" class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 shadow-md hover:shadow-lg transition duration-300 ease-in-out" required>
                        </div>

                        <div>
                            <label for="date_finish" class="text-gray-700 font-medium mb-1 block">Date Finish</label>
                            <input type="date" name="date_finish" id="date_finish" value="{{ old('date_finish') }}" class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 shadow-md hover:shadow-lg transition duration-300 ease-in-out" required>
                        </div>

                        <div>
                            <label for="status" class="text-gray-700 font-medium mb-1 block">Status</label>
                            <select name="status" id="status" class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 shadow-md hover:shadow-lg transition duration-300 ease-in-out" required>
                                <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            </select>
                        </div>

                        <div>
                            <label for="artwork" class="text-gray-700 font-medium mb-1 block">Artwork</label>
                            <select name="artwork" id="artwork" class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 shadow-md hover:shadow-lg transition duration-300 ease-in-out" required>
                                <option value="BGOC" {{ old('artwork') === 'BGOC' ? 'selected' : '' }}>BGOC</option>
                                <option value="Client" {{ old('artwork') === 'Client' ? 'selected' : '' }}>Client</option>
                            </select>
                        </div>

                        <div>
                            <label for="job_number" class="block text-sm font-medium text-gray-700">Job Number</label>
                            <input type="text" name="job_number" id="job_number" value="{{ old('job_number') }}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                readonly>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <button type="submit" class="w-full md:w-auto bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-xl font-semibold shadow-xl transition duration-300 ease-in-out">
                        ➕ Save Master File
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Load Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
    // Product Picker Alpine.js Component
    function productPicker() {
        return {
            categories: {
                'KLTG': [
                    { label: 'KLTG', value: 'KLTG' },
                    { label: 'KLTG listing', value: 'KLTG listing' },
                    { label: 'KLTG Quarter Page', value: 'KLTG quarter page' }
                ],
                'Social Media Management': [
                    { label: 'TikTok Management', value: 'TikTok Management' },
                    { label: 'YouTube Management', value: 'YouTube Management' },
                    { label: 'FB/IG Management', value: 'FB/IG Management' },
                    { label: 'FB Sponsored Ads', value: 'FB IG Ad' },
                    { label: 'TikTok Management Boost', value: 'TikTok Management Boost' },
                    { label: 'Giveaways/ Contest Management', value: 'Giveaways/ Contest Management' },
                    { label: 'Xiaohongshu Management', value: 'Xiaohongshu Management' }
                ],
                'Outdoor': [
                    { label: 'TB - Tempboard', value: 'TB' },
                    { label: 'BB - Billboard', value: 'BB' },
                    { label: 'Newspaper', value: 'NP' },
                    { label: 'Bunting', value: 'Bunting' },
                    { label: 'Flyers', value: 'Flyers' },
                    { label: 'Star', value: 'Star' },
                    { label: 'Signages', value: 'Signages' }
                ]
            },
            selectedCategory: '',
            selectedProduct: '{{ old('product', '') }}',

            init() {
                // Auto-detect category from old product value on page load
                if (this.selectedProduct) {
                    this.detectCategoryFromProduct(this.selectedProduct);
                } else {
                    // Default to first category (KLTG)
                    this.selectedCategory = 'KLTG';
                }
            },

            selectCategory(category) {
                this.selectedCategory = category;
                // Clear product selection when switching categories
                if (!this.isProductInCategory(this.selectedProduct, category)) {
                    this.selectedProduct = '';
                }
            },

            detectCategoryFromProduct(productValue) {
                for (const [category, products] of Object.entries(this.categories)) {
                    if (products.some(p => p.value === productValue)) {
                        this.selectedCategory = category;
                        break;
                    }
                }
            },

            isProductInCategory(productValue, category) {
                return this.categories[category] && this.categories[category].some(p => p.value === productValue);
            },

            getCurrentProducts() {
                return this.categories[this.selectedCategory] || [];
            }
        }
    }

    // Existing job number refresh functionality
    async function refreshNumbers() {
        const date = document.querySelector('input[name="date"]').value;
        const product = document.querySelector('select[name="product"]').value;

        if (!date || !product) return;

        const url = new URL("{{ route('serials.preview') }}", window.location.origin);
        url.searchParams.set('date', date);
        url.searchParams.set('product', product);

        try {
            const res = await fetch(url);
            if (res.ok) {
                const data = await res.json();
                document.getElementById('job_number').value = data.job_number;
            }
        } catch (error) {
            console.error('Error refreshing job number:', error);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Add listeners for job number refresh
        document.querySelector('input[name="date"]').addEventListener('change', refreshNumbers);

        // Use a slight delay to ensure Alpine has initialized
        setTimeout(() => {
            const productSelect = document.querySelector('select[name="product"]');
            if (productSelect) {
                productSelect.addEventListener('change', refreshNumbers);
            }
            // Initial refresh
            refreshNumbers();
        }, 100);
    });
    </script>
</x-app-layout>
