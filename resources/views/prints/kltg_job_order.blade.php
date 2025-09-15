<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Publication Job Order Sheet — KLTG</title>
<style>
  /* ===== PRINT / LAYOUT ===== */
  @page { size: A4; margin: 12mm; }
  body { font-family: Arial, Helvetica, sans-serif; font-size: 11.5px; color:#111; }
  .muted{ color:#666; }
  .title { font-weight:700; text-align:center; font-size:16px; margin:6px 0 8px; letter-spacing:.3px; }

  /* Basic table */
  table { width:100%; border-collapse:collapse; table-layout: fixed; }
  th, td { border:1px solid #000; padding:6px 6px; vertical-align:top; }
  th { background:#efefef; text-align:left; }

  /* Header brand bar */
  .brand-row { border:1px solid #000; padding:6px 8px; display:flex; align-items:center; justify-content:space-between; }
  .brand-left { font-size:12px; line-height:1.3; }
  .brand-left .name { font-size:16px; font-weight:700; }
  .logo { height:34px; }

  /* Two columns: left (70%) + right (30%) like photo */
  .two-col { width:100%; border:0; }
  .two-col td { border:0; vertical-align:top; }
  .left { width:69.5%; padding-right:8px; }
  .right{ width:30.5%; padding-left:0; }

  /* Mini cells */
  .cell { border:1px solid #000; padding:0; margin:0 0 6px; }
  .cell table { border-collapse:collapse; }
  .cell th, .cell td { border:1px solid #000; padding:6px; }

  /* Check cell (for small tick boxes like form) */
  .tick { display:inline-block; width:12px; height:12px; border:1px solid #000; margin:0 4px -2px 0; }
  .is-ticked{ background:#000; }

  /* Signatures */
  .sig-wrap { margin-top:10px; }
  .sig-table td { height:70px; border:1px dashed #777; position:relative; }
  .sig-label { position:absolute; left:6px; bottom:4px; font-size:10px; color:#666; }

  /* Utility */
  .no-border th, .no-border td { border:0; }
  .small { font-size:10px; }
</style>
</head>
<body>

  <!-- ==== HEADER (seperti foto: bar company + kontak) ==== -->
  <div class="brand-row">
    <div class="brand-left">
      <div class="name">Bluedale Publishing (M) Sdn. Bhd.</div>
      <div class="small muted">Tel: +603-7886 9219 &nbsp; • &nbsp; Fax: +603-7886 1219 &nbsp; • &nbsp; Email: enquiry@bluedale.com.my</div>
      <div class="small muted">Website: www.bluedale.com.my</div>
    </div>
    {{-- ganti path logo sesuai punya kamu --}}
    <img class="logo" src="{{ public_path(path: 'logo.png') }}" alt="Logo">
  </div>

  <div class="title">PUBLICATION JOB ORDER SHEET</div>

  <!-- ==== 2 COLUMN WRAPPER ==== -->
  <table class="two-col">
    <tr>
      <!-- ================= LEFT SIDE ================= -->
      <td class="left">

        <!-- Top info block (Client/Contact ... Job No/Order Date/Ref No) -->
        <div class="cell">
          <table>
            <tr>
              <th style="width:18%;">COMPANY</th>
              <td style="width:32%;">{{ $file->company }}</td>
              <th style="width:20%;">JOB ORDER NO</th>
              <td style="width:30%;">{{ $file->job_number }}</td>
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

        <!-- Product selection block (KL The Guide, KV 4 Locals, etc.) -->
        <div class="cell">
          <table>
            <tr>
              <th style="width:25%;">KL The Guide</th>
              <td style="width:25%;">
                <span class="tick {{ str_contains(strtolower($file->product ?? ''), 'guide') ? 'is-ticked' : '' }}"></span>
              </td>
              <th style="width:25%;">REFERENCE</th>
              <td style="width:25%;">{{ $file->reference_no2 ?? '' }}</td>
            </tr>
            <tr>
              <th>Klang Valley 4 Locals</th>
              <td>
                <span class="tick {{ str_contains(strtolower($file->product ?? ''), 'locals') ? 'is-ticked' : '' }}"></span>
              </td>
              <th>ISSUE</th>
              <td></td>
            </tr>
            <tr>
              <th>Listing</th>
              <td><span class="tick {{ str_contains(strtolower($file->product ?? ''), 'listing') ? 'is-ticked' : '' }}"></span></td>
              <th>&nbsp;</th>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <th>e-Guide</th>
              <td><span class="tick {{ str_contains(strtolower($file->product ?? ''), 'e-guide') ? 'is-ticked' : '' }}"></span></td>
              <th>&nbsp;</th>
              <td>&nbsp;</td>
            </tr>
          </table>
        </div>

        <!-- Requirements -->
        <div class="cell">
          <table>
            <tr>
              <th style="width:30%;">REQUIREMENTS</th>
              <td style="width:70%;">{!! nl2br(e($file->requirements ?? $file->remarks ?? '')) !!}</td>
            </tr>
            <tr>
              <th>ARTWORK DEADLINE</th>
              <td>{{ $file->artwork_deadline ?? '' }}</td>
            </tr>
          </table>
        </div>

        <!-- Contents grid (left wide box with many rows + middle remarks like the orange notes) -->
        <div class="cell" style="padding:0;">
          <table>
            <tr>
              <th style="width:22%;">Contents</th>
              <th style="width:43%;">Remarks / Notes</th>
              <th style="width:35%;">&nbsp;</th>
            </tr>
            @php
              $rows = [
                'IFC' => 'Prelim',
                'IBC' => 'Sightseeing',
                'BC'  => 'Dining',
                'DPS' => 'Beauty/Health & Spa',
                'FP'  => 'Night Life',
                'HP'  => 'Shopping',
                'QP'  => 'Accommodations',
                'LISTING' => 'Culture / Supplement',
                'WEB' => 'Outdoor Activity / Website',
              ];
            @endphp
            @foreach($rows as $code => $label)
              <tr>
                <td>{{ $code }}</td>
                <td>{{ $label }}</td>
                <td>{!! nl2br(e($file->notes ?? '')) !!}</td>
              </tr>
            @endforeach
          </table>
        </div>

        <!-- Signatures -->
        <div class="sig-wrap">
          <table class="sig-table" style="width:100%;">
            <tr>
              <td><span class="sig-label">Servicing — Signature</span></td>
              <td><span class="sig-label">Artwork — Signature</span></td>
              <td><span class="sig-label">Internal Team — Signature</span></td>
            </tr>
          </table>
          <table class="no-border" style="width:100%; margin-top:4px;">
            <tr>
              <td class="no-border" style="width:33%;">Name: ____________________</td>
              <td class="no-border" style="width:33%;">Name: ____________________</td>
              <td class="no-border" style="width:34%;">Name: ____________________</td>
            </tr>
            <tr>
              <td class="no-border">Date: {{ $date ?? '' }}</td>
              <td class="no-border">Date: </td>
              <td class="no-border">Date: </td>
            </tr>
          </table>
        </div>

      </td>

      <!-- ================= RIGHT SIDE ================= -->
      <td class="right">

        <!-- Right column block like “INSIDE FRONT COVER, INSIDE BACK COVER, DPS...” -->
        <div class="cell">
            <table border="1" cellspacing="0" cellpadding="6" style="border-collapse:collapse; width:100%; font-family:Arial, sans-serif; font-size:12px;">
                <tr><td><b>IFC</b></td><td>INSIDE FRONT COVER</td></tr>
                <tr><td><b>IBC</b></td><td>INSIDE BACK COVER</td></tr>
                <tr><td><b>IFCS</b></td><td>INSIDE FRONT COVER SPREAD</td></tr>
                <tr><td><b>IBCS</b></td><td>INSIDE BACK COVER SPREAD</td></tr>
                <tr><td><b>PG 1</b></td><td>PAGE 1</td></tr>
                <tr><td><b>BC</b></td><td>BACK COVER</td></tr>
                <tr><td><b>DPS</b></td><td>DOUBLE PAGE SPREAD</td></tr>
                <tr><td><b>FP</b></td><td>FULL PAGE</td></tr>
                <tr><td><b>HP</b></td><td>HALF PAGE</td></tr>
                <tr><td><b>QP</b></td><td>QUARTER PAGE</td></tr>
                <tr><td><b>WEB</b></td><td>WEBSITE</td></tr>
            </table>
        </div>
      </td>
    </tr>
  </table>

</body>
</html>
