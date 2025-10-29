<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Publication Job Order Sheet — KLTG</title>
<style>
  /* ===== PRINT / LAYOUT ===== */
  @page { size: A4; margin: 8mm; }
  * { box-sizing: border-box; }
  body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 10px;
    color:#111;
    margin: 0;
    padding: 0;
  }
  .muted{ color:#666; }
  .title {
    font-weight:700;
    text-align:center;
    font-size:14px;
    margin:5px 0 8px;
    letter-spacing:.3px;
  }

  /* Basic table */
  table { width:100%; border-collapse:collapse; }
  th, td { border:1px solid #000; padding:4px 5px; vertical-align:top; font-size:10px; }
  th { background:#efefef; text-align:left; font-weight:bold; }

  /* Header brand bar */
  .brand-row {
    border:1px solid #000;
    padding:5px 8px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    margin-bottom: 8px;
  }
  .brand-left { font-size:10px; line-height:1.2; }
  .brand-left .name { font-size:13px; font-weight:700; }
  .logo { height:30px; }

  /* Mini cells */
  .cell { border:1px solid #000; padding:0; margin:0 0 8px; }
  .cell table { border-collapse:collapse; }
  .cell th, .cell td { border:1px solid #000; padding:4px 5px; }

  /* Check cell */
  .tick { display:inline-block; width:10px; height:10px; border:1px solid #000; margin:0 3px -1px 0; }
  .is-ticked{ background:#000; }

  /* Layout: 3 columns */
  .three-col-wrapper {
    display: flex;
    gap: 4px;
    margin-bottom: 4px;
  }
  .col-left { flex: 0 0 32%; }
  .col-middle { flex: 0 0 38%; }
  .col-right { flex: 0 0 30%; }

  /* Signatures */
  .sig-wrap { margin-top: 8px; }
  .sig-table { width:100%; }
  .sig-table td {
    height:50px;
    border:1px solid #000;
    position:relative;
    vertical-align: bottom;
    padding: 4px 5px;
  }
  .sig-label { font-size:9px; color:#666; }

  /* Utility */
  .no-border th, .no-border td { border:0; }
  .small { font-size:9px; }
</style>
</head>
<body>

  <!-- ==== HEADER ==== -->
  <div class="brand-row">
    <div class="brand-left">
      <div class="name">Bluedale Publishing (M) Sdn. Bhd.</div>
      <div class="small muted">Tel: +603-7886 9219 &nbsp; • &nbsp; Fax: +603-7886 1219 &nbsp; • &nbsp; Email: enquiry@bluedale.com.my</div>
      <div class="small muted">Website: www.bluedale.com.my</div>
    </div>
    <img class="logo" src="{{ public_path('logo.png') }}" alt="Logo">
  </div>

  <div class="title">PUBLICATION JOB ORDER SHEET</div>

  <!-- ==== TOP INFO BLOCK ==== -->
  <div class="cell">
    <table>
      <tr>
        <th style="width:12%;">CLIENT</th>
        <td style="width:21%;">{{ $file->company }}</td>
        <th style="width:15%;">JOB ORDER NO</th>
        <td style="width:19%;">{{ $file->job_number }}</td>
        <th style="width:10%;">PAYMENT</th>
        <td style="width:23%;"></td>
      </tr>
      <tr>
        <th>CONTACT PERSON</th>
        <td>{{ $file->client ?? '' }}</td>
        <th>JOB ORDER DATE</th>
        <td>{{ $file->created_at ? \Carbon\Carbon::parse($file->created_at)->format('d/m/Y') : '' }}</td>
        <td colspan="2" rowspan="3" style="vertical-align:top;">{{ $file->reference_no2 ?? '' }}</td>
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

  <!-- ==== PRODUCT SELECTION ==== -->
  <div class="cell">
    <table>
      <tr>
        <th style="width:12%;">PUBLICATION</th>
        <td style="width:21%;"></td>
        <th style="width:15%;">TYPE OF SECTION</th>
        <td style="width:19%;"></td>
        <th style="width:10%;">REMARKS</th>
        <td style="width:23%;"></td>
      </tr>
      <tr>
        <th>KL The Guide</th>
        <td><span class="tick {{ str_contains(strtolower($file->product ?? ''), 'guide') ? 'is-ticked' : '' }}"></span></td>
        <th>Prelim</th>
        <td></td>
        <td colspan="2" rowspan="10" style="vertical-align:top; font-size:8px;">{!! nl2br(e($file->notes ?? '')) !!}</td>
      </tr>
      <tr>
        <th>Klang Valley 4 Locals</th>
        <td><span class="tick {{ str_contains(strtolower($file->product ?? ''), 'locals') ? 'is-ticked' : '' }}"></span></td>
        <th>Sightseeing</th>
        <td></td>
      </tr>
      <tr>
        <th>Penang The Guide</th>
        <td><span class="tick"></span></td>
        <th>Medical</th>
        <td></td>
      </tr>
      <tr>
        <th>Ipoh The Guide</th>
        <td><span class="tick"></span></td>
        <th>Tourism/Health & Beauty</th>
        <td></td>
      </tr>
      <tr>
        <th>Others</th>
        <td></td>
        <th>Dining</th>
        <td></td>
      </tr>
      <tr>
        <th></th>
        <td></td>
        <th>Night Life</th>
        <td></td>
      </tr>
      <tr>
        <th></th>
        <td></td>
        <th>Shopping</th>
        <td></td>
      </tr>
      <tr>
        <th></th>
        <td></td>
        <th>Accommodations</th>
        <td></td>
      </tr>
      <tr>
        <th></th>
        <td></td>
        <th>Culture</th>
        <td></td>
      </tr>
      <tr>
        <th></th>
        <td></td>
        <th>Supplement</th>
        <td></td>
      </tr>
    </table>
  </div>

  <!-- ==== REQUIREMENTS & KLTG REMARKS ==== -->
  <div class="cell">
    <table>
      <tr>
        <th style="width:15%;">REQUIREMENTS</th>
        <td style="width:35%; font-size:10px;">{!! nl2br(e($file->requirements ?? '')) !!}</td>
        <th style="width:15%;">KLTG REMARKS</th>
        <td style="width:35%; font-size:10px;">
          @php
            $__map = [
              'Industry'                => 'kltg_industry',
              'X (Reach/Impressions)'   => 'kltg_x',
              'Edition'                 => 'kltg_edition',
              'Material (CBP)'          => 'kltg_material_cbp',
              'Print'                   => 'kltg_print',
              'Article'                 => 'kltg_article',
              'Video'                   => 'kltg_video',
              'Leaderboard'             => 'kltg_leaderboard',
              'QR Code'                 => 'kltg_qr_code',
              'Blog'                    => 'kltg_blog',
              'Email Marketing (eDM)'   => 'kltg_em',
              'Remarks (KLTG)'          => 'kltg_remarks',
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
            @foreach($__rows as [$lbl, $val])
              <strong>{{ $lbl }}:</strong>
              @if(str_contains(strtolower($lbl), 'remarks'))
                {!! nl2br(e($val)) !!}
              @else
                {{ $val }}
              @endif
              @if(!$loop->last)<br>@endif
            @endforeach
          @else
            -
          @endif
        </td>
      </tr>
      <tr>
        <th>Artwork Deadline</th>
        <td style="font-size:10px;">{{ $file->artwork_deadline ?? '' }}</td>
        <td colspan="2"></td>
      </tr>
    </table>
  </div>

  <!-- ==== COMBINED TABLE: TYPE OF ADVERT + TYPE OF SECTION + LEGEND ==== -->
  <div class="cell">
    <table>
      <tr>
        <th colspan="2" style="text-align:center; width:25%;">TYPE OF ADVERTISEMENT</th>
        <th style="width:20%;">TYPE OF SECTION</th>
        <th style="width:15%;">EDM</th>
        <th style="width:20%;">REMARKS</th>
        <th style="width:20%;"></th>
      </tr>
      <tr>
        <td style="width:8%;"><b>IFC</b></td>
        <td style="width:17%;"></td>
        <td style="font-size:10px;">Prelim</td>
        <td></td>
        <td style="font-size:10px;"></td>
        <td style="font-size:8px;"><b>IFC:</b> INSIDE FRONT COVER</td>
      </tr>
      <tr>
        <td><b>IBC</b></td>
        <td></td>
        <td style="font-size:10px;">Sightseeing</td>
        <td></td>
        <td style="font-size:10px;"></td>
        <td style="font-size:8px;"><b>IBC:</b> INSIDE BACK COVER</td>
      </tr>
      <tr>
        <td><b>IFCS</b></td>
        <td></td>
        <td style="font-size:10px;">Dining</td>
        <td></td>
        <td style="font-size:10px;"></td>
        <td style="font-size:8px;"><b>IFCS:</b> INSIDE FRONT COVER SPREAD</td>
      </tr>
      <tr>
        <td><b>IBCS</b></td>
        <td></td>
        <td style="font-size:10px;">Beauty/Health & Spa</td>
        <td></td>
        <td style="font-size:10px;"></td>
        <td style="font-size:8px;"><b>IBCS:</b> INSIDE BACK COVER SPREAD</td>
      </tr>
      <tr>
        <td><b>BC</b></td>
        <td></td>
        <td style="font-size:10px;">Night Life</td>
        <td></td>
        <td style="font-size:10px;"></td>
        <td style="font-size:8px;"><b>PG 1:</b> PAGE 1</td>
      </tr>
      <tr>
        <td><b>DPS</b></td>
        <td></td>
        <td style="font-size:10px;">Shopping</td>
        <td></td>
        <td style="font-size:10px;"></td>
        <td style="font-size:8px;"><b>BC:</b> BACK COVER</td>
      </tr>
      <tr>
        <td><b>FP</b></td>
        <td></td>
        <td style="font-size:10px;">Accommodations</td>
        <td></td>
        <td style="font-size:10px;"></td>
        <td style="font-size:8px;"><b>DPS:</b> DOUBLE PAGE SPREAD</td>
      </tr>
      <tr>
        <td><b>HP</b></td>
        <td></td>
        <td style="font-size:10px;">Culture / Supplement</td>
        <td></td>
        <td style="font-size:10px;"></td>
        <td style="font-size:8px;"><b>FP:</b> FULL PAGE</td>
      </tr>
      <tr>
        <td><b>QP</b></td>
        <td></td>
        <td style="font-size:10px;">Outdoor Activity / Website</td>
        <td></td>
        <td style="font-size:10px;"></td>
        <td style="font-size:8px;"><b>HP:</b> HALF PAGE</td>
      </tr>
      <tr>
        <td><b>LISTING</b></td>
        <td></td>
        <td style="font-size:10px;">Essential Information</td>
        <td></td>
        <td style="font-size:10px;"></td>
        <td style="font-size:8px;"><b>QP:</b> QUARTER PAGE</td>
      </tr>
      <tr>
        <td><b>WEB</b></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td style="font-size:8px;"><b>WEB:</b> WEBSITE</td>
      </tr>
    </table>
  </div>

  <!-- ==== SIGNATURES ==== -->
  <div class="sig-wrap">
    <table class="sig-table">
      <tr>
        <td style="width:33%;">
          <div class="sig-label">Servicing</div>
          <div style="margin-top:22px; font-size:9px;">Signature: _________________</div>
          <div style="font-size:9px;">Name: {{ $file->sales_person ?? '_________________' }}</div>
          <div style="font-size:9px;">Date: {{ $date ?? '_________________' }}</div>
        </td>
        <td style="width:34%;">
          <div class="sig-label">Artwork</div>
          <div style="margin-top:22px; font-size:9px;">Signature: _________________</div>
          <div style="font-size:9px;">Name: _________________</div>
          <div style="font-size:9px;">Date: _________________</div>
        </td>
        <td style="width:33%;">
          <div class="sig-label">Internal Team</div>
          <div style="margin-top:22px; font-size:9px;">Signature: _________________</div>
          <div style="font-size:9px;">Name: _________________</div>
          <div style="font-size:9px;">Date: _________________</div>
        </td>
      </tr>
    </table>
  </div>

</body>
</html>
