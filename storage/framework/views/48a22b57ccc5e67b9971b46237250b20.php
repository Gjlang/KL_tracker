<?php $__env->startSection('title'); ?>
<title>BGOC Outdoor System - Billboard Details</title>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<style>
    .dz-remove {
        display: inline-block;
        margin-top: 0.5rem;
        font-size: 0.875rem;
        color: #f87171;
        cursor: pointer;
    }
    .dz-remove:hover {
        color: #b91c1c;
    }
    .dropzone {
        cursor: pointer;
    }

    .dropzone:hover {
        border-color: #3b82f6;
        background-color: #ebf5ff;
    }

    .dropzone.dragover {
        border-color: #10b981 !important;
        background-color: #ecfdf5 !important;
    }

    
</style>

<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="intro-y flex flex-col sm:flex-row items-center justify-between mb-8">
        <h2 class="text-2xl font-bold text-gray-800">
            Billboard Detail
        </h2>
        <div class="mt-4 sm:mt-0">
            <a href="<?php echo e(route('billboard.download', $billboard_detail->id)); ?>" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200 mr-2">
                <i class="fas fa-download mr-2"></i> Download PDF [INTERNAL]
            </a>
            <a href="<?php echo e(route('billboard.download.client', $billboard_detail->id)); ?>" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition duration-200 mr-2">
                <i class="fas fa-download mr-2"></i> Download PDF [CLIENT]
            </a>
            <?php
                $mapUrl = !empty($billboard_detail->gps_url)
                    ? $billboard_detail->gps_url
                    : "https://www.google.com/maps?q={$billboard_detail->gps_latitude},{$billboard_detail->gps_longitude}";
            ?>
            <a href="<?php echo e($mapUrl); ?>" target="_blank" rel="noopener noreferrer" 
            class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition duration-200 mr-2">
                <i class="fas fa-map-marked-alt mr-2"></i> Show on Maps
            </a>
            <a href="javascript:void(0)" onclick="populateBillboardEditModal(<?php echo e(json_encode($billboard_detail)); ?>)" class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition duration-200">
                <i class="fas fa-edit mr-2"></i> Edit
            </a>
        </div>
    </div>

    <!-- Billboard Details Card -->
    <div class="intro-y box bg-white rounded-xl shadow-lg p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Billboard Information</h3>
                <ul class="space-y-2">
                    <li class="flex">
                        <span class="w-48 font-medium text-gray-600">Site Number:</span>
                        <span class="text-gray-800"><?php echo e($billboard_detail->site_number); ?></span>
                    </li>
                    <li class="flex">
                        <span class="w-48 font-medium text-gray-600">Location:</span>
                        <span class="text-gray-800"><?php echo e($billboard_detail->location_name); ?></span>
                    </li>
                    <li class="flex">
                        <span class="w-48 font-medium text-gray-600">District/State:</span>
                        <span class="text-gray-800"><?php echo e($billboard_detail->district_name); ?>, <?php echo e($billboard_detail->state_name); ?></span>
                    </li>
                    <li class="flex">
                        <span class="w-48 font-medium text-gray-600">Council:</span>
                        <span class="text-gray-800"><?php echo e($billboard_detail->council_abbrv); ?> - <?php echo e($billboard_detail->council_name); ?></span>
                    </li>
                    <li class="flex">
                        <span class="w-48 font-medium text-gray-600">GPS Coordinate:</span>
                        <span class="text-gray-800"><?php echo e($billboard_detail->gps_latitude); ?>, <?php echo e($billboard_detail->gps_longitude); ?></span>
                    </li>
                </ul>
            </div>
            <div>
                <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Specifications</h3>
                <ul class="space-y-2">
                    <li class="flex">
                        <span class="w-48 font-medium text-gray-600">Traffic Volume:</span>
                        <span class="text-gray-800"><?php echo e($billboard_detail->traffic_volume); ?></span>
                    </li>
                    <li class="flex">
                        <span class="w-48 font-medium text-gray-600">Billboard Type:</span>
                        <span class="text-gray-800"><?php echo e($billboard_detail->prefix); ?> - <?php echo e($billboard_detail->type); ?></span>
                    </li>
                    <li class="flex">
                        <span class="w-48 font-medium text-gray-600">Size:</span>
                        <span class="text-gray-800"><?php echo e($billboard_detail->size); ?></span>
                    </li>
                    <li class="flex">
                        <span class="w-48 font-medium text-gray-600">Lighting:</span>
                        <span class="text-gray-800"><?php echo e($billboard_detail->lighting); ?></span>
                    </li>
                    <li class="flex">
                        <span class="w-48 font-medium text-gray-600">Status:</span>
                        <span class="text-gray-800"><?php echo e($billboard_detail->site_type ? strtoupper($billboard_detail->site_type) : '-'); ?></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Billboard Images Card -->
    <div class="intro-y box bg-white rounded-xl shadow-lg p-6">
        <h2 class="intro-y font-bold text-xl sm:text-2xl text-gray-800 mb-6">
            Billboard Site Images
        </h2>

        <?php
            $image1Exists = Storage::disk('public')->exists('billboards/' . $billboard_detail->site_number . '_1.png');
            $image2Exists = Storage::disk('public')->exists('billboards/' . $billboard_detail->site_number . '_2.png');
        ?>

        <div class="intro-y mt-6">
            <div class="flex flex-col md:flex-row gap-6">
                <!-- Image 1 Slot -->
                <div id="image-slot-1" class="flex-1 relative group h-86 overflow-hidden rounded-lg shadow bg-gray-100 border border-gray-200">
                    <?php if($image1Exists): ?>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <img src="<?php echo e(asset('storage/billboards/' . $billboard_detail->site_number . '_1.png')); ?>" 
                                class="w-full h-full object-contain max-h-96"
                                alt="Billboard Image 1">
                        </div>
                        <!-- Delete Button - Hidden by default, shown on hover -->
                        <button 
                            onclick="deleteImage('<?php echo e($billboard_detail->site_number); ?>_1.png', this)"
                            class="absolute top-2 right-2 text-white bg-red-500 hover:bg-red-600 px-3 py-1 rounded-full opacity-0 group-hover:opacity-100 pointer-events-none group-hover:pointer-events-auto transition-opacity duration-200 ease-in-out"
                            aria-label="Delete Image 1">
                            <i class="fas fa-trash"></i>
                        </button>
                    <?php else: ?>
                        <div class="flex flex-col items-center justify-center h-full p-4 text-center text-gray-500 bg-gray-50">
                            <i class="fas fa-image text-4xl mb-2"></i>
                            <p>No Image 1 Uploaded</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Image 2 Slot -->
                <div id="image-slot-2" class="flex-1 relative group h-86 overflow-hidden rounded-lg shadow bg-gray-100 border border-gray-200">
                    <?php if($image2Exists): ?>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <img src="<?php echo e(asset('storage/billboards/' . $billboard_detail->site_number . '_2.png')); ?>" 
                                class="w-full h-full object-contain max-h-96"
                                alt="Billboard Image 2">
                        </div>
                        <!-- Delete Button - Hidden by default, shown on hover -->
                        <button 
                            onclick="deleteImage('<?php echo e($billboard_detail->site_number); ?>_2.png', this)"
                            class="absolute top-2 right-2 text-white bg-red-500 hover:bg-red-600 px-3 py-1 rounded-full opacity-0 group-hover:opacity-100 pointer-events-none group-hover:pointer-events-auto transition-opacity duration-200 ease-in-out"
                            aria-label="Delete Image 2">
                            <i class="fas fa-trash"></i>
                        </button>
                    <?php else: ?>
                        <div class="flex flex-col items-center justify-center h-full p-4 text-center text-gray-500 bg-gray-50">
                            <i class="fas fa-image text-4xl mb-2"></i>
                            <p>No Image 2 Uploaded</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- File Upload Section -->
        <div class="intro-y mt-8 pt-6 border-t border-gray-200">
            <div class="border border-dashed border-gray-300 rounded-lg p-6 bg-gray-50">
                <h3 class="text-lg font-medium text-gray-800 mb-4">Upload New Images</h3>
                <p class="text-gray-600 mb-4">Drag & drop images here or click to browse. Maximum 2 images allowed.</p>
                <form id="fileUploadForm" action="<?php echo e(route('billboard.uploadImage')); ?>" method="POST" enctype="multipart/form-data" class="dropzone border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="site_number" value="<?php echo e($billboard_detail->site_number); ?>">
                    <!-- <div class="fallback">
                        <input name="files[]" id="fileInput" type="file" multiple accept="image/*" />
                    </div> -->
                    <div class="dz-message" data-dz-message>
                        <div class="text-lg font-medium text-gray-700">
                            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i><br>
                            Drop files here or click to upload.
                        </div>
                        <div class="text-gray-500">Only PNG, JPG, JPEG files. Max 10MB each.</div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



<!-- Edit Billboard Modal -->
<div class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50 modal" id="billboardEditModal">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl mx-4 overflow-hidden modal__content">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="font-bold text-xl text-gray-800">Edit Billboard</h2>
            <button type="button" onclick="closeAltEditorModal('#billboardEditModal')" class="text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-full p-1">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="billboardEditForm" action="<?php echo e(route('billboard.update')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <?php echo method_field('POST'); ?>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6 max-h-[70vh] overflow-y-auto">
                <div class="md:col-span-2">
                    <input type="hidden" id="editBillboardModalId" name="id">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Outdoor Type <span class="text-red-500">*</span></label>
                    <select class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition bg-gray-100 cursor-not-allowed" id="editBillboardType" name="type" disabled>
                        <option value="">-- Select Outdoor Type --</option>
                        <option value="BB">Billboard</option>
                        <option value="TB">Tempboard</option>
                        <option value="BU">Bunting</option>
                        <option value="BN">Banner</option>
                    </select>
                    <input type="hidden" id="editBillboardTypeHidden" name="type" value="">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Billboard Size <span class="text-red-500">*</span></label>
                    <select class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" id="editBillboardSize" name="size" required>
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
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lighting <span class="text-red-500">*</span></label>
                    <select class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" id="editBillboardLighting" name="lighting" required>
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

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">State <span class="text-red-500">*</span></label>
                    <select class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition bg-gray-100 cursor-not-allowed" id="editBillboardState" name="state_id" disabled>
                        <option value="">-- Select State --</option>
                        <?php $__currentLoopData = $states; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($state->id); ?>"><?php echo e($state->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <input type="hidden" id="editBillboardStateHidden" name="state_id" value="">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">District <span class="text-red-500">*</span></label>
                    <select class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" id="editBillboardDistrict" name="district_id" required>
                        <option value="">-- Select District --</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Council <span class="text-red-500">*</span></label>
                    <select class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition bg-gray-100 cursor-not-allowed" id="editBillboardCouncil" name="council_id" disabled>
                        <option value="">-- Select Council --</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Location <span class="text-red-500">*</span></label>
                    <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" id="editBillboardLocation" name="location_name" placeholder="Enter location name">
                </div>

                <!-- Separator -->
                <div class="md:col-span-2 py-2">
                    <hr class="border-t border-gray-300">
                </div>

                <div class="md:col-span-2">
                    <label for="editGPSCoordinate" class="block text-sm font-medium text-gray-700 mb-1">GPS Coordinate <span class="text-red-500">*</span></label>
                    <input
                        type="text"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                        id="editGPSCoordinate"
                        name="gps_coordinate"
                        placeholder="e.g. 3.1390, 101.6869"
                        required
                    >
                    <small class="text-gray-500">Format: latitude, longitude</small>
                </div>
                <div class="md:col-span-2">
                    <label for="editGPSUrl" class="block text-sm font-medium text-gray-700 mb-1">GPS URL (Google Maps)</label>
                    <input
                        type="url"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                        id="editGPSUrl"
                        name="gps_url"
                        placeholder="  https://maps.app.goo.gl/xyz123    "
                    >
                    <small class="text-gray-500">Example: https://maps.app.goo.gl/xxxxx    </small>
                </div>

                <!-- Separator -->
                <div class="md:col-span-2 py-2">
                    <hr class="border-t border-gray-300">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Traffic Volume</label>
                    <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" id="editBillboardTrafficVolume" name="traffic_volume" value="" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Site Type</label>
                    <select class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" id="editBillboardSiteType" name="site_type">
                        <option value="">-- Select option --</option>
                        <option value="new">New</option>
                        <option value="existing">Existing</option>
                        <option value="rejected">Rejected</option>
                        <option value="existing_1">Existing 1</option>
                        <option value="existing_2">Existing 2</option>
                        <option value="existing_3">Existing 3</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" id="editBillboardStatus" name="status">
                        <option value="">-- Select option --</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 text-right border-t border-gray-200">
                <button type="button" onclick="closeAltEditorModal('#billboardEditModal')" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition mr-2">
                    Cancel
                </button>
                <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition" id="billboardEditButton">
                    Submit
                </button>
            </div>
        </form>
    </div>
</div>
<!-- Edit Modal End -->
 <?php $__env->stopSection(); ?>


<?php $__env->startSection('scripts'); ?>
<!-- Add these CDN links before your script -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js"></script>

<script>
    // Define functions in the global scope so they're accessible to inline onclick
    function deleteImage(filename, button) {
        if(!confirm('Are you sure you want to delete this image?')) return;

        fetch('<?php echo e(route("billboard.deleteImage")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            },
            body: JSON.stringify({ filename: filename })
        })
        .then(response => {
            if(response.ok) return response.json();
            throw new Error('File not found');
        })
        .then(data => {
            // Find the parent slot container
            const slotContainer = button.closest('.flex-1'); // e.g., #image-slot-1 or #image-slot-2
            if (!slotContainer) {
                throw new Error("Slot container not found");
            }

            // Determine which slot it was to show the correct placeholder
            const slotId = slotContainer.id;
            let placeholderHtml = '';
            if (slotId === 'image-slot-1') {
                placeholderHtml = `
                    <div class="flex flex-col items-center justify-center h-full p-4 text-center text-gray-500 bg-gray-50">
                        <i class="fas fa-image text-4xl mb-2"></i>
                        <p>No Image 1 Uploaded</p>
                    </div>
                `;
            } else if (slotId === 'image-slot-2') {
                placeholderHtml = `
                    <div class="flex flex-col items-center justify-center h-full p-4 text-center text-gray-500 bg-gray-50">
                        <i class="fas fa-image text-4xl mb-2"></i>
                        <p>No Image 2 Uploaded</p>
                    </div>
                `;
            } else {
                // Fallback if slot ID doesn't match
                placeholderHtml = `
                    <div class="flex flex-col items-center justify-center h-full p-4 text-center text-gray-500 bg-gray-50">
                        <i class="fas fa-image text-4xl mb-2"></i>
                        <p>No Image Uploaded</p>
                    </div>
                `;
            }

            // Replace the content of the slot with the placeholder
            slotContainer.innerHTML = placeholderHtml;

            alert(data.message);
            // Optionally, you could reload the page here if you prefer
            // window.location.reload();
        })
        .catch(err => {
            console.error(err);
            alert('Error deleting image.');
        });
    }

    function populateBillboardEditModal(data) {

        // IDs
        let stateID    = data.state_id;
        let districtID = data.district_id;
        let councilID  = data.council_id;

        // Fill form fields
        $('#editBillboardModalId').val(data.id);
        
        // Set the visible select (disabled) to show current value
        $('#editBillboardType').val(data.prefix);
        
        // Set the hidden field with the actual value to be sent
        $('#editBillboardTypeHidden').val(data.prefix);
        
        $('#editBillboardSize').val(data.size);
        $('#editBillboardLighting').val(data.lighting);
        $('#editGPSCoordinate').val(data.gps_latitude + ', ' + data.gps_longitude);
        $('#editGPSUrl').val(data.gps_url);
        $('#editBillboardTrafficVolume').val(data.traffic_volume);
        $('#editBillboardStatus').val(data.status);
        $('#editBillboardSiteType').val(data.site_type);
        $('#editBillboardLocation').val(data.location_name);

        // Set the hidden state ID to send the value
        $('#editBillboardStateHidden').val(stateID);

        // Initialize Select2 for District field with tagging enabled
        $('#editBillboardDistrict').select2({
            tags: true,
            placeholder: "-- Select or Type District --",
            allowClear: true,
            width: '100%'
        });

        // Populate dependent dropdowns
        $('#editBillboardState').val(stateID).trigger('change');

        // Load Districts
        $.post('<?php echo e(route("location.getDistricts")); ?>', {
            _token: '<?php echo e(csrf_token()); ?>',
            state_id: stateID
        }, function (districts) {
            $('#editBillboardDistrict').empty().append(`<option value="">-- Select District --</option>`);
            districts.forEach(function (d) {
                $('#editBillboardDistrict').append(`<option value="${d.id}">${d.name}</option>`);
            });
            $('#editBillboardDistrict').val(districtID).trigger('change');
            $('#editBillboardDistrict').trigger('select2:select'); // Refresh Select2

            // Load Councils
            $.post('<?php echo e(route("location.getCouncils")); ?>', {
                _token: '<?php echo e(csrf_token()); ?>',
                state_id: stateID
            }, function (councils) {
                $('#editBillboardCouncil').empty().append(`<option value="">-- Select Council --</option>`);
                councils.forEach(function (c) {
                    $('#editBillboardCouncil').append(`<option value="${c.id}">${c.name} (${c.abbreviation})</option>`);
                });
                $('#editBillboardCouncil').val(councilID).trigger('change');
            });
        });

        // Open modal with delay to prevent scrollbar error
        openAltEditorModal('#billboardEditModal');
    }

    // Handle form submission when the submit button is clicked
    $(document).on('click', '#billboardEditButton', function (e) {
        e.preventDefault();
        
        // Validate required fields
        if (!validateForm()) {
            return;
        }

        $.ajax({
            url: '<?php echo e(route("billboard.update")); ?>',
            method: 'POST',
            data: $('#billboardEditForm').serialize(),
            success: function(response) {
                console.log("AJAX Success Handler Reached", response);

                // Ensure modal close function exists and call it
                if (typeof closeAltEditorModal === 'function') {
                    closeAltEditorModal('#billboardEditModal');
                } else {
                    console.error("closeAltEditorModal function is not defined!");
                }

                // Ensure toast function exists and call it
                if (typeof window.showSubmitToast !== 'undefined') {
                    window.showSubmitToast(response.message || "Successfully updated.", "#91C714");
                } else {
                    alert(response.message || "Successfully updated.");
                }

                console.log("About to reload page");
                // Ensure no other code prevents the reload
                setTimeout(() => {
                    location.reload();
                }, 100); // Small delay to ensure other operations finish
            },
            error: function(xhr, status, error) {
                // Display the validation error message
                console.log("AJAX Error", xhr.status, xhr.responseText);
                var response = xhr.responseJSON || {error: 'An error occurred'};
                var errorMessage = response.error || response.message || 'An unknown error occurred';

                if (typeof window.showSubmitToast !== 'undefined') {
                    window.showSubmitToast("Error: " + errorMessage, "#D32929");
                } else {
                    alert("Error: " + errorMessage);
                }
            }
        });
    });

    // Form validation function
    function validateForm() {
        let isValid = true;
        let errors = [];

        if (!$('#editBillboardModalId').val()) {
            errors.push('Billboard ID is required');
            isValid = false;
        }

        if (!$('#editBillboardType').val()) {
            errors.push('Outdoor Type is required');
            isValid = false;
        }

        if (!$('#editBillboardSize').val()) {
            errors.push('Billboard Size is required');
            isValid = false;
        }

        if (!$('#editBillboardLighting').val()) {
            errors.push('Lighting is required');
            isValid = false;
        }

        if (!$('#editGPSCoordinate').val()) {
            errors.push('GPS Coordinate is required');
            isValid = false;
        }

        if (errors.length > 0) {
            alert('Please fix the following errors:\n' + errors.join('\n'));
        }

        return isValid;
    }

    // Open modal
    function openAltEditorModal(selector) {
        const modal = document.querySelector(selector);
        if (modal) {
            modal.classList.remove('hidden'); // ✅ Remove 'hidden' class to show
            // Prevent body scroll when modal is open
            document.body.style.overflow = 'hidden';
        }
    }

    function closeAltEditorModal(selector) {
        const modal = document.querySelector(selector);
        if (modal) {
            modal.classList.add('hidden'); // ✅ Add 'hidden' class to hide
            // Re-enable body scroll
            document.body.style.overflow = '';
        }
    }

    // Wait for document ready and Dropzone to be available
    $(document).ready(function () {        
        // Initialize Select2 for District field with tagging enabled
        $('#editBillboardDistrict').select2({
            tags: true,
            placeholder: "-- Select or Type District --",
            allowClear: true,
            width: '100%'
        });

        // Check if Dropzone is available before initializing
        Dropzone.autoDiscover = false;

        if (typeof Dropzone !== 'undefined') {
            var myDropzone = new Dropzone("#fileUploadForm", {
                paramName: "file",
                maxFiles: 2,
                acceptedFiles: "image/*",
                maxFilesize: 10, // MB
                addRemoveLinks: true,
                dictRemoveFile: "Remove",
                dictMaxFilesExceeded: "You can only upload 2 images per site.",

                init: function () {
                    let dz = this;

                    // Preload existing images
                    let existingImages = [
                        <?php if($image1Exists): ?>
                            { name: "<?php echo e($billboard_detail->site_number); ?>_1.png", size: 12345, url: "<?php echo e(asset('storage/billboards/' . $billboard_detail->site_number . '_1.png')); ?>" },
                        <?php endif; ?>
                        <?php if($image2Exists): ?>
                            { name: "<?php echo e($billboard_detail->site_number); ?>_2.png", size: 12345, url: "<?php echo e(asset('storage/billboards/' . $billboard_detail->site_number . '_2.png')); ?>" }
                        <?php endif; ?>
                    ];

                    existingImages.forEach(function(file) {
                        dz.emit("addedfile", file);
                        dz.emit("thumbnail", file, file.url);
                        dz.emit("success", file, { message: "Loaded" }); // ✅ mark as uploaded
                        dz.emit("complete", file);
                        dz.files.push(file); 
                    });

                    // Prevent adding more files if 2 already exist
                    dz.on("addedfile", function(file) {
                        if (dz.files.length > 2) {
                            dz.removeFile(file);
                            alert("You can only upload 2 images per site.");
                        }
                    });

                    // Handle remove
                    dz.on("removedfile", function(file) {
                        if (!file.url) return; // skip new uploads

                        fetch("<?php echo e(route('billboard.deleteImage')); ?>", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "<?php echo e(csrf_token()); ?>"
                            },
                            body: JSON.stringify({ filename: file.name })
                        })
                        .then(r => r.json())
                        .then(data => {
                            alert(data.message);
                            window.location.reload();
                        })
                        .catch(() => alert("Failed to delete image"));
                    });

                },

                sending: function(file, xhr, formData) {
                    formData.append("_token", "<?php echo e(csrf_token()); ?>");
                    formData.append("site_number", "<?php echo e($billboard_detail->site_number); ?>");
                },
                success: function(file, response) {
                    // Determine which slot (1 or 2) based on filename
                    if (response.filename.endsWith("_1.png")) {
                        let slot = document.querySelector("#image-slot-1");
                        // Use the same structure and classes as the Blade version
                        slot.innerHTML = `
                            <div class="absolute inset-0 flex items-center justify-center">
                                <img src="${response.url}" class="w-full h-full object-contain max-h-96" alt="Billboard Image 1">
                            </div>
                            <button onclick="deleteImage('${response.filename}', this)"
                                    class="absolute top-2 right-2 text-white bg-red-500 hover:bg-red-600 px-3 py-1 rounded-full opacity-0 group-hover:opacity-100 pointer-events-none group-hover:pointer-events-auto transition-opacity duration-200 ease-in-out"
                                    aria-label="Delete Image 1">
                                <i class="fas fa-trash"></i>
                            </button>
                        `;
                    } else if (response.filename.endsWith("_2.png")) {
                        let slot = document.querySelector("#image-slot-2");
                        // Use the same structure and classes as the Blade version
                        slot.innerHTML = `
                            <div class="absolute inset-0 flex items-center justify-center">
                                <img src="${response.url}" class="w-full h-full object-contain max-h-96" alt="Billboard Image 2">
                            </div>
                            <button onclick="deleteImage('${response.filename}', this)"
                                    class="absolute top-2 right-2 text-white bg-red-500 hover:bg-red-600 px-3 py-1 rounded-full opacity-0 group-hover:opacity-100 pointer-events-none group-hover:pointer-events-auto transition-opacity duration-200 ease-in-out"
                                    aria-label="Delete Image 2">
                                <i class="fas fa-trash"></i>
                            </button>
                        `;
                    }
                }
            });
        } else {
            console.error("Dropzone not loaded");
        }
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Projects\Laravel\KL_tracker\resources\views\billboard\detail.blade.php ENDPATH**/ ?>