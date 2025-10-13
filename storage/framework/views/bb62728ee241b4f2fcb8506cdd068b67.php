<?php $__env->startSection('title'); ?>
<title>BGOC Outdoor System - Stock Inventory</title>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('sidebar'); ?>
<?php echo $__env->make('layouts.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('app_content'); ?>
<style>
    #inventory_table {
        border-collapse: collapse !important; /* merge borders */
    }

    #inventory_table th,
    #inventory_table td {
        border: 1px solid #ddd !important; /* light gray border */
        padding: 6px 10px;
    }

    #inventory_table thead th {
        border-bottom: 2px solid #bbb !important;
    }
</style>
<div class="intro-y flex flex-col sm:flex-row items-center mt-8">
    <h2 class="text-lg font-medium mr-auto">
        Stock Inventory
    </h2>
</div>

<!-- stock inventory filter and table -->
<div class="intro-y box p-5 mt-5">
    <div class="pos col-span-12 lg:col-span-4">
        <!-- BEGIN: Stock -->
        <div>
            <!-- BEGIN: Filter & Add Stock -->
            <div class="flex flex-col sm:flex-row sm:items-end xl:items-start">
                <!-- BEGIN: Filter -->
                <form class="xl:flex sm:mr-auto">
                    <div class="sm:flex items-center sm:mr-4">
                        <label class="w-12 flex-none xl:w-auto xl:flex-initial mr-2">Contractor</label>
                        <select class="input w-full sm:w-32 xxl:w-full mt-2 sm:mt-0 sm:w-auto border" id="filterStockContractor">
                        <option selected value="">Select an option</option>
                        <?php $__currentLoopData = $contractors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contractor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($contractor->id); ?>"><?php echo e($contractor->name); ?> - <?php echo e($contractor->company_name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="sm:flex items-center sm:mr-4">
                        <label class="w-12 flex-none xl:w-auto xl:flex-initial mr-2">Client</label>
                        <select class="input w-full sm:w-32 xxl:w-full mt-2 sm:mt-0 sm:w-auto border" id="filterStockClient">
                        <option selected value="">Select an option</option>
                        <?php $__currentLoopData = $clientcompany; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $clientcomp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($clientcomp->id); ?>"><?php echo e($clientcomp->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <!-- <div class="mt-2 xl:mt-0">
                        <button type="button" class="button w-full sm:w-16 bg-theme-32 text-white" id="filterClientButton">Filter</button>
                    </div> -->
                </form>
                <!-- END: Filter -->

                <!-- BEGIN: Add Stock Inventory -->
                <div class="text-center">
                    <a href="javascript:;" data-toggle="modal" data-target="#inventoryAddModal" class="button w-50 mr-2 mb-2 flex items-center justify-center bg-theme-32 text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus w-4 h-4">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Add New Stock Inventory
                    </a>
                </div>
                <!-- END: Add Stock Inventory -->
            </div>
            <!-- Monthly Ongoing Date Filter -->
            <div class="flex flex-col sm:flex-row sm:items-end xl:items-start mb-2 mt-2">
                <form class="xl:flex flex-wrap items-end">
                    <div class="row sm:flex items-center sm:mr-4">
                        <label class="w-24 text-gray-700">Start Date</label>
                        <input type="date" id="filterStockStartDate" class="input border w-48" />
                    </div>

                    <div class="row sm:flex items-center sm:mr-4">
                        <label class="w-24 text-gray-700">End Date</label>
                        <input type="date" id="filterStockEndDate" class="input border w-48" />
                    </div>
                    <!-- <div class="row sm:flex items-center sm:mr-4">
                        <label class="w-12 flex-none xl:w-auto xl:flex-initial mr-2">Year</label>
                        <select class="input w-full mt-2 sm:mt-0 sm:w-auto border" id="filterStockInventoryYear">
                            <?php for($y = 2023; $y <= now()->year + 2; $y++): ?>
                                <option value="<?php echo e($y); ?>" <?php echo e($y == now()->year ? 'selected' : ''); ?>><?php echo e($y); ?></option>
                            <?php endfor; ?>
                        </select>
                    </div> -->
                </form>
            </div>
            <!-- Filter End -->
            <!-- END: Filter & Add Stock Inventory -->

            <!-- BEGIN: Stock Inventory List -->
            <div class="overflow-x-auto scrollbar-hidden">
                <table class="table table-report mt-5" id="inventory_table">
                    <thead>
                        <tr class="bg-theme-1 text-white">
                            <th width="5%">No.</th>
                            <th class="bg-orange-500 text-white">Contractor</th>
                            <!-- stock inventory IN section -->
                            <th class="bg-orange-500 text-white">Client</th>
                            <th class="bg-orange-500 text-white">Site</th>
                            <th class="bg-orange-500 text-white">Type</th>
                            <th class="bg-orange-500 text-white">Size</th>
                            <th class="bg-orange-500 text-white">Quantity</th>
                            <th class="bg-orange-500 text-white">Remarks</th>
                            <th class="bg-orange-500 text-white">Date In</th>
                            <th class="bg-yellow-400 text-black">Bal - Contractor</th>
                            <!-- stock inventory OUT section -->
                            <th class="bg-green-600 text-white">Date Out</th>
                            <th class="bg-green-600 text-white">Quantity</th>
                            <th class="bg-green-600 text-white">Size</th>
                            <th class="bg-green-600 text-white">Type</th>
                            <th class="bg-green-600 text-white">Site</th>
                            <th class="bg-green-600 text-white">Client</th>
                            <th class="bg-green-600 text-white">Remarks</th>
                            <th width="10%">Action</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <!-- END: Stock Inventory List -->
        </div>
        <!-- END: Client -->
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('modal_content'); ?>
<!-- BEGIN: Inventory Add Modal -->
<div class="modal items-center justify-center" id="inventoryAddModal">
    <div class="bg-white rounded-lg shadow-lg w-11/12 max-w-7xl p-6">

        <!-- Modal Header -->
        <div class="flex items-center justify-between border-b pb-3 mb-4">
            <h2 class="text-lg font-semibold">Add Stock Inventory</h2>
            <!-- <button type="button" onclick="closeInventoryModal()">✖</button> -->
        </div>
        <form id="addStockInventoryForm">
            <div class="mb-4">
                <label class="block font-medium">Contractor</label>
                <select class="input w-full sm:w-32 xxl:w-full mt-2 sm:mt-0 sm:w-auto border" id="inputContractorName" required>
                        <option selected value="">Select an option</option>
                        <?php $__currentLoopData = $contractors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contractor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($contractor->id); ?>"><?php echo e($contractor->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
            </div>
            <div class="grid grid-cols-2 gap-8">

                <!-- LEFT COLUMN: IN INVENTORY -->
                <div class="bg-orange-50 p-4 rounded-lg">
                    <h3 class="font-bold text-orange-600 mb-3">Stock In Inventory</h3>

                    <div class="mb-3">
                        <label class="block">Balance - Contractor</label>
                        <input type="number" class="input w-full border mt-1" id="balContractor" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="block">Date In</label>
                        <input type="date" class="input w-full border mt-1" id="inputDateIn">
                    </div>

                    <div class="mb-3">
                        <label class="block">Remarks</label>
                        <input type="text" class="input w-full border mt-1" id="inputRemarksIn">
                    </div>
                    <div class="flex items-center sm:py-3 border-gray-200 dark:border-dark-5">
                        <h2 class="font-medium text-base mr-auto">Add Sites</h2>
                    </div>
                    <div id="siteInContainer">
                        <div class="siteIn">
                            <div class="mb-3">
                                <label class="block">Client/Contractor</label>
                                <select class="input w-full border mt-2 select2" name="clients_in[]">
                                <option selected value="">Select an option</option>
                                <!-- <?php $__currentLoopData = $clientcompany; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $clientcomp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($clientcomp->id); ?>"><?php echo e($clientcomp->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select> -->
                                    <optgroup label="Clients">
                                        <?php $__currentLoopData = $clientcompany; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $clientcomp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="client-<?php echo e($clientcomp->id); ?>">
                                                <?php echo e($clientcomp->name); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </optgroup>

                                    <optgroup label="Contractors">
                                        <?php $__currentLoopData = $contractors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contractor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="contractor-<?php echo e($contractor->id); ?>">
                                                <?php echo e($contractor->name); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </optgroup>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="block">Site</label>
                                <select class="input w-full border mt-2 select2" id="inputBillboardIn" name="sites_in[]">
                                    <option selected value="">Select an option</option>
                                    <?php $__currentLoopData = $billboards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $billboard): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option
                                            value="<?php echo e($billboard->id); ?>"
                                            data-type="<?php echo e($billboard->type); ?>"
                                            data-size="<?php echo e($billboard->size); ?>">
                                            <?php echo e($billboard->site_number); ?> - <?php echo e($billboard->name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="block">Type</label>
                                <input type="text" class="input w-full border mt-1" name="types_in[]" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="block">Size</label>
                                <input type="text" class="input w-full border mt-1" name="sizes_in[]" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="block"><strong>Quantity In</strong></label>
                                <input type="number" class="input w-full border mt-1" name="qtys_in[]">
                            </div>
                            <div class="mb-3">
                                <a href="javascript:void(0);" class="button bg-theme-6 text-white" onclick="removeSiteIn(this)">
                                    Remove
                                </a>
                            </div>
                        </div>
                    </div>
                    <button type="button" onclick="siteInAdd()" class="bg-blue-600 text-white px-4 py-2 rounded">Add Site</button>
                </div>

                <!-- RIGHT COLUMN: OUT INVENTORY -->
                <div class="bg-green-50 p-4 rounded-lg">
                    <h3 class="font-bold text-green-600 mb-3">Stock Out Inventory</h3>

                    <div class="mb-3">
                        <label class="block">Bal - BGOC</label>
                        <input type="number" class="input w-full border mt-1" id="balBgoc" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="block">Date Out</label>
                        <input type="date" class="input w-full border mt-1" id="inputDateOut">
                    </div>

                    <div class="mb-3">
                        <label class="block">Remarks</label>
                        <input type="text" class="input w-full border mt-1" id="inputRemarksOut">
                    </div>
                    <div class="flex items-center sm:py-3 border-gray-200 dark:border-dark-5">
                        <h2 class="font-medium text-base mr-auto">Add Sites</h2>
                    </div>
                    <div id="siteOutContainer">
                        <div class="siteOut">
                            <div class="mb-3">
                                <label class="block">Client/Contractor</label>
                                <select class="input w-full border mt-2 select2" name="clients_out[]">
                                <option selected value="">Select an option</option>
                                <!-- <?php $__currentLoopData = $clientcompany; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $clientcomp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($clientcomp->id); ?>"><?php echo e($clientcomp->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> -->
                                    <optgroup label="Clients">
                                        <?php $__currentLoopData = $clientcompany; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $clientcomp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="client-<?php echo e($clientcomp->id); ?>">
                                                <?php echo e($clientcomp->name); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </optgroup>

                                    <optgroup label="Contractors">
                                        <?php $__currentLoopData = $contractors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contractor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="contractor-<?php echo e($contractor->id); ?>">
                                                <?php echo e($contractor->name); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </optgroup>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="block">Site</label>
                                <select class="input w-full border mt-2 select2" id="inputBillboardOut" name="sites_out[]">
                                    <option selected value="">Select an option</option>
                                    <?php $__currentLoopData = $billboards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $billboard): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option
                                            value="<?php echo e($billboard->id); ?>"
                                            data-type="<?php echo e($billboard->type); ?>"
                                            data-size="<?php echo e($billboard->size); ?>">
                                            <?php echo e($billboard->site_number); ?> - <?php echo e($billboard->name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="block">Type</label>
                                <input type="text" class="input w-full border mt-1" name="types_out[]" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="block">Size</label>
                                <input type="text" class="input w-full border mt-1" name="sizes_out[]" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="block"><strong>Quantity Out</strong></label>
                                <input type="number" class="input w-full border mt-1" name="qtys_out[]">
                            </div>
                            <div class="mb-3">
                                <a href="javascript:void(0);" class="button bg-theme-6 text-white" onclick="removeSiteOut(this)">
                                    Remove
                                </a>
                            </div>
                        </div>
                    </div>
                    <button type="button" onclick="siteOutAdd()" class="bg-blue-600 text-white px-4 py-2 rounded">Add Site</button>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-6 text-right">
                <button type="button" id="inventoryAddButton" class="bg-blue-600 text-white px-4 py-2 rounded">Submit</button>
            </div>
        </form>
    </div>
</div>
<!-- END: Inventory Add Modal -->

<!-- BEGIN: Inventory Edit Modal -->
<div class="modal items-center justify-center" id="inventoryEditModal">
    <div class="bg-white rounded-lg shadow-lg w-11/12 max-w-7xl p-6">

        <!-- Modal Header -->
        <div class="flex items-center justify-between border-b pb-3 mb-4">
            <h2 class="text-lg font-semibold">Edit Stock Inventory</h2>
            <!-- <button type="button" onclick="closeInventoryModal()">✖</button> -->
        </div>

        <form id="inventoryEditForm">
            <div class="mb-4">
                <label class="block font-medium">Contractor</label>
                <select class="input w-full sm:w-32 xxl:w-full mt-2 sm:mt-0 sm:w-auto border" id="editContractorName" disabled>
                        <option selected value="">Select an option</option>
                        <?php $__currentLoopData = $contractors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contractor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($contractor->id); ?>"><?php echo e($contractor->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
            </div>
            <div class="grid grid-cols-2 gap-8">

                <!-- LEFT COLUMN: IN INVENTORY -->
                <div class="bg-orange-50 p-4 rounded-lg">
                    <h3 class="font-bold text-orange-600 mb-3">Stock In Inventory</h3>

                    <div class="mb-3">
                        <label class="block">Bal - Contractor</label>
                        <input type="number" class="input w-full border mt-1" id="editBalanceContractor" name="balance_contractor" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="block">Date In</label>
                        <input type="date" class="input w-full border mt-1" id="editDateIn">
                    </div>
                    <div class="mb-3">
                        <label class="block">Remarks</label>
                        <input type="text" class="input w-full border mt-1" id="editRemarksIn">
                    </div>
                    <div class="flex items-center py-3 border-b border-gray-200">
                        <h2 class="font-medium text-base mr-auto">Sites</h2>
                    </div>

                    <div class="mb-3">
                        <label class="block">Client</label>
                        <select class="input w-full sm:w-32 xxl:w-full mt-2 sm:mt-0 sm:w-auto border select2" id="editClientIn">
                        <option selected value="">Select an option</option>
                        <?php $__currentLoopData = $clientcompany; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $clientcomp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($clientcomp->id); ?>"><?php echo e($clientcomp->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="block">Site</label>
                        <select class="input w-full border mt-2 select2" id="editBillboardIn">
                            <option selected value="">Select an option</option>
                            <?php $__currentLoopData = $billboards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $billboard): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option
                                    value="<?php echo e($billboard->id); ?>"
                                    data-type="<?php echo e($billboard->type); ?>"
                                    data-size="<?php echo e($billboard->size); ?>">
                                    <?php echo e($billboard->site_number); ?> - <?php echo e($billboard->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="block">Type</label>
                        <input type="text" class="input w-full border mt-1" id="editTypeIn" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="block">Size</label>
                        <input type="text" class="input w-full border mt-1" id="editSizeIn" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="block"><strong>Quantity In</strong></label>
                        <input type="number" class="input w-full border mt-1" id="editQtyIn">
                    </div>
                </div>

                <!-- RIGHT COLUMN: OUT INVENTORY -->
                <div class="bg-green-50 p-4 rounded-lg">
                    <h3 class="font-bold text-green-600 mb-3">Stock Out Inventory</h3>

                    <div class="mb-3">
                        <label class="block">Bal - BGOC</label>
                        <input type="number" class="input w-full border mt-1" id="editBalanceBgoc" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="block">Date Out</label>
                        <input type="date" class="input w-full border mt-1" id="editDateOut">
                    </div>
                    <div class="mb-3">
                        <label class="block">Remarks</label>
                        <input type="text" class="input w-full border mt-1" id="editRemarksOut">
                    </div>
                    <div class="flex items-center py-3 border-b border-gray-200">
                        <h2 class="font-medium text-base mr-auto">Sites</h2>
                    </div>


                    <div class="mb-3">
                        <label class="block">Client</label>
                        <select class="input w-full sm:w-32 xxl:w-full mt-2 sm:mt-0 sm:w-auto border select2" id="editClientOut">
                        <option selected value="">Select an option</option>
                        <?php $__currentLoopData = $clientcompany; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $clientcomp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($clientcomp->id); ?>"><?php echo e($clientcomp->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="block">Site</label>
                        <select class="input w-full border mt-2 select2" id="editBillboardOut">
                            <option selected value="">Select an option</option>
                            <?php $__currentLoopData = $billboards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $billboard): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option
                                    value="<?php echo e($billboard->id); ?>"
                                    data-type="<?php echo e($billboard->type); ?>"
                                    data-size="<?php echo e($billboard->size); ?>">
                                    <?php echo e($billboard->site_number); ?> - <?php echo e($billboard->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="block">Type</label>
                        <input type="text" class="input w-full border mt-1" id="editTypeOut" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="block">Size</label>
                        <input type="text" class="input w-full border mt-1" id="editSizeOut" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="block"><strong>Quantity Out</strong></label>
                        <input type="number" class="input w-full border mt-1" id="editQtyOut">
                    </div>


                </div>
            </div>

            <!-- Footer -->
            <div class="mt-6 text-right">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
            </div>
        </form>
    </div>
</div>
<!-- END: Inventory Edit Modal -->

<!-- BEGIN: Inventory Delete Modal -->
<div class="modal" id="inventoryDeleteModal">
    <div class="modal__content">
        <div class="p-5 text-center"> <i data-feather="x-circle" class="w-16 h-16 text-theme-6 mx-auto mt-3"></i>
            <div class="text-3xl mt-5">Are you sure?</div>
            <div class="text-gray-600 mt-2">Confirm deleting the client? This process cannot be undone.</div>
        </div>
        <div class="px-5 pb-8 text-center">
            <button type="button" data-dismiss="modal" class="button w-24 border text-gray-700 dark:border-dark-5 dark:text-gray-300 mr-1">Cancel</button>
            <button type="button" class="button w-24 bg-theme-6 text-white" id="inventoryDeleteButton">Delete</button>
        </div>
    </div>
</div>
<!-- END: Inventory Delete Modal -->
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>

<!-- searchable dropdown -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>

    const startDateInput = document.getElementById("filterStockStartDate");
    const endDateInput   = document.getElementById("filterStockEndDate");

    // When start date changes, set it as min for end date
    startDateInput.addEventListener("change", function () {
        endDateInput.min = this.value;

        // If end date is before start date, reset it
        if (endDateInput.value && endDateInput.value < this.value) {
            endDateInput.value = this.value;
        }
    });

    // When end date changes, set it as max for start date
    endDateInput.addEventListener("change", function () {
        startDateInput.max = this.value;

        // If start date is after end date, reset it
        if (startDateInput.value && startDateInput.value > this.value) {
            startDateInput.value = this.value;
        }
    });

    // Add site to In Inventory
    function siteInAdd() {
        let html = `
            <br><div class="siteIn">
                <div class="mb-3">
                    <label class="block">Client/Contractor</label>
                    <select class="input w-full border mt-2 select2" name="clients_in[]">
                        <option selected value="">Select an option</option>
                        <optgroup label="Clients">
                            <?php $__currentLoopData = $clientcompany; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $clientcomp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="client-<?php echo e($clientcomp->id); ?>">
                                    <?php echo e($clientcomp->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </optgroup>
                        <optgroup label="Contractors">
                            <?php $__currentLoopData = $contractors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contractor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="contractor-<?php echo e($contractor->id); ?>">
                                    <?php echo e($contractor->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </optgroup>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="block">Site</label>
                    <select class="input w-full border mt-2 select2" name="sites_in[]">
                        <option selected value="">Select an option</option>
                        <?php $__currentLoopData = $billboards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $billboard): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option
                                value="<?php echo e($billboard->id); ?>"
                                data-type="<?php echo e($billboard->type); ?>"
                                data-size="<?php echo e($billboard->size); ?>">
                                <?php echo e($billboard->site_number); ?> - <?php echo e($billboard->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="block">Type</label>
                    <input type="text" class="input w-full border mt-1" name="types_in[]" readonly>
                </div>
                <div class="mb-3">
                    <label class="block">Size</label>
                    <input type="text" class="input w-full border mt-1" name="sizes_in[]" readonly>
                </div>
                <div class="mb-3">
                    <label class="block"><strong>Quantity In</strong></label>
                    <input type="number" class="input w-full border mt-1" name="qtys_in[]">
                </div>
                <div class="mb-3">
                    <a href="javascript:void(0);" class="button bg-theme-6 text-white" onclick="removeSiteIn(this)">
                        Remove
                    </a>
                </div>
            </div>`;

        // Append
        $("#siteInContainer").append(html);

        // Re-init select2 for all .select2 (new + old)
        $("#siteInContainer .select2").select2({
            width: '100%'
        });

        updateTotalIn();
    }

    function removeSiteIn(el) {
        el.closest(".siteIn").remove();
        updateTotalIn();
    }

    // Add site to Out Inventory
    function siteOutAdd() {
        let html = `
            <br><div class="siteOut">
                <div class="mb-3">
                    <label class="block">Client/Contractor</label>
                    <select class="input w-full border mt-2 select2" name="clients_out[]">
                        <option selected value="">Select an option</option>
                        <optgroup label="Clients">
                            <?php $__currentLoopData = $clientcompany; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $clientcomp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="client-<?php echo e($clientcomp->id); ?>">
                                    <?php echo e($clientcomp->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </optgroup>
                        <optgroup label="Contractors">
                            <?php $__currentLoopData = $contractors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contractor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="contractor-<?php echo e($contractor->id); ?>">
                                    <?php echo e($contractor->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </optgroup>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="block">Site</label>
                    <select class="input w-full border mt-2 select2" name="sites_out[]" required>
                        <option selected value="">Select an option</option>
                        <?php $__currentLoopData = $billboards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $billboard): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option
                                value="<?php echo e($billboard->id); ?>"
                                data-type="<?php echo e($billboard->type); ?>"
                                data-size="<?php echo e($billboard->size); ?>">
                                <?php echo e($billboard->site_number); ?> - <?php echo e($billboard->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="block">Type</label>
                    <input type="text" class="input w-full border mt-1" name="types_out[]" readonly>
                </div>
                <div class="mb-3">
                    <label class="block">Size</label>
                    <input type="text" class="input w-full border mt-1" name="sizes_out[]" readonly>
                </div>
                <div class="mb-3">
                    <label class="block"><strong>Quantity Out</strong></label>
                    <input type="number" class="input w-full border mt-1" name="qtys_out[]" required>
                </div>
                <div class="mb-3">
                    <a href="javascript:void(0);" class="button bg-theme-6 text-white" onclick="removeSiteOut(this)">
                        Remove
                    </a>
                </div>
            </div>`;

        $("#siteOutContainer").append(html);

        // Re-init select2
        $("#siteOutContainer .select2").select2({
            width: '100%'
        });

        updateTotalOut();
    }

    function removeSiteOut(el) {
        el.closest(".siteOut").remove();
        updateTotalOut();
    }

    // Handle auto-fill Type & Size for ADD modal
    $(document).on('change', '#siteInContainer .select2, #siteOutContainer .select2', function() {
        let selected = $(this).find(':selected');
        let type = selected.data('type') || '';
        let size = selected.data('size') || '';

        // find the nearest .siteIn or .siteOut row
        let row = $(this).closest('.siteIn, .siteOut');
        row.find('input[name="types_in[]"], input[name="types_out[]"]').val(type);
        row.find('input[name="sizes_in[]"], input[name="sizes_out[]"]').val(size);
    });

    // Function to calculate total In
    function updateTotalIn() {
        let total = 0;
        document.querySelectorAll("input[name='qtys_in[]']").forEach(function(input) {
            let val = parseInt(input.value) || 0;
            total += val;
        });
        document.getElementById("balContractor").value = total;
    }

    // Function to calculate total Out
    function updateTotalOut() {
        let total = 0;
        document.querySelectorAll("input[name='qtys_out[]']").forEach(function(input) {
            let val = parseInt(input.value) || 0;
            total += val;
        });
        document.getElementById("balBgoc").value = total;
    }

    // Attach events when typing in Quantity fields
    $(document).on("input", "input[name='qtys_in[]']", function() {
        updateTotalIn();
    });

    $(document).on("input", "input[name='qtys_out[]']", function() {
        updateTotalOut();
    });

    // // Function to update total IN for edit modal
    // function updateTotalInEdit() {
    //     let val = parseInt(document.getElementById("editQtyIn").value) || 0;
    //     document.getElementById("editBalanceContractor").value = val;
    // }

    // // Function to update total OUT for edit modal
    // function updateTotalOutEdit() {
    //     let val = parseInt(document.getElementById("editQtyOut").value) || 0;
    //     document.getElementById("editBalanceBgoc").value = val;
    // }

    // // Attach events when typing in Quantity fields
    // document.getElementById("editQtyIn").addEventListener("input", updateTotalInEdit);
    // document.getElementById("editQtyOut").addEventListener("input", updateTotalOutEdit);

    // // Initialize totals when modal opens
    // function initEditTotals() {
    //     updateTotalInEdit();
    //     updateTotalOutEdit();
    // }

    // Update total IN
    function updateTotalInEdit() {
        let total = 0;
        document.querySelectorAll('.editQtyIn').forEach(input => {
            total += parseInt(input.value) || 0;
        });
        document.getElementById("editBalanceContractor").value = total;
    }

    // Update total OUT
    function updateTotalOutEdit() {
        let total = 0;
        document.querySelectorAll('.editQtyOut').forEach(input => {
            total += parseInt(input.value) || 0;
        });
        document.getElementById("editBalanceBgoc").value = total;
    }

    // Attach events
    document.querySelectorAll('.editQtyIn').forEach(input => input.addEventListener("input", updateTotalInEdit));
    document.querySelectorAll('.editQtyOut').forEach(input => input.addEventListener("input", updateTotalOutEdit));

    // Initialize totals on modal open
    function initEditTotals() {
        updateTotalInEdit();
        updateTotalOutEdit();
    }

    $(document).ready(function() {

        // Global variables
        var filterClientCompany;
        var inventoryId;
        var lastClickedLink;
        let transactionInId = null;
        let transactionOutId = null;
        let stockInventoryId = null;

        // Listen to below buttons
        document.getElementById("inventoryDeleteButton").addEventListener("click", inventoryDeleteButton);

        // When "filterClientButton" button is clicked, initiate initClientCompanyDatatable
        // function filterClientButton() {
        //     filterClientCompany = document.getElementById("fliterClient").value;
        //     // initStockInventoryDatatable(filterClientCompany);
        // };

        // When page first loads, load table
        // filterClientButton();

        // Initialize Select2 with search
        $('.select2').select2({
            placeholder: "Select an option",
            allowClear: true,
            width: '100%'
        });

        // $('#inventoryAddButton').on('click', function (e) {
        //     e.preventDefault();
        //     inventoryAddButton();
        // });

        document.getElementById("inventoryAddButton").addEventListener("click", function (e) {
            e.preventDefault();
            inventoryAddButton();
        });

        // ✅ IMPROVED MODAL FUNCTIONS
        // Function to properly show modal
        // function showModal(modalId) {
        //     $(modalId).removeClass('hidden').addClass('flex').css('display', 'flex');
        // }

        // Function to properly hide modal
        // function hideModal(modalId) {
        //     $(modalId).removeClass('flex').addClass('hidden').css('display', 'none');
        //     // Reset the current transaction ID when closing
        //     if (modalId === '#inventoryEditModal') {
        //         currentTransactionId = null;
        //     }
        // }

        // Open modal
        function openAltEditorModal(element) {
            cash(element).modal('show');
        }

        // Close modal
        function closeAltEditorModal(element) {
            cash(element).modal('hide');
        }

        function setupAutoFilter() {
            const tableElement = $('#inventory_table');
            const filterSelectors = '#filterStockContractor, #filterStockClient, #filterStockStartDate, #filterStockEndDate';
            // const selectedYear = $('#filterBillboardBookingYear').val();

            // Reload DataTable
            if ($.fn.DataTable.isDataTable(tableElement)) {
                const table = tableElement.DataTable();

                $(filterSelectors).on('change', function () {
                    // const selectedYear = $('#filterBillboardBookingYear').val();

                    table.ajax.reload();
                    // buildMonthlyJobTableHead(selectedYear);
                    // loadMonthlyJobs();
                    // initBillboardBookingDatatable()
                });

                $('#inventory_table').DataTable().ajax.reload();
            }
        }

        // Add New Inventory
        function inventoryAddButton() {
            // Gather basic fields
            let contractor_id = $("#inputContractorName").val();
            let date_in = $("#inputDateIn").val();
            let date_out = $("#inputDateOut").val();
            let remarks_in = $("#inputRemarksIn").val();
            let remarks_out = $("#inputRemarksOut").val();
            let balance_contractor = $("#balContractor").val();
            let balance_bgoc = $("#balBgoc").val();

            // Gather site IN rows
            let sites_in = [];
            $("#siteInContainer .siteIn").each(function () {
                let siteId = $(this).find("select[name='sites_in[]']").val();
                let rawVal = $(this).find("select[name='clients_in[]']").val();
                if (!rawVal) return; // skip empty

                let clientType = null, clientId = null;
                if (rawVal.startsWith("client-")) {
                    clientType = "client";
                    clientId = rawVal.replace("client-", "");
                } else if (rawVal.startsWith("contractor-")) {
                    clientType = "contractor";
                    clientId = rawVal.replace("contractor-", "");
                }

                sites_in.push({
                    id: siteId || null,
                    client_type: clientType || null,
                    client_id: clientId || null,
                    type: $(this).find("input[name='types_in[]']").val(),
                    size: $(this).find("input[name='sizes_in[]']").val(),
                    qty: parseInt($(this).find("input[name='qtys_in[]']").val()) || 0
                });
            });

            // Gather site OUT rows
            let sites_out = [];
            $("#siteOutContainer .siteOut").each(function () {
                let siteId = $(this).find("select[name='sites_out[]']").val();
                let rawVal = $(this).find("select[name='clients_out[]']").val();
                if (!rawVal) return; // skip empty

                let clientType = null, clientId = null;
                if (rawVal.startsWith("client-")) {
                    clientType = "client";
                    clientId = rawVal.replace("client-", "");
                } else if (rawVal.startsWith("contractor-")) {
                    clientType = "contractor";
                    clientId = rawVal.replace("contractor-", "");
                }

                sites_out.push({
                    id: siteId || null,
                    client_type: clientType || null,
                    client_id: clientId || null,
                    type: $(this).find("input[name='types_out[]']").val(),
                    size: $(this).find("input[name='sizes_out[]']").val(),
                    qty: parseInt($(this).find("input[name='qtys_out[]']").val()) || 0
                });
            });

            // Send request
            $.ajax({
                type: 'POST',
                url: "<?php echo e(route('stockInventory.create')); ?>",
                data: JSON.stringify({
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    contractor_id: contractor_id,
                    date_in: date_in,
                    date_out: date_out,
                    remarks_in: remarks_in,
                    remarks_out: remarks_out,
                    sites_in: sites_in,   // ✅ contains client + site info
                    sites_out: sites_out, // ✅ contains client + site info
                    balance_contractor: balance_contractor,
                    balance_bgoc: balance_bgoc
                }),
                contentType: "application/json",   // 👈 send JSON
                dataType: "json",
                success: function(response) {
                    // Close modal after successfully edited
                    closeAltEditorModal("#inventoryAddModal");

                    // Show successful toast
                    window.showSubmitToast("Successfully added.", "#91C714");

                    // Reset form
                    $('#inventoryAddModal input[type="text"], #inventoryAddModal input[type="number"], #inventoryAddModal input[type="date"]').val('');
                    $('#inventoryAddModal select').val('').trigger('change');
                    $('#siteInContainer').empty();
                    $('#siteOutContainer').empty();

                    // Reload table
                    // $('#inventory_table').DataTable().ajax.reload();
                    $('#inventory_table').DataTable().ajax.reload(null, false);
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
        function editContractor() {
            var company = document.getElementById("contractorEditCompanyName").value;
            var name = document.getElementById("contractorEditPICName").value;
            var phone = document.getElementById("contractorEditPhone").value;

            $.ajax({
                type: 'POST',
                url: "<?php echo e(route('contractors.edit')); ?>",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    company: company,
                    name: name,
                    phone: phone,
                    id: inventoryId,
                },
                success: function(response) {
                    // Close modal after successfully edited
                    var element = "#inventoryEditModal";
                    closeAltEditorModal(element);

                    // Show successful toast
                    window.showSubmitToast("Successfully updated.", "#91C714");

                    // Clean fields
                    document.getElementById("contractorEditCompanyName").value = "";
                    document.getElementById("contractorEditPICName").value = "";
                    document.getElementById("contractorEditPhone").value = "";

                    // Reload table
                    $('#inventory_table').DataTable().ajax.reload();
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

        // stock inventory datatable
        $('#inventory_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "<?php echo e(route('stockInventory.list')); ?>",
                type: "POST",
                data: function (d) {
                    d._token = "<?php echo e(csrf_token()); ?>";
                    d.contractor_id = $('#filterStockContractor').val();
                    d.client_id = $('#filterStockClient').val();
                    d.start_date = $('#filterStockStartDate').val();
                    d.end_date = $('#filterStockEndDate').val();
                    return d;
                },
                dataSrc: function(json) {
                    let newData = [];

                    json.data.forEach(row => {
                        // Split IN data into arrays
                        let inClients = row.client_in_name ? row.client_in_name.split(',') : [];
                        let inSites = row.site_in ? row.site_in.split(',') : [];
                        let inDates = row.date_in ? row.date_in.split(',') : [];
                        let inRemarks = row.remarks_in ? row.remarks_in.split(',') : [];
                        let inQty = row.quantity_in ? row.quantity_in.split(',') : [];

                        // Split OUT data into arrays
                        let outClients = row.client_out_name ? row.client_out_name.split(',') : [];
                        let outSites = row.site_out ? row.site_out.split(',') : [];
                        let outDates = row.date_out ? row.date_out.split(',') : [];
                        let outRemarks = row.remarks_out ? row.remarks_out.split(',') : [];
                        let outQty = row.quantity_out ? row.quantity_out.split(',') : [];

                        // Max number of rows needed
                        let rowCount = Math.max(inDates.length, outDates.length, 1);

                        for (let i = 0; i < rowCount; i++) {
                            newData.push({
                                contractor: row.contractor,
                                balance_contractor: row.balance_contractor,
                                // balance_bgoc: row.balance_bgoc,

                                // IN columns
                                client_in_name: inClients[i] || '',
                                site_in: inSites[i] || '',
                                date_in: inDates[i] || '',
                                remarks_in: inRemarks[i] || '',
                                quantity_in: inQty[i] || '',
                                billboard_type_in: row.billboard_type_in || '',
                                billboard_size_in: row.billboard_size_in || '',

                                // OUT columns
                                client_out_name: outClients[i] || '',
                                site_out: outSites[i] || '',
                                date_out: outDates[i] || '',
                                remarks_out: outRemarks[i] || '',
                                quantity_out: outQty[i] || '',
                                billboard_type_out: row.billboard_type_out || '',
                                billboard_size_out: row.billboard_size_out || '',

                                stock_inventory_id: row.stock_inventory_id,
                                transaction_in_id: row.transaction_in_id,
                                transaction_out_id: row.transaction_out_id,
                                contractor_id: row.contractor_id,
                                client_in_id: row.client_in_id,
                                client_out_id: row.client_out_id,
                                site_in_id: row.site_in_id,
                                site_out_id: row.site_out_id,
                                type_in: row.type_in,
                                size_in: row.size_in,
                                type_out: row.type_out,
                                size_out: row.size_out
                            });
                        }
                    });

                    return newData;
                }

            },
            columns: [
                {
                    data: null,
                    name: 'no',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                { data: 'contractor', name: 'contractors.name' },
                { data: 'client_in_name', name: 'client_companies.name' },
                { data: 'site_in', name: 'site_in.name' },
                { data: 'billboard_type_in', name: 'billboards.type' },
                { data: 'billboard_size_in', name: 'billboards.size' },
                { data: 'quantity_in', name: 'transactions_in.quantity' },
                { data: 'remarks_in', name: 'transactions_in.remarks' },
                { data: 'date_in', name: 'transactions_in.transaction_date' },
                { data: 'balance_contractor', name: 'stock_inventories.balance_contractor' },
                // { data: 'balance_bgoc', name: 'stock_inventories.balance_bgoc' },
                { data: 'date_out', name: 'transactions_out.transaction_date' },
                { data: 'quantity_out', name: 'transactions_out.quantity' },
                { data: 'billboard_size_out', name: 'billboards.size' },
                { data: 'billboard_type_out', name: 'billboards.type' },
                { data: 'site_out', name: 'site_out.name' },
                { data: 'client_out_name', name: 'client_companies.name' },
                { data: 'remarks_out', name: 'transactions_out.remarks' },
                {
                    data: null,
                    orderable: false,
                    render: function(data, type, row) {
                        let transactionId = row.transaction_in_id ? row.transaction_in_id : row.transaction_out_id;
                        let typeLabel = row.transaction_in_id ? 'IN' : 'OUT';

                        return `
                            <div class="flex items-center justify-center space-x-3">
                                <a href="javascript:;"
                                class="button w-24 inline-block mr-2 mb-2 bg-theme-1 text-white edit-inventory"
                                data-transaction-in-id="${row.transaction_in_id || ''}"
                                data-transaction-out-id="${row.transaction_out_id || ''}"
                                data-stock-inventory-id="${row.stock_inventory_id}">
                                Edit
                                </a>
                                <a href="javascript:;" class="text-theme-6"
                                    data-toggle="modal"
                                    data-transaction-id="${transactionId}"
                                    data-transaction-type="${typeLabel}"
                                    data-target="#inventoryDeleteModal"
                                    id="delete-${transactionId}"
                                    title="Delete ${typeLabel} Transaction">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4
                                            a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                        <line x1="10" y1="11" x2="10" y2="17"></line>
                                        <line x1="14" y1="11" x2="14" y2="17"></line>
                                    </svg>
                                </a>
                            </div>
                        `;
                    }
                },
            ],
            order: [[0, 'asc']],

            // 👇 This is where we merge Contractor + Bal-Contractor + Bal-BGOC
            drawCallback: function(settings) {
                let api = this.api();
                let rows = api.rows({ page: 'current' }).nodes();

                let lastStockId = null;
                let groupStart = null;

                api.rows({ page: 'current' }).every(function(rowIdx) {
                    let data = this.data();
                    let stockId = data.stock_inventory_id; // ✅ use stock inventory id, not row id

                    if (stockId !== lastStockId) {
                        // Finish previous group
                        if (groupStart !== null) {
                            let rowCount = rowIdx - groupStart;

                            // Apply rowspan to the groupStart row
                            $('td:eq(1)', rows[groupStart]).attr('rowspan', rowCount);
                            $('td:eq(9)', rows[groupStart]).attr('rowspan', rowCount);

                            // Hide duplicates
                            for (let j = groupStart + 1; j < rowIdx; j++) {
                                $('td:eq(1)', rows[j]).hide();
                                $('td:eq(9)', rows[j]).hide();
                            }
                        }

                        // Start new group
                        groupStart = rowIdx;
                        lastStockId = stockId;
                    }
                });

                // Handle last group
                if (groupStart !== null) {
                    let rowCount = rows.length - groupStart;

                    $('td:eq(1)', rows[groupStart]).attr('rowspan', rowCount);
                    $('td:eq(9)', rows[groupStart]).attr('rowspan', rowCount);

                    for (let j = groupStart + 1; j < rows.length; j++) {
                        $('td:eq(1)', rows[j]).hide();
                        $('td:eq(9)', rows[j]).hide();
                    }
                }
            }
        });

        // // Optional: close when clicking outside modal box
        // $(document).on('click', '#inventoryEditModal', function (e) {
        //     if ($(e.target).is('#inventoryEditModal')) {
        //         $(this).removeClass('flex').addClass('hidden');
        //     }
        // });

        // $('#inventory_table').off('click', '.edit-inventory').on('click', '.edit-inventory', function () {
        //     let btn = $(this);

        //     let currentTransactionId = btn.data('id');

        //     console.log('Edit button data:', {
        //         id: btn.data('id'),
        //         contractor_id: btn.data('contractor-id'),
        //         site_in_id: btn.data('site_in_id'),
        //         site_out_id: btn.data('site_out_id'),
        //         client_in_id: btn.data('client_in_id'),
        //         client_out_id: btn.data('client_out_id')
        //     }); // Debug log

        //     // Contractor & balances
        //     $('#editContractorName').val(btn.data('contractor-id')); // Use contractor ID, not name
        //     $('#editBalanceContractor').val(btn.data('balance_contractor'));
        //     $('#editBalanceBgoc').val(btn.data('balance_bgoc'));

        //     // Date In / Out
        //     $('#editDateIn').val(btn.data('date_in'));
        //     $('#editDateOut').val(btn.data('date_out'));

        //     // Remarks
        //     $('#editRemarksIn').val(btn.data('remarks_in'));
        //     $('#editRemarksOut').val(btn.data('remarks_out'));

        //     // Client & Site IN
        //     let clientInId = btn.data('client_in_id');
        //     let siteInId = btn.data('site_in_id');

        //     if (clientInId) {
        //         $('#editClientIn').val(clientInId).trigger('change');
        //     }

        //     if (siteInId) {
        //         $('#editBillboardIn').val(siteInId).trigger('change');

        //         // Set type, size, and quantity for IN
        //         $('#editTypeIn').val(btn.data('type_in') || '');
        //         $('#editSizeIn').val(btn.data('size_in') || '');
        //         $('#editQtyIn').val(btn.data('quantity_in') || '');
        //     }

        //     // Client & Site OUT
        //     let clientOutId = btn.data('client_out_id');
        //     let siteOutId = btn.data('site_out_id');

        //     if (clientOutId) {
        //         $('#editClientOut').val(clientOutId).trigger('change');
        //     }

        //     if (siteOutId) {
        //         $('#editBillboardOut').val(siteOutId).trigger('change');

        //         // Set type, size, and quantity for OUT
        //         $('#editTypeOut').val(btn.data('type_out') || '');
        //         $('#editSizeOut').val(btn.data('size_out') || '');
        //         $('#editQtyOut').val(btn.data('quantity_out') || '');
        //     }

        //     // Open modal
        //     openAltEditorModal("#inventoryEditModal");
        // });

        // $(document).on('click', '.edit-inventory', function () {
        //     let transactionId = $(this).data('transaction-in-id');

        //     $.get(`/inventory/${transactionId}/edit`, function (data) {
        //         // Populate modal fields
        //         $('#editContractorId').val(data.contractor_id);
        //         $('#editContractorName').val(data.contractor_name);
        //         $('#editBalanceContractor').val(data.balance_contractor);
        //         $('#editBalanceBgoc').val(data.balance_bgoc);

        //         $('#editDateIn').val(data.date_in);
        //         $('#editClientIn').val(data.client_in_id);
        //         $('#editSiteIn').val(data.site_in_id);
        //         $('#editTypeIn').val(data.type_in);
        //         $('#editSizeIn').val(data.size_in);
        //         $('#editQuantityIn').val(data.quantity_in);
        //         $('#editRemarksIn').val(data.remarks_in);

        //         $('#editDateOut').val(data.date_out);
        //         $('#editClientOut').val(data.client_out_id);
        //         $('#editSiteOut').val(data.site_out_id);
        //         $('#editTypeOut').val(data.type_out);
        //         $('#editSizeOut').val(data.size_out);
        //         $('#editQuantityOut').val(data.quantity_out);
        //         $('#editRemarksOut').val(data.remarks_out);

        //         // Show modal
        //         $('#inventoryEditModal').modal('show');
        //     });
        // });

        $(document).on('click', '.edit-inventory', function () {
            stockInventoryId = $(this).data('stock-inventory-id');
            transactionInId  = $(this).data('transaction-in-id') || null;
            transactionOutId = $(this).data('transaction-out-id') || null;

            $.get(`/inventory/${stockInventoryId}/edit`, {
                transaction_in_id: transactionInId,
                transaction_out_id: transactionOutId
            }, function (data) {
                // Contractor & balances (take from IN if exists, otherwise OUT)
                let source = data.in || data.out;
                if (source) {
                    $('#editContractorName').val(source.contractor_name || '');
                    $('#editBalanceContractor').val(source.balance_contractor || 0);
                    $('#editBalanceBgoc').val(source.balance_bgoc || 0);
                }

                // ✅ Format helper for date inputs
                function formatDateForInput(dateStr) {
                    if (!dateStr) return '';
                    const parts = dateStr.split('/');
                    if (parts.length !== 3) return '';
                    const day = parts[0].padStart(2, '0');
                    const month = parts[1].padStart(2, '0');
                    const year = '20' + parts[2]; // "25" → "2025"
                    return `${year}-${month}-${day}`;
                }

                // Populate IN fields
                if (data.in) {
                    $('#editDateIn').val(data.in.transaction_date || '');
                    $('#editClientIn').val(data.in.client_id).trigger('change');
                    $('#editBillboardIn').val(data.in.billboard_id).trigger('change');
                    $('#editTypeIn').val(data.in.type || '');
                    $('#editSizeIn').val(data.in.size || '');
                    $('#editQtyIn').val(data.in.quantity || '');
                    $('#editRemarksIn').val(data.in.remarks || '');
                } else {
                    $('#editDateIn, #editClientIn, #editBillboardIn, #editTypeIn, #editSizeIn, #editQtyIn, #editRemarksIn')
                        .val('')
                        .trigger('change');
                }

                // Populate OUT fields
                if (data.out) {
                    $('#editDateOut').val(data.out.transaction_date || '');
                    $('#editClientOut').val(data.out.client_id).trigger('change');
                    $('#editBillboardOut').val(data.out.billboard_id).trigger('change');
                    $('#editTypeOut').val(data.out.type || '');
                    $('#editSizeOut').val(data.out.size || '');
                    $('#editQtyOut').val(data.out.quantity || '');
                    $('#editRemarksOut').val(data.out.remarks || '');
                } else {
                    $('#editDateOut, #editClientOut, #editBillboardOut, #editTypeOut, #editSizeOut, #editQtyOut, #editRemarksOut')
                        .val('')
                        .trigger('change');
                }

                // ✅ Open modal
                openAltEditorModal("#inventoryEditModal");
            });
        });

        // Auto-fill billboard size/type on change
        $(document).on('change', '#editBillboardIn', function() {
            let selected = $(this).find(':selected');
            $('#editTypeIn').val(selected.data('type') || '');
            $('#editSizeIn').val(selected.data('size') || '');
        });

        $(document).on('change', '#editBillboardOut', function() {
            let selected = $(this).find(':selected');
            $('#editTypeOut').val(selected.data('type') || '');
            $('#editSizeOut').val(selected.data('size') || '');
        });


        // ✅ FIXED FORM SUBMISSION HANDLER
        $('#inventoryEditForm').off('submit').on('submit', function(e) {
            e.preventDefault();

            if (!transactionInId && !transactionOutId) {
                alert('No transaction selected for editing');
                return;
            }

            let formData = {
                _token: $('meta[name="csrf-token"]').attr('content'),
                stock_inventory_id: stockInventoryId,
                transaction_in_id: transactionInId,
                transaction_out_id: transactionOutId,

                contractor_id: $('#editContractorName').val(),
                date_in: $('#editDateIn').val(),
                date_out: $('#editDateOut').val(),
                remarks_in: $('#editRemarksIn').val(),
                remarks_out: $('#editRemarksOut').val(),
                client_in: $('#editClientIn').val(),
                site_in: $('#editBillboardIn').val(),
                type_in: $('#editTypeIn').val(),
                size_in: $('#editSizeIn').val(),
                qty_in: $('#editQtyIn').val(),
                client_out: $('#editClientOut').val(),
                site_out: $('#editBillboardOut').val(),
                type_out: $('#editTypeOut').val(),
                size_out: $('#editSizeOut').val(),
                qty_out: $('#editQtyOut').val(),
                balance_contractor: $('#editBalanceContractor').val(),
                balance_bgoc: $('#editBalanceBgoc').val()
            };

            $.ajax({
                url: "<?php echo e(route('stockInventory.edit')); ?>",
                type: "POST",
                data: formData,
                success: function(response) {
                    closeAltEditorModal('#inventoryEditModal');
                    window.showSubmitToast("Successfully updated.", "#91C714");
                    $('#inventory_table').DataTable().ajax.reload();
                },
                error: function(xhr) {
                    console.error('Update error:', xhr.responseText);
                    window.showSubmitToast("Update failed!", "#D32929");
                }
            });
        });

        setupAutoFilter();
        var filterClientCompany;

        // When "filterClientButton" button is clicked, initiate filterClientCompany
        // function filterClientButton() {
        //     filterClientCompany = document.getElementById("fliterClient").value;
        //     // initStockInventoryDatatable(filterClientCompany);
        // };

        // When page first loads, load tables
        // filterClientButton();

        // Store the ID of the last clicked modal when it's triggered
        (function() {
            $(document).on('click', "[data-toggle='modal']", function() {
                lastClickedLink = $(this).attr('id');
            });
        })();

        // Delete Client Company
        function inventoryDeleteButton() {
            var id = lastClickedLink.split("-")[1];

            $.ajax({
                type: 'POST',
                url: "<?php echo e(route('stockInventory.delete')); ?>",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    id: id,
                },
                success: function (response) {
                    // Close modal after successfully deleted
                    var element = "#inventoryDeleteModal";
                    closeAltEditorModal(element);

                    // Show successful toast
                    window.showSubmitToast("Successfully deleted.", "#91C714");

                    // Reload table
                    $('#inventory_table').DataTable().ajax.reload();
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

<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Gjlang\kl_guide_tracker\resources\views\stockInventory\index.blade.php ENDPATH**/ ?>