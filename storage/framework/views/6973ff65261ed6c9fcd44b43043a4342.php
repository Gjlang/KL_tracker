<?php $__env->startSection('content'); ?>
<div class="p-6">
    <h2 class="text-2xl font-bold mb-4">Master File List</h2>

    <form method="GET" class="mb-4 flex gap-4">
        <input type="text" name="search" placeholder="Search by company or product" class="border px-3 py-2 rounded w-64" value="<?php echo e(request('search')); ?>">
        <select name="month" class="border px-3 py-2 rounded">
            <option value="">All Months</option>
            <?php $__currentLoopData = ['January','February','March','April','May','June','July','August','September','October','November','December']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($m); ?>" <?php echo e(request('month') == $m ? 'selected' : ''); ?>><?php echo e($m); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Filter</button>
    </form>

    <table class="min-w-full bg-white border">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 border">Date</th>
                <th class="px-4 py-2 border">Company</th>
                <th class="px-4 py-2 border">Product</th>
                <th class="px-4 py-2 border">Status</th>
                <th class="px-4 py-2 border">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $masterFiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="border-t hover:bg-gray-50">
                    <td class="px-4 py-2 border"><?php echo e($file->date); ?></td>
                    <td class="px-4 py-2 border"><?php echo e($file->company); ?></td>
                    <td class="px-4 py-2 border"><?php echo e($file->product); ?></td>
                    <td class="px-4 py-2 border"><?php echo e(ucfirst($file->status)); ?></td>
                    <td class="px-4 py-2 border">
                        <a href="<?php echo e(route('masterfile.show', $file->id)); ?>" class="text-blue-600 hover:underline">View</a>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <div class="mt-4">
        <?php echo e($masterFiles->withQueryString()->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Gjlang\kl_guide_tracker\resources\views\confirmation_links\confirmationlink.blade.php ENDPATH**/ ?>