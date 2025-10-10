<?php $__env->startSection('title'); ?>
<title>BGOC Tracking System - Clients</title>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="intro-y flex flex-col sm:flex-row items-center mt-8">
    <h2 class="text-lg font-medium mr-auto">
        Clients
    </h2>
</div>

<div class="intro-y box p-5 mt-5">
    <div class="pos col-span-12 lg:col-span-4">
        <!-- BEGIN: Client -->
        <div>
            <!-- BEGIN: Filter & Add Client -->
            <div class="flex flex-col sm:flex-row sm:items-end xl:items-start">
                <!-- BEGIN: Filter -->
                <form class="xl:flex sm:mr-auto">
                    <div class="sm:flex items-center sm:mr-4">
                        <label class="w-12 flex-none xl:w-auto xl:flex-initial mr-2">Company Name</label>
                        <select class="input w-full sm:w-32 xxl:w-full mt-2 sm:mt-0 sm:w-auto border" id="inputCompanyName">
                        <option selected value="">Select an option</option>
                        <?php $__currentLoopData = $clientcompany; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $clientcomp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($clientcomp->id); ?>"><?php echo e($clientcomp->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="mt-2 xl:mt-0">
                        <button type="button" class="button w-full sm:w-16 bg-theme-32 text-white" id="filterClientButton">Filter</button>
                    </div>
                </form>
                <!-- END: Filter -->

                <!-- BEGIN: Add Client -->
                <div class="text-center">
                    <a href="javascript:;" data-toggle="modal" data-target="#clientAddModal" class="button w-50 mr-2 mb-2 flex items-center justify-center bg-theme-32 text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus w-4 h-4">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Add New Client
                    </a>
                </div>
                <!-- END: Add Client -->
            </div>
            <!-- END: Filter & Add Client -->

            <!-- BEGIN: Client List -->
            <div class="overflow-x-auto scrollbar-hidden">
                <table class="table table-report mt-5" id="client_table">
                    <thead>
                        <tr class="bg-theme-1 text-white">
                            <th width="5%">No.</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Contact No.</th>
                            <th>Company</th>
                            <!-- <th class="whitespace-nowrap">User</th>
                            <th class="whitespace-nowrap" width="20%">User Account Status</th>
                            <th class="whitespace-nowrap" width="10%">Client Status</th> -->
                            <th width="10%">Action</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <!-- END: Client List -->
        </div>
        <!-- END: Client -->
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('modal_content'); ?>
<!-- BEGIN: Client Users Add Modal -->
<div class="modal" id="clientAddModal">
    <div class="modal__content">
        <div class="flex items-center px-5 py-5 sm:py-3 border-b border-gray-200 dark:border-dark-5">
            <h2 class="font-medium text-base mr-auto">Add Client</h2>
        </div>
        <form>
        <?php echo csrf_field(); ?>
        <div class="p-5 grid grid-cols-12 gap-4 gap-y-3">
            <div class="col-span-12 sm:col-span-12">
                <label>Designation</label>
                <input type="text"  title="Please enter client designation" placeholder="Enter a Designation" class="input w-full border mt-2 flex-1" id="clientAddDesignation" required>
            </div>
            <div class="col-span-12 sm:col-span-12">
                <label>Client Name</label>
                <input type="text" placeholder="Enter a Client Name" class="input w-full border mt-2 flex-1" id="clientAddName" required>
            </div>
            <div class="col-span-12 sm:col-span-12">
                <label>Email</label>
                <input type="text" placeholder="Enter a Client Email" class="input w-full border mt-2 flex-1" id="clientAddEmail" required>
            </div>
            <div class="col-span-12 sm:col-span-12">
                <label>Contact No.</label>
                <input type="text"  title="Please enter contact number in correct format" placeholder="Enter a Contact Number" class="input w-full border mt-2 flex-1" id="clientAddContact" required>
            </div>

            <div class="col-span-12 sm:col-span-12">
                <label>Company</label>
                <select class="input w-full border mt-2 flex-1" id="clientAddCompany" required>
                    <option selected disabled value>Select an option</option>
                <?php $__currentLoopData = $clientcompany; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $clientcomp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($clientcomp->id); ?>"><?php echo e($clientcomp->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            </div>
            <div class="px-5 py-3 text-right border-t border-gray-200 dark:border-dark-5">
                <button type="submit" class="button w-50 bg-theme-1 text-white" id="clientAddButton">Save Client</button>
            </div>
        </form>
        
    </div>
</div>
<!-- END: Client Users Add Modal -->

<!-- BEGIN: Client Edit Modal -->
<div class="modal" id="clientEditModal">
    <div class="modal__content">
        <div class="flex items-center px-5 py-5 sm:py-3 border-b border-gray-200 dark:border-dark-5">
            <h2 class="font-medium text-base mr-auto">Edit Client</h2>
        </div>
        <form>
            <div class="p-5 grid grid-cols-12 gap-4 gap-y-3">
                <div class="col-span-12 sm:col-span-12">
                    <label>Client Name</label>
                    <input type="text" class="input w-full border mt-2 flex-1" placeholder="Client Name" id="clientEditName" required>
                </div>
                <div class="col-span-12 sm:col-span-12">
                    <label>Contact No.</label>
                    <input type="text" class="input w-full border mt-2 flex-1" placeholder="Contact No" id="clientEditContact" required>
                </div>
            </div>

            <div class="px-5 py-3 text-right border-t border-gray-200 dark:border-dark-5">
                <button type="submit" class="button w-20 bg-theme-1 text-white" id="clientEditButton">Update</button>
            </div>
        </form>
    </div>
</div>
<!-- END: Client Edit Modal -->

<!-- BEGIN: Client Delete Modal -->
<div class="modal" id="clientDeleteModal">
    <div class="modal__content">
        <div class="p-5 text-center"> <i data-feather="x-circle" class="w-16 h-16 text-theme-6 mx-auto mt-3"></i>
            <div class="text-3xl mt-5">Are you sure?</div>
            <div class="text-gray-600 mt-2">Confirm deleting the client? This process cannot be undone.</div>
        </div>
        <div class="px-5 pb-8 text-center">
            <button type="button" data-dismiss="modal" class="button w-24 border text-gray-700 dark:border-dark-5 dark:text-gray-300 mr-1">Cancel</button>
            <button type="button" class="button w-24 bg-theme-6 text-white" id="clientDeleteButton">Delete</button>
        </div>
    </div>
</div>
<!-- END: Client Delete Modal -->
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script>
    $(document).ready(function() {
        
        // Global variables
        var filterClientCompany;
        var originalClientId;
        var lastClickedLink;

        // Listen to below buttons
        document.getElementById("filterClientButton").addEventListener("click", filterClientButton);
        document.getElementById("clientAddButton").addEventListener("click", clientAddButton);
        document.getElementById("clientDeleteButton").addEventListener("click", clientDeleteButton);

        // When "filterClientButton" button is clicked, initiate initClientCompanyDatatable
        function filterClientButton() {
            filterClientCompany = document.getElementById("inputCompanyName").value;
            initClientDatatable(filterClientCompany);
        };

        // When page first loads, load table
        filterClientButton();

        // When any submit button is clicked
        (function() {
            var client_table = $('#client_table')[0].altEditor;

            document.getElementById('clientAddButton').addEventListener('click', function(e) {
                // Prevent the default form submission behavior
                e.preventDefault();
            });

            document.getElementById('clientEditButton').addEventListener('click', function(e) {
                // Prevent the default form submission behavior
                e.preventDefault();

                // Edit client
                editClient();
            });
        })();

        // Open modal
        function openAltEditorModal(element) {
            cash(element).modal('show');
        }

        // Close modal
        function closeAltEditorModal(element) {
            cash(element).modal('hide');
        }

        // Add New Client
        function clientAddButton() {
            $.ajax({
                type: 'POST',
                url: "<?php echo e(route('clients.create')); ?>",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    name: document.getElementById("clientAddName").value,
                    contact: document.getElementById("clientAddContact").value,
                    company_id: document.getElementById("clientAddCompany").value
                },
                success: function(response) {
                    // Close modal after successfully edited
                    var element = "#clientAddModal";
                    closeAltEditorModal(element);

                    // Show successful toast
                    window.showSubmitToast("Successfully added.", "#91C714");

                    // Clean fields
                    document.getElementById("clientAddName").value = "";
                    document.getElementById("clientAddContact").value = "";
                    document.getElementById("clientAddCompany").value = "";

                    // Reload table
                    $('#client_table').DataTable().ajax.reload();
                },
                error: function(xhr, status, error) {
                    // Display the validation error message
                    var response = JSON.parse(xhr.responseText);
                    var error = "Error: " + response.error;

                    // Show fail toast
                    window.showSubmitToast(error, "#D32929");
                }
            });
        }

        // Edit Client 
        function editClient() {
            var name = document.getElementById("clientEditName").value;
            var contact = document.getElementById("clientEditContact").value;

            $.ajax({
                type: 'POST',
                url: "<?php echo e(route('clients.edit')); ?>",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    name: name,
                    contact: contact,
                    original_client_id: originalClientId,
                },
                success: function(response) {
                    // Close modal after successfully edited
                    var element = "#clientEditModal";
                    closeAltEditorModal(element);

                    // Show successful toast
                    window.showSubmitToast("Successfully updated.", "#91C714");

                    // Clean fields
                    document.getElementById("clientEditName").value = "";
                    document.getElementById("clientEditContact").value = "";

                    // Reload table
                    $('#client_table').DataTable().ajax.reload();
                },
                error: function(xhr, status, error) {
                    // Display the validation error message
                    var response = JSON.parse(xhr.responseText);
                    var error = "Error: " + response.error;

                    // Show fail toast
                    window.showSubmitToast(error, "#D32929");
                }
            });
        }

        // Setup the clients datatable
        function initClientDatatable() {
            const dt = new Date();
            const formattedDate = `${dt.getFullYear()}${(dt.getMonth() + 1).toString().padStart(2, '0')}${dt.getDate().toString().padStart(2, '0')}`;
            const formattedTime = `${dt.getHours()}:${dt.getMinutes()}:${dt.getSeconds()}`;
            const $fileName = `Client_List_${formattedDate}_${formattedTime}`;

            const table = $('#client_table').DataTable({
                altEditor: true, // Enable altEditor
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
                    url: "<?php echo e(route('clients.list')); ?>",
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
                        return json.data;
                    }
                },
                dom: "lBfrtip",
                buttons: [{
                        extend: "csv",
                        className: "button w-24 rounded-full shadow-md mr-1 mb-2 bg-theme-7 text-white",
                        title: $fileName,
                        exportOptions: {
                            columns: ":not(.dt-exclude-export)"
                        },
                        init: function(api, node, config) {
                            $(node).removeClass('dt-button');
                            $(node).removeClass('buttons-html5');
                        },
                    },
                    {
                        extend: "excel",
                        className: "button w-24 rounded-full shadow-md mr-1 mb-2 bg-theme-7 text-white",
                        title: $fileName,
                        exportOptions: {
                            columns: ":not(.dt-exclude-export)"
                        },
                        init: function(api, node, config) {
                            $(node).removeClass('dt-button');
                            $(node).removeClass('buttons-html5');
                        },
                    },
                    {
                        extend: "print",
                        className: "button w-24 rounded-full shadow-md mr-1 mb-2 bg-theme-7 text-white",
                        title: $fileName,
                        // including printing image
                        exportOptions: {
                            stripHtml: false,
                        },
                        init: function(api, node, config) {
                            $(node).removeClass('dt-button');
                            $(node).removeClass('buttons-html5');
                        },
                    },
                ],
                columns: [
                    {
                        data: null, // <-- important
                        name: 'no',
                        orderable: false,
                        searchable: false,
                        render: function (data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: null,
                        name: "name",
                        render: function(data, type, row) {
                            let title = row.designation ?? '';
                            let name = row.name ?? '';
                            return `${title} ${name}`.trim();
                        }
                    },
                    {
                        data: "email",
                    },
                    {
                        data: "phone",
                    },
                    {
                        data: "company_name",
                    },
                    // {
                    //     data: "users_name",
                    // },
                    // {
                    //     data: "users_id",
                    //     type: "readonly",
                    //     render: function(data, type, row) {
                    //         let element = ``
                    //         if (data == null) {
                    //             element = `<a class="p-2 w-24 rounded-full mr-1 mb-2 border text-gray-700 dark:border-dark-5 dark:text-gray-300">No Account Registered</a>`;
                    //         }else{
                    //             element = `<a class="p-2 w-24 rounded-full mr-1 mb-2 bg-theme-18 text-black">Account Registered</a>`;
                    //         }
                    //         return element;
                    //     }
                    // },
                    // {
                    //     data: "status",
                    //     type: "readonly",
                    //     render: function(data, type, row) {
                    //         let element = ``
                    //         if (data == '0') {
                    //             element = `<a class="p-2 w-24 rounded-full mr-1 mb-2 border text-gray-700 dark:border-dark-5 dark:text-gray-300">Inactive</a>`;
                    //         }else{
                    //             element = `<a class="p-2 w-24 rounded-full mr-1 mb-2 bg-theme-18 text-black">Active</a>`;
                    //         }
                    //         return element;
                    //     }
                    // },
                    {
                        data: "id",
                        render: function(data, type, row) {
                            return `
                            <div class="flex items-center justify-center space-x-3">
                                <!-- Edit Icon -->
                                <a href="javascript:;" class="text-theme-9" data-toggle="modal" data-target="#clientEditModal" id="edit-${data}" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path d="M15.232 5.232l3.536 3.536M9 13h3l9-9a1.5 1.5 0 00-2.121-2.121l-9 9v3z"/>
                                    </svg>
                                </a>

                                <!-- Delete Icon -->
                                <a href="javascript:;" class="text-theme-6" data-toggle="modal" data-target="#clientDeleteModal" id="delete-${data}" title="Delete">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4
                                            a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                        <line x1="10" y1="11" x2="10" y2="17"></line>
                                        <line x1="14" y1="11" x2="14" y2="17"></line>
                                    </svg>
                                </a>
                            </div>`;
                        }
                    },
                ],
            });

            // Add classes to the "dt-buttons" div
            var dtButtonsDiv = document.querySelector(".dt-buttons");
            if (dtButtonsDiv) {
                dtButtonsDiv.classList.add("mt-2");
            }

            // Update styling for the filter input
            var filterDiv = document.getElementById("client_table_filter");
            if (filterDiv) {
                filterDiv.style.float = "right";
                filterDiv.classList.remove('dataTables_filter');

                var inputElement = filterDiv.querySelector("label input");
                if (inputElement) {
                    inputElement.classList.add("input", "border", "mt-2", "ml-2", "mr-1", "mb-5");
                }
            }

            // Update styling for the info and paginate elements
            var infoDiv = document.getElementById("client_table_info");
            var paginateDiv = document.getElementById("client_table_paginate");

            if (infoDiv) {
                infoDiv.style.float = "left";
                infoDiv.classList.add("mt-5");
            }

            if (paginateDiv) {
                paginateDiv.style.float = "right";
                paginateDiv.classList.add("mt-5");
            }

            // Update styling for the "client_users_table_length" div and its select element
            var existingDiv = document.getElementById("client_table_length");
            if (existingDiv) {
                existingDiv.classList.remove('dataTables_length');
                existingDiv.classList.add('mt-2', 'mb-1');

                var existingSelect = existingDiv.querySelector('select');
                if (existingSelect) {
                    existingSelect.className = 'input sm:w-auto border';
                }
            }

            // Open modal to edit client
            clientEditModal();
        };

        // Open modal to edit client
        function clientEditModal() {
            // Remove previous click event listeners
            $(document).off('click', "[id^='client_table'] tbody tr td:not(:last-child)");

            $(document).on('click', "[id^='client_table'] tbody tr td:not(:last-child)", function() {
                // Place values to edit form fields in the modal
                document.getElementById("clientEditName").value = $(event.target).closest('tr').find('td:nth-child(' + '1' + ')').text();
                document.getElementById("clientEditContact").value = $(event.target).closest('tr').find('td:nth-child(' + '2' + ')').text();

                // Grab row client id
                originalClientId = $(event.target).closest('tr').find('td:nth-child(7) a').attr('id').split('-')[1];

                // Open modal
                var element = "#clientEditModal";
                openAltEditorModal(element);
            });
        }

        var filterClientCompany;

        // When "filterClientButton" button is clicked, initiate filterClientCompany
        function filterClientButton() {
            filterClientCompany = document.getElementById("inputCompanyName").value;
            initClientDatatable(filterClientCompany);
        };

        // When page first loads, load tables
        filterClientButton();

        // Store the ID of the last clicked modal when it's triggered
        (function() {
            $(document).on('click', "[data-toggle='modal']", function() {
                lastClickedLink = $(this).attr('id');
            });
        })();

        // Delete Client Company
        function clientDeleteButton() {
            var deleteClientId = lastClickedLink.split("-")[1];

            $.ajax({
                type: 'POST',
                url: "<?php echo e(route('clients.delete')); ?>",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    delete_client_id: deleteClientId,
                },
                success: function (response) {
                    // Close modal after successfully deleted
                    var element = "#clientDeleteModal";
                    closeAltEditorModal(element);

                    // Show successful toast
                    window.showSubmitToast("Successfully deleted.", "#91C714");

                    // Reload table
                    $('#client_table').DataTable().ajax.reload();
                },
                error: function (xhr, status, error) {
                    // Display the validation error message
                    var response = JSON.parse(xhr.responseText);
                    var error = "Error: " + response.error;

                    // Show fail toast
                    window.showSubmitToast(error, "#D32929");
                }
            });
        }
    })
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Gjlang\kl_guide_tracker\resources\views\clients\index.blade.php ENDPATH**/ ?>