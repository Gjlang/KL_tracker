<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-3xl text-gray-800 leading-tight">
            🚀 Add New Master File
        </h2>
        <p class="mt-1 text-gray-500 text-sm">Fill out the form below to add a new entry to the masterfile database.</p>
    </x-slot>

    <div class="max-w-6xl mx-auto py-12 px-6">
        <div class="bg-gradient-to-br from-white to-gray-50 border border-gray-200 rounded-2xl shadow-xl p-10">
            <form action="{{ route('masterfile.store') }}" method="POST" class="space-y-10">
                @csrf

                <!-- Section: Basic Info -->
                <div>
                    <h3 class="text-xl font-semibold text-indigo-700 mb-4">📋 Basic Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="month" class="text-gray-700 font-medium mb-1 block">Month</label>
                            <input type="text" name="month" id="month" placeholder="e.g., July" class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 shadow-sm" required>
                        </div>

                        <div>
                            <label for="date" class="text-gray-700 font-medium mb-1 block">Date</label>
                            <input type="date" name="date" id="date" class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 shadow-sm" required>
                        </div>

                        <div>
                            <label for="company" class="text-gray-700 font-medium mb-1 block">Company</label>
                            <input type="text" name="company" id="company" placeholder="Company Name" class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 shadow-sm" required>
                        </div>

                        <div>
                            <label for="client" class="text-gray-700 font-medium mb-1 block">Client</label>
                            <input type="text" name="client" id="client" placeholder="Client Name" class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 shadow-sm" required>
                        </div>
                    </div>
                </div>

                <!-- Section: Product Info -->
                <div>
                    <h3 class="text-xl font-semibold text-indigo-700 mb-4">📦 Product & Traffic Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="product" class="text-gray-700 font-medium mb-1 block">Product</label>
                            <select name="product" id="product" class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 shadow-sm" required>
                                <option value="HM">HM</option>
                                <option value="TB">TB</option>
                                <option value="TTM">TTM</option>
                                <option value="BB">BB</option>
                                <option value="Star">Star</option>
                                <option value="KLTG">KLTG</option>
                                <option value="Flyers">Flyers</option>
                                <option value="Bunting">Bunting</option>
                                <option value="KLTG listing">KLTG listing</option>
                                <option value="KLTG quarter page">KLTG quarter page</option>
                                <option value="Signages">Signages</option>
                                <option value="FB IG Ad">FB IG Ad</option>
                            </select>
                        </div>

                        <div>
                            <label for="traffic" class="text-gray-700 font-medium mb-1 block">Traffic</label>
                            <input type="text" name="traffic" id="traffic" placeholder="Traffic Details" class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 shadow-sm" required>
                        </div>

                        <div>
                            <label for="duration" class="text-gray-700 font-medium mb-1 block">Duration</label>
                            <input type="text" name="duration" id="duration" placeholder="e.g., 3 months" class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 shadow-sm" required>
                        </div>

                        <div>
                            <label for="date_finish" class="text-gray-700 font-medium mb-1 block">Date Finish</label>
                            <input type="date" name="date_finish" id="date_finish" class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 shadow-sm" required>
                        </div>

                        <div>
                            <label for="status" class="text-gray-700 font-medium mb-1 block">Status</label>
                            <input type="text" name="status" id="status" placeholder="e.g., Active, Inactive" class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 shadow-sm" required>
                        </div>

                        <div>
                            <label for="artwork" class="text-gray-700 font-medium mb-1 block">Artwork</label>
                            <select name="artwork" id="artwork" class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 shadow-sm" required>
                                <option value="BGOC">BGOC</option>
                                <option value="Client">Client</option>
                            </select>
                        </div>

                        <div>
                            <label for="job_number" class="text-gray-700 font-medium mb-1 block">Job Number (JO)</label>
                            <input type="text" name="job_number" id="job_number" placeholder="Job Number" class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 shadow-sm" required>
                        </div>
                    </div>
                </div>

                <!-- Section: Invoice Info -->
                <div>
                    <h3 class="text-xl font-semibold text-indigo-700 mb-4">💼 Invoice Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="invoice_date" class="text-gray-700 font-medium mb-1 block">Invoice Date</label>
                            <input type="date" name="invoice_date" id="invoice_date" class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 shadow-sm" required>
                        </div>

                        <div>
                            <label for="invoice_number" class="text-gray-700 font-medium mb-1 block">Invoice Number (Inv NO)</label>
                            <input type="text" name="invoice_number" id="invoice_number" placeholder="Invoice Number" class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 shadow-sm" required>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <button type="submit" class="w-full md:w-auto bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-xl font-semibold shadow-md transition duration-300 ease-in-out">
                        ➕ Save Master File
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
