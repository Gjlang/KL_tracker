<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Media Monthly Ongoing Job</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Fixed sticky column positioning with proper z-index layering */
        :root{
            --w-no: 60px;
            --w-company: 250px;
            --w-product: 150px;
            --w-start: 120px;
            --w-end: 120px;
        }

        .sticky-left-0 {
            position: sticky;
            left: 0px;
            z-index: 40;
            box-shadow: 2px 0 4px -2px rgba(0, 0, 0, 0.1);
        }
        .sticky-left-60 {
            position: sticky;
            left: 60px;
            z-index: 39;
            box-shadow: 2px 0 4px -2px rgba(0, 0, 0, 0.1);
        }
        .sticky-left-310 {
            position: sticky;
            left: 310px;
            z-index: 38;
            box-shadow: 2px 0 4px -2px rgba(0, 0, 0, 0.1);
        }
        .sticky-left-460 {
            position: sticky;
            left: 460px;
            z-index: 37;
            box-shadow: 2px 0 4px -2px rgba(0, 0, 0, 0.1);
        }
        .sticky-left-610 {
            position: sticky;
            left: 610px;
            z-index: 36;
            box-shadow: 2px 0 4px -2px rgba(0, 0, 0, 0.1);
        }
        .sticky-left-730 {
            position: sticky;
            left: 730px;
            z-index: 35;
            box-shadow: 2px 0 4px -2px rgba(0, 0, 0, 0.1);
        }
        .sticky-left-850 {
            position: sticky;
            left: 850px;
            z-index: 34;
            box-shadow: 2px 0 4px -2px rgba(0, 0, 0, 0.1);
        }

        /* Ensure sticky headers work properly */
        .sticky-header {
            position: sticky;
            top: 0;
            z-index: 50;
        }

        /* header cells need to be above body cells */
        .sticky-th {
            z-index: 40 !important;
        }

        /* Fix background inheritance for sticky cells */
        .sticky-cell-bg {
            background: inherit;
        }

        /* keep a solid background so overlapped content doesn't show through */
        thead .sticky-cell-bg {
            background: #f8fafc !important;
        }

        tbody .sticky-cell-bg {
            background: white !important;
        }

        tbody tr:nth-child(even) .sticky-cell-bg {
            background: #f8fafc !important;
        }

        tbody tr:hover .sticky-cell-bg {
            background: rgba(238, 242, 255, 0.6) !important;
        }

        /* Enhanced scrollbar styling */
        .overflow-x-auto::-webkit-scrollbar {
            height: 14px;
        }
        .overflow-x-auto::-webkit-scrollbar-track {
            background: linear-gradient(to right, #f1f5f9, #e2e8f0);
            border-radius: 7px;
            border: 1px solid #e2e8f0;
        }
        .overflow-x-auto::-webkit-scrollbar-thumb {
            background: linear-gradient(to right, #64748b, #475569);
            border-radius: 7px;
            border: 2px solid #f8fafc;
        }
        .overflow-x-auto::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(to right, #475569, #334155);
        }

        .month-cell {
            min-width: 200px;
            width: 200px;
        }

        /* Smooth transitions for all interactive elements */
        .transition-all {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Enhanced focus states */
        input:focus, select:focus {
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        /* Custom status badge animations */
        .status-badge {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        /* Row hover effect enhancement */
        .table-row:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 min-h-screen">
    <div class="flex min-h-screen">
        <main class="flex-1 overflow-y-auto">
            <div class="p-4 md:p-8 max-w-full">
                <!-- Enhanced Header Section -->
                <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl shadow-lg">
                            <span class="text-2xl">📺</span>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Media Monthly Ongoing Job</h1>
                            <p class="text-sm text-gray-600 mt-1">Manage and track media project statuses</p>
                        </div>
                    </div>

                    <!-- Back to Dashboard -->
                    <a href="{{ route('dashboard') }}"
       class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition-colors shadow-sm">
      <span class="ml-2">Dashboard</span>
    </a>

                    <a href="{{ route('coordinator.media.index') }}"
                       class="inline-flex items-center justify-center px-6 py-3 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 font-medium">
                        <span class="mr-2">🗂️</span>
                        Open Media Coordinator
                    </a>

                </div>

                <!-- Enhanced Filter Panel -->
                <div class="mb-8 p-6 bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-300">
                    <div class="flex items-center mb-6">
                        <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-indigo-100 to-blue-100 rounded-xl mr-4">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Filters & Search</h3>
                            <p class="text-sm text-gray-500">Filter data by month or other criteria</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Filter by Month</label>
                            <select class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 font-medium text-gray-700">
                                <option value="">All Months</option>
                                <option value="jan">January</option>
                                <option value="feb">February</option>
                                <option value="mar">March</option>
                                <option value="apr">April</option>
                                <option value="may">May</option>
                                <option value="jun">June</option>
                                <option value="jul">July</option>
                                <option value="aug">August</option>
                                <option value="sep">September</option>
                                <option value="oct">October</option>
                                <option value="nov">November</option>
                                <option value="dec">December</option>
                            </select>
                        </div>
                        <!-- Add more filter options as needed -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Search Companies</label>
                            <input type="text" placeholder="Search by company name..."
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200">
                        </div>
                    </div>
                </div>

                <!-- Enhanced Main Table -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-300 overflow-hidden">

                    <div class="relative overflow-x-auto">
                        <table class="min-w-[3250px] w-full text-sm table-fixed">
                            <thead class="sticky-header bg-gradient-to-r from-slate-50 to-gray-50 border-b-2 border-gray-200">
                                <tr>
                                    <th style="width: var(--w-no)" class="px-4 py-4 text-xs font-bold text-gray-700 uppercase tracking-wider sticky-left-0 sticky-cell-bg sticky-th border-r-2 border-gray-300">
                                        <div class="flex items-center justify-center">
                                            <span class="text-lg">📅</span>
                                            <span class="ml-1">No</span>
                                        </div>
                                    </th>
                                    <th style="width: var(--w-company)" class="px-4 py-4 text-xs font-bold text-black-700 uppercase tracking-wider sticky-left-60 sticky-cell-bg sticky-th border-r-2 border-gray-300">
                                        <div class="flex items-center">
                                            <span class="ml-2">Month</span>
                                        </div>
                                    </th>
                                    <th style="width: var(--w-company)" class="px-4 py-4 text-xs font-bold text-gray-700 uppercase tracking-wider sticky-left-60 sticky-cell-bg sticky-th border-r-2 border-gray-300">
                                        <div class="flex items-center">
                                            <span class="text-lg">🏢</span>
                                            <span class="ml-2">Company</span>
                                        </div>
                                    </th>
                                    <th style="width: var(--w-product)" class="px-4 py-4 text-xs font-bold text-gray-700 uppercase tracking-wider sticky-left-310 sticky-cell-bg sticky-th border-r-2 border-gray-300">
                                        <div class="flex items-center">
                                            <span class="text-lg">📦</span>
                                            <span class="ml-2">Product</span>
                                        </div>
                                    </th>
                                    <th style="width: var(--w-start)" class="px-4 py-4 text-xs font-bold text-gray-700 uppercase tracking-wider sticky-left-460 sticky-cell-bg sticky-th border-r-2 border-gray-300">
                                        <div class="flex items-center">
                                            <span class="text-lg">▶️</span>
                                            <span class="ml-2">Start</span>
                                        </div>
                                    </th>
                                    <th style="width: var(--w-end)" class="px-4 py-4 text-xs font-bold text-gray-700 uppercase tracking-wider sticky-left-580 sticky-cell-bg sticky-th border-r-2 border-gray-300">
                                        <div class="flex items-center">
                                            <span class="text-lg">⏹️</span>
                                            <span class="ml-2">End</span>
                                        </div>
                                    </th>

                                    <!-- Enhanced Month Headers -->
                                    @php
                                        $months = [
                                            1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',
                                            7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December'
                                        ];
                                    @endphp

                                    @foreach($months as $mNum => $mName)
                                        <th class="px-3 py-4 text-center bg-gradient-to-br from-indigo-50 via-blue-50 to-cyan-50 border-r border-gray-200 month-cell">
                                            <div class="font-bold text-indigo-700 text-base">{{ $mName }}</div>
                                            <div class="text-xs text-indigo-500 mt-1">{{ date('Y') }}</div>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @php
                                  /** @var \Illuminate\Support\Collection $rows */
                                  $rows       = collect($rows ?? $monthlyByCategory['Media'] ?? []);
                                  $activeYear = isset($year) ? (int)$year : (int)now()->year;
                                  $detailsMap = $detailsMap ?? [];

                                  // Helper: fetch saved cell once
                                  function md_cell($map, $mid, $yr, $mon) {
                                      return $map[$mid][$yr][$mon] ?? ['value_text'=>null,'value_date'=>null];
                                  }
                                @endphp
                                @forelse($rows as $i => $row)
                                    <tr class="table-row {{ $i % 2 === 0 ? 'bg-white' : 'bg-slate-50/50' }} hover:bg-indigo-50/60 transition-all duration-300 group">
                                        <!-- Enhanced Sticky Columns -->
                                        <td style="width: var(--w-no)" class="px-4 py-4 text-center sticky-left-0 sticky-cell-bg border-r-2 border-gray-300 group-hover:bg-indigo-50/60">
                                            <div class="flex items-center justify-center w-8 h-8 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full font-bold text-gray-700">
                                                {{ $i+1 }}
                                            </div>
                                        </td>
                                        <td style="width: var(--w-month)" class="px-4 py-4 sticky-left-310 sticky-cell-bg border-r-2 border-gray-300 group-hover:bg-indigo-50/60">
                                            <div class="max-w-[130px] truncate font-medium text-gray-700" title="{{ $row->month }}">{{ $row->month }}</div>
                                        </td>
                                        <td style="width: var(--w-company)" class="px-4 py-4 sticky-left-60 sticky-cell-bg border-r-2 border-gray-300 group-hover:bg-indigo-50/60">
                                            <div class="flex items-center gap-3">
                                                <div class="w-2 h-2 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-full"></div>
                                                <div class="max-w-[220px] truncate font-bold text-gray-900" title="{{ $row->company }}">
                                                    {{ $row->company }}
                                                </div>
                                            </div>
                                        </td>
                                        <td style="width: var(--w-product)" class="px-4 py-4 sticky-left-310 sticky-cell-bg border-r-2 border-gray-300 group-hover:bg-indigo-50/60">
                                            <div class="max-w-[130px] truncate font-medium text-gray-700" title="{{ $row->product }}">{{ $row->product }}</div>
                                        </td>
                                        <td style="width: var(--w-start)" class="px-4 py-4 sticky-left-460 sticky-cell-bg border-r-2 border-gray-300 group-hover:bg-indigo-50/60">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700 ring-1 ring-green-200">
                                                {{ $row->date }}
                                            </span>
                                        </td>
                                       <td style="width: var(--w-end)"
                                            class="px-4 py-4 sticky-cell-bg border-r-2 border-gray-300 group-hover:bg-indigo-50/60">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 ring-1 ring-red-200">
                                                {{ $row->date_finish }}
                                            </span>
                                        </td>

                                        <!-- Enhanced Month Columns -->
                                        @php
                                            $yearView = $activeYear;
                                        @endphp

                                        @foreach($months as $mNum => $mName)
                                            @php
                                                $d = md_cell($detailsMap, $row->id, $yearView, $mNum);
                                                $savedStatus = $d['value_text'] ?? '';
                                                $savedDate   = $d['value_date'] ?? '';
                                            @endphp
                                            <td class="px-3 py-3 bg-gradient-to-br from-indigo-50/20 via-blue-50/30 to-cyan-50/20 group-hover:from-indigo-100/50 group-hover:to-blue-100/50 border-r border-gray-200 month-cell transition-all duration-300">
                                                <div class="flex flex-col gap-2">
                                                    <!-- Enhanced Status Dropdown -->
                                                    <select
                                                        class="status-dropdown w-full text-xs font-semibold rounded-lg border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:outline-none bg-white hover:bg-gray-50 shadow-sm transition-all duration-200"
                                                        data-master="{{ $row->id }}"
                                                        data-year="{{ $yearView }}"
                                                        data-month="{{ $mNum }}"
                                                        data-kind="text"
                                                        name="status_{{ $row->id }}_{{ $yearView }}_{{ $mNum }}"
                                                        onchange="saveMediaCell(this); setDropdownColor(this);"
                                                        >
                                                            <option value=""></option>
                                                            <option value="Installation" {{ $savedStatus==='Installation' ? 'selected' : '' }}>🔧 Installation</option>
                                                            <option value="Dismentel"   {{ $savedStatus==='Dismentel'   ? 'selected' : '' }}>🔨 Dismentel</option>
                                                            <option value="Artwork"     {{ $savedStatus==='Artwork'     ? 'selected' : '' }}>🎨 Artwork</option>
                                                            <option value="Payment"     {{ $savedStatus==='Payment'     ? 'selected' : '' }}>💳 Payment</option>
                                                            <option value="Ongoing"     {{ $savedStatus==='Ongoing'     ? 'selected' : '' }}>⚡ Ongoing</option>
                                                            <option value="Renewal"     {{ $savedStatus==='Renewal'     ? 'selected' : '' }}>🔄 Renewal</option>
                                                            <option value="Completed"   {{ $savedStatus==='Completed'   ? 'selected' : '' }}>✅ Completed</option>
                                                            <option value="Material"    {{ $savedStatus==='Material'    ? 'selected' : '' }}>📦 Material</option>
                                                        </select>

                                                    <!-- Enhanced Date Input -->
                                                    <input
                                                        type="date"
                                                        value="{{ $savedDate }}"
                                                        class="w-full text-xs rounded-lg border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:outline-none bg-white hover:bg-gray-50 shadow-sm transition-all duration-200"
                                                        data-master="{{ $row->id }}"
                                                        data-year="{{ $yearView }}"
                                                        data-month="{{ $mNum }}"
                                                        data-kind="date"
                                                        name="date_{{ $row->id }}_{{ $yearView }}_{{ $mNum }}"
                                                        onblur="saveMediaCell(this)"
                                                    />
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="px-4 py-8 text-center text-gray-500" colspan="18">
                                            <div class="flex flex-col items-center gap-2">
                                                <span class="text-4xl">📂</span>
                                                <span class="font-medium">No Media projects found</span>
                                                <span class="text-sm">Add some projects to get started</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Enhanced dropdown color mapping with Tailwind classes
        const STATUS_SOLID = {
            'Installation': 'bg-red-600 text-white',
            'Dismentel':    'bg-red-600 text-white',
            'Payment':      'bg-red-600 text-white',   // 🔴 changed from yellow to red
            'Artwork':      'bg-orange-500 text-white',// 🟠 changed from blue to orange
            'Material':     'bg-yellow-400 text-black',
            'Ongoing':      'bg-sky-500 text-white',
            'Renewal':      'bg-red-600 text-white',   // 🔴 changed from purple to red
            'Completed':    'bg-green-600 text-white',
            '':             'bg-white text-gray-900'
        };

        const BASE_SELECT =
            'status-dropdown w-full text-xs font-semibold rounded-lg border px-3 py-2 ' +
            'focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:outline-none ' +
            'shadow-sm transition-all duration-200';

        function setDropdownColor(selectEl) {
            const colorClass = STATUS_SOLID[selectEl.value] || 'bg-gray-600 text-white';
            selectEl.className = `${BASE_SELECT} ${colorClass}`;
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('select.status-dropdown').forEach(setDropdownColor);
        });


        // Initialize all status dropdowns on page load
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('select[data-kind="text"]').forEach(selectEl => {
                setDropdownColor(selectEl);
                selectEl.addEventListener('change', () => setDropdownColor(selectEl));
            });
        });

        // Enhanced save function with better UX feedback
        const SAVE_URL = "{{ route('media.monthly.upsert') }}";

         function debounce(fn, ms=350){
           let t; return (...args)=>{ clearTimeout(t); t=setTimeout(()=>fn(...args), ms); };
         }

        async function saveMediaCell(el) {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const payload = {
                master_file_id: el.dataset.master,
                year: parseInt(el.dataset.year, 10),
                month: parseInt(el.dataset.month, 10),
                kind: el.dataset.kind,
                value: el.value || null
            };

            try {
                // Enhanced loading state
                el.disabled = true;
                el.classList.add('opacity-60');

                // Add loading indicator
                const originalBg = el.style.backgroundColor;
                el.style.background = 'linear-gradient(45deg, transparent 40%, rgba(59, 130, 246, 0.1) 50%, transparent 60%)';
                el.style.backgroundSize = '200% 100%';
                el.style.animation = 'shimmer 1s infinite';

                const res = await fetch(SAVE_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify(payload)
                });

                if (!res.ok) {
                    const errorText = await res.text();
                    throw new Error(`Save failed: ${res.status} ${errorText.slice(0, 160)}`);
                }

                // Success feedback
                el.style.background = 'linear-gradient(45deg, rgba(34, 197, 94, 0.1), rgba(34, 197, 94, 0.05))';
                setTimeout(() => {
                    el.style.background = originalBg;
                    el.style.animation = '';
                }, 1000);

            } catch (err) {
                console.error('Save error:', err);

                // Error feedback
                el.style.background = 'linear-gradient(45deg, rgba(239, 68, 68, 0.1), rgba(239, 68, 68, 0.05))';

                // User-friendly error notification
                const errorNotification = document.createElement('div');
                errorNotification.className = 'fixed top-4 right-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg shadow-lg z-50 transition-all duration-300';
                errorNotification.innerHTML = `
                    <div class="flex items-center gap-2">
                        <span>⚠️</span>
                        <span class="font-medium">Save failed. Please try again.</span>
                    </div>
                `;
                document.body.appendChild(errorNotification);

                setTimeout(() => {
                    errorNotification.remove();
                    el.style.background = originalBg;
                    el.style.animation = '';
                }, 3000);

            } finally {
                el.disabled = false;
                el.classList.remove('opacity-60');
            }
        }

        // Add shimmer animation for loading states
        const shimmerStyle = document.createElement('style');
        shimmerStyle.textContent = `
            @keyframes shimmer {
                0% { background-position: 200% 0; }
                100% { background-position: -200% 0; }
            }
        `;
        document.head.appendChild(shimmerStyle);
    </script>
</body>
</html>
