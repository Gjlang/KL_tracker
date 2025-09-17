<?php
$map = [
  'Pending'     => 'bg-amber-100 text-amber-800',
  'In Progress' => 'bg-blue-100 text-blue-800',
  'completed'        => 'bg-emerald-100 text-emerald-800',
  'Cancelled'   => 'bg-rose-100 text-rose-800',
];
$cls = $map[$status] ?? 'bg-neutral-100 text-neutral-800';
?>
<span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium <?php echo e($cls); ?>">
  <?php echo e($status); ?>

</span>
<?php /**PATH C:\Users\Gjlang\kl_guide_tracker\resources\views\information_booth\_status_badge.blade.php ENDPATH**/ ?>