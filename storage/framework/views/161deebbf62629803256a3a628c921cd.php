<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Proposal Confirmation</title>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --paper-bg: #F7F7F9;
            --surface: #FFFFFF;
            --ink: #1C1E26;
            --hairline: #EAEAEA;
            --brand-dark: #22255b;
            --brand-light: #4bbbed;
            --destructive: #d33831;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--paper-bg);
            color: var(--ink);
        }

        .font-serif {
            font-family: 'Playfair Display', serif;
        }

        .ink {
            color: var(--ink);
        }

        .hairline {
            border-color: var(--hairline);
        }

        .hairline-bottom {
            border-bottom: 1px solid var(--hairline);
        }

        .small-caps {
            font-variant: small-caps;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            font-size: 11px;
            font-weight: 500;
        }

        .tabular-nums {
            font-variant-numeric: tabular-nums;
        }

        .btn-primary {
            background-color: var(--brand-dark);
            color: white;
            transition: all 150ms ease;
        }

        .btn-primary:hover {
            opacity: 0.9;
        }

        .btn-primary:focus {
            ring: 2px;
            ring-color: var(--brand-light);
            ring-opacity: 0.5;
        }

        .btn-secondary {
            border: 1px solid var(--hairline);
            background-color: transparent;
            color: var(--ink);
            transition: all 150ms ease;
        }

        .btn-secondary:hover {
            background-color: rgba(75, 187, 237, 0.05);
            border-color: var(--brand-light);
        }

        .btn-ghost {
            border: 1px solid #d1d5db;
            background-color: transparent;
            color: #6b7280;
            transition: all 150ms ease;
        }

        .btn-ghost:hover {
            background-color: #f9fafb;
        }

        .btn-destructive {
            background-color: var(--destructive);
            color: white;
            transition: all 150ms ease;
        }

        .btn-destructive:hover {
            opacity: 0.9;
        }

        .card {
            background-color: var(--surface);
            border-radius: 1rem;
            border: 1px solid rgba(229, 231, 235, 0.7);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }

        .filter-chip {
            background-color: #f3f4f6;
            color: #374151;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .table-row:hover {
            background-color: #fafafa;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            transform: translateY(-1px);
            transition: all 150ms ease;
        }

        .nav-card {
            transition: all 200ms ease;
            position: relative;
        }

        .nav-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px 0 rgba(0, 0, 0, 0.1);
        }

        .nav-card:hover::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background-color: var(--brand-dark);
            border-radius: 0 1rem 1rem 0;
        }

        .tab-button {
            position: relative;
            padding: 0.75rem 0;
            color: #6b7280;
            border-bottom: 2px solid transparent;
            transition: all 150ms ease;
        }

        .tab-button.active {
            color: var(--ink);
            border-bottom-color: var(--brand-dark);
        }

        @media (max-width: 768px) {
            .table-mobile-card {
                background: white;
                border-radius: 0.75rem;
                border: 1px solid var(--hairline);
                padding: 1rem;
                margin-bottom: 0.75rem;
            }
        }

        /* Full-width layout */
        .main-container {
            min-height: 100vh;
            max-width: none;
            padding: 0;
        }

        .content-wrapper {
            padding: 2rem 3rem;
        }

        @media (max-width: 1024px) {
            .content-wrapper {
                padding: 1.5rem 2rem;
            }
        }

        @media (max-width: 768px) {
            .content-wrapper {
                padding: 1rem;
            }
        }
    </style>
</head>
<body class="min-h-screen" style="background-color: var(--paper-bg);">

    <!-- Main Container - Full Width -->
    <div class="main-container">
        <div class="content-wrapper">

            <!-- Page Header -->
            <header class="mb-8">
                <div class="flex flex-col lg:flex-row lg:justify-between lg:items-start gap-6">
                    <!-- Title Section -->
                    <div class="flex-1">
                        <h1 class="font-serif text-3xl lg:text-4xl font-medium ink mb-2">Master Proposal Confirmation</h1>
                        <p class="text-sm text-gray-600">
                            <?php if(isset($masterFiles) && $masterFiles->count() > 0): ?>
                                Showing <?php echo e($masterFiles->count()); ?> records
                            <?php else: ?>
                                No records found
                            <?php endif; ?>
                        </p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap gap-3">
                        <!-- Add New - Primary -->
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('masterfile.create')): ?>
                        <a href="<?php echo e(route('masterfile.create')); ?>" class="btn-primary inline-flex items-center px-5 py-2.5 rounded-full text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add New
                        </a>
                        <?php endif; ?>

                        <!-- Calendar View - Secondary -->
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('dashboard.view')): ?>
                        <a href="<?php echo e(route('calendar.index')); ?>" class="btn-secondary inline-flex items-center px-4 py-2.5 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Calendar View
                        </a>
                        <?php endif; ?>

                        <!-- Import Data - Ghost -->
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('masterfile.import')): ?>
                        <button type="button" onclick="testImportModal()" class="btn-ghost inline-flex items-center px-4 py-2.5 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3"></path>
                            </svg>
                            Import Data
                        </button>
                        <?php endif; ?>

                        <!-- Export All Data - Ghost -->
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('export.run')): ?>
                        <a href="<?php echo e(route('masterfile.exportXlsx', request()->query())); ?>" class="btn-ghost inline-flex items-center px-4 py-2.5 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l2-2m0 0l2-2m-2 2H6"></path>
                            </svg>
                            Export All Data
                        </a>
                        <?php endif; ?>

                        <!-- Information Booth -->
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('information.booth.view')): ?>
                        <a href="<?php echo e(route('information.booth.index')); ?>" class="btn-secondary inline-flex items-center px-4 py-2.5 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Information Hub
                        </a>
                        <?php endif; ?>

                        <a href="<?php echo e(route('report.summary', ['year'=>request('year', now()->year), 'month'=>request('month'), 'status'=>request('status')])); ?>"
   class="inline-flex items-center px-3 py-2 rounded-lg text-white"
   style="background:#22255b">
   Print All (Summary)
</a>


                        <!-- Logout - Destructive (Always visible) -->
                        <form method="POST" action="<?php echo e(route('logout')); ?>" class="inline">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn-destructive inline-flex items-center px-4 py-2.5 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7"></path>
                                </svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Filters Card -->
            <div class="card p-6 mb-8">
                <form method="GET" action="<?php echo e(route('dashboard')); ?>" class="space-y-6">
                    <!-- Filter Fields Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Search -->
                        <div class="lg:col-span-2">
                            <label class="small-caps text-gray-600 block mb-2">Search</label>
                            <input type="text"
                                   name="search"
                                   id="search"
                                   value="<?php echo e(request('search')); ?>"
                                   placeholder="Company, product, status, client, month…"
                                   class="w-full h-11 rounded-xl border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent text-sm">
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="small-caps text-gray-600 block mb-2">Status</label>
                            <select name="status" id="status" class="w-full h-11 rounded-xl border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent text-sm">
                                <option value="">All Status</option>
                                <option value="completed" <?php echo e(request('status') == 'completed' ? 'selected' : ''); ?>>Completed</option>
                                <option value="ongoing" <?php echo e(request('status') == 'ongoing' ? 'selected' : ''); ?>>Ongoing</option>
                                <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>Pending</option>
                            </select>
                        </div>

                        <!-- Month -->
                        <div>
                            <label class="small-caps text-gray-600 block mb-2">Month</label>
                            <select name="month" id="month" class="w-full h-11 rounded-xl border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent text-sm">
                                <option value="">All Months</option>
                                <?php $__currentLoopData = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($m); ?>" <?php echo e(request('month') == $m ? 'selected' : ''); ?>><?php echo e($m); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <!-- Category -->
                        <div>
                            <label class="small-caps text-gray-600 block mb-2">Category</label>
                            <select name="product_category" id="product_category" class="w-full h-11 rounded-xl border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent text-sm">
                                <option value="">All Categories</option>
                                <option value="Outdoor" <?php echo e(request('product_category') == 'Outdoor' ? 'selected' : ''); ?>>Outdoor</option>
                                <option value="Media" <?php echo e(request('product_category') == 'Media' ? 'selected' : ''); ?>>Media</option>
                                <option value="KLTG" <?php echo e(request('product_category') == 'KLTG' ? 'selected' : ''); ?>>KLTG</option>
                            </select>
                        </div>
                    </div>

                    <!-- Filter Actions -->
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button type="submit" class="btn-primary flex-1 sm:flex-none px-8 py-2.5 rounded-xl text-sm font-medium">
                            Apply Filters
                        </button>
                        <?php if(request('search') || request('status') || request('month') || request('product_category')): ?>
                            <a href="<?php echo e(route('dashboard')); ?>" class="btn-ghost px-6 py-2.5 rounded-xl text-sm font-medium">
                                Clear Filters
                            </a>
                        <?php endif; ?>
                    </div>

                    <!-- Active Filter Chips -->
                    <?php if(request('search') || request('status') || request('month') || request('product_category')): ?>
                        <div class="flex flex-wrap gap-2">
                            <?php if(request('search')): ?><span class="filter-chip">SEARCH: "<?php echo e(request('search')); ?>"</span><?php endif; ?>
                            <?php if(request('status')): ?><span class="filter-chip">STATUS: <?php echo e(strtoupper(request('status'))); ?></span><?php endif; ?>
                            <?php if(request('month')): ?><span class="filter-chip">MONTH: <?php echo e(strtoupper(request('month'))); ?></span><?php endif; ?>
                            <?php if(request('product_category')): ?><span class="filter-chip">CATEGORY: <?php echo e(strtoupper(request('product_category'))); ?></span><?php endif; ?>
                        </div>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Tab Navigation -->
            <?php echo $__env->make('dashboard.master._tabs', ['active' => $active ?? ''], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <!-- Data Table -->
            <div class="card overflow-hidden mb-8">
                <!-- Desktop Table -->
                <div class="hidden md:block overflow-x-auto">
                    <div style="max-height: 600px; overflow-y: auto;">
                        <table class="min-w-full table-auto divide-y divide-gray-200">
                            <thead class="bg-gray-50/50 sticky top-0 z-10">
                                <tr>
                                    <th class="px-6 py-4 text-left small-caps text-gray-600 font-medium min-w-[120px] whitespace-nowrap">Date Created</th>
                                    <th class="px-6 py-4 text-left small-caps text-gray-600 font-medium min-w-[220px] whitespace-nowrap">Sales Person</th>
                                    <th class="px-6 py-4 text-left small-caps text-gray-600 font-medium min-w-[220px] whitespace-nowrap">Company Name</th>
                                    <th class="px-6 py-4 text-left small-caps text-gray-600 font-medium min-w-[170px] whitespace-nowrap">Person In Charge</th>
                                    <th class="px-6 py-4 text-left small-caps text-gray-600 font-medium min-w-[220px] whitespace-nowrap">Email</th>
                                    <th class="px-6 py-4 text-right small-caps text-gray-600 font-medium min-w-[220px] whitespace-nowrap">Amount</th>
                                    <th class="px-6 py-4 text-left small-caps text-gray-600 font-medium min-w-[170px] whitespace-nowrap">Contact Number</th>
                                    <th class="px-6 py-4 text-left small-caps text-gray-600 font-medium min-w-[120px] whitespace-nowrap">Product</th>
                                    <th class="px-6 py-4 text-left small-caps text-gray-600 font-medium min-w-[120px] whitespace-nowrap">Month</th>
                                    <th class="px-6 py-4 text-left small-caps text-gray-600 font-medium min-w-[120px] whitespace-nowrap">Start Date</th>
                                    <th class="px-6 py-4 text-left small-caps text-gray-600 font-medium min-w-[120px] whitespace-nowrap">End Date</th>
                                    <th class="px-6 py-4 text-left small-caps text-gray-600 font-medium min-w-[120px] whitespace-nowrap">Duration</th>
                                    <th class="px-6 py-4 text-left small-caps text-gray-600 font-medium min-w-[120px] whitespace-nowrap">Status</th>
                                    <th class="px-6 py-4 text-left small-caps text-gray-600 font-medium min-w-[120px] whitespace-nowrap">Job</th>
                                    <th class="px-6 py-4 text-left small-caps text-gray-600 font-medium min-w-[120px] whitespace-nowrap">Artwork</th>
                                    <th class="px-6 py-4 text-left small-caps text-gray-600 font-medium min-w-[100px] whitespace-nowrap">Traffic</th>
                                    <th class="px-6 py-4 text-left small-caps text-gray-600 font-medium min-w-[120px] whitespace-nowrap">Invoice Date</th>
                                    <th class="px-6 py-4 text-left small-caps text-gray-600 font-medium min-w-[160px] whitespace-nowrap">Invoice Number</th>
                                    <th class="px-6 py-4 text-left small-caps text-gray-600 font-medium min-w-[120px] whitespace-nowrap">Remarks</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                <?php if(isset($masterFiles) && $masterFiles->count() > 0): ?>
                                    <?php $__currentLoopData = $masterFiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr class="table-row <?php echo e($loop->iteration % 2 === 0 ? 'bg-white' : 'bg-gray-50'); ?>">
                                            <td class="px-6 py-4 text-sm ink"><?php echo e($file->created_at ? $file->created_at->format('M d, Y') : '-'); ?></td>
                                            <td class="px-6 py-4 text-sm ink"><?php echo e($file->sales_person ?? '-'); ?></td>
                                            <td class="px-6 py-4 text-sm">
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('masterfile.show')): ?>
                                                <a href="<?php echo e(route('masterfile.show', $file->id)); ?>" class="ink hover:text-blue-600 font-medium">
                                                    <div class="max-w-[200px] truncate" title="<?php echo e($file->company); ?>"><?php echo e($file->company); ?></div>
                                                </a>
                                                <?php else: ?>
                                                <div class="max-w-[200px] truncate" title="<?php echo e($file->company); ?>"><?php echo e($file->company); ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-6 py-4 text-sm ink"><?php echo e($file->client ?? '-'); ?></td>
                                            <td class="px-6 py-4 text-sm text-gray-600"><?php echo e($file->email ?? '-'); ?></td>
                                            <td class="px-6 py-4 text-sm ink text-right tabular-nums font-medium"><?php echo e($file->amount ?? '-'); ?></td>
                                            <td class="px-6 py-4 text-sm ink"><?php echo e($file->contact_number ?? '-'); ?></td>
                                            <td class="px-6 py-4 text-sm ink"><?php echo e($file->product ?? '-'); ?></td>
                                            <td class="px-6 py-4 text-sm ink"><?php echo e($file->month ?? '-'); ?></td>
                                            <td class="px-6 py-4 text-sm ink"><?php echo e($file->date ? \Carbon\Carbon::parse($file->date)->format('M d, Y') : '-'); ?></td>
                                            <td class="px-6 py-4 text-sm ink"><?php echo e($file->date_finish ? \Carbon\Carbon::parse($file->end_date)->format('M d, Y') : '-'); ?></td>
                                            <td class="px-6 py-4 text-sm ink"><?php echo e($file->duration ?? '-'); ?></td>
                                            <td class="px-6 py-4 text-sm">
                                                <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full
                                                  <?php echo e($file->status === 'completed' ? 'bg-green-100 text-green-800' : ($file->status === 'ongoing' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')); ?>">
                                                  <?php echo e(ucfirst($file->status ?? 'pending')); ?>

                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm ink"><?php echo e($file->job_number ?? '-'); ?></td>
                                            <td class="px-6 py-4 text-sm ink"><?php echo e($file->artwork ?? '-'); ?></td>
                                            <td class="px-6 py-4 text-sm ink"><?php echo e($file->traffic ?? '-'); ?></td>
                                            <td class="px-6 py-4 text-sm ink"><?php echo e($file->invoice_date ?? '-'); ?></td>
                                            <td class="px-6 py-4 text-sm ink"><?php echo e($file->invoice_number ?? '-'); ?></td>
                                            <td class="px-6 py-4 text-sm ink"><?php echo e($file->remarks ?? '-'); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="19" class="px-6 py-16 text-center text-gray-500">
                                            <div class="flex flex-col items-center">
                                                <svg class="w-16 h-16 text-gray-300 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                <h3 class="font-serif text-lg font-medium ink mb-2">No records found</h3>
                                                <p class="text-gray-600 mb-4">Get started by adding your first record.</p>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('masterfile.create')): ?>
                                                <a href="<?php echo e(route('masterfile.create')); ?>" class="btn-primary px-4 py-2 rounded-xl text-sm">
                                                    Add New Record
                                                </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Table (if needed) -->
                <div class="md:hidden">
                    <!-- Mobile cards would go here if implemented -->
                </div>
            </div>

            <!-- Pagination -->
            <?php if(isset($masterFiles) && method_exists($masterFiles, 'links')): ?>
                <div class="mb-8">
                    <?php echo e($masterFiles->links()); ?>

                </div>
            <?php endif; ?>

            <!-- Quick Navigation -->
            <div class="card p-8">
                <div class="text-center mb-8">
                    <h2 class="font-serif text-2xl font-medium ink mb-2">Quick Navigation</h2>
                    <p class="text-sm text-gray-600">Navigate to different monthly job sections</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                     <!-- KLTG Jobs -->
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('kltg.edit')): ?>
                    <a href="<?php echo e(route('dashboard.kltg')); ?>" class="nav-card card p-6 hover:no-underline group">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center group-hover:bg-blue-50 transition-colors">
                                    <svg class="w-5 h-5 text-gray-600 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-medium ink group-hover:text-blue-900 transition-colors">KLTG Monthly Jobs</h3>
                                <p class="text-sm text-gray-600 mt-1">Manage KLTG monthly jobs and tasks</p>
                            </div>
                        </div>
                    </a>
                    <?php endif; ?>

                    <!-- Media Jobs -->
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('media.edit')): ?>
                    <a href="<?php echo e(route('dashboard.media')); ?>" class="nav-card card p-6 hover:no-underline group">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center group-hover:bg-blue-50 transition-colors">
                                    <svg class="w-5 h-5 text-gray-600 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-medium ink group-hover:text-blue-900 transition-colors">Social Media Monthly Jobs</h3>
                                <p class="text-sm text-gray-600 mt-1">Handle media monthly jobs and campaigns</p>
                            </div>
                        </div>
                    </a>
                    <?php endif; ?>

                    <!-- Outdoor Jobs -->
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('outdoor.edit')): ?>
                    <a href="<?php echo e(route('dashboard.outdoor')); ?>" class="nav-card card p-6 hover:no-underline group">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center group-hover:bg-blue-50 transition-colors">
                                    <svg class="w-5 h-5 text-gray-600 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-medium ink group-hover:text-blue-900 transition-colors">Outdoor Monthly Jobs</h3>
                                <p class="text-sm text-gray-600 mt-1">Manage outdoor monthly advertising jobs</p>
                            </div>
                        </div>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('masterfile.import')): ?>
    <div id="importModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-25 flex items-center justify-center p-4">
        <div class="card max-w-lg w-full p-8 max-h-screen overflow-y-auto">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h3 class="font-serif text-xl font-medium ink">Import Master File Data</h3>
                    <p class="text-sm text-gray-600 mt-1">Upload your master file data</p>
                </div>
                <button onclick="closeImportModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Display any validation errors -->
            <?php if($errors->any()): ?>
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <svg class="w-5 h-5 text-red-400 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="text-sm text-red-800">
                            <h4 class="font-semibold mb-2">Import Error:</h4>
                            <ul class="list-disc list-inside space-y-1">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <form action="<?php echo e(route('masterfile.import')); ?>" method="POST" enctype="multipart/form-data" class="space-y-6" id="importForm">
                <?php echo csrf_field(); ?>
                <!-- File Upload Area -->
                <div>
                    <label class="small-caps text-gray-600 block mb-3">Choose File</label>
                    <div class="border-2 border-dashed hairline rounded-xl p-8 text-center hover:border-blue-300 transition-colors"
                         ondrop="handleDrop(event)" ondragover="handleDragOver(event)" ondragenter="handleDragEnter(event)" ondragleave="handleDragLeave(event)">
                        <svg class="w-10 h-10 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 10v6m0 0l-3-3m3 3l3-3"></path>
                        </svg>
                        <div class="text-sm">
                            <label for="importFile" class="text-blue-600 hover:text-blue-500 cursor-pointer font-medium">
                                Choose a file
                                <input id="importFile" name="file" type="file" class="sr-only" accept=".csv,.xlsx,.xls" required>
                            </label>
                            <span class="text-gray-600"> or drag and drop</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">CSV, XLSX, XLS up to 10MB</p>
                    </div>
                    <div id="selectedFileName" class="mt-2 text-sm text-gray-600 hidden"></div>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-3 pt-4">
                    <button type="button" onclick="closeImportModal()" class="btn-ghost flex-1 px-4 py-2.5 rounded-xl text-sm font-medium">
                        Cancel
                    </button>
                    <button type="submit" id="importSubmitBtn" class="btn-primary flex-1 px-4 py-2.5 rounded-xl text-sm font-medium">
                        Import Data
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <script>
        // Import modal functions
        function testImportModal() {
            console.log('🔵 testImportModal called');
            const modal = document.getElementById('importModal');
            console.log('🔵 Modal element:', modal);

            if (modal) {
                console.log('🟢 Modal found, showing modal');
                modal.classList.remove('hidden');
                modal.style.display = 'flex';
            } else {
                console.log('🔴 Modal NOT found!');
                alert('Error: Modal element not found in DOM');
            }
        }

        function closeImportModal() {
            console.log('🔵 closeImportModal called');
            const modal = document.getElementById('importModal');
            if (modal) {
                modal.classList.add('hidden');
                modal.style.display = 'none';
                // Reset the form
                const form = document.getElementById('importForm');
                if (form) {
                    form.reset();
                    const fileLabel = document.getElementById('selectedFileName');
                    if (fileLabel) {
                        fileLabel.classList.add('hidden');
                        fileLabel.textContent = '';
                    }
                    // Reset submit button
                    const submitBtn = document.getElementById('importSubmitBtn');
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Import Data';
                        submitBtn.classList.remove('opacity-75');
                    }
                }
            }
        }

        // File selection handler
        function handleFileSelect(input) {
            const fileName = input.files[0]?.name;
            const fileLabel = document.getElementById('selectedFileName');
            if (fileName && fileLabel) {
                fileLabel.classList.remove('hidden');
                fileLabel.textContent = `Selected file: ${fileName}`;

                // Validate file type
                const allowedTypes = ['.csv', '.xlsx', '.xls'];
                const fileExtension = '.' + fileName.split('.').pop().toLowerCase();

                if (!allowedTypes.includes(fileExtension)) {
                    alert('Please select a valid file type: CSV, XLSX, or XLS');
                    input.value = '';
                    fileLabel.classList.add('hidden');
                    fileLabel.textContent = '';
                    return;
                }

                // Validate file size (10MB limit)
                const maxSize = 10 * 1024 * 1024; // 10MB in bytes
                if (input.files[0].size > maxSize) {
                    alert('File size must be less than 10MB');
                    input.value = '';
                    fileLabel.classList.add('hidden');
                    fileLabel.textContent = '';
                    return;
                }
            } else if (fileLabel) {
                fileLabel.classList.add('hidden');
                fileLabel.textContent = '';
            }
        }

        // Drag and drop handlers
        function handleDragOver(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        function handleDragEnter(e) {
            e.preventDefault();
            e.stopPropagation();
            e.currentTarget.classList.add('border-blue-300', 'bg-blue-50');
        }

        function handleDragLeave(e) {
            e.preventDefault();
            e.stopPropagation();
            e.currentTarget.classList.remove('border-blue-300', 'bg-blue-50');
        }

        function handleDrop(e) {
            e.preventDefault();
            e.stopPropagation();
            e.currentTarget.classList.remove('border-blue-300', 'bg-blue-50');

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                const fileInput = document.getElementById('importFile');
                if (fileInput) {
                    fileInput.files = files;
                    handleFileSelect(fileInput);
                }
            }
        }

        // Form submission handler with validation
        document.addEventListener('DOMContentLoaded', function() {
            const importForm = document.getElementById('importForm');
            if (importForm) {
                importForm.addEventListener('submit', function(e) {
                    const fileInput = document.getElementById('importFile');
                    if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
                        e.preventDefault();
                        alert('Please select a file to import.');
                        return false;
                    }
                });
            }

            // File input change handler
            const fileInput = document.getElementById('importFile');
            if (fileInput) {
                fileInput.addEventListener('change', function() {
                    handleFileSelect(this);
                });
            }
        });

        // Close modal when clicking outside
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('importModal');
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeImportModal();
                    }
                });
            }
        });

        // Add CSRF token to head if not already present
        if (!document.querySelector('meta[name="csrf-token"]')) {
            const csrfMeta = document.createElement('meta');
            csrfMeta.name = 'csrf-token';
            csrfMeta.content = '<?php echo e(csrf_token()); ?>';
            document.head.appendChild(csrfMeta);
        }

        console.log("✅ Elegant Dashboard loaded successfully!");
    </script>
</body>
</html>
<?php /**PATH C:\Users\Gjlang\kl_guide_tracker\resources\views\dashboard.blade.php ENDPATH**/ ?>