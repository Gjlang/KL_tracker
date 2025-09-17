<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Summary {{ $filters['year'] }}</title>
    <style>
        *{font-family: DejaVu Sans, sans-serif; box-sizing:border-box}
        .wrap{padding:18px}
        .row{display:flex;gap:12px;flex-wrap:wrap}
        .col{flex:1 1 45%;border:1px solid #ddd;border-radius:12px;padding:12px}
        h1{font-size:20px;margin:0 0 6px}
        h2{font-size:14px;margin:0 0 8px}
        .big{font-size:28px;font-weight:700}
        .muted{color:#666;font-size:11px}
        ul{margin:6px 0 0;padding-left:14px}
        li{font-size:12px; margin:2px 0}
        .pair{display:flex;justify-content:space-between;font-size:12px;margin:3px 0}
    </style>
</head>
<body>
<div class="wrap">
    <h1>All-in-One Summary Report</h1>
    <div class="muted">Year: {{ $filters['year'] }} @if($filters['month']) | Month: {{ $filters['month'] }} @endif @if($filters['status']) | Status: {{ $filters['status'] }} @endif</div>

    <div class="row" style="margin-top:10px">
        <div class="col">
            <h2>Master File</h2>
            <div class="big">{{ $master['active_companies'] ?? 0 }}</div>
            <div class="muted">Active companies (distinct in {{ $filters['year'] }})</div>

            <h3 style="font-size:12px;margin-top:8px">By Category</h3>
            <ul>
                @forelse($master['by_category'] ?? [] as $row)
                    <li>{{ $row['category'] ?? 'Unknown' }} — <strong>{{ $row['total'] }}</strong></li>
                @empty
                    <li class="muted">No category column found.</li>
                @endforelse
            </ul>

            @php $sd = $master['status_dist'] ?? []; $tot = array_sum($sd); @endphp
            <h3 style="font-size:12px;margin-top:8px">Status Distribution</h3>
            <div class="pair"><span>Pending</span><span><strong>{{ $tot?round(($sd['pending']??0)/$tot*100):0 }}%</strong></span></div>
            <div class="pair"><span>In-progress</span><span><strong>{{ $tot?round(($sd['in-progress']??0)/$tot*100):0 }}%</strong></span></div>
            <div class="pair"><span>Completed</span><span><strong>{{ $tot?round(($sd['completed']??0)/$tot*100):0 }}%</strong></span></div>
        </div>

        <div class="col">
            <h2>Outdoor</h2>
            @if(($outdoor['available'] ?? false))
                <div class="pair"><span>Active jobs</span><span class="big" style="font-size:22px">{{ $outdoor['active_jobs'] }}</span></div>
                <div class="pair"><span>Completed (period)</span><span><strong>{{ $outdoor['completed_this'] }}</strong></span></div>
                <div class="pair"><span>Completion rate</span><span><strong>{{ $outdoor['completion_rate'] }}%</strong></span></div>
                <div class="pair"><span>Issues</span><span><strong>{{ $outdoor['issues'] }}</strong></span></div>
            @else
                <div class="muted">Table not found.</div>
            @endif
        </div>

        <div class="col">
            <h2>KLTG</h2>
            @if(($kltg['available'] ?? false))
                <div class="pair"><span>Production progress</span><span class="big" style="font-size:22px">{{ $kltg['production_progress'] }}%</span></div>
                <div class="pair"><span>Slots filled</span><span><strong>{{ $kltg['slots_filled'] }}</strong></span></div>
                <div class="pair"><span>Pending approvals</span><span><strong>{{ $kltg['pending_approvals'] }}</strong></span></div>
            @else
                <div class="muted">Table not found.</div>
            @endif
        </div>

        <div class="col">
            <h2>Media Social</h2>
            @if(($media['available'] ?? false))
                <div class="pair"><span>Campaigns</span><span class="big" style="font-size:22px">{{ $media['campaigns'] }}</span></div>
                <div class="pair"><span>Posts (period)</span><span><strong>{{ $media['posts'] }}</strong></span></div>
            @else
                <div class="muted">Table not found.</div>
            @endif
        </div>
    </div>

    <div class="muted" style="margin-top:8px">Generated at {{ $generated->timezone(config('app.timezone'))->format('M d, Y H:i') }}</div>
</div>
</body>
</html>
