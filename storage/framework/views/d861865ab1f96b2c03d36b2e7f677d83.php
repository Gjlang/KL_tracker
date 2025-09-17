
<div id="ib-calendar" class="w-full"></div>

<?php $__env->startPush('head'); ?>
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
<style>
  .ib-badge { display:inline-flex; align-items:center; gap:.375rem; font-size:.65rem; font-weight:600; padding:.15rem .4rem; border-radius:.5rem; border:1px solid transparent; }
  .ib-badge--delay { color:#b42318; background:#fee4e2; border-color:#fcd5ce; }
  .ib-badge--done  { color:#05603a; background:#d1fadf; border-color:#a6f4c5; }

  .fc-event.ib-evt--done    { background:#d1fadf !important; border-color:#a6f4c5 !important; color:#064e3b !important; }
  .fc-event.ib-evt--overdue { background:#fee4e2 !important; border-color:#fcd5ce !important; color:#7a271a !important; }
  .fc-event.ib-evt--normal  { background:#fef3c7 !important; border-color:#fde68a !important; color:#78350f !important; }

  .fc .fc-toolbar-title { font-family:ui-serif, 'Times New Roman', serif; font-weight:600; letter-spacing:.2px; }
  .fc .fc-button { border-radius:.6rem; }
</style>
<?php $__env->stopPush(); ?>

<?php
  // ---- Bangun data aman untuk di-JSON-kan (tanpa collect()->map())
  $__rows = [];
  foreach (($feeds ?? []) as $r) {
      $__rows[] = [
          'id'              => $r->id ?? null,
          'company'         => $r->company ?? null,
          'product'         => $r->product ?? null,
          'client'          => $r->client ?? null,
          'location'        => $r->location ?? null,
          'status'          => $r->status ?? null, // pending|in-progress|done|cancelled
          'expected_finish' => optional($r->expected_finish)->format('Y-m-d'),
      ];
  }
?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script>
(function() {
  // Data dari PHP → JS (pakai json_encode agar Blade gak bingung bracket [])
  const rows = <?php echo json_encode($__rows, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); ?>;

  const today = new Date(); today.setHours(0,0,0,0);

  function isOverdue(row) {
    if (!row.expected_finish) return false;
    const d = new Date(row.expected_finish + 'T00:00:00');
    return (d < today) && (row.status !== 'done' && row.status !== 'cancelled');
  }

  function eventClass(row) {
    if (row.status === 'done') return 'ib-evt--done';
    if (isOverdue(row)) return 'ib-evt--overdue';
    return 'ib-evt--normal';
  }

  const events = rows
    .filter(r => !!r.expected_finish)
    .map(r => ({
      id: String(r.id),
      title: `${r.company ?? ''} — ${r.product ?? ''}`,
      start: r.expected_finish,
      allDay: true,
      extendedProps: {
        status: r.status,
        overdue: isOverdue(r),
        subtitle: [r.client, r.location].filter(Boolean).join(' • ')
      },
      classNames: [eventClass(r)]
    }));

  const calendarEl = document.getElementById('ib-calendar');
  if (!calendarEl) return;

  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    height: 'auto',
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
    },
    navLinks: true,
    weekNumbers: false,
    firstDay: 0,
    events,

    eventContent(arg) {
      const { status, overdue, subtitle } = arg.event.extendedProps;
      const root = document.createElement('div');
      root.style.display = 'grid';
      root.style.gap = '2px';

      const ttl = document.createElement('div');
      ttl.textContent = arg.event.title;
      ttl.style.fontSize = '.8rem';
      ttl.style.fontWeight = 600;
      ttl.style.whiteSpace = 'nowrap';
      ttl.style.overflow = 'hidden';
      ttl.style.textOverflow = 'ellipsis';

      const sub = document.createElement('div');
      sub.textContent = subtitle || '';
      sub.style.fontSize = '.7rem';
      sub.style.opacity = .9;
      sub.style.whiteSpace = 'nowrap';
      sub.style.overflow = 'hidden';
      sub.style.textOverflow = 'ellipsis';

      const row = document.createElement('div');
      row.style.display = 'flex';
      row.style.gap = '6px';
      row.style.alignItems = 'center';

      if (overdue) {
        const b = document.createElement('span');
        b.className = 'ib-badge ib-badge--delay';
        b.textContent = 'DELAY';
        row.appendChild(b);
      } else if (status === 'done') {
        const b = document.createElement('span');
        b.className = 'ib-badge ib-badge--done';
        b.textContent = 'Done';
        row.appendChild(b);
      }

      root.appendChild(ttl);
      if (subtitle) root.appendChild(sub);
      root.appendChild(row);

      return { domNodes: [root] };
    },

    eventDidMount(info) {
      const { status, overdue } = info.event.extendedProps;
      const ef = info.event.start;
      const efStr = ef ? ef.toLocaleDateString() : '-';
      const note = overdue ? ' (DELAY)' : (status==='done' ? ' (Done)' : '');
      info.el.title = `${info.event.title}\nExpected Finish: ${efStr}\nStatus: ${status}${note}`;
    }
  });

  calendar.render();
})();
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\Users\Gjlang\kl_guide_tracker\resources\views\calendar\_fullcalendar_embed.blade.php ENDPATH**/ ?>