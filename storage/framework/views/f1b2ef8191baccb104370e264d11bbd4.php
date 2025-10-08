<?php $__env->startSection('content'); ?>
<div class="w-full max-w-7xl mx-auto px-4 py-6">
    <div class="mb-4 flex flex-wrap items-center gap-3">
        <h1 class="text-2xl font-semibold">Information Booth Calendar</h1>

        
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

<!-- Event Details Modal -->
<div id="calendarModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-xl w-[500px] max-w-[90vw]">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Event Details</h3>
            <button onclick="document.getElementById('calendarModal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 text-xl">
                ×
            </button>
        </div>
        <div id="calendarModalBody" class="text-gray-700 space-y-2"></div>
        <div class="mt-6 flex justify-end">
            <button onclick="document.getElementById('calendarModal').classList.add('hidden')"
                    class="px-4 py-2 bg-[#22255b] text-white rounded-lg hover:bg-[#1a1d4a]">
                Close
            </button>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet" />
<style>
    /* Custom styles for better event visibility */
    .fc-event {
        border: none !important;
        font-weight: 500 !important;
        border-radius: 4px !important;
        padding: 2px 4px !important;
    }

    .fc-event-title {
        font-weight: 600 !important;
    }

    /* Hover effect for events */
    .fc-event:hover {
        opacity: 0.9 !important;
        cursor: pointer !important;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.15) !important;
        transition: all 0.2s ease !important;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
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

    // Helper to format dates nicely
    function formatDate(dateStr) {
        if (!dateStr) return '—';
        const date = new Date(dateStr);
        return date.toLocaleDateString('en-GB', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
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
        events: function (fetchInfo, success, failure) {
            const url =
                "<?php echo e(route('information.booth.events')); ?>" +
                `?start=${fetchInfo.startStr}&end=${fetchInfo.endStr}` +
                currentQuery();
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(success)
                .catch(failure);
        },

        // Style events with colors and tooltips
        eventDidMount: function(info) {
            const p = info.event.extendedProps || {};
            const status = p.status;

            // Apply strong, readable colors based on status
            if (status === 'completed') {
                info.el.style.backgroundColor = '#22C55E'; // green-500
                info.el.style.color = 'white';
                info.el.style.borderLeft = '4px solid #16A34A';
            } else if (status === 'in-progress') {
                info.el.style.backgroundColor = '#3B82F6'; // blue-500
                info.el.style.color = 'white';
                info.el.style.borderLeft = '4px solid #2563EB';
            } else if (status === 'pending') {
                info.el.style.backgroundColor = '#F59E0B'; // amber-500
                info.el.style.color = '#1F2937'; // dark text for better readability
                info.el.style.borderLeft = '4px solid #D97706';
            } else if (status === 'cancelled') {
                info.el.style.backgroundColor = '#EF4444'; // red-500
                info.el.style.color = 'white';
                info.el.style.borderLeft = '4px solid #DC2626';
            } else {
                // Default styling for unknown status
                info.el.style.backgroundColor = '#6B7280'; // gray-500
                info.el.style.color = 'white';
                info.el.style.borderLeft = '4px solid #4B5563';
            }

            // Add tooltip for hover (keeping your original functionality)
            info.el.title =
                `${info.event.title}\n` +
                `Status: ${p.status ?? '—'}\n` +
                `Product: ${p.product ?? '—'}\n` +
                `Location: ${p.location ?? '—'}\n` +
                `Expected: ${formatDate(info.event.startStr)}`;
        },

        // Click event to show details modal
        eventClick: function(info) {
            info.jsEvent.preventDefault(); // Prevent navigation

            const p = info.event.extendedProps || {};

            // Create status badge
            let statusBadge = '';
            const status = p.status;
            if (status === 'completed') {
                statusBadge = '<span class="inline-block px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Completed</span>';
            } else if (status === 'in-progress') {
                statusBadge = '<span class="inline-block px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">In Progress</span>';
            } else if (status === 'pending') {
                statusBadge = '<span class="inline-block px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">Pending</span>';
            } else if (status === 'cancelled') {
                statusBadge = '<span class="inline-block px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">Cancelled</span>';
            } else {
                statusBadge = '<span class="inline-block px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">Unknown</span>';
            }

            const details = `
                <div class="space-y-3">
                    <div>
                        <h4 class="font-semibold text-lg text-gray-900">${info.event.title}</h4>
                    </div>

                    <div class="grid grid-cols-1 gap-3">
                        <div>
                            <span class="text-sm font-medium text-gray-500">Status:</span><br>
                            ${statusBadge}
                        </div>

                        <div>
                            <span class="text-sm font-medium text-gray-500">Product:</span><br>
                            <span class="text-sm text-gray-900">${p.product ?? '—'}</span>
                        </div>

                        <div>
                            <span class="text-sm font-medium text-gray-500">Client:</span><br>
                            <span class="text-sm text-gray-900">${p.client ?? '—'}</span>
                        </div>

                        <div>
                            <span class="text-sm font-medium text-gray-500">Location:</span><br>
                            <span class="text-sm text-gray-900">${p.location ?? '—'}</span>
                        </div>

                        <div>
                            <span class="text-sm font-medium text-gray-500">Expected Finish:</span><br>
                            <span class="text-sm text-gray-900">${formatDate(info.event.startStr)}</span>
                        </div>

                        ${p.overdue ? '<div class="p-2 bg-red-50 border border-red-200 rounded text-red-700 text-sm"><strong>⚠️ Overdue</strong></div>' : ''}
                    </div>
                </div>
            `;

            document.getElementById('calendarModalBody').innerHTML = details;
            document.getElementById('calendarModal').classList.remove('hidden');
        }
    });

    calendar.render();

    // Re-fetch on filter apply
    document.getElementById('applyFilters').addEventListener('click', function () {
        calendar.refetchEvents();
    });

    // Close modal when clicking outside
    document.getElementById('calendarModal').addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.getElementById('calendarModal').classList.add('hidden');
        }
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Gjlang\kl_guide_tracker\resources\views\information_booth\calendar.blade.php ENDPATH**/ ?>