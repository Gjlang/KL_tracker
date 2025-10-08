<?php $__env->startSection('title', 'All-in-One Summary Report'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    body { background-color: #F7F7F9; }
    .ink { color: #1C1E26; }
    .hairline { border-color: #EAEAEA; border-width: 1px; }
    .caps-label { letter-spacing: .06em; text-transform: uppercase; font-size: 11px; color: #6B7280; }
    .tabular-nums { font-variant-numeric: tabular-nums; }

    @media print {
        body { background: #fff !important; }
        .no-print { display: none !important; }
        .card { box-shadow: none !important; border-color: #ddd !important; }
        .page-break { page-break-after: always; }
        * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }

    .progress-fill {
        transition: width 0.3s ease;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen" x-data="{ downloading: false, printing: false }">
    
    <div class="bg-white border-b hairline">
        <div class="max-w-7xl mx-auto px-6 py-6">
            <div class="flex items-start justify-between">
                <div class="space-y-3">
                    <h1 class="text-3xl font-serif font-medium ink">All-in-One Summary Report</h1>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="caps-label">Filters:</span>
                        <?php if (isset($component)) { $__componentOriginal3e43da63772e725970863e9067088b49 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3e43da63772e725970863e9067088b49 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.chip','data' => ['label' => 'Year','value' => ''.e($filters['year']).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('chip'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Year','value' => ''.e($filters['year']).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3e43da63772e725970863e9067088b49)): ?>
<?php $attributes = $__attributesOriginal3e43da63772e725970863e9067088b49; ?>
<?php unset($__attributesOriginal3e43da63772e725970863e9067088b49); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3e43da63772e725970863e9067088b49)): ?>
<?php $component = $__componentOriginal3e43da63772e725970863e9067088b49; ?>
<?php unset($__componentOriginal3e43da63772e725970863e9067088b49); ?>
<?php endif; ?>
                        <?php if(!empty($filters['month'])): ?>
                            <?php if (isset($component)) { $__componentOriginal3e43da63772e725970863e9067088b49 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3e43da63772e725970863e9067088b49 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.chip','data' => ['label' => 'Month','value' => ''.e($filters['month']).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('chip'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Month','value' => ''.e($filters['month']).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3e43da63772e725970863e9067088b49)): ?>
<?php $attributes = $__attributesOriginal3e43da63772e725970863e9067088b49; ?>
<?php unset($__attributesOriginal3e43da63772e725970863e9067088b49); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3e43da63772e725970863e9067088b49)): ?>
<?php $component = $__componentOriginal3e43da63772e725970863e9067088b49; ?>
<?php unset($__componentOriginal3e43da63772e725970863e9067088b49); ?>
<?php endif; ?>
                        <?php endif; ?>
                        <?php if(!empty($filters['status'])): ?>
                            <?php if (isset($component)) { $__componentOriginal3e43da63772e725970863e9067088b49 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3e43da63772e725970863e9067088b49 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.chip','data' => ['label' => 'Status','value' => ''.e($filters['status']).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('chip'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Status','value' => ''.e($filters['status']).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3e43da63772e725970863e9067088b49)): ?>
<?php $attributes = $__attributesOriginal3e43da63772e725970863e9067088b49; ?>
<?php unset($__attributesOriginal3e43da63772e725970863e9067088b49); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3e43da63772e725970863e9067088b49)): ?>
<?php $component = $__componentOriginal3e43da63772e725970863e9067088b49; ?>
<?php unset($__componentOriginal3e43da63772e725970863e9067088b49); ?>
<?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="flex items-center gap-3 no-print">
                    <a href="<?php echo e(route('report.summary.pdf', request()->only(['year','month','status']))); ?>"
                       class="inline-flex items-center px-4 py-2 rounded-full text-white text-sm font-medium transition-colors focus:ring-2 focus:ring-[#4bbbed] focus:outline-none"
                       style="background-color: #22255b;"
                       x-on:click="downloading = true"
                       x-bind:aria-busy="downloading"
                       x-bind:disabled="downloading">
                        <span x-show="!downloading">Download PDF</span>
                        <span x-show="downloading" class="flex items-center gap-2">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Downloading...
                        </span>
                    </a>

                    <button onclick="window.print()"
                            class="inline-flex items-center px-4 py-2 rounded-full border border-neutral-300 text-sm font-medium transition-colors hover:bg-neutral-50 focus:ring-2 focus:ring-[#4bbbed] focus:outline-none ink"
                            x-on:click="printing = true; setTimeout(() => printing = false, 1000)"
                            x-bind:aria-busy="printing"
                            x-bind:disabled="printing">
                        <span x-show="!printing">Print Now</span>
                        <span x-show="printing">Printing...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 py-8 space-y-8">
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php if (isset($component)) { $__componentOriginal7eb63bdf3faa86d7e1b98377393d1632 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.kpi','data' => ['value' => $master['active_companies'] ?? 0,'label' => 'Active Companies','sublabel' => 'distinct in '.e($filters['year']).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('kpi'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($master['active_companies'] ?? 0),'label' => 'Active Companies','sublabel' => 'distinct in '.e($filters['year']).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632)): ?>
<?php $attributes = $__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632; ?>
<?php unset($__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7eb63bdf3faa86d7e1b98377393d1632)): ?>
<?php $component = $__componentOriginal7eb63bdf3faa86d7e1b98377393d1632; ?>
<?php unset($__componentOriginal7eb63bdf3faa86d7e1b98377393d1632); ?>
<?php endif; ?>

            <?php if (isset($component)) { $__componentOriginal7eb63bdf3faa86d7e1b98377393d1632 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.kpi','data' => ['value' => $outdoor['active_jobs'] ?? 0,'label' => 'Outdoor Jobs','sublabel' => 'currently active','available' => $outdoor['available'] ?? false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('kpi'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($outdoor['active_jobs'] ?? 0),'label' => 'Outdoor Jobs','sublabel' => 'currently active','available' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($outdoor['available'] ?? false)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632)): ?>
<?php $attributes = $__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632; ?>
<?php unset($__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7eb63bdf3faa86d7e1b98377393d1632)): ?>
<?php $component = $__componentOriginal7eb63bdf3faa86d7e1b98377393d1632; ?>
<?php unset($__componentOriginal7eb63bdf3faa86d7e1b98377393d1632); ?>
<?php endif; ?>

            <?php if (isset($component)) { $__componentOriginal7eb63bdf3faa86d7e1b98377393d1632 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.kpi','data' => ['value' => ($outdoor['completion_rate'] ?? 0) . '%','label' => 'Completion Rate','sublabel' => 'this period','available' => $outdoor['available'] ?? false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('kpi'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(($outdoor['completion_rate'] ?? 0) . '%'),'label' => 'Completion Rate','sublabel' => 'this period','available' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($outdoor['available'] ?? false)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632)): ?>
<?php $attributes = $__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632; ?>
<?php unset($__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7eb63bdf3faa86d7e1b98377393d1632)): ?>
<?php $component = $__componentOriginal7eb63bdf3faa86d7e1b98377393d1632; ?>
<?php unset($__componentOriginal7eb63bdf3faa86d7e1b98377393d1632); ?>
<?php endif; ?>

            <?php if (isset($component)) { $__componentOriginal7eb63bdf3faa86d7e1b98377393d1632 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.kpi','data' => ['value' => $outdoor['issues'] ?? 0,'label' => 'Pending Issues','sublabel' => 'needs attention','available' => $outdoor['available'] ?? false,'accent' => 'text-[#d33831]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('kpi'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($outdoor['issues'] ?? 0),'label' => 'Pending Issues','sublabel' => 'needs attention','available' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($outdoor['available'] ?? false),'accent' => 'text-[#d33831]']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632)): ?>
<?php $attributes = $__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632; ?>
<?php unset($__attributesOriginal7eb63bdf3faa86d7e1b98377393d1632); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7eb63bdf3faa86d7e1b98377393d1632)): ?>
<?php $component = $__componentOriginal7eb63bdf3faa86d7e1b98377393d1632; ?>
<?php unset($__componentOriginal7eb63bdf3faa86d7e1b98377393d1632); ?>
<?php endif; ?>
        </div>

        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <div class="space-y-8">
                
                <?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => ['title' => 'Master File','class' => 'space-y-6']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Master File','class' => 'space-y-6']); ?>
                    <div>
                        <div class="text-4xl font-semibold tabular-nums ink mb-1"><?php echo e($master['active_companies'] ?? 0); ?></div>
                        <p class="text-sm text-neutral-600">Active companies (distinct in <?php echo e($filters['year']); ?>)</p>
                    </div>

                    <div>
                        <h3 class="font-medium ink mb-3">By Category</h3>
                        <div class="space-y-2">
                            <?php $__empty_1 = true; $__currentLoopData = $master['by_category'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <div class="flex justify-between items-center py-1">
                                    <span class="text-sm text-neutral-700"><?php echo e($row['category'] ?? 'Unknown'); ?></span>
                                    <span class="text-sm font-medium tabular-nums ink"><?php echo e($row['total']); ?></span>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <p class="text-sm text-neutral-400">No category data available</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div>
                        <h3 class="font-medium ink mb-3">Status Distribution</h3>
                        <?php
                            $sd = $master['status_dist'] ?? [];
                            $total = array_sum($sd);
                            $pending = $sd['pending'] ?? 0;
                            $inProgress = $sd['in-progress'] ?? 0;
                            $completed = $sd['completed'] ?? 0;
                        ?>

                        <?php if($total > 0): ?>
                            <div class="space-y-3">
                                <div class="h-2 rounded-full bg-neutral-100 overflow-hidden">
                                    <div class="h-full flex">
                                        <div class="bg-amber-400 progress-fill" style="width: <?php echo e(($pending / $total) * 100); ?>%"></div>
                                        <div class="bg-blue-400 progress-fill" style="width: <?php echo e(($inProgress / $total) * 100); ?>%"></div>
                                        <div class="bg-green-400 progress-fill" style="width: <?php echo e(($completed / $total) * 100); ?>%"></div>
                                    </div>
                                </div>
                                <div class="flex justify-between text-xs">
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 rounded-full bg-amber-400"></div>
                                        <span class="text-neutral-600">Pending (<?php echo e(round(($pending / $total) * 100)); ?>%)</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 rounded-full bg-blue-400"></div>
                                        <span class="text-neutral-600">In-progress (<?php echo e(round(($inProgress / $total) * 100)); ?>%)</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 rounded-full bg-green-400"></div>
                                        <span class="text-neutral-600">Completed (<?php echo e(round(($completed / $total) * 100)); ?>%)</span>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <p class="text-sm text-neutral-400">No status data available</p>
                        <?php endif; ?>
                    </div>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $attributes = $__attributesOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__attributesOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $component = $__componentOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__componentOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>

                
                <?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => ['title' => 'KLTG','class' => 'space-y-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'KLTG','class' => 'space-y-4']); ?>
                    <?php if(($kltg['available'] ?? false) === false): ?>
                        <p class="text-neutral-400 text-center py-8">Table not found</p>
                    <?php else: ?>
                        <div class="space-y-4">
                            <div>
                                <div class="flex justify-between items-end mb-2">
                                    <span class="text-sm text-neutral-600">Production Progress</span>
                                    <span class="text-2xl font-semibold tabular-nums ink"><?php echo e($kltg['production_progress']); ?>%</span>
                                </div>
                                <?php if (isset($component)) { $__componentOriginale375e741fa8af2e5aa10d29452e1526c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale375e741fa8af2e5aa10d29452e1526c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.progress','data' => ['value' => $kltg['production_progress'],'color' => 'bg-[#4bbbed]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('progress'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($kltg['production_progress']),'color' => 'bg-[#4bbbed]']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale375e741fa8af2e5aa10d29452e1526c)): ?>
<?php $attributes = $__attributesOriginale375e741fa8af2e5aa10d29452e1526c; ?>
<?php unset($__attributesOriginale375e741fa8af2e5aa10d29452e1526c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale375e741fa8af2e5aa10d29452e1526c)): ?>
<?php $component = $__componentOriginale375e741fa8af2e5aa10d29452e1526c; ?>
<?php unset($__componentOriginale375e741fa8af2e5aa10d29452e1526c); ?>
<?php endif; ?>
                            </div>

                            <div class="grid grid-cols-2 gap-4 pt-2">
                                <div class="text-center">
                                    <div class="text-lg font-medium tabular-nums ink"><?php echo e($kltg['slots_filled']); ?></div>
                                    <div class="text-xs text-neutral-600">Slots Filled</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-lg font-medium tabular-nums ink"><?php echo e($kltg['pending_approvals']); ?></div>
                                    <div class="text-xs text-neutral-600">Pending Approvals</div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $attributes = $__attributesOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__attributesOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $component = $__componentOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__componentOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
            </div>

            
            <div class="space-y-8">
                
                <?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => ['title' => 'Outdoor','class' => 'space-y-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Outdoor','class' => 'space-y-4']); ?>
                    <?php if(($outdoor['available'] ?? false) === false): ?>
                        <p class="text-neutral-400 text-center py-8">Table not found</p>
                    <?php else: ?>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-neutral-600">Active Jobs</span>
                                <span class="text-2xl font-semibold tabular-nums ink"><?php echo e($outdoor['active_jobs']); ?></span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-sm text-neutral-600">Completed (period)</span>
                                <span class="font-medium tabular-nums ink"><?php echo e($outdoor['completed_this']); ?></span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-sm text-neutral-600">Completion Rate</span>
                                <span class="font-medium tabular-nums ink"><?php echo e($outdoor['completion_rate']); ?>%</span>
                            </div>

                            <div class="flex justify-between items-center pt-2 border-t hairline">
                                <span class="text-sm text-neutral-600">Issues (pending/in-progress)</span>
                                <span class="font-medium tabular-nums text-[#d33831]"><?php echo e($outdoor['issues']); ?></span>
                            </div>
                        </div>
                    <?php endif; ?>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $attributes = $__attributesOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__attributesOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $component = $__componentOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__componentOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>

                
                <?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => ['title' => 'Media Social','class' => 'space-y-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Media Social','class' => 'space-y-4']); ?>
                    <?php if(($media['available'] ?? false) === false): ?>
                        <p class="text-neutral-400 text-center py-8">Table not found</p>
                    <?php else: ?>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-neutral-600">Campaigns</span>
                                <span class="text-2xl font-semibold tabular-nums ink"><?php echo e($media['campaigns']); ?></span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-sm text-neutral-600">Posts (period)</span>
                                <span class="font-medium tabular-nums ink"><?php echo e($media['posts']); ?></span>
                            </div>
                        </div>
                    <?php endif; ?>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $attributes = $__attributesOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__attributesOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $component = $__componentOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__componentOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
            </div>
        </div>

        
        <div class="text-center">
            <p class="text-xs text-neutral-400">
                Generated at <?php echo e($generated->timezone(config('app.timezone'))->format('M d, Y H:i')); ?>

            </p>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Gjlang\kl_guide_tracker\resources\views\reports\summary.blade.php ENDPATH**/ ?>