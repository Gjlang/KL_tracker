@extends('layouts.app')

@section('title')
    Clients
@endsection
@section('content')

    <head>

        <!-- ... other head content ... -->
        <title>@yield('title', 'Default Title')</title> <!-- Example -->
        <style>
            /* Force pagination container into horizontal row */
            #client_company_table_paginate {
                display: flex !important;
                justify-content: center;
                /* center horizontally */
                align-items: center;
                gap: 0.5rem;
                /* spacing between buttons */
            }

            /* Make each button horizontal-friendly */
            #client_company_table_paginate .paginate_button {
                display: inline-flex !important;
                align-items: center;
                justify-content: center;
            }

            /* Optional: style current/active page */
            #client_company_table_paginate .paginate_button.current {
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
        <!-- Add jQuery CDN -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

        <!-- Include DataTables JS (if not already included elsewhere) -->
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script> <!-- If using Bootstrap theme -->

        @vite(['resources/css/app.css', 'resources/js/app.js']) <!-- If using Vite -->
    </head>
    <div class="intro-y flex flex-col sm:flex-row items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Clients
        </h2>
    </div>


    <!-- filter and datatable -->
    <div class="intro-y box p-5 mt-5">
        <div class="pos col-span-12 lg:col-span-4">
            <!-- BEGIN: Client Company -->
            <div>
                <!-- BEGIN: Filter & Add Client Company -->
                <div class="flex flex-col sm:flex-row sm:items-end xl:items-start">
                    <!-- BEGIN: Filter -->
                    <form class="xl:flex sm:mr-auto">
                        <!-- <div class="sm:flex items-center sm:mr-4">
                            <label class="w-12 flex-none xl:w-auto xl:flex-initial mr-2">Company Status</label>
                            <select class="input w-full sm:w-32 xxl:w-full mt-2 sm:mt-0 sm:w-auto border" id="inputContractStatus">
                                <option value="all">All</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="mt-2 xl:mt-0">
                            <button type="button" class="button w-full sm:w-16 bg-theme-32 text-white" id="filterClientCompanyButton">Filter</button>
                        </div> -->
                    </form>
                    <!-- END: Filter -->

                    <!-- BEGIN: Add Client Company -->
                    <div class="text-center">
                        <a href="javascript:;" onclick="openAltEditorModal('#clientCompanyAddModal')"
                            class="flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-plus w-4 h-4">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            Add New Client
                        </a>
                    </div>
                    <!-- END: Add Client Company -->
                </div>
                <!-- END: Filter & Add Client Company -->

                <!-- BEGIN: Client Company List -->
                <div class="overflow-x-auto">
                    <table id="client_company_table" class="min-w-full border-collapse border border-neutral-300">
                        <!-- Add border classes to the table -->
                        <thead class="bg-neutral-50 sticky top-0 z-10">
                            <tr class="border-b border-neutral-300">
                                <th class="px-4 py-4 table-header border-r border-neutral-300">PIC</th>
                                <!-- for details-control -->
                                <th class="px-4 py-4 table-header border-r border-neutral-300">No</th>
                                <th class="px-4 py-4 table-header min-w-[350px] border-r border-neutral-300">Client Name
                                </th>
                                <th class="px-4 py-4 table-header min-w-[350px] border-r border-neutral-300">Address</th>
                                <th class="px-4 py-4 table-header min-w-[100px] border-r border-neutral-300">Phone No.</th>
                                <th class="px-4 py-4 table-header min-w-[100px] dt-exclude-export dt-no-sort">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="client_company_tbody" class="bg-white divide-y divide-neutral-200">
                            <!-- 'divide-y' adds borders between rows -->
                            <!-- DataTables will populate this body with <tr> elements -->
                            <!-- Each <tr> generated by DataTables should ideally have borders applied -->
                            <!-- You can modify the DataTables column definitions to add borders to cells if needed -->
                        </tbody>
                    </table>
                </div>
                <!-- END: Client Company List -->
            </div>
            <!-- END: Client Company -->
        </div>
    </div>

    <!-- BEGIN: Client Company Add Modal -->
    <div class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden"
        id="clientCompanyAddModal">
        <div class="modal__content bg-white rounded-lg shadow-xl w-full max-w-md sm:max-w-lg mx-4">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Add Client Company</h2>
                <button type="button" onclick="closeAltEditorModal('#clientCompanyAddModal')"
                    class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <!-- Form ID remains the same -->
            <form id="clientCompanyAddForm">
                <div class="p-6 space-y-4">
                    <!-- Client Name -->
                    <div class="space-y-1">
                        <label for="clientCompanyAddName" class="block text-sm font-medium text-gray-700">
                            Client Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out"
                            placeholder="Enter Client Name" id="clientCompanyAddName" name="name" required>
                    </div>
                    <!-- Client Address -->
                    <div class="space-y-1">
                        <label for="clientCompanyAddAddress" class="block text-sm font-medium text-gray-700">
                            Client Address
                        </label>
                        <input type="text"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out"
                            placeholder="Enter Client Address" id="clientCompanyAddAddress" name="address">
                    </div>
                    <!-- Phone No. -->
                    <div class="space-y-1">
                        <label for="clientCompanyAddPhone" class="block text-sm font-medium text-gray-700">
                            Phone No.
                        </label>
                        <input type="tel"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out"
                            placeholder="Enter Phone Number" id="clientCompanyAddPhone" name="companyPhone">
                    </div>

                    <!-- Separator -->
                    <div class="py-2">
                        <hr class="border-t border-gray-300">
                    </div>

                    <!-- PIC Section Header -->
                    <div class="space-y-1">
                        <h3 class="text-lg font-medium text-gray-800">Add PIC(s)</h3>
                    </div>

                    <!-- PIC Container -->
                    <div id="picContainer">
                        <div class="pic space-y-4">
                            <!-- First PIC Input Group -->
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-gray-700">Name <span
                                        class="text-red-500">*</span></label>
                                <input type="text"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out"
                                    placeholder="Enter Name" name="pic_names[]" required>
                            </div>
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out"
                                    placeholder="Enter Email" name="pic_emails[]">
                            </div>
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-gray-700">Phone No.</label>
                                <input type="tel"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out"
                                    placeholder="Enter Phone No." name="pic_phones[]">
                            </div>
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-gray-700">Designation</label>
                                <input type="text"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out"
                                    placeholder="Enter Designation" name="pic_designations[]">
                            </div>
                            <!-- Remove Button for first PIC (initially disabled) -->
                            <div class="flex justify-end">
                                <button type="button"
                                    class="text-sm text-red-600 hover:text-red-800 px-2 py-1 rounded-md border border-red-300 hover:bg-red-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                    onclick="removePIC(this)" disabled>
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Add PIC Button -->
                    <div class="pt-2">
                        <button type="button" onclick="picAdd()"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                            + Add Another PIC
                        </button>
                    </div>
                </div>
                <!-- Modal Footer -->
                <div
                    class="flex items-center justify-end px-6 py-4 space-x-3 border-t border-gray-200 bg-gray-50 rounded-b-lg">
                    <button type="button" onclick="closeAltEditorModal('#clientCompanyAddModal')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                        Cancel
                    </button>
                    <!-- Changed button type to "submit" and removed onclick -->
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition"
                        id="addClientCompany">
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!-- END: Client Company Add Modal -->

    <!-- PIC Template (Hidden) -->
    <template id="picTemplate">
        <div class="pic space-y-4">
            <div class="space-y-1">
                <label class="block text-sm font-medium text-gray-700">Name <span class="text-red-500">*</span></label>
                <input type="text"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out"
                    placeholder="Enter Name" name="pic_names[]" required>
            </div>
            <div class="space-y-1">
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out"
                    placeholder="Enter Email" name="pic_emails[]">
            </div>
            <div class="space-y-1">
                <label class="block text-sm font-medium text-gray-700">Phone No.</label>
                <input type="tel"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out"
                    placeholder="Enter Phone No." name="pic_phones[]">
            </div>
            <div class="space-y-1">
                <label class="block text-sm font-medium text-gray-700">Designation</label>
                <input type="text"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out"
                    placeholder="Enter Designation" name="pic_designations[]">
            </div>
            <div class="flex justify-end">
                <button type="button"
                    class="text-sm text-red-600 hover:text-red-800 px-2 py-1 rounded-md border border-red-300 hover:bg-red-50"
                    onclick="removePIC(this)">
                    Remove
                </button>
            </div>
        </div>
    </template>

    <!-- BEGIN: Client Company Edit Modal -->
    <div class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden"
        id="clientCompanyEditModal">
        <!-- Backdrop -->
        <div class="modal__content bg-white rounded-lg shadow-xl w-full max-w-md sm:max-w-lg mx-4">
            <!-- Adjusted max-width and added mx-4 -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Edit Client Company</h2> <!-- Updated title -->
                <!-- Close Button -->
                <button type="button" onclick="closeAltEditorModal('#clientCompanyEditModal')" data-dismiss="modal"
                    class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="clientCompanyEditForm">
                <div class="p-6 space-y-4"> <!-- Used space-y for consistent vertical spacing -->
                    <!-- Client Name -->
                    <div class="space-y-1">
                        <label for="clientCompanyEditName" class="block text-sm font-medium text-gray-700">
                            Client Name <span class="text-red-500">*</span> <!-- Improved required indicator -->
                        </label>
                        <input type="text"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out"
                            placeholder="Enter Client Name" id="clientCompanyEditName" name="name" required>
                    </div>
                    <!-- Client Address -->
                    <div class="space-y-1">
                        <label for="clientCompanyEditAddress" class="block text-sm font-medium text-gray-700">
                            Client Address
                        </label>
                        <input type="text"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out"
                            placeholder="Enter Client Address" id="clientCompanyEditAddress" name="address">
                    </div>
                    <!-- Phone No. -->
                    <div class="space-y-1">
                        <label for="clientCompanyEditPhone" class="block text-sm font-medium text-gray-700">
                            Phone No.
                        </label>
                        <input type="tel"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out"
                            placeholder="Enter Phone Number" id="clientCompanyEditPhone" name="phone">
                    </div>
                </div>
                <!-- Modal Footer -->
                <div
                    class="flex items-center justify-end px-6 py-4 space-x-3 border-t border-gray-200 bg-gray-50 rounded-b-lg">
                    <button type="button" onclick="closeAltEditorModal('#clientCompanyEditModal')" data-dismiss="modal"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition"
                        id="clientCompanyEditButton">
                        Update <!-- Changed text from 'Submit' to 'Update' for clarity -->
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!-- END: Client Company Edit Modal -->

    <!-- BEGIN: Client Company Delete Modal -->
    <div class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden"
        id="clientCompanyDeleteModal">
        <div class="modal__content bg-white rounded-lg shadow-xl w-full max-w-md sm:max-w-sm mx-4">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Confirm Deletion</h2>
                <button type="button" onclick="closeAltEditorModal('#clientCompanyDeleteModal')" data-dismiss="modal"
                    class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-6">
                <div class="text-center">
                    <!-- Warning Icon -->
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-red-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Are you sure?</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        Confirm deleting the client company? This action cannot be undone.
                    </p>
                </div>
            </div>
            <div
                class="flex flex-col sm:flex-row items-center justify-center px-6 py-4 space-y-3 sm:space-y-0 sm:space-x-3 border-t border-gray-200 bg-gray-50 rounded-b-lg">
                <button type="button" onclick="closeAltEditorModal('#clientCompanyDeleteModal')" data-dismiss="modal"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                    Cancel
                </button>
                <button type="button"
                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition"
                    id="clientCompanyDeleteButton">
                    Delete
                </button>
            </div>
        </div>
    </div>
    <!-- END: Client Company Delete Modal -->




    <!-- BEGIN: PIC Add Modal -->
    <div class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden" id="picAddModal">
        <div class="modal__content bg-white rounded-lg shadow-xl w-full max-w-md sm:max-w-lg mx-4">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Add Person In Charge (PIC)</h2>
                <button type="button" onclick="closeAltEditorModal('#picAddModal')" data-dismiss="modal"
                    class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="picAddForm"> <!-- Added ID for potential event listener -->
                @csrf
                <input type="hidden" id="picAddCompanyId"> <!-- hidden field for company ID -->
                <div class="p-6 space-y-4"> <!-- Used space-y for consistent vertical spacing -->
                    <!-- Designation -->
                    <div class="space-y-1">
                        <label for="picAddDesignation" class="block text-sm font-medium text-gray-700">
                            Designation
                        </label>
                        <input type="text" title="Please enter client designation" placeholder="Enter Designation"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out"
                            id="picAddDesignation" name="designation">
                    </div>
                    <!-- PIC Name -->
                    <div class="space-y-1">
                        <label for="picAddName" class="block text-sm font-medium text-gray-700">
                            PIC Name <span class="text-red-500">*</span> <!-- Improved required indicator -->
                        </label>
                        <input type="text" placeholder="Enter PIC Name"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out"
                            id="picAddName" name="name" required>
                    </div>
                    <!-- Email -->
                    <div class="space-y-1">
                        <label for="picAddEmail" class="block text-sm font-medium text-gray-700">
                            Email
                        </label>
                        <input type="email" placeholder="Enter Email Address"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out"
                            id="picAddEmail" name="email" required>
                    </div>
                    <!-- Contact No. -->
                    <div class="space-y-1">
                        <label for="picAddContact" class="block text-sm font-medium text-gray-700">
                            Contact No.
                        </label>
                        <input type="tel" title="Please enter contact number in correct format"
                            placeholder="Enter Contact Number"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out"
                            id="picAddContact" name="phone" required>
                    </div>
                </div>
                <!-- Modal Footer -->
                <div
                    class="flex items-center justify-end px-6 py-4 space-x-3 border-t border-gray-200 bg-gray-50 rounded-b-lg">
                    <button type="button" onclick="closeAltEditorModal('#picAddModal')" data-dismiss="modal"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition"
                        id="picAddButton">
                        Save PIC
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!-- END: PIC Add Modal -->

    <!-- BEGIN: PIC Edit Modal -->
    <div class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden"
        id="picEditModal">
        <div class="modal__content bg-white rounded-lg shadow-xl w-full max-w-md sm:max-w-lg mx-4">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Edit Person In Charge (PIC)</h2>
                <button type="button" onclick="closeAltEditorModal('#picEditModal')" data-dismiss="modal"
                    class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="picEditForm"> <!-- Added ID for potential event listener -->
                <div class="p-6 space-y-4"> <!-- Used space-y for consistent vertical spacing -->
                    <!-- Designation -->
                    <div class="space-y-1">
                        <label for="picEditDesignation" class="block text-sm font-medium text-gray-700">
                            Designation
                        </label>
                        <input type="text"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out"
                            placeholder="Enter Designation" id="picEditDesignation" name="designation">
                    </div>
                    <!-- PIC Name -->
                    <div class="space-y-1">
                        <label for="picEditName" class="block text-sm font-medium text-gray-700">
                            PIC Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out"
                            placeholder="Enter PIC Name" id="picEditName" name="name" required>
                    </div>
                    <!-- Contact No. -->
                    <div class="space-y-1">
                        <label for="picEditContact" class="block text-sm font-medium text-gray-700">
                            Contact No.
                        </label>
                        <input type="tel"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out"
                            placeholder="Enter Contact Number" id="picEditContact" name="phone">
                    </div>
                    <!-- Email -->
                    <div class="space-y-1">
                        <label for="picEditEmail" class="block text-sm font-medium text-gray-700">
                            Email
                        </label>
                        <input type="email"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out"
                            placeholder="Enter Email Address" id="picEditEmail" name="email">
                    </div>
                </div>
                <!-- Modal Footer -->
                <div
                    class="flex items-center justify-end px-6 py-4 space-x-3 border-t border-gray-200 bg-gray-50 rounded-b-lg">
                    <button type="button" onclick="closeAltEditorModal('#picEditModal')" data-dismiss="modal"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition"
                        id="picEditButton">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!-- END: PIC Edit Modal -->

    <!-- BEGIN: PIC Delete Modal -->
    <div class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden"
        id="picDeleteModal">
        <div class="modal__content bg-white rounded-lg shadow-xl w-full max-w-md sm:max-w-sm mx-4">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Confirm Deletion</h2>
                <button type="button" onclick="closeAltEditorModal('#picDeleteModal')" data-dismiss="modal"
                    class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-6">
                <div class="text-center">
                    <!-- Warning Icon -->
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-red-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Are you sure?</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        Confirm deleting the Person In Charge (PIC)? This action cannot be undone.
                    </p>
                </div>
            </div>
            <div
                class="flex flex-col sm:flex-row items-center justify-center px-6 py-4 space-y-3 sm:space-y-0 sm:space-x-3 border-t border-gray-200 bg-gray-50 rounded-b-lg">
                <button type="button" onclick="closeAltEditorModal('#picDeleteModal')" data-dismiss="modal"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                    Cancel
                </button>
                <button type="button"
                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition"
                    id="picDeleteButton">
                    Delete
                </button>
            </div>
        </div>
    </div>
    <!-- END: PIC Delete Modal -->


    <script>
        // Add a new PIC input field
        function picAdd() {
            // Clone the template content
            var newPicElement = document.getElementById('picTemplate').content.cloneNode(true);
            // Append the cloned element to the container
            document.getElementById('picContainer').appendChild(newPicElement.firstElementChild);

            // Optional: Enable the 'Remove' button for previous PICs if there was only one before
            // This is just for better UX if you want the first PIC to become removable after adding a second
            var picElements = document.querySelectorAll('#picContainer .pic');
            if (picElements.length > 1) {
                // Find the 'Remove' button in the *previous* last element (the one before the one just added)
                var lastPic = picElements[picElements.length - 2]; // Index of the one before the newly added one
                var removeButton = lastPic.querySelector('button[onclick="removePIC(this)"]');
                if (removeButton) {
                    removeButton.disabled = false; // Enable the remove button
                }
            }
        }

        // Remove a pic input field in add modal
        function removePIC(button) {
            var container = button.closest('.pic'); // Use closest to find the parent .pic div
            if (container) {
                var inputFields = container.querySelectorAll('input'); // Get all input fields within the container
                var hasData = false;
                // Check if any input field within the container has a value
                inputFields.forEach(function(input) {
                    if (input.value.trim() !== '') {
                        hasData = true;
                    }
                });

                // Remove the entire .pic container div
                container.remove();

                // Optional: Disable the 'Remove' button of the *new* last PIC if only one remains
                var remainingPicElements = document.querySelectorAll('#picContainer .pic');
                if (remainingPicElements.length === 1) {
                    var lastRemainingPic = remainingPicElements[0];
                    var lastRemainingRemoveButton = lastRemainingPic.querySelector('button[onclick="removePIC(this)"]');
                    if (lastRemainingRemoveButton) {
                        lastRemainingRemoveButton.disabled = true; // Disable the remove button again
                    }
                }
            }
        }

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

        $(document).ready(function() {
            // Global variables
            var filterClientCompanyStatus = null;
            let originalCompanyId = null;
            var lastClickedLink;

            // Listen to below buttons
            // document.getElementById("filterClientCompanyButton").addEventListener("click", filterClientCompanyButton);
            document.getElementById("addClientCompany").addEventListener("click", addClientCompany);
            document.getElementById("clientCompanyDeleteButton").addEventListener("click",
                clientCompanyDeleteButton);

            function initClientCompanyDatatable() {
                const dt = new Date();
                const formattedDate =
                    `${dt.getFullYear()}${(dt.getMonth() + 1).toString().padStart(2, '0')}${dt.getDate().toString().padStart(2, '0')}`;
                const formattedTime = `${dt.getHours()}:${dt.getMinutes()}:${dt.getSeconds()}`;
                const $fileName = `Client_Company_List_${formattedDate}_${formattedTime}`;

                const table = $('#client_company_table').DataTable({
                    destroy: true,
                    processing: true,
                    serverSide: true,
                    ordering: true,
                    order: [
                        [0, 'desc']
                    ],
                    pageLength: 25,
                    ajax: {
                        url: "{{ route('client-company.list') }}",
                        type: "POST",
                        data: function(d) {
                            d._token = $('meta[name="csrf-token"]').attr('content');
                            d.status = filterClientCompanyStatus;
                            return d;
                        },
                        dataSrc: function(json) {
                            return json.data;
                        }
                    },
                    dom: "lBfrtip",
                    buttons: [{
                        extend: "excel",
                        className: "button w-24 rounded-full shadow-md mr-1 mb-2 bg-theme-7 text-white",
                        title: $fileName,
                        exportOptions: {
                            columns: ":not(.dt-exclude-export)"
                        },
                        init: function(api, node) {
                            $(node).removeClass('dt-button buttons-html5');
                        },
                    }, ],
                    columnDefs: [{
                        targets: 'dt-no-sort',
                        orderable: false
                    }],
                    columns: [{
                            className: 'details-control',
                            orderable: false,
                            data: null,
                            defaultContent: '',
                            render: () =>
                                '<button class="bg-blue-600 text-white px-1 rounded">+</button>'
                        },
                        {
                            data: null,
                            name: 'no',
                            orderable: false,
                            searchable: false,
                            render: (data, type, row, meta) => meta.row + meta.settings._iDisplayStart +
                                1
                        },
                        {
                            data: "name"
                        },
                        {
                            data: "address"
                        },
                        {
                            data: "phone"
                        },
                        {
                            data: "id",
                            render: (data) => `
                            <div class="flex justify-center items-center gap-3">
                                <!-- Edit Icon -->
                                <a href="javascript:;" 
                                class="client-company-edit flex items-center justify-center w-8 h-8 text-theme-9 rounded hover:bg-gray-100" 
                                data-id="${data}" 
                                title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" 
                                        class="w-5 h-5" 
                                        fill="none" 
                                        viewBox="0 0 24 24" 
                                        stroke="currentColor">
                                        <path d="M15.232 5.232l3.536 3.536M9 13h3l9-9a1.5 1.5 0 00-2.121-2.121l-9 9v3z"/>
                                    </svg>
                                </a>

                                <!-- Delete Icon -->
                                <a href="javascript:;" 
                                class="client-company-delete flex items-center justify-center w-8 h-8 text-theme-6 rounded hover:bg-gray-100" 
                                data-id="${data}" 
                                title="Delete">
                                    <svg xmlns="http://www.w3.org/2000/svg" 
                                        class="w-5 h-5" 
                                        fill="none" 
                                        viewBox="0 0 24 24" 
                                        stroke="currentColor">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4
                                                a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                        <line x1="10" y1="11" x2="10" y2="17"></line>
                                        <line x1="14" y1="11" x2="14" y2="17"></line>
                                    </svg>
                                </a>
                            </div>
                        `
                        }
                    ],
                    createdRow: function(row, data) {
                        // Add Tailwind border and center classes to ALL cells in the row
                        $(row).find('td').addClass('border border-neutral-300 text-center');

                        // Find the cell corresponding to the 'client_name' column (index 7) and change its alignment to left
                        const clientCellIndex = 2;
                        const clientCell = $(row).find('td').eq(clientCellIndex);
                        clientCell.removeClass('text-center').addClass(
                        'text-left'); // Remove center, add left

                        // Add padding to the client cell for better readability
                        clientCell.addClass('px-4 py-2'); // Add horizontal and vertical padding

                        // Find the cell corresponding to the 'Show Detail' column (index 10) and ensure it's center-aligned
                        const clientAddrCellIndex = 3;
                        const clientAddrCell = $(row).find('td').eq(clientAddrCellIndex);
                        clientAddrCell.removeClass('text-center').addClass(
                        'text-left'); // Ensure center

                        // Add padding to the client cell for better readability
                        clientAddrCell.addClass('px-4 py-2'); // Add horizontal and vertical padding

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
                        $("#client_company_table_paginate")
                            .addClass("flex justify-center items-center gap-2");

                        $("#client_company_table_paginate .paginate_button")
                            .addClass(
                                "inline-flex items-center justify-center px-2 py-1 border rounded text-xs"
                                );

                        $("#client_company_table_paginate .paginate_button.current")
                            .addClass("bg-neutral-200 font-semibold");
                    },

                    initComplete: function() {
                        // Safe styling adjustments (only run after table is built)
                        const dtButtonsDiv = document.querySelector(".dt-buttons");
                        if (dtButtonsDiv) dtButtonsDiv.classList.add("mt-2");

                        const filterDiv = document.getElementById("client_company_table_filter");
                        if (filterDiv) {
                            filterDiv.style.float = "right";
                            filterDiv.classList.remove('dataTables_filter');
                            const inputElement = filterDiv.querySelector("label input");
                            if (inputElement) inputElement.classList.add("input", "border", "mt-2",
                                "ml-2", "mr-1", "mb-5");
                        }

                        const infoDiv = document.getElementById("client_company_table_info");
                        if (infoDiv) {
                            infoDiv.style.float = "left";
                            infoDiv.classList.add("mt-5");
                        }

                        const paginateDiv = document.getElementById("client_company_table_paginate");
                        if (paginateDiv) {
                            paginateDiv.style.float = "right";
                            paginateDiv.classList.add("mt-5");
                        }

                        const existingDiv = document.getElementById("client_company_table_length");
                        if (existingDiv) {
                            existingDiv.classList.remove('dataTables_length');
                            existingDiv.classList.add('mt-2', 'mb-1');
                            const existingSelect = existingDiv.querySelector('select');
                            if (existingSelect) existingSelect.className = 'input sm:w-auto border';
                        }
                    }
                });

                // Row expand/collapse for PICs
                $('#client_company_table tbody').off('click', 'td.details-control').on('click',
                    'td.details-control',
                    function() {
                        const tr = $(this).closest('tr');
                        const row = table.row(tr);

                        if (row.child.isShown()) {
                            row.child.hide();
                            tr.removeClass('shown');
                            $(this).find("button").text("+");
                        } else {
                            const companyId = row.data().id;
                            $.ajax({
                                url: "{{ route('client-company.pics') }}",
                                type: "POST",
                                data: {
                                    _token: $('meta[name="csrf-token"]').attr('content'),
                                    company_id: companyId
                                },
                                success: function(response) {
                                    // pass companyId to formatPICs
                                    row.child(formatPICs(response.pics, companyId)).show();
                                    tr.addClass('shown');
                                    $(tr).find("td.details-control button").text("-");
                                }
                            });

                        }
                    });

                // clientCompanyEditModal();
            }

            function formatPICs(pics, companyId) {
                if (!pics || pics.length === 0) {
                    return `
                    <div class="p-2">No PICs available</div>
                    <div class="flex justify-end mb-2 mt-2">
                        <button class="bg-theme-1 text-white px-5 py-1 rounded add-pic-btn" data-company-id="${companyId}">
                            + Add PIC
                        </button>
                    </div>
                `;
                }

                // Start building the HTML string for the table
                let html = `
                <div class="ml-3 mb-1"><strong>PIC</strong></div>
                <table class="table-auto w-full text-sm border border-gray-300 rounded-lg overflow-hidden">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-300">Name</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-300">Email</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-300">Phone</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-300">Designation</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-300">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
            `;

                // Loop through each PIC and add a row
                pics.forEach(pic => {
                    html += `
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 border-b border-gray-200">${pic.name ? pic.name : '-'}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 border-b border-gray-200">${pic.email ? pic.email : '-'}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 border-b border-gray-200">${pic.phone ? pic.phone : '-'}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 border-b border-gray-200">${pic.designation ? pic.designation : '-'}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm border-b border-gray-200">
                            <button class="edit-pic-btn bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs mr-1"
                                data-name="${pic.name}"
                                data-phone="${pic.phone}"
                                data-email="${pic.email}"
                                data-designation="${pic.designation}"
                                data-id="${pic.id}">Edit</button>
                            <button class="delete-pic-btn bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs"
                                data-id="${pic.id}">Delete</button>
                        </td>
                    </tr>
                `;
                });

                // Close the table tags
                html += `
                    </tbody>
                </table>
                <div class="flex justify-front mb-2 mt-2">
                    <button class="bg-blue-600 text-white px-1 ml-2 rounded add-pic-btn" data-company-id="${companyId}">
                        + Add PIC
                    </button>
                </div>
            `;

                return html;
            }



            // CREATE FUNCTION
            $('#clientCompanyAddForm').off('submit').on('submit', function(e) {
                e.preventDefault(); // Prevent the default form submission

                let pics = [];
                $('#picContainer .pic').each(function() {
                    // Use vanilla JS for better performance in loops
                    const picDiv = this;
                    const nameInput = picDiv.querySelector('input[name="pic_names[]"]');
                    const emailInput = picDiv.querySelector('input[name="pic_emails[]"]');
                    const phoneInput = picDiv.querySelector('input[name="pic_phones[]"]');
                    const designationInput = picDiv.querySelector(
                        'input[name="pic_designations[]"]');

                    pics.push({
                        name: nameInput ? nameInput.value : '',
                        email: emailInput ? emailInput.value : '',
                        phone: phoneInput ? phoneInput.value : '',
                        designation: designationInput ? designationInput.value : ''
                    });
                });

                $.ajax({
                    type: 'POST',
                    url: "{{ route('client-company.create') }}",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        name: $('#clientCompanyAddName').val(),
                        address: $('#clientCompanyAddAddress').val(),
                        companyPhone: $('#clientCompanyAddPhone').val(),
                        pics: pics
                    },
                    success: function(response) {
                        try {
                            // Close the modal
                            closeAltEditorModal("#clientCompanyAddModal");

                            // Show success message
                            if (typeof window.showSubmitToast === 'function') {
                                window.showSubmitToast("Successfully created.", "#91C714");
                            } else {
                                alert("Successfully created.");
                            }

                            // Reset the form
                            $('#clientCompanyAddForm')[0].reset();

                            // Also reset the PIC container to its initial state
                            const initialPic = document.getElementById('picContainer')
                                .firstElementChild;
                            document.getElementById('picContainer').innerHTML = '';
                            document.getElementById('picContainer').appendChild(initialPic);
                            // Ensure the remove button for the initial PIC is disabled again
                            const initialRemoveButton = initialPic.querySelector(
                                'button[onclick="removePIC(this)"]');
                            if (initialRemoveButton) {
                                initialRemoveButton.disabled = true;
                            }

                            // Refresh the DataTable
                            if ($.fn.DataTable.isDataTable('#client_company_table')) {
                                $('#client_company_table').DataTable().ajax.reload(null, false);
                            } else {
                                console.error('DataTable is not initialized');
                            }
                        } catch (error) {
                            console.error('Error in success callback:', error);
                            alert(
                                'Update successful, but there was an issue refreshing the table.');
                        }
                    },
                    error: function(xhr) {
                        try {
                            let errorMessage = "An error occurred while updating.";

                            if (xhr.responseJSON && xhr.responseJSON.error) {
                                errorMessage = "Error: " + xhr.responseJSON.error;
                            } else if (xhr.responseText) {
                                try {
                                    const response = JSON.parse(xhr.responseText);
                                    errorMessage = "Error: " + response.error;
                                } catch (e) {
                                    errorMessage = "Error: " + xhr.status + " " + xhr
                                    .statusText;
                                }
                            }

                            if (typeof window.showSubmitToast === 'function') {
                                window.showSubmitToast(errorMessage, "#D32929");
                            } else {
                                alert(errorMessage);
                            }
                        } catch (error) {
                            console.error('Error in error callback:', error);
                            alert('An error occurred while processing your request.');
                        }
                    }
                });
            });

            // EDIT FUNCTION
            $(document).off('click', '.client-company-edit').on('click', '.client-company-edit', function() {
                const companyId = $(this).data('id');
                originalCompanyId = companyId;

                const $row = $(this).closest('tr');
                const cells = $row.find('td');

                $('#clientCompanyEditName').val(cells.eq(2).text().trim());
                $('#clientCompanyEditAddress').val(cells.eq(3).text().trim());
                $('#clientCompanyEditPhone').val(cells.eq(4).text().trim());

                openAltEditorModal('#clientCompanyEditModal');
            });

            $('#clientCompanyEditForm').off('submit').on('submit', function(e) {
                e.preventDefault();

                // Disable the submit button to prevent multiple submissions
                $('#clientCompanyEditButton').prop('disabled', true).text('Updating...');

                $.ajax({
                    type: 'POST',
                    url: "{{ route('client-company.edit') }}",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        id: originalCompanyId,
                        name: $('#clientCompanyEditName').val().trim(),
                        address: $('#clientCompanyEditAddress').val().trim(),
                        companyPhone: $('#clientCompanyEditPhone').val().trim()
                    },
                    success: function(response) {
                        try {
                            // Close the modal
                            closeAltEditorModal('#clientCompanyEditModal');

                            // Show success message
                            if (typeof window.showSubmitToast === 'function') {
                                window.showSubmitToast("Successfully updated.", "#91C714");
                            } else {
                                alert("Successfully updated.");
                            }

                            // Reset the form
                            $('#clientCompanyEditForm')[0].reset();

                            // Refresh the DataTable
                            if ($.fn.DataTable.isDataTable('#client_company_table')) {
                                $('#client_company_table').DataTable().ajax.reload(null, false);
                            } else {
                                console.error('DataTable is not initialized');
                            }
                        } catch (error) {
                            console.error('Error in success callback:', error);
                            alert(
                                'Update successful, but there was an issue refreshing the table.');
                        }
                    },
                    error: function(xhr, status, error) {
                        try {
                            let errorMessage = "An error occurred while updating.";

                            if (xhr.responseJSON && xhr.responseJSON.error) {
                                errorMessage = "Error: " + xhr.responseJSON.error;
                            } else if (xhr.responseText) {
                                try {
                                    const response = JSON.parse(xhr.responseText);
                                    errorMessage = "Error: " + response.error;
                                } catch (e) {
                                    errorMessage = "Error: " + xhr.status + " " + xhr
                                    .statusText;
                                }
                            }

                            if (typeof window.showSubmitToast === 'function') {
                                window.showSubmitToast(errorMessage, "#D32929");
                            } else {
                                alert(errorMessage);
                            }
                        } catch (error) {
                            console.error('Error in error callback:', error);
                            alert('An error occurred while processing your request.');
                        }
                    },
                    complete: function() {
                        // Re-enable the submit button
                        $('#clientCompanyEditButton').prop('disabled', false).text('Update');
                    }
                });
            });



            // Store the ID of the last clicked moda when it's triggered
            (function() {
                $(document).on('click', "[data-toggle='modal']", function() {
                    lastClickedLink = $(this).attr('id');
                });
            })();

            // DELETE FUNCTION
            $(document).on('click', '.client-company-delete', function() {
                const companyId = $(this).data('id');

                // Store the company ID on the modal itself
                $('#clientCompanyDeleteModal').attr('data-company-id', companyId);

                // Open modal
                openAltEditorModal('#clientCompanyDeleteModal');
            });

            $('#clientCompanyDeleteButton').off('click').on('click', function() {
                const companyId = $('#clientCompanyDeleteModal').attr('data-company-id');

                $.ajax({
                    type: 'POST',
                    url: "{{ route('client-company.delete') }}",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        id: companyId
                    },
                    success: function(response) {
                        try {
                            // Close the modal
                            closeAltEditorModal('#clientCompanyDeleteModal');

                            // Show success message
                            if (typeof window.showSubmitToast === 'function') {
                                window.showSubmitToast("Successfully deleted.", "#91C714");
                            } else {
                                alert("Successfully deleted.");
                            }

                            // Refresh the DataTable
                            if ($.fn.DataTable.isDataTable('#client_company_table')) {
                                $('#client_company_table').DataTable().ajax.reload(null, false);
                            } else {
                                console.error('DataTable is not initialized');
                            }
                        } catch (error) {
                            console.error('Error in success callback:', error);
                            alert(
                                'Update successful, but there was an issue refreshing the table.');
                        }
                    },
                    error: function(xhr) {
                        try {
                            let errorMessage = "An error occurred while updating.";

                            if (xhr.responseJSON && xhr.responseJSON.error) {
                                errorMessage = "Error: " + xhr.responseJSON.error;
                            } else if (xhr.responseText) {
                                try {
                                    const response = JSON.parse(xhr.responseText);
                                    errorMessage = "Error: " + response.error;
                                } catch (e) {
                                    errorMessage = "Error: " + xhr.status + " " + xhr
                                    .statusText;
                                }
                            }

                            if (typeof window.showSubmitToast === 'function') {
                                window.showSubmitToast(errorMessage, "#D32929");
                            } else {
                                alert(errorMessage);
                            }
                        } catch (error) {
                            console.error('Error in error callback:', error);
                            alert('An error occurred while processing your request.');
                        }
                    }
                });
            });




















            // handle add PIC click button
            $(document).on('click', '.add-pic-btn', function() {
                const companyId = $(this).data('company-id');

                // Clear modal fields
                $('#picAddName').val('');
                $('#picAddContact').val('');
                $('#picAddEmail').val('');
                $('#picAddDesignation').val('');
                $('#picAddCompany').val(companyId);

                // Set company ID in hidden field
                $('#picAddCompanyId').val(companyId);

                openAltEditorModal('#picAddModal');
            });


            $('#picAddButton').off('click').on('click', function(e) {
                e.preventDefault();

                const name = $('#picAddName').val();
                const phone = $('#picAddContact').val();
                const email = $('#picAddEmail').val();
                const designation = $('#picAddDesignation').val();
                const companyId = $('#picAddCompanyId').val(); // now correctly set

                $.ajax({
                    url: '/client-company/pic/create', // create this route in Laravel
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        company_id: companyId,
                        name: name,
                        phone: phone,
                        email: email,
                        designation: designation
                    },
                    success: function(response) {
                        try {
                            // Close the modal
                            closeAltEditorModal('#picAddModal');

                            // Show success message
                            if (typeof window.showSubmitToast === 'function') {
                                window.showSubmitToast("Successfully created.", "#91C714");
                            } else {
                                alert("Successfully created.");
                            }

                            // Reset the form
                            $('#picAddForm')[0].reset();

                            // Refresh the DataTable
                            if ($.fn.DataTable.isDataTable('#client_company_table')) {
                                $('#client_company_table').DataTable().ajax.reload(null, false);
                            } else {
                                console.error('DataTable is not initialized');
                            }
                        } catch (error) {
                            console.error('Error in success callback:', error);
                            alert(
                                'Update successful, but there was an issue refreshing the table.');
                        }
                    },
                    error: function(xhr, status, error) {
                        try {
                            let errorMessage = "An error occurred while updating.";

                            if (xhr.responseJSON && xhr.responseJSON.error) {
                                errorMessage = "Error: " + xhr.responseJSON.error;
                            } else if (xhr.responseText) {
                                try {
                                    const response = JSON.parse(xhr.responseText);
                                    errorMessage = "Error: " + response.error;
                                } catch (e) {
                                    errorMessage = "Error: " + xhr.status + " " + xhr
                                    .statusText;
                                }
                            }

                            if (typeof window.showSubmitToast === 'function') {
                                window.showSubmitToast(errorMessage, "#D32929");
                            } else {
                                alert(errorMessage);
                            }
                        } catch (error) {
                            console.error('Error in error callback:', error);
                            alert('An error occurred while processing your request.');
                        }
                    },
                });
            });



            // Handle PIC Edit button click
            $('#client_company_table tbody').off('click', '.edit-pic-btn').on('click', '.edit-pic-btn', function() {
                const btn = $(this);
                const picId = btn.data('id');
                const name = btn.data('name');
                const phone = btn.data('phone');
                const email = btn.data('email');
                const designation = btn.data('designation');

                // Fill modal fields
                $('#picEditName').val(name);
                $('#picEditContact').val(phone);
                $('#picEditEmail').val(email);
                $('#picEditDesignation').val(designation);

                // Store PIC ID directly on modal element
                $('#picEditModal').attr('data-pic-id', picId);

                var element = "#picEditModal";
                openAltEditorModal(element);
            });

            $('#picEditButton').off('click').on('click', function(e) {
                e.preventDefault();
                const picId = $('#picEditModal').attr('data-pic-id');
                const name = $('#picEditName').val();
                const phone = $('#picEditContact').val();
                const email = $('#picEditEmail').val();
                const designation = $('#picEditDesignation').val();

                $.ajax({
                    url: '/client-company/pic/update', // create this route in Laravel
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        id: picId,
                        name: name,
                        phone: phone,
                        email: email,
                        designation: designation
                    },
                    success: function(response) {
                        try {
                            // Close the modal
                            closeAltEditorModal('#picEditModal');

                            // Show success message
                            if (typeof window.showSubmitToast === 'function') {
                                window.showSubmitToast("Successfully updated.", "#91C714");
                            } else {
                                alert("Successfully updated.");
                            }

                            // Reset the form
                            $('#picEditForm')[0].reset();

                            // Refresh the DataTable
                            if ($.fn.DataTable.isDataTable('#client_company_table')) {
                                $('#client_company_table').DataTable().ajax.reload(null, false);
                            } else {
                                console.error('DataTable is not initialized');
                            }
                        } catch (error) {
                            console.error('Error in success callback:', error);
                            alert(
                                'Update successful, but there was an issue refreshing the table.');
                        }
                    },
                    error: function(xhr, status, error) {
                        try {
                            let errorMessage = "An error occurred while updating.";

                            if (xhr.responseJSON && xhr.responseJSON.error) {
                                errorMessage = "Error: " + xhr.responseJSON.error;
                            } else if (xhr.responseText) {
                                try {
                                    const response = JSON.parse(xhr.responseText);
                                    errorMessage = "Error: " + response.error;
                                } catch (e) {
                                    errorMessage = "Error: " + xhr.status + " " + xhr
                                    .statusText;
                                }
                            }

                            if (typeof window.showSubmitToast === 'function') {
                                window.showSubmitToast(errorMessage, "#D32929");
                            } else {
                                alert(errorMessage);
                            }
                        } catch (error) {
                            console.error('Error in error callback:', error);
                            alert('An error occurred while processing your request.');
                        }
                    },
                });
            });

            // Handle PIC Delete button click
            let lastClickedPicId = null;

            $('#client_company_table tbody').off('click', '.delete-pic-btn').on('click', '.delete-pic-btn',
                function() {
                    lastClickedPicId = $(this).data('id');

                    // Show delete confirmation modal
                    var element = "#picDeleteModal";
                    openAltEditorModal(element);
                });

            $('#picDeleteButton').off('click').on('click', function() {
                if (!lastClickedPicId) return;

                $.ajax({
                    type: 'POST',
                    url: '/client-company/pic/delete', // create this route in Laravel
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        id: lastClickedPicId,
                    },
                    success: function(response) {
                        try {
                            // Close the modal
                            closeAltEditorModal('#picDeleteModal');

                            // Show success message
                            if (typeof window.showSubmitToast === 'function') {
                                window.showSubmitToast("Successfully deleted.", "#91C714");
                            } else {
                                alert("Successfully deleted.");
                            }

                            // Refresh the DataTable
                            if ($.fn.DataTable.isDataTable('#client_company_table')) {
                                $('#client_company_table').DataTable().ajax.reload(null, false);
                            } else {
                                console.error('DataTable is not initialized');
                            }
                        } catch (error) {
                            console.error('Error in success callback:', error);
                            alert(
                                'Update successful, but there was an issue refreshing the table.');
                        }
                    },
                    error: function(xhr, status, error) {
                        try {
                            let errorMessage = "An error occurred while updating.";

                            if (xhr.responseJSON && xhr.responseJSON.error) {
                                errorMessage = "Error: " + xhr.responseJSON.error;
                            } else if (xhr.responseText) {
                                try {
                                    const response = JSON.parse(xhr.responseText);
                                    errorMessage = "Error: " + response.error;
                                } catch (e) {
                                    errorMessage = "Error: " + xhr.status + " " + xhr
                                    .statusText;
                                }
                            }

                            if (typeof window.showSubmitToast === 'function') {
                                window.showSubmitToast(errorMessage, "#D32929");
                            } else {
                                alert(errorMessage);
                            }
                        } catch (error) {
                            console.error('Error in error callback:', error);
                            alert('An error occurred while processing your request.');
                        }
                    },
                });
            });




            initClientCompanyDatatable();
        });
    </script>
@endsection
