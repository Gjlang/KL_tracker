@extends('layouts.app')

@section('head')
    <title>BGOC Outdoor System - Contractors</title>
    <style>
        /* Typography */
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Inter:wght@400;500;600&display=swap');

        body {
            background-color: #F7F7F9;
            font-family: 'Inter', sans-serif;
        }

        .font-serif {
            font-family: 'Playfair Display', serif;
        }

        /* Color System */
        .ink {
            color: #1C1E26;
        }

        .hairline {
            border-color: #EAEAEA;
        }

        /* Typography Utilities */
        .tabular-nums {
            font-variant-numeric: tabular-nums;
        }

        .header-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #6B7280;
            font-weight: 600;
        }

        /* Button System */
        .btn-primary {
            background-color: #22255b;
            color: white;
            transition: all 150ms ease;
        }

        .btn-primary:hover {
            opacity: 0.9;
        }

        .btn-secondary {
            border: 1px solid #EAEAEA;
            color: #1C1E26;
            background: white;
            transition: all 150ms ease;
        }

        .btn-secondary:hover {
            background-color: rgba(75, 187, 237, 0.1);
        }

        .btn-destructive {
            background-color: #d33831;
            color: white;
            transition: all 150ms ease;
        }

        .btn-destructive:hover {
            opacity: 0.9;
        }

        .btn-ghost {
            border: 1px solid #D1D5DB;
            color: #4B5563;
            background: transparent;
            transition: all 150ms ease;
        }

        .btn-ghost:hover {
            background-color: #F9FAFB;
        }

        /* Card System */
        .card-elegant {
            background: white;
            border-radius: 1rem;
            border: 1px solid rgba(234, 234, 234, 0.7);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
        }

        .card-floating {
            background: white;
            border-radius: 1.5rem;
            border: 1px solid rgba(234, 234, 234, 0.7);
            box-shadow: 0 2px 6px 0 rgba(0, 0, 0, 0.04);
        }

        /* Filter Chips */
        .filter-chip {
            display: inline-flex;
            align-items: center;
            padding: 0.375rem 0.75rem;
            background: rgba(34, 37, 91, 0.05);
            border-radius: 9999px;
            font-size: 0.75rem;
            color: #22255b;
            letter-spacing: 0.02em;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
        }

        /* Input System */
        .input-elegant {
            height: 2.75rem;
            border-radius: 0.75rem;
            border: 1px solid #D1D5DB;
            transition: all 150ms ease;
        }

        .input-elegant:focus {
            outline: none;
            border-color: transparent;
            box-shadow: 0 0 0 2px #4bbbed;
        }

        /* Table Ledger Style */
        .ledger-row {
            border-bottom: 1px solid #EAEAEA;
            transition: all 200ms ease;
        }

        .ledger-row:hover {
            background-color: #FAFAFA;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            transform: translateY(-1px);
        }

        /* Quick Navigation Cards */
        .quick-nav-card {
            background: white;
            border: 1px solid #EAEAEA;
            border-radius: 0.75rem;
            padding: 1.5rem;
            transition: all 200ms ease;
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }

        .quick-nav-card::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: #22255b;
            transform: scaleY(0);
            transition: transform 200ms ease;
        }

        .quick-nav-card:hover::before {
            transform: scaleY(1);
        }

        .quick-nav-card:hover {
            border-color: #22255b;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #6B7280;
        }

        /* Modal System */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .modal.show {
            display: flex !important;
        }

        .modal__content {
            background: white;
            border-radius: 1rem;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        /* DataTables Custom Styling */
        .dataTables_wrapper {
            color: #1C1E26;
        }

        .dataTables_info {
            font-size: 0.875rem;
            color: #6B7280;
        }

        .dataTables_paginate {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .dataTables_paginate .paginate_button {
            padding: 0.5rem 0.75rem !important;
            margin: 0 !important;
            border-radius: 0.5rem !important;
            border: 1px solid transparent !important;
            transition: all 150ms ease !important;
            background: transparent !important;
            color: #6B7280 !important;
            min-width: 2.5rem;
            text-align: center;
            display: inline-block;
        }

        .dataTables_paginate .paginate_button:hover {
            background: #F3F4F6 !important;
            border-color: #D1D5DB !important;
            color: #1C1E26 !important;
        }

        .dataTables_paginate .paginate_button.current {
            background: #22255b !important;
            color: white !important;
            border-color: #22255b !important;
        }

        .dataTables_paginate .paginate_button.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            pointer-events: none;
        }

        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            margin-top: 1rem;
        }

        .dataTables_wrapper .dataTables_length {
            float: left;
        }

        .dataTables_wrapper .dataTables_filter {
            float: right;
        }

        .dataTables_wrapper .dataTables_info {
            clear: both;
            float: left;
            padding-top: 0.75rem;
        }

        .dataTables_wrapper .dataTables_paginate {
            float: right;
            padding-top: 0.75rem;
        }

        /* Truncate long text with tooltip */
        .truncate-cell {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>
@endsection

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Top Bar -->
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <!-- Left: Title -->
                <div>
                    <h1 class="font-serif text-4xl font-bold ink">Contractors</h1>
                    <p class="text-sm text-gray-500 mt-1" id="recordsSubtitle">Managing contractor relationships</p>
                </div>

                <!-- Right: Actions -->
                <div class="flex flex-wrap items-center gap-2">
                    <a id="openAddContractor" href="javascript:;" data-toggle="modal" data-target="#contractorAddModal"
                        class="btn-primary inline-flex items-center gap-2 px-5 py-2.5 rounded-full font-medium text-sm focus:ring-2 focus:ring-[#4bbbed] focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Add New Contractor
                    </a>
                </div>
            </div>

            <!-- Active Filters Chips -->
            <div id="activeFiltersChips" class="mt-4 hidden">
                <div class="text-xs text-gray-500 mb-2">Active Filters:</div>
                <div id="filterChipsContainer"></div>
            </div>
        </div>

        <!-- Filter Panel -->
        <div class="card-floating p-6 mb-6">
            <form class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Company Filter -->
                <div>
                    <label class="header-label block mb-2">Company</label>
                    <select class="input-elegant w-full px-3" id="inputCompanyName">
                        <option selected value="">All Companies</option>
                        @foreach ($clientcompany as $clientcomp)
                            <option value="{{ $clientcomp->id }}">{{ $clientcomp->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Apply Button -->
                <div class="sm:col-span-2 lg:col-span-4 flex justify-end">
                    <button type="button"
                        class="btn-primary px-6 py-2.5 rounded-full font-medium text-sm focus:ring-2 focus:ring-[#4bbbed] focus:outline-none"
                        id="filterClientButton">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <!-- Data Table -->
        <div class="card-elegant p-6 mb-8">
            <div class="overflow-x-auto">
                <table class="table w-full" id="contractor_table">
                    <thead>
                        <tr class="border-b-2 hairline">
                            <th class="header-label text-left py-4 px-4" width="5%">No.</th>
                            <th class="header-label text-left py-4 px-4">Company Name</th>
                            <th class="header-label text-left py-4 px-4">PIC Name</th>
                            <th class="header-label text-left py-4 px-4">Phone No.</th>
                            <th class="header-label text-center py-4 px-4" width="10%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>



    </div>

    <!-- BEGIN: Contractor Add Modal -->
    <div class="modal hidden" id="contractorAddModal">
        <div class="modal__content">
            <div class="flex items-center px-6 py-5 border-b hairline">
                <h2 class="font-serif text-2xl font-semibold ink">Add Contractor</h2>
            </div>
            <form>
                @csrf
                <div class="p-6 space-y-5">
                    <div>
                        <label class="header-label block mb-2">Company</label>
                        <input type="text" placeholder="Enter a Company Name" class="input-elegant w-full px-3"
                            id="contractorAddCompanyName" required>
                    </div>
                    <div>
                        <label class="header-label block mb-2">Contractor PIC Name</label>
                        <input type="text" placeholder="Enter a PIC Name" class="input-elegant w-full px-3"
                            id="contractorAddPICName" required>
                    </div>
                    <div>
                        <label class="header-label block mb-2">Phone No.</label>
                        <input type="text" title="Please enter Phone number in correct format"
                            placeholder="Enter a Phone Number" class="input-elegant w-full px-3" id="contractorAddPhone"
                            required>
                    </div>
                </div>
                <div class="px-6 py-4 flex justify-end gap-3 border-t hairline">
                    <button type="button" data-dismiss="modal"
                        class="btn-secondary px-6 py-2.5 rounded-full font-medium text-sm">Cancel</button>
                    <button type="submit"
                        class="btn-primary px-6 py-2.5 rounded-full font-medium text-sm focus:ring-2 focus:ring-[#4bbbed] focus:outline-none"
                        id="contractorAddButton">Save</button>
                </div>
            </form>
        </div>
    </div>
    <!-- END: Contractor Add Modal -->

    <!-- BEGIN: Contractor Edit Modal -->
    <div class="modal hidden" id="contractorEditModal">
        <div class="modal__content">
            <div class="flex items-center px-6 py-5 border-b hairline">
                <h2 class="font-serif text-2xl font-semibold ink">Edit Contractor</h2>
            </div>
            <form>
                <div class="p-6 space-y-5">
                    <div>
                        <label class="header-label block mb-2">Company Name</label>
                        <input type="text" class="input-elegant w-full px-3" placeholder="Company Name"
                            id="contractorEditCompanyName" required>
                    </div>
                    <div>
                        <label class="header-label block mb-2">Contractor PIC Name</label>
                        <input type="text" class="input-elegant w-full px-3" placeholder="Contractor PIC Name"
                            id="contractorEditPICName" required>
                    </div>
                    <div>
                        <label class="header-label block mb-2">Phone No.</label>
                        <input type="text" class="input-elegant w-full px-3" placeholder="Phone No"
                            id="contractorEditPhone" required>
                    </div>
                </div>
                <div class="px-6 py-4 flex justify-end gap-3 border-t hairline">
                    <button type="button" data-dismiss="modal"
                        class="btn-secondary px-6 py-2.5 rounded-full font-medium text-sm">Cancel</button>
                    <button type="submit"
                        class="btn-primary px-6 py-2.5 rounded-full font-medium text-sm focus:ring-2 focus:ring-[#4bbbed] focus:outline-none"
                        id="contractorEditButton">Update</button>
                </div>
            </form>
        </div>
    </div>
    <!-- END: Contractor Edit Modal -->

    <!-- BEGIN: Contractor Delete Modal -->
    <div class="modal hidden" id="contractorDeleteModal">
        <div class="modal__content">
            <div class="p-8 text-center">
                <div class="w-16 h-16 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-[#d33831]" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="font-serif text-2xl font-semibold ink mb-2">Are you sure?</h3>
                <p class="text-gray-600">Confirm deleting the contractor? This process cannot be undone.</p>
            </div>
            <div class="px-6 pb-6 flex justify-center gap-3">
                <button type="button" data-dismiss="modal"
                    class="btn-secondary px-6 py-2.5 rounded-full font-medium text-sm">Cancel</button>
                <button type="button"
                    class="btn-destructive px-6 py-2.5 rounded-full font-medium text-sm focus:ring-2 focus:ring-red-300 focus:outline-none"
                    id="contractorDeleteButton">Delete</button>
            </div>
        </div>
    </div>
    <!-- END: Contractor Delete Modal -->
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {

            // Global variables
            var filterClientCompany;
            var contractorID;
            var lastClickedLink;

            // Define modal functions if they don't exist in layout
            if (typeof openModal === 'undefined') {
                window.openModal = function(id) {
                    const modal = document.getElementById(id);
                    if (modal) {
                        modal.classList.remove('hidden');
                        modal.classList.add('show');
                        modal.style.display = 'flex';
                    }
                };
            }

            if (typeof closeModal === 'undefined') {
                window.closeModal = function(id) {
                    const modal = document.getElementById(id);
                    if (modal) {
                        modal.classList.add('hidden');
                        modal.classList.remove('show');
                        modal.style.display = 'none';
                    }
                };
            }

            // Modal helper functions
            function openAltEditorModal(selector) {
                const id = selector.startsWith('#') ? selector.slice(1) : selector;
                openModal(id);
            }

            function closeAltEditorModal(selector) {
                const id = selector.startsWith('#') ? selector.slice(1) : selector;
                closeModal(id);
            }

            // Listen to buttons with safety guards
            const filterBtn = document.getElementById("filterClientButton");
            if (filterBtn) filterBtn.addEventListener("click", filterClientButton);

            const addBtn = document.getElementById("contractorAddButton");
            if (addBtn) addBtn.addEventListener("click", contractorAddButton);

            const delBtn = document.getElementById("contractorDeleteButton");
            if (delBtn) delBtn.addEventListener("click", contractorDeleteButton);

            // Direct listener for opening Add Contractor modal
            const openAddBtn = document.getElementById('openAddContractor');
            if (openAddBtn) {
                openAddBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    openAltEditorModal('#contractorAddModal');
                });
            }

            // When "filterClientButton" button is clicked, initiate initClientCompanyDatatable
            function filterClientButton() {
                filterClientCompany = document.getElementById("inputCompanyName").value;
                initContractorDatatable(filterClientCompany);
                updateFilterChips();
            };

            // Update filter chips display
            function updateFilterChips() {
                const companySelect = document.getElementById("inputCompanyName");
                const chipContainer = document.getElementById("filterChipsContainer");
                const chipsSection = document.getElementById("activeFiltersChips");

                let chips = [];

                if (companySelect.value) {
                    const selectedText = companySelect.options[companySelect.selectedIndex].text;
                    chips.push(`COMPANY: ${selectedText}`);
                }

                if (chips.length > 0) {
                    chipContainer.innerHTML = chips.map(chip =>
                        `<span class="filter-chip">${chip}</span>`
                    ).join('');
                    chipsSection.classList.remove('hidden');
                } else {
                    chipsSection.classList.add('hidden');
                }
            }

            // When page first loads, load table
            filterClientButton();

            // When any submit button is clicked
            (function() {
                const addBtn2 = document.getElementById('contractorAddButton');
                if (addBtn2) {
                    addBtn2.addEventListener('click', function(e) {
                        e.preventDefault();
                    });
                }

                const editBtn = document.getElementById('contractorEditButton');
                if (editBtn) {
                    editBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        editContractor();
                    });
                }
            })();

            // Add New Contractor
            function contractorAddButton() {
                $.ajax({
                    type: 'POST',
                    url: "{{ route('contractors.create') }}",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        company: document.getElementById("contractorAddCompanyName").value,
                        name: document.getElementById("contractorAddPICName").value,
                        phone: document.getElementById("contractorAddPhone").value,
                    },
                    success: function(response) {
                        var element = "#contractorAddModal";
                        closeAltEditorModal(element);
                        window.showSubmitToast("Successfully added.", "#91C714");
                        document.getElementById("contractorAddCompanyName").value = "";
                        document.getElementById("contractorAddPICName").value = "";
                        document.getElementById("contractorAddPhone").value = "";
                        $('#contractor_table').DataTable().ajax.reload();
                    },
                    error: function(xhr, status, error) {
                        var response = JSON.parse(xhr.responseText);
                        var error = "Error: " + response.error;
                        window.showSubmitToast(error, "#D32929");
                    }
                });
            }

            // Edit Contractor
            function editContractor() {
                var company = document.getElementById("contractorEditCompanyName").value;
                var name = document.getElementById("contractorEditPICName").value;
                var phone = document.getElementById("contractorEditPhone").value;

                $.ajax({
                    type: 'POST',
                    url: "{{ route('contractors.edit') }}",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        company: company,
                        name: name,
                        phone: phone,
                        id: contractorID,
                    },
                    success: function(response) {
                        var element = "#contractorEditModal";
                        closeAltEditorModal(element);
                        window.showSubmitToast("Successfully updated.", "#91C714");
                        document.getElementById("contractorEditCompanyName").value = "";
                        document.getElementById("contractorEditPICName").value = "";
                        document.getElementById("contractorEditPhone").value = "";
                        $('#contractor_table').DataTable().ajax.reload();
                    },
                    error: function(xhr, status, error) {
                        var response = JSON.parse(xhr.responseText);
                        var error = "Error: " + response.error;
                        window.showSubmitToast(error, "#D32929");
                    }
                });
            }

            // Setup the contractors datatable
            function initContractorDatatable() {
                const dt = new Date();
                const formattedDate =
                    `${dt.getFullYear()}${(dt.getMonth() + 1).toString().padStart(2, '0')}${dt.getDate().toString().padStart(2, '0')}`;
                const formattedTime = `${dt.getHours()}:${dt.getMinutes()}:${dt.getSeconds()}`;
                const $fileName = `Contractor_List_${formattedDate}_${formattedTime}`;

                const table = $('#contractor_table').DataTable({
                    altEditor: true,
                    destroy: true,
                    debug: true,
                    processing: true,
                    searching: true,
                    serverSide: true,
                    ordering: true,
                    order: [
                        [0, 'desc']
                    ],
                    pagingType: 'full_numbers',
                    pageLength: 25,
                    aLengthMenu: [
                        [25, 50, 75, -1],
                        [25, 50, 75, "All"]
                    ],
                    iDisplayLength: 25,
                    ajax: {
                        url: "{{ route('contractors.list') }}",
                        dataType: "json",
                        type: "POST",
                        data: function(d) {
                            d._token = $('meta[name="csrf-token"]').attr('content');
                            d.company = filterClientCompany;
                            return d;
                        },
                        dataSrc: function(json) {
                            json.recordsTotal = json.recordsTotal;
                            json.recordsFiltered = json.recordsFiltered;

                            const subtitle = document.getElementById('recordsSubtitle');
                            if (subtitle) {
                                subtitle.textContent =
                                    `Showing ${json.recordsFiltered} of ${json.recordsTotal} records`;
                            }

                            return json.data;
                        }
                    },
                    dom: "lBfrtip",
                    buttons: [{
                            extend: "csv",
                            className: "btn-ghost px-4 py-2 rounded-full text-sm font-medium",
                            title: $fileName,
                            exportOptions: {
                                columns: ":not(.dt-exclude-export)"
                            },
                            init: function(api, node, config) {
                                $(node).removeClass('dt-button buttons-html5');
                            },
                        },
                        {
                            extend: "excel",
                            className: "btn-ghost px-4 py-2 rounded-full text-sm font-medium",
                            title: $fileName,
                            exportOptions: {
                                columns: ":not(.dt-exclude-export)"
                            },
                            init: function(api, node, config) {
                                $(node).removeClass('dt-button buttons-html5');
                            },
                        },
                        {
                            extend: "print",
                            className: "btn-ghost px-4 py-2 rounded-full text-sm font-medium",
                            title: $fileName,
                            exportOptions: {
                                stripHtml: false,
                            },
                            init: function(api, node, config) {
                                $(node).removeClass('dt-button buttons-html5');
                            },
                        },
                    ],
                    columns: [{
                            data: null,
                            name: 'no',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row, meta) {
                                return meta.row + meta.settings._iDisplayStart + 1;
                            }
                        },
                        {
                            data: "company_name",
                            render: function(data) {
                                return `<span class="truncate-cell" title="${data}">${data}</span>`;
                            }
                        },
                        {
                            data: "name",
                        },
                        {
                            data: "phone",
                            render: function(data) {
                                return `<span class="tabular-nums">${data}</span>`;
                            }
                        },
                        {
                            data: "id",
                            className: "text-center dt-exclude-export",
                            render: function(data, type, row) {
                                return `
                            <div class="flex items-center justify-center gap-3">
                                <a href="javascript:;" class="text-[#4bbbed] hover:text-[#22255b] transition-colors" data-toggle="modal" data-target="#contractorEditModal" id="edit-${data}" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <a href="javascript:;" class="text-[#d33831] hover:text-red-700 transition-colors" data-toggle="modal" data-target="#contractorDeleteModal" id="delete-${data}" title="Delete">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </a>
                            </div>`;
                            }
                        },
                    ],
                    drawCallback: function() {
                        $('#contractor_table tbody tr').addClass('ledger-row');
                    }
                });

                // Style DataTable elements
                var dtButtonsDiv = document.querySelector(".dt-buttons");
                if (dtButtonsDiv) {
                    dtButtonsDiv.classList.add("mb-4", "flex", "flex-wrap", "gap-2");
                }

                var filterDiv = document.getElementById("contractor_table_filter");
                if (filterDiv) {
                    filterDiv.style.float = "right";
                    filterDiv.classList.remove('dataTables_filter');
                    var inputElement = filterDiv.querySelector("label input");
                    if (inputElement) {
                        inputElement.className = "input-elegant px-3";
                        inputElement.placeholder = "Search contractors...";
                    }
                }

                // Style info and pagination
                setTimeout(function() {
                    var infoDiv = document.getElementById("contractor_table_info");
                    var paginateDiv = document.getElementById("contractor_table_paginate");

                    if (infoDiv) {
                        infoDiv.style.cssText = "float: left; margin-top: 1rem;";
                        infoDiv.classList.add("text-sm", "text-gray-600");
                    }

                    if (paginateDiv) {
                        paginateDiv.style.cssText =
                            "float: right; margin-top: 1rem; display: flex; gap: 0.25rem;";
                    }
                }, 100);

                var existingDiv = document.getElementById("contractor_table_length");
                if (existingDiv) {
                    existingDiv.classList.remove('dataTables_length');
                    existingDiv.classList.add('mb-4');
                    var existingSelect = existingDiv.querySelector('select');
                    if (existingSelect) {
                        existingSelect.className = 'input-elegant px-3';
                    }
                }

                // Open modal to edit contractor
                contractorEditModal();
            };

            // Open modal to edit contractor
            function contractorEditModal() {
                $(document).off('click', "[id^='contractor_table'] tbody tr td:not(:last-child)");

                $(document).on('click', "[id^='contractor_table'] tbody tr td:not(:last-child)", function() {
                    document.getElementById("contractorEditCompanyName").value = $(event.target).closest(
                        'tr').find('td:nth-child(' + '2' + ')').text();
                    document.getElementById("contractorEditPICName").value = $(event.target).closest('tr')
                        .find('td:nth-child(' + '3' + ')').text();
                    document.getElementById("contractorEditPhone").value = $(event.target).closest('tr')
                        .find('td:nth-child(' + '4' + ')').text().trim();

                    contractorID = $(event.target).closest('tr').find('td:nth-child(5) a').attr('id').split(
                        '-')[1];

                    var element = "#contractorEditModal";
                    openAltEditorModal(element);
                });
            }

            // Store the ID of the last clicked modal when it's triggered
            (function() {
                $(document).on('click', "[data-toggle='modal']", function() {
                    lastClickedLink = $(this).attr('id');
                });
            })();

            // Handle modal triggers
            $(document).on('click', '[data-toggle="modal"]', function() {
                const target = $(this).attr('data-target');
                if (target) openAltEditorModal(target);
            });

            // Handle modal dismiss
            $(document).on('click', '[data-dismiss="modal"]', function() {
                const $modal = $(this).closest('.modal');
                if ($modal.length) closeAltEditorModal('#' + $modal.attr('id'));
            });

            // Close modal when clicking outside
            $(document).on('click', '.modal', function(e) {
                if ($(e.target).hasClass('modal')) {
                    closeAltEditorModal('#' + $(this).attr('id'));
                }
            });

            // Delete Contractor
            function contractorDeleteButton() {
                var id = lastClickedLink.split("-")[1];

                $.ajax({
                    type: 'POST',
                    url: "{{ route('contractors.delete') }}",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        id: id,
                    },
                    success: function(response) {
                        var element = "#contractorDeleteModal";
                        closeAltEditorModal(element);
                        window.showSubmitToast("Successfully deleted.", "#91C714");
                        $('#contractor_table').DataTable().ajax.reload();
                    },
                    error: function(xhr, status, error) {
                        var response = JSON.parse(xhr.responseText);
                        var error = "Error: " + response.error;
                        window.showSubmitToast(error, "#D32929");
                    }
                });
            }
        })
    </script>
@endsection
