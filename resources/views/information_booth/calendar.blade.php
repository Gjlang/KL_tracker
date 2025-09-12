@extends('layouts.app')

@section('content')
<div class="w-full max-w-7xl mx-auto px-4 py-6">
    <div class="mb-4 flex flex-wrap items-center gap-3">
        <h1 class="text-2xl font-semibold">Information Booth Calendar</h1>

        {{-- Optional: quick filters --}}
        <form id="filters" class="ml-auto flex items-center gap-2">
            <input type="text" name="company" placeholder="Filter company"
                   class="border rounded-md px-3 py-1 text-sm">
            <select name="status" class="border rounded-md px-3 py-1 text-sm">
                <option value="">All statuses</option>
                <option value="pending">Pending</option>
                <option value="in-progress">In Progress</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
            <button type="button" id="applyFilters"
                    class="px-3 py-1 rounded-md border text-sm bg-white hover:bg-neutral-50">
                Apply
            </button>
        </form>
    </div>

    <div id="calendar" class="bg-white rounded-2xl border border-neutral-200/70 shadow-sm p-3"></div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');

    // Helper to get current filters
    function currentQuery() {
        const form = document.getElementById('filters');
        const params = new URLSearchParams(new FormData(form));
        return params.toString() ? '&' + params.toString() : '';
    }

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek'
        },
        height: 'auto',
        dayMaxEventRows: true,
        navLinks: true,
        fixedWeekCount: false,
        firstDay: 1, // Monday (like many calendar apps; set 0 for Sunday)
        eventDisplay: 'block',
        eventTimeFormat: { hour: '2-digit', minute: '2-digit', meridiem: false },

        // Load events (with server-side date range)
        events: function(fetchInfo, success, failure) {
            const url = `{{ route('information.booth.events') }}?start=${fetchInfo.startStr}&end=${fetchInfo.endStr}` + currentQuery();
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(data => success(data))
                .catch(err => failure(err));
        },

        // Tooltips (simple title)
        eventDidMount: function(info) {
            const p = info.event.extendedProps || {};
            const dateFmt = (d) => {
                if (!d) return '—';
                const [y,m,dd] = d.split('-');
                return `${dd}/${m}/${y}`; // d/m/y as you wanted
            };
            info.el.title =
                `${info.event.title}\n` +
                `Status: ${p.status ?? '—'}\n` +
                `Product: ${p.product ?? '—'}\n` +
                `Location: ${p.location ?? '—'}\n` +
                `Expected: ${dateFmt(p.expected)}`;
        },

        // Click navigates to your page (url set by controller)
        eventClick: function(info) {
            if (info.event.url) {
                // allow middle-click etc.
                window.open(info.event.url, '_blank');
                info.jsEvent.preventDefault();
            }
        }
    });

    calendar.render();

    // Re-fetch on filter apply
    document.getElementById('applyFilters').addEventListener('click', function () {
        calendar.refetchEvents();
    });
});
</script>
@endpush
