{{-- resources/views/dashboard/master/_table.blade.php --}}
@php
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
@endphp

<div class="overflow-x-auto rounded-xl border border-gray-200">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50">
            <tr>
                @foreach($columns as $c)
                    @php
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
                    @endphp
                    <th class="px-4 py-3 text-left font-semibold text-gray-700 uppercase tracking-wider {{ $isWideColumn ? 'min-w-48' : 'whitespace-nowrap' }}">
                        {{ $label }}
                    </th>
                @endforeach
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-100 bg-white">
            @forelse($rows as $row)
                <tr class="hover:bg-gray-50">
                    @foreach($columns as $c)
                        @php
                            $colKey      = is_array($c) ? ($c['key'] ?? '') : $c;
                            $isEditable  = array_key_exists($colKey, $editable);
                            $type        = $isEditable ? ($editable[$colKey] ?? 'text') : null;
                            $rowId       = data_get($row, 'id');

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
                        @endphp

                        <td class="px-4 py-3 text-gray-800 {{ $isWideColumn ? 'min-w-48' : 'whitespace-nowrap' }}">
                            @if($isEditable && $rowId)
                                @if($type === 'date')
                                    <input
                                        type="date"
                                        class="mf-edit w-full min-w-32 rounded border border-gray-300 px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                        value="{{ $raw($effectiveKey, $cellValue) }}"
                                        data-id="{{ $rowId }}"
                                        data-col="{{ $effectiveKey }}"
                                        data-send-col="{{ $sendCol }}"
                                        data-url="{{ $updateUrl }}"
                                        data-extra='@json($updatePayloadExtra)'
                                        @if(isset($row['outdoor_item_id']) || isset($row->outdoor_item_id))
                                            data-outdoor-item-id="{{ is_array($row) ? ($row['outdoor_item_id'] ?? '') : ($row->outdoor_item_id ?? '') }}"
                                        @endif
                                    />
                                @elseif($type === 'number')
                                    <input
                                        type="number"
                                        step="any"
                                        class="mf-edit w-full min-w-24 rounded border border-gray-300 px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                        value="{{ $raw($effectiveKey, $cellValue) }}"
                                        data-id="{{ $rowId }}"
                                        data-col="{{ $effectiveKey }}"
                                        data-send-col="{{ $sendCol }}"
                                        data-url="{{ $updateUrl }}"
                                        data-extra='@json($updatePayloadExtra)'
                                        @if(isset($row['outdoor_item_id']) || isset($row->outdoor_item_id))
                                            data-outdoor-item-id="{{ is_array($row) ? ($row['outdoor_item_id'] ?? '') : ($row->outdoor_item_id ?? '') }}"
                                        @endif
                                    />
                                @elseif($type === 'textarea')
                                    <textarea
                                        class="mf-edit w-full min-w-48 rounded border border-gray-300 px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                        rows="3"
                                        data-id="{{ $rowId }}"
                                        data-col="{{ $effectiveKey }}"
                                        data-send-col="{{ $sendCol }}"
                                        data-url="{{ $updateUrl }}"
                                        data-extra='@json($updatePayloadExtra)'
                                        @if(isset($row['outdoor_item_id']) || isset($row->outdoor_item_id))
                                            data-outdoor-item-id="{{ is_array($row) ? ($row['outdoor_item_id'] ?? '') : ($row->outdoor_item_id ?? '') }}"
                                        @endif
                                    >{{ $raw($effectiveKey, $cellValue) }}</textarea>
                                @else
                                    {{-- ✅ Different width for different types of text inputs --}}
                                    <input
                                        type="text"
                                        class="mf-edit w-full {{ $isWideColumn ? 'min-w-48' : 'min-w-24' }} rounded border border-gray-300 px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                        value="{{ $raw($effectiveKey, $cellValue) }}"
                                        data-id="{{ $rowId }}"
                                        data-col="{{ $effectiveKey }}"
                                        data-send-col="{{ $sendCol }}"
                                        data-url="{{ $updateUrl }}"
                                        data-extra='@json($updatePayloadExtra)'
                                        @if(isset($row['outdoor_item_id']) || isset($row->outdoor_item_id))
                                            data-outdoor-item-id="{{ is_array($row) ? ($row['outdoor_item_id'] ?? '') : ($row->outdoor_item_id ?? '') }}"
                                        @endif
                                    />
                                @endif
                            @else
                                <div class="{{ $isWideColumn ? 'min-w-48 break-words' : '' }}">
                                    {{ $fmt($effectiveKey, $cellValue) }}
                                </div>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($columns) }}" class="px-4 py-6 text-center text-gray-500">
                        No data found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if(isset($paginator))
    <div class="mt-4">
        {{ $paginator->onEachSide(1)->links() }}
    </div>
@elseif(method_exists($rows, 'links'))
    <div class="mt-4">
        {{ $rows->onEachSide(1)->links() }}
    </div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const token = document.querySelector('meta[name="csrf-token"]')?.content || '';

    // small debounce for inputs
    const debounce = (fn, ms=350) => {
        let t; return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), ms); };
    };

    const DEFAULT_UPDATE_URL = @json($updateUrl);

    const save = async (el) => {
        const url   = el.dataset.url || DEFAULT_UPDATE_URL;
        const id    = el.dataset.id;
        let   col   = el.dataset.sendCol || el.dataset.col; // ✅ pakai sendCol kalau ada
        const extra = (() => { try { return JSON.parse(el.dataset.extra || '{}'); } catch { return {}; }})();
        const outdoorItemId = el.dataset.outdoorItemId ? parseInt(el.dataset.outdoorItemId, 10) : null;
        const value = (el.type === 'checkbox') ? (el.checked ? 1 : 0) : el.value;

        if (!url || !id || !col) return;

        // Fallback guard: kalau scope outdoor & child row & masih ada prefix 'outdoor_', buang prefix
        if ((extra?.scope === 'outdoor') && outdoorItemId && /^outdoor_/.test(col)) {
            col = col.replace(/^outdoor_/, '');
        }

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
                body: JSON.stringify(Object.assign(
                    {},
                    extra,
                    { id, column: col, value },
                    outdoorItemId ? { outdoor_item_id: outdoorItemId } : {}
                ))
            });

            const json = await res.json().catch(() => ({}));

            // Check if request failed or server returned error
            if (!res.ok) throw new Error(`Save failed (${res.status})`);
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
            console.error(e);
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
@endpush
