@push('styles')
  {{-- FullCalendar v5 (global build) --}}
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
@endpush

<div id="ibCalendar"
     data-events-url="{{ route('information.booth.calendar.events') }}"
     data-status="{{ request('status') }}"
     data-client="{{ request('client') }}"
     class="rounded-xl border border-neutral-200 overflow-hidden bg-white">
</div>

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const el = document.getElementById('ibCalendar');
      const eventsUrl = el.dataset.eventsUrl;
      const qStatus   = el.dataset.status || '';
      const qClient   = el.dataset.client || '';

      const calendar = new FullCalendar.Calendar(el, {
        height: 'auto',
        timeZone: 'Asia/Kuala_Lumpur',
        initialView: 'dayGridMonth',
        nowIndicator: true,
        navLinks: true,

        headerToolbar: {
          left:   'prev,next today',
          center: 'title',
          right:  'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },

        // Fetch your events with current filters
        events: function(fetchInfo, success, failure) {
          const params = new URLSearchParams({
            start: fetchInfo.startStr,
            end:   fetchInfo.endStr,
          });
          if (qStatus) params.set('status', qStatus);
          if (qClient) params.set('client', qClient);

          fetch(`${eventsUrl}?${params.toString()}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
          })
          .then(r => r.json())
          .then(data => success(data))
          .catch(err => failure(err));
        },

        // UX like Google Calendar
        selectable: true,
        selectMirror: true,
        selectAllow: (info) => true,

        // Click empty slot → go to create with date prefilled
        select: function(info) {
          const d = info.startStr; // YYYY-MM-DD (all-day)
          window.location.href = "{{ route('information.booth.create') }}" + `?date=${d}`;
        },

        // Click event → go to your existing edit page (provided in 'url')
        eventClick: function(info) {
          if (info.event.url) {
            info.jsEvent.preventDefault();
            window.location.href = info.event.url;
          }
        },

        // Drag/drop & resize → PATCH to update
        editable: true,
        eventDrop: handleMove,
        eventResize: handleMove,

        // Visual polish
        dayMaxEvents: true,
        eventDisplay: 'block',
      });

      calendar.render();

      // Allow filters (if you have a form) to refresh events
      const filtersForm = document.querySelector('#filtersForm');
      if (filtersForm) {
        filtersForm.addEventListener('change', () => calendar.refetchEvents());
        filtersForm.addEventListener('submit', (e) => {
          e.preventDefault();
          calendar.refetchEvents();
        });
      }

      function handleMove(info) {
        const id    = info.event.id;
        const start = info.event.startStr;
        const end   = info.event.endStr; // exclusive

        fetch("{{ route('information.booth.calendar.move', ':id') }}".replace(':id', id), {
          method: 'PATCH',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
          },
          body: JSON.stringify({ start, end })
        })
        .then(r => {
          if (!r.ok) throw new Error('Failed to update');
          return r.json();
        })
        .catch(err => {
          console.error(err);
          info.revert(); // roll back if server fails
        });
      }
    });
  </script>
@endpush
