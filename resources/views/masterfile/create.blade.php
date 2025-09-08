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

        <!-- ✅ New: Sales Person -->
        <div>
            <label for="sales_person" class="text-gray-700 font-medium mb-1 block">Sales Person</label>
            <input type="text" name="sales_person" id="sales_person" placeholder="e.g., Aisyah / Daniel"
                value="{{ old('sales_person') }}"
                class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 shadow-md hover:shadow-lg transition duration-300 ease-in-out">
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

    <!-- keep product_category in the payload -->
    <input type="hidden" name="product_category" :value="selectedCategory">

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
            <input type="text" name="traffic" id="traffic" placeholder="Traffic Details" value="{{ old('traffic') }}"
                   class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 shadow-md hover:shadow-lg transition duration-300 ease-in-out" required>
        </div>

        <div>
            <label for="amount" class="text-gray-700 font-medium mb-1 block">Amount (MYR)</label>
            <input type="number" name="amount" id="amount" step="0.01" min="0"
                    value="{{ old('amount') }}"
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 shadow-md hover:shadow-lg transition">
        </div>


        <div>
            <label for="duration" class="text-gray-700 font-medium mb-1 block">Remarks</label>
            <input type="text" name="duration" id="duration" placeholder="e.g., 3 months" value="{{ old('duration') }}"
                   class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 shadow-md hover:shadow-lg transition duration-300 ease-in-out" required>
        </div>

         <div>
            <label for="date" class="text-gray-700 font-medium mb-1 block">Start Date</label>
            <input type="date" name="date" id="date" value="{{ old('date') }}"
                   class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 shadow-md hover:shadow-lg transition duration-300 ease-in-out"
                   required>
        </div>

        <div>
            <label for="date_finish" class="text-gray-700 font-medium mb-1 block">Date Finish</label>
            <input type="date" name="date_finish" id="date_finish" value="{{ old('date_finish') }}"
                   class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 shadow-md hover:shadow-lg transition duration-300 ease-in-out" required>
        </div>

        <div>
            <label for="status" class="text-gray-700 font-medium mb-1 block">Status</label>
            <select name="status" id="status"
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 shadow-md hover:shadow-lg transition duration-300 ease-in-out" required>
                <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>Pending</option>
            </select>
        </div>

        <div>
            <label for="artwork" class="text-gray-700 font-medium mb-1 block">Artwork</label>
            <select name="artwork" id="artwork"
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 shadow-md hover:shadow-lg transition duration-300 ease-in-out" required>
                <option value="BGOC" {{ old('artwork') === 'BGOC' ? 'selected' : '' }}>BGOC</option>
                <option value="Client" {{ old('artwork') === 'Client' ? 'selected' : '' }}>Client</option>
            </select>
        </div>

        <div>
            <label for="job_number" class="block text-sm font-medium text-gray-700">Job Number</label>
            <input type="text" name="job_number" id="job_number" value="{{ old('job_number') }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" readonly>
        </div>
    </div>

    {{-- ===== Dynamic Sections ===== --}}
    <div class="mt-6 space-y-6">

        {{-- KLTG Panel --}}
        <div x-show="selectedCategory === 'KLTG'" x-cloak
             class="rounded-xl border border-indigo-200 bg-white/70 p-5 shadow-sm">
            <h4 class="text-sm font-semibold text-indigo-700 mb-4">KLTG Details</h4>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="text-gray-700 font-medium mb-1 block" for="kltg_industry">Industry</label>
                    <input type="text" name="kltg_industry" id="kltg_industry" value="{{ old('kltg_industry') }}"
                           class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="text-gray-700 font-medium mb-1 block" for="kltg_x">X</label>
                    <input type="text" name="kltg_x" id="kltg_x" value="{{ old('kltg_x') }}"
                           class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="text-gray-700 font-medium mb-1 block" for="kltg_edition">Edition</label>
                    <input type="text" name="kltg_edition" id="kltg_edition" value="{{ old('kltg_edition') }}"
                           class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="text-gray-700 font-medium mb-1 block" for="kltg_material_cbp">Material C/BP</label>
                    <input type="text" name="kltg_material_cbp" id="kltg_material_cbp" value="{{ old('kltg_material_cbp') }}"
                           class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="text-gray-700 font-medium mb-1 block" for="kltg_print">Print</label>
                    <input type="text" name="kltg_print" id="kltg_print" value="{{ old('kltg_print') }}"
                           class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="text-gray-700 font-medium mb-1 block" for="kltg_article">Article</label>
                    <input type="text" name="kltg_article" id="kltg_article" value="{{ old('kltg_article') }}"
                           class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="text-gray-700 font-medium mb-1 block" for="kltg_video">Video</label>
                    <input type="text" name="kltg_video" id="kltg_video" value="{{ old('kltg_video') }}"
                           class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="text-gray-700 font-medium mb-1 block" for="kltg_leaderboard">Leaderboard</label>
                    <input type="text" name="kltg_leaderboard" id="kltg_leaderboard" value="{{ old('kltg_leaderboard') }}"
                           class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="text-gray-700 font-medium mb-1 block" for="kltg_qr_code">QR Code</label>
                    <input type="text" name="kltg_qr_code" id="kltg_qr_code" value="{{ old('kltg_qr_code') }}"
                           class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="text-gray-700 font-medium mb-1 block" for="kltg_blog">Blog</label>
                    <input type="text" name="kltg_blog" id="kltg_blog" value="{{ old('kltg_blog') }}"
                           class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="text-gray-700 font-medium mb-1 block" for="kltg_em">EM</label>
                    <input type="text" name="kltg_em" id="kltg_em" value="{{ old('kltg_em') }}"
                           class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="text-gray-700 font-medium mb-1 block" for="barter">Barter</label>
                    <input type="text" name="barter" id="barter" value="{{ old('barter') }}"
                           class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="text-gray-700 font-medium mb-1 block" for="kltg_remarks">Remarks (KLTG)</label>
                    <input type="text" name="kltg_remarks" id="kltg_remarks" value="{{ old('kltg_remarks') }}"
                           class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </div>

            {{-- Outdoor Panel --}}
            <div x-show="selectedCategory === 'Outdoor'" x-cloak
                class="rounded-xl border border-emerald-200 bg-white/70 p-5 shadow-sm">
            <h4 class="text-sm font-semibold text-emerald-700 mb-4">Outdoor Details</h4>


            {{-- BULK locations: satu baris = satu lokasi --}}
            <hr class="my-4">
            <label class="text-gray-700 font-medium mb-1 block">
                Bulk locations (one line = one location)
            </label>
            <textarea name="bulk_placements" rows="6"
                class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-emerald-500"
                placeholder="Format: site | size | council | coords | remarks
            Example:
            Wangsa Maju LRT | 10x20ft | DBKL | 3.154,101.74 | Near station
            BB: Jalan Kuching KM3 | 60x20ft | DBKL | 3.182,101.68 | City inbound
            TB: Setia Alam Exit | 12x24ft | MBSA | 3.090,101.48 | Toll plaza">{{ old('bulk_placements') }}</textarea>
            <p class="text-xs text-gray-500 mt-2">
            • Use <code>|</code> or a comma to separate columns.<br>
            • Optional prefix <code>BB:</code> / <code>TB:</code> before the <em>site</em> will override the sub-product for that line.<br>
            • Without a prefix → it will use the value from your <strong>Product</strong> dropdown (e.g., BB / TB).
            </p>
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
function productPicker() {
  return {
    // ===== Data =====
    categories: {
      'KLTG': [
        { label: 'THE GUIDE',                value: 'THE GUIDE' },
        { label: 'KLTG listing',        value: 'KLTG listing' },
        { label: 'KLTG Quarter Page',   value: 'KLTG Quarter Page' },
      ],
      'Social Media Management': [
        { label: 'TikTok Management',           value: 'TikTok Management' },
        { label: 'YouTube Management',          value: 'YouTube Management' },
        { label: 'FB/IG Management',            value: 'FB/IG Management' },
        { label: 'FB Sponsored Ads',            value: 'FB Sponsored Ads' },  // was "FB IG Ad"
        { label: 'TikTok Management Boost',     value: 'TikTok Management Boost' },
        { label: 'Giveaways/ Contest Management', value: 'Giveaways/ Contest Management' },
        { label: 'Xiaohongshu Management',      value: 'Xiaohongshu Management' },
      ],
      'Outdoor': [
        { label: 'TB - Tempboard',  value: 'TB' },
        { label: 'BB - Billboard',  value: 'BB' },
        { label: 'Newspaper',       value: 'Newspaper' },
        { label: 'Bunting',         value: 'Bunting' },
        { label: 'Flyers',          value: 'Flyers' },
        { label: 'Star',            value: 'Star' },
        { label: 'Signages',        value: 'Signages' },
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
      const jobEl  = document.getElementById('job_number');

      const date = dateEl?.value || '';
      const product = prodEl?.value || this.selectedProduct || '';

      if (!date || !product || !jobEl) return;

      try {
        const url = new URL({!! json_encode(route('serials.preview')) !!}, window.location.origin);
        url.searchParams.set('date', date);
        url.searchParams.set('product', product);

        const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
        if (!res.ok) return; // don’t block the form if backend errs

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
