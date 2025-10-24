@extends('layouts.app')

@section('head')
    <title>Users</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;500;600&family=Inter:wght@300;400;500;600&display=swap"
        rel="stylesheet">
    <style>
        .modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .35);
            z-index: 9998;
            align-items: center;
            justify-content: center;
        }

        .modal.show,
        .modal.is-open {
            display: flex;
        }

        .modal__content {
            background: #fff;
            border-radius: 12px;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .12);
            z-index: 9999;
        }

        :root {
            --paper: #F7F7F9;
            --surface: #FFFFFF;
            --ink: #1C1E26;
            --hairline: #EAEAEA;
            --brand-dark: #22255b;
            --brand-light: #4bbbed;
            --destructive: #d33831;
        }

        body {
            background-color: var(--paper);
            color: var(--ink);
            font-family: 'Inter', sans-serif;
        }

        .serif {
            font-family: 'EB Garamond', serif;
        }

        .hairline {
            border-width: 0.5px;
            border-color: var(--hairline);
        }

        .ink {
            color: var(--ink);
        }

        .small-caps {
            font-variant: small-caps;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            font-size: 11px;
        }

        .tabular-nums {
            font-variant-numeric: tabular-nums;
        }

        .elegant-card {
            background: var(--surface);
            border-radius: 1rem;
            border: 0.5px solid rgba(234, 234, 234, 0.7);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.02), 0 1px 2px 0 rgba(0, 0, 0, 0.03);
        }

        .elegant-btn-primary {
            background: var(--brand-dark);
            color: white;
            border-radius: 9999px;
            padding: 0.625rem 1.5rem;
            font-size: 0.9375rem;
            font-weight: 500;
            transition: all 150ms ease;
            border: none;
        }

        .elegant-btn-primary:hover {
            opacity: 0.9;
            transform: translateY(-0.5px);
        }

        .elegant-btn-primary:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(75, 187, 237, 0.3);
        }

        .elegant-btn-destructive {
            background: var(--destructive);
            color: white;
            border-radius: 9999px;
            padding: 0.625rem 1.5rem;
            font-size: 0.9375rem;
            font-weight: 500;
            transition: all 150ms ease;
            border: none;
        }

        .elegant-btn-destructive:hover {
            opacity: 0.9;
            transform: translateY(-0.5px);
        }

        .elegant-btn-ghost {
            background: transparent;
            color: #4b5563;
            border: 1px solid #d1d5db;
            border-radius: 0.75rem;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 150ms ease;
        }

        .elegant-btn-ghost:hover {
            background: #f9fafb;
        }

        .elegant-input {
            height: 2.75rem;
            border-radius: 0.75rem;
            border: 1px solid #d1d5db;
            padding: 0 1rem;
            font-size: 0.9375rem;
            transition: all 150ms ease;
        }

        .elegant-input:focus {
            outline: none;
            border-color: transparent;
            box-shadow: 0 0 0 2px var(--brand-light);
        }

        .filter-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: #f3f4f6;
            border: 0.5px solid #e5e7eb;
            border-radius: 9999px;
            padding: 0.375rem 0.875rem;
            font-size: 0.8125rem;
            font-weight: 500;
            color: var(--ink);
        }

        .table-ledger {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-ledger thead th {
            background: var(--surface);
            padding: 1rem 1.25rem;
            text-align: left;
            font-weight: 600;
            color: #6b7280;
            border-bottom: 0.5px solid var(--hairline);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .table-ledger tbody tr {
            background: var(--surface);
            transition: all 150ms ease;
        }

        .table-ledger tbody tr:hover {
            background: #fafafa;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
            transform: translateY(-1px);
        }

        .table-ledger tbody td {
            padding: 1rem 1.25rem;
            border-bottom: 0.5px solid var(--hairline);
            color: var(--ink);
        }

        .table-ledger tbody tr:last-child td {
            border-bottom: none;
        }

        .quick-nav-card {
            background: var(--surface);
            border: 0.5px solid var(--hairline);
            border-radius: 1rem;
            padding: 1.5rem;
            transition: all 200ms ease;
            position: relative;
            overflow: hidden;
        }

        .quick-nav-card::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: var(--brand-dark);
            transform: scaleY(0);
            transition: transform 200ms ease;
        }

        .quick-nav-card:hover::before {
            transform: scaleY(1);
        }

        .quick-nav-card:hover {
            border-color: var(--brand-dark);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .truncate-cell {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* DataTables custom styling */
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 0.5rem !important;
            margin: 0 0.25rem;
            padding: 0.5rem 0.75rem !important;
            border: 1px solid #e5e7eb !important;
            background: white !important;
            color: var(--ink) !important;
            transition: all 150ms ease;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #f3f4f6 !important;
            border-color: var(--brand-dark) !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--brand-dark) !important;
            color: white !important;
            border-color: var(--brand-dark) !important;
        }
    </style>
@endsection

@section('content')
    <!-- Top Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mt-8 mb-6">
        <div>
            <h1 class="serif text-3xl font-semibold ink mb-1">Users</h1>
            <p class="text-sm text-gray-500" id="userCountSubtitle">Managing user accounts</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="javascript:;" data-toggle="modal" data-target="#usersAddModal" id="usersAddModalButton"
                class="elegant-btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="inline-block mr-2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Register New User
            </a>
        </div>
    </div>

    <!-- Info Panel -->
    <div class="elegant-card p-6 mb-6">
        <div class="flex items-start gap-4">
            <div class="mt-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                    class="text-gray-400">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-base mb-2 ink">User Status Information</h3>
                <div class="text-sm text-gray-600 space-y-1">
                    <p><strong class="font-medium">Active</strong> — Users can access the system</p>
                    <p><strong class="font-medium">Disabled</strong> — Users cannot sign in</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="elegant-card p-6">

        <!-- Filter Panel -->
        <div class="mb-6 pb-6 border-b hairline">
            <form class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="small-caps text-gray-600 mb-2 block">Search</label>
                    <input type="text" class="elegant-input w-full" placeholder="Name, username, email..."
                        id="filterSearch">
                </div>
                <div>
                    <label class="small-caps text-gray-600 mb-2 block">Role</label>
                    <select class="elegant-input w-full" id="filterRole">
                        <option value="">All Roles</option>
                        <option value="superadmin">Superadmin</option>
                        <option value="admin">Admin</option>
                        <option value="support">Support</option>
                        <option value="sales">Sales</option>
                        <option value="services">Services</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="button" class="elegant-btn-primary w-full md:w-auto px-8" id="applyFiltersBtn">
                        Apply Filters
                    </button>
                </div>
            </form>

            <!-- Active Filter Chips -->
            <div id="activeFilters" class="mt-4 flex flex-wrap gap-2 hidden">
                <!-- Chips will be inserted here dynamically -->
            </div>
        </div>

        <!-- Users Table -->
        <div class="overflow-x-auto">
            <table class="table-ledger" id="users_table">
                <thead>
                    <tr>
                        <th class="small-caps">No.</th>
                        <th class="small-caps">Name</th>
                        <th class="small-caps">Username</th>
                        <th class="small-caps">Role</th>
                        <th class="small-caps tabular-nums">Email</th>
                        <th class="small-caps dt-no-sort dt-exclude-export">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data populated by DataTables -->
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('modals')
    <!-- BEGIN: Users Edit Modal -->
    <div class="modal" id="usersEditModal">
        <div class="modal__content" style="max-width: 500px;">
            <div class="flex items-center px-6 py-5 border-b hairline">
                <h2 class="serif text-xl font-semibold ink">Edit User</h2>
            </div>
            <form>
                <div class="p-6 space-y-5">
                    <div>
                        <label class="small-caps text-gray-600 mb-2 block">Name</label>
                        <input type="text" class="elegant-input w-full" placeholder="Name" id="usersEditName"
                            required>
                    </div>
                    <div>
                        <label class="small-caps text-gray-600 mb-2 block">Username</label>
                        <input type="text" class="elegant-input w-full" placeholder="System Login Username"
                            id="usersEditUsername" required>
                    </div>
                    <div>
                        <label class="small-caps text-gray-600 mb-2 block">Role</label>
                        <select class="elegant-input w-full" id="usersEditRole" required>
                            <option value="superadmin">Superadmin</option>
                            <option value="admin">Admin</option>
                            <option value="support">Support</option>
                            <option value="sales">Sales</option>
                            <option value="services">Services</option>
                        </select>
                    </div>
                    <div>
                        <label class="small-caps text-gray-600 mb-2 block">Email</label>
                        <input type="email" class="elegant-input w-full" placeholder="example@gmail.com"
                            id="usersEditEmail" required>
                    </div>
                </div>

                <div class="px-6 py-4 text-right border-t hairline bg-gray-50">
                    <button type="button" data-dismiss="modal" class="elegant-btn-ghost mr-3">Cancel</button>
                    <button type="submit" class="elegant-btn-primary" id="usersEditButton">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
    <!-- END: Users Edit Modal -->

    <!-- BEGIN: Users Add Modal -->
    <div class="modal" id="usersAddModal">
        <div class="modal__content" style="max-width: 500px;">
            <div class="flex items-center px-6 py-5 border-b hairline">
                <h2 class="serif text-xl font-semibold ink">Add User</h2>
            </div>
            <form>
                <div class="p-6 space-y-5">
                    <div>
                        <label class="small-caps text-gray-600 mb-2 block">Name</label>
                        <input type="text" class="elegant-input w-full" placeholder="Name" id="usersAddName"
                            required>
                    </div>
                    <div>
                        <label class="small-caps text-gray-600 mb-2 block">Username</label>
                        <input type="text" class="elegant-input w-full" placeholder="Username" id="usersAddUsername"
                            required>
                    </div>
                    <div>
                        <label class="small-caps text-gray-600 mb-2 block">Role</label>
                        <select class="elegant-input w-full" id="usersAddRole" required>
                            <option value="superadmin">Superadmin</option>
                            <option value="admin">Admin</option>
                            <option value="support">Support</option>
                            <option value="sales">Sales</option>
                            <option value="services">Services</option>
                        </select>
                    </div>
                    <div>
                        <label class="small-caps text-gray-600 mb-2 block">Password</label>
                        <input type="password" class="elegant-input w-full" placeholder="Password" id="usersAddPassword"
                            required>
                    </div>
                    <div>
                        <label class="small-caps text-gray-600 mb-2 block">Password Confirmation</label>
                        <input type="password" class="elegant-input w-full" placeholder="Confirm Password"
                            id="usersAddPasswordConfirmation" required>
                    </div>
                    <div>
                        <label class="small-caps text-gray-600 mb-2 block">Email</label>
                        <input type="email" class="elegant-input w-full" placeholder="example@gmail.com"
                            id="usersAddEmail" required>
                    </div>
                </div>

                <div class="px-6 py-4 text-right border-t hairline bg-gray-50">
                    <button type="button" data-dismiss="modal" class="elegant-btn-ghost mr-3">Cancel</button>
                    <button type="button" class="elegant-btn-primary" id="usersAddButton">Create User</button>
                </div>
            </form>
        </div>
    </div>
    <!-- END: Users Add Modal -->

    <!-- BEGIN: Users Delete Modal -->
    <div class="modal" id="usersDeleteModal">
        <div class="modal__content" style="max-width: 440px;">
            <div class="p-6 text-center">
                <div class="w-16 h-16 rounded-full bg-red-50 mx-auto flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round" class="text-red-600">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="15" y1="9" x2="9" y2="15"></line>
                        <line x1="9" y1="9" x2="15" y2="15"></line>
                    </svg>
                </div>
                <h3 class="serif text-2xl font-semibold ink mb-2">Delete User?</h3>
                <p class="text-gray-600">This action cannot be undone. The user will be permanently removed from the
                    system.</p>
            </div>
            <div class="px-6 pb-6 text-center flex gap-3 justify-center">
                <button type="button" data-dismiss="modal" class="elegant-btn-ghost px-6">Cancel</button>
                <button type="button" class="elegant-btn-destructive px-6" id="usersDeleteButton">Delete User</button>
            </div>
        </div>
    </div>
    <!-- END: Users Delete Modal -->
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            // Global variables
            var original_username;
            var lastClickedLink;

            $(document).on('click', '#usersAddButton', function(e) {
                e.preventDefault();
                usersAddButton();
            });

            // Edit
            $(document).on('click', '#usersEditButton', function(e) {
                e.preventDefault();
                editUsers();
            });

            // Delete (confirm button inside modal)
            $(document).on('click', '.delete-user-link', function(e) {
                e.preventDefault();
                const id = $(this).data('user-id');
                // attach the id to the confirm button that actually submits
                $('#usersDeleteButton').data('user-id', id);
                openAltEditorModal('#usersDeleteModal'); // Use the existing function
            });


            // Store the ID of the last clicked modal when it's triggered
            (function() {
                $(document).on('click', "[data-toggle='modal']", function() {
                    lastClickedLink = $(this).attr('id');
                });
            })();

            // Open modal - works with cash() or fallback
            function openAltEditorModal(element) {
                if (window.cash && typeof cash(element).modal === 'function') {
                    cash(element).modal('show');
                } else {
                    $(element).addClass('show').css('display', 'flex');
                }
            }

            // Close modal - works with cash() or fallback
            function closeAltEditorModal(element) {
                if (window.cash && typeof cash(element).modal === 'function') {
                    cash(element).modal('hide');
                } else {
                    $(element).removeClass('show').css('display', 'none');
                }
            }

            // Add helper for data-dismiss functionality
            $(document).on('click', '[data-dismiss="modal"]', function() {
                var target = $(this).closest('.modal');
                closeAltEditorModal('#' + target.attr('id'));
            });

            /**
             * USERS
             */

            // Setup the users datatable
            function initUsersDatatable() {
                const dt = new Date();
                const formattedDate =
                    `${dt.getFullYear()}${(dt.getMonth() + 1).toString().padStart(2, '0')}${dt.getDate().toString().padStart(2, '0')}`;
                const formattedTime = `${dt.getHours()}:${dt.getMinutes()}:${dt.getSeconds()}`;
                const $fileName = `Users_List_${formattedDate}_${formattedTime}`;

                const table = $('#users_table').DataTable({
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
                        url: "{{ route('users.list') }}",
                        dataType: "json",
                        type: "POST",
                        method: "POST",
                        data: function(d) {
                            d._token = $('meta[name="csrf-token"]').attr('content');
                        },
                        dataSrc: function(json) {
                            json.recordsTotal = json.recordsTotal;
                            json.recordsFiltered = json.recordsFiltered;

                            // Update subtitle with count
                            $('#userCountSubtitle').text(`Showing ${json.recordsFiltered} users`);

                            return json.data;
                        }
                    },
                    dom: "lBfrtip",
                    buttons: [{
                            extend: "csv",
                            className: "elegant-btn-ghost",
                            text: "CSV",
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
                            className: "elegant-btn-ghost",
                            text: "Excel",
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
                            className: "elegant-btn-ghost",
                            text: "Print",
                            title: $fileName,
                            exportOptions: {
                                columns: ":not(.dt-exclude-export)",
                                stripHtml: false,
                            },
                            init: function(api, node, config) {
                                $(node).removeClass('dt-button buttons-html5');
                            },
                        },
                    ],
                    columnDefs: [{
                        targets: 'dt-no-sort',
                        orderable: false
                    }],
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
                            data: "name",
                            render: function(data) {
                                return `<span class="font-medium">${data}</span>`;
                            }
                        },
                        {
                            data: "username",
                        },
                        {
                            data: "role",
                            render: function(data) {
                                return `<span class="px-3 py-1 text-xs font-medium bg-gray-100 rounded-full">${data}</span>`;
                            }
                        },
                        {
                            data: "email",
                            render: function(data) {
                                return `<span class="tabular-nums truncate-cell" title="${data}">${data}</span>`;
                            }
                        },
                        {
                            data: "id",
                            render: function(data, type, row) {
                                return `
                            <a class="elegant-btn-ghost inline-flex items-center px-3 py-1.5 delete-user-link"
                                href="javascript:;" data-toggle="modal" data-target="#usersDeleteModal"
                                id="delete-user-${data}" data-user-id="${data}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1.5">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                <line x1="14" y1="11" x2="14" y2="17"></line>
                                </svg>
                                Delete
                            </a>
                            `;
                            }
                        },
                    ],
                });

                // Style DataTables elements
                var dtButtonsDiv = document.querySelector(".dt-buttons");
                if (dtButtonsDiv) {
                    dtButtonsDiv.classList.add("flex", "gap-2", "mb-4");
                }

                var filterDiv = document.getElementById("users_table_filter");
                if (filterDiv) {
                    filterDiv.style.display = "none"; // Hide default search
                }

                var infoDiv = document.getElementById("users_table_info");
                var paginateDiv = document.getElementById("users_table_paginate");

                if (infoDiv) {
                    infoDiv.style.float = "left";
                    infoDiv.classList.add("mt-6", "text-sm", "text-gray-600");
                }

                if (paginateDiv) {
                    paginateDiv.style.float = "right";
                    paginateDiv.classList.add("mt-6");
                }

                var existingDiv = document.getElementById("users_table_length");
                if (existingDiv) {
                    existingDiv.classList.add('mb-4', 'flex', 'items-center', 'gap-2');
                    existingDiv.innerHTML = existingDiv.innerHTML.replace('Show ',
                        '<span class="text-sm text-gray-600">Show</span> ');
                    existingDiv.innerHTML = existingDiv.innerHTML.replace(' entries',
                        '<span class="text-sm text-gray-600">entries</span>');

                    var existingSelect = existingDiv.querySelector('select');
                    if (existingSelect) {
                        existingSelect.className = 'elegant-input py-1.5 px-3 h-auto';
                    }
                }

                usersEditModal();
            }

            // Add New User
            function usersAddButton() {
                $.ajax({
                    type: 'POST',
                    url: "{{ route('users.create') }}",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        name: document.getElementById("usersAddName").value,
                        username: document.getElementById("usersAddUsername").value,
                        role: document.getElementById("usersAddRole").value,
                        password: document.getElementById("usersAddPassword").value,
                        password_confirmation: document.getElementById("usersAddPasswordConfirmation")
                            .value,
                        email: document.getElementById("usersAddEmail").value
                    },
                    success: function(response) {
                        closeAltEditorModal('#usersAddModal');
                        window.showSubmitToast("Successfully added.", "#91C714");

                        document.getElementById("usersAddName").value = "";
                        document.getElementById("usersAddUsername").value = "";
                        document.getElementById("usersAddRole").value = "";
                        document.getElementById("usersAddPassword").value = "";
                        document.getElementById("usersAddPasswordConfirmation").value = "";
                        document.getElementById("usersAddEmail").value = "";

                        $('#users_table').DataTable().ajax.reload();
                    },
                    error: function(xhr) {
                        let msg = 'Unexpected error';
                        try {
                            const r = JSON.parse(xhr.responseText);
                            msg = r.error || r.message || msg;
                        } catch (e) {
                            msg = (xhr.status + ' ' + xhr.statusText) || msg;
                        }
                        window.showSubmitToast(msg, "#D32929");
                    }

                });
            }

            // Open modal to edit user
            function usersEditModal() {
                $(document).off('click', "[id^='users_table'] tbody tr td:not(:last-child)");

                $(document).on('click', "#users_table tbody tr td:not(:last-child)", function(e) {
                    const $row = $(this).closest('tr');

                    original_username = $row.find('td:nth-child(3)').text().trim();

                    $('#usersEditName').val($row.find('td:nth-child(2)').text().trim());
                    $('#usersEditUsername').val($row.find('td:nth-child(3)').text().trim());
                    $('#usersEditEmail').val($row.find('td:nth-child(5)').text().trim());

                    const roleText = $row.find('td:nth-child(4)').text().trim().toLowerCase();
                    $('#usersEditRole').val(roleText);

                    openAltEditorModal('#usersEditModal');
                });

            }

            // Edit User
            function editUsers() {
                var name = document.getElementById("usersEditName").value;
                var username = document.getElementById("usersEditUsername").value;
                var role = document.getElementById("usersEditRole").value;
                var email = document.getElementById("usersEditEmail").value;

                $.ajax({
                    type: 'POST',
                    url: "{{ route('users.update') }}",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        name: name,
                        username: username,
                        original_username: original_username,
                        role: role,
                        email: email
                    },
                    success: function(response) {
                        closeAltEditorModal('#usersEditModal');
                        window.showSubmitToast("Successfully edited.", "#91C714");
                        $('#users_table').DataTable().ajax.reload();
                    },
                    error: function(xhr) {
                        let msg = 'Unexpected error';
                        try {
                            const r = JSON.parse(xhr.responseText);
                            msg = r.error || r.message || msg;
                        } catch (e) {
                            msg = (xhr.status + ' ' + xhr.statusText) || msg;
                        }
                        window.showSubmitToast(msg, "#D32929");
                    }
                });
            }

            // Delete User
            $(document).on('click', '#usersDeleteButton', function(e) {
                e.preventDefault();
                usersDeleteButton();
            });

            function usersDeleteButton() {
                const delete_user_id = $('#usersDeleteButton').data('user-id');

                $.ajax({
                    type: 'POST',
                    url: "{{ route('users.delete') }}",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        delete_user_id: delete_user_id
                    },
                    success: function() {
                        closeAltEditorModal('#usersDeleteModal');
                        window.showSubmitToast("Successfully deleted.", "#91C714");
                        $('#users_table').DataTable().ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        const msg = xhr.responseJSON?.error || 'Delete failed.';
                        window.showSubmitToast(msg, "#D32929");
                    }
                });
            }


            // Filter functionality
            $('#applyFiltersBtn').on('click', function() {
                var searchVal = $('#filterSearch').val();
                var roleVal = $('#filterRole').val();

                var table = $('#users_table').DataTable();

                // Apply search filter
                table.search(searchVal).draw();

                // Show active filters
                var filterChipsHtml = '';
                if (searchVal) {
                    filterChipsHtml += `<span class="filter-chip">SEARCH: ${searchVal}</span>`;
                }
                if (roleVal) {
                    filterChipsHtml += `<span class="filter-chip">ROLE: ${roleVal.toUpperCase()}</span>`;
                }

                if (filterChipsHtml) {
                    $('#activeFilters').html(filterChipsHtml).removeClass('hidden');
                } else {
                    $('#activeFilters').addClass('hidden');
                }
            });

            // Ensure CSRF header is set for all AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Fallback for missing toast function
            if (typeof window.showSubmitToast !== 'function') {
                window.showSubmitToast = function(msg) {
                    console.log('[toast]', msg);
                };
            }

            // Explicitly open Add User modal when button clicked
            $(document).on('click', '#usersAddModalButton', function(e) {
                e.preventDefault();
                openAltEditorModal('#usersAddModal');
            });

            initUsersDatatable();
        });
    </script>
@endpush
