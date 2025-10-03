 

<?php $__env->startSection('title'); ?>
<title>BGOC Outdoor System - Billboard Master</title>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<head>
    <!-- ... other head content ... -->
    <title><?php echo $__env->yieldContent('title', 'Default Title'); ?></title> <!-- Example -->

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
    </style>

    <!-- Add jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <!-- Include DataTables JS (if not already included elsewhere) -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script> <!-- If using Bootstrap theme -->

    <!-- Include other necessary scripts -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?> <!-- If using Vite -->
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
    <div class="flex flex-col sm:flex-row sm:items-end xl:items-start">
        <form class="xl:flex flex-col sm:mr-auto">
            <div class="sm:flex items-center sm:mr-4 mb-2">
                <label class="w-12 flex-none xl:w-auto xl:flex-initial mr-2">Status</label>
                <select class="input w-full sm:w-32 xxl:w-full mt-2 sm:mt-0 sm:w-auto border" id="filterBillboardStatus">
                    <option value="all">All</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
            <?php if(Auth::guard('web')->check() && Auth::guard('web')->user()->hasRole(['superadmin', 'admin'])): ?>
            <!-- Row 1: State & District -->
            <div class="sm:flex items-center mb-2">
                <div class="sm:flex items-center sm:mr-4">
                    <label class="w-12 flex-none xl:w-auto xl:flex-initial mr-2">State</label>
                    <select class="input w-full sm:w-32 xxl:w-full mt-2 sm:mt-0 sm:w-auto border" id="filterBillboardState">
                        <option value="all">All</option>
                        <?php $__currentLoopData = $states; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($state->id); ?>"><?php echo e($state->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="sm:flex items-center sm:mr-4">
                    <label class="w-12 flex-none xl:w-auto xl:flex-initial mr-2">District</label>
                    <select class="input w-full sm:w-32 xxl:w-full mt-2 sm:mt-0 sm:w-auto border" id="filterBillboardDistrict">
                        <option value="all">All</option>
                        <?php $__currentLoopData = $districts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $district): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($district->id); ?>"><?php echo e($district->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
            <!-- Row 2: Type, New/Existing, Size -->
            <div class="sm:flex items-center mb-2">
                <div class="sm:flex items-center sm:mr-4 mb-2 sm:mb-0">
                    <label class="w-12 flex-none xl:w-auto xl:flex-initial mr-2">Type</label>
                    <select class="input w-full sm:w-32 xxl:w-full mt-2 sm:mt-0 sm:w-auto border" id="filterBillboardType">
                        <option value="all">All</option>
                        <?php $__currentLoopData = $billboardTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($type); ?>"><?php echo e($type); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="sm:flex items-center sm:mr-4 mb-2 sm:mb-0">
                    <label class="w-12 flex-none xl:w-auto xl:flex-initial mr-2">New/Existing</label>
                    <select class="input w-full sm:w-32 xxl:w-full mt-2 sm:mt-0 sm:w-auto border" id="filterBillboardSiteType">
                        <option value="all">All</option>
                        <option value="new">New</option>
                        <option value="existing">Existing</option>
                        <option value="rejected">Rejected</option>
                        <option value="existing_1">Existing 1</option>
                        <option value="existing_2">Existing 2</option>
                        <option value="existing_3">Existing 3</option>
                    </select>
                </div>
                <div class="sm:flex items-center sm:mr-4">
                    <label class="w-12 flex-none xl:w-auto xl:flex-initial mr-2">Size</label>
                    <select class="input w-full sm:w-32 xxl:w-full mt-2 sm:mt-0 sm:w-auto border" id="filterBillboardSize">
                        <option value="all">All</option>
                        <?php $__currentLoopData = $billboardSize; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $size): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($size); ?>"><?php echo e($size); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
            <?php endif; ?>
        </form>
        <div class="text-center">
            <!-- Buttons remain the same -->
            <a href="javascript:;" data-toggle="modal" data-target="#addBillboardModal" class="btn-secondary h-11 w-[200px] text-left">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus w-4 h-4">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Add New Stock
            </a>
            <br>
            <a href="<?php echo e(route('stockInventory.index')); ?>" class="btn-secondary h-11 w-[200px] text-left">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download w-4 h-4 mr-2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                    <polyline points="7 10 12 15 17 10" />
                    <line x1="12" y1="15" x2="12" y2="3" />
                </svg>
                Inventory
            </a>
            <br>
            <a href="#" id="exportBtnClient" class="btn-secondary h-11 w-[200px] text-left" target="_blank">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download w-4 h-4 mr-2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                    <polyline points="7 10 12 15 17 10" />
                    <line x1="12" y1="15" x2="12" y2="3" />
                </svg>
                Download Details [CLIENT]
            </a>
            <br>
            <a href="#" id="exportBtn" class="btn-secondary h-11 w-[200px] text-left" target="_blank">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download w-4 h-4 mr-2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                    <polyline points="7 10 12 15 17 10" />
                    <line x1="12" y1="15" x2="12" y2="3" />
                </svg>
                Download Details [INTERNAL]
            </a>
        </div>
    </div>
    <!-- END: Billboard Filter -->
    <!-- BEGIN: Billboard List -->
    <div class="overflow-x-auto">
        <table id="billboard_table" class="min-w-full border-collapse border border-neutral-300"> <!-- Add border classes to the table -->
            <thead class="bg-neutral-50 sticky top-0 z-10">
                <tr class="border-b border-neutral-300"> <!-- Add border to header row -->
                    <th class="px-4 py-4 table-header border-r border-neutral-300"><input type="checkbox" id="select-all-billboards"></th> <!-- Add border to header cell -->
                    <th class="px-4 py-4 table-header border-r border-neutral-300">No</th> <!-- Add border to header cell -->
                    <th class="px-4 py-4 table-header min-w-[180px] w-[180px] border-r border-neutral-300">Site #</th> <!-- Add border to header cell -->
                    <th class="px-4 py-4 table-header border-r border-neutral-300">New/Existing</th> <!-- Add border to header cell -->
                    <th class="px-4 py-4 table-header min-w-[100px] w-[100px] border-r border-neutral-300">Type</th> <!-- Add border to header cell -->
                    <th class="px-4 py-4 table-header min-w-[100px] w-[100px] border-r border-neutral-300">Size</th> <!-- Add border to header cell -->
                    <th class="px-4 py-4 table-header min-w-[100px] border-r border-neutral-300">Lighting</th> <!-- Add border to header cell -->
                    <th class="px-4 py-4 table-header min-w-[350px] border-r border-neutral-300">Location</th> <!-- Add border to header cell -->
                    <th class="px-4 py-4 table-header min-w-[200px] border-r border-neutral-300">District</th> <!-- Add border to header cell -->
                    <th class="px-4 py-4 table-header min-w-[160px] hidden border-r border-neutral-300">GPS Coordinate</th> <!-- Add border to header cell -->
                    <th class="px-4 py-4 table-header min-w-[200px] dt-exclude-export dt-no-sort border-r border-neutral-300">Show Detail</th> <!-- Add border to header cell -->
                    <th class="px-4 py-4 table-header min-w-[160px] dt-exclude-export dt-no-sort">Actions</th> <!-- Last header cell, no right border if you don't want it -->
                </tr>
            </thead>
            <tbody id="billboard_tbody" class="bg-white divide-y divide-neutral-200"> <!-- 'divide-y' adds borders between rows -->
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
<div class="row flex flex-col sm:flex-row sm:items-end xl:items-start mb-2">
    <div class="modal" id="addBillboardModal">
        <div class="modal__content">
            <div class="flex items-center px-5 py-5 sm:py-3 border-b border-gray-200 dark:border-dark-5">
                <h2 class="font-medium text-base mr-auto">Add New Stock</h2>
            </div>
            <form>
                <div class="p-5 grid grid-cols-12 gap-4 gap-y-3">
                    <div class="col-span-12 sm:col-span-12">
                        <label>Outdoor Type <span style="color: red;">*</span></label>
                        <select class="input w-full sm:w-32 xxl:w-full mt-2 sm:mt-0 sm:w-auto border" id="inputBillboardType" required>
                            <option value="">-- Select Outdoor Type --</option>
                            <option value="BB">Billboard</option>
                            <option value="TB">Tempboard</option>
                            <option value="BU">Bunting</option>
                            <option value="BN">Banner</option>
                        </select>
                    </div>
                    <div class="col-span-12 sm:col-span-12">
                        <label>Size (H)'x(W)' <span style="color: red;">*</span></label>
                        <select class="input w-full sm:w-32 xxl:w-full mt-2 sm:mt-0 sm:w-auto border" id="inputBillboardSize" required>
                            <option value="">-- Select Size --</option>
                            <option value="10x10">10x10</option>
                            <option value="15x10">15x10</option>
                            <option value="30x20">30x20</option>
                            <option value="10x40">10x40</option>
                            <option value="6x3">6x3</option>
                            <option value="7x3">7x3</option>
                            <option value="8x3">8x3</option>
                        </select>
                    </div>
                    <div class="col-span-12 sm:col-span-12">
                        <label>Lighting <span style="color: red;">*</span></label>
                        <select class="input w-full sm:w-32 xxl:w-full mt-2 sm:mt-0 sm:w-auto border" id="inputBillboardLighting" required>
                            <option value="">-- Select Lighting --</option>
                            <option value="None">None</option>
                            <option value="TNB">TNB</option>
                            <option value="SOLAR">SOLAR</option>
                        </select>
                    </div>
                    <!-- Separator -->
                    <div class="col-span-12">
                        <hr class="my-6 border-t-4 border-black">
                    </div>
                    <div class="col-span-12 sm:col-span-12">
                        <label>State <span style="color: red;">*</span></label>
                        <select class="input w-full sm:w-32 xxl:w-full mt-2 sm:mt-0 sm:w-auto border" id="inputBillboardState" required>
                            <option value="">-- Select State --</option>
                            <?php $__currentLoopData = $states; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($state->id); ?>"><?php echo e($state->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-span-12 sm:col-span-12">
                        <label>District <span style="color: red;">*</span></label>
                        <select class="input w-full sm:w-32 xxl:w-full mt-2 sm:mt-0 sm:w-auto border" id="inputBillboardDistrict" required>
                            <option value="">-- Select District --</option>
                        </select>
                    </div>
                    <div class="col-span-12 sm:col-span-12">
                        <label>Council <span style="color: red;">*</span></label>
                        <select class="input w-full sm:w-32 xxl:w-full mt-2 sm:mt-0 sm:w-auto border" id="inputBillboardCouncil" required>
                            <option value="">-- Select Council --</option>
                        </select>
                    </div>
                    <div class="col-span-12 sm:col-span-12">
                        <label>Location <span style="color: red;">*</span></label>
                        <input type="text" class="input w-full sm:w-32 xxl:w-full mt-2 sm:mt-0 sm:w-auto border" id="inputBillboardLocation" placeholder="Enter location name" required>
                    </div>
                    <div class="col-span-12 sm:col-span-12">
                        <label>State/Private Land <span style="color: red;">*</span></label>
                        <select class="input w-full sm:w-32 xxl:w-full mt-2 sm:mt-0 sm:w-auto border" id="inputBillboardLand" required>
                            <option value="">-- Select option --</option>
                            <option value="A">A - State Land</option>
                            <option value="B">B - Private Land</option>
                            <option value="C">C - KKR</option>
                            <option value="D">D - Others</option>
                        </select>
                    </div>
                    <!-- Separator -->
                    <div class="col-span-12">
                        <hr class="my-6 border-t-4 border-black">
                    </div>
                    <div class="col-span-12 sm:col-span-12">
                        <label for="inputGPSCoordinate" class="form-label">GPS Coordinate <span style="color: red;">*</span></label>
                        <input
                            type="text"
                            class="input w-full border mt-2 flex-1"
                            id="inputGPSCoordinate"
                            name="gps_coordinate"
                            pattern="^-?([0-8]?\d(\.\d+)?|90(\.0+)?),\s*-?(1[0-7]\d(\.\d+)?|180(\.0+)?)$"
                            placeholder="e.g. 3.1390, 101.6869"
                            required>
                        <small class="text-gray-500">Format: latitude (-90 → 90), longitude (-180 → 180)</small>
                    </div>
                    <!-- New field for Google Maps URL -->
                    <div class="col-span-12 sm:col-span-12">
                        <label for="inputMapsUrl" class="form-label">Google Maps Link</label>
                        <input
                            type="url"
                            class="input w-full border mt-2 flex-1"
                            id="inputMapsUrl"
                            name="gps_url"
                            placeholder="Paste Google Maps share link (e.g. https://maps.app.goo.gl/xyz123)"
                            pattern="https:\/\/maps\.app\.goo\.gl\/[A-Za-z0-9]+">
                        <small class="text-gray-500">Paste the short link from Google Maps → Share → Copy Link</small>
                    </div>
                    <!-- Separator -->
                    <div class="col-span-12">
                        <hr class="my-6 border-t-4 border-black">
                    </div>
                    <div class="col-span-12 sm:col-span-12">
                        <label for="inputBillboardTrafficVolume" class="form-label">Traffic Volume</label>
                        <input
                            type="number"
                            class="input w-full border mt-2 flex-1"
                            id="inputBillboardTrafficVolume"
                            name="traffic_volume"
                            step="1"
                            placeholder="e.g. 50000"
                            required>
                    </div>
                    <div class="col-span-12 sm:col-span-12">
                        <label>Site Type</label>
                        <select class="input w-full sm:w-32 xxl:w-full mt-2 sm:mt-0 sm:w-auto border" id="inputBillboardSiteType">
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
                <div class="px-5 py-3 text-right border-t border-gray-200 dark:border-dark-5">
                    <button type="submit" class="button w-20 bg-theme-1 text-white" id="billboardAddButton">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Create Modal End -->
<!-- Edit Billboard Modal -->
<div class="row flex flex-col sm:flex-row sm:items-end xl:items-start mb-2">
    <div class="modal" id="billboardEditModal">
        <div class="modal__content">
            <div class="flex items-center px-5 py-5 sm:py-3 border-b border-gray-200 dark:border-dark-5">
                <h2 class="font-medium text-base mr-auto">Edit Stock</h2>
            </div>
            <form>
                <div class="p-5 grid grid-cols-12 gap-4 gap-y-3">
                    <div class="col-span-12 sm:col-span-12">
                        <input type="hidden" id="editBillboardModalId" name="id">
                        <label>Outdoor Type <span style="color: red;">*</span></label>
                        <select class="input w-full sm:w-32 xxl:w-full mt-2 sm:mt-0 sm:w-auto border" id="editBillboardType" disabled>
                            <option value="">-- Select Outdoor Type --</option>
                            <option value="BB">Billboard</option>
                            <option value="TB">Tempboard</option>
                            <option value="BU">Bunting</option>
                            <option value="BN">Banner</option>
                        </select>
                    </div>
                    <div class="col-span-12 sm:col-span-12">
                        <label>Billboard Size <span style="color: red;">*</span></label>
                        <select class="input w-full sm:w-32 xxl:w-full mt-2 sm:mt-0 sm:w-auto border" id="editBillboardSize" required>
                            <option value="">-- Select Size --</option>
                            <option value="10x10">10x10</option>
                            <option value="15x10">15x10</option>
                            <option value="30x20">30x20</option>
                            <option value="10x40">10x40</option>
                            <option value="6x3">6x3</option>
                            <option value="7x3">7x3</option>
                            <option value="8x3">8x3</option>
                        </select>
                    </div>
                    <div class="col-span-12 sm:col-span-12">
                        <label>Lighting <span style="color: red;">*</span></label>
                        <select class="input w-full sm:w-32 xxl:w-full mt-2 sm:mt-0 sm:w-auto border" id="editBillboardLighting" required>
                            <option value="">-- Select Lighting --</option>
                            <option value="None">None</option>
                            <option value="TNB">TNB</option>
                            <option value="SOLAR">SOLAR</option>
                        </select>
                    </div>
                    <!-- Separator -->
                    <div class="col-span-12">
                        <hr class="my-6 border-t-4 border-black">
                    </div>
                    <div class="col-span-12 sm:col-span-12">
                        <label>State <span style="color: red;">*</span></label>
                        <select class="input w-full sm:w-32 xxl:w-full mt-2 sm:mt-0 sm:w-auto border" id="editBillboardState" disabled>
                            <option value="">-- Select State --</option>
                            <?php $__currentLoopData = $states; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($state->id); ?>"><?php echo e($state->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-span-12 sm:col-span-12">
                        <label>District <span style="color: red;">*</span></label>
                        <select class="input w-full sm:w-32 xxl:w-full mt-2 sm:mt-0 sm:w-auto border" id="editBillboardDistrict" required>
                            <option value="">-- Select District --</option>
                        </select>
                    </div>
                    <div class="col-span-12 sm:col-span-12">
                        <label>Council <span style="color: red;">*</span></label>
                        <select class="input w-full sm:w-32 xxl:w-full mt-2 sm:mt-0 sm:w-auto border" id="editBillboardCouncil" disabled>
                            <option value="">-- Select Council --</option>
                        </select>
                    </div>
                    <div class="col-span-12 sm:col-span-12">
                        <label>Location <span style="color: red;">*</span></label>
                        <input type="text" class="input w-full sm:w-32 xxl:w-full mt-2 sm:mt-0 sm:w-auto border" id="editBillboardLocation" placeholder="Enter location name">
                    </div>
                    <!-- Separator -->
                    <div class="col-span-12">
                        <hr class="my-6 border-t-4 border-black">
                    </div>
                    <div class="col-span-12 sm:col-span-12">
                        <label for="editGPSCoordinate" class="form-label">GPS Coordinate <span style="color: red;">*</span></label>
                        <input
                            type="text"
                            class="input w-full border mt-2 flex-1"
                            id="editGPSCoordinate"
                            name="gps_coordinate"
                            placeholder="e.g. 3.1390, 101.6869"
                            required>
                        <small class="text-gray-500">Format: latitude, longitude</small>
                    </div>
                    <div class="col-span-12 sm:col-span-12">
                        <label for="editGPSUrl" class="form-label">GPS URL (Google Maps)</label>
                        <input
                            type="url"
                            class="input w-full border mt-2 flex-1"
                            id="editGPSUrl"
                            name="gps_url"
                            placeholder="https://maps.app.goo.gl/xyz123">
                        <small class="text-gray-500">Example: https://maps.app.goo.gl/xxxxx</small>
                    </div>
                    <!-- Separator -->
                    <div class="col-span-12">
                        <hr class="my-6 border-t-4 border-black">
                    </div>
                    <div class="col-span-12 sm:col-span-12">
                        <label>Traffic Volume</label>
                        <input type="text" class="input w-full border mt-2 flex-1" id="editBillboardTrafficVolume" value="" required>
                    </div>
                    <div class="col-span-12 sm:col-span-12">
                        <label>Site Type</label>
                        <select class="input w-full sm:w-32 xxl:w-full mt-2 sm:mt-0 sm:w-auto border" id="editBillboardSiteType">
                            <option value="">-- Select option --</option>
                            <option value="new">New</option>
                            <option value="existing">Existing</option>
                            <option value="rejected">Rejected</option>
                            <option value="existing_1">Existing 1</option>
                            <option value="existing_2">Existing 2</option>
                            <option value="existing_3">Existing 3</option>
                        </select>
                    </div>
                    <div class="col-span-12 sm:col-span-12">
                        <label>Status</label>
                        <select class="input w-full sm:w-32 xxl:w-full mt-2 sm:mt-0 sm:w-auto border" id="editBillboardStatus">
                            <option value="">-- Select option --</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="px-5 py-3 text-right border-t border-gray-200 dark:border-dark-5">
                    <button type="submit" class="button w-20 bg-theme-1 text-white" id="billboardEditButton">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Edit Modal End -->
<!-- BEGIN: Billboard Delete Modal -->
<div class="modal" id="billboardDeleteModal">
    <div class="modal__content">
        <div class="p-5 text-center"> <i data-feather="x-circle" class="w-16 h-16 text-theme-6 mx-auto mt-3"></i>
            <div class="text-3xl mt-5">Are you sure?</div>
            <div class="text-gray-600 mt-2">Confirm delete this billboard info? This process cannot be undone.</div>
        </div>
        <div class="px-5 pb-8 text-center">
            <button type="button" data-dismiss="modal" class="button w-24 border text-gray-700 dark:border-dark-5 dark:text-gray-300 mr-1">Cancel</button>
            <button type="button" class="button w-24 bg-theme-6 text-white" id="billboardDeleteButton" onclick="billboardDeleteButton()">Delete</button>
        </div>
    </div>
</div>
<!-- END: Service Request Reject Modal -->

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
                url: '<?php echo e(route("location.getAllDistricts")); ?>',
                type: 'GET',
                success: function(districts) {
                    districts.forEach(function(district) {
                        $('#filterBillboardDistrict').append(`<option value="${district.id}">${district.name}</option>`);
                    });
                },
                error: function() {
                    alert('Failed to load all districts.');
                }
            });
        } else {
            $.ajax({
                url: '<?php echo e(route("location.getDistricts")); ?>',
                type: 'POST',
                data: {
                    _token: '<?php echo e(csrf_token()); ?>',
                    state_id: stateId
                },
                success: function(districts) {
                    districts.forEach(function(district) {
                        $('#filterBillboardDistrict').append(`<option value="${district.id}">${district.name}</option>`);
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
        let exportUrl = '<?php echo e(route("billboards.export.pdf")); ?>' + '?' + query;
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
        let exportUrl = '<?php echo e(route("billboards.export.pdf.client")); ?>' + '?' + query; // Fixed route
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
        $('#filterBillboardStatus, #filterBillboardState, #filterBillboardDistrict, #filterBillboardType, #filterBillboardSiteType, #filterBillboardSize').on('change', function() {
            table.ajax.reload();
        });
    }
    $(document).ready(function() {
        // Global variables
        var filterBillboardStatus;
        document.getElementById("billboardDeleteButton").addEventListener("click", billboardDeleteButton);
        // Initialize Select2 with search
        $('.select2').select2({
            placeholder: "Select an option",
            allowClear: true,
            width: '100%'
        });
        $('#inputBillboardDistrict').select2({
            tags: true,
            placeholder: "-- Select or Add District --",
            allowClear: true,
        });
        $('#editBillboardDistrict').select2({
            tags: true, // ✅ allow typing new values
            placeholder: "-- Select or Type District --",
            allowClear: true,
            width: '100%'
        });
        // When "State" is changed in add form
        $('#inputBillboardState').on('change', function() {
            let stateId = $(this).val();
            // Reset District & Council
            $('#inputBillboardDistrict').empty().append('<option value="">-- Select District --</option>');
            $('#inputBillboardCouncil').empty().append('<option value="">-- Select Council --</option>');
            if (stateId !== '') {
                $.ajax({
                    url: '<?php echo e(route("location.getDistricts")); ?>',
                    type: 'POST',
                    data: {
                        _token: '<?php echo e(csrf_token()); ?>',
                        state_id: stateId
                    },
                    success: function(districts) {
                        districts.forEach(function(district) {
                            let option = new Option(district.name, district.id, false, false);
                            $('#inputBillboardDistrict').append(option);
                        });
                        // Refresh select2 options after AJAX load
                        $('#inputBillboardDistrict').trigger('change');
                    },
                    error: function() {
                        alert('Failed to load districts.');
                    }
                });
            }
        });
        // When district changes → load councils
        $('#inputBillboardDistrict').on('change', function() {
            let stateId = $('#inputBillboardState').val();
            $('#inputBillboardCouncil').empty().append('<option value="">-- Select Council --</option>');
            if (stateId !== '') {
                $.ajax({
                    url: '<?php echo e(route("location.getCouncils")); ?>',
                    type: 'POST',
                    data: {
                        _token: '<?php echo e(csrf_token()); ?>',
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
        document.getElementById("billboardAddButton").addEventListener("click", function(e) {
            e.preventDefault();
            billboardAddButton();
        });
        //Store the ID of the last clicked update status modal when it's triggered
        (function() {
            $(document).on('click', "[data-target='#workOrderStatusUpdateModal'], [data-target='#workOrderAssignTcModal']", function() {
                originalWorkOrderIdStatusUpdate = ($(this).attr('id')).split("-")[2];
                // console.log(originalWorkOrderIdStatusUpdate);
            });
            document.getElementById('billboardDeleteButton').addEventListener('click', function(e) {
                // Prevent the default form submission behavior
                e.preventDefault();
            });
        })();
        // Store the ID of the last clicked modal when it's triggered
        (function() {
            $(document).on('click', "[data-toggle='modal']", function() {
                lastClickedLink = $(this).attr('id');
                // Grab row client id
                originalWorkOrderId = $(event.target).closest('tr').find('td:nth-child(9) a').attr('id').split("-")[1];
                console.log(originalWorkOrderId);
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
        // Setup the on-going Billboard datatable
        function initBillboardDatatable() {
            const dt = new Date();
            const formattedDate = `${dt.getFullYear()}${(dt.getMonth() + 1).toString().padStart(2, '0')}${dt.getDate().toString().padStart(2, '0')}`;
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
                    [7, 'desc'] // Assuming 'region' (which uses location_name) is the 8th column (0-indexed)
                ],
                pagingType: 'simple_numbers',
                pageLength: 25,
                aLengthMenu: [
                    [25, 50, 75, -1],
                    [25, 50, 75, "All"]
                ],
                iDisplayLength: 25,
                ajax: {
                    url: "<?php echo e(route('billboard.list')); ?>",
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
                            action: "<?php echo e(route('billboards.export')); ?>"
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
                        document.querySelectorAll('.billboard-checkbox').forEach(cb => cb.checked = false);
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
                            var a = "<?php echo e(route('billboard.detail', ['id'=>':data'] )); ?>".replace(':data', data);
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
                            let element = `
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
                                <a class="flex items-center text-theme-6" href="javascript:;" data-toggle="modal" data-target="#billboardDeleteModal" id="delete-billboard-` + data + `">
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
                    locationCell.removeClass('text-center').addClass('text-left'); // Remove center, add left

                    // Add padding to the location cell for better readability
                    locationCell.addClass('px-4 py-2'); // Add horizontal and vertical padding

                    // Find the cell corresponding to the 'Show Detail' column (index 10) and ensure it's center-aligned
                    const showDetailCellIndex = 10;
                    const showDetailCell = $(row).find('td').eq(showDetailCellIndex);
                    showDetailCell.removeClass('text-left').addClass('text-center'); // Ensure center

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
                        .addClass("inline-flex items-center justify-center px-2 py-1 border rounded text-xs");

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
                        paginateDiv.classList.add('flex', 'items-center', 'justify-center', 'space-x-2', 'mt-2');

                        // Style the individual page links
                        $(paginateDiv).find('a').addClass('px-3', 'py-1', 'border', 'border-gray-300', 'rounded', 'hover:bg-gray-100', 'focus:outline-none', 'focus:ring-2', 'focus:ring-blue-500', 'focus:border-blue-500');
                        $(paginateDiv).find('span').addClass('px-3', 'py-1', 'bg-gray-200', 'border', 'border-gray-300', 'rounded', 'font-bold');

                        // Style the "First", "Previous", "Next", "Last" links
                        $(paginateDiv).find('a').filter(function() {
                            return $(this).text().trim() === 'First' || $(this).text().trim() === 'Previous' || $(this).text().trim() === 'Next' || $(this).text().trim() === 'Last';
                        }).addClass('px-3', 'py-1', 'border', 'border-gray-300', 'rounded', 'hover:bg-gray-100', 'focus:outline-none', 'focus:ring-2', 'focus:ring-blue-500', 'focus:border-blue-500');
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
            var paginateDiv = document.getElementById("billboard_table_paginate"); // DataTables auto-generates this ID
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
            $.post('<?php echo e(route("location.getDistricts")); ?>', {
                _token: '<?php echo e(csrf_token()); ?>',
                state_id: stateID
            }, function(districts) {
                $('#editBillboardDistrict').empty().append(`<option value="">-- Select District --</option>`);
                districts.forEach(function(d) {
                    $('#editBillboardDistrict').append(`<option value="${d.id}">${d.name}</option>`);
                });
                // ✅ if districtID is not in list (user had custom one before), add it manually
                if (districtID && !$('#editBillboardDistrict option[value="' + districtID + '"]').length) {
                    $('#editBillboardDistrict').append(
                        `<option value="${districtID}" selected>${districtID}</option>`
                    );
                }
                $('#editBillboardDistrict').val(districtID).trigger('change');
                // ✅ Fetch councils after districts load
                $.post('<?php echo e(route("location.getCouncils")); ?>', {
                    _token: '<?php echo e(csrf_token()); ?>',
                    state_id: stateID
                }, function(councils) {
                    $('#editBillboardCouncil').empty().append(`<option value="">-- Select Council --</option>`);
                    councils.forEach(function(c) {
                        $('#editBillboardCouncil').append(`<option value="${c.id}">${c.name} (${c.abbreviation})</option>`);
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
                    let options = '<option value="">-- Select District --</option>';
                    data.forEach(function(district) {
                        options += `<option value="${district.id}">${district.name}</option>`;
                    });
                    $('#editBillboardDistrict').html(options);
                });
                // councils
                $.get('/get-councils/' + stateID, function(data) {
                    let options = '<option value="">-- Select Council --</option>';
                    data.forEach(function(c) {
                        options += `<option value="${c.id}">${c.abbreviation} - ${c.name} </option>`;
                    });
                    $('#editBillboardCouncil').html(options);
                });
            }
        });
        // 🔄 On District change => fetch locations
        $('#editBillboardDistrict').on('change', function() {
            let districtID = $(this).val();
            $('#editBillboardLocation').html('<option value="">-- Loading Locations --</option>');
            if (districtID) {
                $.get('/get-locations/' + districtID, function(data) {
                    let options = '<option value="">-- Select Location --</option>';
                    data.forEach(function(location) {
                        options += `<option value="${location.id}">${location.name}</option>`;
                    });
                    $('#editBillboardLocation').html(options);
                });
            }
        });
        // Add New Billboard
        function billboardAddButton() {
            $.ajax({
                type: 'POST',
                url: "<?php echo e(route('billboard.create')); ?>",
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
                    $('#inputBillboardDistrict').val(null).trigger('change');
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
                url: '<?php echo e(route("billboard.update")); ?>',
                method: 'POST',
                data: {
                    _token: '<?php echo e(csrf_token()); ?>',
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
                    document.getElementById('billboardEditButton').style.display = 'inline-block'; // Shows the button again
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
        function billboardDeleteButton() {
            var id = lastClickedLink.split("-")[2];
            $.ajax({
                type: 'POST',
                url: "<?php echo e(route('billboard.delete')); ?>",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    id: id,
                },
                success: function(response) {
                    // Close modal after successfully deleted
                    var element = "#billboardDeleteModal";
                    closeAltEditorModal(element);
                    // Show successful toast
                    window.showSubmitToast("Successfully deleted.", "#91C714");
                    // Reload table
                    $('#billboard_table').DataTable().ajax.reload();
                    // Reload the entire page
                    // location.reload();
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
        // Store the ID of the last clicked modal when it's triggered
        (function() {
            $(document).on('click', "[data-toggle='modal']", function() {
                lastClickedLink = $(this).attr('id');
            });
        })();
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Projects\Laravel\KL_tracker\resources\views\billboard\index.blade.php ENDPATH**/ ?>