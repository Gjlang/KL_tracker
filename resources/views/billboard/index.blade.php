@extends('layouts.app')

@section('title')
    Billboard Stock Inventory Master
@endsection

@section('content')

    <head>
        <!-- ... other head content ... -->
        <title>@yield('title', 'Default Title')</title> <!-- Example -->

        <style>
            /* Force pagination container into horizontal row */
            #billboard_table_paginate {
                display: flex !important;
                justify-content: center;
                /* center horizontally */
                align-items: center;
                gap: 0.5rem;
                /* spacing between buttons */
            }

            /* Make each button horizontal-friendly */
            #billboard_table_paginate .paginate_button {
                display: inline-flex !important;
                align-items: center;
                justify-content: center;
            }

            /* Optional: style current/active page */
            #billboard_table_paginate .paginate_button.current {
                background-color: #e5e7eb;
                /* Tailwind neutral-200 */
                border-radius: 0.375rem;
                font-weight: 600;
            }

            .modal {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 9999;
                overflow: auto;
            }

            .modal.show {
                display: block;
            }

            .modal__content {
                background: white;
                margin: 5% auto;
                padding: 0;
                border-radius: 8px;
                max-width: 600px;
                width: 90%;
            }
        </style>



        <!-- Include other necessary scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js']) <!-- If using Vite -->
        <!-- ... rest of head content ... -->
    </head>
    <div class="intro-y flex flex-col sm:flex-row items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Billboard Master
        </h2>
    </div>
    <div class="intro-y box p-5 mt-5">
        <div class="mb-5 p-5 rounded-md" style="background-color:#ECF9FD;">
            <h2 class="text-lg font-medium">
                Billboard Master
            </h2>
            <p class="w-12 flex-none xl:w-auto xl:flex-initial ml-2">
                <i class="font-bold">Billboard Master</i> - Lorem ipsum.
            </p>
        </div>
        <!-- BEGIN: Billboard Filter-->
        <div class="flex flex-col sm:flex-row sm:items-end xl:items-start gap-4 mb-4">
            <form class="flex-1 flex flex-col sm:flex-row sm:flex-wrap gap-4">
                <!-- Status Filter -->
                <div class="flex flex-col">
                    <label for="filterBillboardStatus" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select
                        class="w-full sm:w-40 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                        id="filterBillboardStatus">
                        <option value="all">All</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>

                @if (Auth::guard('web')->check() &&
                        Auth::guard('web')->user()->hasRole(['superadmin', 'admin']))
                    <!-- Row 1: State & Area -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="flex flex-col">
                            <label for="filterBillboardState"
                                class="block text-sm font-medium text-gray-700 mb-1">State</label>
                            <select
                                class="w-full sm:w-40 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                id="filterBillboardState">
                                <option value="all">All</option>
                                @foreach ($states as $state)
                                    <option value="{{ $state->id }}">{{ $state->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex flex-col">
                            <label for="filterBillboardDistrict"
                                class="block text-sm font-medium text-gray-700 mb-1">Area</label>
                            <select
                                class="w-full sm:w-40 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                id="filterBillboardDistrict">
                                <option value="all">All</option>
                                @foreach ($districts as $district)
                                    <option value="{{ $district->id }}">{{ $district->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Row 2: Type, New/Existing, Size -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="flex flex-col">
                            <label for="filterBillboardType"
                                class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                            <select
                                class="w-full sm:w-40 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                id="filterBillboardType">
                                <option value="all">All</option>
                                @foreach ($billboardTypes as $type)
                                    <option value="{{ $type }}">{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex flex-col">
                            <label for="filterBillboardSiteType"
                                class="block text-sm font-medium text-gray-700 mb-1">New/Existing</label>
                            <select
                                class="w-full sm:w-40 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                id="filterBillboardSiteType">
                                <option value="all">All</option>
                                <option value="new">New</option>
                                <option value="existing">Existing</option>
                                <option value="rejected">Rejected</option>
                                <option value="existing_1">Existing 1</option>
                                <option value="existing_2">Existing 2</option>
                                <option value="existing_3">Existing 3</option>
                            </select>
                        </div>
                        <div class="flex flex-col">
                            <label for="filterBillboardSize"
                                class="block text-sm font-medium text-gray-700 mb-1">Size</label>
                            <select
                                class="w-full sm:w-40 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                id="filterBillboardSize">
                                <option value="all">All</option>
                                @foreach ($billboardSize as $size)
                                    <option value="{{ $size }}">{{ $size }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif
            </form>

            <!-- Buttons Section -->
            <div class="flex flex-col gap-2">
                <!-- Add New Stock Button -->
                <a href="javascript:;" data-toggle="modal" data-target="#addBillboardModal"
                    class="flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add New Stock
                </a>

                <!-- Inventory Button -->
                <a href="{{ route('stockInventory.index') }}"
                    class="flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Inventory
                </a>

                <!-- Download Details [CLIENT] Button -->
                <a href="#" id="exportBtnClient"
                    class="flex items-center justify-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition"
                    target="_blank">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Download [CLIENT]
                </a>

                <!-- Download Details [INTERNAL] Button -->
                <a href="#" id="exportBtn"
                    class="flex items-center justify-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition"
                    target="_blank">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Download [INTERNAL]
                </a>
            </div>
        </div>
        <!-- END: Billboard Filter -->
        <!-- BEGIN: Billboard List -->
        <div class="overflow-x-auto">
            <table id="billboard_table" class="min-w-full border-collapse border border-neutral-300">
                <!-- Add border classes to the table -->
                <thead class="bg-neutral-50 sticky top-0 z-10">
                    <tr class="border-b border-neutral-300"> <!-- Add border to header row -->
                        <th class="px-4 py-4 table-header border-r border-neutral-300"><input type="checkbox"
                                id="select-all-billboards"></th> <!-- Add border to header cell -->
                        <th class="px-4 py-4 table-header border-r border-neutral-300">No</th>
                        <!-- Add border to header cell -->
                        <th class="px-4 py-4 table-header min-w-[180px] w-[180px] border-r border-neutral-300">Site #</th>
                        <!-- Add border to header cell -->
                        <th class="px-4 py-4 table-header border-r border-neutral-300">New/Existing</th>
                        <!-- Add border to header cell -->
                        <th class="px-4 py-4 table-header min-w-[100px] w-[100px] border-r border-neutral-300">Type</th>
                        <!-- Add border to header cell -->
                        <th class="px-4 py-4 table-header min-w-[100px] w-[100px] border-r border-neutral-300">Size</th>
                        <!-- Add border to header cell -->
                        <th class="px-4 py-4 table-header min-w-[100px] border-r border-neutral-300">Lighting</th>
                        <!-- Add border to header cell -->
                        <th class="px-4 py-4 table-header min-w-[350px] border-r border-neutral-300">Location</th>
                        <!-- Add border to header cell -->
                        <th class="px-4 py-4 table-header min-w-[200px] border-r border-neutral-300">Area</th>
                        <!-- Add border to header cell -->
                        <th class="px-4 py-4 table-header min-w-[160px] hidden border-r border-neutral-300">GPS Coordinate
                        </th> <!-- Add border to header cell -->
                        <th
                            class="px-4 py-4 table-header min-w-[200px] dt-exclude-export dt-no-sort border-r border-neutral-300">
                            Show Detail</th> <!-- Add border to header cell -->
                        <th class="px-4 py-4 table-header min-w-[100px] dt-exclude-export dt-no-sort">Actions</th>
                        <!-- Last header cell, no right border if you don't want it -->
                    </tr>
                </thead>
                <tbody id="billboard_tbody" class="bg-white divide-y divide-neutral-200">
                    <!-- 'divide-y' adds borders between rows -->
                    <!-- DataTables will populate this body with <tr> elements -->
                    <!-- Each <tr> generated by DataTables should ideally have borders applied -->
                    <!-- You can modify the DataTables column definitions to add borders to cells if needed -->
                </tbody>
            </table>
        </div>
        <!-- END: Billboard List -->
    </div>

    <!-- Modal content -->
    <!-- Create Billboard Modal -->
    <div class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden"
        id="addBillboardModal">
        <div class="modal__content bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Add New Stock</h2>
                <button type="button" data-dismiss="modal" class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="addBillboardForm">
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Outdoor Type -->
                    <div class="md:col-span-1">
                        <label for="inputBillboardType" class="block text-sm font-medium text-gray-700 mb-1">Outdoor Type
                            <span class="text-red-500">*</span></label>
                        <select
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            id="inputBillboardType" required>
                            <option value="">-- Select Outdoor Type --</option>
                            <option value="BB">Billboard</option>
                            <option value="TB">Tempboard</option>
                            <option value="BU">Bunting</option>
                            <option value="BN">Banner</option>
                        </select>
                    </div>

                    <!-- Size -->
                    <div class="md:col-span-1">
                        <label for="inputBillboardSize" class="block text-sm font-medium text-gray-700 mb-1">Size
                            (H)'x(W)' <span class="text-red-500">*</span></label>
                        <select
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            id="inputBillboardSize" required>
                            <option value="">-- Select Size --</option>
                            <option value="10x10">10x10</option>
                            <option value="15x10">15x10</option>
                            <option value="20x15">20x15</option>
                            <option value="30x20">30x20</option>
                            <option value="10x40">10x40</option>
                            <option value="6x3">6x3</option>
                            <option value="7x3">7x3</option>
                            <option value="8x3">8x3</option>
                        </select>
                    </div>

                    <!-- Lighting -->
                    <div class="md:col-span-1">
                        <label for="inputBillboardLighting" class="block text-sm font-medium text-gray-700 mb-1">Lighting
                            <span class="text-red-500">*</span></label>
                        <select
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            id="inputBillboardLighting" required>
                            <option value="">-- Select Lighting --</option>
                            <option value="None">None</option>
                            <option value="TNB">TNB</option>
                            <option value="SOLAR">SOLAR</option>
                        </select>
                    </div>

                    <!-- Separator -->
                    <div class="md:col-span-2 py-2">
                        <hr class="border-t border-gray-300">
                    </div>

                    <!-- State -->
                    <div class="md:col-span-1">
                        <label for="inputBillboardState" class="block text-sm font-medium text-gray-700 mb-1">State <span
                                class="text-red-500">*</span></label>
                        <select
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            id="inputBillboardState" required>
                            <option value="">-- Select State --</option>
                            @foreach ($states as $state)
                                <option value="{{ $state->id }}">{{ $state->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Area -->
                    <div class="md:col-span-1">
                        <label for="inputBillboardDistrict" class="block text-sm font-medium text-gray-700 mb-1">
                            Area <span class="text-red-500">*</span>
                        </label>
                        <select id="inputBillboardDistrict"
                            class="select2 w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            required>
                            <option value="">-- Select Area --</option>
                        </select>
                    </div>

                    <!-- Council -->
                    <div class="md:col-span-1">
                        <label for="inputBillboardCouncil" class="block text-sm font-medium text-gray-700 mb-1">Council
                            <span class="text-red-500">*</span></label>
                        <select
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            id="inputBillboardCouncil" required>
                            <option value="">-- Select Council --</option>
                        </select>
                    </div>

                    <!-- Location -->
                    <div class="md:col-span-1">
                        <label for="inputBillboardLocation" class="block text-sm font-medium text-gray-700 mb-1">Location
                            <span class="text-red-500">*</span></label>
                        <input type="text"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            id="inputBillboardLocation" placeholder="Enter location name" required>
                    </div>

                    <!-- Land Type -->
                    <div class="md:col-span-1">
                        <label for="inputBillboardLand" class="block text-sm font-medium text-gray-700 mb-1">State/Private
                            Land <span class="text-red-500">*</span></label>
                        <select
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            id="inputBillboardLand" required>
                            <option value="">-- Select option --</option>
                            <option value="A">A - State Land</option>
                            <option value="B">B - Private Land</option>
                            <option value="C">C - KKR</option>
                            <option value="D">D - Others</option>
                        </select>
                    </div>

                    <!-- Separator -->
                    <div class="md:col-span-2 py-2">
                        <hr class="border-t border-gray-300">
                    </div>

                    <!-- GPS Coordinate -->
                    <div class="md:col-span-2">
                        <label for="inputGPSCoordinate" class="block text-sm font-medium text-gray-700 mb-1">GPS
                            Coordinate <span class="text-red-500">*</span></label>
                        <input type="text"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            id="inputGPSCoordinate" name="gps_coordinate"
                            pattern="^-?([0-8]?\d(\.\d+)?|90(\.0+)?),\s*-?(1[0-7]\d(\.\d+)?|180(\.0+)?)$"
                            placeholder="e.g. 3.1390, 101.6869" required>
                        <small class="text-gray-500 mt-1 block">Format: latitude (-90 → 90), longitude (-180 → 180)</small>
                    </div>

                    <!-- Maps URL -->
                    <div class="md:col-span-2">
                        <label for="inputMapsUrl" class="block text-sm font-medium text-gray-700 mb-1">Google Maps
                            Link</label>
                        <input type="url"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            id="inputMapsUrl" name="gps_url"
                            placeholder="Paste Google Maps share link (e.g. https://maps.app.goo.gl/xyz123)"
                            pattern="https:\/\/maps\.app\.goo\.gl\/[A-Za-z0-9]+">
                        <small class="text-gray-500 mt-1 block">Paste the short link from Google Maps → Share → Copy
                            Link</small>
                    </div>

                    <!-- Separator -->
                    <div class="md:col-span-2 py-2">
                        <hr class="border-t border-gray-300">
                    </div>

                    <!-- Traffic Volume -->
                    <div class="md:col-span-1">
                        <label for="inputBillboardTrafficVolume"
                            class="block text-sm font-medium text-gray-700 mb-1">Traffic Volume</label>
                        <input type="number"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            id="inputBillboardTrafficVolume" name="traffic_volume" step="1"
                            placeholder="e.g. 50000">
                    </div>

                    <!-- Site Type -->
                    <div class="md:col-span-1">
                        <label for="inputBillboardSiteType" class="block text-sm font-medium text-gray-700 mb-1">Site
                            Type</label>
                        <select
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            id="inputBillboardSiteType">
                            <option value="">-- Select option --</option>
                            <option value="new">New</option>
                            <option value="existing">Existing</option>
                            <option value="rejected">Rejected</option>
                            <option value="existing_1">Existing 1</option>
                            <option value="existing_2">Existing 2</option>
                            <option value="existing_3">Existing 3</option>
                        </select>
                    </div>
                </div>

                <div
                    class="flex items-center justify-end px-6 py-4 space-x-3 border-t border-gray-200 bg-gray-50 rounded-b-lg">
                    <button type="button" data-dismiss="modal"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                        Cancel
                    </button>
                    <button type="submit" id="billboardAddButton"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!-- Create Modal End -->

    <!-- Edit Billboard Modal Start -->
    <div class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden"
        id="billboardEditModal">
        <div class="modal__content bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Edit Stock</h2>
                <button type="button" data-dismiss="modal" class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="editBillboardForm">
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Hidden ID -->
                    <input type="hidden" id="editBillboardModalId" name="id">

                    <!-- Outdoor Type (Disabled) -->
                    <div class="md:col-span-1">
                        <label for="editBillboardType" class="block text-sm font-medium text-gray-700 mb-1">Outdoor Type
                            <span class="text-red-500">*</span></label>
                        <select
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition bg-gray-100 cursor-not-allowed"
                            id="editBillboardType" disabled>
                            <option value="">-- Select Outdoor Type --</option>
                            <option value="BB">Billboard</option>
                            <option value="TB">Tempboard</option>
                            <option value="BU">Bunting</option>
                            <option value="BN">Banner</option>
                        </select>
                    </div>

                    <!-- Size -->
                    <div class="md:col-span-1">
                        <label for="editBillboardSize" class="block text-sm font-medium text-gray-700 mb-1">Billboard Size
                            <span class="text-red-500">*</span></label>
                        <select
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            id="editBillboardSize" required>
                            <option value="">-- Select Size --</option>
                            <option value="10x10">10x10</option>
                            <option value="15x10">15x10</option>
                            <option value="20x15">20x15</option>
                            <option value="30x20">30x20</option>
                            <option value="10x40">10x40</option>
                            <option value="6x3">6x3</option>
                            <option value="7x3">7x3</option>
                            <option value="8x3">8x3</option>
                        </select>
                    </div>

                    <!-- Lighting -->
                    <div class="md:col-span-1">
                        <label for="editBillboardLighting" class="block text-sm font-medium text-gray-700 mb-1">Lighting
                            <span class="text-red-500">*</span></label>
                        <select
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            id="editBillboardLighting" required>
                            <option value="">-- Select Lighting --</option>
                            <option value="None">None</option>
                            <option value="TNB">TNB</option>
                            <option value="SOLAR">SOLAR</option>
                        </select>
                    </div>

                    <!-- Separator -->
                    <div class="md:col-span-2 py-2">
                        <hr class="border-t border-gray-300">
                    </div>

                    <!-- State (Disabled) -->
                    <div class="md:col-span-1">
                        <label for="editBillboardState" class="block text-sm font-medium text-gray-700 mb-1">State <span
                                class="text-red-500">*</span></label>
                        <select
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition bg-gray-100 cursor-not-allowed"
                            id="editBillboardState" disabled>
                            <option value="">-- Select State --</option>
                            @foreach ($states as $state)
                                <option value="{{ $state->id }}">{{ $state->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Area -->
                    <div class="md:col-span-1">
                        <label for="editBillboardDistrict" class="block text-sm font-medium text-gray-700 mb-1">Area <span
                                class="text-red-500">*</span></label>
                        <select
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            id="editBillboardDistrict" required>
                            <option value="">-- Select Area --</option>
                        </select>
                    </div>

                    <!-- Council (Disabled) -->
                    <div class="md:col-span-1">
                        <label for="editBillboardCouncil" class="block text-sm font-medium text-gray-700 mb-1">Council
                            <span class="text-red-500">*</span></label>
                        <select
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition bg-gray-100 cursor-not-allowed"
                            id="editBillboardCouncil" disabled>
                            <option value="">-- Select Council --</option>
                        </select>
                    </div>

                    <!-- Location -->
                    <div class="md:col-span-1">
                        <label for="editBillboardLocation" class="block text-sm font-medium text-gray-700 mb-1">Location
                            <span class="text-red-500">*</span></label>
                        <input type="text"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            id="editBillboardLocation" placeholder="Enter location name">
                    </div>

                    <!-- Separator -->
                    <div class="md:col-span-2 py-2">
                        <hr class="border-t border-gray-300">
                    </div>

                    <!-- GPS Coordinate -->
                    <div class="md:col-span-2">
                        <label for="editGPSCoordinate" class="block text-sm font-medium text-gray-700 mb-1">GPS Coordinate
                            <span class="text-red-500">*</span></label>
                        <input type="text"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            id="editGPSCoordinate" name="gps_coordinate" placeholder="e.g. 3.1390, 101.6869" required>
                        <small class="text-gray-500 mt-1 block">Format: latitude, longitude</small>
                    </div>

                    <!-- Maps URL -->
                    <div class="md:col-span-2">
                        <label for="editGPSUrl" class="block text-sm font-medium text-gray-700 mb-1">GPS URL (Google
                            Maps)</label>
                        <input type="url"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            id="editGPSUrl" name="gps_url" placeholder="https://maps.app.goo.gl/xyz123">
                        <small class="text-gray-500 mt-1 block">Example: https://maps.app.goo.gl/xxxxx</small>
                    </div>

                    <!-- Separator -->
                    <div class="md:col-span-2 py-2">
                        <hr class="border-t border-gray-300">
                    </div>

                    <!-- Traffic Volume -->
                    <div class="md:col-span-1">
                        <label for="editBillboardTrafficVolume"
                            class="block text-sm font-medium text-gray-700 mb-1">Traffic Volume</label>
                        <input type="text"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            id="editBillboardTrafficVolume" name="traffic_volume" placeholder="e.g. 50000">
                    </div>

                    <!-- Site Type -->
                    <div class="md:col-span-1">
                        <label for="editBillboardSiteType" class="block text-sm font-medium text-gray-700 mb-1">Site
                            Type</label>
                        <select
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            id="editBillboardSiteType">
                            <option value="">-- Select option --</option>
                            <option value="new">New</option>
                            <option value="existing">Existing</option>
                            <option value="rejected">Rejected</option>
                            <option value="existing_1">Existing 1</option>
                            <option value="existing_2">Existing 2</option>
                            <option value="existing_3">Existing 3</option>
                        </select>
                    </div>

                    <!-- Status -->
                    <div class="md:col-span-1">
                        <label for="editBillboardStatus"
                            class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            id="editBillboardStatus">
                            <option value="">-- Select option --</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>

                <div
                    class="flex items-center justify-end px-6 py-4 space-x-3 border-t border-gray-200 bg-gray-50 rounded-b-lg">
                    <button type="button" data-dismiss="modal"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                        Cancel
                    </button>
                    <button type="submit" id="billboardEditButton"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!-- Edit Billboard Modal End -->
    <!-- BEGIN: Billboard Delete Modal -->
    <div class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden"
        id="billboardDeleteModal">
        <!-- Note: Using max-w-md for a smaller, more appropriate size for a confirmation modal -->
        <div class="modal__content bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
            <div class="p-6">
                <!-- Icon and Message -->
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100">
                        <!-- Using the same warning icon style as before, but with consistent Tailwind classes -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-red-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mt-4">Are you sure?</h3>
                    <div class="mt-2 text-gray-500">
                        <p>Confirm delete this billboard info?</p>
                        <p class="text-sm mt-1">This process cannot be undone.</p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-6 flex justify-center space-x-3">
                    <button type="button" data-dismiss="modal"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                        Cancel
                    </button>
                    <!-- The delete button now has an ID and calls the billboardDeleteButton function -->
                    <button type="button" id="billboardDeleteButton"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- END: Billboard Delete Modal -->

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Add jQuery CDN -->


    <!-- Include DataTables JS (if not already included elsewhere) -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script> <!-- If using Bootstrap theme -->

    <!-- Scripts -->
    <script>
        // Global array
        let selectedBillboards = [];
        // Row checkbox clicked
        $('#billboard_table').on('change', 'input.billboard-checkbox', function() {
            let id = $(this).val().toString(); // always string
            if ($(this).is(':checked')) {
                if (!selectedBillboards.includes(id)) selectedBillboards.push(id);
            } else {
                selectedBillboards = selectedBillboards.filter(i => i != id);
            }
        });
        // Select all checkbox
        $('#select-all-billboards').on('click', function() {
            let isChecked = $(this).is(':checked');
            $('#billboard_table tbody input.billboard-checkbox').each(function() {
                let id = $(this).val().toString();
                $(this).prop('checked', isChecked);
                if (isChecked) {
                    if (!selectedBillboards.includes(id)) selectedBillboards.push(id);
                } else {
                    selectedBillboards = selectedBillboards.filter(i => i != id);
                }
            });
        });
        // Restore on redraw
        $('#billboard_table').on('draw.dt', function() {
            $('#billboard_table tbody input.billboard-checkbox').each(function() {
                let id = $(this).val().toString();
                $(this).prop('checked', selectedBillboards.includes(id));
            });
            // Update "select all"
            let allChecked = $('#billboard_table tbody input.billboard-checkbox').length ===
                $('#billboard_table tbody input.billboard-checkbox:checked').length;
            $('#select-all-billboards').prop('checked', allChecked);
        });
        $('#filterBillboardState').on('change', function() {
            let stateId = $(this).val();
            $('#filterBillboardDistrict').empty().append('<option value="all">All</option>');
            if (stateId === 'all') {
                $.ajax({
                    url: '{{ route('location.getAllDistricts') }}',
                    type: 'GET',
                    success: function(districts) {
                        districts.forEach(function(district) {
                            $('#filterBillboardDistrict').append(
                                `<option value="${district.id}">${district.name}</option>`);
                        });
                    },
                    error: function() {
                        alert('Failed to load all districts.');
                    }
                });
            } else {
                $.ajax({
                    url: '{{ route('location.getDistricts') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        state_id: stateId
                    },
                    success: function(districts) {
                        districts.forEach(function(district) {
                            $('#filterBillboardDistrict').append(
                                `<option value="${district.id}">${district.name}</option>`);
                        });
                    },
                    error: function() {
                        alert('Failed to load districts.');
                    }
                });
            }
        });
        document.getElementById('exportBtn').addEventListener('click', function(e) {
            e.preventDefault();
            // Read filters
            let state = document.getElementById('filterBillboardState')?.value || 'all';
            let district = document.getElementById('filterBillboardDistrict')?.value || 'all';
            let type = document.getElementById('filterBillboardType')?.value || 'all';
            let site_type = document.getElementById('filterBillboardSiteType')?.value || 'all';
            let size = document.getElementById('filterBillboardSize')?.value || 'all';
            let status = document.getElementById('filterBillboardStatus')?.value || 'all';
            // Build params
            let params = {
                state_id: state,
                district_id: district,
                type: type,
                site_type: site_type,
                size: size,
                status: status
            };
            // ✅ Add selectedBillboards if any
            if (selectedBillboards.length > 0) {
                params.billboard_ids = selectedBillboards.join(',');
            }
            // Build query string
            let query = new URLSearchParams(params).toString();
            // Redirect with query string
            let exportUrl = '{{ route('billboards.export.pdf') }}' + '?' + query;
            window.open(exportUrl, '_blank');
            // ✅ After opening, clear selections
            selectedBillboards = []; // reset array
            document.querySelectorAll('.billboard-checkbox').forEach(cb => cb.checked = false);
            let selectAll = document.getElementById('select-all-billboards');
            if (selectAll) {
                selectAll.checked = false;
            }
        });
        document.getElementById('exportBtnClient').addEventListener('click', function(e) {
            e.preventDefault();
            // Read filters
            let state = document.getElementById('filterBillboardState')?.value || 'all';
            let district = document.getElementById('filterBillboardDistrict')?.value || 'all';
            let type = document.getElementById('filterBillboardType')?.value || 'all';
            let site_type = document.getElementById('filterBillboardSiteType')?.value || 'all';
            let size = document.getElementById('filterBillboardSize')?.value || 'all';
            let status = document.getElementById('filterBillboardStatus')?.value || 'all';
            // Build params
            let params = {
                state_id: state,
                district_id: district,
                type: type,
                site_type: site_type,
                size: size,
                status: status
            };
            // ✅ Add selectedBillboards if any
            if (selectedBillboards.length > 0) {
                params.billboard_ids = selectedBillboards.join(',');
            }
            // Build query string
            let query = new URLSearchParams(params).toString();
            // Redirect with query string
            let exportUrl = '{{ route('billboards.export.pdf.client') }}' + '?' + query; // Fixed route
            window.open(exportUrl, '_blank');
            // ✅ After opening, clear selections
            selectedBillboards = []; // reset array
            document.querySelectorAll('.billboard-checkbox').forEach(cb => cb.checked = false);
            let selectAll = document.getElementById('select-all-billboards');
            if (selectAll) {
                selectAll.checked = false;
            }
        });
        // Function to reload the DataTable when any filter changes
        function setupAutoFilter() {
            const tableElement = $('#billboard_table');
            if (!$.fn.DataTable.isDataTable(tableElement)) {
                console.warn("DataTable is not yet initialized.");
                return;
            }
            const table = tableElement.DataTable();
            $('#filterBillboardStatus, #filterBillboardState, #filterBillboardDistrict, #filterBillboardType, #filterBillboardSiteType, #filterBillboardSize')
                .on('change', function() {
                    table.ajax.reload();
                });
        }

        $(document).ready(function() {
            // Global variables
            var filterBillboardStatus;
            document.getElementById("billboardDeleteButton").addEventListener("click", billboardDeleteButton);





            // When "State" is changed in add form
            $('#inputBillboardState').on('change', function() {
                let stateId = $(this).val();
                let $districtSelect = $('#inputBillboardDistrict');
                let $councilSelect = $('#inputBillboardCouncil');

                // Reset dropdowns (Clear underlying select)
                $districtSelect.empty().append('<option value="">-- Select Area --</option>');
                $councilSelect.empty().append('<option value="">-- Select Council --</option>');

                // Clear Select2 selection for Area
                $districtSelect.val(null).trigger('change'); // Clears the Select2 UI

                if (stateId !== '') {
                    $.ajax({
                        url: '{{ route('location.getDistricts') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            state_id: stateId
                        },
                        success: function(districts) {
                            // Clear and refill dropdown
                            $districtSelect.empty().append(
                                '<option value="">-- Select Area --</option>');
                            districts.forEach(function(district) {
                                $districtSelect.append(
                                    `<option value="${district.id}">${district.name}</option>`
                                );
                            });

                            // Destroy and reinitialize Select2 once only
                            $districtSelect.select2('destroy').select2({
                                tags: true, // <-- enables user input
                                placeholder: "-- Select or Add Area --",
                                allowClear: true,
                                width: '100%',
                                dropdownParent: $districtSelect.parent()
                            });
                        },
                        error: function() {
                            alert('Failed to load districts.');
                            // Ensure Select2 is still functional even after an error
                            // Re-initialize with just the default option if it was destroyed
                            if ($districtSelect.data('select2')) {
                                $districtSelect.select2('destroy');
                            }
                            $districtSelect.select2('destroy').select2({
                                tags: true,
                                placeholder: "-- Select or Add Area --",
                                allowClear: true,
                                width: '100%'
                            });
                        }
                    });
                } else {
                    // If no state is selected, ensure Select2 is re-initialized with just the default option
                    if ($districtSelect.data('select2')) {
                        $districtSelect.select2('destroy');
                    }
                    $districtSelect.select2({
                        tags: true,
                        placeholder: "-- Select or Add Area --",
                        allowClear: true,
                        width: '100%'
                    });
                }
            });

            // When district changes → load councils
            $('#inputBillboardDistrict').on('change', function() {
                let stateId = $('#inputBillboardState').val();
                $('#inputBillboardCouncil').empty().append(
                    '<option value="">-- Select Council --</option>');
                if (stateId !== '') {
                    $.ajax({
                        url: '{{ route('location.getCouncils') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            state_id: stateId
                        },
                        success: function(councils) {
                            councils.forEach(function(council) {
                                $('#inputBillboardCouncil').append(
                                    `<option value="${council.id}">${council.abbreviation} - ${council.name}</option>`
                                );
                            });
                        },
                        error: function() {
                            alert('Failed to load councils.');
                        }
                    });
                }
            });

            const $districtSelect = $('#inputBillboardDistrict');

            // Initialize Select2
            $districtSelect.select2({
                tags: true, // <-- allows custom text input
                placeholder: "-- Select or Add Area --",
                allowClear: true,
                width: '100%',
                dropdownParent: $districtSelect.parent()
            });

            // Open "Add New Stock" modal
            $('a[data-toggle="modal"][data-target="#addBillboardModal"]').on('click', function(e) {
                e.preventDefault();
                openAltEditorModal("#addBillboardModal");
            });

            $('#billboardDeleteModal [data-dismiss="modal"]').on('click', function() {
                closeAltEditorModal("#billboardDeleteModal");
            });

            $(document).on('click', 'a[data-toggle="modal"][data-target="#billboardDeleteModal"]', function(e) {
                e.preventDefault();
                // Store the ID of the clicked delete link (e.g., "delete-billboard-123")
                // This is crucial for billboardDeleteButton() to know which ID to delete
                lastClickedLink = $(this).attr('id');
                openAltEditorModal("#billboardDeleteModal");
            });

            // Close any modal with [data-dismiss="modal"]
            $(document).on('click', '[data-dismiss="modal"]', function() {
                // Find the closest modal and close it
                const modal = $(this).closest('.modal')[0];
                if (modal) {
                    closeAltEditorModal('#' + modal.id);
                }
            });

            document.getElementById("billboardAddButton").addEventListener("click", function(e) {
                e.preventDefault();
                billboardAddButton();
            });

            // Open modal
            function openAltEditorModal(selector) {
                const modal = document.querySelector(selector);
                if (modal) {
                    modal.classList.add('show');
                    // Prevent body scroll when modal is open
                    document.body.style.overflow = 'hidden';
                }
            }

            function closeAltEditorModal(selector) {
                const modal = document.querySelector(selector);
                if (modal) {
                    modal.classList.remove('show');
                    // Re-enable body scroll
                    document.body.style.overflow = '';
                }
            }
            // Setup the on-going Billboard datatable
            function initBillboardDatatable() {
                const dt = new Date();
                const formattedDate =
                    `${dt.getFullYear()}${(dt.getMonth() + 1).toString().padStart(2, '0')}${dt.getDate().toString().padStart(2, '0')}`;
                const formattedTime = `${dt.getHours()}:${dt.getMinutes()}:${dt.getSeconds()}`;
                const $fileName = `Billboard_List_${formattedDate}_${formattedTime}`;
                const table = $('#billboard_table').DataTable({
                    destroy: true,
                    debug: true,
                    processing: true,
                    searching: true,
                    serverSide: true,
                    ordering: true,
                    order: [
                        [7,
                            'desc'
                        ] // Assuming 'region' (which uses location_name) is the 8th column (0-indexed)
                    ],
                    pagingType: 'simple_numbers',
                    pageLength: 25,
                    aLengthMenu: [
                        [25, 50, 75, -1],
                        [25, 50, 75, "All"]
                    ],
                    iDisplayLength: 25,
                    ajax: {
                        url: "{{ route('billboard.list') }}",
                        dataType: "json",
                        type: "POST",
                        method: "POST",
                        data: function(d) {
                            d._token = $('meta[name="csrf-token"]').attr('content');
                            d.status = $('#filterBillboardStatus').val();
                            d.state = $('#filterBillboardState').val();
                            d.district = $('#filterBillboardDistrict').val();
                            d.type = $('#filterBillboardType').val();
                            d.site_type = $('#filterBillboardSiteType').val();
                            d.size = $('#filterBillboardSize').val();
                        },
                        dataSrc: function(json) {
                            json.recordsTotal = json.recordsTotal;
                            json.recordsFiltered = json.recordsFiltered;
                            return json.data;
                        }
                    },
                    dom: "lBfrtip", // Keep the same dom structure
                    buttons: [{
                        text: "Export Excel",
                        className: "btn-secondary h-11",
                        action: function() {
                            let form = $('<form>', {
                                method: 'POST',
                                action: "{{ route('billboards.export') }}"
                            });
                            // Add filters as hidden inputs
                            form.append($('<input>', {
                                type: 'hidden',
                                name: '_token',
                                value: $('meta[name="csrf-token"]').attr('content')
                            }));
                            // Add selected IDs if any
                            if (selectedBillboards.length > 0) {
                                form.append($('<input>', {
                                    type: 'hidden',
                                    name: 'billboard_ids',
                                    value: selectedBillboards.join(',')
                                }));
                            }
                            form.append($('<input>', {
                                type: 'hidden',
                                name: 'status',
                                value: $('#filterBillboardStatus').val()
                            }));
                            form.append($('<input>', {
                                type: 'hidden',
                                name: 'state',
                                value: $('#filterBillboardState').val()
                            }));
                            form.append($('<input>', {
                                type: 'hidden',
                                name: 'district',
                                value: $('#filterBillboardDistrict').val()
                            }));
                            form.append($('<input>', {
                                type: 'hidden',
                                name: 'type',
                                value: $('#filterBillboardType').val()
                            }));
                            form.append($('<input>', {
                                type: 'hidden',
                                name: 'site_type',
                                value: $('#filterBillboardSiteType').val()
                            }));
                            form.append($('<input>', {
                                type: 'hidden',
                                name: 'size',
                                value: $('#filterBillboardSize').val()
                            }));
                            form.appendTo('body').submit().remove();
                            // ✅ After opening, clear selections
                            selectedBillboards = []; // reset array
                            document.querySelectorAll('.billboard-checkbox').forEach(cb => cb
                                .checked = false);
                            let selectAll = document.getElementById('select-all-billboards');
                            if (selectAll) {
                                selectAll.checked = false;
                            }
                        }
                    }],
                    columnDefs: [{
                        targets: 'dt-no-sort',
                        orderable: false
                    }],
                    columns: [{
                            data: "id",
                            orderable: false,
                            searchable: false,
                            render: function(data) {
                                let checked = selectedBillboards.includes(data) ? 'checked' : '';
                                return `<input type="checkbox" class="billboard-checkbox" value="${data}" ${checked}>`;
                            }
                        },
                        {
                            data: null, // <-- important
                            name: 'no',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row, meta) {
                                return meta.row + meta.settings._iDisplayStart + 1;
                            }
                        },
                        {
                            data: "site_number",
                        },
                        {
                            data: "site_type",
                        },
                        {
                            data: "type",
                        },
                        {
                            data: "size",
                        },
                        {
                            data: "lighting",
                        },
                        {
                            data: "location_name", // Column index 7 (0-indexed)
                        },
                        {
                            data: "region", // Column index 8 (0-indexed)
                        },
                        {
                            data: "gps_latitude", // point to a valid field
                            name: "gps_coordinate",
                            visible: false, // keep hidden in UI
                            render: function(data, type, row) {
                                let lat = row.gps_latitude ? row.gps_latitude : "";
                                let lng = row.gps_longitude ? row.gps_longitude : "";
                                return (lat && lng) ? `${lat}, ${lng}` : "";
                            }
                        },
                        {
                            data: "id", // Column index 10 (0-indexed) - Show Detail
                            render: function(data, type, row) {
                                var a = "{{ route('billboard.detail', ['id' => ':data']) }}"
                                    .replace(':data', data);
                                let mapUrl = row.gps_url && row.gps_url.trim() !== "" ?
                                    row.gps_url :
                                    `https://www.google.com/maps?q=${row.gps_latitude},${row.gps_longitude}`;
                                let element =
                                    `<div class="flex flex-row">
                                    <a href="javascript:;" id="detail-` + data + `"
                                        class="btn-secondary h-11" data-toggle="button" onclick="window.open('${a}')" >
                                        Site location
                                    </a>
                                    <!-- Map Button -->
                                    <a href="${mapUrl}" target="_blank"
                                    class="btn-secondary h-11">
                                    Map
                                    </a>
                                </div>`;
                                return element;
                            }
                        },
                        {
                            data: "id", // Column index 11 (0-indexed) - Actions
                            render: function(data, type, row) {
                                let element =
                                    `
                            <div class="flex items-center space-x-2">
                                <!-- Edit Button -->
                                <a href="javascript:;" 
                                    class="btn-secondary h-11 edit-billboard" 
                                    data-id="${row.id}"
                                    data-type="${row.type_prefix}"
                                    data-size="${row.size}"
                                    data-lighting="${row.lighting}"
                                    data-state_id="${row.state_id}"
                                    data-district_id="${row.district_id}"
                                    data-council_id="${row.council_id}"
                                    data-location="${row.location_name}"
                                    data-gps_latitude="${row.gps_latitude}"
                                    data-gps_longitude="${row.gps_longitude}"
                                    data-gps_url="${row.gps_url}"
                                    data-traffic_volume="${row.traffic_volume}"
                                    data-status="${row.status}"
                                    data-site_type="${row.site_type}"
                                >
                                    Edit
                                </a>
                                <!-- Delete Button -->
                                <a class="flex items-center text-theme-6" href="javascript:;" data-toggle="modal" data-target="#billboardDeleteModal" id="delete-billboard-` +
                                    data + `">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 w-4 h-4 mr-1">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                        <line x1="10" y1="11" x2="10" y2="17"></line>
                                        <line x1="14" y1="11" x2="14" y2="17"></line>
                                    </svg> 
                                </a>
                            </div>`;
                                return element;
                            }
                        },
                    ],
                    createdRow: function(row, data) {
                        // Add Tailwind border and center classes to ALL cells in the row
                        $(row).find('td').addClass('border border-neutral-300 text-center');

                        // Find the cell corresponding to the 'location_name' column (index 7) and change its alignment to left
                        const locationCellIndex = 7;
                        const locationCell = $(row).find('td').eq(locationCellIndex);
                        locationCell.removeClass('text-center').addClass(
                            'text-left'); // Remove center, add left

                        // Add padding to the location cell for better readability
                        locationCell.addClass('px-4 py-2'); // Add horizontal and vertical padding

                        // Find the cell corresponding to the 'Show Detail' column (index 10) and ensure it's center-aligned
                        const showDetailCellIndex = 10;
                        const showDetailCell = $(row).find('td').eq(showDetailCellIndex);
                        showDetailCell.removeClass('text-left').addClass(
                            'text-center'); // Ensure center

                        // Find the cell corresponding to the 'Actions' column (index 11) and ensure it's center-aligned
                        const actionsCellIndex = 11;
                        const actionsCell = $(row).find('td').eq(actionsCellIndex);
                        actionsCell.removeClass('text-left').addClass('text-center'); // Ensure center

                        // Add other attributes if needed
                        $(row)
                            .attr('data-prefix', data.prefix)
                            .attr('data-size', data.size)
                            .attr('data-lighting', data.lighting)
                            .attr('data-state_id', data.state_id)
                            .attr('data-district_id', data.district_id)
                            .attr('data-location_id', data.location_id);
                    },
                    // Add a drawCallback to potentially style the generated pagination elements if needed
                    drawCallback: function(settings) {
                        $("#billboard_table_paginate")
                            .addClass("flex justify-center items-center gap-2");

                        $("#billboard_table_paginate .paginate_button")
                            .addClass(
                                "inline-flex items-center justify-center px-2 py-1 border rounded text-xs"
                            );

                        $("#billboard_table_paginate .paginate_button.current")
                            .addClass("bg-neutral-200 font-semibold");
                    },
                    // Add callback to style pagination after initialization
                    initComplete: function(settings, json) {
                        // Style the info div (showing X to Y of Z entries)
                        var infoDiv = document.getElementById("billboard_table_info");
                        if (infoDiv) {
                            infoDiv.classList.add('text-sm', 'text-gray-600', 'mt-2');
                            // You can also wrap the text in a span or adjust spacing if needed
                        }

                        // Style the pagination div
                        var paginateDiv = document.getElementById("Billboard_paginate");
                        if (paginateDiv) {
                            paginateDiv.classList.add('flex', 'items-center', 'justify-center',
                                'space-x-2', 'mt-2');

                            // Style the individual page links
                            $(paginateDiv).find('a').addClass('px-3', 'py-1', 'border',
                                'border-gray-300', 'rounded', 'hover:bg-gray-100',
                                'focus:outline-none', 'focus:ring-2', 'focus:ring-blue-500',
                                'focus:border-blue-500');
                            $(paginateDiv).find('span').addClass('px-3', 'py-1', 'bg-gray-200',
                                'border', 'border-gray-300', 'rounded', 'font-bold');

                            // Style the "First", "Previous", "Next", "Last" links
                            $(paginateDiv).find('a').filter(function() {
                                return $(this).text().trim() === 'First' || $(this).text()
                                    .trim() === 'Previous' || $(this).text().trim() ===
                                    'Next' || $(this).text().trim() === 'Last';
                            }).addClass('px-3', 'py-1', 'border', 'border-gray-300', 'rounded',
                                'hover:bg-gray-100', 'focus:outline-none', 'focus:ring-2',
                                'focus:ring-blue-500', 'focus:border-blue-500');
                        }
                    }
                });

                // Add classes to the "dt-buttons" div
                var dtButtonsDiv = document.querySelector(".dt-buttons");
                if (dtButtonsDiv) {
                    dtButtonsDiv.classList.add("mt-2");
                }
                // Update styling for the filter input
                var filterDiv = document.getElementById("billboard_table_filter");
                if (filterDiv) {
                    filterDiv.style.float = "right";
                    filterDiv.classList.remove('dataTables_filter');
                    var inputElement = filterDiv.querySelector("label input");
                    if (inputElement) {
                        inputElement.classList.add("input", "border", "mt-2", "ml-2", "mr-1", "mb-5");
                    }
                }
                // Update styling for the info and paginate elements
                var infoDiv = document.getElementById("billboard_table_info");
                var paginateDiv = document.getElementById(
                    "billboard_table_paginate"); // DataTables auto-generates this ID
                if (infoDiv) {
                    // infoDiv.style.float = "left"; // Not Tailwind
                    infoDiv.className += " float-left"; // Example standard CSS class addition
                    // infoDiv.classList.add("mt-5"); // Tailwind
                    infoDiv.className += " mt-5"; // Example standard CSS class addition
                }
                if (paginateDiv) {
                    // paginateDiv.style.float = "right"; // Not Tailwind
                    paginateDiv.className += " float-right"; // Example standard CSS class addition
                    // paginateDiv.classList.add("mt-5"); // Tailwind
                    paginateDiv.className += " mt-5"; // Example standard CSS class addition
                }
                // Update styling for the "billboard_table_length" div and its select element
                var existingDiv = document.getElementById("billboard_table_length");
                if (existingDiv) {
                    existingDiv.classList.remove('dataTables_length');
                    existingDiv.classList.add('mt-2', 'mb-1');
                    var existingSelect = existingDiv.querySelector('select');
                    if (existingSelect) {
                        existingSelect.className = 'input sm:w-auto border';
                    }
                }
                // billboardEditModal();
            };

            initBillboardDatatable();
            setupAutoFilter();
            billboardEditModal();
            $('#billboard_table').off('click', '.edit-billboard').on('click', '.edit-billboard', function() {
                const $this = $(this);
                const billboardID = $this.data('id');
                // Set values
                $('#editBillboardType').val($this.data('type'));
                $('#editBillboardSize').val($this.data('size'));
                $('#editBillboardLighting').val($this.data('lighting'));
                // Combine latitude & longitude into one coordinate
                const latitude = $this.data('gps_latitude');
                const longitude = $this.data('gps_longitude');
                $('#editGPSCoordinate').val(latitude + ', ' + longitude);
                $('#editGPSUrl').val($this.data('gps_url'));
                $('#editBillboardTrafficVolume').val($this.data('traffic_volume'));
                $('#editBillboardStatus').val($this.data('status'));
                $('#editBillboardSiteType').val($this.data('site_type'));
                $('#editBillboardModalId').val(billboardID);
                // Get IDs
                const stateID = $this.data('state_id');
                const districtID = $this.data('district_id');
                const councilID = $this.data('council_id');
                const location = $this.data('location');
                // ✅ Set state
                $('#editBillboardState').val(stateID).trigger('change');
                // ✅ Fetch districts
                $.post('{{ route('location.getDistricts') }}', {
                    _token: '{{ csrf_token() }}',
                    state_id: stateID
                }, function(districts) {
                    $('#editBillboardDistrict').empty().append(
                        `<option value="">-- Select Area --</option>`);
                    districts.forEach(function(d) {
                        $('#editBillboardDistrict').append(
                            `<option value="${d.id}">${d.name}</option>`);
                    });
                    // ✅ if districtID is not in list (user had custom one before), add it manually
                    if (districtID && !$('#editBillboardDistrict option[value="' + districtID +
                            '"]').length) {
                        $('#editBillboardDistrict').append(
                            `<option value="${districtID}" selected>${districtID}</option>`
                        );
                    }
                    $('#editBillboardDistrict').val(districtID).trigger('change');
                    // ✅ Fetch councils after districts load
                    $.post('{{ route('location.getCouncils') }}', {
                        _token: '{{ csrf_token() }}',
                        state_id: stateID
                    }, function(councils) {
                        $('#editBillboardCouncil').empty().append(
                            `<option value="">-- Select Council --</option>`);
                        councils.forEach(function(c) {
                            $('#editBillboardCouncil').append(
                                `<option value="${c.id}">${c.name} (${c.abbreviation})</option>`
                            );
                        });
                        $('#editBillboardCouncil').val(councilID).trigger('change');
                    });
                });
                // ✅ Location
                $('#editBillboardLocation').val(location);
                // Open modal
                openAltEditorModal("#billboardEditModal");
            });
            // 🔄 On State change => fetch districts + councils
            $('#editBillboardState').on('change', function() {
                let stateID = $(this).val();
                $('#editBillboardDistrict').html('<option value="">-- Loading Districts --</option>');
                $('#editBillboardCouncil').html('<option value="">-- Loading Councils --</option>');
                $('#editBillboardLocation').html('<option value="">-- Select Location --</option>');
                if (stateID) {
                    // districts
                    $.get('/get-districts/' + stateID, function(data) {
                        let options = '<option value="">-- Select Area --</option>';
                        data.forEach(function(district) {
                            options +=
                                `<option value="${district.id}">${district.name}</option>`;
                        });
                        $('#editBillboardDistrict').html(options);
                    });
                    // councils
                    $.get('/get-councils/' + stateID, function(data) {
                        let options = '<option value="">-- Select Council --</option>';
                        data.forEach(function(c) {
                            options +=
                                `<option value="${c.id}">${c.abbreviation} - ${c.name} </option>`;
                        });
                        $('#editBillboardCouncil').html(options);
                    });
                }
            });
            // 🔄 On Area change => fetch locations
            $('#editBillboardDistrict').on('change', function() {
                let districtID = $(this).val();
                $('#editBillboardLocation').html('<option value="">-- Loading Locations --</option>');
                if (districtID) {
                    $.get('/get-locations/' + districtID, function(data) {
                        let options = '<option value="">-- Select Location --</option>';
                        data.forEach(function(location) {
                            options +=
                                `<option value="${location.id}">${location.name}</option>`;
                        });
                        $('#editBillboardLocation').html(options);
                    });
                }
            });
            // Add New Billboard
            function billboardAddButton() {
                $.ajax({
                    type: 'POST',
                    url: "{{ route('billboard.create') }}",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        type: document.getElementById("inputBillboardType").value,
                        size: document.getElementById("inputBillboardSize").value,
                        lighting: document.getElementById("inputBillboardLighting").value,
                        state: document.getElementById("inputBillboardState").value,
                        district: document.getElementById("inputBillboardDistrict").value,
                        council: document.getElementById("inputBillboardCouncil").value,
                        land: document.getElementById("inputBillboardLand").value,
                        location: document.getElementById("inputBillboardLocation").value,
                        gps_coordinate: document.getElementById("inputGPSCoordinate").value,
                        gps_url: document.getElementById("inputMapsUrl").value,
                        trafficvolume: document.getElementById("inputBillboardTrafficVolume").value,
                        siteType: document.getElementById("inputBillboardSiteType").value,
                    },
                    success: function(response) {
                        // Close modal after successfully edited
                        var element = "#addBillboardModal";
                        closeAltEditorModal(element);
                        // Show successful toast
                        window.showSubmitToast("Successfully added.", "#91C714");
                        // Clean fields
                        document.getElementById("inputBillboardType").value = "";
                        document.getElementById("inputBillboardSize").value = "";
                        document.getElementById("inputBillboardLighting").value = "";
                        document.getElementById("inputBillboardState").value = "";
                        document.getElementById("inputBillboardDistrict").value = "";
                        // $('#inputBillboardDistrict').val(null).trigger('change');
                        document.getElementById("inputBillboardCouncil").value = "";
                        document.getElementById("inputBillboardLand").value = "";
                        document.getElementById("inputBillboardLocation").value = "";
                        document.getElementById("inputGPSCoordinate").value = "";
                        document.getElementById("inputMapsUrl").value = "";
                        document.getElementById("inputBillboardTrafficVolume").value = "";
                        document.getElementById("inputBillboardSiteType").value = "";
                        // Reload table
                        $('#billboard_table').DataTable().ajax.reload();
                    },
                    error: function(xhr, status, error) {
                        // Display the validation error message
                        var response = JSON.parse(xhr.responseText);
                        var error = "Error: " + response.error;
                        // Show fail toast
                        window.showSubmitToast(error, "#D32929");
                    }
                });
            };
            $('#billboardEditButton').on('click', function(e) {
                e.preventDefault();
                $.ajax({
                    url: '{{ route('billboard.update') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: $('#editBillboardModalId').val(),
                        type: $('#editBillboardType').val(),
                        size: $('#editBillboardSize').val(),
                        lighting: $('#editBillboardLighting').val(),
                        state_id: $('#editBillboardState').val(),
                        district_id: $('#editBillboardDistrict').val(),
                        council_id: $('#editBillboardCouncil').val(),
                        location_name: $('#editBillboardLocation').val(), // 👈 send as name
                        gps_coordinate: $('#editGPSCoordinate').val(),
                        gps_url: $('#editGPSUrl').val(),
                        traffic_volume: $('#editBillboardTrafficVolume').val(),
                        status: $('#editBillboardStatus').val(),
                        site_type: $('#editBillboardSiteType').val(),
                    },
                    success: function(response) {
                        // Close modal after successfully edited
                        var element = "#billboardEditModal";
                        closeAltEditorModal(element);
                        // Show successful toast
                        window.showSubmitToast("Successfully added.", "#91C714");
                        // Reload table
                        $('#billboard_table').DataTable().ajax.reload();
                        // Clean fields
                        document.getElementById("editBillboardModalId").value = "";
                        document.getElementById("editBillboardType").value = "";
                        document.getElementById("editBillboardSize").value = "";
                        document.getElementById("editBillboardLighting").value = "";
                        document.getElementById("editBillboardState").value = "";
                        document.getElementById("editBillboardDistrict").value = "";
                        document.getElementById("editBillboardCouncil").value = "";
                        document.getElementById("editBillboardLocation").value = "";
                        document.getElementById("editGPSLongitude").value = "";
                        document.getElementById("editGPSLatitude").value = "";
                        document.getElementById("editGPSCoordinate").value = "";
                        document.getElementById("editGPSUrl").value = "";
                        document.getElementById("editBillboardTrafficVolume").value = "";
                        document.getElementById("editBillboardStatus").value = "";
                        document.getElementById("editBillboardSiteType").value = "";
                        // Reset the button visibility and enable it for next submission
                        document.getElementById("billboardEditButton").disabled = false;
                        document.getElementById('billboardEditButton').style.display =
                            'inline-block'; // Shows the button again
                    },
                    error: function(xhr, status, error) {
                        // Display the validation error message
                        var response = JSON.parse(xhr.responseText);
                        var error = "Error: " + response.error;
                        // Show fail toast
                        window.showSubmitToast(error, "#D32929");
                    }
                });
            });

            function billboardEditModal() {
                $(document).off('click', "[id^='edit-']");
                $(document).on('click', "[id^='edit-']", function(event) {
                    event.preventDefault();
                    let billboardID = $(this).attr('id').split('-')[1];
                    let row = $(this).closest('tr');
                    let prefix = row.attr('data-prefix') || "";
                    let size = row.attr('data-size') || "";
                    let lighting = row.attr('data-lighting') || "";
                    let stateID = row.attr('data-state_id') || "";
                    let districtID = row.attr('data-district_id') || "";
                    let locationID = row.attr('data-location_id') || "";
                    let latitude = row.attr('data-latitude') || "";
                    let longitude = row.attr('data-longitude') || "";
                    let traffic = row.attr('data-traffic') || "";
                    $('#editBillboardType').val(prefix);
                    $('#editBillboardSize').val(size);
                    $('#editBillboardLighting').val(lighting);
                    $('#editGPSLatitude').val(latitude);
                    $('#editGPSLongitude').val(longitude);
                    $('#editGPSCoordinate').val(longitude);
                    $('#editGPSUrl').val(row.data('gps_url') || "");
                    // Combined GPS coordinate field
                    if (latitude && longitude) {
                        $('#editGPSCoordinate').val(latitude + ', ' + longitude);
                    } else {
                        $('#editGPSCoordinate').val("");
                    }
                    $('#editBillboardTrafficVolume').val(traffic);
                    $('#editBillboardStatus').val(status);
                    $('#editBillboardSiteType').val(site_type);
                    // Trigger state change to load districts
                    $('#editBillboardState').val(stateID).trigger('change');
                    setTimeout(() => {
                        $('#editBillboardDistrict').val(districtID).trigger('change');
                        setTimeout(() => {
                            $('#editBillboardLocation').val(locationID);
                        }, 300);
                    }, 300);
                    openAltEditorModal("#billboardEditModal");
                });
            }
            // Delete billboard ID
            // function billboardDeleteButton() {
            //     var id = lastClickedLink.split("-")[2];
            //     $.ajax({
            //         type: 'POST',
            //         url: "{{ route('billboard.delete') }}",
            //         data: {
            //             _token: $('meta[name="csrf-token"]').attr('content'),
            //             id: id,
            //         },
            //         success: function(response) {
            //             // Close modal after successfully deleted
            //             var element = "#billboardDeleteModal";
            //             closeAltEditorModal(element);
            //             // Show successful toast
            //             window.showSubmitToast("Successfully deleted.", "#91C714");
            //             // Reload table
            //             $('#billboard_table').DataTable().ajax.reload();
            //             // Reload the entire page
            //             // location.reload();
            //         },
            //         error: function(xhr, status, error) {
            //             // Display the validation error message
            //             var response = JSON.parse(xhr.responseText);
            //             var error = "Error: " + response.error;
            //             // Show fail toast
            //             window.showSubmitToast(error, "#D32929");
            //         }
            //     });
            // }
            // Delete billboard ID
            function billboardDeleteButton() {
                // 1. Get the ID from the stored link ID (e.g., "delete-billboard-123" -> "123")
                var id = lastClickedLink.split("-")[2];
                console.log("Attempting to delete billboard with ID:", id); // Debug log

                // 2. Perform the AJAX request
                $.ajax({
                    type: 'POST',
                    url: "{{ route('billboard.delete') }}",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        id: id,
                    },
                    success: function(response) {
                        console.log("Delete AJAX call successful, response:", response); // Debug log

                        // 3. Close the modal
                        var element = "#billboardDeleteModal";
                        closeAltEditorModal(element);
                        console.log("Modal closed."); // Debug log

                        // 4. Attempt to show success toast
                        try {
                            // Ensure the function exists before calling it
                            if (typeof window.showSubmitToast === 'function') {
                                window.showSubmitToast("Successfully deleted.", "#91C714");
                                console.log("Success toast shown via showSubmitToast."); // Debug log
                            } else {
                                console.warn("showSubmitToast function is not defined.");
                                // Fallback: Use a standard browser alert or a different notification method
                                alert("Successfully deleted billboard with ID: " +
                                    id); // Or use a better notification library if available
                                console.log("Success message shown via alert (fallback)."); // Debug log
                            }
                        } catch (toastError) {
                            console.error("Error calling showSubmitToast:", toastError);
                            // Even if the toast function failed, the action was successful, so maybe alert
                            alert("Successfully deleted billboard with ID: " + id); // Fallback
                        }

                        // 5. Attempt to reload the DataTable (this part is working now)
                        try {
                            // Ensure the DataTable exists and is initialized
                            var tableElement = $('#billboard_table');
                            if ($.fn.DataTable.isDataTable(tableElement)) {
                                var table = tableElement.DataTable();
                                table.ajax.reload(); // Reload data from server
                                console.log("DataTable reloaded."); // Debug log
                            } else {
                                console.warn(
                                    "DataTable is not initialized on #billboard_table when trying to reload."
                                );
                                // Fallback: maybe reload the whole page if reload fails critically
                                // location.reload();
                            }
                        } catch (reloadError) {
                            console.error("Error reloading DataTable:", reloadError);
                            // Fallback: maybe reload the whole page if reload fails critically
                            // location.reload();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Delete AJAX call failed:", error, xhr, status); // Debug log
                        // Close the modal even on error
                        var element = "#billboardDeleteModal";
                        closeAltEditorModal(element);

                        // Display the error message
                        var errorMessage = "Error deleting billboard.";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseText) {
                            try {
                                var responseObj = JSON.parse(xhr.responseText);
                                if (responseObj.error) {
                                    errorMessage = "Error: " + responseObj.error;
                                }
                            } catch (e) {
                                // If response is not JSON, use the raw text or status
                                errorMessage = "Error: " + xhr.status + " " + xhr.statusText;
                            }
                        }
                        // Show error toast or alert
                        if (typeof window.showSubmitToast === 'function') {
                            window.showSubmitToast(errorMessage, "#D32929"); // Red color for error
                        } else {
                            alert(errorMessage); // Fallback
                        }
                    }
                });
            }
            // Store the ID of the last clicked modal when it's triggered
            (function() {
                $(document).on('click', "[data-toggle='modal']", function() {
                    lastClickedLink = $(this).attr('id');
                });
            })();
        });
    </script>
@endsection
