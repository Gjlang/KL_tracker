

<?php
$colsByTab = [
  'content'  => ['No','Company','Client Name','Package (Product)','Total Artwork (Date)','Pending (Date)','Draft WA','Approved','Remarks'],
  'editing'  => ['No','Company','Client Name','Package (Product)','Total Artwork (Date)','Pending (Date)','Draft WA','Approved','Remarks'],
  'schedule' => ['No','Company','Client Name','Package (Product)','Total Artwork (Date)','CRM (Date)','Meta/Ads Manager (Date)','TikTok/IG Draft','Remarks'],
  'report'   => ['No','Company','Client Name','Package (Product)','Pending (Date)','Completed (Date)','Remarks'],
  'valueadd' => ['No','Company','Client Name','Quota','Completed','Remarks'],
];
$headers = $colsByTab[$activeTab] ?? [];

$editableFieldsByTab = [
  'content'  => ['total_artwork_date' => 'date', 'pending_date' => 'date', 'draft_wa' => 'number', 'approved' => 'number', 'remarks' => 'text'],
  'editing'  => ['total_artwork_date' => 'date', 'pending_date' => 'date', 'draft_wa' => 'number', 'approved' => 'number', 'remarks' => 'text'],
  'schedule' => ['total_artwork_date' => 'date', 'crm_date' => 'date', 'meta_ads_manager_date' => 'date', 'tiktok_ig_draft' => 'number', 'remarks' => 'text'],
  'report'   => ['pending_date' => 'date', 'completed_date' => 'date', 'remarks' => 'text'],
  'valueadd' => ['quota' => 'number', 'completed' => 'number', 'remarks' => 'text'],
];

$edits = $editableFieldsByTab[$activeTab] ?? [];
$editingEnabled = !empty($month) && !empty($year); // upsert butuh month & year
?>

<?php if(!$editingEnabled): ?>
  <div class="mb-3 rounded-md bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-2">
    Select <strong>Month</strong> and <strong>Year</strong> to enable editing.
  </div>
<?php endif; ?>

<div class="overflow-hidden">
  <?php if(count($headers) > 0): ?>
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <?php $__currentLoopData = $headers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $header): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                <?php echo e($header); ?>

              </th>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
              $mfid = $row->master_file_id ?? null;
            ?>

            <?php if($activeTab === 'content' || $activeTab === 'editing'): ?>
              <tr class="hover:bg-gray-50 transition-colors duration-150" data-row-master="<?php echo e($mfid); ?>">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo e($index + 1); ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo e($row->company ?? ''); ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo e($row->client ?? ''); ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo e($row->product ?? ''); ?></td>

                
                <td class="px-6 py-2 text-sm">
                  <input type="date"
                         class="w-44 border border-gray-300 rounded-md px-2 py-1 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                         value="<?php echo e($row->total_artwork_date ?? ''); ?>"
                         <?php echo e($editingEnabled ? '' : 'disabled'); ?>

                         data-upsert
                         data-section="<?php echo e($activeTab); ?>"
                         data-field="total_artwork_date"
                         data-master="<?php echo e($mfid); ?>"
                         data-year="<?php echo e($year); ?>"
                         data-month="<?php echo e($month); ?>">
                </td>

                
                <td class="px-6 py-2 text-sm">
                  <input type="date"
                         class="w-44 border border-gray-300 rounded-md px-2 py-1 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                         value="<?php echo e($row->pending_date ?? ''); ?>"
                         <?php echo e($editingEnabled ? '' : 'disabled'); ?>

                         data-upsert
                         data-section="<?php echo e($activeTab); ?>"
                         data-field="pending_date"
                         data-master="<?php echo e($mfid); ?>"
                         data-year="<?php echo e($year); ?>"
                         data-month="<?php echo e($month); ?>">
                </td>

                
                <td class="px-6 py-2 text-sm">
                  <input type="number" min="0"
                         class="w-28 border border-gray-300 rounded-md px-2 py-1 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                         value="<?php echo e($row->draft_wa ?? ''); ?>"
                         <?php echo e($editingEnabled ? '' : 'disabled'); ?>

                         data-upsert
                         data-section="<?php echo e($activeTab); ?>"
                         data-field="draft_wa"
                         data-master="<?php echo e($mfid); ?>"
                         data-year="<?php echo e($year); ?>"
                         data-month="<?php echo e($month); ?>">
                </td>

                
                <td class="px-6 py-2 text-sm">
                  <input type="number" min="0"
                         class="w-28 border border-gray-300 rounded-md px-2 py-1 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                         value="<?php echo e($row->approved ?? ''); ?>"
                         <?php echo e($editingEnabled ? '' : 'disabled'); ?>

                         data-upsert
                         data-section="<?php echo e($activeTab); ?>"
                         data-field="approved"
                         data-master="<?php echo e($mfid); ?>"
                         data-year="<?php echo e($year); ?>"
                         data-month="<?php echo e($month); ?>">
                </td>

                
                <td class="px-6 py-2 text-sm">
                  <input type="text"
                         class="w-64 border border-gray-300 rounded-md px-2 py-1 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                         value="<?php echo e($row->remarks ?? ''); ?>"
                         <?php echo e($editingEnabled ? '' : 'disabled'); ?>

                         data-upsert
                         data-section="<?php echo e($activeTab); ?>"
                         data-field="remarks"
                         data-master="<?php echo e($mfid); ?>"
                         data-year="<?php echo e($year); ?>"
                         data-month="<?php echo e($month); ?>">
                </td>
              </tr>

            <?php elseif($activeTab === 'schedule'): ?>
              <tr class="hover:bg-gray-50 transition-colors duration-150" data-row-master="<?php echo e($mfid); ?>">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo e($index + 1); ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo e($row->company ?? ''); ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo e($row->client ?? ''); ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo e($row->product ?? ''); ?></td>

                
                <td class="px-6 py-2 text-sm">
                  <input type="date" class="w-44 border border-gray-300 rounded-md px-2 py-1 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                         value="<?php echo e($row->total_artwork_date ?? ''); ?>" <?php echo e($editingEnabled ? '' : 'disabled'); ?>

                         data-upsert data-section="<?php echo e($activeTab); ?>" data-field="total_artwork_date"
                         data-master="<?php echo e($mfid); ?>" data-year="<?php echo e($year); ?>" data-month="<?php echo e($month); ?>">
                </td>

                
                <td class="px-6 py-2 text-sm">
                  <input type="date" class="w-44 border border-gray-300 rounded-md px-2 py-1 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                         value="<?php echo e($row->crm_date ?? ''); ?>" <?php echo e($editingEnabled ? '' : 'disabled'); ?>

                         data-upsert data-section="<?php echo e($activeTab); ?>" data-field="crm_date"
                         data-master="<?php echo e($mfid); ?>" data-year="<?php echo e($year); ?>" data-month="<?php echo e($month); ?>">
                </td>

                
                <td class="px-6 py-2 text-sm">
                  <input type="date" class="w-44 border border-gray-300 rounded-md px-2 py-1 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                         value="<?php echo e($row->meta_ads_manager_date ?? ''); ?>" <?php echo e($editingEnabled ? '' : 'disabled'); ?>

                         data-upsert data-section="<?php echo e($activeTab); ?>" data-field="meta_ads_manager_date"
                         data-master="<?php echo e($mfid); ?>" data-year="<?php echo e($year); ?>" data-month="<?php echo e($month); ?>">
                </td>

                
                <td class="px-6 py-2 text-sm">
                  <input type="number" min="0" class="w-28 border border-gray-300 rounded-md px-2 py-1 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                         value="<?php echo e($row->tiktok_ig_draft ?? ''); ?>" <?php echo e($editingEnabled ? '' : 'disabled'); ?>

                         data-upsert data-section="<?php echo e($activeTab); ?>" data-field="tiktok_ig_draft"
                         data-master="<?php echo e($mfid); ?>" data-year="<?php echo e($year); ?>" data-month="<?php echo e($month); ?>">
                </td>

                
                <td class="px-6 py-2 text-sm">
                  <input type="text" class="w-64 border border-gray-300 rounded-md px-2 py-1 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                         value="<?php echo e($row->remarks ?? ''); ?>" <?php echo e($editingEnabled ? '' : 'disabled'); ?>

                         data-upsert data-section="<?php echo e($activeTab); ?>" data-field="remarks"
                         data-master="<?php echo e($mfid); ?>" data-year="<?php echo e($year); ?>" data-month="<?php echo e($month); ?>">
                </td>
              </tr>

            <?php elseif($activeTab === 'report'): ?>
              <tr class="hover:bg-gray-50 transition-colors duration-150" data-row-master="<?php echo e($mfid); ?>">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo e($index + 1); ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo e($row->company ?? ''); ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo e($row->client ?? ''); ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo e($row->product ?? ''); ?></td>

                
                <td class="px-6 py-2 text-sm">
                  <input type="date" class="w-44 border border-gray-300 rounded-md px-2 py-1 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                         value="<?php echo e($row->pending_date ?? ''); ?>" <?php echo e($editingEnabled ? '' : 'disabled'); ?>

                         data-upsert data-section="<?php echo e($activeTab); ?>" data-field="pending_date"
                         data-master="<?php echo e($mfid); ?>" data-year="<?php echo e($year); ?>" data-month="<?php echo e($month); ?>">
                </td>

                
                <td class="px-6 py-2 text-sm">
                  <input type="date" class="w-44 border border-gray-300 rounded-md px-2 py-1 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                         value="<?php echo e($row->completed_date ?? ''); ?>" <?php echo e($editingEnabled ? '' : 'disabled'); ?>

                         data-upsert data-section="<?php echo e($activeTab); ?>" data-field="completed_date"
                         data-master="<?php echo e($mfid); ?>" data-year="<?php echo e($year); ?>" data-month="<?php echo e($month); ?>">
                </td>

                
                <td class="px-6 py-2 text-sm">
                  <input type="text" class="w-64 border border-gray-300 rounded-md px-2 py-1 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                         value="<?php echo e($row->remarks ?? ''); ?>" <?php echo e($editingEnabled ? '' : 'disabled'); ?>

                         data-upsert data-section="<?php echo e($activeTab); ?>" data-field="remarks"
                         data-master="<?php echo e($mfid); ?>" data-year="<?php echo e($year); ?>" data-month="<?php echo e($month); ?>">
                </td>
              </tr>

            <?php elseif($activeTab === 'valueadd'): ?>
              <tr class="hover:bg-gray-50 transition-colors duration-150" data-row-master="<?php echo e($mfid); ?>">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo e($index + 1); ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo e($row->company ?? ''); ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo e($row->client ?? ''); ?></td>

                
                <td class="px-6 py-2 text-sm">
                  <input type="number" min="0" class="w-28 border border-gray-300 rounded-md px-2 py-1 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                         value="<?php echo e($row->quota ?? ''); ?>" <?php echo e($editingEnabled ? '' : 'disabled'); ?>

                         data-upsert data-section="<?php echo e($activeTab); ?>" data-field="quota"
                         data-master="<?php echo e($mfid); ?>" data-year="<?php echo e($year); ?>" data-month="<?php echo e($month); ?>">
                </td>

                
                <td class="px-6 py-2 text-sm">
                  <input type="number" min="0" class="w-28 border border-gray-300 rounded-md px-2 py-1 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                         value="<?php echo e($row->completed ?? ''); ?>" <?php echo e($editingEnabled ? '' : 'disabled'); ?>

                         data-upsert data-section="<?php echo e($activeTab); ?>" data-field="completed"
                         data-master="<?php echo e($mfid); ?>" data-year="<?php echo e($year); ?>" data-month="<?php echo e($month); ?>">
                </td>

                
                <td class="px-6 py-2 text-sm">
                  <input type="text" class="w-64 border border-gray-300 rounded-md px-2 py-1 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                         value="<?php echo e($row->remarks ?? ''); ?>" <?php echo e($editingEnabled ? '' : 'disabled'); ?>

                         data-upsert data-section="<?php echo e($activeTab); ?>" data-field="remarks"
                         data-master="<?php echo e($mfid); ?>" data-year="<?php echo e($year); ?>" data-month="<?php echo e($month); ?>">
                </td>
              </tr>
            <?php endif; ?>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
              <td colspan="<?php echo e(count($headers)); ?>" class="px-6 py-12 text-center">
                <div class="text-gray-400">
                  <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                  <h3 class="mt-2 text-sm font-medium text-gray-900">No data available</h3>
                  <p class="mt-1 text-sm text-gray-500">
                    No data for the selected filters. Try selecting a different month/year or add new data.
                  </p>
                </div>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <div class="text-center py-12">
      <div class="text-gray-400">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Invalid tab</h3>
        <p class="mt-1 text-sm text-gray-500">The selected tab is not configured properly.</p>
      </div>
    </div>
  <?php endif; ?>
</div>
<?php /**PATH D:\Projects\Laravel\KL_tracker\resources\views\coordinators\partials\_tab_table.blade.php ENDPATH**/ ?>