
<div id="ib-calendar" class="w-full"></div>

<?php $__env->startPush('head'); ?>
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
<style>
  /* Enhanced badges with stronger colors */
  .ib-badge {
    display: inline-flex;
    align-items: center;
    gap: .375rem;
    font-size: .7rem;
    font-weight: 700;
    padding: .25rem .5rem;
    border-radius: .5rem;
    border: 2px solid transparent;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .ib-badge--delay {
    color: #FFFFFF;
    background: #DC2626;
    border-color: #B91C1C;
    box-shadow: 0 2px 4px rgba(220, 38, 38, 0.3);
  }

  .ib-badge--done {
    color: #FFFFFF;
    background: #16A34A;
    border-color: #15803D;
    box-shadow: 0 2px 4px rgba(22, 163, 74, 0.3);
  }

  .ib-badge--progress {
    color: #FFFFFF;
    background: #2563EB;
    border-color: #1D4ED8;
    box-shadow: 0 2px 4px rgba(37, 99, 235, 0.3);
  }

  /* Strong, clear event colors */
  .fc-event.ib-evt--done {
    background: #16A34A !important;
    border-color: #15803D !important;
    color: #FFFFFF !important;
    font-weight: 600 !important;
    box-shadow: 0 2px 8px rgba(22, 163, 74, 0.4) !important;
  }

  .fc-event.ib-evt--overdue {
    background: #DC2626 !important;
    border-color: #B91C1C !important;
    color: #FFFFFF !important;
    font-weight: 600 !important;
    box-shadow: 0 2px 8px rgba(220, 38, 38, 0.4) !important;
    animation: pulse-red 2s infinite;
  }

  .fc-event.ib-evt--progress {
    background: #2563EB !important;
    border-color: #1D4ED8 !important;
    color: #FFFFFF !important;
    font-weight: 600 !important;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.4) !important;
  }

  .fc-event.ib-evt--normal {
    background: #F59E0B !important;
    border-color: #D97706 !important;
    color: #FFFFFF !important;
    font-weight: 600 !important;
    box-shadow: 0 2px 8px rgba(245, 158, 11, 0.4) !important;
  }

  /* Pulse animation for overdue items */
  @keyframes pulse-red {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.8; }
  }

  /* Enhanced event styling */
  .fc-event {
    border-radius: 8px !important;
    border-width: 2px !important;
    padding: 6px !important;
    margin: 2px !important;
    min-height: 35px !important;
  }

  /* Hover effects */
  .fc-event:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2) !important;
    transition: all 0.3s ease !important;
    cursor: pointer !important;
  }

  /* Calendar styling */
  .fc .fc-toolbar-title {
    font-family: ui-serif, 'Times New Roman', serif;
    font-weight: 600;
    letter-spacing: .2px;
    font-size: 1.5rem;
    color: #1F2937;
  }

  .fc .fc-button {
    border-radius: .6rem;
    font-weight: 600;
    border: 2px solid;
    padding: 8px 16px;
    transition: all 0.2s ease;
  }

  .fc .fc-button:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
  }

  /* Day grid styling */
  .fc-daygrid-day {
    border: 1px solid #E5E7EB !important;
  }

  .fc-daygrid-day:hover {
    background-color: #F9FAFB !important;
  }

  /* Today highlight */
  .fc-day-today {
    background-color: #EBF8FF !important;
    border-color: #3B82F6 !important;
  }

  /* Weekend styling */
  .fc-day-sun, .fc-day-sat {
    background-color: #FAFAFA !important;
  }
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
          'expected_finish_date' => optional($r->expected_finish_date)->format('Y-m-d'),
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
    if (!row.expected_finish_date) return false;
    const d = new Date(row.expected_finish_date + 'T00:00:00');
    return (d < today) && (row.status !== 'done' && row.status !== 'cancelled');
  }

  function eventClass(row) {
    if (row.status === 'done') return 'ib-evt--done';
    if (isOverdue(row)) return 'ib-evt--overdue';
    if (row.status === 'in-progress') return 'ib-evt--progress';
    return 'ib-evt--normal';
  }

  const events = rows
    .filter(r => !!r.expected_finish_date)
    .map(r => ({
      id: String(r.id),
      title: `${r.company ?? ''} — ${r.product ?? ''}`,
      start: r.expected_finish_date,
      allDay: true,
      extendedProps: {
        status: r.status,
        overdue: isOverdue(r),
        subtitle: [r.client, r.location].filter(Boolean).join(' • '),
        company: r.company,
        product: r.product,
        client: r.client,
        location: r.location
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
      root.style.gap = '4px';
      root.style.padding = '2px';

      const ttl = document.createElement('div');
      ttl.textContent = arg.event.title;
      ttl.style.fontSize = '.85rem';
      ttl.style.fontWeight = '700';
      ttl.style.whiteSpace = 'nowrap';
      ttl.style.overflow = 'hidden';
      ttl.style.textOverflow = 'ellipsis';
      ttl.style.lineHeight = '1.2';

      const sub = document.createElement('div');
      sub.textContent = subtitle || '';
      sub.style.fontSize = '.72rem';
      sub.style.opacity = '0.95';
      sub.style.whiteSpace = 'nowrap';
      sub.style.overflow = 'hidden';
      sub.style.textOverflow = 'ellipsis';
      sub.style.fontWeight = '500';

      const row = document.createElement('div');
      row.style.display = 'flex';
      row.style.gap = '6px';
      row.style.alignItems = 'center';
      row.style.marginTop = '2px';

      if (overdue) {
        const b = document.createElement('span');
        b.className = 'ib-badge ib-badge--delay';
        b.textContent = '⚠ OVERDUE';
        row.appendChild(b);
      } else if (status === 'done') {
        const b = document.createElement('span');
        b.className = 'ib-badge ib-badge--done';
        b.textContent = '✓ COMPLETED';
        row.appendChild(b);
      } else if (status === 'in-progress') {
        const b = document.createElement('span');
        b.className = 'ib-badge ib-badge--progress';
        b.textContent = '⚡ IN PROGRESS';
        row.appendChild(b);
      }

      root.appendChild(ttl);
      if (subtitle) root.appendChild(sub);
      root.appendChild(row);

      return { domNodes: [root] };
    },

    eventDidMount(info) {
      const { status, overdue, company, product, client, location } = info.event.extendedProps;
      const ef = info.event.start;
      const efStr = ef ? ef.toLocaleDateString('en-GB') : '-';

      let statusText = status || 'pending';
      if (overdue) statusText += ' (OVERDUE!)';

      const tooltip = [
        `${company || ''} — ${product || ''}`,
        `Expected Finish: ${efStr}`,
        `Status: ${statusText}`,
        client ? `Client: ${client}` : '',
        location ? `Location: ${location}` : ''
      ].filter(Boolean).join('\n');

      info.el.title = tooltip;

      // Add click handler for event details
      info.el.addEventListener('click', function(e) {
        e.preventDefault();
        showEventDetails(info.event.extendedProps, ef);
      });
    }
  });

  calendar.render();

  // Event details modal function
  function showEventDetails(props, date) {
    const { status, overdue, company, product, client, location } = props;
    const dateStr = date ? date.toLocaleDateString('en-GB') : '-';

    let statusBadge = '';
    if (overdue) {
      statusBadge = '<span class="ib-badge ib-badge--delay">⚠ OVERDUE</span>';
    } else if (status === 'done') {
      statusBadge = '<span class="ib-badge ib-badge--done">✓ COMPLETED</span>';
    } else if (status === 'in-progress') {
      statusBadge = '<span class="ib-badge ib-badge--progress">⚡ IN PROGRESS</span>';
    } else {
      statusBadge = '<span class="ib-badge" style="background:#F59E0B; color:white;">📋 PENDING</span>';
    }

    const details = `
      <div style="background:white; padding:20px; border-radius:12px; box-shadow:0 10px 25px rgba(0,0,0,0.2); max-width:400px; margin:auto;">
        <h3 style="margin:0 0 15px 0; color:#1F2937; font-size:1.1rem; font-weight:700;">${company || ''} — ${product || ''}</h3>

        <div style="margin-bottom:10px;">${statusBadge}</div>

        <div style="color:#4B5563; line-height:1.6; font-size:0.9rem;">
          <div style="margin:8px 0;"><strong>Expected Finish:</strong> ${dateStr}</div>
          ${client ? `<div style="margin:8px 0;"><strong>Client:</strong> ${client}</div>` : ''}
          ${location ? `<div style="margin:8px 0;"><strong>Location:</strong> ${location}</div>` : ''}
        </div>

        <button onclick="this.closest('.event-modal').remove()"
                style="margin-top:15px; background:#3B82F6; color:white; border:none; padding:8px 16px; border-radius:6px; cursor:pointer; font-weight:600;">
          Close
        </button>
      </div>
    `;

    // Remove existing modal if any
    const existingModal = document.querySelector('.event-modal');
    if (existingModal) existingModal.remove();

    // Create new modal
    const modal = document.createElement('div');
    modal.className = 'event-modal';
    modal.style.cssText = 'position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); display:flex; align-items:center; justify-content:center; z-index:9999;';
    modal.innerHTML = details;

    modal.addEventListener('click', function(e) {
      if (e.target === modal) modal.remove();
    });

    document.body.appendChild(modal);
  }
})();
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH D:\Projects\Laravel\KL_tracker\resources\views\calendar\_fullcalendar_embed.blade.php ENDPATH**/ ?>