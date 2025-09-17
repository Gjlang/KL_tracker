<?php if (isset($component)) { $__componentOriginal4619374cef299e94fd7263111d0abc69 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4619374cef299e94fd7263111d0abc69 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-layout','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            📘 KLTG Matrix Vertical View for <?php echo e($file->company); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-10 px-6">
        <div class="bg-white rounded shadow p-6 w-full max-w-4xl mx-auto">
            <table class="w-full border text-sm text-left">
                <thead>
                    <tr>
                        <th class="px-4 py-2 bg-gray-100">Period</th>
                        <th class="px-4 py-2 bg-gray-100 text-center"><?php echo e($file->company); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $value = optional($grouped[$month] ?? collect())->firstWhere('type', $type)?->status;
                                $bg = str_contains(strtolower($value), 'tbd') ? 'bg-red-200' :
                                      (str_contains(strtolower($value), 'x') ? 'bg-green-200' :
                                      (str_contains(strtolower($value), 'free') ? 'bg-red-100' : ''));
                            ?>
                            <tr class="border-t">
                                <td class="px-4 py-2 font-medium text-gray-700"><?php echo e($month); ?> - <?php echo e($type); ?></td>
                                <td class="px-4 py-2 text-center <?php echo e($bg); ?>"><?php echo e($value ?? '-'); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>

            <div class="mt-6">
                <a href="<?php echo e(route('dashboard')); ?>" class="text-indigo-600 hover:underline">← Back to Dashboard</a>
            </div>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4619374cef299e94fd7263111d0abc69)): ?>
<?php $attributes = $__attributesOriginal4619374cef299e94fd7263111d0abc69; ?>
<?php unset($__attributesOriginal4619374cef299e94fd7263111d0abc69); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4619374cef299e94fd7263111d0abc69)): ?>
<?php $component = $__componentOriginal4619374cef299e94fd7263111d0abc69; ?>
<?php unset($__componentOriginal4619374cef299e94fd7263111d0abc69); ?>
<?php endif; ?>
<?php /**PATH C:\Users\Gjlang\kl_guide_tracker\resources\views\kltg\matrix.blade.php ENDPATH**/ ?>