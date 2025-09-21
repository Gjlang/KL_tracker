<?php $__env->startSection('head'); ?>
    
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<style>
/* Classic • Elegant • Minimal */
.paper { background-color:#F7F7F9; }
.surface { background:#FFF; }
.ink { color:#1C1E26; }
.hairline { border-color:#EAEAEA; }
.small-caps { letter-spacing:.06em; text-transform:uppercase; font-size:11px; font-weight:500; }
.floating-card { background:#FFF; border:1px solid #EAEAEA; border-radius:16px; box-shadow:0 1px 3px rgba(0,0,0,.04); }
</style>

<div class="paper min-h-screen py-6">
  <div class="mx-auto max-w-7xl px-4">
    <div class="mb-5 flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-semibold ink">Coordinator Calendar</h1>
        <p class="text-sm text-neutral-500">All coordinator dates across Outdoor • Media • KLTG</p>
      </div>
      <div class="text-sm">
        <a href="<?php echo e(route('dashboard')); ?>" class="text-[#22255b] hover:underline">← Back to Dashboard</a>
      </div>
    </div>

    
    <div class="floating-card p-4 mb-5">
      <form id="filters" class="grid grid-cols-1 md:grid-cols-5 gap-3">
        <div>
          <label class="small-caps text-neutral-600">Module</label>
          <select name="module" class="w-full border hairline rounded-md p-2">
            <option value="">All</option>
            <option value="outdoor">Outdoor</option>
            <option value="media">Media</option>
            <option value="kltg">KLTG</option>
          </select>
        </div>
        <div>
          <label class="small-caps text-neutral-600">Year</label>
          <input type="number" name="year" min="2000" max="2100" class="w-full border hairline rounded-md p-2" placeholder="e.g. 2025">
        </div>
        <div>
          <label class="small-caps text-neutral-600">Month</label>
          <input type="number" name="month" min="1" max="12" class="w-full border hairline rounded-md p-2" placeholder="1-12">
        </div>
        <div class="md:col-span-2">
          <label class="small-caps text-neutral-600">Company / Client</label>
          <input type="text" name="company" class="w-full border hairline rounded-md p-2" placeholder="Search company/client...">
        </div>
      </form>
    </div>

    <div class="floating-card p-3">
      <div id="coordinator-calendar"></div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const calendarEl = document.getElementById('coordinator-calendar');
  const filters = document.getElementById('filters');

  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    height: 'auto',
    firstDay: 1,
    timeZone: 'Asia/Kuala_Lumpur',
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek,listWeek'
    },
    events: {
      url: "<?php echo e(route('calendar.coordinators.events')); ?>",
      failure: function() {
        alert('Failed to load events.');
      },
      extraParams: function () {
        const fd = new FormData(filters);
        return Object.fromEntries(fd.entries());
      }
    },
    eventClick: function(info) {
      // Default: show a friendly alert with details (you can swap to a modal or deep-link later)
      const p = info.event.extendedProps || {};
      const lines = [
        `Title: ${info.event.title}`,
        `Module: ${p.module || '-'}`,
        `Milestone: ${p.milestone || '-'}`,
        `Company: ${p.company || '-'}`,
        `Date: ${info.event.startStr || '-'}`
      ];
      alert(lines.join('\n'));
    },
  });

  calendar.render();

  // Re-fetch when filters change
  filters.addEventListener('change', () => calendar.refetchEvents());
  filters.addEventListener('keyup',  () => calendar.refetchEvents()); // for text input typing
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Gjlang\kl_guide_tracker\resources\views/calendar/coordinators.blade.php ENDPATH**/ ?>