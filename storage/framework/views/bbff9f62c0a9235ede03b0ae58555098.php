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

/* Button styles for modal */
.primary-btn {
  @apply bg-[#22255b] text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-[#1a1d4a] transition-colors;
}
.secondary-btn {
  @apply bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors;
}
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

<!-- Event Detail Modal -->
<div id="eventModal" class="fixed inset-0 z-50 hidden">
  <!-- overlay -->
  <div class="absolute inset-0 bg-black/40" data-close="true"></div>

  <!-- card -->
  <div class="relative mx-auto mt-16 w-[92%] max-w-xl rounded-2xl bg-white shadow-2xl ring-1 ring-black/5">
    <div class="flex items-start justify-between p-5 border-b border-neutral-200/70">
      <div>
        <h3 class="text-lg font-semibold ink">Job Details</h3>
        <div class="mt-1 text-xs text-neutral-500 flex items-center gap-2">
          <span id="modal-module-dot" class="inline-block w-2.5 h-2.5 rounded-full"></span>
          <span id="modal-module">—</span>
        </div>
      </div>
      <button class="rounded-full border border-neutral-300 w-9 h-9 grid place-content-center text-neutral-600 hover:bg-neutral-100"
              aria-label="Close" data-close="true">✕</button>
    </div>

    <div class="p-5 space-y-4">
      <div>
        <div class="small-caps text-neutral-600">Company</div>
        <div id="modal-company" class="font-medium ink">—</div>
      </div>
      <div>
        <div class="small-caps text-neutral-600">Title</div>
        <div id="modal-title" class="font-medium ink">—</div>
      </div>
      <div id="row-site" class="hidden">
        <div class="small-caps text-neutral-600">Site</div>
        <div id="modal-site" class="ink">—</div>
      </div>
      <div>
        <div class="small-caps text-neutral-600">Milestone</div>
        <span id="modal-milestone" class="inline-flex px-2 py-1 rounded-full text-xs bg-amber-100 text-amber-800">—</span>
      </div>
      <div>
        <div class="small-caps text-neutral-600">Date</div>
        <div id="modal-date" class="font-medium ink">—</div>
      </div>
      <div id="row-remarks" class="hidden">
        <div class="small-caps text-neutral-600">Notes</div>
        <div id="modal-remarks" class="ink">—</div>
      </div>
    </div>

    <div class="flex items-center justify-end gap-2 p-5 border-t border-neutral-200/70">
      <a id="modal-link" href="#" target="_blank"
         class="hidden primary-btn">Open Detail</a>
      <button class="secondary-btn" data-close="true">Close</button>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const calendarEl = document.getElementById('coordinator-calendar');
  const filters = document.getElementById('filters');

  const COLORS = {
    outdoor: '#22255b',
    media:   '#4bbbed',
    kltg:    '#d33831'
  };

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
      failure: function() { alert('Failed to load events.'); },
      extraParams: function () {
        const fd = new FormData(filters);
        return Object.fromEntries(fd.entries());
      }
    },
    eventClick: function(info) {
      info.jsEvent?.preventDefault();
      const p = info.event.extendedProps || {};

      // Fill modal
      setText('#modal-title',  p.title_raw || safe(info.event.title));
      setText('#modal-company', p.company || '—');
      setText('#modal-milestone', p.milestone || '—');
      setText('#modal-module',  (p.module || '—').toUpperCase());
      setText('#modal-date',    formatDate(info.event.start));

      // Optional fields
      toggleRow('#row-site', !!p.site);
      setText('#modal-site', p.site || '');

      const remarks = (p.objective_raw || '').toString().trim();
      toggleRow('#row-remarks', !!remarks);
      setText('#modal-remarks', remarks);

      // Module dot color
      const dot = document.querySelector('#modal-module-dot');
      dot.style.backgroundColor = COLORS[p.module] || '#CBD5E1';

      // Optional deep-link if you add `url` to events later
      const link = document.querySelector('#modal-link');
      if (p.url) {
        link.href = p.url;
        link.classList.remove('hidden');
      } else {
        link.classList.add('hidden');
      }

      openModal();
    },
  });

  calendar.render();

  // Re-fetch when filters change
  filters.addEventListener('change', () => calendar.refetchEvents());
  filters.addEventListener('keyup',  () => calendar.refetchEvents()); // for text input typing

  // ===== Modal helpers =====
  const modal = document.getElementById('eventModal');

  function openModal(){ modal.classList.remove('hidden'); }
  function closeModal(){ modal.classList.add('hidden'); }

  modal.addEventListener('click', (e) => {
    if (e.target.matches('[data-close]')) closeModal();
  });
  document.addEventListener('keydown', (e)=>{
    if (e.key === 'Escape') closeModal();
  });

  function setText(sel, value){
    const el = document.querySelector(sel);
    if (el) el.textContent = value ?? '';
  }
  function toggleRow(sel, show){
    const el = document.querySelector(sel);
    if (!el) return;
    el.classList.toggle('hidden', !show);
  }
  function safe(t){ return (t || '').toString(); }
  function formatDate(d){
    try {
      return new Intl.DateTimeFormat('en-MY', { year:'numeric', month:'numeric', day:'numeric' }).format(d);
    } catch { return (d?.toISOString?.() || '').substring(0,10); }
  }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Gjlang\kl_guide_tracker\resources\views\calendar\coordinators.blade.php ENDPATH**/ ?>