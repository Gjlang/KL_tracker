<?php if (isset($component)) { $__componentOriginal9144295cee351e372dbe9bffc4f13bc5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9144295cee351e372dbe9bffc4f13bc5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-shell','data' => ['title' => 'KLTG – Monthly Ongoing Job']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-shell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'KLTG – Monthly Ongoing Job']); ?>

  <?php $__env->startPush('head'); ?>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
  <?php $__env->stopPush(); ?>

  <!-- Page Container -->

  <div class="min-h-screen bg-[#F7F7F9]">

    

      <!-- Sticky Top Bar -->
<div class="sticky top-0 z-40 bg-white border-b hairline shadow-sm">
  <div class="px-6 py-4">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
      <!-- Title Section -->
      <div>
        <h1 class="font-serif text-2xl ink font-semibold tracking-tight">
          MONTHLY Ongoing Job – KL The Guide
        </h1>
        <p class="text-sm text-neutral-600 mt-1">Inline updates enabled</p>
      </div>

      <!-- Action Button Group -->
      <div class="flex flex-wrap items-center gap-2">


        
        <?php
          $showClone = (isset($hasAnyForYear) ? !$hasAnyForYear : false) && !empty($bestSourceYear);
        ?>
        <?php if($showClone): ?>
          <form method="POST" action="<?php echo e(route('kltg.cloneYear')); ?>"
                onsubmit="return confirm('Clone structure from <?php echo e($bestSourceYear); ?> into <?php echo e($activeYear); ?>? Values will be empty.');">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="year" value="<?php echo e($activeYear); ?>">
            <button type="submit" class="btn-primary">Clone from <?php echo e($bestSourceYear); ?></button>
          </form>
        <?php endif; ?>

        <a href="<?php echo e(route('kltg.exportMatrix', array_filter(request()->only([
          'year','filter_year','month','filter_month','q','search','status','start','end','date_from','date_to'
        ])))); ?>" class="btn-primary">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4"/>
          </svg>
          Export Excel
        </a>

        <a href="<?php echo e(route('coordinator.kltg.index')); ?>" class="btn-secondary">
          Open KLTG Coordinator
        </a>

        <a href="<?php echo e(route('dashboard')); ?>" class="btn-ghost">
          Dashboard
        </a>
      </div>
    </div>
  </div>
</div>

<!-- Main Content -->
<div class="px-6 py-6 space-y-6">

  <!-- Advanced Filters Card -->
  <div class="bg-white rounded-2xl border border-neutral-200/70 shadow-sm">
    <div class="p-6">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h3 class="font-serif text-lg ink font-medium">Advanced Filters</h3>
          <p class="text-sm text-neutral-600 mt-1">Refine your view with precision</p>
        </div>
        <button id="clear-filters" class="btn-ghost text-sm">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
          Clear All
        </button>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <!-- Month Filter (client-side visual filter only) -->
        <div class="space-y-2">
          <label for="filter-month" class="header-label">Month</label>
          <?php $mSel = (string)request('filter_month', ''); ?>
          <select id="filter-month" class="form-input">
            <option value="" <?php echo e($mSel===''?'selected':''); ?>>All Months</option>
            <?php $__currentLoopData = [
              'January','February','March','April','May','June',
              'July','August','September','October','November','December'
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($m); ?>" <?php echo e($mSel===$m?'selected':''); ?>><?php echo e($m); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>

        
        <form method="GET" action="<?php echo e(route('kltg.index')); ?>" class="flex items-center gap-2">
          <label for="active-year" class="text-sm text-neutral-600">Year</label>
          <select id="active-year" name="year" class="form-input" onchange="this.form.submit()">
            <?php
              $yNow = now('Asia/Kuala_Lumpur')->year;
              $years = range($yNow - 2, $yNow + 3);
              $activeYear = isset($activeYear) ? (int)$activeYear : (int)request('year', $yNow);
            ?>
            <?php $__currentLoopData = $years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($y); ?>" <?php echo e($activeYear === (int)$y ? 'selected' : ''); ?>><?php echo e($y); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
          
          <?php $__currentLoopData = request()->except(['year', '_token']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(is_array($v)): ?>
              <?php $__currentLoopData = $v; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <input type="hidden" name="<?php echo e($k); ?>[]" value="<?php echo e($vv); ?>">
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php elseif($v !== ''): ?>
              <input type="hidden" name="<?php echo e($k); ?>" value="<?php echo e($v); ?>">
            <?php endif; ?>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </form>
      </div>

      <!-- Active Filter Chips -->
      <div id="filter-summary" class="mt-4 hidden">
        <div class="flex flex-wrap items-center gap-2">
          <span class="header-label">Active:</span>
          <div id="active-filters-chips" class="flex flex-wrap gap-2"></div>
        </div>
      </div>
    </div>
  </div>
</div>


<script>
  document.getElementById('filter-year')?.addEventListener('change', (e) => {
    const params = new URLSearchParams(window.location.search);
    const v = e.target.value;
    if (v) params.set('year', v); else params.delete('year');
    // optionally preserve other filters:
    window.location = "<?php echo e(route('kltg.index')); ?>?" + params.toString();
  });
</script>


      <!-- Data Table Card -->
<div class="bg-white rounded-2xl border border-neutral-200/70 shadow-sm overflow-hidden">
  <!-- Table Container -->
  <div class="overflow-x-auto" style="max-height: 75vh;">
    <table class="min-w-[5500px] w-full text-sm border-collapse">
      <!-- Sticky Header (atas saja) -->
      <thead class="sticky top-0 z-20 bg-white">
        <tr class="bg-neutral-50/80">
          <th class="hairline px-4 py-3 header-label whitespace-nowrap text-center">No</th>
          <th class="hairline px-4 py-3 header-label whitespace-nowrap text-center">Created At</th>
            <th class="hairline px-4 py-3 header-label whitespace-nowrap text-center">Month</th>
            <th class="hairline px-4 py-3 header-label whitespace-nowrap text-center">Company</th>
            <th class="hairline px-4 py-3 header-label whitespace-nowrap text-center">Product</th>
            <th class="hairline px-4 py-3 header-label whitespace-nowrap text-center">Publication</th>
            <th class="hairline px-4 py-3 header-label whitespace-nowrap text-center">Edition</th>
            <th class="hairline px-4 py-3 header-label whitespace-nowrap text-center">Status</th>
            <th class="hairline px-4 py-3 header-label whitespace-nowrap text-center">Start</th>
            <th class="hairline px-4 py-3 header-label whitespace-nowrap text-center">End</th>

          <?php for($m=1; $m<=12; $m++): ?>
            <th class="px-4 py-3 text-center hairline bg-neutral-50/60 header-label min-w-[900px]">
              <?php echo e(\Carbon\Carbon::create()->startOfYear()->month($m)->format('F')); ?>

            </th>
          <?php endfor; ?>
        </tr>
      </thead>

      <tbody>
        <?php if(isset($rows) && count($rows) > 0): ?>
          <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr class="table-row transition-all duration-150 hover:bg-neutral-50 hover:shadow-[inset_0_0_0_1px_rgba(0,0,0,0.03)]"
                data-master="<?php echo e($r['id'] ?? ''); ?>"
                data-status="<?php echo e(strtolower($r['status'] ?? '')); ?>"
                data-company="<?php echo e(strtolower($r['company'] ?? '')); ?>"
                data-product="<?php echo e(strtolower($r['product'] ?? '')); ?>"
                data-year="<?php echo e($year ?? date('Y')); ?>"
                data-month="<?php echo e($r['month_name'] ?? ''); ?>"
                data-created-date="<?php echo e($r['created_at'] ?? ''); ?>">

              <!-- Kolom awal (tidak sticky) -->
              <td class="hairline px-4 py-3 align-top ink tabular-nums"><?php echo e($i+1); ?></td>
              <td class="hairline px-4 py-3 align-top ink tabular-nums"><?php echo e($r['created_at'] ?? ''); ?></td>
              <td class="hairline px-4 py-3 align-top ink"><?php echo e($r['month_name'] ?? ''); ?></td>

              <td class="hairline px-4 py-3 align-top ink" style="max-width:150px;">
                <div class="truncate pr-1" title="<?php echo e($r['company'] ?? ''); ?>"><?php echo e($r['company'] ?? 'N/A'); ?></div>
              </td>

              <td class="hairline px-4 py-3 align-top">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-neutral-100 text-neutral-800">
                  <?php echo e($r['product'] ?? 'N/A'); ?>

                </span>
              </td>

              <!-- Publication Input -->
              <td class="hairline px-4 py-3 align-top">
                <input
                  class="form-input auto-save-input w-32"
                  value="<?php echo e($r['publication'] ?? ''); ?>"
                  data-master="<?php echo e($r['id'] ?? ''); ?>"
                  data-year="<?php echo e($year ?? date('Y')); ?>"
                  data-category="KLTG"
                  data-type="PUBLICATION"
                  data-field="publication"
                  oninput="debouncedSave(this)"
                  placeholder="Type name…">
              </td>

              <!-- Edition Input -->
              <td class="hairline px-4 py-3 align-top">
                <input
                  class="form-input auto-save-input w-32"
                  value="<?php echo e($r['edition'] ?? ''); ?>"
                  data-master="<?php echo e($r['id'] ?? ''); ?>"
                  data-year="<?php echo e($year ?? date('Y')); ?>"
                  data-category="KLTG"
                  data-type="EDITION"
                  data-field="edition"
                  oninput="debouncedSave(this)"
                  placeholder="Type name…">
              </td>

              <!-- Status Badge -->
              <td class="hairline px-4 py-3 align-top">
                <span class="badge-<?php echo e(strtolower($r['status'] ?? 'pending')); ?>">
                  <?php echo e($r['status'] ?? 'Pending'); ?>

                </span>
              </td>

                <?php
                $fmt = function($v, $fallbackYear) {
                    if (!$v) return '';
                    try {
                    if ($v instanceof \Carbon\Carbon) return $v->format('d/m/Y');

                    $s = trim((string)$v);

                    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $s)) {
                        return \Carbon\Carbon::parse($s)->format('d/m/Y');          // 2026-08-07
                    }
                    if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $s)) {
                        return \Carbon\Carbon::createFromFormat('d/m/Y', $s)->format('d/m/Y'); // 07/08/2026
                    }
                    if (preg_match('/^\d{1,2}\/\d{1,2}$/', $s)) {
                        // only day/month provided → fall back to the row's year, not current year
                        return \Carbon\Carbon::createFromFormat('d/m/Y', $s.'/'.$fallbackYear)->format('d/m/Y');
                    }
                    } catch (\Throwable $e) {}
                    return $s;
                };

                $rowYear = (int)($r['year'] ?? $year ?? date('Y'));
                ?>

                <td class="hairline px-4 py-3 align-top ink tabular-nums"><?php echo e($fmt($r['start'] ?? null, $rowYear)); ?></td>
                <td class="hairline px-4 py-3 align-top ink tabular-nums"><?php echo e($fmt($r['end']   ?? null, $rowYear)); ?></td>



              <!-- Monthly Category Input Cells -->
              <?php for($m=1; $m<=12; $m++): ?>
                <?php
                  $cats = [
                    ['code' => 'KLTG',   'label' => 'KLTG'],
                    ['code' => 'VIDEO',  'label' => 'Video'],
                    ['code' => 'ARTICLE','label' => 'Article'],
                    ['code' => 'LB',     'label' => 'LB'],
                    ['code' => 'EM',     'label' => 'EM'],
                  ];
                ?>

                <td class="px-2 py-2 align-top hairline month-cell" data-month="<?php echo e($m); ?>">
                  <div class="min-w-[900px] border border-neutral-200 rounded-xl bg-white shadow-sm">


                    <div class="flex h-full">
                      <?php $__currentLoopData = $cats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex-1 flex flex-col <?php echo e($index < count($cats) - 1 ? 'border-r border-neutral-200' : ''); ?>">
                          <!-- Category Header -->
                          <div class="text-center py-2 bg-neutral-50/30 border-b border-neutral-200 flex-shrink-0">
                            <div class="header-label text-neutral-700"><?php echo e($c['label']); ?></div>
                          </div>

                          <!-- Input Container -->
                          <div class="flex flex-col flex-1 p-3 space-y-2">
                            <?php
                              $gridKey = sprintf('%02d_%s', $m, $c['code']);
                            ?>

                            <!-- Status Select -->
                            <select
                              class="form-input text-xs status-select"
                              data-input="text"
                              data-master="<?php echo e($r['id'] ?? ''); ?>"
                              data-year="<?php echo e($year ?? date('Y')); ?>"
                              data-month="<?php echo e($m); ?>"
                              data-category="<?php echo e($c['code']); ?>"
                              data-type="STATUS"
                              onchange="saveCell(this); setDropdownColor(this);">
                                <option value=""></option>
                                <option value="Installation" <?php echo e(($r['grid'][$gridKey]['status'] ?? '') == 'Installation' ? 'selected' : ''); ?>>Installation</option>
                                <option value="Dismantle" <?php echo e(($r['grid'][$gridKey]['status'] ?? '') == 'Dismantle' ? 'selected' : ''); ?>>Dismantle</option>
                                <option value="Artwork" <?php echo e(($r['grid'][$gridKey]['status'] ?? '') == 'Artwork' ? 'selected' : ''); ?>>Artwork</option>
                                <option value="Payment" <?php echo e(($r['grid'][$gridKey]['status'] ?? '') == 'Payment' ? 'selected' : ''); ?>>Payment</option>
                                <option value="Ongoing" <?php echo e(($r['grid'][$gridKey]['status'] ?? '') == 'Ongoing' ? 'selected' : ''); ?>>Ongoing</option>
                                <option value="Renewal" <?php echo e(($r['grid'][$gridKey]['status'] ?? '') == 'Renewal' ? 'selected' : ''); ?>>Renewal</option>
                                <option value="Completed" <?php echo e(($r['grid'][$gridKey]['status'] ?? '') == 'Completed' ? 'selected' : ''); ?>>Completed</option>
                                <option value="Material" <?php echo e(($r['grid'][$gridKey]['status'] ?? '') == 'Material' ? 'selected' : ''); ?>>Material</option>
                                <option value="Whatsapp" <?php echo e(($r['grid'][$gridKey]['status'] ?? '') == 'Whatsapp' ? 'selected' : ''); ?>>Whatsapp</option>
                                <option value="Posted" <?php echo e(($r['grid'][$gridKey]['status'] ?? '') == 'Posted' ? 'selected' : ''); ?>>Posted</option>
                            </select>

                            <?php
                              $inputIdStart = "date-start-y{$year}-m{$m}-{$c['code']}-{$r['id']}-" . uniqid();
                            ?>

                            <div class="flex items-center gap-2">
                              <input
                                id="<?php echo e($inputIdStart); ?>"
                                type="date"
                                class="form-input text-xs flex-1"
                                value="<?php echo e($r['grid'][$gridKey]['start'] ?? ''); ?>"
                                data-input="date"
                                data-master="<?php echo e($r['id'] ?? ''); ?>"
                                data-year="<?php echo e($year); ?>"
                                data-month="<?php echo e($m); ?>"
                                data-category="<?php echo e($c['code']); ?>"
                                data-type="START"
                                onchange="saveCell(this)">
                              <button type="button"
                                class="p-2 text-neutral-500 hover:text-neutral-700 transition-colors"
                                onclick="document.getElementById('<?php echo e($inputIdStart); ?>').showPicker()"
                                title="Start date">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                              </button>
                            </div>
                          </div>
                        </div>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                  </div>
                </td>
              <?php endfor; ?>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
          <!-- Empty State -->
          <tr>
            <td colspan="22" class="hairline px-6 py-16 text-center">
              <div class="flex flex-col items-center">
                <svg class="w-12 h-12 text-neutral-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="font-serif text-lg ink mb-2">No ongoing jobs found</h3>
                <p class="text-neutral-600 mb-4">Try adjusting your filters or add new entries.</p>
                <a href="<?php echo e(route('coordinator.kltg.index')); ?>" class="btn-secondary">
                  Open KLTG Coordinator
                </a>
              </div>
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

      </div>
    </div>
  </div>

  <!-- JavaScript (preserve all existing logic) -->
  <script>
    const UPDATE_URL = "<?php echo e(route('kltg.details.upsert')); ?>";

    // CSRF Token handling
    function getCSRFToken() {
      let token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      if (!token) {
        token = window.Laravel?.csrfToken || "<?php echo e(csrf_token()); ?>";
      }
      return token;
    }

    // Debounced save for all fields
    let saveTimeout;
    function debouncedSave(el) {
      const f = (el.dataset.field || '').toLowerCase();
      const t = (el.dataset.type || '').toUpperCase();

      if (f === 'publication' || t === 'PUBLICATION' || f === 'edition' || t === 'EDITION') {
        return savePublicationField(el);
      }
      return saveCell(el);
    }

    // Publication field save
    function savePublicationField(el) {
      const csrfToken = getCSRFToken();
      const master = parseInt(el.dataset.master, 10);
      const year   = parseInt(el.dataset.year, 10);
      const value  = (el.value || '').trim();
      const category = (el.dataset.category || 'KLTG').toUpperCase();
      const type     = (el.dataset.type || 'PUBLICATION').toUpperCase();

      let sentinelMonth;
      if (type === 'PUBLICATION') {
        sentinelMonth = 0;
      } else if (type === 'EDITION') {
        sentinelMonth = 0;
      } else {
        sentinelMonth = 1;
      }

      const payload = {
        master_file_id: master,
        year: year,
        month: sentinelMonth,
        category: category,
        type: type,
        field_type: 'text',
        value: value || null
      };

      // Add saving visual feedback
      el.classList.add('opacity-50');

      fetch(UPDATE_URL, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify(payload),
      })
      .then(r => r.json().then(j => (r.ok ? j : Promise.reject(j))))
      .then(() => {
        el.classList.remove('opacity-50');
        el.classList.add('ring-2','ring-[#4bbbed]');
        setTimeout(() => el.classList.remove('ring-2','ring-[#4bbbed]'), 800);
      })
      .catch(err => {
        console.error(err);
        el.classList.remove('opacity-50');
        el.classList.add('ring-2','ring-[#d33831]');
        setTimeout(() => el.classList.remove('ring-2','ring-[#d33831]'), 1200);
        alert(err?.message || 'Save failed');
      });
    }

    function normalizeDate(v, year) {
      const s = (v || '').trim();
      if (/^\d{4}-\d{2}-\d{2}$/.test(s)) return s;
      const m = s.match(/^(\d{1,2})\/(\d{1,2})(?:\/(\d{2,4}))?$/);
      if (!m) return null;
      const d = String(m[1]).padStart(2,'0');
      const mo= String(m[2]).padStart(2,'0');
      let yy  = m[3] ? String(m[3]) : String(year);
      if (yy.length===2) yy='20'+yy;
      return `${yy}-${mo}-${d}`;
    }

    function saveCell(el) {
      const field  = (el.dataset.field || '').toLowerCase();
      const tHint  = (el.dataset.type  || '').toUpperCase();

      if (field === 'publication' || tHint === 'PUBLICATION' || field === 'edition' || tHint === 'EDITION') {
        return savePublicationField(el);
      }

      const csrfToken = getCSRFToken();
      if (!csrfToken) {
        alert('CSRF token missing. Please refresh the page.');
        return;
      }

      let master = parseInt(el.dataset.master || (el.closest('tr')?.dataset.master ?? ''), 10);
      if (!Number.isInteger(master)) {
        alert('Error: Could not find master file ID. Please refresh the page.');
        return;
      }

      let year = parseInt(
        el.dataset.year ||
        document.querySelector('[data-active-year]')?.dataset.activeYear ||
        new Date().getFullYear(),
        10
      );

      const monthRaw = el.dataset.month || el.closest('[data-month]')?.dataset.month || el.closest('td')?.dataset.month || el.closest('div[data-month]')?.dataset.month;
      let month = parseInt(monthRaw, 10);

      let category = (el.dataset.category || el.closest('[data-category]')?.dataset.category || '').toUpperCase();

      const isDate = (el.dataset.input === 'date') || (el.type === 'date');
      let value    = (el.value ?? '').trim();
      if (value === '') value = null;

      let type = (tHint || (isDate ? 'START' : 'STATUS')).toUpperCase();

      if (isDate && value) {
        if (!/^\d{4}-\d{2}-\d{2}$/.test(value)) {
          const d = new Date(value);
          if (!isNaN(d.getTime())) value = d.toISOString().slice(0, 10);
        }
      }

      if (!Number.isInteger(year) || !Number.isInteger(month) || month < 1 || month > 12 || !category) {
        alert('Error: Missing required data (year/month/category). Please refresh the page.');
        return;
      }

      const payload = {
        master_file_id: master,
        year,
        month,
        category,
        type,
        field_type: isDate ? 'date' : 'text',
        value
      };

      el.classList.add('opacity-50');
      el.disabled = true;

      fetch(UPDATE_URL, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify(payload),
      })
      .then(response => response.text().then(text => {
        let data;
        try { data = JSON.parse(text); } catch (e) {
          throw new Error('Invalid JSON response');
        }
        if (!response.ok) throw new Error(data.message || `HTTP ${response.status}`);
        return data;
      }))
      .then(data => {
        el.classList.add('ring-2', 'ring-[#4bbbed]');
        setTimeout(() => el.classList.remove('ring-2', 'ring-[#4bbbed]'), 1000);
      })
      .catch(error => {
        alert('Save failed: ' + error.message);
        el.classList.add('ring-2', 'ring-[#d33831]');
        setTimeout(() => el.classList.remove('ring-2', 'ring-[#d33831]'), 2000);
      })
      .finally(() => {
        el.classList.remove('opacity-50');
        el.disabled = false;
      });
    }

    function setDropdownColor(selectEl) {
      const colors = {
        'Installation': '#fecaca',
        'Dismantle': '#fecaca',
        'Artwork': '#fef3c7',
        'Payment': '#fecaca',
        'Ongoing': '#bfdbfe',
        'Renewal': '#fecaca',
        'Completed': '#bbf7d0',
        'Material': '#fef3c7',
        'Whatsapp': '#bbf7d0',
        'Posted': '#bbf7d0'
      };
      const selected = selectEl.value;
      selectEl.style.backgroundColor = colors[selected] || '';
    }

    // Initialize dropdowns
    document.querySelectorAll('select[data-input="text"]').forEach(sel => {
      setDropdownColor(sel);
    });

    // Filter functionality
    document.addEventListener('DOMContentLoaded', function () {
      const monthFilter = document.getElementById('filter-month');
      const yearFilter = document.getElementById('filter-year');
      const clearFiltersBtn = document.getElementById('clear-filters');
      const filterSummary = document.getElementById('filter-summary');
      const activeFiltersChips = document.getElementById('active-filters-chips');

      function createChip(label, onRemove) {
        const chip = document.createElement('div');
        chip.className = 'chip';
        chip.innerHTML = `
          <span>${label}</span>
          <button class="ml-2 hover:text-[#d33831] transition-colors" onclick="${onRemove}">×</button>
        `;
        return chip;
      }

      function updateFilterSummary() {
        if (!filterSummary || !activeFiltersChips) return;

        activeFiltersChips.innerHTML = '';
        const hasFilters = (monthFilter?.value || yearFilter?.value);

        if (hasFilters) {
          filterSummary.classList.remove('hidden');

          if (monthFilter?.value) {
            const chip = createChip(`MONTH: ${monthFilter.value.toUpperCase()}`, `document.getElementById('filter-month').value = ''; filterTable();`);
            activeFiltersChips.appendChild(chip);
          }

          if (yearFilter?.value) {
            const chip = createChip(`YEAR: ${yearFilter.value}`, `document.getElementById('filter-year').value = ''; filterTable();`);
            activeFiltersChips.appendChild(chip);
          }
        } else {
          filterSummary.classList.add('hidden');
        }
      }

      function filterTable() {
        const rows = document.querySelectorAll('tbody tr.table-row');
        const mVal = (monthFilter?.value || '').trim();
        const yVal = (yearFilter?.value || '').trim();

        let visibleCount = 0;

        rows.forEach(row => {
          const rowYear = (row.dataset.year || '').trim();
          const rowMonth = (row.dataset.month || '').trim();

          const yearOK = !yVal || rowYear === yVal;
          const monthOK = !mVal || rowMonth === mVal;

          const show = yearOK && monthOK;
          row.style.display = show ? '' : 'none';
          if (show) visibleCount++;
        });

        updateFilterSummary();
      }

      // Event listeners
      if (monthFilter) monthFilter.addEventListener('change', filterTable);
      if (yearFilter) yearFilter.addEventListener('change', filterTable);
      if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', () => {
          if (monthFilter) monthFilter.value = '';
          if (yearFilter) yearFilter.value = '';
          filterTable();
        });
      }

      // Initial filter run
      filterTable();
    });
  </script>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9144295cee351e372dbe9bffc4f13bc5)): ?>
<?php $attributes = $__attributesOriginal9144295cee351e372dbe9bffc4f13bc5; ?>
<?php unset($__attributesOriginal9144295cee351e372dbe9bffc4f13bc5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9144295cee351e372dbe9bffc4f13bc5)): ?>
<?php $component = $__componentOriginal9144295cee351e372dbe9bffc4f13bc5; ?>
<?php unset($__componentOriginal9144295cee351e372dbe9bffc4f13bc5); ?>
<?php endif; ?>
<?php /**PATH C:\Users\Gjlang\kl_guide_tracker\resources\views\dashboard\kltg.blade.php ENDPATH**/ ?>