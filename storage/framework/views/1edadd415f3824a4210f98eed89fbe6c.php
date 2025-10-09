<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billboard Booking Calendar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
        :root {
            --brand-dark: #22255b;
            --brand-red: #d33831;
            --brand-sky: #4bbbed;
        }

        /* Custom FullCalendar Styling */
        .fc {
            font-family: inherit;
        }

        .fc-theme-standard .fc-scrollgrid {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
        }

        .fc-theme-standard th {
            background-color: #f8fafc;
            border-color: #e5e7eb;
            font-weight: 600;
            color: #475569;
            padding: 12px 8px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .fc-theme-standard td {
            border-color: #f1f5f9;
            vertical-align: top;
            position: relative;
        }

        .fc-daygrid-day {
            min-height: 120px;
        }

        .fc-daygrid-day-number {
            position: absolute;
            top: 8px;
            right: 8px;
            font-weight: 500;
            color: #64748b;
            font-size: 13px;
            z-index: 2;
        }

        .fc-day-today {
            background-color: #eff6ff !important;
        }

        .fc-day-today .fc-daygrid-day-number {
            background: var(--brand-dark);
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 0 2px white, 0 0 0 4px var(--brand-dark);
        }

        .fc-day-sat, .fc-day-sun {
            background-color: #f8fafc;
        }

        .fc-day-other .fc-daygrid-day-number {
            opacity: 0.5;
        }

        .fc-event {
            border: none !important;
            border-radius: 9999px !important;
            font-size: 11px !important;
            font-weight: 500 !important;
            margin: 1px 6px 2px 6px !important;
            padding: 2px 8px !important;
            cursor: pointer !important;
            box-shadow: none !important;
            position: relative !important;
        }

        .fc-event:hover {
            opacity: 0.8;
            transform: translateY(-1px);
        }

        .fc-event-title {
            font-weight: 500 !important;
            line-height: 1.2 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        .fc-event-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            margin-right: 6px;
            flex-shrink: 0;
        }

        /* Category Colors - High Contrast */
        .event-master {
            background-color: #6b7280 !important;
            color: white !important;
            border: 1px solid #4b5563 !important;
        }

        .event-kltg {
            background-color: var(--brand-dark) !important;
            color: white !important;
            border: 1px solid #1a1d4a !important;
        }

        .event-media {
            background-color: #7b00ff !important;
            color: white !important;
            border: 1px solid #7b00ff !important;
        }

        .event-outdoor {
            background-color: #00d0ff !important;
            color: white !important;
            border: 1px solid #00d0ff !important;
        }

        .fc-daygrid-more-link {
            font-size: 10px !important;
            font-weight: 600 !important;
            color: #64748b !important;
            padding: 2px 8px !important;
            margin: 1px 6px !important;
            background: #f1f5f9 !important;
            border-radius: 9999px !important;
            text-decoration: none !important;
            border: 1px solid #e2e8f0 !important;
        }

        .fc-daygrid-more-link:hover {
            background: #e2e8f0 !important;
            color: #475569 !important;
        }

        .fc-toolbar {
            display: none !important; /* Hide default toolbar */
        }

        /* Year View Grid */
        .year-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .year-month {
            background: white;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            padding: 16px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .year-month:hover {
            border-color: var(--brand-dark);
            box-shadow: 0 4px 12px rgba(34, 37, 91, 0.1);
        }

        .year-month-header {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 12px;
            font-size: 14px;
        }

        .year-mini-calendar {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 2px;
        }

        .year-day-header {
            font-size: 10px;
            font-weight: 500;
            color: #64748b;
            text-align: center;
            padding: 4px 0;
            text-transform: uppercase;
        }

        .year-day {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            color: #64748b;
            position: relative;
        }

        .year-day.has-events::after {
            content: '';
            position: absolute;
            bottom: 2px;
            left: 50%;
            transform: translateX(-50%);
            width: 3px;
            height: 3px;
            background: var(--brand-sky);
            border-radius: 50%;
        }

        .year-day.today {
            background: var(--brand-dark);
            color: white;
            border-radius: 50%;
            font-weight: 600;
        }

        /* Loading Skeleton */
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* Popover */
        .popover {
            position: absolute;
            z-index: 1000;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            padding: 12px;
            min-width: 200px;
            max-height: 200px;
            overflow-y: auto;
        }

        .hidden {
            display: none !important;
        }

        /* Search highlight */
        .search-highlight {
            background-color: #fef3c7;
        }

        @media (max-width: 768px) {
            .year-grid {
                grid-template-columns: 1fr;
                gap: 16px;
                padding: 16px;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Sticky Header -->
    <div class="sticky top-0 z-40 bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Left: Navigation -->
                <div class="flex items-center space-x-4">
                    <button id="prevBtn" class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors" aria-label="Previous">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                    <button id="todayBtn" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Today
                    </button>
                    <button id="nextBtn" class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors" aria-label="Next">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>

                <!-- Center: Title -->
                <h1 id="calendarTitle" class="text-xl font-semibold text-gray-900">
                    Calendar Loading...
                </h1>

                <!-- Right: Controls -->
                <div class="flex items-center space-x-3">
                    <!-- Search -->
                    <div class="relative">
                        <input
                            type="text"
                            id="searchInput"
                            placeholder="Search events..."
                            class="pl-8 pr-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                        <svg class="absolute left-2.5 top-2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>

                    <!-- View Switch -->
                    <div class="flex bg-gray-100 rounded-lg p-1">
                        <button class="view-btn active px-3 py-1.5 text-xs font-medium rounded-md transition-colors" data-view="dayGridMonth" aria-pressed="true">Month</button>
                        <button class="view-btn px-3 py-1.5 text-xs font-medium rounded-md transition-colors" data-view="timeGridWeek" aria-pressed="false">Week</button>
                        <button class="view-btn px-3 py-1.5 text-xs font-medium rounded-md transition-colors" data-view="timeGridDay" aria-pressed="false">Day</button>
                        <button class="view-btn px-3 py-1.5 text-xs font-medium rounded-md transition-colors" data-view="year" aria-pressed="false">Year</button>
                    </div>

                    <!-- Back to Dashboard -->
                    <button onclick="window.history.back()" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Dashboard
                    </button>

                    <!-- Download PDF -->
                    <button id="downloadPdf" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span id="pdfButtonText">PDF</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Legend & Filters -->
        <div class="mb-6 p-4 bg-white rounded-lg border border-gray-200">
            <div class="flex flex-wrap items-center gap-3">
                <span class="text-sm font-medium text-gray-700">Categories:</span>
                <button class="legend-chip active inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium border transition-colors"
                        data-type="master" aria-pressed="true">
                    <span class="w-2 h-2 rounded-full bg-gray-400 mr-2"></span>
                    Master Files
                </button>
                <button class="legend-chip active inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium border transition-colors"
                        data-type="kltg" aria-pressed="true">
                    <span class="w-2 h-2 rounded-full mr-2" style="background-color: var(--brand-dark)"></span>
                    KLTG
                </button>

                <button class="legend-chip active inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium border transition-colors"
                        data-type="media" aria-pressed="true">
                    <span class="w-2 h-2 rounded-full mr-2" style="background-color: #7b00ff"></span>
                    Media
                </button>

                <button class="legend-chip active inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium border transition-colors"
                        data-type="outdoor" aria-pressed="true">
                    <span class="w-2 h-2 rounded-full mr-2" style="background-color: #00d0ff"></span>
                    Outdoor
                </button>
            </div>
        </div>

        <!-- Loading Skeleton -->
        <div id="loadingSkeleton" class="hidden">
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <div class="grid grid-cols-7 gap-4 mb-4">
                    <div class="h-6 skeleton rounded"></div>
                    <div class="h-6 skeleton rounded"></div>
                    <div class="h-6 skeleton rounded"></div>
                    <div class="h-6 skeleton rounded"></div>
                    <div class="h-6 skeleton rounded"></div>
                    <div class="h-6 skeleton rounded"></div>
                    <div class="h-6 skeleton rounded"></div>
                </div>
                <div class="grid grid-cols-7 gap-4">
                    <div class="h-24 skeleton rounded"></div>
                    <div class="h-24 skeleton rounded"></div>
                    <div class="h-24 skeleton rounded"></div>
                    <div class="h-24 skeleton rounded"></div>
                    <div class="h-24 skeleton rounded"></div>
                    <div class="h-24 skeleton rounded"></div>
                    <div class="h-24 skeleton rounded"></div>
                </div>
            </div>
        </div>

        <!-- Year View -->
        <div id="yearView" class="hidden">
            <div id="yearGrid" class="year-grid">
                <!-- Year months will be populated by JavaScript -->
            </div>
        </div>

        <!-- FullCalendar Container -->
        <div id="calendar" class="bg-white rounded-lg border border-gray-200" style="min-height: 600px;">
            <!-- Calendar renders here -->
        </div>
    </div>

    <!-- Event Detail Modal -->
    <div id="eventModalBg" class="fixed inset-0 bg-black/30 z-[9998] hidden"></div>
    <div id="eventModal" class="fixed inset-0 z-[9999] hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-full p-4">
            <div class="relative bg-white rounded-lg shadow-xl w-96 max-w-full">
                <button id="closeModal" type="button"
                        class="absolute top-3 right-3 w-9 h-9 rounded-lg flex items-center justify-center
                               bg-white/90 hover:bg-white shadow ring-1 ring-slate-200 z-50 text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4" id="modalTitle">Job Details</h3>
                    <div id="modalContent" class="space-y-3">
                        <!-- Content will be populated by JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PDF Progress Modal -->
    <div id="pdfProgressModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-1/2 transform -translate-y-1/2 mx-auto p-6 border w-80 shadow-lg rounded-md bg-white">
            <div class="text-center">
                <svg class="animate-spin h-8 w-8 mx-auto mb-4 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Generating PDF...</h3>
                <p class="text-sm text-gray-500" id="pdfProgressText">Preparing calendar for export</p>
            </div>
        </div>
    </div>

    <script>
        let calendar = null;
        let calendarInitialized = false;
        let currentView = localStorage.getItem('calendarView') || 'dayGridMonth';
        let searchTimeout = null;

        // Configuration - UNCHANGED
        const CALENDAR_CONFIG = {
            eventsUrl: '/calendar/events',
        };

        // Utility Functions
        function showLoadingSkeleton(show = true) {
            const skeleton = document.getElementById('loadingSkeleton');
            const calendarEl = document.getElementById('calendar');
            const yearView = document.getElementById('yearView');

            if (show) {
                skeleton.classList.remove('hidden');
                calendarEl.classList.add('hidden');
                yearView.classList.add('hidden');
            } else {
                skeleton.classList.add('hidden');
                if (currentView === 'year') {
                    calendarEl.classList.add('hidden');
                    yearView.classList.remove('hidden');
                } else {
                    calendarEl.classList.remove('hidden');
                    yearView.classList.add('hidden');
                }
            }
        }

        function showPdfProgress(show = true, message = 'Preparing calendar for export') {
            const modal = document.getElementById('pdfProgressModal');
            const text = document.getElementById('pdfProgressText');

            if (show) {
                text.textContent = message;
                modal.classList.remove('hidden');
            } else {
                modal.classList.add('hidden');
            }
        }

        function updateTitle() {
            const title = document.getElementById('calendarTitle');
            if (currentView === 'year') {
                title.textContent = new Date().getFullYear();
            } else if (calendar) {
                title.textContent = calendar.getCurrentData().viewTitle;
            }
        }

        // Event Modal Functions - UNCHANGED
        function showEventModal(eventInfo) {
            const modal = document.getElementById('eventModal');
            const modalBg = document.getElementById('eventModalBg');
            const modalContent = document.getElementById('modalContent');

            const props = eventInfo.event.extendedProps;

            modalContent.innerHTML = `
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Company</label>
                        <p class="mt-1 text-lg font-semibold text-gray-900">${props.company || 'N/A'}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Product</label>
                        <p class="mt-1 text-lg text-gray-900">${props.product || 'N/A'}</p>
                    </div>
                    ${props.client ? `
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Person In Charge</label>
                        <p class="mt-1 text-gray-900">${props.client}</p>
                    </div>
                    ` : ''}
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Status</label>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                            props.status === 'Ongoing' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800'
                        }">
                            ${eventInfo.event.title || 'Unknown'}
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Date</label>
                        <p class="mt-1 text-gray-900">${eventInfo.event.start.toLocaleDateString()}</p>
                    </div>
                </div>
            `;

            modal.classList.remove('hidden');
            modalBg.classList.remove('hidden');
        }

        function hideEventModal() {
            const modal = document.getElementById('eventModal');
            const modalBg = document.getElementById('eventModalBg');
            modal.classList.add('hidden');
            modalBg.classList.add('hidden');
        }

        // Year View Functions
        function generateYearView() {
            const year = new Date().getFullYear();
            const yearGrid = document.getElementById('yearGrid');
            yearGrid.innerHTML = '';

            const months = [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ];

            const dayNames = ['S', 'M', 'T', 'W', 'T', 'F', 'S'];

            months.forEach((monthName, monthIndex) => {
                const monthDiv = document.createElement('div');
                monthDiv.className = 'year-month';
                monthDiv.dataset.month = monthIndex;
                monthDiv.dataset.year = year;

                const header = document.createElement('div');
                header.className = 'year-month-header';
                header.textContent = monthName;
                monthDiv.appendChild(header);

                const miniCal = document.createElement('div');
                miniCal.className = 'year-mini-calendar';

                // Add day headers
                dayNames.forEach(day => {
                    const dayHeader = document.createElement('div');
                    dayHeader.className = 'year-day-header';
                    dayHeader.textContent = day;
                    miniCal.appendChild(dayHeader);
                });

                // Calculate days for this month
                const firstDay = new Date(year, monthIndex, 1).getDay();
                const daysInMonth = new Date(year, monthIndex + 1, 0).getDate();
                const today = new Date();

                // Add empty cells for days before month starts
                for (let i = 0; i < firstDay; i++) {
                    const emptyDay = document.createElement('div');
                    emptyDay.className = 'year-day';
                    miniCal.appendChild(emptyDay);
                }

                // Add days of the month
                for (let day = 1; day <= daysInMonth; day++) {
                    const dayDiv = document.createElement('div');
                    dayDiv.className = 'year-day';
                    dayDiv.textContent = day;

                    if (today.getFullYear() === year &&
                        today.getMonth() === monthIndex &&
                        today.getDate() === day) {
                        dayDiv.classList.add('today');
                    }

                    // TODO: Add event indicators when events are loaded
                    // This would require accessing calendar events

                    miniCal.appendChild(dayDiv);
                }

                monthDiv.appendChild(miniCal);

                // Click handler to navigate to month view
                monthDiv.addEventListener('click', () => {
                    currentView = 'dayGridMonth';
                    localStorage.setItem('calendarView', currentView);

                    if (calendar) {
                        calendar.changeView('dayGridMonth', new Date(year, monthIndex, 1));
                        updateViewButtons();
                        showView('calendar');
                        updateTitle();
                    }
                });

                yearGrid.appendChild(monthDiv);
            });
        }

        function showView(viewId) {
            const calendar = document.getElementById('calendar');
            const yearView = document.getElementById('yearView');
            const skeleton = document.getElementById('loadingSkeleton');

            skeleton.classList.add('hidden');

            if (viewId === 'year') {
                calendar.classList.add('hidden');
                yearView.classList.remove('hidden');
                generateYearView();
            } else {
                calendar.classList.remove('hidden');
                yearView.classList.add('hidden');
            }

            updateTitle();
        }

        // Search Functions
        function performSearch(query) {
            if (!calendar) return;

            const events = calendar.getCurrentData().events;

            events.forEach(event => {
                const eventEl = document.querySelector(`[data-event-id="${event.id}"]`);
                if (eventEl) {
                    const title = event.title.toLowerCase();
                    const matchesSearch = !query || title.includes(query.toLowerCase());

                    if (matchesSearch) {
                        eventEl.classList.remove('hidden');
                        if (query) {
                            eventEl.classList.add('search-highlight');
                        } else {
                            eventEl.classList.remove('search-highlight');
                        }
                    } else {
                        eventEl.classList.add('hidden');
                        eventEl.classList.remove('search-highlight');
                    }
                }
            });
        }

        // Category Filter Functions
        function toggleCategoryFilter(type) {
            const chip = document.querySelector(`[data-type="${type}"]`);
            const isActive = chip.classList.contains('active');

            if (isActive) {
                chip.classList.remove('active');
                chip.setAttribute('aria-pressed', 'false');
                chip.classList.add('opacity-50');
            } else {
                chip.classList.add('active');
                chip.setAttribute('aria-pressed', 'true');
                chip.classList.remove('opacity-50');
            }

            // Apply filter to events
            if (calendar) {
                calendar.rerenderEvents();
            }
        }

        function updateViewButtons() {
            document.querySelectorAll('.view-btn').forEach(btn => {
                const isActive = btn.dataset.view === currentView;
                btn.classList.toggle('active', isActive);
                btn.classList.toggle('bg-white', isActive);
                btn.classList.toggle('text-gray-900', isActive);
                btn.classList.toggle('shadow-sm', isActive);
                btn.classList.toggle('text-gray-600', !isActive);
                btn.setAttribute('aria-pressed', isActive);
            });
        }

        // PDF Generation - UNCHANGED LOGIC
        async function generatePDF() {
            try {
                if (typeof html2canvas === 'undefined' || typeof window.jspdf === 'undefined') {
                    alert('PDF libraries are not loaded. Please refresh the page and try again.');
                    return;
                }

                if (currentView === 'year') {
                    // For year view, capture the year grid
                    const yearGrid = document.getElementById('yearView');
                    if (!yearGrid) {
                        alert('Year view is not available for PDF export.');
                        return;
                    }
                    await generateYearPDF(yearGrid);
                } else if (calendar) {
                    await generateCalendarPDF();
                } else {
                    alert('Calendar is not initialized. Please wait for the calendar to load.');
                }
            } catch (error) {
                console.error('PDF generation failed:', error);
                showPdfProgress(false);
                alert(`Failed to generate PDF: ${error.message}`);
            }
        }

        async function generateCalendarPDF() {
            showPdfProgress(true, 'Preparing calendar for export...');

            const button = document.getElementById('downloadPdf');
            const buttonText = document.getElementById('pdfButtonText');
            button.disabled = true;
            buttonText.textContent = 'Generating...';

            const currentTitle = calendar.getCurrentData().viewTitle;
            const filename = `Billboard_Calendar_${currentTitle.replace(/\s+/g, '_')}.pdf`;

            const calendarEl = document.querySelector('.fc');
            if (!calendarEl) {
                throw new Error('Calendar element not found');
            }

            showPdfProgress(true, 'Capturing calendar view...');

            const canvas = await html2canvas(calendarEl, {
                useCORS: true,
                scale: 2,
                backgroundColor: '#ffffff',
                logging: false,
                allowTaint: false,
                removeContainer: false,
                imageTimeout: 15000,
                height: calendarEl.scrollHeight,
                width: calendarEl.scrollWidth
            });

            showPdfProgress(true, 'Creating PDF document...');

            const imgData = canvas.toDataURL('image/png');
            const { jsPDF } = window.jspdf;

            const imgProps = canvas.width / canvas.height;
            const pdfWidth = 841.89;
            const pdfHeight = 595.28;

            let imgWidth = pdfWidth - 40;
            let imgHeight = imgWidth / imgProps;

            if (imgHeight > pdfHeight - 40) {
                imgHeight = pdfHeight - 40;
                imgWidth = imgHeight * imgProps;
            }

            const pdf = new jsPDF('landscape', 'pt', 'a4');

            pdf.setFontSize(16);
            pdf.setFont(undefined, 'bold');
            pdf.text('Billboard Booking Calendar', 40, 30);

            pdf.setFontSize(12);
            pdf.setFont(undefined, 'normal');
            pdf.text(`Generated: ${new Date().toLocaleString()}`, 40, 50);
            pdf.text(`Period: ${currentTitle}`, 40, 70);

            pdf.addImage(imgData, 'PNG', 20, 80, imgWidth, imgHeight);

            showPdfProgress(true, 'Saving PDF file...');
            pdf.save(filename);

            showPdfProgress(false);

            setTimeout(() => {
                alert(`PDF exported successfully as "${filename}"`);
            }, 500);

            button.disabled = false;
            buttonText.textContent = 'PDF';
        }

        async function generateYearPDF(yearGrid) {
            showPdfProgress(true, 'Preparing year view for export...');

            const button = document.getElementById('downloadPdf');
            const buttonText = document.getElementById('pdfButtonText');
            button.disabled = true;
            buttonText.textContent = 'Generating...';

            const year = new Date().getFullYear();
            const filename = `Billboard_Calendar_Year_${year}.pdf`;

            showPdfProgress(true, 'Capturing year view...');

            const canvas = await html2canvas(yearGrid, {
                useCORS: true,
                scale: 2,
                backgroundColor: '#ffffff',
                logging: false
            });

            showPdfProgress(true, 'Creating PDF document...');

            const imgData = canvas.toDataURL('image/png');
            const { jsPDF } = window.jspdf;

            const pdf = new jsPDF('landscape', 'pt', 'a4');

            pdf.setFontSize(18);
            pdf.setFont(undefined, 'bold');
            pdf.text(`Billboard Booking Calendar - ${year}`, 40, 30);

            pdf.setFontSize(12);
            pdf.setFont(undefined, 'normal');
            pdf.text(`Generated: ${new Date().toLocaleString()}`, 40, 50);

            const imgProps = canvas.width / canvas.height;
            const pdfWidth = 841.89 - 80;
            const pdfHeight = 595.28 - 120;

            let imgWidth = pdfWidth;
            let imgHeight = imgWidth / imgProps;

            if (imgHeight > pdfHeight) {
                imgHeight = pdfHeight;
                imgWidth = imgHeight * imgProps;
            }

            const x = (841.89 - imgWidth) / 2;
            pdf.addImage(imgData, 'PNG', x, 70, imgWidth, imgHeight);

            showPdfProgress(true, 'Saving PDF file...');
            pdf.save(filename);

            showPdfProgress(false);

            setTimeout(() => {
                alert(`Year view PDF exported successfully as "${filename}"`);
            }, 500);

            button.disabled = false;
            buttonText.textContent = 'PDF';
        }

        // Calendar Initialization - LOGIC UNCHANGED, ONLY STYLING UPDATES
        function initializeCalendar() {
            console.log('Starting calendar initialization...');
            showLoadingSkeleton(true);

            try {
                const calendarEl = document.getElementById('calendar');

                if (!calendarEl) {
                    throw new Error('Calendar element not found');
                }

                if (typeof FullCalendar === 'undefined') {
                    throw new Error('FullCalendar library not loaded');
                }

                if (calendar) {
                    calendar.destroy();
                    calendar = null;
                }

                calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: currentView === 'year' ? 'dayGridMonth' : currentView,
                    headerToolbar: false, // We use custom header

                    // Backend integration - UNCHANGED
                    events: {
                        url: CALENDAR_CONFIG.eventsUrl,
                        method: 'GET',
                        failure: function() {
                            alert('Failed to load events from server');
                            console.error('Calendar events loading failed');
                        },
                        extraParams: function() {
                            return {};
                        }
                    },

                    eventDisplay: 'block',
                    dayMaxEvents: 4,
                    moreLinkClick: 'popover',
                    dayHeaderFormat: { weekday: 'short' },
                    eventTimeFormat: {
                        hour: undefined,
                        minute: undefined,
                        meridiem: false
                    },
                    displayEventEnd: false,
                    allDaySlot: true,

                    // Enhanced event rendering with pills and categories
                    eventContent: function(arg) {
                        const title = arg.event.title || '';
                        const props = arg.event.extendedProps || {};
                        const source = props.source;
                        const status = props.status;
                        const type = props.type || 'master';

                        const container = document.createElement('div');
                        container.className = 'flex items-center min-w-0 px-2 py-1';
                        container.setAttribute('data-event-id', arg.event.id);

                        // Category dot
                        const dot = document.createElement('span');
                        dot.className = 'fc-event-dot flex-shrink-0';
                        container.appendChild(dot);

                        // Title
                        const titleSpan = document.createElement('span');
                        titleSpan.className = 'text-xs font-medium break-words whitespace-normal';
                        titleSpan.textContent = title;
                        titleSpan.title = title; // Tooltip
                        container.appendChild(titleSpan);

                        // END marker for Master Files
                        if (source === 'MASTER') {
                            const endMarker = document.createElement('span');
                            endMarker.textContent = 'END';
                            endMarker.className = 'ml-1 inline-flex items-center rounded-full bg-slate-100 px-1.5 py-[1px] text-[10px] font-semibold text-slate-600';
                            container.appendChild(endMarker);
                        }

                        return { domNodes: [container] };
                    },

                    eventDidMount: function(info) {
                        const props = info.event.extendedProps || {};
                        const type = props.type || 'master';
                        const status = props.status;

                        // Apply category classes
                        info.el.classList.add(`event-${type}`);

                        // Enhanced tooltip
                        let tip = info.event.title || 'Event';
                        if (props.company && props.product) {
                            tip += `\n${props.company} — ${props.product}`;
                        }
                        if (props.client) {
                            tip += `\nPerson In Charge: ${props.client}`;
                        }
                        if (status) {
                            tip += `\nStatus: ${status}`;
                        }
                        if (props.source === 'MASTER') {
                            tip += '\n📍 Project End Date';
                        }

                        info.el.title = tip;

                        // Apply category filter visibility
                        const chip = document.querySelector(`[data-type="${type}"]`);
                        if (chip && !chip.classList.contains('active')) {
                            info.el.classList.add('hidden');
                        }
                    },

                    eventClick: function(info) {
                        showEventModal(info);
                    },

                    datesSet: function() {
                        updateTitle();
                    },

                    loading: function(isLoading) {
                        if (isLoading) {
                            showLoadingSkeleton(true);
                        } else {
                            showLoadingSkeleton(false);
                        }
                    },

                    height: 'auto',
                    aspectRatio: 1.35,
                    navLinks: false,
                    weekNumbers: false,
                    fixedWeekCount: false,
                    showNonCurrentDates: true
                });

                calendar.render();
                calendarInitialized = true;
                showLoadingSkeleton(false);

                console.log('Calendar initialized successfully!');

            } catch (error) {
                console.error('Calendar initialization failed:', error);

                const calendarEl = document.getElementById('calendar');
                calendarEl.innerHTML = `
                    <div class="flex items-center justify-center h-full p-8">
                        <div class="text-center text-red-600">
                            <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="text-lg font-semibold mb-2">Calendar Error</h3>
                            <p class="text-sm mb-4">${error.message}</p>
                            <button onclick="initializeCalendar()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                Try Again
                            </button>
                        </div>
                    </div>
                `;

                calendarInitialized = false;
            }
        }

        // Event Listeners
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM ready, setting up calendar...');

            // Initialize calendar
            setTimeout(() => {
                initializeCalendar();
            }, 500);

            // Navigation buttons
            document.getElementById('prevBtn').addEventListener('click', () => {
                if (currentView === 'year') {
                    // Handle year navigation if needed
                } else if (calendar) {
                    calendar.prev();
                }
            });

            document.getElementById('nextBtn').addEventListener('click', () => {
                if (currentView === 'year') {
                    // Handle year navigation if needed
                } else if (calendar) {
                    calendar.next();
                }
            });

            document.getElementById('todayBtn').addEventListener('click', () => {
                if (currentView === 'year') {
                    currentView = 'dayGridMonth';
                    localStorage.setItem('calendarView', currentView);

                    if (calendar) {
                        calendar.changeView('dayGridMonth');
                        calendar.today();
                        updateViewButtons();
                        showView('calendar');
                    }
                } else if (calendar) {
                    calendar.today();
                }
            });

            // View buttons
            document.querySelectorAll('.view-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const view = btn.dataset.view;
                    currentView = view;
                    localStorage.setItem('calendarView', view);

                    if (view === 'year') {
                        showView('year');
                    } else {
                        if (calendar) {
                            calendar.changeView(view);
                        }
                        showView('calendar');
                    }

                    updateViewButtons();
                });
            });

            // Search functionality
            const searchInput = document.getElementById('searchInput');
            searchInput.addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    performSearch(e.target.value.trim());
                }, 200);
            });

            // Legend/filter chips
            document.querySelectorAll('.legend-chip').forEach(chip => {
                chip.addEventListener('click', () => {
                    toggleCategoryFilter(chip.dataset.type);
                });
            });

            // PDF download
            document.getElementById('downloadPdf').addEventListener('click', generatePDF);

            // Modal event listeners
            const modal = document.getElementById('eventModal');
            const modalBg = document.getElementById('eventModalBg');
            const closeBtn = document.getElementById('closeModal');

            function hideModal() {
                modal.classList.add('hidden');
                modalBg.classList.add('hidden');
            }

            closeBtn.addEventListener('click', hideModal);
            modalBg.addEventListener('click', hideModal);
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') hideModal();
            });

            // Initialize view buttons
            updateViewButtons();

            console.log('Setup complete');
        });

        // Set legend chip styles
        document.addEventListener('DOMContentLoaded', function() {
            const legendChips = document.querySelectorAll('.legend-chip');
            legendChips.forEach(chip => {
                chip.classList.add('bg-white', 'border-gray-200', 'text-gray-700', 'hover:bg-gray-50');

                if (!chip.classList.contains('active')) {
                    chip.classList.add('opacity-50');
                }
            });
        });

        // Global debug functions - UNCHANGED
        window.calendarDebug = {
            reinit: initializeCalendar,
            calendar: () => calendar,
            generatePdf: generatePDF
        };
    </script>
</body>
</html>
<?php /**PATH C:\Users\Gjlang\kl_guide_tracker\resources\views/calendar/index.blade.php ENDPATH**/ ?>