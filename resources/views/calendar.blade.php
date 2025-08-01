@extends('layouts.app')

@section('title', 'Job Management Calendar')

@section('styles')
<link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.10/main.min.css' rel='stylesheet' />
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<style>
    .calendar-container {
        display: flex;
        gap: 20px;
        margin-top: 20px;
    }

    .sidebar {
        width: 300px;
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        height: fit-content;
    }

    .main-calendar {
        flex: 1;
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .mini-calendar {
        margin-bottom: 30px;
    }

    .upcoming-tasks {
        max-height: 400px;
        overflow-y: auto;
    }

    .task-item {
        padding: 12px;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .task-item:hover {
        background-color: #f9fafb;
        border-color: #d1d5db;
    }

    .task-status {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
        color: white;
    }

    .status-pending { background-color: #ef4444; }
    .status-ongoing { background-color: #f59e0b; }
    .status-completed { background-color: #10b981; }

    .modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background: white;
        padding: 30px;
        border-radius: 12px;
        width: 90%;
        max-width: 600px;
        max-height: 80vh;
        overflow-y: auto;
        position: relative;
    }

    .modal.hidden {
        display: none;
    }

    .close-btn {
        position: absolute;
        top: 15px;
        right: 20px;
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #6b7280;
    }

    .close-btn:hover {
        color: #374151;
    }

    .progress-bar {
        width: 100%;
        height: 8px;
        background-color: #e5e7eb;
        border-radius: 4px;
        overflow: hidden;
        margin: 10px 0;
    }

    .progress-fill {
        height: 100%;
        background-color: #3b82f6;
        transition: width 0.3s ease;
    }

    .filter-section {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }

    .filter-controls {
        display: flex;
        gap: 15px;
        align-items: center;
        flex-wrap: wrap;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .form-group label {
        font-size: 14px;
        font-weight: 500;
        color: #374151;
    }

    .form-control {
        padding: 8px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 14px;
    }

    .btn {
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
    }

    .btn-primary {
        background-color: #3b82f6;
        color: white;
    }

    .btn-primary:hover {
        background-color: #2563eb;
    }

    .btn-success {
        background-color: #10b981;
        color: white;
    }

    .btn-success:hover {
        background-color: #059669;
    }

    .btn-secondary {
        background-color: #6b7280;
        color: white;
    }

    .btn-secondary:hover {
        background-color: #4b5563;
    }

    .btn-danger {
        background-color: #ef4444;
        color: white;
    }

    .btn-danger:hover {
        background-color: #dc2626;
    }

    .file-upload-area {
        border: 2px dashed #d1d5db;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        margin: 15px 0;
        cursor: pointer;
        transition: all 0.2s;
    }

    .file-upload-area:hover {
        border-color: #3b82f6;
        background-color: #f8fafc;
    }

    .fc-event {
        cursor: pointer;
    }

    .fc-event:hover {
        opacity: 0.8;
    }

    @media (max-width: 768px) {
        .calendar-container {
            flex-direction: column;
        }

        .sidebar {
            width: 100%;
        }

        .filter-controls {
            flex-direction: column;
            align-items: stretch;
        }

        .form-group {
            width: 100%;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Job Management Calendar</h1>
        <button class="btn btn-primary" onclick="openAddJobModal()">
            <i class="fas fa-plus"></i> Add New Job
        </button>
    </div>

    <!-- Filters -->
    <div class="filter-section">
        <div class="filter-controls">
            <div class="form-group">
                <label for="sectionFilter">Section</label>
                <select id="sectionFilter" class="form-control" onchange="applyFilters()">
                    <option value="">All Sections</option>
                    @if(isset($sections))
                        @foreach($sections as $section)
                            <option value="{{ $section }}">{{ $section }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" id="myTasksOnly" onchange="applyFilters()"> My Tasks Only
                </label>
            </div>

            <div class="form-group">
                <label for="statusFilter">Status</label>
                <select id="statusFilter" class="form-control" onchange="applyFilters()">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="ongoing">Ongoing</option>
                    <option value="completed">Completed</option>
                </select>
            </div>

            <button class="btn btn-secondary" onclick="clearFilters()">Clear Filters</button>
        </div>
    </div>

    <!-- Calendar Container -->
    <div class="calendar-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Mini Calendar -->
            <div class="mini-calendar">
                <h5 class="mb-3"><i class="fas fa-calendar-alt"></i> Quick Navigation</h5>
                <div id="miniCalendar"></div>
            </div>

            <!-- Upcoming Tasks -->
            <div class="upcoming-tasks">
                <h5 class="mb-3"><i class="fas fa-clock"></i> Upcoming Tasks</h5>
                @if(isset($upcomingTasks) && $upcomingTasks->count())
                    @foreach($upcomingTasks as $task)
                        <div class="task-item" onclick="jumpToDate('{{ $task->start_date->format('Y-m-d') }}')">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="mb-1">{{ $task->company_name }}</h6>
                                <span class="task-status status-{{ $task->status }}">{{ ucfirst($task->status) }}</span>
                            </div>
                            <p class="mb-1 text-muted small">{{ Str::limit($task->product, 30) }}</p>
                            <p class="mb-0 text-muted small">
                                <i class="fas fa-calendar"></i> {{ $task->start_date->format('M d, Y') }}
                            </p>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted">No upcoming tasks</p>
                @endif
            </div>
        </div>

        <!-- Main Calendar -->
        <div class="main-calendar">
            <div id="calendar"></div>
        </div>
    </div>
</div>

<!-- Job Details Modal -->
<div id="jobModal" class="modal hidden">
    <div class="modal-content">
        <button class="close-btn" onclick="closeModal()">&times;</button>
        <h4 id="modalTitle" class="mb-3"></h4>
        <div id="modalContent">
            <!-- Content will be populated by JavaScript -->
        </div>
        <div class="d-flex gap-2 mt-4">
            <button class="btn btn-primary" onclick="editJob()">Edit</button>
            <button class="btn btn-danger" onclick="deleteJob()">Delete</button>
            <button class="btn btn-secondary" onclick="closeModal()">Close</button>
        </div>
    </div>
</div>

<!-- Add/Edit Job Modal -->
<div id="jobFormModal" class="modal hidden">
    <div class="modal-content">
        <button class="close-btn" onclick="closeJobFormModal()">&times;</button>
        <h4 id="formModalTitle">Add New Job</h4>
        <form id="jobForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="jobId" name="job_id">

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="company_name">Company Name *</label>
                        <input type="text" id="company_name" name="company_name" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="product">Product *</label>
                        <input type="text" id="product" name="product" class="form-control" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="start_date">Start Date *</label>
                        <input type="datetime-local" id="start_date" name="start_date" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="end_date">End Date *</label>
                        <input type="datetime-local" id="end_date" name="end_date" class="form-control" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label for="status">Status *</label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="pending">Pending</option>
                            <option value="ongoing">Ongoing</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label for="section">Section *</label>
                        <input type="text" id="section" name="section" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label for="progress">Progress (%)</label>
                        <input type="number" id="progress" name="progress" class="form-control" min="0" max="100">
                    </div>
                </div>
            </div>

            <div class="form-group mb-3">
                <label for="remarks">Remarks</label>
                <textarea id="remarks" name="remarks" class="form-control" rows="3"></textarea>
            </div>

            <div class="form-group mb-3">
                <label for="file">Upload File</label>
                <div class="file-upload-area" onclick="document.getElementById('file').click()">
                    <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                    <p class="text-muted mb-0">Click to upload or drag and drop</p>
                    <small class="text-muted">PDF, DOC, DOCX, XLS, XLSX, JPG, PNG (Max: 10MB)</small>
                </div>
                <input type="file" id="file" name="file" class="d-none" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.png">
                <div id="fileInfo" class="mt-2 text-muted small"></div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">Save Job</button>
                <button type="button" class="btn btn-secondary" onclick="closeJobFormModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.10/main.min.js'></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.3.4/axios.min.js"></script>

<script>
    let calendar;
    let miniCalendar;                                                                                                                                                                                                           
    let currentJob = null;

    document.addEventListener('DOMContentLoaded', function() {
        initializeCalendars();
        setupFileUpload();
    }); 

    function initializeCalendars() {
        // Main Calendar
        var calendarEl = document.getElementById('calendar');
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
            },
            events: function(fetchInfo, successCallback, failureCallback) {
                fetchEvents(fetchInfo.startStr, fetchInfo.endStr, successCallback, failureCallback);
            },
            eventClick: function(info) {
                showJobModal(info.event);
            },
            eventDidMount: function(info) {
                // Add tooltip
                info.el.setAttribute('title', `${info.event.extendedProps.company} - ${info.event.extendedProps.status}`);
            },
            height: 'auto',
            eventDisplay: 'block',
            dayMaxEvents: 3
        });
        calendar.render();

        // Mini Calendar
        var miniCalendarEl = document.getElementById('miniCalendar');
        miniCalendar = new FullCalendar.Calendar(miniCalendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev',
                center: 'title',
                right: 'next'
            },
            height: 250,
            dateClick: function(info) {
                calendar.gotoDate(info.dateStr);
            },
            eventDisplay: 'none'
        });
        miniCalendar.render();
    }

    function fetchEvents(start, end, successCallback, failureCallback) {
        const params = new URLSearchParams({
            start: start,
            end: end
        });

        // Add filters
        const section = document.getElementById('sectionFilter').value;
        const myTasksOnly = document.getElementById('myTasksOnly').checked;
        const status = document.getElementById('statusFilter').value;

        if (section) params.append('section', section);
        if (myTasksOnly) params.append('my_tasks_only', '1');
        if (status) params.append('status', status);

        axios.get(`/calendar/events?${params}`)
            .then(response => {
                successCallback(response.data);
            })
            .catch(error => {
                console.error('Error fetching events:', error);
                failureCallback(error);
            });
    }

    function showJobModal(event) {
        currentJob = event;
        const props = event.extendedProps;

        document.getElementById('modalTitle').textContent = event.title;

        const progressHtml = `
            <div class="progress-bar">
                <div class="progress-fill" style="width: ${props.progress}%"></div>
            </div>`;

        const fileHtml = props.file_path ?
            `<p><strong>File:</strong> <a href="/storage/${props.file_path}" target="_blank" class="text-primary">
                <i class="fas fa-download"></i> Download File
            </a></p>` : '';

        document.getElementById('modalContent').innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Company:</strong> ${props.company}</p>
                    <p><strong>Product:</strong> ${props.product}</p>
                    <p><strong>Section:</strong> ${props.section}</p>
                    <p><strong>Assigned to:</strong> ${props.assigned_user || 'Unassigned'}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Status:</strong>
                        <span class="task-status status-${props.status}">${props.status.charAt(0).toUpperCase() + props.status.slice(1)}</span>
                    </p>
                    <p><strong>Start Date:</strong> ${new Date(event.start).toLocaleDateString()}</p>
                    <p><strong>End Date:</strong> ${new Date(event.end).toLocaleDateString()}</p>
                    <p><strong>Progress:</strong> ${props.progress}%</p>
                </div>
            </div>
            ${progressHtml}
            ${props.remarks ? `<p><strong>Remarks:</strong> ${props.remarks}</p>` : ''}
            ${fileHtml}
        `;

        document.getElementById('jobModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('jobModal').classList.add('hidden');
        currentJob = null;
    }

    function openAddJobModal() {
        document.getElementById('formModalTitle').textContent = 'Add New Job';
        document.getElementById('jobForm').reset();
        document.getElementById('jobId').value = '';
        document.getElementById('fileInfo').textContent = '';
        document.getElementById('jobFormModal').classList.remove('hidden');
    }

    function closeJobFormModal() {
        document.getElementById('jobFormModal').classList.add('hidden');
    }

    function editJob() {
        if (!currentJob) return;

        const props = currentJob.extendedProps;

        document.getElementById('formModalTitle').textContent = 'Edit Job';
        document.getElementById('jobId').value = currentJob.id;
        document.getElementById('company_name').value = props.company;
        document.getElementById('product').value = props.product;
        document.getElementById('start_date').value = formatDateForInput(currentJob.start);
        document.getElementById('end_date').value = formatDateForInput(currentJob.end);
        document.getElementById('status').value = props.status;
        document.getElementById('section').value = props.section;
        document.getElementById('progress').value = props.progress;
        document.getElementById('remarks').value = props.remarks;

        if (props.file_path) {
            document.getElementById('fileInfo').innerHTML = `
                <i class="fas fa-file"></i> Current file:
                <a href="/storage/${props.file_path}" target="_blank" class="text-primary">View File</a>
            `;
        }

        closeModal();
        document.getElementById('jobFormModal').classList.remove('hidden');
    }

    function deleteJob() {
        if (!currentJob) return;

        if (confirm('Are you sure you want to delete this job?')) {
            axios.delete(`/calendar/jobs/${currentJob.id}`)
                .then(response => {
                    if (response.data.success) {
                        calendar.refetchEvents();
                        closeModal();
                        showAlert('Job deleted successfully!', 'success');
                    }
                })
                .catch(error => {
                    console.error('Error deleting job:', error);
                    showAlert('Error deleting job', 'error');
                });
        }
    }

    function formatDateForInput(date) {
        const d = new Date(date);
        return d.toISOString().slice(0, 16);
    }

    function applyFilters() {
        calendar.refetchEvents();
    }

    function clearFilters() {
        document.getElementById('sectionFilter').value = '';
        document.getElementById('myTasksOnly').checked = false;
        document.getElementById('statusFilter').value = '';
        calendar.refetchEvents();
    }

    function jumpToDate(dateStr) {
        calendar.gotoDate(dateStr);
    }

    function setupFileUpload() {
        const fileInput = document.getElementById('file');
        const fileInfo = document.getElementById('fileInfo');

        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                fileInfo.innerHTML = `
                    <i class="fas fa-file"></i> Selected: ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)
                `;
            } else {
                fileInfo.textContent = '';
            }
        });
    }

    // Handle form submission
    document.getElementById('jobForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const jobId = document.getElementById('jobId').value;

        let url = '/calendar/jobs';
        let method = 'POST';

        if (jobId) {
            url = `/calendar/jobs/${jobId}`;
            formData.append('_method', 'PUT');
        }

        // Show loading state
        const submitBtn = document.querySelector('#jobForm button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Saving...';
        submitBtn.disabled = true;

        axios.post(url, formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (response.data.success) {
                calendar.refetchEvents();
                closeJobFormModal();
                showAlert(jobId ? 'Job updated successfully!' : 'Job created successfully!', 'success');
            }
        })
        .catch(error => {
            console.error('Error saving job:', error);
            let errorMessage = 'Error saving job';

            if (error.response && error.response.data && error.response.data.errors) {
                const errors = Object.values(error.response.data.errors).flat();
                errorMessage = errors.join(', ');
            }

            showAlert(errorMessage, 'error');
        })
        .finally(() => {
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        });
    });

    function showAlert(message, type = 'info') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show`;
        alertDiv.style.position = 'fixed';
        alertDiv.style.top = '20px';
        alertDiv.style.right = '20px';
        alertDiv.style.zIndex = '9999';
        alertDiv.style.minWidth = '300px';

        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
        `;

        document.body.appendChild(alertDiv);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.parentNode.removeChild(alertDiv);
            }
        }, 5000);
    }

    // Close modals when clicking outside
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal')) {
            e.target.classList.add('hidden');
        }
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
            closeJobFormModal();
        }
    });
</script>
@endsection
