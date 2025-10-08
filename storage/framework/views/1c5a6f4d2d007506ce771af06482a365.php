
<div class="flex gap-2 border-b border-gray-200 mb-4">
    <a href="<?php echo e(route('dashboard.master.kltg')); ?>"
       class="px-4 py-2 text-sm font-medium border-b-2 <?php echo e(($active ?? '')==='kltg' ? 'border-blue-600 text-blue-700' : 'border-transparent text-gray-500 hover:text-gray-700'); ?>">
        KLTG MASTER CLIENTELE
    </a>
    <a href="<?php echo e(route('dashboard.master.outdoor')); ?>"
       class="px-4 py-2 text-sm font-medium border-b-2 <?php echo e(($active ?? '')==='outdoor' ? 'border-blue-600 text-blue-700' : 'border-transparent text-gray-500 hover:text-gray-700'); ?>">
        OUTDOOR MASTER CLIENTELE
    </a>
</div>
<?php /**PATH C:\Users\Gjlang\kl_guide_tracker\resources\views\dashboard\master\_tabs.blade.php ENDPATH**/ ?>