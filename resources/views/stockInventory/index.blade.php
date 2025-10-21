@extends('layouts.app')

@section('head')
<title>BGOC Outdoor System - Stock Inventory</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;500;600&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
    :root {
        --paper-bg: #F7F7F9;
        --surface: #FFFFFF;
        --ink: #1C1E26;
        --hairline: #EAEAEA;
        --brand-dark: #22255b;
        --brand-light: #4bbbed;
        --destructive: #d33831;
        --muted: #6B7280;
    }

    body {
        background-color: var(--paper-bg);
        color: var(--ink);
        font-family: 'Inter', sans-serif;
    }

    .serif {
        font-family: 'EB Garamond', serif;
    }

    .small-caps {
        font-variant: small-caps;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        font-size: 11px;
        color: var(--muted);
        font-weight: 500;
    }

    .hairline {
        border: 1px solid var(--hairline);
    }

    .card-elegant {
        background: var(--surface);
        border-radius: 1rem;
        border: 1px solid rgba(234, 234, 234, 0.7);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    }

    .btn-primary {
        background: var(--brand-dark);
        color: white;
        padding: 0.625rem 1.5rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 150ms ease;
        border: none;
        cursor: pointer;
    }

    .btn-primary:hover {
        opacity: 0.9;
        transform: translateY(-1px);
    }

    .btn-primary:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(75, 187, 237, 0.3);
    }

    .btn-secondary {
        background: transparent;
        color: var(--ink);
        padding: 0.625rem 1.5rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 500;
        border: 1px solid #D1D5DB;
        transition: all 150ms ease;
        cursor: pointer;
    }

    .btn-secondary:hover {
        background: #F9FAFB;
    }

    .btn-destructive {
        background: var(--destructive);
        color: white;
        padding: 0.625rem 1.5rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 150ms ease;
        border: none;
        cursor: pointer;
    }

    .input-elegant {
        height: 2.75rem;
        border-radius: 0.75rem;
        border: 1px solid #D1D5DB;
        padding: 0 1rem;
        font-size: 0.875rem;
        transition: all 150ms ease;
        background: white;
    }

    .input-elegant:focus {
        outline: none;
        border-color: var(--brand-light);
        box-shadow: 0 0 0 3px rgba(75, 187, 237, 0.1);
    }

    .select-elegant {
        height: 2.75rem;
        border-radius: 0.75rem;
        border: 1px solid #D1D5DB;
        padding: 0 1rem;
        font-size: 0.875rem;
        transition: all 150ms ease;
        background: white;
    }

    .select-elegant:focus {
        outline: none;
        border-color: var(--brand-light);
        box-shadow: 0 0 0 3px rgba(75, 187, 237, 0.1);
    }

    #inventory_table {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
    }

    #inventory_table thead th {
        background: var(--surface);
        color: var(--muted);
        font-weight: 600;
        text-align: left;
        padding: 1rem 0.75rem;
        border-bottom: 2px solid var(--hairline);
        font-size: 11px;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    #inventory_table tbody td {
        padding: 1rem 0.75rem;
        border-bottom: 1px solid var(--hairline);
        font-size: 0.875rem;
        color: var(--ink);
        vertical-align: middle;
    }

    #inventory_table tbody tr {
        background: white;
        transition: all 150ms ease;
    }

    #inventory_table tbody tr:hover {
        background: #FAFAFA;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
    }

    .section-header {
        background: #F9FAFB;
        font-weight: 600;
        color: var(--ink);
    }

    .numeric-col {
        font-variant-numeric: tabular-nums;
        text-align: right;
    }

    .filter-chip {
        display: inline-flex;
        align-items: center;
        padding: 0.375rem 0.75rem;
        background: var(--brand-dark);
        color: white;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .filter-chip-remove {
        margin-left: 0.5rem;
        cursor: pointer;
        opacity: 0.8;
    }

    .filter-chip-remove:hover {
        opacity: 1;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
    }

    .empty-state svg {
        width: 4rem;
        height: 4rem;
        color: #D1D5DB;
        margin: 0 auto 1rem;
    }

    .modal {
        position: fixed;
        inset: 0;
        z-index: 10000;
        display: none;
        align-items: center;
        justify-content: center;
        background: rgba(0,0,0,.5);
    }
    .modal.show {
        display: flex !important;
    }

    .modal__content,
    .modal-content-elegant {
        max-height: 90vh;
        overflow: auto;
    }

    .modal-content-elegant {
        background: var(--surface);
        border-radius: 1rem;
        padding: 2rem;
        max-width: 90rem;
        margin: 2rem auto;
    }

    .bg-stock-in {
        background: rgba(251, 146, 60, 0.05);
    }

    .bg-balance {
        background: rgba(250, 204, 21, 0.05);
    }

    .bg-stock-out {
        background: rgba(34, 197, 94, 0.05);
    }

    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate {
        font-size: 0.875rem;
        color: var(--muted);
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0.375rem 0.75rem;
        margin: 0 0.125rem;
        border-radius: 0.5rem;
        border: 1px solid var(--hairline);
        background: white;
        color: var(--ink);
        transition: all 150ms ease;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: var(--brand-dark);
        color: white;
        border-color: var(--brand-dark);
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: var(--brand-dark);
        color: white;
        border-color: var(--brand-dark);
    }

    .select2-container--default .select2-selection--single {
        height: 2.75rem;
        border-radius: 0.75rem;
        border: 1px solid #D1D5DB;
        padding: 0.5rem 1rem;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 1.75rem;
        padding-left: 0;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 2.75rem;
    }
</style>
@endsection

@section('content')
<div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Top Bar -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl serif font-semibold text-ink mb-1">Stock Inventory</h1>
                <p class="text-sm text-muted">Manage your stock transactions and balances</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="javascript:;"
                   data-toggle="modal"
                   data-target="#inventoryAddModal"
                   class="btn-primary inline-flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Add New Stock Inventory
                </a>
            </div>
        </div>
    </div>

    <!-- Filter Panel -->
    <div class="card-elegant p-6 mb-8">
        <h3 class="small-caps mb-4">Filters</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="small-caps block mb-2">Contractor</label>
                <select class="select-elegant w-full" id="filterStockContractor">
                    <option selected value="">All Contractors</option>
                    @foreach ($contractors as $contractor)
                        <option value="{{ $contractor->id }}">{{ $contractor->name }} - {{ $contractor->company_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="small-caps block mb-2">Client</label>
                <select class="select-elegant w-full" id="filterStockClient">
                    <option selected value="">All Clients</option>
                    @foreach ($clientcompany as $clientcomp)
                        <option value="{{ $clientcomp->id }}">{{ $clientcomp->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="small-caps block mb-2">Start Date</label>
                <input type="date" id="filterStockStartDate" class="input-elegant w-full" />
            </div>
            <div>
                <label class="small-caps block mb-2">End Date</label>
                <input type="date" id="filterStockEndDate" class="input-elegant w-full" />
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card-elegant overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table table-report" id="inventory_table">
                <thead>
                    <tr class="bg-theme-1 text-white">
                        <th width="5%">No.</th>
                        <th class="bg-orange-500 text-white">Contractor</th>
                        <th class="bg-orange-500 text-white">Client</th>
                        <th class="bg-orange-500 text-white">Site</th>
                        <th class="bg-orange-500 text-white">Type</th>
                        <th class="bg-orange-500 text-white">Size</th>
                        <th class="bg-orange-500 text-white">Quantity</th>
                        <th class="bg-orange-500 text-white">Remarks</th>
                        <th class="bg-orange-500 text-white">Date In</th>
                        <th class="bg-yellow-400 text-black">Bal - Contractor</th>
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
                <tbody></tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@section('modal_content')
<!-- BEGIN: Inventory Add Modal -->
<div class="modal items-center justify-center" id="inventoryAddModal">
    <div class="modal-content-elegant">
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
            <h2 class="text-2xl serif font-semibold">Add Stock Inventory</h2>
        </div>

        <form id="addStockInventoryForm">
            <div class="mb-6">
    <label class="small-caps block mb-2">Contractor</label>
    <input type="text" class="input-elegant w-full sm:w-64" id="editContractorName" readonly>
    <input type="hidden" id="editContractorId">
</div>

            <div class="grid grid-cols-2 gap-8">

                <!-- LEFT COLUMN: IN INVENTORY -->
                <div class="bg-orange-50 p-4 rounded-lg">
                    <h3 class="font-bold text-orange-600 mb-3">Stock In Inventory</h3>

                    <div class="mb-3">
                        <label class="block small-caps">Balance - Contractor</label>
                        <input type="number" class="input-elegant w-full" id="balContractor" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="block small-caps">Date In</label>
                        <input type="date" class="input-elegant w-full" id="inputDateIn">
                    </div>

                    <div class="mb-3">
                        <label class="block small-caps">Remarks</label>
                        <input type="text" class="input-elegant w-full" id="inputRemarksIn">
                    </div>
                    <div class="flex items-center sm:py-3 border-gray-200 dark:border-dark-5">
                        <h2 class="font-medium text-base mr-auto">Add Sites</h2>
                    </div>
                    <div id="siteInContainer">
                        <div class="siteIn">
                            <div class="mb-3">
                                <label class="block small-caps">Client/Contractor</label>
                                <select class="select-elegant w-full select2" name="clients_in[]">
                                    <option selected value="">Select an option</option>
                                    <optgroup label="Clients">
                                        @foreach ($clientcompany as $clientcomp)
                                            <option value="client-{{ $clientcomp->id }}">
                                                {{ $clientcomp->name }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="Contractors">
                                        @foreach ($contractors as $contractor)
                                            <option value="contractor-{{ $contractor->id }}">
                                                {{ $contractor->name }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="block small-caps">Site</label>
                                <select class="select-elegant w-full select2" name="sites_in[]">
                                    <option selected value="">Select an option</option>
                                    @foreach ($billboards as $billboard)
                                        <option
                                            value="{{ $billboard->id }}"
                                            data-type="{{ $billboard->type }}"
                                            data-size="{{ $billboard->size }}">
                                            {{ $billboard->site_number }} - {{ $billboard->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="block small-caps">Type</label>
                                <input type="text" class="input-elegant w-full" name="types_in[]" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="block small-caps">Size</label>
                                <input type="text" class="input-elegant w-full" name="sizes_in[]" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="block small-caps"><strong>Quantity In</strong></label>
                                <input type="number" class="input-elegant w-full" name="qtys_in[]">
                            </div>
                            <div class="mb-3">
                                <a href="javascript:void(0);" class="btn-destructive text-sm" onclick="removeSiteIn(this)">
                                    Remove
                                </a>
                            </div>
                        </div>
                    </div>
                    <button type="button" onclick="siteInAdd()" class="btn-primary">Add Site</button>
                </div>

                <!-- RIGHT COLUMN: OUT INVENTORY -->
                <div class="bg-green-50 p-4 rounded-lg">
                    <h3 class="font-bold text-green-600 mb-3">Stock Out Inventory</h3>

                    <div class="mb-3">
                        <label class="block small-caps">Bal - BGOC</label>
                        <input type="number" class="input-elegant w-full" id="balBgoc" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="block small-caps">Date Out</label>
                        <input type="date" class="input-elegant w-full" id="inputDateOut">
                    </div>

                    <div class="mb-3">
                        <label class="block small-caps">Remarks</label>
                        <input type="text" class="input-elegant w-full" id="inputRemarksOut">
                    </div>
                    <div class="flex items-center sm:py-3 border-gray-200 dark:border-dark-5">
                        <h2 class="font-medium text-base mr-auto">Add Sites</h2>
                    </div>
                    <div id="siteOutContainer">
                        <div class="siteOut">
                            <div class="mb-3">
                                <label class="block small-caps">Client/Contractor</label>
                                <select class="select-elegant w-full select2" name="clients_out[]">
                                    <option selected value="">Select an option</option>
                                    <optgroup label="Clients">
                                        @foreach ($clientcompany as $clientcomp)
                                            <option value="client-{{ $clientcomp->id }}">
                                                {{ $clientcomp->name }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="Contractors">
                                        @foreach ($contractors as $contractor)
                                            <option value="contractor-{{ $contractor->id }}">
                                                {{ $contractor->name }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="block small-caps">Site</label>
                                <select class="select-elegant w-full select2" name="sites_out[]">
                                    <option selected value="">Select an option</option>
                                    @foreach ($billboards as $billboard)
                                        <option
                                            value="{{ $billboard->id }}"
                                            data-type="{{ $billboard->type }}"
                                            data-size="{{ $billboard->size }}">
                                            {{ $billboard->site_number }} - {{ $billboard->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="block small-caps">Type</label>
                                <input type="text" class="input-elegant w-full" name="types_out[]" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="block small-caps">Size</label>
                                <input type="text" class="input-elegant w-full" name="sizes_out[]" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="block small-caps"><strong>Quantity Out</strong></label>
                                <input type="number" class="input-elegant w-full" name="qtys_out[]">
                            </div>
                            <div class="mb-3">
                                <a href="javascript:void(0);" class="btn-destructive text-sm" onclick="removeSiteOut(this)">
                                    Remove
                                </a>
                            </div>
                        </div>
                    </div>
                    <button type="button" onclick="siteOutAdd()" class="btn-primary">Add Site</button>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <button type="button" data-dismiss="modal" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- BEGIN: Inventory Edit Modal -->
<div class="modal items-center justify-center" id="inventoryEditModal">
    <div class="modal-content-elegant">
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
            <h2 class="text-2xl serif font-semibold">Edit Stock Inventory</h2>
        </div>

        <form id="inventoryEditForm">
            <div class="mb-6">
                <label class="small-caps block mb-2">Contractor</label>
                <input type="text" class="input-elegant w-full sm:w-64" id="editContractorName" readonly>
            </div>

            <div class="grid grid-cols-2 gap-8">
                <!-- LEFT COLUMN: IN INVENTORY -->
                <div class="bg-orange-50 p-4 rounded-lg">
                    <h3 class="font-bold text-orange-600 mb-3">Stock In Inventory</h3>

                    <div class="mb-3">
                        <label class="block small-caps">Balance - Contractor</label>
                        <input type="number" class="input-elegant w-full" id="editBalanceContractor" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="block small-caps">Date In</label>
                        <input type="date" class="input-elegant w-full" id="editDateIn">
                    </div>
                    <div class="mb-3">
                        <label class="block small-caps">Client/Contractor</label>
                        <select class="select-elegant w-full select2" id="editClientIn">
                            <option value="">Select an option</option>
                            @foreach ($clientcompany as $clientcomp)
                                <option value="{{ $clientcomp->id }}">{{ $clientcomp->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="block small-caps">Site</label>
                        <select class="select-elegant w-full select2" id="editBillboardIn">
                            <option value="">Select an option</option>
                            @foreach ($billboards as $billboard)
                                <option value="{{ $billboard->id }}"
                                        data-type="{{ $billboard->type }}"
                                        data-size="{{ $billboard->size }}">
                                    {{ $billboard->site_number }} - {{ $billboard->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="block small-caps">Type</label>
                        <input type="text" class="input-elegant w-full" id="editTypeIn" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="block small-caps">Size</label>
                        <input type="text" class="input-elegant w-full" id="editSizeIn" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="block small-caps"><strong>Quantity In</strong></label>
                        <input type="number" class="input-elegant w-full editQtyIn" id="editQtyIn">
                    </div>
                    <div class="mb-3">
                        <label class="block small-caps">Remarks</label>
                        <input type="text" class="input-elegant w-full" id="editRemarksIn">
                    </div>
                </div>

                <!-- RIGHT COLUMN: OUT INVENTORY -->
                <div class="bg-green-50 p-4 rounded-lg">
                    <h3 class="font-bold text-green-600 mb-3">Stock Out Inventory</h3>

                    <div class="mb-3">
                        <label class="block small-caps">Bal - BGOC</label>
                        <input type="number" class="input-elegant w-full" id="editBalanceBgoc" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="block small-caps">Date Out</label>
                        <input type="date" class="input-elegant w-full" id="editDateOut">
                    </div>
                    <div class="mb-3">
                        <label class="block small-caps">Client/Contractor</label>
                        <select class="select-elegant w-full select2" id="editClientOut">
                            <option value="">Select an option</option>
                            @foreach ($clientcompany as $clientcomp)
                                <option value="{{ $clientcomp->id }}">{{ $clientcomp->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="block small-caps">Site</label>
                        <select class="select-elegant w-full select2" id="editBillboardOut">
                            <option value="">Select an option</option>
                            @foreach ($billboards as $billboard)
                                <option value="{{ $billboard->id }}"
                                        data-type="{{ $billboard->type }}"
                                        data-size="{{ $billboard->size }}">
                                    {{ $billboard->site_number }} - {{ $billboard->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="block small-caps">Type</label>
                        <input type="text" class="input-elegant w-full" id="editTypeOut" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="block small-caps">Size</label>
                        <input type="text" class="input-elegant w-full" id="editSizeOut" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="block small-caps"><strong>Quantity Out</strong></label>
                        <input type="number" class="input-elegant w-full editQtyOut" id="editQtyOut">
                    </div>
                    <div class="mb-3">
                        <label class="block small-caps">Remarks</label>
                        <input type="text" class="input-elegant w-full" id="editRemarksOut">
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <button type="button" data-dismiss="modal" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>

<!-- BEGIN: Inventory Delete Modal -->
<div class="modal" id="inventoryDeleteModal">
    <div class="modal__content">
        <div class="p-5 text-center">
            <i data-feather="x-circle" class="w-16 h-16 text-theme-6 mx-auto mt-3"></i>
            <div class="text-3xl mt-5">Are you sure?</div>
            <div class="text-gray-600 mt-2">Confirm deleting this transaction? This process cannot be undone.</div>
        </div>
        <div class="px-5 pb-8 text-center">
            <button type="button" data-dismiss="modal" class="btn-secondary mr-2">Cancel</button>
            <button type="button"
            class="btn-destructive" id="inventoryDeleteButton">Delete</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- searchable dropdown -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
// ============================================
// GLOBAL VARIABLES (declared once at the top)
// ============================================
let lastClickedLink = null;
let stockInventoryId = null;
let transactionInId = null;
let transactionOutId = null;

// ============================================
// DATE VALIDATION
// ============================================
const startDateInput = document.getElementById("filterStockStartDate");
const endDateInput = document.getElementById("filterStockEndDate");

if (startDateInput && endDateInput) {
    startDateInput.addEventListener("change", function () {
        endDateInput.min = this.value;
        if (endDateInput.value && endDateInput.value < this.value) {
            endDateInput.value = this.value;
        }
    });

    endDateInput.addEventListener("change", function () {
        startDateInput.max = this.value;
        if (startDateInput.value && startDateInput.value > this.value) {
            startDateInput.value = this.value;
        }
    });
}

// ============================================
// HELPER FUNCTIONS
// ============================================
function openAltEditorModal(selector) {
    const id = selector.startsWith('#') ? selector.slice(1) : selector;
    window.openModal(id);
}

function closeAltEditorModal(selector) {
    const id = selector.startsWith('#') ? selector.slice(1) : selector;
    window.closeModal(id);
}

// ============================================
// ADD SITE FUNCTIONS
// ============================================
window.siteInAdd = function () {
    let html = `
        <br><div class="siteIn space-y-4">
            <div>
                <label class="small-caps block mb-2">Client/Contractor</label>
                <select class="select-elegant w-full select2" name="clients_in[]">
                    <option selected value="">Select an option</option>
                    <optgroup label="Clients">
                        @foreach ($clientcompany as $clientcomp)
                            <option value="client-{{ $clientcomp->id }}">
                                {{ $clientcomp->name }}
                            </option>
                        @endforeach
                    </optgroup>
                    <optgroup label="Contractors">
                        @foreach ($contractors as $contractor)
                            <option value="contractor-{{ $contractor->id }}">
                                {{ $contractor->name }}
                            </option>
                        @endforeach
                    </optgroup>
                </select>
            </div>
            <div>
                <label class="small-caps block mb-2">Site</label>
                <select class="select-elegant w-full select2" name="sites_in[]">
                    <option selected value="">Select an option</option>
                    @foreach ($billboards as $billboard)
                        <option
                            value="{{ $billboard->id }}"
                            data-type="{{ $billboard->type }}"
                            data-size="{{ $billboard->size }}">
                            {{ $billboard->site_number }} - {{ $billboard->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="small-caps block mb-2">Type</label>
                    <input type="text" class="input-elegant w-full" name="types_in[]" readonly>
                </div>
                <div>
                    <label class="small-caps block mb-2">Size</label>
                    <input type="text" class="input-elegant w-full" name="sizes_in[]" readonly>
                </div>
            </div>
            <div>
                <label class="small-caps block mb-2">Quantity In</label>
                <input type="number" class="input-elegant w-full" name="qtys_in[]">
            </div>
            <div>
                <a href="javascript:void(0);" class="btn-destructive text-sm" onclick="removeSiteIn(this)">
                    Remove
                </a>
            </div>
        </div>`;

    $("#siteInContainer").append(html);
    const $addModal = $('#inventoryAddModal');
    $("#siteInContainer .select2").select2({
        width: '100%',
        dropdownParent: $addModal
    });
    updateTotalIn();
}

window.removeSiteIn = function (el) {
    el.closest(".siteIn").remove();
    updateTotalIn();
}

window.siteOutAdd = function () {
    let html = `
        <br><div class="siteOut space-y-4">
            <div>
                <label class="small-caps block mb-2">Client/Contractor</label>
                <select class="select-elegant w-full select2" name="clients_out[]">
                    <option selected value="">Select an option</option>
                    <optgroup label="Clients">
                        @foreach ($clientcompany as $clientcomp)
                            <option value="client-{{ $clientcomp->id }}">
                                {{ $clientcomp->name }}
                            </option>
                        @endforeach
                    </optgroup>
                    <optgroup label="Contractors">
                        @foreach ($contractors as $contractor)
                            <option value="contractor-{{ $contractor->id }}">
                                {{ $contractor->name }}
                            </option>
                        @endforeach
                    </optgroup>
                </select>
            </div>
            <div>
                <label class="small-caps block mb-2">Site</label>
                <select class="select-elegant w-full select2" name="sites_out[]">
                    <option selected value="">Select an option</option>
                    @foreach ($billboards as $billboard)
                        <option
                            value="{{ $billboard->id }}"
                            data-type="{{ $billboard->type }}"
                            data-size="{{ $billboard->size }}">
                            {{ $billboard->site_number }} - {{ $billboard->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="small-caps block mb-2">Type</label>
                    <input type="text" class="input-elegant w-full" name="types_out[]" readonly>
                </div>
                <div>
                    <label class="small-caps block mb-2">Size</label>
                    <input type="text" class="input-elegant w-full" name="sizes_out[]" readonly>
                </div>
            </div>
            <div>
                <label class="small-caps block mb-2">Quantity Out</label>
                <input type="number" class="input-elegant w-full" name="qtys_out[]">
            </div>
            <div>
                <a href="javascript:void(0);" class="btn-destructive text-sm" onclick="removeSiteOut(this)">
                    Remove
                </a>
            </div>
        </div>`;

    $("#siteOutContainer").append(html);
    const $addModal = $('#inventoryAddModal');
    $("#siteOutContainer .select2").select2({
        width: '100%',
        dropdownParent: $addModal
    });
    updateTotalOut();
}

window.removeSiteOut = function (el) {
    el.closest(".siteOut").remove();
    updateTotalOut();
}

// ============================================
// AUTO-FILL TYPE & SIZE
// ============================================
$(document).on('change', 'select[name="sites_in[]"], select[name="sites_out[]"]', function() {
    const selected = $(this).find(':selected');
    const type = selected.data('type') || '';
    const size = selected.data('size') || '';
    const row = $(this).closest('.siteIn, .siteOut');
    row.find('input[name="types_in[]"], input[name="types_out[]"]').val(type);
    row.find('input[name="sizes_in[]"], input[name="sizes_out[]"]').val(size);
});

// ============================================
// TOTAL CALCULATIONS
// ============================================
function updateTotalIn() {
    let total = 0;
    document.querySelectorAll("input[name='qtys_in[]']").forEach(function(input) {
        let val = parseInt(input.value) || 0;
        total += val;
    });
    document.getElementById("balContractor").value = total;
}

function updateTotalOut() {
    let total = 0;
    document.querySelectorAll("input[name='qtys_out[]']").forEach(function(input) {
        let val = parseInt(input.value) || 0;
        total += val;
    });
    document.getElementById("balBgoc").value = total;
}

$(document).on("input", "input[name='qtys_in[]']", updateTotalIn);
$(document).on("input", "input[name='qtys_out[]']", updateTotalOut);

function updateTotalInEdit() {
    let total = 0;
    document.querySelectorAll('.editQtyIn').forEach(input => {
        total += parseInt(input.value) || 0;
    });
    const elem = document.getElementById("editBalanceContractor");
    if (elem) elem.value = total;
}

function updateTotalOutEdit() {
    let total = 0;
    document.querySelectorAll('.editQtyOut').forEach(input => {
        total += parseInt(input.value) || 0;
    });
    const elem = document.getElementById("editBalanceBgoc");
    if (elem) elem.value = total;
}

// ============================================
// DELETE FUNCTION
// ============================================
function inventoryDeleteButton() {
    if (!lastClickedLink || lastClickedLink.indexOf('-') === -1) {
        closeAltEditorModal("#inventoryDeleteModal");
        window.showSubmitToast("No transaction ID found to delete.", "#D32929");
        return;
    }

    const id = lastClickedLink.split("-")[1];

    if (!id || id === 'undefined' || id === 'null' || id === '') {
        closeAltEditorModal("#inventoryDeleteModal");
        window.showSubmitToast("Invalid transaction ID.", "#D32929");
        return;
    }

    $.ajax({
        type: 'POST',
        url: "{{ route('stockInventory.delete') }}",
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        data: { id: id },
        success: function () {
            closeAltEditorModal("#inventoryDeleteModal");
            window.showSubmitToast("Successfully deleted.", "#91C714");
            $('#inventory_table').DataTable().ajax.reload();
            lastClickedLink = null;
        },
        error: function (xhr) {
            let msg = "Delete failed";
            try {
                const response = JSON.parse(xhr.responseText);
                msg = response.error || msg;
            } catch(e) {}
            closeAltEditorModal("#inventoryDeleteModal");
            window.showSubmitToast(msg, "#D32929");
        }
    });
}

// ============================================
// DOCUMENT READY
// ============================================
$(document).ready(function() {
    const delBtn = document.getElementById("inventoryDeleteButton");
    if (delBtn) delBtn.addEventListener("click", inventoryDeleteButton);

    const $addModal = $('#inventoryAddModal');
    const $editModal = $('#inventoryEditModal');

    $('.select2').select2({
        placeholder: "Select an option",
        allowClear: true,
        width: '100%',
        dropdownParent: $addModal
    });

    // Initialize Select2 for Edit Modal
    $('#editClientIn, #editBillboardIn, #editClientOut, #editBillboardOut').select2({
        placeholder: "Select an option",
        allowClear: true,
        width: '100%',
        dropdownParent: $editModal
    });

    // ADD FORM SUBMIT
    $('#addStockInventoryForm').off('submit').on('submit', function(e) {
        e.preventDefault();
        inventoryAddButton();
    });

    // EDIT FORM SUBMIT
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
    contractor_id: $('#editContractorId').val(),
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
            url: "{{ route('stockInventory.edit') }}",
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

    // AUTO FILTER SETUP
    function setupAutoFilter() {
        const tableElement = $('#inventory_table');
        const filterSelectors = '#filterStockContractor, #filterStockClient, #filterStockStartDate, #filterStockEndDate';

        if ($.fn.DataTable.isDataTable(tableElement)) {
            const table = tableElement.DataTable();
            $(filterSelectors).on('change', function () {
                table.ajax.reload();
            });
        }
    }

    // ADD INVENTORY FUNCTION
    function inventoryAddButton() {
        let contractor_id = $("#inputContractorName").val();
        let date_in = $("#inputDateIn").val();
        let date_out = $("#inputDateOut").val();
        let remarks_in = $("#inputRemarksIn").val();
        let remarks_out = $("#inputRemarksOut").val();
        let balance_contractor = $("#balContractor").val();
        let balance_bgoc = $("#balBgoc").val();

        let sites_in = [];
        $("#siteInContainer .siteIn").each(function () {
            let siteId = $(this).find("select[name='sites_in[]']").val();
            let rawVal = $(this).find("select[name='clients_in[]']").val();
            if (!rawVal) return;

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

        let sites_out = [];
        $("#siteOutContainer .siteOut").each(function () {
            let siteId = $(this).find("select[name='sites_out[]']").val();
            let rawVal = $(this).find("select[name='clients_out[]']").val();
            if (!rawVal) return;

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

        $.ajax({
            type: 'POST',
            url: "{{ route('stockInventory.create') }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            contentType: 'application/json; charset=utf-8',
            dataType: 'json',
            data: JSON.stringify({
                contractor_id: contractor_id,
                date_in: date_in,
                date_out: date_out,
                remarks_in: remarks_in,
                remarks_out: remarks_out,
                sites_in: sites_in,
                sites_out: sites_out,
                balance_contractor: balance_contractor,
                balance_bgoc: balance_bgoc
            }),
           success: function(response) {
    closeAltEditorModal("#inventoryAddModal");
    window.showSubmitToast("Successfully added.", "#91C714");

    // Clear form
    $('#addStockInventoryForm')[0].reset();
    $('#inventoryAddModal select').val('').trigger('change');
    $('#siteInContainer').empty();
    $('#siteOutContainer').empty();
    $('#balContractor').val('');
    $('#balBgoc').val('');

    // Re-add initial site rows
    siteInAdd();
    siteOutAdd();

    // Force complete reload with callback
    var table = $('#inventory_table').DataTable();
    table.ajax.reload(function() {
        console.log('Table reloaded successfully');
    }, true); // true = reset paging
},
            error: function(xhr, status, error) {
                console.error("AJAX Error:", xhr.responseText);

                let message = "An error occurred while saving data.";
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.error) message = response.error;
                } catch (e) {
                    message = xhr.responseText || message;
                }

                window.showSubmitToast("Error: " + message, "#D32929");
            }
        });
    }

    // DATATABLE INITIALIZATION
    $('#inventory_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('stockInventory.list') }}",
            type: "POST",
            cache: false,
            data: function (d) {
                d._token = "{{ csrf_token() }}";
                d.contractor_id = $('#filterStockContractor').val();
                d.client_id = $('#filterStockClient').val();
                d.start_date = $('#filterStockStartDate').val();
                d.end_date = $('#filterStockEndDate').val();
                return d;
            },
            dataSrc: function(json) {
                let newData = [];
                json.data.forEach(row => {
                    let inClients = row.client_in_name ? row.client_in_name.split(',') : [];
                    let inSites = row.site_in ? row.site_in.split(',') : [];
                    let inDates = row.date_in ? row.date_in.split(',') : [];
                    let inRemarks = row.remarks_in ? row.remarks_in.split(',') : [];
                    let inQty = row.quantity_in ? row.quantity_in.split(',') : [];

                    let outClients = row.client_out_name ? row.client_out_name.split(',') : [];
                    let outSites = row.site_out ? row.site_out.split(',') : [];
                    let outDates = row.date_out ? row.date_out.split(',') : [];
                    let outRemarks = row.remarks_out ? row.remarks_out.split(',') : [];
                    let outQty = row.quantity_out ? row.quantity_out.split(',') : [];

                    let rowCount = Math.max(inDates.length, outDates.length, 1);

                    for (let i = 0; i < rowCount; i++) {
                        newData.push({
                            contractor: row.contractor,
                            balance_contractor: row.balance_contractor,
                            client_in_name: inClients[i] || '',
                            site_in: inSites[i] || '',
                            date_in: inDates[i] || '',
                            remarks_in: inRemarks[i] || '',
                            quantity_in: inQty[i] || '',
                            billboard_type_in: row.billboard_type_in || '',
                            billboard_size_in: row.billboard_size_in || '',
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
            { data: 'quantity_in', name: 'transactions_in.quantity', className: 'numeric-col' },
            { data: 'remarks_in', name: 'transactions_in.remarks' },
            { data: 'date_in', name: 'transactions_in.transaction_date' },
            { data: 'balance_contractor', name: 'stock_inventories.balance_contractor', className: 'numeric-col' },
            { data: 'date_out', name: 'transactions_out.transaction_date' },
            { data: 'quantity_out', name: 'transactions_out.quantity', className: 'numeric-col' },
            { data: 'billboard_size_out', name: 'billboards.size' },
            { data: 'billboard_type_out', name: 'billboards.type' },
            { data: 'site_out', name: 'site_out.name' },
            { data: 'client_out_name', name: 'client_companies.name' },
            { data: 'remarks_out', name: 'transactions_out.remarks' },
            {
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    let transId = row.transaction_in_id || row.transaction_out_id;
                    let typeLabel = row.transaction_in_id ? 'IN' : 'OUT';

                    // Only show buttons if there's a valid transaction ID
                    if (!transId || transId === '' || transId === 'null') {
                        return '<div class="flex items-center justify-center">—</div>';
                    }

                    return `
                        <div class="flex items-center justify-center space-x-3">
                            <a href="javascript:;" class="btn-primary text-xs px-3 py-1 edit-inventory"
                            data-transaction-in-id="${row.transaction_in_id || ''}"
                            data-transaction-out-id="${row.transaction_out_id || ''}"
                            data-stock-inventory-id="${row.stock_inventory_id}">
                            Edit
                            </a>
                            <a href="javascript:;" class="text-red-600 hover:text-red-800 delete-inventory"
                                data-transaction-id="${transId}"
                                data-transaction-type="${typeLabel}"
                                id="delete-${transId}"
                                title="Delete ${typeLabel} Transaction">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
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
        drawCallback: function(settings) {
            let api = this.api();
            let rows = api.rows({ page: 'current' }).nodes();
            let lastStockId = null;
            let groupStart = null;

            api.rows({ page: 'current' }).every(function(rowIdx) {
                let data = this.data();
                let stockId = data.stock_inventory_id;

                if (stockId !== lastStockId) {
                    if (groupStart !== null) {
                        let rowCount = rowIdx - groupStart;
                        $('td:eq(1)', rows[groupStart]).attr('rowspan', rowCount);
                        $('td:eq(9)', rows[groupStart]).attr('rowspan', rowCount);
                        for (let j = groupStart + 1; j < rowIdx; j++) {
                            $('td:eq(1)', rows[j]).hide();
                            $('td:eq(9)', rows[j]).hide();
                        }
                    }
                    groupStart = rowIdx;
                    lastStockId = stockId;
                }
            });

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

    // EDIT INVENTORY CLICK
    $(document).on('click', '.edit-inventory', function () {
        stockInventoryId = $(this).data('stock-inventory-id');
        transactionInId  = $(this).data('transaction-in-id') || null;
        transactionOutId = $(this).data('transaction-out-id') || null;

        $.get(`/inventory/${stockInventoryId}/edit`, {
    transaction_in_id: transactionInId,
    transaction_out_id: transactionOutId
}, function (data) {
    let source = data.in || data.out;
    if (source) {
        $('#editContractorName').val(source.contractor_name || '');
        $('#editContractorId').val(source.contractor_id || '');
        $('#editBalanceContractor').val(source.balance_contractor || 0);
        $('#editBalanceBgoc').val(source.balance_bgoc || 0);
    }

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
                    .val('').trigger('change');
            }

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
                    .val('').trigger('change');
            }

            openAltEditorModal("#inventoryEditModal");
        });
    });

    // AUTO-FILL TYPE & SIZE FOR EDIT MODAL
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

    // DELETE CLICK HANDLER
    $(document).on('click', '.delete-inventory', function () {
        lastClickedLink = $(this).attr('id');
        openAltEditorModal('#inventoryDeleteModal');
    });

    setupAutoFilter();
});
</script>
@endsection
