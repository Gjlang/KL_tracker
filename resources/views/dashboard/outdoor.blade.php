@php use Illuminate\Support\Str; @endphp

@php
    /** @var \Illuminate\Support\Collection $existing */
    $existing = isset($existing) && $existing ? collect($existing) : collect();

    function omd($existing, $id, $m, $key, $type)
    {
        $row = $existing->get("{$id}:{$m}:{$key}");
        if (!$row) {
            return '';
        }

        if ($type === 'date') {
            $v = $row->value_date ?? null;
            if (!$v) {
                return '';
            }
            // If it's already 'YYYY-MM-DD', just return it
        if (is_string($v) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $v)) {
            return $v;
        }
        // If it's Carbon/DateTime or some other string, normalize
            try {
                return Carbon::parse($v)->format('Y-m-d');
            } catch (\Throwable $e) {
                return '';
            }
        }

        // text
        return $row->value_text ?? '';
    }

    // New display formatter function
    use Illuminate\Support\Carbon;

    function df($v, $fmt = 'd/m/Y')
    {
        if (empty($v)) {
            return '';
        }
        try {
            return $v instanceof \DateTimeInterface ? $v->format($fmt) : Carbon::parse($v)->format($fmt);
        } catch (\Throwable $e) {
            return ''; // or return (string)$v;
        }
    }
@endphp

@push('head')
    <link rel="icon" type="image/x-icon" href="{{ asset('images/bluedale_logo_1.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* Style Tokens */
        .ink {
            color: #1C1E26;
        }

        .card {
            @apply bg-white rounded-2xl border border-neutral-200/70 shadow-sm;
        }

        .hairline {
            border-color: #EAEAEA;
        }

        .btn-primary {
            @apply bg-[#22255b] text-white hover:opacity-90 focus:ring-2 focus:ring-[#4bbbed] rounded-full px-5 py-2 transition-all duration-150;
        }

        .btn-secondary {
            @apply border border-neutral-300 text-neutral-800 hover:bg-neutral-50 rounded-full px-5 py-2 transition-all duration-150;
        }

        .btn-ghost {
            @apply text-neutral-700 hover:bg-neutral-50 rounded-full px-4 py-2 transition-all duration-150;
        }

        .chip {
            @apply bg-neutral-100 text-neutral-700 px-3 py-1 rounded-full text-xs flex items-center gap-1;
        }

        .tabular-nums {
            font-variant-numeric: tabular-nums;
        }

        /* Typography */
        .serif {
            font-family: 'Playfair Display', 'EB Garamond', serif;
        }

        .sans {
            font-family: 'Inter', 'Proxima Nova', sans-serif;
        }

        /* Table headers with small caps */
        .table-header {
            @apply text-xs uppercase tracking-wider font-medium;
            color: #6B7280;
            letter-spacing: 0.05em;
        }

        /* Hover effects */
        .hover-lift:hover {
            @apply shadow-sm;
            transform: translateY(-1px);
        }

        /* Focus rings */
        .focus-ring:focus {
            @apply ring-1 ring-[#4bbbed]/20 outline-none;
        }

        /* Monthly grid specific styles */
        .monthly-input {
            @apply h-10 text-xs rounded border border-neutral-200 focus:ring-1 focus:ring-[#4bbbed]/20 focus:border-[#4bbbed] transition-all duration-150;
        }

        .monthly-input:hover {
            @apply ring-1 ring-[#4bbbed]/20;
        }
    </style>
@endpush

<x-app-shell title="Outdoor Monthly Ongoing Jobs">
    <div class="min-h-screen bg-[#F7F7F9]">
        @include('partials.sidebar')
        <main class="flex-1 overflow-y-auto">
            <div class="p-6 md:p-8 max-w-full">

                {{-- Header Section --}}
                <div class="mb-8">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                        <div>
                            <h1 class="serif text-4xl font-light ink mb-2">Outdoor Monthly Ongoing Jobs</h1>
                            <p class="sans text-neutral-600">Manage and track outdoor advertising campaigns</p>
                        </div>
                        <div>
                            <a href="{{ route('dashboard') }}" class="btn-ghost">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                                </svg>
                                Dashboard
                            </a>
                        </div>
                    </div>

                    {{-- Flash Messages --}}
                    @if (session('status'))
                        <div class="mb-6 p-4 card bg-green-50 border-green-200 text-green-800">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                {{ session('status') }}
                            </div>
                        </div>
                    @endif
                </div>
                {{-- Filters Panel --}}
                <div class="mb-6 card">
                    <div class="p-6">
                        <form method="GET" action="{{ url()->current() }}">

                            {{-- Preserve cross-page filters if you need them --}}
                            <input type="hidden" name="status" value="{{ request('status') }}">
                            {{-- Keep section category "Outdoor" locked (do NOT reuse this name for subproduct) --}}
                            <input type="hidden" name="category" value="Outdoor">

                            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                                {{-- Category (locked) --}}
                                <div class="space-y-2">
                                    <label class="sans table-header">Category</label>
                                    <div class="h-11 flex items-center">
                                        <span class="chip bg-[#22255b] text-white">OUTDOOR</span>
                                    </div>
                                </div>

                                {{-- Year --}}
                                <div class="space-y-2">
                                    <label for="year" class="sans table-header">Year</label>
                                    @php
                                        $currentYear = (int) ($year ?? now()->year);
                                    @endphp
                                    <select id="year" name="year"
                                        class="w-full h-11 sans rounded border border-neutral-200 focus-ring px-3">
                                        @foreach ($years ?? [now()->year] as $y)
                                            <option value="{{ (int) $y }}" @selected((int) $y === $currentYear)>
                                                {{ (int) $y }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Month --}}
                                <div class="space-y-2">
                                    <label for="month" class="sans table-header">Month</label>
                                    @php
                                        $mSel = (int) (request('month') ?? 0);
                                        $monthNames = [
                                            1 => 'Jan',
                                            2 => 'Feb',
                                            3 => 'Mar',
                                            4 => 'Apr',
                                            5 => 'May',
                                            6 => 'Jun',
                                            7 => 'Jul',
                                            8 => 'Aug',
                                            9 => 'Sep',
                                            10 => 'Oct',
                                            11 => 'Nov',
                                            12 => 'Dec',
                                        ];
                                    @endphp
                                    <select id="month" name="month"
                                        class="w-full h-11 sans rounded border border-neutral-200 focus-ring px-3">
                                        <option value="0">All months</option>
                                        @foreach ($monthNames as $mi => $mn)
                                            <option value="{{ $mi }}" @selected($mSel === $mi)>
                                                {{ $mn }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Subproduct (NEW) --}}
                                <div class="space-y-2">
                                    <label for="product_category" class="sans table-header">Subproduct</label>
                                    @php
                                        $subproducts = [
                                            'BB',
                                            'TB',
                                            'Newspaper',
                                            'Bunting',
                                            'Flyers',
                                            'Star',
                                            'Signages',
                                        ];
                                        $pc = (string) request('product_category', '');
                                    @endphp
                                    <select id="product_category" name="product_category"
                                        class="w-full h-11 sans rounded border border-neutral-200 focus-ring px-3">
                                        <option value="">All</option>
                                        @foreach ($subproducts as $opt)
                                            <option value="{{ $opt }}" @selected($pc === $opt)>
                                                {{ $opt }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Search --}}
                                <div class="space-y-2">
                                    <label for="search" class="sans table-header">Search</label>
                                    <input id="search" name="search" type="text" value="{{ request('search') }}"
                                        class="w-full h-11 input sans" placeholder="Company / product / site…">
                                </div>
                            </div>

                            <div class="mt-4 flex flex-wrap gap-3">
                                <button type="submit" class="btn-primary h-11">Apply Filters</button>
                                <a href="{{ url()->current() }}" class="btn-secondary h-11">Clear All</a>
                            </div>

                            {{-- Active filter chips with clear links --}}
                            @php
                                $hasYear = request()->filled('year');
                                $hasMonth = request()->filled('month') && (int) request('month') !== 0;
                                $hasSearch = trim((string) request('search')) !== '';
                                $hasSubprod = trim((string) request('product_category')) !== '';
                            @endphp

                            @if ($hasYear || $hasMonth || $hasSearch || $hasSubprod)
                                <div class="mt-4 flex flex-wrap items-center gap-2">
                                    <span class="sans text-sm text-neutral-600">Active:</span>

                                    <a class="chip"
                                        href="{{ request()->fullUrlWithQuery([
                                            'search' => request('search'),
                                            'month' => request('month'),
                                            'year' => request('year'),
                                            'product_category' => request('product_category'),
                                        ]) }}">
                                        CATEGORY: OUTDOOR
                                    </a>

                                    @if ($hasYear)
                                        <a class="chip" href="{{ request()->fullUrlWithQuery(['year' => null]) }}">
                                            YEAR: {{ (int) request('year') }} <span class="ml-1">×</span>
                                        </a>
                                    @endif

                                    @if ($hasMonth)
                                        <a class="chip" href="{{ request()->fullUrlWithQuery(['month' => 0]) }}">
                                            MONTH: {{ $monthNames[(int) request('month')] ?? '' }} <span
                                                class="ml-1">×</span>
                                        </a>
                                    @endif

                                    @if ($hasSubprod)
                                        <a class="chip"
                                            href="{{ request()->fullUrlWithQuery(['product_category' => null]) }}">
                                            SUBPRODUCT: {{ request('product_category') }} <span class="ml-1">×</span>
                                        </a>
                                    @endif

                                    @if ($hasSearch)
                                        <a class="chip" href="{{ request()->fullUrlWithQuery(['search' => null]) }}">
                                            SEARCH: "{{ Str::limit(request('search'), 20) }}" <span
                                                class="ml-1">×</span>
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </form>

                        {{-- Clone structure button (OUTSIDE the filter form; show only when there is no data) --}}
                        @if (($existing ?? collect())->isEmpty())
                            <form method="POST" action="{{ route('coordinator.outdoor.cloneYear') }}" class="mt-3">
                                @csrf
                                <input type="hidden" name="to_year" value="{{ (int) ($year ?? now()->year) }}">
                                <input type="hidden" name="from_year"
                                    value="{{ (int) ($year ?? now()->year) - 1 }}">
                                <button type="submit" class="btn btn-soft">
                                    Clone previous year's structure (no values)
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

            </div>
            {{-- Action Bar --}}
            <div class="mb-6 card">
                <div class="p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <a href="{{ route('coordinator.outdoor.index') }}" class="btn-secondary">
                        Outdoor Coordinator List
                    </a>

                    <a href="{{ route('outdoor.whiteboard.index') }}"
                        class="inline-flex items-center px-4 py-2 rounded-lg bg-[#22255b] text-white hover:opacity-90">
                        OUTDOOR Whiteboard
                    </a>
                    <a href="{{ route('coordinator.outdoor.exportMatrix', ['year' => $year]) }}" class="btn-primary">
                        Export CSV
                    </a>
                </div>
            </div>

            {{-- Data Table --}}
            <div class="card overflow-hidden">
                @if (($rows ?? [])->count() > 0)
                    {{-- Small pager control bar --}}
                    <div class="flex items-center justify-between px-4 py-2 hairline border-b">
                        <span class="text-xs text-neutral-600">Showing <span id="rowsPerPage">15</span> rows per
                            page</span>
                        <div class="flex items-center gap-2">
                            <button id="prevPage" type="button"
                                class="px-3 py-1.5 text-xs rounded-full border border-neutral-300 text-neutral-700 hover:bg-neutral-50 disabled:opacity-40"
                                disabled>Prev</button>
                            <span id="pageInfo" class="text-xs text-neutral-600">1 / 1</span>
                            <button id="nextPage" type="button"
                                class="px-3 py-1.5 text-xs rounded-full border border-neutral-300 text-neutral-700 hover:bg-neutral-50 disabled:opacity-40">Next</button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table id="outdoorTable" class="min-w-[3250px] w-full">
                            <thead class="bg-neutral-50 sticky top-0 z-10">
                                <tr class="hairline border-b">
                                    <th class="px-3 py-2 text-right w-12">NO</th>
                                    <th class="px-4 py-4 text-left table-header" style="min-width:120px;">
                                        Date Created
                                    </th>
                                    <th class="px-4 py-4 text-left table-header" style="min-width:220px;">
                                        Company
                                    </th>
                                    <th class="px-4 py-4 text-left table-header" style="min-width:160px;">
                                        Product
                                    </th>
                                    <th class="px-4 py-4 text-left table-header" style="min-width:220px;">
                                        Site(s)
                                    </th>
                                    <th class="px-4 py-4 text-left table-header" style="min-width:140px;">
                                        Category
                                    </th>
                                    <th class="px-4 py-4 text-left table-header" style="min-width:120px;">
                                        Start Date
                                    </th>
                                    <th class="px-4 py-4 text-left table-header" style="min-width:120px;">
                                        End Date
                                    </th>
                                    @php
                                        $monthLabels = [
                                            1 => 'January',
                                            2 => 'February',
                                            3 => 'March',
                                            4 => 'April',
                                            5 => 'May',
                                            6 => 'June',
                                            7 => 'July',
                                            8 => 'August',
                                            9 => 'September',
                                            10 => 'October',
                                            11 => 'November',
                                            12 => 'December',
                                        ];
                                    @endphp
                                    @foreach ($monthLabels as $mNum => $mName)
                                        <th class="px-3 py-4 text-left table-header bg-neutral-100"
                                            style="min-width:180px;">
                                            {{ $mName }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody id="outdoorTbody" class="bg-white divide-y divide-neutral-200">
                                @foreach ($rows as $index => $row)
                                    @php
                                        $company = $row->company;
                                        $start = $row->start_date ?? ($row->date ?? null);
                                        $end = $row->date_finish ?? ($row->end_date ?? null);
                                        $startDisp = df($start);
                                        $endDisp = df($end);
                                    @endphp
                                    <tr class="hover:bg-neutral-50 hover-lift transition-all duration-150"
                                        data-idx="{{ $loop->index }}">
                                        <td class="px-3 py-2 text-right tabular-nums">{{ $loop->iteration }}</td>
                                        <td class="px-4 py-3 sans text-sm text-neutral-600 tabular-nums">
                                            {{ $row->created_at ? \Carbon\Carbon::parse($row->created_at)->format('d/m/y') : '' }}
                                        </td>

                                        <td class="px-4 py-3 sans text-sm font-medium ink">
                                            <div class="max-w-[200px] truncate" title="{{ $company }}">
                                                {{ $company }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 sans text-sm text-neutral-700">
                                            {{ $row->product }}
                                        </td>
                                        @php
                                            // 1) Prefer JOIN aliases if controller selected them:
                                            $siteCode = isset($row->site_code) ? (string) $row->site_code : '';
                                            $district = isset($row->district_name) ? (string) $row->district_name : '';

                                            // 2) If still empty, try Eloquent relations (when ->with(...) was used)
                                            if (($siteCode === '' || $district === '') && isset($row->billboard)) {
                                                $bb = $row->billboard;
                                                if ($siteCode === '') {
                                                    $siteCode = (string) ($bb?->site_number ?? '');
                                                }
                                                if ($district === '') {
                                                    $district = (string) ($bb?->location?->district?->name ?? '');
                                                }
                                            }

                                            // 3) Compose display
                                            $siteDisplay =
                                                $siteCode !== '' && $district !== ''
                                                    ? $siteCode . ' - ' . $district
                                                    : ($siteCode !== ''
                                                        ? $siteCode
                                                        : ($district !== ''
                                                            ? $district
                                                            : '—'));
                                        @endphp
                                        <td class="px-4 py-3 sans text-sm text-neutral-700"
                                            title="{{ $siteDisplay }}">
                                            {{ $siteDisplay }}
                                        </td>

                                        <td class="px-4 py-3">
                                            <span class="chip text-[#22255b] bg-[#22255b]/10">
                                                {{ $row->product_category ?? 'Outdoor' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 sans text-sm text-neutral-700 tabular-nums">
                                            {{ $startDisp ? \Carbon\Carbon::createFromFormat('d/m/Y', $startDisp)->format('d/m/y') : '' }}

                                        </td>
                                        <td class="px-4 py-3 sans text-sm text-[#d33831] tabular-nums font-medium">
                                            {{ $endDisp ? \Carbon\Carbon::createFromFormat('d/m/Y', $endDisp)->format('d/m/y') : '' }}

                                        </td>

                                        {{-- Month cells --}}
                                        @foreach ($monthLabels as $mNum => $mName)
                                            @php
                                                $savedStatus = omd(
                                                    $existing,
                                                    $row->outdoor_item_id,
                                                    $mNum,
                                                    'status',
                                                    'text',
                                                );
                                                $savedDate = omd(
                                                    $existing,
                                                    $row->outdoor_item_id,
                                                    $mNum,
                                                    'installed_on',
                                                    'date',
                                                );
                                            @endphp
                                            <td class="px-3 py-3 bg-neutral-50/50">
                                                <div class="space-y-2">
                                                    <!-- Status dropdown -->
                                                    <select class="status-dropdown w-full monthly-input text-xs"
                                                        data-master="{{ $row->id }}"
                                                        data-item="{{ $row->outdoor_item_id }}"
                                                        data-year="{{ $year }}"
                                                        data-month="{{ $mNum }}" data-kind="text"
                                                        name="status_{{ $row->id }}_{{ $year }}_{{ $mNum }}"
                                                        onchange="saveOutdoorCell(this); setDropdownColor(this);">
                                                        <option value="">Select status...</option>
                                                        <option value="Installation"
                                                            {{ $savedStatus === 'Installation' ? 'selected' : '' }}>
                                                            Installation</option>
                                                        <option value="Dismantle"
                                                            {{ $savedStatus === 'Dismantle' ? 'selected' : '' }}>
                                                            Dismantle</option>
                                                        <option value="Artwork"
                                                            {{ $savedStatus === 'Artwork' ? 'selected' : '' }}>
                                                            Artwork</option>
                                                        <option value="Payment"
                                                            {{ $savedStatus === 'Payment' ? 'selected' : '' }}>
                                                            Payment</option>
                                                        <option value="Ongoing"
                                                            {{ $savedStatus === 'Ongoing' ? 'selected' : '' }}>
                                                            Ongoing</option>
                                                        <option value="Renewal"
                                                            {{ $savedStatus === 'Renewal' ? 'selected' : '' }}>
                                                            Renewal</option>
                                                        <option value="Completed"
                                                            {{ $savedStatus === 'Completed' ? 'selected' : '' }}>
                                                            Completed</option>
                                                        <option value="Material"
                                                            {{ $savedStatus === 'Material' ? 'selected' : '' }}>
                                                            Material</option>
                                                    </select>

                                                    <!-- Date input -->
                                                    <input type="date" value="{{ $savedDate }}"
                                                        class="w-full monthly-input text-xs tabular-nums"
                                                        data-master="{{ $row->id }}"
                                                        data-item="{{ $row->outdoor_item_id }}"
                                                        data-year="{{ $year }}"
                                                        data-month="{{ $mNum }}" data-kind="date"
                                                        name="date_{{ $row->id }}_{{ $year }}_{{ $mNum }}"
                                                        onblur="saveOutdoorCell(this)">

                                                    <!-- Status indicators -->
                                                    <div class="flex items-center justify-between text-xs">
                                                        <small class="hidden text-green-600" data-saved>Saved</small>
                                                        <small class="hidden text-[#d33831]" data-error></small>
                                                    </div>
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    {{-- Empty State --}}
                    <div class="px-6 py-16 text-center">
                        <svg class="w-12 h-12 text-neutral-300 mx-auto mb-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="serif text-lg text-neutral-600 mb-2">No ongoing jobs found</p>
                        <p class="sans text-sm text-neutral-500 mb-4">Try adjusting your filters or create new outdoor
                            campaigns</p>
                        <a href="{{ route('coordinator.outdoor.index') }}" class="btn-secondary">
                            Outdoor Coordinator List
                        </a>


                    </div>



                @endif
            </div>

    </div>
    </main>
    </div>


</x-app-shell>

<script>
    // ------- Small helpers -------
    const getCsrf = () =>
        document.querySelector('meta[name="csrf-token"]')?.content || "{{ csrf_token() }}";

    const toInt = (v) => {
        const n = Number(v);
        return Number.isFinite(n) ? n : null;
    };

    const normalizeDate = (v) => {
        // Accept "", null, undefined
        if (!v) return null;
        // If already YYYY-MM-DD (native date inputs), just return
        // You can add more parsing if your backend needs it
        return v;
    };

    async function saveOutdoorCell(el) {
        // Required IDs from data-attrs on the input/select
        const outdoor_item_id = toInt(el.dataset.item);
        if (!outdoor_item_id) {
            console.warn('saveOutdoorCell: missing data-item (outdoor_item_id).');
            return;
        }
        // Optional: still store master_file_id for joins/filtering if you like
        const master_file_id = toInt(el.dataset.master);

        const year = toInt(el.dataset.year); // optional for monthly tables
        const month = toInt(el.dataset.month); // optional for monthly tables
        const kind = el.dataset.kind || 'text'; // "text" | "date"

        // Field mapping (adjust if your backend expects different keys)
        const payload = {
            outdoor_item_id,
            master_file_id, // ok to be null
            year, // ok to be null (if your monthly endpoint uses it)
            month, // ok to be null
            field_key: kind === 'date' ? 'installed_on' : 'status', // <-- adjust if needed
            field_type: kind, // 'text' or 'date'
        };

        if (kind === 'date') {
            payload.value_date = normalizeDate(el.value);
        } else {
            payload.value_text = (el.value ?? '').toString();
        }

        const td = el.closest('td');
        const savedBadge = td?.querySelector('[data-saved]');
        const errorBadge = td?.querySelector('[data-error]');

        // reset badges
        if (errorBadge) {
            errorBadge.classList.add('hidden');
            errorBadge.textContent = '';
        }

        try {
            const res = await fetch("{{ route('outdoor.monthly.upsert') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": getCsrf(),
                    "Accept": "application/json",
                },
                body: JSON.stringify(payload),
            });

            // Try to parse JSON either way, for better messages
            let data = null;
            try {
                data = await res.json();
            } catch (_) {}

            if (!res.ok || (data && data.ok === false)) {
                const msg = (data && (data.message || data.error)) || res.statusText || 'Save failed';
                throw new Error(msg);
            }

            // Success feedback
            savedBadge?.classList.remove('hidden');

            el.classList.remove('border-[#d33831]', 'bg-red-50');
            el.classList.add('border-green-400', 'bg-green-50');

            setTimeout(() => {
                savedBadge?.classList.add('hidden');
                el.classList.remove('border-green-400', 'bg-green-50');
                el.classList.add('border-neutral-200');
            }, 1500);

        } catch (e) {
            // Error feedback
            el.classList.remove('border-neutral-200');
            el.classList.add('border-[#d33831]', 'bg-red-50');
            if (errorBadge) {
                errorBadge.textContent = e?.message || 'Save failed';
                errorBadge.classList.remove('hidden');
            }
            setTimeout(() => el.classList.remove('bg-red-50'), 3000);
        }
    }

    // -------- Status dropdown color mapping (non-destructive to Tailwind classes) --------
    function setDropdownColor(selectEl) {
        const colorMap = {
            'Installation': {
                bg: '#22255b',
                color: '#fff',
                border: '#22255b'
            },
            'Dismantle': {
                bg: '#d33831',
                color: '#fff',
                border: '#d33831'
            },
            'Payment': {
                bg: '#d33831',
                color: '#fff',
                border: '#d33831'
            },
            'Renewal': {
                bg: '#d33831',
                color: '#fff',
                border: '#d33831'
            },
            'Completed': {
                bg: '#16a34a',
                color: '#fff',
                border: '#16a34a'
            },
            'Artwork': {
                bg: '#f97316',
                color: '#fff',
                border: '#f97316'
            },
            'Material': {
                bg: '#f97316',
                color: '#fff',
                border: '#f97316'
            },
            'Ongoing': {
                bg: '#4bbbed',
                color: '#1C1E26',
                border: '#4bbbed'
            },
        };

        // Preserve existing classes; only tweak inline styles
        const style = colorMap[selectEl.value];
        if (style) {
            selectEl.style.backgroundColor = style.bg;
            selectEl.style.color = style.color;
            selectEl.style.borderColor = style.border;
        } else {
            // Default visuals
            selectEl.style.backgroundColor = '#ffffff';
            selectEl.style.color = '#1C1E26';
            selectEl.style.borderColor = '#d4d4d8';
        }
    }

    // -------- Initialize on page load --------
    document.addEventListener('DOMContentLoaded', function() {
        // Style preselected dropdowns + watch changes
        document.querySelectorAll('.status-dropdown').forEach(selectEl => {
            setDropdownColor(selectEl);
            selectEl.addEventListener('change', function() {
                setDropdownColor(this);
                // Optional: auto-save when status changes
                // saveOutdoorCell(this);
            });
        });

        // Nice focus transitions
        document.querySelectorAll('.monthly-input').forEach(input => {
            input.addEventListener('focus', function() {
                this.style.transform = 'translateY(-1px)';
                this.style.boxShadow = '0 4px 6px -1px rgba(0, 0, 0, 0.1)';
            });
            input.addEventListener('blur', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = 'none';
            });
        });
    });

    // -------- Client-side pagination --------
    (function() {
        const PAGE_SIZE = 15;
        const tbody = document.getElementById('outdoorTbody');
        if (!tbody) return;

        const rows = Array.from(tbody.querySelectorAll('tr[data-idx]'));
        const prevBtn = document.getElementById('prevPage');
        const nextBtn = document.getElementById('nextPage');
        const pageInfo = document.getElementById('pageInfo');
        const rowsPerEl = document.getElementById('rowsPerPage');

        if (rowsPerEl) rowsPerEl.textContent = String(PAGE_SIZE);

        let page = 1;
        const totalRows = rows.length;
        const totalPages = Math.max(1, Math.ceil(totalRows / PAGE_SIZE));

        function render() {
            const start = (page - 1) * PAGE_SIZE;
            const end = start + PAGE_SIZE;

            rows.forEach((tr, i) => {
                tr.style.display = (i >= start && i < end) ? '' : 'none';
            });

            if (pageInfo) pageInfo.textContent = `${page} / ${totalPages}`;
            if (prevBtn) prevBtn.disabled = page <= 1;
            if (nextBtn) nextBtn.disabled = page >= totalPages;
        }

        if (prevBtn) prevBtn.addEventListener('click', () => {
            if (page > 1) {
                page--;
                render();
            }
        });
        if (nextBtn) nextBtn.addEventListener('click', () => {
            if (page < totalPages) {
                page++;
                render();
            }
        });

        // Initial paint
        render();
    })();
</script>
