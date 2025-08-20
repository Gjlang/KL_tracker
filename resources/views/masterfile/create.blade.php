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
                            <input type="text" name="month" id="month" placeholder="e.g., July" class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 shadow-md hover:shadow-lg transition duration-300 ease-in-out" required>
                        </div>

                        <div>
                            <label for="date" class="text-gray-700 font-medium mb-1 block">Date</label>
                            <input type="date" name="date" id="date" class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 shadow-md hover:shadow-lg transition duration-300 ease-in-out" required>
                        </div>

                        <div>
                            <label for="company" class="text-gray-700 font-medium mb-1 block">Company</label>
                            <input type="text" name="company" id="company" placeholder="Company Name" class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 shadow-md hover:shadow-lg transition duration-300 ease-in-out" required>
                        </div>

                        <div>
                            <label for="client" class="text-gray-700 font-medium mb-1 block">Person In Charge</label>
                            <input type="text" name="client" id="client" placeholder="PIC Name" class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 shadow-md hover:shadow-lg transition duration-300 ease-in-out" required>
                        </div>
                    </div>
                </div>

                <!-- Section: Product Info -->
                <div class="bg-white p-6 rounded-xl shadow-lg mt-8">
                    <h3 class="text-xl font-semibold text-indigo-700 mb-4">📦 Product & Traffic Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="product" class="text-gray-700 font-medium mb-1 block">Product</label>
                            <select name="product" id="product" class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 shadow-md hover:shadow-lg transition duration-300 ease-in-out" required>
                                <option value="HM">HM</option>
                                <option value="TB">TB</option>
                                <option value="TTM">TTM</option>
                                <option value="BB">BB</option>
                                <option value="Star">Star</option>
                                <option value="KLTG">KLTG</option>
                                <option value="Flyers">Flyers</option>
                                <option value="Bunting">Bunting</option>
                                <option value="NP">Newspaper</option>
                                <option value="KLTG listing">KLTG listing</option>
                                <option value="KLTG quarter page">KLTG quarter page</option>
                                <option value="Signages">Signages</option>
                                <option value="FB IG Ad">FB IG Ad</option>
                            </select>
                        </div>

                        <div>
                            <label for="traffic" class="text-gray-700 font-medium mb-1 block">Traffic</label>
                            <input type="text" name="traffic" id="traffic" placeholder="Traffic Details" class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 shadow-md hover:shadow-lg transition duration-300 ease-in-out" required>
                        </div>

                        <div>
                            <label for="duration" class="text-gray-700 font-medium mb-1 block">Duration</label>
                            <input type="text" name="duration" id="duration" placeholder="e.g., 3 months" class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 shadow-md hover:shadow-lg transition duration-300 ease-in-out" required>
                        </div>

                        <div>
                            <label for="date_finish" class="text-gray-700 font-medium mb-1 block">Date Finish</label>
                            <input type="date" name="date_finish" id="date_finish" class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 shadow-md hover:shadow-lg transition duration-300 ease-in-out" required>
                        </div>

                        <select name="status" id="status" class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 shadow-md hover:shadow-lg transition duration-300 ease-in-out" required>
                            <option value="pending">Pending</option>
                            <option value="ongoing">Ongoing</option>
                            <option value="completed">Completed</option>
                        </select>

                        <div>
                            <label for="artwork" class="text-gray-700 font-medium mb-1 block">Artwork</label>
                            <select name="artwork" id="artwork" class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 shadow-md hover:shadow-lg transition duration-300 ease-in-out" required>
                                <option value="BGOC">BGOC</option>
                                <option value="Client">Client</option>
                            </select>
                        </div>

                        <div>
                            <label for="job_number" class="block text-sm font-medium text-gray-700">Job Number</label>
                            <input type="text" name="job_number" id="job_number"
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

    <script>
    async function refreshNumbers() {
        const date = document.querySelector('input[name="date"]').value;
        const product = document.querySelector('select[name="product"]').value;

        const url = new URL("{{ route('serials.preview') }}", window.location.origin);
        url.searchParams.set('date', date);
        url.searchParams.set('product', product);

        const res = await fetch(url);
        if (res.ok) {
            const data = await res.json();
            document.getElementById('job_number').value = data.job_number;
        }
    }

    document.addEventListener('DOMContentLoaded', refreshNumbers);
    document.querySelector('input[name="date"]').addEventListener('change', refreshNumbers);
    document.querySelector('select[name="product"]').addEventListener('change', refreshNumbers);
    </script>
</x-app-layout>
