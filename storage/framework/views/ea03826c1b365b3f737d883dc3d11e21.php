
<?php
    // Normalize current values from the request
    $currentMonth = (int) request('month', 0); // 0 = All
    $query        = trim((string) request('q', ''));
?>

<form method="GET" action="<?php echo e($action ?? url()->current()); ?>" class="mb-4">
    <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-end">
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Month</label>
            <select name="month"
                    class="block w-48 rounded-lg border-gray-300 focus:ring-2 focus:ring-indigo-500">
                <option value="0" <?php echo e($currentMonth===0 ? 'selected' : ''); ?>>All months</option>
                <?php for($m=1; $m<=12; $m++): ?>
                    <option value="<?php echo e($m); ?>" <?php echo e($currentMonth===$m ? 'selected' : ''); ?>>
                        <?php echo e(\Carbon\Carbon::create(null, $m, 1)->format('F')); ?>

                    </option>
                <?php endfor; ?>
            </select>
        </div>

        
        <div class="flex-1 min-w-[240px]">
            <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
            <input type="text" name="q" value="<?php echo e($query); ?>"
                   placeholder="Type any keyword…"
                   class="block w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-indigo-500" />
        </div>

        
        <div class="flex gap-2">
            <button type="submit"
                    class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">
                Apply
            </button>
            
            <a href="<?php echo e($clearUrl ?? url()->current()); ?>"
               class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">
                Reset
            </a>
        </div>
    </div>
</form>
<?php /**PATH C:\Users\Gjlang\kl_guide_tracker\resources\views/dashboard/master/_filters.blade.php ENDPATH**/ ?>