<!-- Import Modal -->
<div id="importModal" class="fixed inset-0 hidden z-50 bg-black bg-opacity-40 flex items-center justify-center">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-lg font-semibold mb-4">Import Master File</h2>

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
