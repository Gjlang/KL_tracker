<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Media Monthly Ongoing Job</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;500;600&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        /* Custom CSS Tokens */
        .ink { color: #1C1E26; }
        .card { @apply bg-white rounded-2xl border border-neutral-200/70 shadow-sm; }
        .hairline { @apply border border-neutral-200; }

        .btn-primary {
            @apply bg-[#22255b] text-white hover:opacity-90 focus:ring-2 focus:ring-[#4bbbed] rounded-full px-5 py-2.5 h-11 inline-flex items-center justify-center font-medium transition-all duration-200;
        }

        .btn-secondary {
            @apply bg-[#4bbbed]/10 text-[#22255b] hover:bg-[#4bbbed]/20 focus:ring-2 focus:ring-[#4bbbed] rounded-full px-5 py-2.5 h-11 inline-flex items-center justify-center font-medium transition-all duration-200;
        }

        .btn-ghost {
            @apply border border-neutral-300 text-neutral-700 hover:bg-neutral-50 focus:ring-2 focus:ring-neutral-300 rounded-full px-5 py-2.5 h-11 inline-flex items-center justify-center font-medium transition-all duration-200;
        }

        .tabular-nums {
            font-variant-numeric: tabular-nums;
        }

        .chip {
            @apply inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium;
        }

        /* Typography */
        .serif { font-family: 'EB Garamond', serif; }
        .sans { font-family: 'Inter', sans-serif; }

        /* Small caps for table headers */
        .small-caps {
            font-variant: small-caps;
            letter-spacing: 0.05em;
        }

        /* Sticky column positioning - preserving original logic */
        :root{
            --w-no: 60px;
            --w-month: 120px;
            --w-company: 250px;
            --w-product: 150px;
            --w-start: 120px;
            --w-end: 120px;
        }

        .sticky-left-0 {
            position: sticky;
            left: 0px;
            z-index: 40;
        }
        .sticky-left-60 {
            position: sticky;
            left: 60px;
            z-index: 39;
        }
        .sticky-left-180 {
            position: sticky;
            left: 180px;
            z-index: 38;
        }
        .sticky-left-430 {
            position: sticky;
            left: 430px;
            z-index: 37;
        }
        .sticky-left-580 {
            position: sticky;
            left: 580px;
            z-index: 36;
        }
        .sticky-left-700 {
            position: sticky;
            left: 700px;
            z-index: 35;
        }

        .sticky-header {
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .sticky-cell-bg {
            background: inherit;
        }

        thead .sticky-cell-bg {
            background: #fafafa !important;
        }

        tbody .sticky-cell-bg {
            background: white !important;
        }

        tbody tr:hover .sticky-cell-bg {
            background: #fafafa !important;
        }

        .month-cell {
            min-width: 200px;
            width: 200px;
        }

        /* Status dropdown colors - preserving original logic */
        .status-Installation { @apply bg-[#d33831] text-white; }
        .status-Dismantle { @apply bg-[#d33831] text-white; }
        .status-Payment { @apply bg-[#d33831] text-white; }
        .status-Artwork { @apply bg-orange-500 text-white; }
        .status-Material { @apply bg-yellow-400 text-black; }
        .status-Ongoing { @apply bg-[#4bbbed] text-white; }
        .status-Renewal { @apply bg-[#d33831] text-white; }
        .status-Completed { @apply bg-green-600 text-white; }

        /* Enhanced scrollbar */
        .overflow-x-auto::-webkit-scrollbar {
            height: 8px;
        }
        .overflow-x-auto::-webkit-scrollbar-track {
            background: #f5f5f5;
            border-radius: 4px;
        }
        .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 4px;
        }
        .overflow-x-auto::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }
    </style>
</head>

<body class="bg-[#F7F7F9] min-h-screen">
    <div class="flex min-h-screen">
        <main class="flex-1">
            <div class="max-w-full px-6 py-8 lg:px-12">

                <!-- Header Section -->
                <header class="mb-8">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <div>
                            <h1 class="serif text-4xl lg:text-5xl font-semibold ink mb-2">Media Monthly Ongoing Job</h1>
                            <p class="sans text-neutral-600 text-lg">Manage and track media project statuses</p>
                        </div>

                        <div class="flex items-center gap-3">
                            <a href="<?php echo e(route('dashboard')); ?>" class="btn-ghost">
                                Dashboard
                            </a>
                            <a href="<?php echo e(route('coordinator.media.index')); ?>" class="btn-secondary">
                                Open Media Coordinator
                            </a>
                        </div>
                    </div>
                </header>

                <!-- Filter Panel -->
                <div class="card mb-8">
                    <div class="p-6">
                        <div class="mb-6">
                            <h2 class="serif text-xl font-medium ink mb-1">Filters & Search</h2>
                            <p class="sans text-neutral-500 text-sm">Filter projects by month or search by company name</p>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
                            <div class="space-y-2">
                                <label class="block sans text-sm font-medium text-neutral-700">Filter by Month</label>
                                <select class="w-full h-11 px-4 rounded-lg hairline bg-white focus:outline-none focus:ring-2 focus:ring-[#4bbbed] focus:border-transparent sans text-sm">
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

                            <div class="space-y-2">
                                <label class="block sans text-sm font-medium text-neutral-700">Search Companies</label>
                                <input
                                    type="text"
                                    placeholder="Search by company name..."
                                    class="w-full h-11 px-4 rounded-lg hairline bg-white focus:outline-none focus:ring-2 focus:ring-[#4bbbed] focus:border-transparent sans text-sm"
                                />
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <button class="btn-primary">Apply Filters</button>
                            <button class="btn-ghost">Reset</button>
                        </div>
                    </div>
                </div>

                <!-- Table Section -->
                <div class="card">
                    <div class="p-6 border-b border-neutral-200">
                        <h3 class="sans text-sm font-medium text-neutral-500 small-caps">Media Projects</h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-[3250px] w-full text-sm table-fixed">
                            <thead class="sticky-header bg-neutral-50 border-b border-neutral-200">
                                <tr>
                                    <th style="width: var(--w-no)" class="px-4 py-4 text-left sans text-xs font-medium text-neutral-500 small-caps sticky-left-0 sticky-cell-bg border-r border-neutral-200">
                                        No.
                                    </th>
                                    <th style="width: var(--w-month)" class="px-4 py-4 text-left sans text-xs font-medium text-neutral-500 small-caps sticky-left-60 sticky-cell-bg border-r border-neutral-200">
                                        Month
                                    </th>
                                    <th style="width: var(--w-company)" class="px-4 py-4 text-left sans text-xs font-medium text-neutral-500 small-caps sticky-left-180 sticky-cell-bg border-r border-neutral-200">
                                        Company
                                    </th>
                                    <th style="width: var(--w-product)" class="px-4 py-4 text-left sans text-xs font-medium text-neutral-500 small-caps sticky-left-430 sticky-cell-bg border-r border-neutral-200">
                                        Product
                                    </th>
                                    <th style="width: var(--w-start)" class="px-4 py-4 text-left sans text-xs font-medium text-neutral-500 small-caps sticky-left-580 sticky-cell-bg border-r border-neutral-200">
                                        Start Date
                                    </th>
                                    <th style="width: var(--w-end)" class="px-4 py-4 text-left sans text-xs font-medium text-neutral-500 small-caps sticky-left-700 sticky-cell-bg border-r border-neutral-200">
                                        End Date
                                    </th>

                                    <?php
                                        $months = [
                                            1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',
                                            7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December'
                                        ];
                                    ?>

                                    <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mNum => $mName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <th class="px-3 py-4 text-center bg-neutral-50 border-r border-neutral-200 month-cell">
                                            <div class="serif font-medium text-neutral-700"><?php echo e($mName); ?></div>
                                            <div class="sans text-xs text-neutral-500 mt-1"><?php echo e(date('Y')); ?></div>
                                        </th>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-neutral-200">
                                <?php
                                  $rows = collect($rows ?? $monthlyByCategory['Media'] ?? []);
                                  $activeYear = isset($year) ? (int)$year : (int)now()->year;
                                  $detailsMap = $detailsMap ?? [];

                                  function md_cell($map, $mid, $yr, $mon) {
                                      return $map[$mid][$yr][$mon] ?? ['value_text'=>null,'value_date'=>null];
                                  }
                                ?>

                                <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr class="hover:bg-neutral-50 hover:shadow-sm transition-all duration-150 group">
                                        <td style="width: var(--w-no)" class="px-4 py-4 sticky-left-0 sticky-cell-bg border-r border-neutral-200">
                                            <span class="tabular-nums sans text-sm font-medium text-neutral-500"><?php echo e($i+1); ?></span>
                                        </td>
                                        <td style="width: var(--w-month)" class="px-4 py-4 sticky-left-60 sticky-cell-bg border-r border-neutral-200">
                                            <span class="sans text-sm font-medium ink" title="<?php echo e($row->month); ?>">
                                                <?php echo e(strlen($row->month) > 15 ? substr($row->month, 0, 15) . '...' : $row->month); ?>

                                            </span>
                                        </td>
                                        <td style="width: var(--w-company)" class="px-4 py-4 sticky-left-180 sticky-cell-bg border-r border-neutral-200">
                                            <span class="sans text-sm font-semibold ink" title="<?php echo e($row->company); ?>">
                                                <?php echo e(strlen($row->company) > 30 ? substr($row->company, 0, 30) . '...' : $row->company); ?>

                                            </span>
                                        </td>
                                        <td style="width: var(--w-product)" class="px-4 py-4 sticky-left-430 sticky-cell-bg border-r border-neutral-200">
                                            <span class="sans text-sm text-neutral-700" title="<?php echo e($row->product); ?>">
                                                <?php echo e(strlen($row->product) > 20 ? substr($row->product, 0, 20) . '...' : $row->product); ?>

                                            </span>
                                        </td>
                                        <td style="width: var(--w-start)" class="px-4 py-4 sticky-left-580 sticky-cell-bg border-r border-neutral-200">
                                            <span class="chip bg-green-50 text-green-700 border border-green-200 tabular-nums">
                                                <?php echo e($row->date); ?>

                                            </span>
                                        </td>
                                        <td style="width: var(--w-end)" class="px-4 py-4 sticky-left-700 sticky-cell-bg border-r border-neutral-200">
                                            <span class="chip bg-red-50 text-red-700 border border-red-200 tabular-nums">
                                                <?php echo e($row->date_finish); ?>

                                            </span>
                                        </td>

                                        <?php $yearView = $activeYear; ?>

                                        <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mNum => $mName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $d = md_cell($detailsMap, $row->id, $yearView, $mNum);
                                                $savedStatus = $d['value_text'] ?? '';
                                                $savedDate = $d['value_date'] ?? '';
                                            ?>
                                            <td class="px-3 py-3 bg-white group-hover:bg-neutral-50 border-r border-neutral-200 month-cell transition-colors duration-150">
                                                <div class="flex flex-col gap-2">
                                                    <select
                                                        class="status-dropdown w-full text-xs font-medium rounded-lg hairline px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-[#4bbbed] focus:border-transparent bg-white transition-all duration-150"
                                                        data-master="<?php echo e($row->id); ?>"
                                                        data-year="<?php echo e($yearView); ?>"
                                                        data-month="<?php echo e($mNum); ?>"
                                                        data-kind="text"
                                                        name="status_<?php echo e($row->id); ?>_<?php echo e($yearView); ?>_<?php echo e($mNum); ?>"
                                                        onchange="saveMediaCell(this); setDropdownColor(this);"
                                                    >
                                                        <option value=""></option>
                                                        <option value="Installation" <?php echo e($savedStatus==='Installation' ? 'selected' : ''); ?>>Installation</option>
                                                        <option value="Dismantle" <?php echo e($savedStatus==='Dismantle' ? 'selected' : ''); ?>>Dismantle</option>
                                                        <option value="Artwork" <?php echo e($savedStatus==='Artwork' ? 'selected' : ''); ?>>Artwork</option>
                                                        <option value="Payment" <?php echo e($savedStatus==='Payment' ? 'selected' : ''); ?>>Payment</option>
                                                        <option value="Ongoing" <?php echo e($savedStatus==='Ongoing' ? 'selected' : ''); ?>>Ongoing</option>
                                                        <option value="Renewal" <?php echo e($savedStatus==='Renewal' ? 'selected' : ''); ?>>Renewal</option>
                                                        <option value="Completed" <?php echo e($savedStatus==='Completed' ? 'selected' : ''); ?>>Completed</option>
                                                        <option value="Material" <?php echo e($savedStatus==='Material' ? 'selected' : ''); ?>>Material</option>
                                                    </select>

                                                    <input
                                                        type="date"
                                                        value="<?php echo e($savedDate); ?>"
                                                        class="w-full text-xs rounded-lg hairline px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-[#4bbbed] focus:border-transparent bg-white transition-all duration-150 tabular-nums"
                                                        data-master="<?php echo e($row->id); ?>"
                                                        data-year="<?php echo e($yearView); ?>"
                                                        data-month="<?php echo e($mNum); ?>"
                                                        data-kind="date"
                                                        name="date_<?php echo e($row->id); ?>_<?php echo e($yearView); ?>_<?php echo e($mNum); ?>"
                                                        onblur="saveMediaCell(this)"
                                                    />
                                                </div>
                                            </td>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td class="px-4 py-16 text-center" colspan="18">
                                            <div class="flex flex-col items-center gap-4 max-w-sm mx-auto">
                                                <div class="w-16 h-16 bg-neutral-100 rounded-full flex items-center justify-center">
                                                    <svg class="w-8 h-8 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    </svg>
                                                </div>
                                                <div class="text-center">
                                                    <h3 class="sans text-base font-medium ink">No Media projects found</h3>
                                                    <p class="sans text-sm text-neutral-500 mt-1">Add some projects to get started</p>
                                                </div>
                                                <button class="btn-ghost">Add Project</button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <script>
        // Status color mapping - preserving original logic
        const STATUS_COLORS = {
            'Installation': 'status-Installation',
            'Dismantle': 'status-Dismantle',
            'Payment': 'status-Payment',
            'Artwork': 'status-Artwork',
            'Material': 'status-Material',
            'Ongoing': 'status-Ongoing',
            'Renewal': 'status-Renewal',
            'Completed': 'status-Completed',
            '': ''
        };

        const BASE_SELECT_CLASSES = 'status-dropdown w-full text-xs font-medium rounded-lg hairline px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-[#4bbbed] focus:border-transparent transition-all duration-150';

        function setDropdownColor(selectEl) {
            const colorClass = STATUS_COLORS[selectEl.value] || '';
            selectEl.className = `${BASE_SELECT_CLASSES} ${colorClass}`;
        }

        // Initialize all status dropdowns on page load
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('select[data-kind="text"]').forEach(selectEl => {
                setDropdownColor(selectEl);
            });
        });

        // Enhanced save function - preserving original logic
        const SAVE_URL = "<?php echo e(route('media.monthly.upsert')); ?>";

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
                el.disabled = true;
                el.classList.add('opacity-60');

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
                el.style.boxShadow = '0 0 0 2px rgba(34, 197, 94, 0.2)';
                setTimeout(() => {
                    el.style.boxShadow = '';
                }, 1000);

            } catch (err) {
                console.error('Save error:', err);

                // Error feedback
                el.style.boxShadow = '0 0 0 2px rgba(211, 56, 49, 0.2)';

                setTimeout(() => {
                    el.style.boxShadow = '';
                }, 3000);

            } finally {
                el.disabled = false;
                el.classList.remove('opacity-60');
            }
        }
    </script>
</body>
</html>
<?php /**PATH C:\Users\Gjlang\kl_guide_tracker\resources\views/dashboard/media.blade.php ENDPATH**/ ?>