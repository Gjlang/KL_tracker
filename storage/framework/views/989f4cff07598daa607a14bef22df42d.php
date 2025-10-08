
<?php
    use Illuminate\Support\Str;
    use Carbon\Carbon;

    /** @var array|null $column_labels */
    /** @var array|null $dateColumns */
    /** @var array|null $editable       // e.g. ['company'=>'text','location'=>'text','date'=>'date'] */
    /** @var string|null $updateUrl     // e.g. route('clientele.inline.update') */
    /** @var array $updatePayloadExtra  // optional: extra keys to send (e.g. ['scope'=>'outdoor']) */

    $updateUrl = $updateUrl ?? url('/inline-update');      // fallback
    $updatePayloadExtra = $updatePayloadExtra ?? [];

    // default date columns
    $dateCols = $dateColumns ?? ['created_at','updated_at','date','date_finish','start_date','end_date','invoice_date'];

    // map master columns -> child outdoor_items columns when the row is an outdoor item
    $childDateRewrite = [
        'date'        => 'start_date',
        'date_finish' => 'end_date',
    ];

    // TABLE display formatter
    $fmt = function (string $col, $val) use ($dateCols) {
        if ($val === null || $val === '' || (is_array($val) && count(array_filter($val, fn($v) => $v !== null && $v !== '')) === 0)) return '—';
        if (is_array($val)) return implode(', ', array_filter($val, fn($v) => $v !== null && $v !== ''));
        if ($col === 'amount') return number_format(is_numeric($val) ? (float)$val : 0, 2);

        if (in_array($col, $dateCols, true)) {
            try {
                $dt = $val instanceof \DateTimeInterface ? $val : Carbon::parse($val);
                return $dt->format('d/m/Y');   // <-- changed from n/j/y
            } catch (\Throwable $e) { return (string)$val; }
        }
        return is_scalar($val) ? (string)$val : '—';
    };


    // RAW value for input fields
    $raw = function (string $col, $val) use ($dateCols) {
        if (in_array($col, $dateCols, true)) {
            try {
                $dt = $val instanceof \DateTimeInterface ? $val : ($val ? Carbon::parse($val) : null);
                return $dt ? $dt->format('Y-m-d') : '';
            } catch (\Throwable $e) { return ''; }
        }
        return is_scalar($val) ? (string)$val : '';
    };

    // what type to render if editable
    $editable = $editable ?? []; // e.g. ['company'=>'text', 'date'=>'date', 'outdoor_size'=>'text']
?>

<div class="overflow-x-auto rounded-xl border border-gray-200">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50">
            <tr>
                <?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $key = is_array($c) ? ($c['key'] ?? '') : $c;
                        $label = is_array($c)
                            ? ($c['label'] ?? Str::headline($key))
                            : ($column_labels[$c] ?? Str::headline($c));

                        // ✅ Define wider columns for specific fields
                        $isWideColumn = in_array($key, [
                            'company_name', 'company', 'product', 'product_category',
                            'kltg_industry', 'kltg_material_cbp', 'kltg_article', 'kltg_remarks',
                            'outdoor_district_council', 'outdoor_coordinates', 'remarks'
                        ]);
                    ?>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700 uppercase tracking-wider <?php echo e($isWideColumn ? 'min-w-48' : 'whitespace-nowrap'); ?>">
                        <?php echo e($label); ?>

                    </th>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-100 bg-white">
            <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50">
                    <?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $colKey      = is_array($c) ? ($c['key'] ?? '') : $c;
                            $isEditable  = array_key_exists($colKey, $editable);
                            $type        = $isEditable ? ($editable[$colKey] ?? 'text') : null;

                            // ✅ FIXED: Use fallback to master_file_id for outdoor rows
                            $rowId       = data_get($row, 'id')
                                ?? data_get($row, 'master_file_id')
                                ?? data_get($row, 'mf_id')
                                ?? null;

                            // detect child row (joined outdoor_items) — your code already carries this id
                            $isChildRow  = isset($row['outdoor_item_id']) || isset($row->outdoor_item_id);

                            // if child row, rewrite master date cols to child date cols
                            $effectiveKey = ($isChildRow && isset($childDateRewrite[$colKey]))
                                ? $childDateRewrite[$colKey]
                                : $colKey;

                            // === Kolom yang DIKIRIM ke server (sendCol) ===
                            // - Child row: tanggal ikut rewrite (start_date/end_date)
                            //             selain itu buang prefix "outdoor_"
                            // - Non-child : kirim effectiveKey apa adanya
                            $sendCol = $effectiveKey;
                            if ($isChildRow) {
                                if (isset($childDateRewrite[$colKey])) {
                                    $sendCol = $childDateRewrite[$colKey]; // start_date / end_date
                                } else {
                                    $sendCol = \Illuminate\Support\Str::startsWith($colKey, 'outdoor_')
                                        ? \Illuminate\Support\Str::after($colKey, 'outdoor_')
                                        : $colKey;
                                }
                            }

                            // fetch value using the effective key
                            $cellValue   = data_get($row, $effectiveKey);

                            // ✅ Check if this is a wide column that needs more space
                            $isWideColumn = in_array($colKey, [
                                'company_name', 'company', 'product', 'product_category',
                                'kltg_industry', 'kltg_material_cbp', 'kltg_article', 'kltg_remarks',
                                'outdoor_district_council', 'outdoor_coordinates', 'remarks'
                            ]);
                        ?>

                        <td class="px-4 py-3 text-gray-800 <?php echo e($isWideColumn ? 'min-w-48' : 'whitespace-nowrap'); ?>">
                            <?php if($isEditable && $rowId): ?>
                                <?php if($type === 'date'): ?>
                                    <input
                                        type="date"
                                        class="mf-edit w-full min-w-32 rounded border border-gray-300 px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                        value="<?php echo e($raw($effectiveKey, $cellValue)); ?>"
                                        data-id="<?php echo e($rowId); ?>"
                                        data-col="<?php echo e($effectiveKey); ?>"
                                        data-send-col="<?php echo e($sendCol); ?>"
                                        data-url="<?php echo e($updateUrl); ?>"
                                        data-extra='<?php echo json_encode($updatePayloadExtra, 15, 512) ?>'
                                        <?php if(isset($row['outdoor_item_id']) || isset($row->outdoor_item_id)): ?>
                                            data-outdoor-item-id="<?php echo e(is_array($row) ? ($row['outdoor_item_id'] ?? '') : ($row->outdoor_item_id ?? '')); ?>"
                                        <?php endif; ?>
                                    />
                                <?php elseif($type === 'number'): ?>
                                    <input
                                        type="number"
                                        step="any"
                                        class="mf-edit w-full min-w-24 rounded border border-gray-300 px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                        value="<?php echo e($raw($effectiveKey, $cellValue)); ?>"
                                        data-id="<?php echo e($rowId); ?>"
                                        data-col="<?php echo e($effectiveKey); ?>"
                                        data-send-col="<?php echo e($sendCol); ?>"
                                        data-url="<?php echo e($updateUrl); ?>"
                                        data-extra='<?php echo json_encode($updatePayloadExtra, 15, 512) ?>'
                                        <?php if(isset($row['outdoor_item_id']) || isset($row->outdoor_item_id)): ?>
                                            data-outdoor-item-id="<?php echo e(is_array($row) ? ($row['outdoor_item_id'] ?? '') : ($row->outdoor_item_id ?? '')); ?>"
                                        <?php endif; ?>
                                    />
                                <?php elseif($type === 'textarea'): ?>
                                    <textarea
                                        class="mf-edit w-full min-w-48 rounded border border-gray-300 px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                        rows="3"
                                        data-id="<?php echo e($rowId); ?>"
                                        data-col="<?php echo e($effectiveKey); ?>"
                                        data-send-col="<?php echo e($sendCol); ?>"
                                        data-url="<?php echo e($updateUrl); ?>"
                                        data-extra='<?php echo json_encode($updatePayloadExtra, 15, 512) ?>'
                                        <?php if(isset($row['outdoor_item_id']) || isset($row->outdoor_item_id)): ?>
                                            data-outdoor-item-id="<?php echo e(is_array($row) ? ($row['outdoor_item_id'] ?? '') : ($row->outdoor_item_id ?? '')); ?>"
                                        <?php endif; ?>
                                    ><?php echo e($raw($effectiveKey, $cellValue)); ?></textarea>
                                <?php else: ?>
                                    
                                    <input
                                        type="text"
                                        class="mf-edit w-full <?php echo e($isWideColumn ? 'min-w-48' : 'min-w-24'); ?> rounded border border-gray-300 px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                        value="<?php echo e($raw($effectiveKey, $cellValue)); ?>"
                                        data-id="<?php echo e($rowId); ?>"
                                        data-col="<?php echo e($effectiveKey); ?>"
                                        data-send-col="<?php echo e($sendCol); ?>"
                                        data-url="<?php echo e($updateUrl); ?>"
                                        data-extra='<?php echo json_encode($updatePayloadExtra, 15, 512) ?>'
                                        <?php if(isset($row['outdoor_item_id']) || isset($row->outdoor_item_id)): ?>
                                            data-outdoor-item-id="<?php echo e(is_array($row) ? ($row['outdoor_item_id'] ?? '') : ($row->outdoor_item_id ?? '')); ?>"
                                        <?php endif; ?>
                                    />
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="<?php echo e($isWideColumn ? 'min-w-48 break-words' : ''); ?>">
                                    <?php echo e($fmt($effectiveKey, $cellValue)); ?>

                                </div>
                            <?php endif; ?>
                        </td>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="<?php echo e(count($columns)); ?>" class="px-4 py-6 text-center text-gray-500">
                        No data found.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if(isset($paginator)): ?>
    <div class="mt-4">
        <?php echo e($paginator->onEachSide(1)->links()); ?>

    </div>
<?php elseif(method_exists($rows, 'links')): ?>
    <div class="mt-4">
        <?php echo e($rows->onEachSide(1)->links()); ?>

    </div>
<?php endif; ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const token = document.querySelector('meta[name="csrf-token"]')?.content || '';

    // small debounce for inputs
    const debounce = (fn, ms=350) => {
        let t; return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), ms); };
    };

    const DEFAULT_UPDATE_URL = <?php echo json_encode($updateUrl, 15, 512) ?>;

    const save = async (el) => {
        const url   = el.dataset.url || DEFAULT_UPDATE_URL;
        const id    = el.dataset.id;
        let   col   = el.dataset.sendCol || el.dataset.col;
        const extra = (() => { try { return JSON.parse(el.dataset.extra || '{}'); } catch { return {}; }})();
        const outdoorItemId = el.dataset.outdoorItemId ? parseInt(el.dataset.outdoorItemId, 10) : null;
        const value = (el.type === 'checkbox') ? (el.checked ? 1 : 0) : el.value;

        if (!url || !id || !col) return;

        // ✅ NEW: explicit guard for missing outdoor_item_id
        if ((extra?.scope === 'outdoor') && !outdoorItemId) {
            alert('Save failed: outdoor_item_id is missing in this row. Make sure your query selects oi.id AS outdoor_item_id and the cell has data-outdoor-item-id.');
            el.classList.add('ring-2','ring-red-200');
            return;
        }

        // Fallback guard: kalau scope outdoor & child row & masih ada prefix 'outdoor_', buang prefix
        if ((extra?.scope === 'outdoor') && outdoorItemId && /^outdoor_/.test(col)) {
            col = col.replace(/^outdoor_/, '');
        }

        // ✅ BUILD PAYLOAD - Log what we're sending for debugging
        const payload = Object.assign(
            {},
            extra,
            { id, column: col, value },
            outdoorItemId ? { outdoor_item_id: outdoorItemId } : {}
        );

        // 🔍 DEBUG: Log the payload being sent
        console.log('🚀 SENDING PAYLOAD:', payload);
        console.log('📍 URL:', url);
        console.log('🎯 Element data attributes:', {
            id: el.dataset.id,
            col: el.dataset.col,
            sendCol: el.dataset.sendCol,
            outdoorItemId: el.dataset.outdoorItemId,
            extra: el.dataset.extra
        });

        el.classList.remove('ring-2','ring-red-200','ring-green-200');
        el.classList.add('opacity-60');

        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(payload)
            });

            const json = await res.json().catch(() => ({}));

            // 🔍 DEBUG: Log the response
            console.log('📥 RESPONSE STATUS:', res.status);
            console.log('📥 RESPONSE DATA:', json);

            // Check if request failed or server returned error
            if (!res.ok) {
                // ✅ Better error handling for 422 validation errors
                if (res.status === 422 && json.errors) {
                    const errorMessages = Object.values(json.errors).flat().join(', ');
                    throw new Error(`Validation failed: ${errorMessages}`);
                } else if (json.message) {
                    throw new Error(json.message);
                } else {
                    throw new Error(`Save failed (${res.status})`);
                }
            }

            if (json && json.ok === false) {
                // Treat "No row changed" as no-op success (nilai sama)
                if (/no row changed/i.test(json.message || '')) {
                    el.classList.add('ring-2','ring-green-200');
                } else {
                    throw new Error(json.message || 'Save failed');
                }
            } else {
                el.classList.add('ring-2','ring-green-200');
            }
        } catch (e) {
            console.error('❌ SAVE ERROR:', e);
            el.classList.add('ring-2','ring-red-200');

            // Show more specific error message
            const errorMsg = e.message || 'Save failed. Check console / server logs.';
            alert(`Save failed: ${errorMsg}`);
        } finally {
            el.classList.remove('opacity-60');
            setTimeout(() => { el.classList.remove('ring-2','ring-green-200','ring-red-200'); }, 900);
        }
    };

    // change + debounced input (good UX for text fields)
    document.body.addEventListener('change', (e) => {
        if (e.target.classList.contains('mf-edit')) save(e.target);
    });
    document.body.addEventListener('input', debounce((e) => {
        if (e.target.classList.contains('mf-edit')) save(e.target);
    }, 600));
});
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH D:\Projects\Laravel\KL_tracker\resources\views\dashboard\master\_table.blade.php ENDPATH**/ ?>