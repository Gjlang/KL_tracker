<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Publication Job Order Sheet — KLTG</title>
<style>
  /* ===== ONE-PAGE FIT WITH LARGER FONTS ===== */
  @page { size: A4; margin: 8mm; }
  * { box-sizing: border-box; }
  body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 11px;
    color:#111;
    margin: 0;
    padding: 0;
    line-height: 1.3;
  }
  .muted{ color:#666; }
  .title {
    font-weight:700;
    text-align:center;
    font-size:16px;
    margin:5px 0 6px;
    letter-spacing:.3px;
  }

  /* Basic table */
  table { width:100%; border-collapse:collapse; }
  th, td { border:1px solid #000; padding:3px 4px; vertical-align:top; font-size:11px; }
  th { background:#efefef; text-align:left; font-weight:bold; }

  /* Header brand bar */
  .brand-row {
    border:1px solid #000;
    padding:5px 8px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    margin-bottom: 6px;
  }
  .brand-left { display:flex; align-items:center; gap:10px; }
  .brand-right { font-size:9px; line-height:1.4; text-align:right; }
  .brand-right .name { font-size:13px; font-weight:700; margin-bottom:2px; }
  .logo { height:32px; }

  /* Blocks */
  .cell { border:1px solid #000; padding:0; margin:0 0 5px; }
  .cell table { border-collapse:collapse; }
  .cell th, .cell td { border:1px solid #000; padding:3px 4px; }

  /* Check cell */
  .tick { display:inline-block; width:11px; height:11px; border:1px solid #000; margin:0 3px -1px 0; }
  .is-ticked{ background:#000; }

  /* Signatures */
  .sig-wrap { margin-top: 5px; }
  .sig-table { width:100%; }
  .sig-table td {
    height:80px;
    border:1px solid #000;
    position:relative;
    vertical-align: bottom;
    padding: 4px 6px;
  }
  .sig-label { font-size:15px; color:#666; margin-bottom: 20px; font-weight:bold; }

  /* Matrix 3 columns side-by-side */
  .matrix-container {
    display: table;
    width: 100%;
    border-collapse: collapse;
  }
  .matrix-row {
    display: table-row;
  }
  .matrix-cell {
    display: table-cell;
    vertical-align: top;
    border: 1px solid #000;
  }
  .matrix-cell-left { width: 22%; }
  .matrix-cell-middle { width: 53%; }
  .matrix-cell-right { width: 25%; }

  /* Left: Advertisement types */
  .adv-table { width:100%; border-collapse:collapse; }
  .adv-table th, .adv-table td { border:1px solid #000; padding:2px 3px; font-size:9px; height:0.55cm; }
  .adv-table .chk-col { width:15%; text-align:right; }
  .adv-table .name-col { text-align:left; }
  .adv-table th { background:#efefef; text-align:center; font-weight:bold; }

  /* Middle: Sections */
  .section-table { width:100%; border-collapse:collapse; }
  .section-table td { border:1px solid #000; padding:2px 3px; font-size:9px; height:0.55cm; }
  .section-table .chk-col { width:8%; text-align:right; }
  .section-table .name-col { width:50%; text-align:left; }
  .section-table .blank-col { width:42%; }

/* Right: Legend */
  .legend-table { width:100%; border-collapse:collapse; }
  .legend-table th, .legend-table td { border:0; padding:3px 4px; font-size:12.5px; line-height:1.6; }
  .legend-table th { background:#efefef; border:1px solid #000; border-bottom:0; font-weight:bold; text-align:center; }
  .legend-table td { border:1px solid #000; border-top:0; }

  /* Prevent breaks */
  @media print {
    .brand-row, .cell, .matrix-container, .sig-wrap { page-break-inside: avoid !important; }
  }
</style>
</head>
<body>

  <!-- HEADER -->
  <div class="brand-row">
    <div class="brand-left">
      <img class="logo" src="{{ public_path('logo.png') }}" alt="Logo">
    </div>
    <div class="brand-right">
      <div class="name">Bluedale Publishing (M) Sdn. Bhd.</div>
      <div class="muted">Tel: +603-7886 9219 • Fax: +603-7886 1219</div>
      <div class="muted">Email: enquiry@bluedale.com.my</div>
      <div class="muted">Website: www.bluedale.com.my</div>
    </div>
  </div>

  <div class="title">PUBLICATION JOB ORDER SHEET</div>

  <!-- TOP INFO -->
  <div class="cell">
    <table>
      <tr>
        <th style="width:15%;">CLIENT</th>
        <td style="width:35%;">{{ $file->company }}</td>
        <th style="width:18%;">JOB ORDER NO</th>
        <td style="width:32%;">{{ $file->job_number }}</td>
      </tr>
      <tr>
        <th>CONTACT PERSON</th>
        <td>{{ $file->client ?? '' }}</td>
        <th>JOB ORDER DATE</th>
        <td>{{ $file->created_at ? \Carbon\Carbon::parse($file->created_at)->format('d/m/Y') : '' }}</td>
      </tr>
      <tr>
        <th>CONTACT NUMBER</th>
        <td>{{ $file->contact_number ?? '' }}</td>
        <th>SALES PERSON</th>
        <td>{{ $file->sales_person ?? '' }}</td>
      </tr>
      <tr>
        <th>EMAIL</th>
        <td>{{ $file->email ?? '' }}</td>
        <th>REFERENCE NUMBER</th>
        <td>{{ $file->reference_no ?? '' }}</td>
      </tr>
    </table>
  </div>

  <!-- PUBLICATION SELECTION -->
  <div class="cell">
 <table>
  <tr>
    <th style="width:25%;">PUBLICATION</th>
    <td style="width:5%;"></td>
    <th colspan="2" style="text-align:center;">EDITION</th>
  </tr>
  <tr>
    <th>KL The Guide</th>
    <td>
      <span class="tick {{ str_contains(strtolower($file->product ?? ''), 'guide') ? 'is-ticked' : '' }}"></span>
    </td>
    <td colspan="2" rowspan="5" style="vertical-align:top; font-size:10px; padding:6px;">
      {{ strtoupper($file->kltg_edition ?? '') }}
    </td>
  </tr>
  <tr>
    <th>Klang Valley 4 Locals</th>
    <td>
      <span class="tick {{ str_contains(strtolower($file->product ?? ''), 'locals') ? 'is-ticked' : '' }}"></span>
    </td>
  </tr>
  <tr><th>Penang The Guide</th><td><span class="tick"></span></td></tr>
  <tr><th>Ipoh The Guide</th><td><span class="tick"></span></td></tr>
  <tr><th>Others</th><td></td></tr>
</table>


  </div>

  <!-- KLTG REMARKS -->
  @php
    $__map = [
      'Industry' => 'kltg_industry',
      'X (Reach/Impressions)' => 'kltg_x',
      'Edition' => 'kltg_edition',
      'Material (CBP)' => 'kltg_material_cbp',
      'Print' => 'kltg_print',
      'Article' => 'kltg_article',
      'Video' => 'kltg_video',
      'Leaderboard' => 'kltg_leaderboard',
      'QR Code' => 'kltg_qr_code',
      'Blog' => 'kltg_blog',
      'Email Marketing (eDM)' => 'kltg_em',
      'Remarks (KLTG)' => 'kltg_remarks',
    ];
    $__rows = [];
    foreach ($__map as $__label => $__attr) {
        $v = $file->{$__attr} ?? null;
        if (isset($v) && trim((string)$v) !== '') {
            $__rows[] = [$__label, $v];
        }
    }
    if (!empty($file->remarks)) {
        $__rows[] = ['General Remarks', $file->remarks];
    }
  @endphp

  @if(!empty($__rows))
  <div class="cell">
    <table>
      <tr>
        <th style="width:15%;">KLTG REMARKS</th>
        <td style="font-size:10px;">
          @foreach($__rows as [$lbl, $val])
            <strong>{{ $lbl }}:</strong>
            @if(str_contains(strtolower($lbl), 'remarks'))
              {!! nl2br(e($val)) !!}
            @else
              {{ $val }}
            @endif
            @if(!$loop->last)<br>@endif
          @endforeach
        </td>
      </tr>
    </table>
  </div>
  @endif

  <!-- MATRIX: 3 COLUMNS SIDE-BY-SIDE -->
  <div class="cell" style="padding:0;">
    <div class="matrix-container">
      <div class="matrix-row">

        <!-- LEFT: Advertisement Types -->
        <div class="matrix-cell matrix-cell-left">
          <table class="adv-table">
            <tr><th colspan="2">TYPE OF ADVERTISEMENT</th></tr>
            <tr><td class="name-col"><b>IFC</b></td><td class="chk-col"></td></tr>
            <tr><td class="name-col"><b>IBC</b></td><td class="chk-col"></td></tr>
            <tr><td class="name-col"><b>IFCS</b></td><td class="chk-col"></td></tr>
            <tr><td class="name-col"><b>IBCS</b></td><td class="chk-col"></td></tr>
            <tr><td class="name-col"><b>BC</b></td><td class="chk-col"></td></tr>
            <tr><td class="name-col"><b>DPS</b></td><td class="chk-col"></td></tr>
            <tr><td class="name-col"><b>FP</b></td><td class="chk-col"></td></tr>
            <tr><td class="name-col"><b>HP</b></td><td class="chk-col"></td></tr>
            <tr><td class="name-col"><b>QP</b></td><td class="chk-col"></td></tr>
            <tr><td class="name-col"><b>LISTING</b></td><td class="chk-col"></td></tr>
            <tr><td class="name-col"><b>WEB</b></td><td class="chk-col"></td></tr>
          </table>
        </div>

        <!-- MIDDLE: Sections with blank area -->
        <div class="matrix-cell matrix-cell-middle">
          <table class="section-table">
            <tr>
              <th colspan="2" style="text-align:center;">TYPE OF SECTION</th>
              <th class="blank-col" rowspan="14" style="vertical-align:top;"></th>
            </tr>
            <tr>
              <td class="name-col">Prelim</td>
              <td class="chk-col"></td>
            </tr>
            <tr><td>Sightseeing</td><td class="chk-col"></td></tr>
            <tr><td>Medical</td><td class="chk-col"></td></tr>
            <tr><td>Tourism/Health & Beauty</td><td class="chk-col"></td></tr>
            <tr><td>Eating Out</td><td class="chk-col"></td></tr>
            <tr><td>Night Life</td><td class="chk-col"></td></tr>
            <tr><td>Shopping</td><td class="chk-col"></td></tr>
            <tr><td>Accommodations</td><td class="chk-col"></td></tr>
            <tr><td>Culture</td><td class="chk-col"></td></tr>
            <tr><td>Supplement</td><td class="chk-col"></td></tr>
            <tr><td>Outdoor Activity</td><td class="chk-col"></td></tr>
            <tr><td>Essential Information</td><td class="chk-col"></td></tr>
            <tr><td>FOC Write Up</td><td class="chk-col"></td></tr>
          </table>
        </div>

        <!-- RIGHT: Legend -->
        <div class="matrix-cell matrix-cell-right">
          <table class="legend-table">
            <tr><th>LEGEND / POSITION</th></tr>
            <tr><td>
              <b>IFC:</b> INSIDE FRONT COVER<br>
              <b>IBC:</b> INSIDE BACK COVER<br>
              <b>IFCS:</b> INSIDE FRONT COVER SPREAD<br>
              <b>IBCS:</b> INSIDE BACK COVER SPREAD<br>
              <b>PG 1:</b> PAGE 1<br>
              <b>BC:</b> BACK COVER<br>
              <b>DPS:</b> DOUBLE PAGE SPREAD<br>
              <b>FP:</b> FULL PAGE<br>
              <b>HP:</b> HALF PAGE<br>
              <b>QP:</b> QUARTER PAGE<br>
              <b>WEB:</b> WEBSITE
            </td></tr>
          </table>
        </div>

      </div>
    </div>
  </div>

  <!-- SIGNATURES -->
  <div class="sig-wrap">
    <table class="sig-table">
      <tr>
        <td style="width:33%;">
          <div class="sig-label">Servicing</div>
          <div style="font-size:15px; margin-top:8px;">Signature: _______________</div>
          <div style="font-size:13px; margin-top:4px;">Name: {{ $file->sales_person ?? '_______________' }}</div>
          <div style="font-size:10px; margin-top:4px;">Date: {{ $date ?? '_______________' }}</div>
        </td>
        <td style="width:34%;">
          <div class="sig-label">Artwork</div>
          <div style="font-size:15px; margin-top:8px;">Signature: _______________</div>
          <div style="font-size:13px; margin-top:4px;">Name: _______________</div>
          <div style="font-size:10px; margin-top:4px;">Date: _______________</div>
        </td>
        <td style="width:33%;">
          <div class="sig-label">Internal Team</div>
          <div style="font-size:15px; margin-top:8px;">Signature: _______________</div>
          <div style="font-size:15px; margin-top:4px;">Name: _______________</div>
          <div style="font-size:10px; margin-top:4px;">Date: _______________</div>
        </td>
      </tr>
    </table>
  </div>

</body>
</html>
