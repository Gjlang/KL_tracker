@extends('layouts.main')

@section('title')
<title>BGOC Outdoor System - Billboard Availability</title>
@endsection('title')

@section('sidebar')
@include('layouts.sidebar')
@endsection

@section('app_content')
<style>
    table td, table th {
        white-space: nowrap;
    }

    /* Wrapper scrolls both directions */
    .monthly-booking-table-wrapper {
        max-height: 400px;
        overflow: auto;
    }

    #monthly-booking-table {
        border-collapse: collapse;
        min-width: 1200px; /* adjust based on columns */
    }

    #monthly-booking-table th,
    #monthly-booking-table td {
        border: 1px solid #d1d5db; /* Tailwind border-gray-300 */
        padding: 4px 8px;
        white-space: nowrap;
        /* background: #fff; important for frozen cells */
    }

    /* Sticky header row */
    #monthly-booking-table thead th {
        position: sticky;
        top: 0;
        background-color: #f3f4f6; /* bg-gray-100 */
        z-index: 4; /* sits above normal cells */
    }

    /* ===== Freeze first 3 columns ===== */
    #monthly-booking-table th:nth-child(1),
    #monthly-booking-table td:nth-child(1) {
        position: sticky;
        left: 0;
        min-width: 50px;   /* No */
        background: #fff;
        z-index: 5;
    }

    #monthly-booking-table th:nth-child(2),
    #monthly-booking-table td:nth-child(2) {
        position: sticky;
        left: 30px;        /* width of col1 */
        min-width: 120px;  /* Site No */
        background: #fff;
        z-index: 5;
    }

    #monthly-booking-table th:nth-child(3),
    #monthly-booking-table td:nth-child(3) {
        position: sticky;
        left: 200px;       /* col1 + col2 */
        min-width: 200px;  /* Location */
        background: #fff;
        z-index: 5;
    }

    /* Ensure header cells of frozen columns stay on top */
    #monthly-booking-table thead th:nth-child(1),
    #monthly-booking-table thead th:nth-child(2),
    #monthly-booking-table thead th:nth-child(3) {
        z-index: 6;
    }

    /* Collapsed (default) */
    .expand-cell {
        max-width: 250px;          /* adjust width */
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        position: relative;
        vertical-align: top;
        padding-right: 30px;
    }

    .expand-cell .toggle-location {
        position: absolute;
        display: inline-block;
        right: 4px;                /* pin to right edge */
        top: 50%;
        transform: translateY(-50%);
        color: blue;
        background: white;         /* prevent overlap */
        z-index: 10;               /* bring to front */
    }

    .expand-cell .location-text {
        padding-right: 25px; /* space reserved for [+] */
        display: inline-block;
    }

    /* Expanded (full text, no cutoff) */
    .expand-cell.expanded {
        white-space: normal;       /* allow multiple lines */
        overflow: visible;         /* no hidden text */
        text-overflow: clip;       /* remove ... */
        max-width: none;           /* allow full width */
    }


    .select2-container {
        min-width: 250px !important; /* adjust size */
    }
</style>



<div class="intro-y flex flex-col sm:flex-row items-center mt-8">
    <h2 class="text-lg font-medium mr-auto">
        Billboard Availability
    </h2>
</div>
<div class="intro-y box p-5 mt-5">
    <div class="mb-5 p-5 rounded-md" style="background-color:#ECF9FD;">
        <h2 class="text-lg font-medium">
            Check Billboard Availability
        </h2>
        <p class="w-12 flex-none xl:w-auto xl:flex-initial ml-2">
            <i class="font-bold">Billboard Availability</i> - Lorem Ipsum.
        </p>
    </div>

    


    <!-- Billboard Booking Calendar Filter -->
    <div class="flex flex-col sm:flex-row sm:items-end xl:items-start mb-2 mt-2">
        <form class="xl:flex sm:mr-auto">
            <div class="row sm:flex items-center sm:mr-4">
                <label class="w-12 flex-none xl:w-auto xl:flex-initial mr-2">State</label>
                <select class="input w-full mt-2 sm:mt-0 sm:w-auto border select2-state" id="filterAvailabilityState">
                    <option value="" selected="">-- Select State --</option>
                    @foreach ($states as $state)
                        <option value="{{ $state->id }}">{{ $state->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="row sm:flex items-center sm:mr-4">
                <label class="w-12 flex-none xl:w-auto xl:flex-initial mr-2">District</label>
                <select class="input w-full mt-2 sm:mt-0 sm:w-auto border select2-district" id="filterAvailabilityDistrict">
                    <option value="" selected="">-- Select District --</option>
                    @foreach ($districts as $district)
                        <option value="{{ $district->id }}">{{ $district->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sm:flex items-center sm:mr-4">
                <label class="w-12 flex-none xl:w-auto xl:flex-initial mr-2">Location</label>
                <select class="input w-64 mt-2 sm:mt-0 border select2-location" id="filterAvailabilityLocation">
                    <option value="" selected="">-- Select Location --</option>
                    @foreach ($locations as $location)
                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                    @endforeach
                </select>
            </div>
        </form> 
    </div>
    <!-- Filter End -->

    <!-- Billboard Booking Calendar Filter -->
    <div class="flex flex-col sm:flex-row sm:items-end xl:items-start mb-2 mt-2">
        <form class="xl:flex sm:mr-auto">
            
            <div class="sm:flex items-center sm:mr-4">
                <label class="w-12 flex-none xl:w-auto xl:flex-initial mr-2">Type</label>
                <select class="input w-full sm:w-32 xxl:w-full mt-2 sm:mt-0 sm:w-auto border" id="filterAvailabilityType">
                    <option value="" selected="">-- Select Type --</option>
                    @foreach ($types as $type)
                        <option value="{{ $type->prefix }}">{{ $type->type }}</option>
                    @endforeach
                </select>
            </div>
            <div class="row sm:flex items-center sm:mr-4">
                <label class="w-12 flex-none xl:w-auto xl:flex-initial mr-2">New/Existing</label>
                <select class="input w-full mt-2 sm:mt-0 sm:w-auto border" id="filterAvailabilitySiteType">
                    <option value="" selected="">All</option>
                    <option value="new">New</option>
                    <option value="existing">Existing</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
            <div class="row sm:flex items-center sm:mr-4">
                <label class="w-12 flex-none xl:w-auto xl:flex-initial mr-2">Status</label>
                <select class="input w-full mt-2 sm:mt-0 sm:w-auto border" id="filterAvailabilityStatus">
                    <option value="" selected="">All</option>
                    <option value="pending_payment">Pending Payment</option>
                    <option value="pending_install">Pending Install</option>
                    <option value="ongoing">Ongoing</option>
                    <option value="completed">Completed</option>
                    <option value="dismantle">Dismantle</option>
                </select>
            </div>
        </form> 
    </div>
    <!-- Filter End -->

    
     <!-- Billboard Availability Date Filter -->
    <div class="flex flex-col sm:flex-row sm:items-end xl:items-start mb-2 mt-2">
        <form class="xl:flex flex-wrap items-end space-y-4 xl:space-y-0 xl:space-x-4 mb-4">
            <div class="row sm:flex items-center sm:mr-4">
                <label class="w-24 text-gray-700">Start Date</label>
                <input type="date" id="filterAvailabilityStart" class="input border w-48" />
            </div>

            <div class="row sm:flex items-center sm:mr-4">
                <label class="w-24 text-gray-700">End Date</label>
                <input type="date" id="filterAvailabilityEnd" class="input border w-48" />
            </div>
        </form>
    </div>
    <!-- Filter End -->

    <!-- Availability Year Filter -->
    <div class="flex flex-col sm:flex-row sm:items-end xl:items-start mb-2 mt-2">
        <form class="xl:flex flex-wrap items-end space-y-4 xl:space-y-0 xl:space-x-4 mb-4">
            <div class="row sm:flex items-center sm:mr-4">
                <label class="w-12 flex-none xl:w-auto xl:flex-initial mr-2">Year</label>
                <select class="input w-full mt-2 sm:mt-0 sm:w-auto border" id="filterAvailabilityYear">
                    @for ($y = 2023; $y <= now()->year + 2; $y++)
                        <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
        </form>
    </div>
    <!-- Filter End -->

    <div class="mb-4">
        Search:<input type="text" id="globalSearchInput"
            class="input border p-2 w-64 ml-4"
            placeholder="Search all tables...">
    </div>

    <!-- Legend -->
    <div class="flex flex-wrap items-center gap-4 mb-4 text-sm">
        <div class="flex items-center gap-2">
            <span class="w-4 h-4 rounded-sm bg-theme-6 mr-1"></span>
            <span class="text-gray-700">Pending Payment</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-4 h-4 rounded-sm bg-theme-1 mr-1"></span>
            <span class="text-gray-700">Pending Install</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-4 h-4 rounded-sm bg-green-600 mr-1"></span>
            <span class="text-gray-700">Ongoing</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-4 h-4 rounded-sm bg-theme-12 mr-1"></span>
            <span class="text-gray-700">Completed</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-4 h-4 rounded-sm bg-theme-13 mr-1"></span>
            <span class="text-gray-700">Dismantle</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-4 h-4 rounded-sm bg-gray-400 mr-1"></span>
            <span class="text-gray-700">Other</span>
        </div>
    </div>
    <div class="flex flex-wrap items-center gap-4 mb-4 text-sm">
        <p class="italic text-gray-500">*Click on the colored cells in the table to edit status</p>
    </div>
    <!-- Legend End -->

    <!-- billboard availability calendar table -->
    <div class="shadow-sm rounded-lg border border-gray-200 overflow-hidden">
        <div class="monthly-booking-table-wrapper">
            <table id="monthly-booking-table" class="w-full text-sm text-left">
                @php
                    $year = request('year', now()->year);
                    $months = [
                        'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                        'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
                    ];
                @endphp
                <thead id="monthly-booking-head">
                    <!-- Populated by JS -->
                </thead>
                <tbody id="monthly-booking-body">
                    <!-- Populated by JS -->
                </tbody>
            </table>
        </div>
    </div>



    <!-- Check Availability table -->
    <div class="overflow-x-auto scrollbar-hidden">
        <table class="table mt-5" id="billboard_availability_table">
            <thead>
                <tr class="bg-theme-1 text-white">
                    <th class="whitespace-nowrap">No.</th>
                    <th class="whitespace-nowrap">Site #</th>
                    <th class="whitespace-nowrap">Client Name</th>
                    <th class="whitespace-nowrap">Location</th>
                    <th class="whitespace-nowrap">Start Date</th>
                    <th class="whitespace-nowrap">End Date</th> 
                    <th class="whitespace-nowrap">Duration (Month)</th>
                    <th class="whitespace-nowrap">Status</th>
                    <th class="whitespace-nowrap">Remarks</th>
                    <th class="whitespace-nowrap">Detail</th>
                    <th class="whitespace-nowrap flex flex-row">Action</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
    <!-- Table End -->
</div>
@endsection('app_content')

@section('modal_content')
<!-- Remarks Modal -->
<div class="row flex flex-col sm:flex-row sm:items-end xl:items-start mb-2">
    <div class="modal" id="remarksModal">
        <div class="modal__content">
            <!-- Modal Header -->
            <div class="flex items-center px-5 py-5 sm:py-3 border-b border-gray-200 dark:border-dark-5">
                <h2 class="font-medium text-base mr-auto">Remarks</h2>
            </div>

            <!-- Modal Body -->
            <div class="p-5 grid grid-cols-12 gap-4 gap-y-3">
                <div class="col-span-12 sm:col-span-12">
                    <label>Full Remarks</label>
                    <textarea class="input w-full border mt-2 flex-1" id="remarksContent" rows="8" readonly></textarea>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Remarks Modal End -->

<!-- Create Job Order Modal -->
<div class="row flex flex-col sm:flex-row sm:items-end xl:items-start mb-2">
    <div class="modal" id="addBookingModal">
        <div class="modal__content">
            <div class="flex items-center px-5 py-5 sm:py-3 border-b border-gray-200 dark:border-dark-5">
                <h2 class="font-medium text-base mr-auto">Add New Job Order</h2>
            </div>
            <form id="inputBookingForm">
                <div class="p-5 grid grid-cols-12 gap-4 gap-y-3">
                    <div class="col-span-12 sm:col-span-12">
                        <label>Site Number <span style="color: red;">*</span></label>
                        <input type="text" class="input w-full border mt-2 flex-1" id="inputBookingSiteNo" value="" readonly>
                    </div>
                    <div class="col-span-12 sm:col-span-12">
                        <label>Client <span style="color: red;">*</span></label>
                        <select id="inputBookingCompany" class="input w-full border mt-2 select2-client" required>
                            <option value="">-- Select Client --</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-12 sm:col-span-12">
                        <label>State <span style="color: red;">*</span></label>
                        <select class="input w-full sm:w-32 xxl:w-full mt-2 sm:mt-0 sm:w-auto border" id="inputBookingState" disabled>
                            <option value="">-- Select State --</option>
                            @foreach ($states as $state)
                                <option value="{{ $state->id }}">{{ $state->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-12 sm:col-span-12">
                        <label>District <span style="color: red;">*</span></label>
                        <select class="input w-full sm:w-32 xxl:w-full mt-2 sm:mt-0 sm:w-auto border" id="inputBookingDistrict" disabled>
                            <option value="">-- Select District --</option>
                        </select>
                    </div>
                    <div class="col-span-12 sm:col-span-12">
                        <label>Location <span style="color: red;">*</span></label>
                        <select class="input w-full sm:w-32 xxl:w-full mt-2 sm:mt-0 sm:w-auto border" id="inputBookingLocation" disabled>
                            <option value="">-- Select Location --</option>
                        </select>
                    </div>                     
                    <div class="col-span-12 sm:col-span-6">
                        <label for="start_date" class="form-label">Start Date <span style="color: red;">*</span></label>
                        <input type="text" id="start_date" class="input border mt-2" placeholder="Select start date">
                    </div>
                    <div class="col-span-12 sm:col-span-6">
                        <label for="end_date" class="form-label">End Date <span style="color: red;">*</span></label>
                        <input type="text" id="end_date" class="input border mt-2" placeholder="Select end date">
                    </div>
                    <div class="col-span-12 sm:col-span-12">
                        <label>Status <span style="color: red;">*</span></label>
                        <select id="inputBookingStatus" class="input w-full border mt-2 select" required>
                            <option disabled selected hidden value="">-- Select Status --</option>
                            <option value="pending_payment">Pending Payment</option>
                            <option value="pending_install">Pending Install</option>
                            <option value="ongoing">Ongoing</option>
                            <option value="completed">Completed</option>
                            <option value="dismantle">Dismantle</option>          
                        </select>
                    </div>
                    <div class="col-span-12 sm:col-span-12">
                        <label>Artwork by <span style="color: red;">*</span></label>
                        <select id="inputBookingArtworkBy" class="input w-full border mt-2 select" required>
                            <option disabled selected hidden value="">-- Select Artwork by --</option>
                            <option value="Client">Client</option>
                            <option value="Bluedale">Bluedale</option>
                        </select>
                    </div>
                    <div class="col-span-12 sm:col-span-12">
                        <label>DBP Approval <span style="color: red;">*</span></label>
                        <select id="inputBookingDBPApproval" class="input w-full border mt-2 select" required>
                            <option disabled selected hidden value="">-- Select DBP Approval --</option>
                            <option value="NA">Not Available</option>
                            <option value="In Review">In Review</option>
                            <option value="Approved">Approved</option>
                            <option value="Rejected">Rejected</option>
                        </select>
                    </div>
                    <div class="col-span-12 sm:col-span-12">
                        <label>Remarks <span style="color: red;">*</span></label>
                        <!-- <input type="text" class="input w-full border mt-2 flex-1" id="inputBookingRemarks" value="" required> -->
                        <textarea class="input w-full border mt-2 flex-1" id="inputBookingRemarks" rows="5" required></textarea>
                    </div>
                </div>

                <!-- hidden inputs that hold the values as disabled fields are not submitted with the form -->
                <input type="hidden" name="site_number" id="hiddenBookingSiteNo">
                <input type="hidden" name="state_id" id="hiddenBookingState">
                <input type="hidden" name="district_id" id="hiddenBookingDistrict">
                <input type="hidden" name="location_id" id="hiddenBookingLocation">


                <div class="px-5 py-3 text-right border-t border-gray-200 dark:border-dark-5">
                    <button type="submit" class="button w-20 bg-theme-1 text-white" id="inputBookingSubmit">Submit</button>
                </div>
            </form>
        </div>
    </div> 
</div>
<!-- Create Job Order End -->

<!-- Edit Status Modal -->
<div class="modal" id="editStatusModal">
    <div class="modal__content">
        <div class="flex items-center px-5 py-5 sm:py-3 border-b border-gray-200 dark:border-dark-5">
            <h2 class="font-medium text-base mr-auto">Edit Status</h2>
        </div>
        <form>
            <div class="p-5 grid grid-cols-12 gap-4 gap-y-3">
                <!-- Client Name -->
                <div class="col-span-12">
                    <label>Client</label>
                    <p id="editBookingClient" class="mt-2 font-medium text-gray-700"></p>
                </div>

                <!-- Booking Dates -->
                <div class="col-span-12">
                    <label>Booking Period</label>
                    <p id="editBookingDates" class="mt-2 font-medium text-gray-700"></p>
                </div>

                <!-- Status Dropdown -->
                <div class="col-span-12">
                    <label>Status <span style="color: red;">*</span></label>
                    <select id="editBookingStatus" class="input w-full border mt-2 select" required>
                        <option disabled selected hidden value="">-- Select Status --</option>
                        <option value="pending_payment">Pending Payment</option>
                        <option value="pending_install">Pending Install</option>
                        <option value="ongoing">Ongoing</option>
                        <option value="completed">Completed</option>
                        <option value="dismantle">Dismantle</option>           
                    </select>
                </div>
                <div class="col-span-12 sm:col-span-12">
                    <label>Remarks <span style="color: red;">*</span></label>
                    <textarea class="input w-full border mt-2 flex-1" placeholder="Remarks" id="editBookingRemarks" rows="5" required></textarea>
                </div>
            </div>

            <!-- Buttons -->
            <div class="px-5 py-3 text-right border-t border-gray-200 dark:border-dark-5">
                <button type="submit" class="button w-20 bg-theme-1 text-white" id="editBookingButton">Update</button>
            </div>
        </form>
    </div>
</div>
<!-- View Modal End -->
@endsection('modal_content')

@section('script')

<!-- searchable dropdown -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- download excel -->
<script src="https://cdn.jsdelivr.net/npm/exceljs/dist/exceljs.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/file-saver@2.0.5/dist/FileSaver.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://unpkg.com/xlsx-style@latest/dist/xlsx.full.min.js"></script>
<script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/file-saver/dist/FileSaver.min.js"></script>


<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>

    // Global search across both tables
    $('#globalSearchInput').on('keyup', function () {
        const value = $(this).val().toLowerCase();

        // 🔎 Filter Monthly Booking table manually
        $('#monthly-booking-table tbody tr').each(function () {
            const match = $(this).text().toLowerCase().indexOf(value) > -1;
            $(this).toggle(match);
        });

        // ✅ Re-index No column for visible rows
        $('#monthly-booking-table tbody tr:visible').each(function (i) {
            $(this).find('td:first').text(i + 1);
        });

        // 🔎 Filter Check Availability table via DataTable API
        const dt = $('#billboard_availability_table').DataTable();
        dt.search(value).draw();
    });

    


    // <!-- BEGIN: Billboard Booking List Filter -->
    $('#filterAvailabilityState').on('change', function () {
        let stateId = $(this).val();

        const $districtSelect = $('#filterAvailabilityDistrict');
        const $locationSelect = $('#filterAvailabilityLocation');

        $districtSelect.empty().append('<option value="">-- Select District --</option>');
        $locationSelect.empty().append('<option value="">-- Select Location --</option>');

        if (stateId === '' || stateId === 'all') {
            // Load all districts if no specific state is selected
            $.ajax({
                url: '{{ route("location.getAllDistricts") }}',
                type: 'GET',
                success: function (districts) {
                    districts.forEach(function (district) {
                        $districtSelect.append(`<option value="${district.id}">${district.name}</option>`);
                    });

                    // ✅ Reload table after loading all districts
                    $('#billboard_availability_table').DataTable().ajax.reload();
                },
                error: function () {
                    alert('Failed to load all districts.');
                }
            });
        } else {
            // Load filtered districts
            $.ajax({
                url: '{{ route("location.getDistricts") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    state_id: stateId
                },
                success: function (districts) {
                    districts.forEach(function (district) {
                        $districtSelect.append(`<option value="${district.id}">${district.name}</option>`);
                    });

                    // ✅ Reload table after loading filtered districts
                    $('#billboard_availability_table').DataTable().ajax.reload();
                },
                error: function () {
                    alert('Failed to load districts.');
                }
            });
        }
    });

    // When "District" is changed in add form
    $('#filterAvailabilityDistrict').on('change', function () {
        let districtId = $(this).val();

        // Reset Location dropdown
        $('#filterAvailabilityLocation').empty().append('<option value="">-- Select Location --</option>');

        if (districtId !== '') {
            $.ajax({
                url: '{{ route("location.getLocations") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    district_id: districtId
                },
                success: function (locations) {
                    locations.forEach(function (location) {
                        $('#filterAvailabilityLocation').append(`<option value="${location.id}">${location.name}</option>`);
                    });

                    // ✅ Reload table after loading filtered districts
                    $('#billboard_availability_table').DataTable().ajax.reload();
                },
                error: function () {
                    alert('Failed to load locations.');
                }
            });
        }
    });
    // <!-- END: Billboard Booking List Filter -->

    // When "State" is changed in create job order form
    $('#inputBookingState').on('change', function () {
        let stateId = $(this).val();

        // Reset District and Location dropdowns
        $('#inputBookingDistrict').empty().append('<option value="">-- Select District --</option>');
        $('#inputBookingLocation').empty().append('<option value="">-- Select Location --</option>');

        if (stateId !== '') {
            $.ajax({
                url: '{{ route("location.getDistricts") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    state_id: stateId
                },
                success: function (districts) {
                    districts.forEach(function (district) {
                        $('#inputBookingDistrict').append(`<option value="${district.id}">${district.name}</option>`);
                    });
                },
                error: function () {
                    alert('Failed to load districts.');
                }
            });
        }
    });

    // When "District" is changed in add form
    $('#inputBookingDistrict').on('change', function () {
        let districtId = $(this).val();

        // Reset Location dropdown
        $('#inputBookingLocation').empty().append('<option value="">-- Select Location --</option>');

        if (districtId !== '') {
            $.ajax({
                url: '{{ route("location.getLocations") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    district_id: districtId
                },
                success: function (locations) {
                    locations.forEach(function (location) {
                        $('#inputBookingLocation').append(`<option value="${location.id}">${location.name}</option>`);
                    });
                },
                error: function () {
                    alert('Failed to load locations.');
                }
            });
        }
    });

    // -------------------
    // Booking Modal Dates
    // -------------------
    let startPicker, endPicker; 

    function initBookingDatePickers() {
        if (startPicker && typeof startPicker.destroy === 'function') startPicker.destroy();
        if (endPicker && typeof endPicker.destroy === 'function') endPicker.destroy();

        startPicker = flatpickr("#start_date", {
            dateFormat: "Y-m-d",
            onChange: function (selectedDates, dateStr) {
                if (endPicker) {
                    endPicker.set('minDate', dateStr);

                    const endDate = endPicker.selectedDates[0];
                    if (endDate && endDate < selectedDates[0]) {
                        endPicker.clear();
                    }
                }
            }
        });

        endPicker = flatpickr("#end_date", {
            dateFormat: "Y-m-d"
        });
    }

    async function exportCombinedExcel() {
        const year = $('#filterAvailabilityYear').val() || new Date().getFullYear();
        let billboardTypeVal  = $('#filterAvailabilityType').val();   // e.g. TB
        let billboardType     = $('#filterAvailabilityType option:selected').text(); // e.g. Tempboard

        // If no type selected, fallback to "Billboard"
        if (!billboardTypeVal) {
            billboardType = 'Billboard';
        }
        
        const dt = new Date();
        const formattedDate = `${dt.getFullYear()}${String(dt.getMonth() + 1).padStart(2, '0')}${String(dt.getDate()).padStart(2, '0')}`;
        const formattedTime = `${dt.getHours()}:${dt.getMinutes()}:${dt.getSeconds()}`;
        const fileName = `${billboardType}_Availability_Report_${year}_${formattedDate}_${formattedTime}.xlsx`;

        const workbook = new ExcelJS.Workbook();
        const monthlySheet = workbook.addWorksheet('Monthly Calendar');

        // Prepare data
        const monthlyData = prepareMonthlyData();   // array of rows; row[0] is header
        const mergeInfo   = getMonthlyMergeInfo();  // merges per row
        const totalCols   = monthlyData[0].length;

        // Helpers
        const colLetter = (n) => {
            let s = '';
            while (n > 0) {
                const m = (n - 1) % 26;
                s = String.fromCharCode(65 + m) + s;
                n = Math.floor((n - 1) / 26);
            }
            return s;
        };

        // ---- Title ----
        const lastCol = colLetter(totalCols);
        monthlySheet.mergeCells(`A1:${lastCol}1`);
        const titleCell = monthlySheet.getCell('A1');
        titleCell.value = `${billboardType} Availability Report - ${year}`; // <-- use filter year
        titleCell.font = { size: 16, bold: true };  
        titleCell.alignment = { horizontal: 'center', vertical: 'middle' };

        // ---- UPDATE timestamp in B2 ----
        const updateCell = monthlySheet.getCell('B2');
        const now = new Date();
        const formattedNow = `${String(now.getDate()).padStart(2,'0')}/${String(now.getMonth()+1).padStart(2,'0')}/${now.getFullYear()} ${String(now.getHours()).padStart(2,'0')}:${String(now.getMinutes()).padStart(2,'0')}`;
        updateCell.value = `UPDATE: ${formattedNow}`;
        updateCell.font = { italic: true, color: { argb: 'FF000000' } };
        updateCell.alignment = { horizontal: 'left', vertical: 'middle' };

        // ---- Legend on row 2 (H2–L2) ----
        const legendItems = [
            { label: 'Pending Payment', color: 'FFD32929' },
            { label: 'Pending Install', color: 'FF1C3FAA' },
            { label: 'Ongoing',         color: 'FF059669' },
            { label: 'Completed',       color: 'FFFBC500' },
            { label: 'Dismantle',       color: 'FF7F9EB9' },
        ];

        const legendRow = 2;
        let startCol = 8; // H

        legendItems.forEach((item, i) => {
            const col = startCol + i;
            const cell = monthlySheet.getCell(legendRow, col);
            cell.value = item.label;
            cell.alignment = { horizontal: 'center', vertical: 'middle' };
            cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: item.color } };
            const fontColor = (item.color === 'FFFBC500') ? 'FF000000' : 'FFFFFFFF';
            cell.font = { bold: true, color: { argb: fontColor } };
            cell.border = {
                top: { style: 'thin' }, bottom: { style: 'thin' },
                left: { style: 'thin' }, right: { style: 'thin' }
            };
            monthlySheet.getColumn(col).width = Math.max(18, monthlySheet.getColumn(col).width || 0);
        });

        // ---- Table start (row 4) ----
        const headerRowIndex = 4;
        const firstDataRowIndex = headerRowIndex + 1;

        // Color map
        const colorMap = {
            'bg-theme-6':   'FFD32929', // pending_payment
            'bg-theme-1':   'FF1C3FAA', // pending_install
            'bg-green-600': 'FF059669', // ongoing
            'bg-theme-12':  'FFFBC500', // completed
            'bg-theme-13':  'FF7F9EB9', // dismantle
            'bg-white':     'FFFFFFFF', // white background
        };

        // Write header + data
        monthlyData.forEach((rowData, i) => {
            const excelRow = monthlySheet.getRow(headerRowIndex + i);

            rowData.forEach((v, colIdx) => {
                excelRow.getCell(colIdx + 1).value = v;
            });

            excelRow.eachCell((cell, colNumber) => {
                // Apply borders to all cells
                cell.border = {
                    top: { style: 'thin', color: { argb: 'FF000000' } },
                    bottom: { style: 'thin', color: { argb: 'FF000000' } },
                    left: { style: 'thin', color: { argb: 'FF000000' } },
                    right: { style: 'thin', color: { argb: 'FF000000' } }
                };

                if (i === 0) {
                    // Header row styling
                    cell.font = { bold: true, color: { argb: 'FFFFFFFF' } };
                    cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF366092' } };
                    cell.alignment = { horizontal: 'center', vertical: 'middle' };
                } else {
                    // Alignment for specific columns
                    if (colNumber === 3 || colNumber === 4) {
                        cell.alignment = { horizontal: 'left', vertical: 'middle' };
                    } else {
                        cell.alignment = { horizontal: 'center', vertical: 'middle' };
                    }

                    const colors = rowData._colors || [];
                    const colorClass = colors[colNumber - 1];

                    if (colNumber <= 8) {
                        // Force first 8 columns to black font
                        cell.font = { color: { argb: 'FF000000' } };
                    } else if (colorClass) {
                        // Booking / colored cell
                        const bgColor = colorMap[colorClass] || 'FFFFFFFF';
                        cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: bgColor } };
                        const fontColor = (colorClass === 'bg-theme-12') ? 'FF000000' : 'FFFFFFFF';
                        cell.font = { bold: true, color: { argb: fontColor } };
                    } else {
                        // Monthly columns with no booking: white font
                        cell.font = { color: { argb: 'FFFFFFFF' } };
                    }
                }
            });

            excelRow.commit();
        });




        // ---- Apply merges ----
        mergeInfo.forEach((rowMerges, dataRowIdx) => {
            const rowNum = firstDataRowIndex + dataRowIdx;
            rowMerges.forEach(merge => {
                const startCol = merge.startCol + 1;
                const endCol   = merge.endCol + 1;
                monthlySheet.mergeCells(rowNum, startCol, rowNum, endCol);

                const tl = monthlySheet.getCell(rowNum, startCol);
                const bgColor = colorMap[merge.color] || 'FF6B7280';
                tl.value = merge.text || '';
                tl.alignment = { horizontal: 'center', vertical: 'middle' };
                
                // font color: black if yellow (bg-theme-12), else white
                const fontColor = (merge.color === 'bg-theme-12') ? 'FF000000' : 'FFFFFFFF';
                tl.font = { bold: true, color: { argb: fontColor } };
                tl.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: bgColor } };

                for (let c = startCol; c <= endCol; c++) {
                    const cc = monthlySheet.getCell(rowNum, c);
                    cc.border = {
                        top: { style: 'thin', color: { argb: 'FF000000' } },
                        bottom: { style: 'thin', color: { argb: 'FF000000' } },
                        left: { style: 'thin', color: { argb: 'FF000000' } },
                        right: { style: 'thin', color: { argb: 'FF000000' } }
                    };
                }
            });
        });

        // Column widths
        const colWidths = [5, 12, 25, 20, 12, 10, ...Array(totalCols - 6).fill(15)];
        colWidths.forEach((w, i) => monthlySheet.getColumn(i + 1).width = w);

        // ---- Availability List (2nd sheet) ----
        const availabilitySheet = workbook.addWorksheet('Monthly Ongoing Job List');
        const availabilityData = prepareAvailabilityData();
        availabilityData.forEach((rowData, rowIndex) => {
            const row = availabilitySheet.addRow(rowData);
            row.eachCell((cell, colNumber) => {
                cell.border = {
                    top: { style: 'thin', color: { argb: 'FF000000' } },
                    bottom: { style: 'thin', color: { argb: 'FF000000' } },
                    left: { style: 'thin', color: { argb: 'FF000000' } },
                    right: { style: 'thin', color: { argb: 'FF000000' } }
                };
                if (rowIndex === 0) {
                    cell.font = { bold: true, color: { argb: 'FFFFFFFF' } };
                    cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF366092' } };
                    cell.alignment = { horizontal: 'center', vertical: 'middle' };
                } else {
                    cell.alignment = { horizontal: 'center', vertical: 'middle' };
                    
                    if ([2, 3, 4, 9].includes(colNumber)) {
                        cell.alignment = { horizontal: 'left', vertical: 'middle' };
                    }

                    // Mapping from DB → readable
                    const statusMap = {
                        'pending_payment': 'Pending Payment',
                        'pending_install': 'Pending Install',
                        'ongoing': 'Ongoing',
                        'completed': 'Completed',
                        'dismantle': 'Dismantle'
                    };

                    // Replace the cell value with the readable wording
                    if (statusMap[status]) {
                        cell.value = statusMap[status];
                    }
                    
                    if (colNumber === 8 && cell.value) {
                        const status = String(cell.value).toLowerCase();
                        if (status.includes('pending_payment')) {
                            cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFD32929' } };
                            cell.font = { color: { argb: 'FFFFFFFF' }, bold: true };
                        } else if (status.includes('pending_install')) {
                            cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF1C3FAA' } };
                            cell.font = { color: { argb: 'FFFFFFFF' }, bold: true };
                        } else if (status.includes('ongoing')) {
                            cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF059669' } };
                            cell.font = { color: { argb: 'FFFFFFFF' }, bold: true };
                        } else if (status.includes('completed')) {
                            cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFFBC500' } };
                            cell.font = { color: { argb: 'FF000000' }, bold: true };
                        } else if (status.includes('dismantle')) {
                            cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF7F9EB9' } };
                            cell.font = { color: { argb: 'FFFFFFFF' }, bold: true };
                        }
                    }
                }
            });
        });
        [5, 12, 25, 20, 15, 15].forEach((w, i) => availabilitySheet.getColumn(i + 1).width = w);

        // ---- Download ----
        const buf = await workbook.xlsx.writeBuffer();
        const blob = new Blob([buf], { type: "application/octet-stream" });
        saveAs(blob, fileName);
    }

    function prepareMonthlyData() {
        const year = $('#filterAvailabilityYear').val() || new Date().getFullYear();
        const shortYear = String(year).slice(-2);
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        // Build header row
        const header = [
            'No', 'Site No', 'Location', 'Area', 'New/Existing', 'Type', 'Size',
            ...months.map(month => `${month} '${shortYear}`)
        ];

        const data = [header];

        // Loop through table rows
        $('#monthly-booking-body tr').each(function () {
            const $row = $(this);
            const $cells = $row.find('td');

            // Skip empty rows or header-like rows
            if ($cells.length === 0 || $cells.first().hasClass('text-center')) return;

            const rowData = [];
            const rowColors = []; // NEW: track color class for each cell

            // First 6 columns
            for (let i = 0; i < 6; i++) {
                const cellText = $($cells[i]).text().trim();
                const classList = $($cells[i]).attr('class') || '';
                const colorClass = classList.split(/\s+/).find(c => c.startsWith('bg-')) || 'bg-gray-400';

                rowData.push(cellText);
                rowColors.push(colorClass);
            }

            // Monthly columns (handle colspan)
            let monthIndex = 0;
            for (let i = 6; i < $cells.length; i++) {
                const $cell = $($cells[i]);
                const colspan = parseInt($cell.attr('colspan')) || 1;
                const cellText = $cell.text().trim();
                const classList = $cell.attr('class') || '';
                const colorClass = classList.split(/\s+/).find(c => c.startsWith('bg-')) || 'bg-gray-400';

                for (let j = 0; j < colspan; j++) {
                    if (monthIndex + j < 12) {
                        rowData.push(cellText);
                        rowColors.push(colorClass); // add same color for each month in colspan
                    }
                }

                monthIndex += colspan;
            }

            // Fill any missing months with empty strings and default color
            while (rowData.length < header.length) {
                rowData.push('');
                rowColors.push('bg-gray-400');
            }

            rowData._colors = rowColors; // attach colors array to rowData
            data.push(rowData);
        });

        return data;
    }

    function prepareAvailabilityData() {
        // Define header row
        const header = ['No', 'Site #', 'Client', 'Location', 'Start Date', 'End Date', 'Duration (month)', 'Status', 'Remarks'];
        const data = [header];

        // Get DataTable instance
        const table = $('#billboard_availability_table').DataTable();
        const tableData = table.data();

        // Loop through DataTable rows
        tableData.each(function (rowData, index) {
            const row = [
                index + 1,
                rowData.site_number || '',
                rowData.company_name || '',
                rowData.location_name || '',
                rowData.start_date || '',
                rowData.end_date || '',
                rowData.duration || '',
                rowData.status || '',
                // `${rowData.district_name || ''}, ${rowData.state_name || ''}`,
                rowData.remarks || ''
            ];
            data.push(row);
        });

        return data;
    }


    function styleMonthlySheet(worksheet, data, mergeInfo = []) {
        const range = XLSX.utils.decode_range(worksheet['!ref']);

        if (!worksheet['!merges']) worksheet['!merges'] = [];

        // --- Apply merges safely ---
        for (let row = 1; row <= range.e.r; row++) {
            const rowMerges = Array.isArray(mergeInfo[row - 1]) ? mergeInfo[row - 1] : [];

            rowMerges.forEach(merge => {
                if (!merge || merge.startCol === undefined || merge.endCol === undefined) return;

                worksheet['!merges'].push({
                    s: { r: row, c: merge.startCol },
                    e: { r: row, c: merge.endCol }
                });

                for (let col = merge.startCol; col <= merge.endCol; col++) {
                    const cellAddress = XLSX.utils.encode_cell({ r: row, c: col });
                    if (!worksheet[cellAddress]) {
                        worksheet[cellAddress] = { v: col === merge.startCol ? (merge.text || '') : '', t: 's' };
                    }
                }
            });
        }

        // --- Fill empty cells ---
        for (let r = 0; r <= range.e.r; r++) {
            for (let c = 0; c <= range.e.c; c++) {
                const cellAddress = XLSX.utils.encode_cell({ r, c });
                if (!worksheet[cellAddress]) {
                    worksheet[cellAddress] = { v: '', t: 's' };
                }
            }
        }
    }


    function getMonthlyMergeInfo() {
        const mergeInfo = [];
        $('#monthly-booking-body tr').each(function() {
            const rowMerges = [];
            let colIndex = 6; // start from month columns

            $(this).find('td').slice(6).each(function() {
                const colspan = parseInt($(this).attr('colspan')) || 1;
                const text = $(this).text().trim();

                // Get color class (assumes only one bg-* class per td)
                const classList = $(this).attr('class') || '';
                const classes = classList.split(/\s+/);
                const colorClass = classes.find(c => c.startsWith('bg-')) || 'bg-gray-400';

                if (colspan > 1) {
                    rowMerges.push({
                        startCol: colIndex,
                        endCol: colIndex + colspan - 1,
                        text: text,
                        color: colorClass
                    });
                }

                colIndex += colspan;
            });

            mergeInfo.push(rowMerges);
        });
        return mergeInfo;
    }

    function styleAvailabilitySheet(worksheet) {
        // Style header row
        const headerRow = worksheet.getRow(1);
        headerRow.eachCell((cell) => {
            cell.font = { bold: true, color: { argb: 'FFFFFFFF' } };
            cell.fill = { type: 'pattern', pattern:'solid', fgColor: { argb: 'FF366092' } };
            cell.alignment = { horizontal: 'center', vertical: 'middle' };
            cell.border = {
                top: { style: 'thin' },
                bottom: { style: 'thin' },
                left: { style: 'thin' },
                right: { style: 'thin' }
            };
        });

        // Status colors mapping
        const statusColors = {
            'pending_payment': { bg: 'FFD32929', font: 'FFFFFFFF' }, // red
            'pending_install': { bg: 'FF1C3FAA', font: 'FFFFFFFF' }, // blue
            'ongoing':         { bg: 'FF059669', font: 'FFFFFFFF' }, // green
            'completed':       { bg: 'FFFBC500', font: 'FF000000' }, // yellow
            'dismantle':       { bg: 'FF7F9EB9', font: 'FFFFFFFF' }  // gray
        };

        // Style data rows
        worksheet.eachRow({ includeEmpty: false }, (row, rowNumber) => {
            if (rowNumber === 1) return; // skip header

            row.eachCell((cell, colNumber) => {
                cell.alignment = { horizontal: 'center', vertical: 'middle' };
                cell.border = {
                    top: { style: 'thin', color: { argb: 'FFCCCCCC' } },
                    bottom: { style: 'thin', color: { argb: 'FFCCCCCC' } },
                    left: { style: 'thin', color: { argb: 'FFCCCCCC' } },
                    right: { style: 'thin', color: { argb: 'FFCCCCCC' } }
                };

                // Apply status color for column 8 ("Status")
                if (colNumber === 8 && cell.value) {
                    const status = String(cell.value).toLowerCase();
                    for (const key in statusColors) {
                        if (status.includes(key)) {
                            cell.fill = { type:'pattern', pattern:'solid', fgColor:{ argb: statusColors[key].bg } };
                            cell.font = { color:{ argb: statusColors[key].font }, bold: true };
                            break;
                        }
                    }
                }
            });
        });

        // Set column widths
        const colWidths = [5, 12, 25, 20, 15, 15, 15, 15, 20];
        colWidths.forEach((w, i) => {
            worksheet.getColumn(i + 1).width = w;
        });
    }



    function buildMonthlyBookingTableHead(selectedYear) {
        const shortYear = String(selectedYear).slice(-2);
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        let headerHtml = '<tr>';
        headerHtml += '<th>No</th>';
        headerHtml += '<th>Site #</th>';
        headerHtml += '<th>Location</th>';
        headerHtml += '<th>District</th>';
        headerHtml += '<th>New/Existing</th>';
        headerHtml += '<th>Type</th>';
        headerHtml += '<th>Size</th>';
        months.forEach(month => {
            headerHtml += `<th>${month} '${shortYear}</th>`;
        });
        headerHtml += '</tr>';
        $('#monthly-booking-head').html(headerHtml);
    }

    function loadMonthlyAvailability() {
        const selectedYear = parseInt($('#filterAvailabilityYear').val()) || new Date().getFullYear();

        // Build start/end of year dates
        const startDate = `${selectedYear}-01-01`;
        const endDate   = `${selectedYear}-12-31`;

        $.ajax({
            url: '{{ route("billboard.monthly.availability") }}',
            method: 'GET',
            data: {
                start_date: startDate,
                end_date: endDate,
                year: selectedYear,
                type: $('#filterAvailabilityType').val(),
                site_type: $('#filterAvailabilitySiteType').val(),
                state: $('#filterAvailabilityState').val(),
                district: $('#filterAvailabilityDistrict').val(),
                location: $('#filterAvailabilityLocation').val(),
                status: $('#filterAvailabilityStatus').val()
            },
            success: function (response) {
                const tbody = $('#monthly-booking-body');
                tbody.empty();

                if (!response.data || response.data.length === 0) {
                    tbody.append(`<tr><td colspan="16" class="text-center p-4">No data available</td></tr>`);
                    return;
                }

                response.data.forEach((row, index) => {
                    let html = `<tr data-id="${row.id}" class="cursor-pointer hover:bg-gray-100"> 
                        <td class="border border-gray-300">${index + 1}</td>
                        <td class="border border-gray-300">${row.site_number}</td>
                        <td class="border border-gray-300 expand-cell">
                            <span class="location-text">${row.location}</span>
                            ${row.location.length > 25 
                                ? '<button type="button" class="toggle-location text-blue text-xs ml-2 align-top">&nbsp;&nbsp;&nbsp;[+]</button>' 
                                : ''
                            }
                        </td>
                        <td class="border border-gray-300">${row.area}</td>
                        <td class="border border-gray-300">${row.site_type}</td>
                        <td class="border border-gray-300">${row.type}</td>
                        <td class="border border-gray-300">${row.size}</td>`;
                    
                    // row.months.forEach(month => {
                    //     let cellClass = `border border-gray-300 ${month.color} font-semibold`;
                    //     html += `<td colspan="${month.span}" class="${cellClass}">${month.text}</td>`;
                    // });

                    row.months.forEach(month => {
                        let bookingAttr = "";
                        if (month.booking_id) {
                            bookingAttr = `
                                data-booking-id="${month.booking_id}" 
                                data-status="${month.status}" 
                                data-client="${month.client || ''}" 
                                data-start-date="${month.start_date || ''}" 
                                data-end-date="${month.end_date || ''}"
                                data-remarks="${month.remarks ? month.remarks.replace(/"/g, '&quot;') : ''}"
                            `;
                        }

                        html += `<td colspan="${month.span}" class="border border-gray-300 ${month.color}" ${bookingAttr}>${month.text}</td>`;
                    });

                    html += `</tr>`;
                    tbody.append(html);
                });
            },
            error: function (xhr) {
                console.error("AJAX error:", xhr.responseText);
            }
        });
    }


    // Setup billboard availability datatable
    function initBillboardAvailabilityDatatable() {
        const dt = new Date();
        const formattedDate = `${dt.getFullYear()}${(dt.getMonth() + 1).toString().padStart(2, '0')}${dt.getDate().toString().padStart(2, '0')}`;
        const formattedTime = `${dt.getHours()}:${dt.getMinutes()}:${dt.getSeconds()}`;
        const $fileName = `Billboard_Availability_List_${formattedDate}_${formattedTime}`;

        const table = $('#billboard_availability_table').DataTable({
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
                url: "{{ route('billboard.booking.list') }}",
                type: "POST",
                data: function(d) {
                    d._token = $('meta[name="csrf-token"]').attr('content');
                    d.start_date = $('#filterAvailabilityStart').val();
                    d.end_date = $('#filterAvailabilityEnd').val();
                    d.type = $('#filterAvailabilityType').val();
                    d.site_type = $('#filterAvailabilitySiteType').val();
                    d.status = $('#filterAvailabilityStatus').val();
                    d.state = $('#filterAvailabilityState').val();
                    d.district = $('#filterAvailabilityDistrict').val();
                    d.location = $('#filterAvailabilityLocation').val();
                },
                dataSrc: function(json) {
                    json.recordsTotal = json.recordsTotal;
                    json.recordsFiltered = json.recordsFiltered;
                    return json.data;
                }
            },
            language: {
                emptyTable: "No records found. Please apply at least one filter."
            },
            dom: "lBrtip",
            buttons: [
                {
                    text: "Export Excel",
                    className: "button w-24 rounded-full shadow-md mr-1 mb-2 bg-theme-7 text-white",
                    action: function(e, dt, node, config) {
                        exportCombinedExcel();
                    }
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
                    data: "site_number",
                },
                {
                    data: "company_name",
                    render: function (data, type, row) {
                        if (!data) return "-"; 

                        let shortText = data.length > 30 ? data.substr(0, 30) + "..." : data;

                        return `
                            <span class="location-short">${shortText}</span>
                            ${data.length > 30 
                                ? `<a href="javascript:void(0)" class="read-more text-blue-500 ml-2" 
                                    data-full="${encodeURIComponent(data)}"
                                    data-short="${encodeURIComponent(shortText)}">[+]</a>` 
                                : "" }
                        `;
                    }
                },
                {
                    data: "location_name",
                    name: "location_name",
                    render: function (data, type, row) {
                        if (!data) return "-"; 

                        let shortText = data.length > 30 ? data.substr(0, 30) + "..." : data;

                        return `
                            <span class="location-short">${shortText}</span>
                            ${data.length > 30 
                                ? `<a href="javascript:void(0)" class="read-more text-blue-500 ml-2" 
                                    data-full="${encodeURIComponent(data)}"
                                    data-short="${encodeURIComponent(shortText)}">[+]</a>` 
                                : "" }
                        `;
                    }
                },
                {
                    data: "start_date",
                },
                {
                    data: "end_date",
                },
                {
                    data: "duration",
                },
                {
                    data: "status",
                    type: "readonly",
                    render: function(data, type, row) {
                        let element = ``
                        if (data == 'pending_payment'){
                            element = `<a class="p-2 w-24 rounded-full mr-1 mb-2 bg-theme-6 text-white">Pending Payment</a>`;
                        } else if (data == 'pending_install'){
                            element = `<a class="p-2 w-24 rounded-full mr-1 mb-2 bg-theme-1 text-white">Pending Install</a>`;
                        } else if (data == 'ongoing') {
                            element = `<a class="p-2 w-24 rounded-full mr-1 mb-2 bg-green-600 text-white">Ongoing</a>`;
                        } else if (data == 'completed') {
                            element = `<a class="p-2 w-24 rounded-full mr-1 mb-2 bg-theme-12 text-black">Completed</a>`;
                        } else if (data == 'dismantle') {
                            element = `
                                <div class="flex flex-col space-y-2">
                                    <a href="javascript:;" 
                                    class="button p-2 w-32 bg-theme-13 text-white stock-inventory-btn"
                                    data-id="${row.id}">
                                        Dismantle
                                    </a>
                                </div>`;
                        }
                        
                        return element;
                    }
                },
                {
                    data: "remarks",
                    name: "remarks",
                    render: function (data, type, row) {
                        if (!data) return "-"; // handle empty remarks

                        let shortText = data.length > 30 ? data.substr(0, 30) + "..." : data;

                        return `
                            <span class="remarks-short">${shortText}</span>
                            ${data.length > 30 ? `<a href="javascript:void(0)" class="remarks-read-more text-blue-500 ml-2" data-full="${encodeURIComponent(data)}">Read more</a>` : ""}
                        `;
                    }
                },
                {
                    data: "billboard_id",
                    render: function(data, type, row) {
                        var a = "{{ route('billboard.detail', ['id'=>':data'] )}}".replace(':data', data);
                        let element = 
                        `<div class="flex flex-row">
                            <a href="javascript:;" id="${data}"
                                class="button w-32 inline-block mr-2 mb-2 bg-theme-9 text-white text-center" data-toggle="button" onclick="window.open('${a}')" >
                                Site location
                            </a>
                        </div>`;

                        return element;
                    }
                },
                {
                    data: "id",
                    render: function(data, type, row) {
                        return `
                        <div class="flex items-center space-x-2">
                            <!-- Edit Button -->
                            <a href="javascript:;" 
                            class="edit-booking button w-24 inline-block mr-2 mb-2 bg-theme-1 text-white" 
                            data-id="${data}">
                            Edit
                            </a>

                            <!-- Delete Button -->
                            <a class="flex items-center text-theme-6" href="javascript:;" 
                            data-toggle="modal" data-target="#billboardBookingDeleteModal" 
                            id="delete-${data}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" 
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" 
                                    stroke-width="1.5" stroke-linecap="round" 
                                    stroke-linejoin="round" class="feather feather-trash-2 w-4 h-4 mr-1">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4
                                            a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    <line x1="10" y1="11" x2="10" y2="17"></line>
                                    <line x1="14" y1="11" x2="14" y2="17"></line>
                                </svg> 
                            </a>
                        </div>`;
                    }
                }
            ],
        });

        // Add classes to the "dt-buttons" div
        var dtButtonsDiv = document.querySelector(".dt-buttons");
        if (dtButtonsDiv) {
            dtButtonsDiv.classList.add("mt-2");
        }

        // Update styling for the filter input
        var filterDiv = document.getElementById("billboard_availability_table_filter");
        if (filterDiv) {
            filterDiv.style.float = "right";
            filterDiv.classList.remove('dataTables_filter');

            var inputElement = filterDiv.querySelector("label input");
            if (inputElement) {
                inputElement.classList.add("input", "border", "mt-2", "ml-2", "mr-1", "mb-5");
            }
        }

        // Update styling for the info and paginate elements
        var infoDiv = document.getElementById("billboard_availability_table_info");
        var paginateDiv = document.getElementById("billboard_availability_table_paginate");

        if (infoDiv) {
            infoDiv.style.float = "left";
            infoDiv.classList.add("mt-5");
        }

        if (paginateDiv) {
            paginateDiv.style.float = "right";
            paginateDiv.classList.add("mt-5");
        }

        // Update styling for the "billboard_availability_table_length" div and its select element
        var existingDiv = document.getElementById("billboard_availability_table_length");
        if (existingDiv) {
            existingDiv.classList.remove('dataTables_length');
            existingDiv.classList.add('mt-2', 'mb-1');

            var existingSelect = existingDiv.querySelector('select');
            if (existingSelect) {
                existingSelect.className = 'input sm:w-auto border';
            }
        }

        // Open modal to edit SR
        // editAvailabilityModal();
    };

    



    


    // -------------------
    // Filter Dates
    // -------------------
    document.addEventListener('DOMContentLoaded', function () {
        const startDateInput = document.getElementById('start_date');
        const endDateInput   = document.getElementById('end_date');

        const startPicker = flatpickr(startDateInput, {
            dateFormat: "d/m/Y",   // show DD/MM/YYYY
            onChange: function (selectedDates) {
                if (selectedDates.length > 0) {
                    endPicker.set('minDate', selectedDates[0]);
                    if (endDateInput.value) {
                        const endDate = endPicker.parseDate(endDateInput.value, "d/m/Y");
                        if (endDate < selectedDates[0]) {
                            endDateInput.value = "";
                        }
                    }
                } else {
                    endPicker.set('minDate', null);
                }
            }
        });

        const endPicker = flatpickr(endDateInput, {
            dateFormat: "d/m/Y"
        });
    });



    


    	
    $(document).ready(function() {

        // Global variables
        var filterAvailabilityState;
        var filterAvailabilityDistrict;
        var filterAvailabilityLocation;
        var filterAvailabilityType;
        var filterAvailabilitySiteType;
        var filterAvailabilityStatus;


        var filterServiceRequestStatus;
        var originalServiceRequestId;
        var lastClickedLink;
        let startPicker = null;
        let endPicker = null;

        document.getElementById("inputBookingSubmit").addEventListener("click", inputBookingSubmit);

        $(document).on('click', '.toggle-location', function (e) {
            e.preventDefault();
            const cell = $(this).closest('.expand-cell');
            cell.toggleClass('expanded');
            $(this).text(cell.hasClass('expanded') ? '  [-]' : '  [+]');
        });

        // Handle Read more / Read less for location & remarks
        $(document).on('click', '.read-more', function () {
            const $this = $(this);
            const $cell = $this.closest('td');
            const $span = $cell.find('span');

            let fullText = decodeURIComponent($this.data('full'));
            let shortText = decodeURIComponent($this.data('short'));

            if ($this.text() === "[+]") {
                $span.text(fullText);
                $this.text("[-]");
            } else {
                $span.text(shortText);
                $this.text("[+]");
            }
        });

        $(document).on("click", ".remarks-read-more", function () {
            let fullText = decodeURIComponent($(this).data("full"));
            $("#remarksContent").val(fullText);

            // Use your helper instead of jQuery modal
            openAltEditorModal("#remarksModal");
        });

        $('#inputBookingForm').on('submit', function (e) {
            e.preventDefault(); // Prevent default form submission
            inputBookingSubmit(); // Call your AJAX function
        });

        function inputBookingSubmit() {
            const start_date = document.getElementById('start_date').value;
            const end_date   = document.getElementById('end_date').value;

            if (!start_date || !end_date) {
                alert("Please select both Start and End dates.");
                return;
            }

            document.getElementById("inputBookingSubmit").disabled = true;
            document.getElementById('inputBookingSubmit').style.display = 'none';

            $.ajax({
                type: 'POST',
                url: "{{ route('billboard.booking.create') }}",
                data: {
                    _token          : $('meta[name="csrf-token"]').attr('content'),
                    client_id       : $("#inputBookingCompany").val(),
                    site_number     : $("#hiddenBookingSiteNo").val(),
                    state_id        : $("#hiddenBookingState").val(),
                    district_id     : $("#hiddenBookingDistrict").val(),
                    location_id     : $("#hiddenBookingLocation").val(),
                    start_date      : start_date,
                    end_date        : end_date,
                    status          : $("#inputBookingStatus").val(),
                    artwork_by      : $("#inputBookingArtworkBy").val(),
                    dbp_approval    : $("#inputBookingDBPApproval").val(),
                    remarks         : $("#inputBookingRemarks").val(),
                },

                success: function(response) {
                    // Close modal
                    const element = "#addBookingModal";
                    closeAltEditorModal(element);

                    // Success toast
                    window.showSubmitToast("Successfully added.", "#91C714");

                    // Clear inputs
                    $('#inputBookingCompany').val('').trigger('change');
                    $('#inputBookingSiteNo').val('');
                    document.getElementById("inputBookingState").value = "";
                    document.getElementById("inputBookingDistrict").value = "";
                    document.getElementById("inputBookingLocation").value = "";
                    document.getElementById("inputBookingStatus").value = "";
                    document.getElementById("inputBookingArtworkBy").value = "";
                    document.getElementById("inputBookingDBPApproval").value = "";
                    document.getElementById("inputBookingRemarks").value = "";
                    if (startPicker) startPicker.clear();
                    if (endPicker) endPicker.clear();

                    // Reload table
                    // $('#billboard_availability_table').DataTable().ajax.reload();

                    window.location.href = "{{ route('billboard.booking.index') }}";

                    // Reset button
                    document.getElementById("inputBookingSubmit").disabled = false;
                    document.getElementById('inputBookingSubmit').style.display = 'inline-block';
                },
                error: function(xhr) {
                    const response = JSON.parse(xhr.responseText);
                    const error = "Error: " + response.error;

                    // Show fail toast
                    window.showSubmitToast(error, "#D32929");

                    document.getElementById("inputBookingSubmit").disabled = false;
                    document.getElementById('inputBookingSubmit').style.display = 'inline-block';
                }
            });
        }

        $('.select2-client').select2({
            placeholder: "Select a client",
            allowClear: true,
            width: '100%'
        });

        $('.select2-state').select2({
            placeholder: "Select a state",
            allowClear: true,
            width: '100%'
        });

        $('.select2-district').select2({
            placeholder: "Select a district",
            allowClear: true,
            width: '100%'
        });

        $('.select2-location').select2({
            placeholder: "Select a location",
            allowClear: true,
            width: '100%'
        });

        // Function to reload the DataTable when any filter changes
        function setupAutoFilter() {
            const tableElement = $('#billboard_availability_table');

            // Reload DataTable
            if ($.fn.DataTable.isDataTable(tableElement)) {
                const table = tableElement.DataTable();

                $('#filterAvailabilityCompany, #filterAvailabilityState, #filterAvailabilityDistrict, #filterAvailabilityLocation, #filterAvailabilityType, #filterAvailabilitySiteType, #filterAvailabilityStatus, #filterAvailabilityStart, #filterAvailabilityEnd, #filterAvailabilityYear')
                    .on('change', function () {
                        const selectedYear = $('#filterAvailabilityYear').val();

                        table.ajax.reload();
                        buildMonthlyBookingTableHead(selectedYear);
                        loadMonthlyAvailability();
                    });
            }

            // Also reload monthly table if only it exists
            $('#filterAvailabilityCompany, #filterAvailabilityState, #filterAvailabilityDistrict, #filterAvailabilityLocation, #filterAvailabilityType, #filterAvailabilitySiteType, #filterAvailabilityStatus, #filterAvailabilityStart, #filterAvailabilityEnd, #filterAvailabilityYear')
                .on('change', function () {
                    const selectedYear = $('#filterAvailabilityYear').val();
                    buildMonthlyBookingTableHead(selectedYear);
                    loadMonthlyAvailability(); // <-- add this in case DataTable not initialized
                });
        }


        function setupMonthlyAvailabilityFilter() {
            const filterSelectors = '#filterAvailabilityState, #filterAvailabilityDistrict, #filterAvailabilityLocation, #filterAvailabilityStatus, #filterAvailabilityStart, #filterAvailabilityEnd, #filterAvailabilityYear';

            $(filterSelectors).on('change', function () {
                const selectedYear = $('#filterAvailabilityYear').val();
                buildMonthlyBookingTableHead(selectedYear);
                loadMonthlyAvailability(); // this function contains your $.ajax code
            });
        }

        $(document).ready(function () {
            const selectedYear = $('#filterAvailabilityYear').val();
            setupAutoFilter(); // your existing DataTable filter
            setupMonthlyAvailabilityFilter(); // new for monthly table
            buildMonthlyBookingTableHead(selectedYear);
            loadMonthlyAvailability(); // load once on page load
        });



        /**
         * Escape HTML to avoid XSS
         */
        function escapeHtml(text) {
            return $('<div>').text(text).html();
        }



        // Init Flatpickr only once when modal is opened
        $('[data-target="#addBillboardBookingModal"]').on('click', function () {
            setTimeout(() => {
                if (!startPicker) {
                    startPicker = flatpickr("#start_date", {
                        dateFormat: "Y-m-d",
                        onChange: function (selectedDates, dateStr) {
                            if (endPicker) {
                                endPicker.set('minDate', dateStr);
                            }
                        }
                    });
                }

                if (!endPicker) {
                    endPicker = flatpickr("#end_date", {
                        dateFormat: "Y-m-d"
                    });
                }
            }, 200); // slight delay after modal opens
        });

        // Store the ID of the last clicked modal when it's triggered
        (function() {
            $(document).on('click', "[data-toggle='modal']", function() {
                lastClickedLink = $(this).attr('id');
            });
        })();

        // click on table 1 cell with booking_id to open edit status modal
        $(document).on("click", "#monthly-booking-body td[data-booking-id]", function() {
            const bookingId = $(this).data("booking-id");
            const client = $(this).data("client");
            const start = $(this).data("start-date");
            const end = $(this).data("end-date");
            const status = $(this).data("status");
            const remarks = $(this).data("remarks");

            if (!bookingId) return;

            $("#editStatusModal").data("booking-id", bookingId);

            $("#editBookingClient").text(client || "N/A");
            $("#editBookingDates").text(start && end ? `${start} – ${end}` : "N/A");
            $("#editBookingStatus").val(status);
            $("#editBookingRemarks").val(remarks);

            openAltEditorModal("#editStatusModal");
        });

        // Open modal to edit Billboard Booking (via Edit button)
        $(document).on("click", ".edit-booking", function () {
            booking_id = $(this).data("id"); // from data-id attribute

            let row = $('#billboard_availability_table')
                        .DataTable()
                        .row($(this).closest('tr'))
                        .data();

            // Fill form fields
            $("#editStatusModal").data("booking-id", row.id);
            $("#editBookingClient").text(row.company_name || "N/A");
            $("#editBookingDates").text(row.start_date && row.end_date ? `${row.start_date} – ${row.end_date}` : "N/A");
            $("#editBookingStatus").val(row.status);
            $("#editBookingRemarks").val(row.remarks);

            // Open modal
            openAltEditorModal("#editStatusModal");
        });


        $(document).on("click", "#cancelModal", function() {
            $("#editStatusModal").addClass("hidden").removeClass("flex");
        });

        $("#editStatusModal form").on("submit", function(e) {
            e.preventDefault();

            const bookingId = $("#editStatusModal").data("booking-id");
            const status = $("#editBookingStatus").val();
            const remarks = $("#editBookingRemarks").val();

            $.ajax({
                url: "{{ route('billboard.update.status') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: bookingId,
                    status: status,
                    remarks: remarks
                },
                success: function(response) {
                    $("#editStatusModal").addClass("hidden").removeClass("flex");
                    window.showSubmitToast("Successfully added.", "#91C714");
                    location.reload(); // reload table
                },
                error: function(xhr) {
                    alert("Failed to update status");
                    console.error(xhr.responseText);
                }
            });
        });

        






        // Edit Billboard Booking
        function editBooking() {
            var status = document.getElementById("editBookingStatus").value;
            var remarks = document.getElementById("editBookingRemarks").value;

            $.ajax({
                type: 'POST',
                url: "{{ route('billboard.booking.edit') }}",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    status: status,
                    remarks: remarks,
                    booking_id: booking_id,
                },
                success: function(response) {
                    // Close modal after successfully edited
                    var element = "#editBookingModal";
                    closeAltEditorModal(element);

                    // Show successful toast
                    window.showSubmitToast("Successfully updated.", "#91C714");

                    // Clean fields
                    document.getElementById("editBookingStatus").value = "";
                    document.getElementById("editBookingRemarks").value = "";

                    // Reload table
                    $('#billboard_booking_table').DataTable().ajax.reload();
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















        // Edit Billboard Availability
        function editAvailability() {
            var status = document.getElementById("editBookingStatus").value;
            var remarks = document.getElementById("editBookingRemarks").value;

            $.ajax({
                type: 'POST',
                url: "{{ route('billboard.booking.edit') }}",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    status: status,
                    remarks: remarks,
                    booking_id: booking_id,
                },
                success: function(response) {
                    // Close modal after successfully edited
                    var element = "#editAvailabilityModal";
                    closeAltEditorModal(element);

                    // Show successful toast
                    window.showSubmitToast("Successfully updated.", "#91C714");

                    // Clean fields
                    document.getElementById("editBookingStatus").value = "";
                    document.getElementById("editBookingRemarks").value = "";

                    // Reload table
                    $('#billboard_availability_table').DataTable().ajax.reload();
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

        // Open modal
        function openAltEditorModal(element) {
            cash(element).modal('show');
        }
        // Close modal
        function closeAltEditorModal(element) {
            cash(element).modal('hide');
        }

        


        
    
        

        initBillboardAvailabilityDatatable();
        setupAutoFilter();

        // Open modal to edit Billboard Booking (only via Edit button)
        $(document).on("click", ".new-job-order", function () {
            const table = $('#billboard_availability_table').DataTable();
            const row = table.row($(this).closest('tr')).data();

            if (!row) return;

            // Always reset modal
            $("#inputBookingForm")[0].reset();
            $(".select2-client").val("").trigger("change");

            // Prefill static fields
            $("#inputBookingSiteNo").val(row.site_number);
            $("#hiddenBookingSiteNo").val(row.site_number);

            $("#inputBookingState").val(row.state_id).trigger("change");
            $("#hiddenBookingState").val(row.state_id);

            // --- Prefill district after districts load ---
            $.ajax({
                url: '{{ route("location.getDistricts") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    state_id: row.state_id
                },
                success: function (districts) {
                    $('#inputBookingDistrict').empty().append('<option value="">-- Select District --</option>');
                    districts.forEach(function (district) {
                        $('#inputBookingDistrict').append(
                            `<option value="${district.id}">${district.name}</option>`
                        );
                    });

                    // Now set the district
                    $("#inputBookingDistrict").val(row.district_id).trigger("change");
                    $("#hiddenBookingDistrict").val(row.district_id);

                    // --- Prefill location after locations load ---
                    $.ajax({
                        url: '{{ route("location.getLocations") }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            district_id: row.district_id
                        },
                        success: function (locations) {
                            $('#inputBookingLocation').empty().append('<option value="">-- Select Location --</option>');
                            locations.forEach(function (location) {
                                $('#inputBookingLocation').append(
                                    `<option value="${location.id}">${location.name}</option>`
                                );
                            });

                            // Finally set the location
                            $("#inputBookingLocation").val(row.location_id).trigger("change");
                            $("#hiddenBookingLocation").val(row.location_id);
                        }
                    });
                }
            });

            // Open modal
            $("#addBookingModal").modal("show");
        });




        $(document).off('click', '.edit-availability').on('click', '.edit-availability', function() {
            const table = $('#billboard_availability_table').DataTable();
            const rowData = table.row($(this).closest('tr')).data(); // full row

            // Map boolean to select value
            if (rowData.is_available) {
                $('#editAvailability').val('1'); // Available
            } else {
                $('#editAvailability').val('2'); // Not Available
            }

            openAltEditorModal('#editAvailabilityModal');
        });


        $('#saveAvailabilityButton').on('click', function (e) {
            e.preventDefault();

            let id = $('#editAvailabilityModal').data('billboard-id');
            let availability = $('#editAvailability').val(); // "1" or "2"

            $.ajax({
                url: '{{ route("billboard.availability.update") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    is_available: availability
                },
                success: function(response) {
                    // Close modal
                    closeAltEditorModal("#editAvailabilityModal");

                    // Show toast
                    window.showSubmitToast("Availability updated.", "#91C714");

                    // Reload table
                    $('#billboard_availability_table').DataTable().ajax.reload();
                },
                error: function(xhr) {
                    var response = JSON.parse(xhr.responseText);
                    window.showSubmitToast("Error: " + response.error, "#D32929");
                }
            });
        });

        























        
        
        
        
        
        
        
        // Edit SR 
        function editServiceRequest() {
            var description = document.getElementById("serviceRequestEditDescription").value;
            var client_remark = document.getElementById("serviceRequestEditClientRemark").value;

            $.ajax({
                type: 'POST',
                url: "{{ route('serviceRequest.edit') }}",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    desc: description,
                    client_remark: client_remark,
                    id: originalServiceRequestId,
                },
                success: function(response) {
                    // Close modal after successfully edited
                    var element = "#serviceRequestEditModal";
                    closeAltEditorModal(element);

                    // Show successful toast
                    window.showSubmitToast("Successfully updated.", "#91C714");

                    // Clean fields
                    document.getElementById("serviceRequestEditDescription").value = "";
                    document.getElementById("serviceRequestEditClientRemark").value = "";

                    // Reload table
                    $('#billboard_availability_table').DataTable().ajax.reload();
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
        
        
        // Open modal to edit SR
        function serviceRequestEditModal() {
            // Remove previous click event listeners
            $(document).off('click', "[id^='billboard_availability_table'] tbody tr td:not(:last-child)");

            $(document).on('click', "[id^='billboard_availability_table'] tbody tr td:not(:last-child)", function() {

                // Grab row client company id
                originalServiceRequestId = $(event.target).closest('tr').find('td:nth-last-child(2) a').attr('id').split('-')[1];

                // Place values to edit form fields in the modal
                document.getElementById("serviceRequestEditDescription").value = $(event.target).closest('tr').find('td:nth-child(' + '3' + ')').text();
                document.getElementById("serviceRequestEditClientRemark").value = $(event.target).closest('tr').find('td:nth-child(' + '4' + ')').text();

                // Open modal
                var element = "#serviceRequestEditModal";
                openAltEditorModal(element);
            });
        }

        var table = $('#table').DataTable({
            "dom": 'rtip',
            "paging":   false,
            "ordering": false,
            "info":     false
        });
    });

</script>
@endsection('script')