@push('head')
    <link rel="icon" type="image/x-icon" href="{{ asset('images/bluedale_logo_1.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400;0,500;0,600;1,400&family=Inter:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
@endpush

@push('styles')
    <style>
        /* Typography & Base Styles */
        .font-serif {
            font-family: 'EB Garamond', Georgia, serif;
        }

        .font-sans {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        /* Editorial Color Palette */
        .ink {
            color: #1C1E26;
        }

        .soft-ink {
            color: #6B7280;
        }

        .lighter-ink {
            color: #9CA3AF;
        }

        .paper-bg {
            background-color: #F7F7F9;
        }

        .surface {
            background-color: #FFFFFF;
        }

        /* Borders & Lines */
        .hairline {
            border-color: #EAEAEA;
        }

        .hairline-subtle {
            border-color: rgba(234, 234, 234, 0.7);
        }

        /* Typography Utilities */
        .caps-header {
            text-transform: uppercase;
            letter-spacing: 0.06em;
            font-size: 11px;
            font-weight: 600;
            color: #6B7280;
        }

        .tabular-nums {
            font-variant-numeric: tabular-nums;
            font-feature-settings: 'tnum' 1;
        }

        /* Layout Components */
        .page-canvas {
            background-color: #F7F7F9;
            min-height: 100vh;
        }

        /* Header Bar */
        .header-bar {
            background: white;
            border-bottom: 1px solid #EAEAEA;
            padding: 1.5rem 0;
            margin-bottom: 2rem;
        }

        /* Filter Card */
        .filter-card {
            background: white;
            border-radius: 1rem;
            border: 1px solid rgba(229, 231, 235, 0.7);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        /* Filter Chips */
        .filter-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #EAEAEA;
        }

        .filter-chip {
            display: inline-flex;
            align-items: center;
            padding: 0.375rem 0.75rem;
            background: #F3F4F6;
            border: 1px solid #E5E7EB;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            color: #374151;
            transition: all 150ms ease;
        }

        .filter-chip-close {
            margin-left: 0.5rem;
            color: #9CA3AF;
            cursor: pointer;
            font-weight: 600;
            transition: color 150ms ease;
        }

        .filter-chip-close:hover {
            color: #6B7280;
        }

        /* Button System */
        .btn-primary {
            background-color: #22255b;
            color: white;
            border-radius: 9999px;
            padding: 0.625rem 1.25rem;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 150ms ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            cursor: pointer;
            text-decoration: none;
            height: 2.75rem;
        }

        .btn-primary:hover {
            opacity: 0.9;
            text-decoration: none;
            color: white;
        }

        .btn-primary:focus {
            outline: none;
            box-shadow: 0 0 0 2px #4bbbed, 0 0 0 4px rgba(75, 187, 237, 0.2);
        }

        .btn-ghost {
            border: 1px solid #E5E7EB;
            color: #6B7280;
            background: white;
            border-radius: 9999px;
            padding: 0.625rem 1.25rem;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 150ms ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            text-decoration: none;
            height: 2.75rem;
        }

        .btn-ghost:hover {
            background-color: #F9FAFB;
            color: #374151;
            text-decoration: none;
            border-color: #D1D5DB;
        }

        .btn-ghost:focus {
            outline: none;
            box-shadow: 0 0 0 2px #4bbbed, 0 0 0 4px rgba(75, 187, 237, 0.2);
        }

        /* Form Controls */
        .form-control {
            height: 2.75rem;
            width: 100%;
            border: 1px solid #D1D5DB;
            border-radius: 0.75rem;
            padding: 0 1rem;
            font-size: 0.875rem;
            font-family: 'Inter', sans-serif;
            transition: all 150ms ease;
            background: white;
        }

        .form-control:focus {
            outline: none;
            border-color: #4bbbed;
            box-shadow: 0 0 0 3px rgba(75, 187, 237, 0.1);
        }

        .form-label {
            display: block;
            color: #6B7280;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            font-size: 11px;
            font-weight: 600;
        }

        /* Data Table - Force Override */
        .table-card {
            background: white !important;
            border-radius: 1rem !important;
            border: 2px solid #D1D5DB !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
            overflow: hidden !important;
        }

        .ledger-table {
            width: 100% !important;
            font-size: 0.875rem !important;
            border-collapse: separate !important;
            border-spacing: 0 !important;
        }

        .ledger-table thead th {
            padding: 1.25rem 1rem !important;
            background: #F3F4F6 !important;
            border-bottom: 3px solid #D1D5DB !important;
            border-right: 2px solid #D1D5DB !important;
            position: sticky !important;
            top: 0 !important;
            z-index: 10 !important;
            text-align: left !important;
            font-weight: 700 !important;
            color: #374151 !important;
        }

        .ledger-table thead th:last-child {
            border-right: none !important;
        }

        .ledger-table thead th.text-right {
            text-align: right !important;
        }

        .ledger-table tbody td {
            padding: 1.25rem 1rem !important;
            border-bottom: 1px solid #D1D5DB !important;
            border-right: 1px solid #D1D5DB !important;
            transition: all 150ms ease !important;
            vertical-align: middle !important;
            background: white !important;
        }

        .ledger-table tbody td:last-child {
            border-right: none !important;
        }

        .ledger-table tbody td.text-right {
            text-align: right !important;
        }

        .ledger-table tbody tr:hover td {
            background-color: #EBF8FF !important;
            border-color: #93C5FD !important;
        }

        .ledger-table tbody tr:last-child td {
            border-bottom: 2px solid #D1D5DB !important;
        }

        /* Table Inputs */
        .table-input {
            height: 2.25rem;
            font-size: 0.875rem;
            padding: 0 0.75rem;
            border-radius: 0.5rem;
            border: 1px solid #E5E7EB;
            width: 100%;
            transition: all 150ms ease;
            min-width: 100px;
            background: white;
        }

        .table-input:focus {
            outline: none;
            border-color: #4bbbed;
            box-shadow: 0 0 0 3px rgba(75, 187, 237, 0.1);
        }

        .table-input.text-right {
            text-align: right;
        }

        /* Autosave States */
        .table-input.saving {
            background-color: #FEF3CD;
            border-color: #F59E0B;
        }

        .table-input.saved {
            background-color: #D1FAE5;
            border-color: #10B981;
        }

        .table-input.error {
            background-color: #FEE2E2;
            border-color: #EF4444;
        }

        /* Column Sizing */
        .col-no {
            width: 60px;
            min-width: 60px;
        }

        .col-date {
            width: 120px;
            min-width: 120px;
        }

        .col-company {
            width: 200px;
            min-width: 180px;
        }

        .col-person {
            width: 140px;
            min-width: 120px;
        }

        .col-standard {
            width: 140px;
            min-width: 120px;
        }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.625rem;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            font-weight: 500;
            background-color: #F3F4F6;
            color: #374151;
            border: 1px solid #E5E7EB;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 1.5rem;
        }

        .empty-icon {
            width: 4rem;
            height: 4rem;
            margin: 0 auto 1rem;
            border-radius: 50%;
            background: #F3F4F6;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .placeholder-dash {
            color: #D1D5DB;
            font-weight: 400;
        }

        /* Tab System */
        .tab-container {
            background: white;
            border-radius: 1rem;
            border: 1px solid rgba(229, 231, 235, 0.7);
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .tab-strip {
            display: flex;
            background: #F8F9FA;
            padding: 0.25rem;
            gap: 0.125rem;
            overflow-x: auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .tab-strip::-webkit-scrollbar {
            display: none;
        }

        .tab {
            padding: 0.75rem 1.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 150ms ease;
            border-radius: 0.75rem;
            white-space: nowrap;
            position: relative;
            color: #6B7280;
            text-decoration: none;
            min-width: max-content;
            background: transparent;
        }

        .tab:not(.active):hover {
            color: #374151;
            background-color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
        }

        .tab.active {
            color: #1C1E26;
            background: white;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }

        .tab:focus-visible {
            outline: none;
            box-shadow: 0 0 0 2px #4bbbed;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .col-company {
                width: 160px;
                min-width: 140px;
            }

            .col-standard {
                width: 120px;
                min-width: 100px;
            }

            .ledger-table thead th,
            .ledger-table tbody td {
                padding: 0.75rem 0.5rem;
            }

            .tab {
                padding: 0.875rem 1rem;
            }

            .filter-card {
                padding: 1rem;
            }

            .header-bar {
                padding: 1rem 0;
            }
        }

        /* Fine Details */
        .text-balance {
            text-wrap: balance;
        }

        .tracking-wide {
            letter-spacing: 0.025em;
        }
    </style>
@endpush

@php
    /** @var \Illuminate\Support\Collection $rows */
    /** @var \Illuminate\Support\Collection $existing */

    function _dbcol($k)
    {
        static $map = [
            // umum
            'title' => 'title_snapshot',
            'company' => 'company_snapshot',
            'client_bp' => 'client_bp',
            'x' => 'x',
            'edition' => 'edition',
            'publication' => 'publication',
            'remarks' => 'remarks',
            'artwork_party' => 'artwork_bp_client',

            // KLTG/Print dates (di DB disimpan tanpa _date)
            'artwork_reminder_date' => 'artwork_reminder',
            'material_received_date' => 'material_record',
            'artwork_done_date' => 'artwork_done',
            'send_chop_sign_date' => 'send_chop_sign',
            'chop_sign_approval_date' => 'chop_sign_approval',
            'park_in_server_date' => 'park_in_file_server',

            // Video/LB/Article
            'material_reminder_text' => 'material_reminder_text',
            'video_done_date' => 'video_done',
            'pending_approval_date' => 'pending_approval',
            'video_approved_date' => 'video_approved',
            'video_scheduled_date' => 'video_scheduled',
            'video_posted_date' => 'video_posted',
            'article_done_date' => 'article_done',
            'article_approved_date' => 'article_approved',
            'article_scheduled_date' => 'article_scheduled',
            'article_posted_date' => 'article_posted',
            'post_link' => 'post_link',

            // EM
            'em_date_write' => 'em_date_write',
            'em_date_to_post' => 'em_date_to_post',
            'em_post_date' => 'em_post_date',
            'em_qty' => 'em_qty',
            'blog_link' => 'blog_link',
        ];
        return $map[$k] ?? $k;
    }

    function cellVal($existing, $row, $key, $type, $activeTab)
{
    // Build composite key: master_file_id_subcategory_year_month
    $masterId = $row->master_file_id ?? $row->id;
    $year = $row->activity_year;
    $month = $row->activity_month;

    // Map activeTab to stored subcategory - SEMUA UPPERCASE!
    $subcategoryMap = [
        'print'   => 'KLTG',
        'video'   => 'VIDEO',     // ✅ UPPERCASE
        'article' => 'ARTICLE',   // ✅ UPPERCASE
        'lb'      => 'LB',
        'em'      => 'EM',
    ];
    $subcategory = $subcategoryMap[$activeTab] ?? strtoupper($activeTab);

    $compositeKey = $masterId . '_' . $subcategory . '_' . $year . '_' . $month;
    $record = $existing->get($compositeKey);

    if (!$record) {
        return '';
    }

    $col = _dbcol($key);
    $v = $record->{$col} ?? '';

    // Normalize date values for HTML <input type="date">
    if ($type === 'date') {
        // If it's a Carbon/DateTime object, format it
        if (is_object($v) && method_exists($v, 'format')) {
            return $v->format('Y-m-d');
        }
        // If it's a string, trim time or parse
        if (is_string($v) && $v !== '') {
            // Common case: "YYYY-MM-DD HH:MM:SS" -> "YYYY-MM-DD"
            if (preg_match('/^\d{4}-\d{2}-\d{2}/', $v, $m)) {
                return $m[0];
            }
            try {
                return \Carbon\Carbon::parse($v)->format('Y-m-d');
            } catch (\Throwable $e) {
                return '';
            }
        }
        return '';
    }

    // Non-date: return as-is (string/number/bool)
    return $v ?? '';
}
@endphp

<x-app-shell title="KLTG • Coordinator List">
    <div class="page-canvas">
        <!-- Header Bar -->
        <div class="header-bar">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                    <div class="flex items-center gap-6">
                        <a href="{{ route('dashboard.kltg') }}" class="btn-ghost">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back
                        </a>

                        <div>
                            <h1 class="font-serif text-4xl font-medium ink text-balance">{{ $periodLabel }}</h1>
                            <p class="soft-ink text-sm mt-1 tracking-wide">Monthly KLTG Overview</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <form method="GET" action="{{ route('coordinator.kltg.export') }}" id="exportForm">
                            <input type="hidden" name="subcategory" value="{{ $activeTab }}">
                            <input type="hidden" name="month" id="exportMonth" value="{{ $month }}">
                            <input type="hidden" name="year" id="exportYear" value="{{ $year }}">
                            <input type="hidden" name="working" value="{{ request('working') }}">
                            <button type="submit" class="btn-primary">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Export
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
            <!-- Filter Card -->
            <div class="filter-card"
                style="background: white; border-radius: 1rem; border: 2px solid #E5E7EB; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05); padding: 1.5rem; margin-bottom: 2rem;">
                <!-- Active Filter Chips -->
                @if ($month || $year)
                    <div
                        style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid #E5E7EB;">
                        @if ($month)
                            <span
                                style="display: inline-flex; align-items: center; padding: 0.5rem 1rem; background: linear-gradient(135deg, #22255b 0%, #1a1d4a 100%); color: white; border-radius: 9999px; font-size: 0.875rem; font-weight: 500; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);">
                                {{ date('F', mktime(0, 0, 0, $month, 1)) }}
                                <span
                                    style="margin-left: 0.75rem; color: rgba(255, 255, 255, 0.7); cursor: pointer; font-weight: 600; padding: 0.125rem 0.25rem; border-radius: 50%; transition: all 150ms ease;"
                                    onclick="clearParam('month')"
                                    onmouseover="this.style.backgroundColor='rgba(255,255,255,0.2)'; this.style.color='white';"
                                    onmouseout="this.style.backgroundColor='transparent'; this.style.color='rgba(255,255,255,0.7)';">×</span>
                            </span>
                        @endif
                        @if ($year)
                            <span
                                style="display: inline-flex; align-items: center; padding: 0.5rem 1rem; background: linear-gradient(135deg, #22255b 0%, #1a1d4a 100%); color: white; border-radius: 9999px; font-size: 0.875rem; font-weight: 500; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);">
                                {{ $year }}
                                <span
                                    style="margin-left: 0.75rem; color: rgba(255, 255, 255, 0.7); cursor: pointer; font-weight: 600; padding: 0.125rem 0.25rem; border-radius: 50%; transition: all 150ms ease;"
                                    onclick="clearParam('year')"
                                    onmouseover="this.style.backgroundColor='rgba(255,255,255,0.2)'; this.style.color='white';"
                                    onmouseout="this.style.backgroundColor='transparent'; this.style.color='rgba(255,255,255,0.7)';">×</span>
                            </span>
                        @endif
                    </div>
                @endif

                <!-- Filter Form -->
                <form method="get">
                    <input type="hidden" name="tab" value="{{ $activeTab }}">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 items-end">
                        <div>
                            <label
                                style="display: block; color: #6B7280; margin-bottom: 0.75rem; text-transform: uppercase; letter-spacing: 0.06em; font-size: 11px; font-weight: 600; font-family: 'Inter', sans-serif;">Month</label>
                            <select name="month"
                                style="height: 2.75rem; width: 100%; border: 2px solid #D1D5DB; border-radius: 0.75rem; padding: 0 1rem; font-size: 0.875rem; font-family: 'Inter', sans-serif; transition: all 150ms ease; background: white; appearance: none; background-image: url('data:image/svg+xml;utf8,<svg xmlns=\"http://www.w3.org/2000/svg\" fill=\"none\" viewBox=\"0 0 24 24\" stroke=\"%236B7280\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M19 9l-7 7-7-7\"/></svg>'); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 1rem;"
                                onfocus="this.style.borderColor='#4bbbed'; this.style.boxShadow='0 0 0 3px rgba(75, 187, 237, 0.1)';"
                                onblur="this.style.borderColor='#D1D5DB'; this.style.boxShadow='none';">
                                <option value="">All Months</option>
                                @for ($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" @selected($month == $m)>
                                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                                @endfor
                            </select>
                        </div>

                        <div>
                            <label
                                style="display: block; color: #6B7280; margin-bottom: 0.75rem; text-transform: uppercase; letter-spacing: 0.06em; font-size: 11px; font-weight: 600; font-family: 'Inter', sans-serif;">Year</label>
                            <select name="year"
                                style="height: 2.75rem; width: 100%; border: 2px solid #D1D5DB; border-radius: 0.75rem; padding: 0 1rem; font-size: 0.875rem; font-family: 'Inter', sans-serif; transition: all 150ms ease; background: white; appearance: none; background-image: url('data:image/svg+xml;utf8,<svg xmlns=\"http://www.w3.org/2000/svg\" fill=\"none\" viewBox=\"0 0 24 24\" stroke=\"%236B7280\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M19 9l-7 7-7-7\"/></svg>'); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 1rem;"
                                onfocus="this.style.borderColor='#4bbbed'; this.style.boxShadow='0 0 0 3px rgba(75, 187, 237, 0.1)';"
                                onblur="this.style.borderColor='#D1D5DB'; this.style.boxShadow='none';">
                                <option value="">All Years</option>
                                @for ($y = now()->year + 1; $y >= now()->year - 4; $y--)
                                    <option value="{{ $y }}" @selected($year == $y)>
                                        {{ $y }}</option>
                                @endfor
                            </select>
                        </div>

                        <div>
            <label style="display: block; color: #6B7280; margin-bottom: 0.75rem; text-transform: uppercase; letter-spacing: 0.06em; font-size: 11px; font-weight: 600; font-family: 'Inter', sans-serif;">Company</label>
            <select name="company"
                style="height: 2.75rem; width: 100%; border: 2px solid #D1D5DB; border-radius: 0.75rem; padding: 0 1rem; font-size: 0.875rem; font-family: 'Inter', sans-serif; transition: all 150ms ease; background: white; appearance: none; background-image: url('data:image/svg+xml;utf8,<svg xmlns=\"http://www.w3.org/2000/svg\" fill=\"none\" viewBox=\"0 0 24 24\" stroke=\"%236B7280\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M19 9l-7 7-7-7\"/></svg>'); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 1rem;"
                onfocus="this.style.borderColor='#4bbbed'; this.style.boxShadow='0 0 0 3px rgba(75, 187, 237, 0.1)';"
                onblur="this.style.borderColor='#D1D5DB'; this.style.boxShadow='none';">
                <option value="">All Companies</option>
                @foreach($companies as $c)
                    <option value="{{ $c }}" @selected($company == $c)>{{ $c }}</option>
                @endforeach
            </select>
        </div>

                        <div>
                            <button type="submit"
                                style="width: 100%; background: linear-gradient(135deg, #22255b 0%, #1a1d4a 100%); color: white; border-radius: 9999px; padding: 0.75rem 1.5rem; font-size: 0.875rem; font-weight: 600; transition: all 150ms ease; border: none; cursor: pointer; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1); font-family: 'Inter', sans-serif;"
                                onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1)';"
                                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 1px 3px 0 rgba(0, 0, 0, 0.1)';">
                                Apply Filters
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Tab Container -->
            @php $tabs = ['print'=>'KLTG','video'=>'Video','article'=>'Article','lb'=>'LB','em'=>'EM']; @endphp
            <div
                style="background: white; border-radius: 1rem; border: 2px solid #E5E7EB; margin-bottom: 2rem; overflow: hidden; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);">
                <div
                    style="display: flex; background: #F1F3F4; padding: 0.75rem; gap: 0.375rem; overflow-x: auto; scrollbar-width: none; -ms-overflow-style: none;">
                    @foreach ($tabs as $key => $label)
                        @php
                            $isActive = $activeTab === $key;
                            $baseStyle =
                                "display: inline-block; padding: 0.875rem 1.75rem; font-size: 0.875rem; font-weight: 600; text-decoration: none; border-radius: 0.75rem; white-space: nowrap; min-width: max-content; transition: all 200ms ease; font-family: 'Inter', sans-serif;";
                            $activeStyle = $isActive
                                ? 'color: #1C1E26; background: white; box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); border: 1px solid #E5E7EB;'
                                : 'color: #6B7280; background: transparent; border: 1px solid transparent;';
                            $hoverStyle = !$isActive
                                ? "onmouseover=\"this.style.backgroundColor='rgba(255,255,255,0.6)'; this.style.color='#374151';\" onmouseout=\"this.style.backgroundColor='transparent'; this.style.color='#6B7280';\""
                                : '';
                        @endphp
                        <a href="{{ route('coordinator.kltg.index', array_filter(['tab' => $key, 'month' => $month, 'year' => $year])) }}"
                            style="{{ $baseStyle }} {{ $activeStyle }}" {!! $hoverStyle !!}>
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Data Table -->
            @if ($rows->isEmpty())
                <div class="table-card">
                    <div class="empty-state">
                        <div class="empty-icon">
                            <svg class="w-8 h-8 lighter-ink" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="font-serif text-xl font-medium ink mb-2 text-balance">No entries found</h3>
                        <p class="soft-ink text-sm tracking-wide">No coordinator items found for the selected period.
                        </p>
                    </div>
                </div>
            @else
                <div class="table-card"
                    style="border: 2px solid #D1D5DB; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                    <div class="overflow-x-auto">
                        <table class="ledger-table"
                            style="border-collapse: separate; border-spacing: 0; width: 100%;">

                            <thead>
                                <tr>
                                    <th class="col-no text-right caps-header"
                                        style="background: #F3F4F6; border-bottom: 3px solid #D1D5DB; border-right: 2px solid #D1D5DB; padding: 1.25rem 1rem; font-weight: 700;">
                                        No</th>
                                    <th class="col-date caps-header"
                                        style="background: #F3F4F6; border-bottom: 3px solid #D1D5DB; border-right: 2px solid #D1D5DB; padding: 1.25rem 1rem; font-weight: 700;">
                                        Date Created</th>
                                    <th class="col-company caps-header"
                                        style="background: #F3F4F6; border-bottom: 3px solid #D1D5DB; border-right: 2px solid #D1D5DB; padding: 1.25rem 1rem; font-weight: 700;">
                                        Company</th>
                                    <th class="col-person caps-header"
                                        style="background: #F3F4F6; border-bottom: 3px solid #D1D5DB; border-right: 2px solid #D1D5DB; padding: 1.25rem 1rem; font-weight: 700;">
                                        Person In Charge</th>
                                    @foreach ($columns[$activeTab] as $col)
                                        @php
                                            $isNumeric = in_array($col['key'], [
                                                'x',
                                                'edition',
                                                'publication',
                                                'em_qty',
                                            ]);
                                            $headerStyle =
                                                'background: #F3F4F6; border-bottom: 3px solid #D1D5DB; border-right: 2px solid #D1D5DB; padding: 1.25rem 1rem; font-weight: 700;';
                                            if ($isNumeric) {
                                                $headerStyle .= ' text-align: right;';
                                            }
                                        @endphp
                                        <th class="col-standard caps-header {{ $isNumeric ? 'text-right' : '' }}"
                                            style="{{ $headerStyle }}">
                                            @if ($col['key'] === 'x')
                                                X
                                            @elseif($col['key'] === 'em_qty')
                                                Quantity
                                            @else
                                                {{ $col['label'] }}
                                            @endif
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rows as $i => $r)
                                    <tr style="transition: all 150ms ease;"
                                        onmouseover="this.style.backgroundColor='#EBF8FF'"
                                        onmouseout="this.style.backgroundColor='white'">
                                        <td class="col-no text-right soft-ink font-medium tabular-nums"
                                            style="padding: 1.25rem 1rem; border-bottom: 1px solid #D1D5DB; border-right: 1px solid #D1D5DB; background: white;">
                                            {{ $i + 1 }}</td>
                                        <td class="col-date ink font-medium tabular-nums"
                                            style="padding: 1.25rem 1rem; border-bottom: 1px solid #D1D5DB; border-right: 1px solid #D1D5DB; background: white;">
                                            {{ optional($r->date ?? null)->format('Y-m-d') ?? optional($r->created_at)->format('Y-m-d') }}
                                        </td>
                                        <td class="col-company ink font-medium"
                                            style="padding: 1.25rem 1rem; border-bottom: 1px solid #D1D5DB; border-right: 1px solid #D1D5DB; background: white;">
                                            <div class="truncate" title="{{ $r->company_name }}">
                                                {{ $r->company_name }}</div>
                                        </td>
                                        <td class="col-person ink"
                                            style="padding: 1.25rem 1rem; border-bottom: 1px solid #D1D5DB; border-right: 1px solid #D1D5DB; background: white;">
                                            {{ $r->client }}</td>

                                        @foreach ($columns[$activeTab] as $col)
                                            @php
                                                $key = $col['key'];
                                                $type = $col['type'];
                                                $isNumeric = in_array($key, ['x', 'edition', 'publication', 'em_qty']);
                                                $cellStyle =
                                                    'padding: 1.25rem 1rem; border-bottom: 1px solid #D1D5DB; border-right: 1px solid #D1D5DB; background: white;';
                                                if ($isNumeric) {
                                                    $cellStyle .= ' text-align: right;';
                                                }
                                            @endphp

                                            @if ($key === 'edition')
                                                <td class="col-standard {{ $isNumeric ? 'text-right' : '' }}"
                                                    style="{{ $cellStyle }}">
                                                    @if ($r->edition)
                                                        <span class="badge">{{ $r->edition }}</span>
                                                    @else
                                                        <span class="placeholder-dash">—</span>
                                                    @endif
                                                </td>
                                            @elseif ($key === 'publication')
                                                <td class="col-standard {{ $isNumeric ? 'text-right' : '' }}"
                                                    style="{{ $cellStyle }}">
                                                    @if ($r->publication)
                                                        <span class="badge">{{ $r->publication }}</span>
                                                    @else
                                                        <span class="placeholder-dash">—</span>
                                                    @endif
                                                </td>
                                            @else
                                                @php $val = cellVal($existing, $r, $key, $type, $activeTab); @endphp
                                                <td class="col-standard {{ $isNumeric ? 'text-right' : '' }}"
                                                    style="{{ $cellStyle }}">
                                                    @if ($type === 'date')
                                                        <input type="date"
                                                            class="table-input {{ $isNumeric ? 'text-right' : '' }} tabular-nums"
                                                            value="{{ $val }}"
                                                            data-master-file-id="{{ $r->id }}"
                                                            data-subcategory="{{ ['print'=>'KLTG','video'=>'VIDEO','article'=>'ARTICLE','lb'=>'LB','em'=>'EM'][$activeTab] }}"
                                                            data-field="{{ $key }}" />
                                                    @else
                                                        <input type="text"
                                                            class="table-input {{ $isNumeric ? 'text-right tabular-nums' : '' }}"
                                                            value="{{ $val }}" placeholder="—"
                                                            data-master-file-id="{{ $r->id }}"
                                                            data-subcategory="{{ $activeTab }}"
                                                            data-field="{{ $key }}" />
                                                    @endif
                                                </td>
                                            @endif
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>
    </div>

    <script>
        // Clear filter parameter
        function clearParam(param) {
            const url = new URL(window.location);
            url.searchParams.delete(param);
            window.location.href = url.toString();
        }

        // Export form sync
        function syncExportForm() {
            const mSel = document.querySelector('select[name="month"]');
            const ySel = document.querySelector('select[name="year"]');
            document.getElementById('exportMonth').value = mSel?.value ?? '';
            document.getElementById('exportYear').value = ySel?.value ?? '';
        }

        document.addEventListener('DOMContentLoaded', function() {
            const mSel = document.querySelector('select[name="month"]');
            const ySel = document.querySelector('select[name="year"]');
            mSel?.addEventListener('change', syncExportForm);
            ySel?.addEventListener('change', syncExportForm);
            syncExportForm();
        });

        window.KLTG = {
            upsertUrl: @json(route('coordinator.kltg.upsert')),
            csrf: @json(csrf_token())
        };

        // Autosave functionality
        (async function() {
            const upsertUrl = window.KLTG?.upsertUrl;
            const csrf = window.KLTG?.csrf;

            if (!upsertUrl || !csrf) {
                console.error('[KLTG] Missing upsertUrl or CSRF');
                return;
            }

            function getYearMonth() {
                const ySel = document.querySelector('select[name="year"]');
                const mSel = document.querySelector('select[name="month"]');
                let year = ySel?.value ?? '';
                let month = mSel?.value ?? '';

                year = String(year ?? '').replace(/[^0-9]/g, '');
                const yNum = Number(year || 0);
                const mNum = Number(month || 0);

                return {
                    year: yNum,
                    month: mNum
                };
            }

            function requireConcreteMonth() {
                const {
                    year,
                    month
                } = getYearMonth();
                if (!month || month < 1 || month > 12) {
                    alert('Choose the month first before editing.');
                    return null;
                }
                if (!year || year < 1900) {
                    alert('Year not valid.');
                    return null;
                }
                return {
                    year,
                    month
                };
            }
// ▼ Tambah di atas buildPayload (sebelum dipakai)
const COLMAP = {
  // KLTG/Print dates
  artwork_reminder_date: 'artwork_reminder',
  material_received_date: 'material_record',
  artwork_done_date: 'artwork_done',
  send_chop_sign_date: 'send_chop_sign',
  chop_sign_approval_date: 'chop_sign_approval',
  park_in_server_date: 'park_in_file_server',

  // Video/Article/LB dates
  video_done_date: 'video_done',
  pending_approval_date: 'pending_approval',
  video_approved_date: 'video_approved',
  video_scheduled_date: 'video_scheduled',
  video_posted_date: 'video_posted',
  article_done_date: 'article_done',
  article_approved_date: 'article_approved',
  article_scheduled_date: 'article_scheduled',
  article_posted_date: 'article_posted',

  // Texts/links
  material_reminder_text: 'material_reminder_text',
  post_link: 'post_link',
  blog_link: 'blog_link',

  // EM
  em_date_write: 'em_date_write',
  em_date_to_post: 'em_date_to_post',
  em_post_date: 'em_post_date',
  em_qty: 'em_qty',
};

// ▼ Update buildPayload: column pakai map
function buildPayload(el) {
  const masterId = Number(el.dataset.masterFileId);
  const subcategory = el.dataset.subcategory;
  const field = el.dataset.field;

  if (!masterId || !subcategory || !field) return null;

  const value = el.value ?? '';
  const ym = requireConcreteMonth();
  if (!ym) return null;

  const column = COLMAP[field] || field;

  return {
    master_file_id: masterId,
    subcategory: subcategory,
    year: ym.year,
    month: ym.month,
    field: field,    // biarin sebagai meta UI
    column: column,  // ← ini yang dipakai server untuk DB
    value: value
  };
}


            const inputs = document.querySelectorAll('[data-master-file-id][data-field]');

            inputs.forEach(el => {
                el.addEventListener('change', () => save(el));
                el.addEventListener('blur', () => save(el));
            });

            async function save(el) {
                const payload = buildPayload(el);
                if (!payload) return;

                el.classList.remove('error', 'saved');
                el.classList.add('saving');

                try {
                    const resp = await fetch(upsertUrl, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify(payload)
                    });

                    if (!resp.ok) {
                        throw new Error(`HTTP ${resp.status}`);
                    }

                    el.classList.remove('saving');
                    el.classList.add('saved');

                    // Remove saved class after 2 seconds
                    setTimeout(() => {
                        el.classList.remove('saved');
                    }, 2000);

                } catch (e) {
                    el.classList.remove('saving');
                    el.classList.add('error');
                }
            }
        })();
    </script>

</x-app-shell>
