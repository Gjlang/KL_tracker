@extends('layouts.main')

@section('title')
<title>BGOC Outdoor System - Billboard Details</title>
@endsection('title')

@section('sidebar')
@include('layouts.sidebar')
@endsection

@section('app_content')
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
</style>

<div class="intro-y flex flex-col sm:flex-row items-center mt-8">
    <h2 class="text-lg font-medium mr-auto">
        Billboard Detail
    </h2>
</div>

<!-- BEGIN: Profile Info -->
<div class="intro-y box px-5 pt-5 mt-5">
    <div class="flex flex-col lg:flex-row border-b border-gray-200 dark:border-dark-5 pb-5 -mx-5">

        <!-- Billboard Details -->
        <div class="mt-6 lg:mt-0 flex-1 dark:text-gray-300 px-5 border-l border-r border-gray-200 dark:border-dark-5 border-t lg:border-t-0 pt-5 lg:pt-0">
            <div class="text-center lg:text-left" id="billboard" data-id="{{ $billboard_detail->id }}">
                <div class="font-bold text-2xl mt-5">Billboard Details</div><br>
                <div class="text-gray-600">Site Number: {{ $billboard_detail->site_number }} </div>
                <div class="text-gray-600">Location: {{ $billboard_detail->location_name }} </div>
                <div class="text-gray-600">District/State: {{ $billboard_detail->district_name }}, {{ $billboard_detail->state_name }} </div>
                <div class="text-gray-600">Council: {{ $billboard_detail->council_abbrv }} - {{ $billboard_detail->council_name }} </div>
                <div class="text-gray-600">GPS Coordinate: {{ $billboard_detail->gps_latitude }}, {{ $billboard_detail->gps_longitude }}</div>
                <div class="text-gray-600">Traffic Volume: {{ $billboard_detail->traffic_volume }} </div>
                <div class="text-gray-600">Billboard Type: {{ $billboard_detail->prefix }} - {{ $billboard_detail->type }} </div>
                <div class="text-gray-600">Size: {{ $billboard_detail->size }} </div>
                <div class="text-gray-600">Lighting: {{ $billboard_detail->lighting }} </div>
                <div class="text-gray-600">Status: {{ $billboard_detail->site_type ? strtoupper($billboard_detail->site_type) : '-' }} </div>
            </div>
            <br>
            <div class="mt-2 xl:mt-0">
                <a href="{{ route('billboard.download', $billboard_detail->id) }}" class="button bg-theme-9 text-black">Download PDF [INTERNAL]</a>
                <a href="{{ route('billboard.download.client', $billboard_detail->id) }}" class="button bg-theme-12 text-black">Download PDF [CLIENT]</a>
                @php
                    $mapUrl = !empty($billboard_detail->gps_url)
                        ? $billboard_detail->gps_url
                        : "https://www.google.com/maps?q={$billboard_detail->gps_latitude},{$billboard_detail->gps_longitude}";
                @endphp

                <a href="{{ $mapUrl }}" target="_blank" rel="noopener noreferrer" 
                class="button bg-theme-1 text-white">Show on Maps</a>
                <a href="javascript:void(0)" onclick="populateBillboardEditModal({{ json_encode($billboard_detail) }})" class="button bg-theme-1 text-white">Edit</a>
            </div>
        </div>
    </div>
</div>

<div class="intro-y box px-5 pt-5 mt-5">
    <h2 class="intro-y font-medium text-xl sm:text-2xl">
        Billboard Site Images
    </h2>
    @php
        $imageBasePath = '/home/bluedale2/public_html/bgocoutdoor.bluedale.com.my/images/billboards/';
        $image1Path = $imageBasePath . $billboard_detail->site_number . '_1.png';
        $image2Path = $imageBasePath . $billboard_detail->site_number . '_2.png';

        $image1Exists = file_exists($image1Path);
        $image2Exists = file_exists($image2Path);
    @endphp

            

    <div class="intro-y mt-6">
        <div class="flex gap-4">
            <!-- Image 1 Slot -->
            <div class="flex-1 relative group h-86 overflow-hidden rounded-lg shadow bg-gray-100">
                @if($image1Exists)
                    <button 
                        onclick="deleteImage('{{ $billboard_detail->site_number }}_1.png', this)" 
                        class="absolute top-2 right-2 text-white bg-theme-6 px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition">
                        X
                    </button>
                    <img src="{{ url('images/billboards/' . $billboard_detail->site_number . '_1.png') }}?v={{ time() }}" 
                        alt="{{ $billboard_detail->location_name }}" 
                        class="w-full h-full object-cover">
                @endif
            </div>

            <!-- Image 2 Slot -->
            <div class="flex-1 relative group h-86 overflow-hidden rounded-lg shadow bg-gray-100">
                @if($image2Exists)
                    <button 
                        onclick="deleteImage('{{ $billboard_detail->site_number }}_2.png', this)" 
                        class="absolute top-2 right-2 text-white bg-theme-6 px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition">
                        X
                    </button>
                    <img src="{{ url('images/billboards/' . $billboard_detail->site_number . '_2.png') }}?v={{ time() }}" 
                        alt="{{ $billboard_detail->location_name }}" 
                        class="w-full h-full object-cover">
                @endif
            </div>
        </div>
    </div>



    <div class="intro-y mt-5 pt-5 border-t border-gray-200 dark:border-dark-5">
        <div class="border border-gray-200 dark:border-dark-5 rounded-md p-5 mt-5" id="fileUpload">
            <div class="mt-5">
                <div class="mt-3">
                    <div class="flex items-center">
                        <label class="font-medium">Upload Image</label>
                    </div>
                    <form id="fileUploadForm" action="{{ route('billboard.uploadImage') }}" method="POST" enctype="multipart/form-data" class="dropzone border-gray-200 border-dashed">
                        @csrf
                        <input type="hidden" name="site_number" value="{{ $billboard_detail->site_number }}">
                        <div class="fallback">
                            <input name="files[]" id="fileInput" type="file" multiple />
                        </div>
                        <div class="dz-message" data-dz-message>
                            <div class="text-lg font-medium">Drop files here or click to upload.</div>
                            <div class="text-gray-600">Only 2 images per site are allowed.</div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection('app_content')

@section('modal_content')
<!-- Edit Billboard Modal -->
<div class="row flex flex-col sm:flex-row sm:items-end xl:items-start mb-2">
    <div class="modal" id="billboardEditModal">
        <div class="modal__content">
            <div class="flex items-center px-5 py-5 sm:py-3 border-b border-gray-200 dark:border-dark-5">
                <h2 class="font-medium text-base mr-auto">Edit Billboard</h2>
            </div>
            <form id="billboardEditForm" action="{{ route('billboard.update') }}" method="POST">
                @csrf
                @method('POST')
                <div class="p-5 grid grid-cols-12 gap-4 gap-y-3">
                    <div class="col-span-12 sm:col-span-12">
                        <input type="hidden" id="editBillboardModalId" name="id">
                        <label>Outdoor Type <span style="color: red;">*</span></label>
                        <select class="input w-full sm:w-32 xxl:w-full mt-2 sm:mt-0 sm:w-auto border" id="editBillboardType" name="type" disabled>
                            <option value="">-- Select Outdoor Type --</option>
                            <option value="BB">Billboard</option>
                            <option value="TB">Tempboard</option>
                            <option value="BU">Bunting</option>
                            <option value="BN">Banner</option>
                        </select>
                        <input type="hidden" id="editBillboardTypeHidden" name="type" value="">
                    </div>
                    <div class="col-span-12 sm:col-span-12">
                        <label>Billboard Size <span style="color: red;">*</span></label>
                        <select class="input w-full sm:w-32 xxl:w-full mt-2 sm:mt-0 sm:w-auto border" id="editBillboardSize" name="size" required>
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
                        <select class="input w-full sm:w-32 xxl:w-full mt-2 sm:mt-0 sm:w-auto border" id="editBillboardLighting" name="lighting" required>
                            <option value="">-- Select Lighting --</option>
                            <option value="None">None</option>
                            <option value="TNB">TNB</option>
                            <option value="SOLAR">SOLAR</option>
                        </select>
                    </div>

                    <!-- Separator -->
                    <div class="col-span-12">
                        <hr class="my-6 border-t-1 border-gray-300">
                    </div>

                    <div class="col-span-12 sm:col-span-12">
                        <label>State <span style="color: red;">*</span></label>
                        <select class="input w-full sm:w-32 xxl:w-full mt-2 sm:mt-0 sm:w-auto border" id="editBillboardState" name="state_id" disabled>
                            <option value="">-- Select State --</option>
                            @foreach ($states as $state)
                                <option value="{{ $state->id }}">{{ $state->name }}</option>
                            @endforeach
                        </select>
                        <!-- Hidden input to send state_id value -->
                        <input type="hidden" id="editBillboardStateHidden" name="state_id" value="">
                    </div>
                    <div class="col-span-12 sm:col-span-12">
                        <label>District <span style="color: red;">*</span></label>
                        <select class="input w-full sm:w-32 xxl:w-full mt-2 sm:mt-0 sm:w-auto border" id="editBillboardDistrict" name="district_id" required>
                            <option value="">-- Select District --</option>
                        </select>
                    </div>
                    <div class="col-span-12 sm:col-span-12">
                        <label>Council <span style="color: red;">*</span></label>
                        <select class="input w-full sm:w-32 xxl:w-full mt-2 sm:mt-0 sm:w-auto border" id="editBillboardCouncil" name="council_id" disabled>
                            <option value="">-- Select Council --</option>
                        </select>
                    </div>
                    <div class="col-span-12 sm:col-span-12">
                        <label>Location <span style="color: red;">*</span></label>
                        <input type="text" class="input w-full sm:w-32 xxl:w-full mt-2 sm:mt-0 sm:w-auto border" id="editBillboardLocation" name="location_name" placeholder="Enter location name">
                    </div>

                    <!-- Separator -->
                    <div class="col-span-12">
                        <hr class="my-6 border-t-1 border-gray-300">
                    </div>

                    <div class="col-span-12 sm:col-span-12">
                        <label for="editGPSCoordinate" class="form-label">GPS Coordinate <span style="color: red;">*</span></label>
                        <input 
                            type="text" 
                            class="input w-full border mt-2 flex-1" 
                            id="editGPSCoordinate" 
                            name="gps_coordinate"
                            placeholder="e.g. 3.1390, 101.6869" 
                            required
                        >
                        <small class="text-gray-500">Format: latitude, longitude</small>
                    </div>
                    <div class="col-span-12 sm:col-span-12">
                        <label for="editGPSUrl" class="form-label">GPS URL (Google Maps)</label>
                        <input 
                            type="url" 
                            class="input w-full border mt-2 flex-1" 
                            id="editGPSUrl" 
                            name="gps_url"
                            placeholder="https://maps.app.goo.gl/xyz123"
                        >
                        <small class="text-gray-500">Example: https://maps.app.goo.gl/xxxxx</small>
                    </div>

                    <!-- Separator -->
                    <div class="col-span-12">
                        <hr class="my-6 border-t-1 border-gray-300">
                    </div>

                    <div class="col-span-12 sm:col-span-12">
                        <label>Traffic Volume</label>
                        <input type="text" class="input w-full border mt-2 flex-1" id="editBillboardTrafficVolume" name="traffic_volume" value="" required>
                    </div>
                    <div class="col-span-12 sm:col-span-12">
                        <label>Site Type</label>
                        <select class="input w-full sm:w-32 xxl:w-full mt-2 sm:mt-0 sm:w-auto border" id="editBillboardSiteType" name="site_type">
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
                        <select class="input w-full sm:w-32 xxl:w-full mt-2 sm:mt-0 sm:w-auto border" id="editBillboardStatus" name="status">
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
@endsection('modal_content')

@section('script')
<!-- Add these CDN links before your script -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js"></script>

<script>
    // Define functions in the global scope so they're accessible to inline onclick
    function deleteImage(filename, button) {
        if(!confirm('Are you sure you want to delete this image?')) return;

        fetch('{{ route("billboard.deleteImage") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ filename: filename })
        })
        .then(response => {
            if(response.ok) return response.json();
            throw new Error('File not found');
        })
        .then(data => {
            button.closest('.flex-1').remove();
            alert(data.message);
            window.location.reload(); // Refresh to update Dropzone state
        })
        .catch(err => {
            console.error(err);
            alert('Error deleting image.');
        });
    }

    // Fixed modal functions to prevent scrollbar error
    function openAltEditorModal(element) {
        // Ensure DOM is ready before opening modal
        setTimeout(() => {
            try {
                cash(element).modal('show');
            } catch (e) {
                console.error('Modal error:', e);
                // Fallback: manually show modal
                document.querySelector(element).style.display = 'block';
                document.querySelector(element).classList.add('show');
            }
        }, 100);
    }
    
    function closeAltEditorModal(element) {
        // Ensure DOM is ready before closing modal
        setTimeout(() => {
            try {
                cash(element).modal('hide');
            } catch (e) {
                console.error('Modal close error:', e);
                // Fallback: manually hide modal
                document.querySelector(element).style.display = 'none';
                document.querySelector(element).classList.remove('show');
            }
        }, 100);
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
        $.post('{{ route("location.getDistricts") }}', {
            _token: '{{ csrf_token() }}',
            state_id: stateID
        }, function (districts) {
            $('#editBillboardDistrict').empty().append(`<option value="">-- Select District --</option>`);
            districts.forEach(function (d) {
                $('#editBillboardDistrict').append(`<option value="${d.id}">${d.name}</option>`);
            });
            $('#editBillboardDistrict').val(districtID).trigger('change');
            $('#editBillboardDistrict').trigger('select2:select'); // Refresh Select2

            // Load Councils
            $.post('{{ route("location.getCouncils") }}', {
                _token: '{{ csrf_token() }}',
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
        if (typeof Dropzone !== 'undefined') {
            // Initialize Dropzone manually by targeting the form ID
            Dropzone.options.fileUploadForm = {
                paramName: "file",
                maxFiles: 2,
                acceptedFiles: 'image/*',
                maxFilesize: 10,
                addRemoveLinks: true,
                dictRemoveFile: "Remove",
                dictMaxFilesExceeded: "You can only upload 2 images per site.",

                init: function () {
                    let dz = this;

                    let existingImages = [
                        @if($image1Exists)
                            { name: "{{ $billboard_detail->site_number }}_1.png", url: "{{ url('images/billboards/' . $billboard_detail->site_number . '_1.png') }}" },
                        @endif
                        @if($image2Exists)
                            { name: "{{ $billboard_detail->site_number }}_2.png", url: "{{ url('images/billboards/' . $billboard_detail->site_number . '_2.png') }}" }
                        @endif
                    ];

                    existingImages.forEach(function(file) {
                        dz.emit("addedfile", file);
                        dz.emit("thumbnail", file, file.url);
                        dz.emit("complete", file);
                    });

                    dz.options.maxFiles = dz.options.maxFiles - existingImages.length;

                    dz.on("removedfile", function(file) {
                        if (file.name) {
                            axios.post("{{ route('billboard.deleteImage') }}", {
                                filename: file.name,
                                _token: "{{ csrf_token() }}"
                            })
                            .then(response => {
                                alert(response.data.message);
                                dz.options.maxFiles++; 
                                window.location.reload();
                            })
                            .catch(error => {
                                alert(error.response?.data?.message || "Failed to delete image");
                            });
                        }
                    });
                },

                sending: function(file, xhr, formData) {
                    formData.append("_token", "{{ csrf_token() }}");
                    formData.append("site_number", "{{ $billboard_detail->site_number }}");
                },

                success: function(file, response) {
                    alert(response.message);

                    // Update image directly without full reload
                    let imgSelector = `img[src*='${response.filename.split('.')[0]}']`;
                    let img = document.querySelector(imgSelector);

                    if (img) {
                        img.src = response.url; // already has ?v=timestamp
                    } else {
                        window.location.reload(); // fallback
                    }
                },

                error: function(file, response) {
                    console.error("❌ Dropzone error:", response);
                    this.removeFile(file);
                }
            };
        } else {
            console.error('Dropzone library is not loaded');
        }

        // Handle form submission when the submit button is clicked
        $(document).on('click', '#billboardEditButton', function (e) {
            e.preventDefault();
            
            // Validate required fields
            if (!validateForm()) {
                return;
            }

            $.ajax({
                url: '{{ route("billboard.update") }}',
                method: 'POST',
                data: $('#billboardEditForm').serialize(),
                success: function(response) {
                    // Close modal after successfully edited
                    var element = "#billboardEditModal";
                    closeAltEditorModal(element);

                    // Show successful toast
                    if (typeof window.showSubmitToast !== 'undefined') {
                        window.showSubmitToast("Successfully updated.", "#91C714");
                    } else {
                        alert("Successfully updated.");
                    }

                    // Reload the page to see changes
                    location.reload();
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
    });
</script>
@endsection