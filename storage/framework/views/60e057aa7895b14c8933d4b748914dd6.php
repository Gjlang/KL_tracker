<?php use Illuminate\Support\Str; ?>

<?php
    /** @var \Illuminate\Support\Collection $existing */
    $existing = isset($existing) && $existing ? collect($existing) : collect();


    function omd($existing, $id, $m, $key, $type) {
        $row = $existing->get("{$id}:{$m}:{$key}");
        if (!$row) return '';

        if ($type === 'date') {
            $v = $row->value_date ?? null;
            if (!$v) return '';
            // If it's already 'YYYY-MM-DD', just return it
            if (is_string($v) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $v)) {
                return $v;
            }
            // If it's Carbon/DateTime or some other string, normalize
            try {
                return Carbon::parse($v)->format('Y-m-d');
            } catch (\Throwable $e) {
                return '';
            }
        }

        // text
        return $row->value_text ?? '';
    }

    // New display formatter function
    use Illuminate\Support\Carbon;

    function df($v, $fmt = 'd/m/Y') {
        if (empty($v)) return '';
        try {
            return ($v instanceof \DateTimeInterface)
                ? $v->format($fmt)
                : Carbon::parse($v)->format($fmt);
        } catch (\Throwable $e) {
            return ''; // or return (string)$v;
        }
    }
?>

<?php $__env->startPush('head'); ?>
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<style>
/* Style Tokens */
.ink { color: #1C1E26; }
.card { @apply bg-white rounded-2xl border border-neutral-200/70 shadow-sm; }
.hairline { border-color: #EAEAEA; }
.btn-primary { @apply bg-[#22255b] text-white hover:opacity-90 focus:ring-2 focus:ring-[#4bbbed] rounded-full px-5 py-2 transition-all duration-150; }
.btn-secondary { @apply border border-neutral-300 text-neutral-800 hover:bg-neutral-50 rounded-full px-5 py-2 transition-all duration-150; }
.btn-ghost { @apply text-neutral-700 hover:bg-neutral-50 rounded-full px-4 py-2 transition-all duration-150; }
.chip { @apply bg-neutral-100 text-neutral-700 px-3 py-1 rounded-full text-xs flex items-center gap-1; }
.tabular-nums { font-variant-numeric: tabular-nums; }

/* Typography */
.serif { font-family: 'Playfair Display', 'EB Garamond', serif; }
.sans { font-family: 'Inter', 'Proxima Nova', sans-serif; }

/* Table headers with small caps */
.table-header {
  @apply text-xs uppercase tracking-wider font-medium;
  color: #6B7280;
  letter-spacing: 0.05em;
}

/* Hover effects */
.hover-lift:hover {
  @apply shadow-sm;
  transform: translateY(-1px);
}

/* Focus rings */
.focus-ring:focus {
  @apply ring-1 ring-[#4bbbed]/20 outline-none;
}

/* Monthly grid specific styles */
.monthly-input {
  @apply h-10 text-xs rounded border border-neutral-200 focus:ring-1 focus:ring-[#4bbbed]/20 focus:border-[#4bbbed] transition-all duration-150;
}

.monthly-input:hover {
  @apply ring-1 ring-[#4bbbed]/20;
}
</style>
<?php $__env->stopPush(); ?>

<?php if (isset($component)) { $__componentOriginal4619374cef299e94fd7263111d0abc69 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4619374cef299e94fd7263111d0abc69 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-layout','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
  <div class="min-h-screen bg-[#F7F7F9]">
    <?php echo $__env->make('partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <main class="flex-1 overflow-y-auto">
      <div class="p-6 md:p-8 max-w-full">

        
        <div class="mb-8">
          <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <div>
              <h1 class="serif text-4xl font-light ink mb-2">Outdoor Monthly Ongoing Jobs</h1>
              <p class="sans text-neutral-600">Manage and track outdoor advertising campaigns</p>
            </div>
            <div>
              <a href="<?php echo e(route('dashboard')); ?>" class="btn-ghost">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                </svg>
                Dashboard
              </a>
            </div>
          </div>

          
          <?php if(session('status')): ?>
            <div class="mb-6 p-4 card bg-green-50 border-green-200 text-green-800">
              <div class="flex items-center">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <?php echo e(session('status')); ?>

              </div>
            </div>
          <?php endif; ?>
        </div>
        
<div class="mb-6 card">
  <div class="p-6">
    <form method="GET" action="<?php echo e(url()->current()); ?>">

      
      <input type="hidden" name="status" value="<?php echo e(request('status')); ?>">
      
      <input type="hidden" name="category" value="Outdoor">

      <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        
        <div class="space-y-2">
          <label class="sans table-header">Category</label>
          <div class="h-11 flex items-center">
            <span class="chip bg-[#22255b] text-white">OUTDOOR</span>
          </div>
        </div>

        
        <div class="space-y-2">
          <label for="year" class="sans table-header">Year</label>
          <?php
            $currentYear = (int) ($year ?? now()->year);
          ?>
          <select id="year" name="year" class="w-full h-11 sans rounded border border-neutral-200 focus-ring px-3">
            <?php $__currentLoopData = ($years ?? [now()->year]); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e((int)$y); ?>" <?php if((int)$y === $currentYear): echo 'selected'; endif; ?>><?php echo e((int)$y); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>

        
        <div class="space-y-2">
          <label for="month" class="sans table-header">Month</label>
          <?php
            $mSel = (int) (request('month') ?? 0);
            $monthNames = [1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec'];
          ?>
          <select id="month" name="month" class="w-full h-11 sans rounded border border-neutral-200 focus-ring px-3">
            <option value="0">All months</option>
            <?php $__currentLoopData = $monthNames; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mi => $mn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($mi); ?>" <?php if($mSel === $mi): echo 'selected'; endif; ?>><?php echo e($mn); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>

        
        <div class="space-y-2">
          <label for="product_category" class="sans table-header">Subproduct</label>
          <?php
            $subproducts = ['BB','TB','Newspaper','Bunting','Flyers','Star','Signages'];
            $pc = (string) request('product_category', '');
          ?>
          <select id="product_category" name="product_category" class="w-full h-11 sans rounded border border-neutral-200 focus-ring px-3">
            <option value="">All</option>
            <?php $__currentLoopData = $subproducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($opt); ?>" <?php if($pc === $opt): echo 'selected'; endif; ?>><?php echo e($opt); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>

        
        <div class="space-y-2">
          <label for="search" class="sans table-header">Search</label>
          <input id="search" name="search" type="text"
                 value="<?php echo e(request('search')); ?>"
                 class="w-full h-11 input sans"
                 placeholder="Company / product / site…">
        </div>
      </div>

      <div class="mt-4 flex flex-wrap gap-3">
        <button type="submit" class="btn-primary h-11">Apply Filters</button>
        <a href="<?php echo e(url()->current()); ?>" class="btn-secondary h-11">Clear All</a>
      </div>

      
      <?php
        $hasYear      = request()->filled('year');
        $hasMonth     = request()->filled('month') && (int)request('month') !== 0;
        $hasSearch    = trim((string)request('search')) !== '';
        $hasSubprod   = trim((string)request('product_category')) !== '';
      ?>

      <?php if($hasYear || $hasMonth || $hasSearch || $hasSubprod): ?>
        <div class="mt-4 flex flex-wrap items-center gap-2">
          <span class="sans text-sm text-neutral-600">Active:</span>

          <a class="chip" href="<?php echo e(request()->fullUrlWithQuery([
                'search'=>request('search'),
                'month'=>request('month'),
                'year'=>request('year'),
                'product_category'=>request('product_category'),
            ])); ?>">
            CATEGORY: OUTDOOR
          </a>

          <?php if($hasYear): ?>
            <a class="chip" href="<?php echo e(request()->fullUrlWithQuery(['year'=>null])); ?>">
              YEAR: <?php echo e((int)request('year')); ?> <span class="ml-1">×</span>
            </a>
          <?php endif; ?>

          <?php if($hasMonth): ?>
            <a class="chip" href="<?php echo e(request()->fullUrlWithQuery(['month'=>0])); ?>">
              MONTH: <?php echo e($monthNames[(int)request('month')] ?? ''); ?> <span class="ml-1">×</span>
            </a>
          <?php endif; ?>

          <?php if($hasSubprod): ?>
            <a class="chip" href="<?php echo e(request()->fullUrlWithQuery(['product_category'=>null])); ?>">
              SUBPRODUCT: <?php echo e(request('product_category')); ?> <span class="ml-1">×</span>
            </a>
          <?php endif; ?>

          <?php if($hasSearch): ?>
            <a class="chip" href="<?php echo e(request()->fullUrlWithQuery(['search'=>null])); ?>">
              SEARCH: “<?php echo e(Str::limit(request('search'), 20)); ?>” <span class="ml-1">×</span>
            </a>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </form>

    
    <?php if(($existing ?? collect())->isEmpty()): ?>
      <form method="POST" action="<?php echo e(route('coordinator.outdoor.cloneYear')); ?>" class="mt-3">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="to_year" value="<?php echo e((int)($year ?? now()->year)); ?>">
        <input type="hidden" name="from_year" value="<?php echo e((int)($year ?? now()->year) - 1); ?>">
        <button type="submit" class="btn btn-soft">
          Clone previous year’s structure (no values)
        </button>
      </form>
    <?php endif; ?>
  </div>
</div>

        </div>
        
        <div class="mb-6 card">
          <div class="p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <a href="<?php echo e(route('coordinator.outdoor.index')); ?>" class="btn-secondary">
              Outdoor Coordinator List
            </a>

             <a href="<?php echo e(route('outdoor.whiteboard.index')); ?>"
                class="inline-flex items-center px-4 py-2 rounded-lg bg-[#22255b] text-white hover:opacity-90">
                OUTDOOR Whiteboard
                </a>
            <a href="<?php echo e(route('coordinator.outdoor.exportMatrix', ['year' => $year])); ?>" class="btn-primary">
              Export CSV
            </a>
          </div>
        </div>

        
        <div class="card overflow-hidden">
          <?php if(($rows ?? [])->count() > 0): ?>
            <div class="overflow-x-auto">
              <table class="min-w-[3250px] w-full">
                <thead class="bg-neutral-50 sticky top-0 z-10">
                  <tr class="hairline border-b">
                    <th class="px-3 py-2 text-right w-12">NO</th>
                    <th class="px-4 py-4 text-left table-header" style="min-width:120px;">
                      Date Created
                    </th>
                    <th class="px-4 py-4 text-left table-header" style="min-width:220px;">
                      Company
                    </th>
                    <th class="px-4 py-4 text-left table-header" style="min-width:160px;">
                      Product
                    </th>
                    <th class="px-4 py-4 text-left table-header" style="min-width:220px;">
                      Site(s)
                    </th>
                    <th class="px-4 py-4 text-left table-header" style="min-width:140px;">
                      Category
                    </th>
                    <th class="px-4 py-4 text-left table-header" style="min-width:120px;">
                      Start Date
                    </th>
                    <th class="px-4 py-4 text-left table-header" style="min-width:120px;">
                      End Date
                    </th>
                    <?php
                        $monthLabels = [1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',
                                        7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December'];
                    ?>
                    <?php $__currentLoopData = $monthLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mNum => $mName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <th class="px-3 py-4 text-left table-header bg-neutral-100" style="min-width:180px;">
                        <?php echo e($mName); ?>

                      </th>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-neutral-200">
                  <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $company = $row->company ?? $row->client;
                        $start   = $row->start_date ?? $row->date ?? null;
                        $end     = $row->date_finish ?? $row->end_date ?? null;
                        $startDisp = df($start);
                        $endDisp   = df($end);
                    ?>
                    <tr class="hover:bg-neutral-50 hover-lift transition-all duration-150">
                    <td class="px-3 py-2 text-right tabular-nums"><?php echo e($loop->iteration); ?></td>
                      <td class="px-4 py-3 sans text-sm text-neutral-600 tabular-nums">
                        <?php echo e(df($row->created_at)); ?>

                      </td>
                      <td class="px-4 py-3 sans text-sm font-medium ink">
                        <div class="max-w-[200px] truncate" title="<?php echo e($company); ?>">
                          <?php echo e($company); ?>

                        </div>
                      </td>
                      <td class="px-4 py-3 sans text-sm text-neutral-700">
                        <?php echo e($row->product); ?>

                      </td>
                      <td class="px-4 py-3 sans text-sm text-neutral-700">
                        <?php echo e($row->site ?? '—'); ?>

                      </td>
                      <td class="px-4 py-3">
                        <span class="chip text-[#22255b] bg-[#22255b]/10">
                          <?php echo e($row->product_category ?? 'Outdoor'); ?>

                        </span>
                      </td>
                      <td class="px-4 py-3 sans text-sm text-neutral-700 tabular-nums">
                        <?php echo e($startDisp); ?>

                      </td>
                      <td class="px-4 py-3 sans text-sm text-[#d33831] tabular-nums font-medium">
                        <?php echo e($endDisp); ?>

                      </td>

                      
                      <?php $__currentLoopData = $monthLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mNum => $mName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                        <?php
                          $savedStatus = omd($existing, $row->outdoor_item_id, $mNum, 'status', 'text');
                          $savedDate   = omd($existing, $row->outdoor_item_id, $mNum, 'installed_on', 'date');
                        ?>
                        <td class="px-3 py-3 bg-neutral-50/50">
                          <div class="space-y-2">
                            <!-- Status dropdown -->
                            <select
                              class="status-dropdown w-full monthly-input text-xs"
                              data-master="<?php echo e($row->id); ?>"
                              data-item="<?php echo e($row->outdoor_item_id); ?>"
                              data-year="<?php echo e($year); ?>"
                              data-month="<?php echo e($mNum); ?>"
                              data-kind="text"
                              name="status_<?php echo e($row->id); ?>_<?php echo e($year); ?>_<?php echo e($mNum); ?>"
                              onchange="saveOutdoorCell(this); setDropdownColor(this);">
                                <option value="">Select status...</option>
                                <option value="Installation" <?php echo e($savedStatus==='Installation' ? 'selected' : ''); ?>>Installation</option>
                                <option value="Dismantle"   <?php echo e($savedStatus==='Dismantle'   ? 'selected' : ''); ?>>Dismantle</option>
                                <option value="Artwork"     <?php echo e($savedStatus==='Artwork'     ? 'selected' : ''); ?>>Artwork</option>
                                <option value="Payment"     <?php echo e($savedStatus==='Payment'     ? 'selected' : ''); ?>>Payment</option>
                                <option value="Ongoing"     <?php echo e($savedStatus==='Ongoing'     ? 'selected' : ''); ?>>Ongoing</option>
                                <option value="Renewal"     <?php echo e($savedStatus==='Renewal'     ? 'selected' : ''); ?>>Renewal</option>
                                <option value="Completed"   <?php echo e($savedStatus==='Completed'   ? 'selected' : ''); ?>>Completed</option>
                                <option value="Material"    <?php echo e($savedStatus==='Material'    ? 'selected' : ''); ?>>Material</option>
                            </select>

                            <!-- Date input -->
                            <input
                              type="date"
                              value="<?php echo e($savedDate); ?>"
                              class="w-full monthly-input text-xs tabular-nums"
                              data-master="<?php echo e($row->id); ?>"
                              data-item="<?php echo e($row->outdoor_item_id); ?>"
                              data-year="<?php echo e($year); ?>"
                              data-month="<?php echo e($mNum); ?>"
                              data-kind="date"
                              name="date_<?php echo e($row->id); ?>_<?php echo e($year); ?>_<?php echo e($mNum); ?>"
                              onblur="saveOutdoorCell(this)">

                            <!-- Status indicators -->
                            <div class="flex items-center justify-between text-xs">
                              <small class="hidden text-green-600" data-saved>Saved</small>
                              <small class="hidden text-[#d33831]" data-error></small>
                            </div>
                          </div>
                        </td>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tr>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            
            <div class="px-6 py-16 text-center">
              <svg class="w-12 h-12 text-neutral-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
              <p class="serif text-lg text-neutral-600 mb-2">No ongoing jobs found</p>
              <p class="sans text-sm text-neutral-500 mb-4">Try adjusting your filters or create new outdoor campaigns</p>
              <a href="<?php echo e(route('coordinator.outdoor.index')); ?>" class="btn-secondary">
                Outdoor Coordinator List
              </a>


            </div>



          <?php endif; ?>
        </div>

      </div>
    </main>
  </div>


 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4619374cef299e94fd7263111d0abc69)): ?>
<?php $attributes = $__attributesOriginal4619374cef299e94fd7263111d0abc69; ?>
<?php unset($__attributesOriginal4619374cef299e94fd7263111d0abc69); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4619374cef299e94fd7263111d0abc69)): ?>
<?php $component = $__componentOriginal4619374cef299e94fd7263111d0abc69; ?>
<?php unset($__componentOriginal4619374cef299e94fd7263111d0abc69); ?>
<?php endif; ?>

<script>
// ------- Small helpers -------
const getCsrf = () =>
  document.querySelector('meta[name="csrf-token"]')?.content || "<?php echo e(csrf_token()); ?>";

const toInt = (v) => {
  const n = Number(v);
  return Number.isFinite(n) ? n : null;
};

const normalizeDate = (v) => {
  // Accept "", null, undefined
  if (!v) return null;
  // If already YYYY-MM-DD (native date inputs), just return
  // You can add more parsing if your backend needs it
  return v;
};

async function saveOutdoorCell(el) {
  // Required IDs from data-attrs on the input/select
  const outdoor_item_id = toInt(el.dataset.item);
  if (!outdoor_item_id) {
    console.warn('saveOutdoorCell: missing data-item (outdoor_item_id).');
    return;
  }
  // Optional: still store master_file_id for joins/filtering if you like
  const master_file_id  = toInt(el.dataset.master);

  const year  = toInt(el.dataset.year);   // optional for monthly tables
  const month = toInt(el.dataset.month);  // optional for monthly tables
  const kind  = el.dataset.kind || 'text'; // "text" | "date"

  // Field mapping (adjust if your backend expects different keys)
  const payload = {
    outdoor_item_id,
    master_file_id,   // ok to be null
    year,             // ok to be null (if your monthly endpoint uses it)
    month,            // ok to be null
    field_key: kind === 'date' ? 'installed_on' : 'status', // <-- adjust if needed
    field_type: kind, // 'text' or 'date'
  };

  if (kind === 'date') {
    payload.value_date = normalizeDate(el.value);
  } else {
    payload.value_text = (el.value ?? '').toString();
  }

  const td = el.closest('td');
  const savedBadge = td?.querySelector('[data-saved]');
  const errorBadge = td?.querySelector('[data-error]');

  // reset badges
  if (errorBadge) { errorBadge.classList.add('hidden'); errorBadge.textContent = ''; }

  try {
    const res = await fetch("<?php echo e(route('outdoor.monthly.upsert')); ?>", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": getCsrf(),
        "Accept": "application/json",
      },
      body: JSON.stringify(payload),
    });

    // Try to parse JSON either way, for better messages
    let data = null;
    try { data = await res.json(); } catch (_) {}

    if (!res.ok || (data && data.ok === false)) {
      const msg = (data && (data.message || data.error)) || res.statusText || 'Save failed';
      throw new Error(msg);
    }

    // Success feedback
    savedBadge?.classList.remove('hidden');

    el.classList.remove('border-[#d33831]', 'bg-red-50');
    el.classList.add('border-green-400', 'bg-green-50');

    setTimeout(() => {
      savedBadge?.classList.add('hidden');
      el.classList.remove('border-green-400', 'bg-green-50');
      el.classList.add('border-neutral-200');
    }, 1500);

  } catch (e) {
    // Error feedback
    el.classList.remove('border-neutral-200');
    el.classList.add('border-[#d33831]', 'bg-red-50');
    if (errorBadge) {
      errorBadge.textContent = e?.message || 'Save failed';
      errorBadge.classList.remove('hidden');
    }
    setTimeout(() => el.classList.remove('bg-red-50'), 3000);
  }
}

// -------- Status dropdown color mapping (non-destructive to Tailwind classes) --------
function setDropdownColor(selectEl) {
  const colorMap = {
    'Installation': { bg:'#22255b', color:'#fff', border:'#22255b' },
    'Dismantle':    { bg:'#d33831', color:'#fff', border:'#d33831' },
    'Payment':      { bg:'#d33831', color:'#fff', border:'#d33831' },
    'Renewal':      { bg:'#d33831', color:'#fff', border:'#d33831' },
    'Completed':    { bg:'#16a34a', color:'#fff', border:'#16a34a' },
    'Artwork':      { bg:'#f97316', color:'#fff', border:'#f97316' },
    'Material':     { bg:'#f97316', color:'#fff', border:'#f97316' },
    'Ongoing':      { bg:'#4bbbed', color:'#1C1E26', border:'#4bbbed' },
  };

  // Preserve existing classes; only tweak inline styles
  const style = colorMap[selectEl.value];
  if (style) {
    selectEl.style.backgroundColor = style.bg;
    selectEl.style.color = style.color;
    selectEl.style.borderColor = style.border;
  } else {
    // Default visuals
    selectEl.style.backgroundColor = '#ffffff';
    selectEl.style.color = '#1C1E26';
    selectEl.style.borderColor = '#d4d4d8';
  }
}

// -------- Initialize on page load --------
document.addEventListener('DOMContentLoaded', function() {
  // Style preselected dropdowns + watch changes
  document.querySelectorAll('.status-dropdown').forEach(selectEl => {
    setDropdownColor(selectEl);
    selectEl.addEventListener('change', function() {
      setDropdownColor(this);
      // Optional: auto-save when status changes
      // saveOutdoorCell(this);
    });
  });

  // Nice focus transitions
  document.querySelectorAll('.monthly-input').forEach(input => {
    input.addEventListener('focus', function() {
      this.style.transform = 'translateY(-1px)';
      this.style.boxShadow = '0 4px 6px -1px rgba(0, 0, 0, 0.1)';
    });
    input.addEventListener('blur', function() {
      this.style.transform = 'translateY(0)';
      this.style.boxShadow = 'none';
    });
  });
});
</script>
<?php /**PATH C:\Users\Gjlang\kl_guide_tracker\resources\views/dashboard/outdoor.blade.php ENDPATH**/ ?>