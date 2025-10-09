<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Outdoor Job Order</title>
<style>
  @page { size: A4; margin: 12mm; }
  body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; color:#111; }

  /* Header */
  .bar {
    background:#000; color:#fff; padding:10px 12px;
    display:flex; align-items:center; justify-content:space-between;
    border:1px solid #000;
  }
  .company { line-height:1.35; }
  .company .name { font-weight:700; font-size:14px; }
  .small { font-size:10px; }
  .logo { height:36px; }

  /* Title row + updated row (no float!) */
  .title { text-align:center; font-weight:700; font-size:14px; border:1px solid #000; border-top:none; padding:6px 0; }
  .meta { width:100%; border-collapse:collapse; table-layout:fixed; }
  .meta td { border-left:1px solid #000; border-right:1px solid #000; padding:2px 6px; }
  .meta td.left { text-align:left; }
  .meta td.right { text-align:right; font-size:10px; color:#444; }

  /* Tables */
  table { width:100%; border-collapse:collapse; table-layout:fixed; }
  th, td { border:1px solid #000; padding:6px 7px; vertical-align:top; }
  th { background:#efefef; text-align:left; }

  /* Row heights (tidy look) */
  .job-rows td { height:28px; }     /* baris tabel tengah */
  .remarks td { height:110px; }     /* box remarks */

  /* Signatures */
  .sigs th { background:#efefef; text-align:center; }
  .sigs td { height:70px; }
  .sig-label { font-size:10px; color:#666; margin-top:3px; }

  /* Width helpers */
  .w20{width:20%}.w30{width:30%}.w40{width:40%}
</style>
</head>
<body>

  <!-- HEADER -->
  <div class="bar">
    <div class="company">
      <div class="name">Bluedale Media (M) Sdn. Bhd.</div>
      <div class="small">No. 312, Blok F2, Level 2, Jalan PJU 1/42A, Dataran Prima, 47301 Petaling Jaya, Selangor Darul Ehsan, Malaysia.</div>
      <div class="small">Tel: +603 - 7886 9219 | Fax: +603 - 7887 8212 | Website: www.bluedale.com.my | Email: enquiry@bluedale.com.my</div>
    </div>
    <img class="logo" src="<?php echo e(public_path('logo.png')); ?>" alt="Logo">
  </div>

  <!-- TITLE + META -->
  <div class="title">BILLBOARD / TEMPBOARD / BUNTING JOB ORDER</div>
  <table class="meta">
    <tr>
      <td class="left">&nbsp;</td>
      <td class="right">Updated: <?php echo e(now()->format('d/m/Y')); ?></td>
    </tr>
  </table>

  <!-- CLIENT & JOB INFO -->
  <table style="margin-top:6px;">
    <tr>
      <th class="w20">CLIENT</th>
      <td class="w30"><?php echo e($file->company); ?></td>
      <th class="w20">JOB ORDER NO.</th>
      <td class="w30"><?php echo e($file->job_number); ?></td>
    </tr>
    <tr>
      <th>CONTACT PERSON</th>
      <td><?php echo e($file->client ?? ''); ?></td>
      <th>JOB ORDER DATE</th>
        <td>
        <?php echo e($file->created_at ? \Carbon\Carbon::parse($file->created_at)->format('d/m/Y') : ''); ?>

        </td>
    </tr>
    <tr>
      <th>CONTACT NUMBER</th>
      <td><?php echo e($file->contact_number ?? ''); ?></td>
      <th>SALES PERSON</th>
      <td><?php echo e($file->traffic ?? ''); ?></td>
    </tr>
    <tr>
      <th>EMAIL</th>
      <td><?php echo e($file->email ?? ''); ?></td>
      <th>PROJECT TEAM</th>
      <td><?php echo e($file->project_team ?? ''); ?></td>
    </tr>
  </table>

  <!-- JOB TABLE -->
<table style="margin-top:6px;" class="job-rows">
  <tr>
    <th class="w40">SITE / LOCATION</th>
    <th class="w20">SIZE</th>
    <th class="w20">DURATION</th>
    <th class="w20">IN CHARGE DATE</th>
  </tr>

 <?php
  $total = max(6, $items->count()); // keep the layout (6 rows min)
?>

<?php for($i = 0; $i < $total; $i++): ?>
  <?php $it = $items[$i] ?? null; ?>
  <tr>
    <td><?php echo e($it->site ?? ''); ?></td>
    <td><?php echo e($it->size ?? ''); ?></td>

    
    <td>
      <?php if($it): ?>
        <?php echo e($file->duration ?? $file->month ?? ''); ?>

      <?php endif; ?>
    </td>

    
    <td>
      <?php if($it): ?>
        <?php if($it->start_date && $it->end_date): ?>
          <?php echo e($it->start_date->format('d/m/Y')); ?> - <?php echo e($it->end_date->format('d/m/Y')); ?>

        <?php elseif($it->start_date): ?>
          <?php echo e($it->start_date->format('d/m/Y')); ?>

        <?php elseif($it->end_date): ?>
          <?php echo e($it->end_date->format('d/m/Y')); ?>

        <?php endif; ?>
      <?php endif; ?>
    </td>
  </tr>
<?php endfor; ?>

</table>

  <!-- REMARKS -->
  <table style="margin-top:6px;" class="remarks">
    <tr><th>REMARKS</th></tr>
    <tr>
      <td><?php echo nl2br(e($file->remarks ?? '')); ?></td>
    </tr>
  </table>

  <!-- SIGNATURES -->
  <table style="margin-top:12px;" class="sigs">
    <tr>
      <th>Servicing</th>
      <th>Artwork</th>
      <th>Artwork</th>
      <th>Artwork</th>
    </tr>
    <tr>
      <td></td><td></td><td></td><td></td>
    </tr>
    <tr>
      <td class="sig-label">Signature / Name / Date</td>
      <td class="sig-label">Signature / Name / Date</td>
      <td class="sig-label">Signature / Name / Date</td>
      <td class="sig-label">Signature / Name / Date</td>
    </tr>
  </table>

</body>
</html>
<?php /**PATH D:\Projects\Laravel\KL_tracker\resources\views\prints\outdoor_job_order.blade.php ENDPATH**/ ?>