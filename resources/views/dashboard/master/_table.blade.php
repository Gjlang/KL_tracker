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

    // TABLE display formatter
    $fmt = function (string $col, $val) use ($dateCols) {
        if ($val === null || $val === '' || (is_array($val) && count(array_filter($val, fn($v) => $v !== null && $v !== '')) === 0)) return '—';
        if (is_array($val)) return implode(', ', array_filter($val, fn($v) => $v !== null && $v !== ''));
        if ($col === 'amount') return number_format(is_numeric($val) ? (float)$val : 0, 2);

        if (in_array($col, $dateCols, true)) {
            try {
                $dt = $val instanceof \DateTimeInterface ? $val : Carbon::parse($val);
                return $dt->format('n/j/y');
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
                    @endphp
                    <th class="px-4 py-3 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">
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
                            $colKey    = is_array($c) ? ($c['key'] ?? '') : $c;
                            $cellValue = data_get($row, $colKey);
                            $isEditable = array_key_exists($colKey, $editable);
                            $type = $isEditable ? ($editable[$colKey] ?? 'text') : null;
                            $rowId = data_get($row, 'id');
                        @endphp

                        <td class="px-4 py-3 whitespace-nowrap text-gray-800">
                            @if($isEditable && $rowId)
                                @if($type === 'date')
                                    <input
                                        type="date"
                                        class="mf-edit w-full rounded border border-gray-300 px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                        value="{{ $raw($colKey, $cellValue) }}"
                                        data-id="{{ $rowId }}"
                                        data-col="{{ $colKey }}"
                                        data-url="{{ $updateUrl }}"
                                        data-extra='@json($updatePayloadExtra)'
                                    />
                                @elseif($type === 'number')
                                    <input
                                        type="number"
                                        step="any"
                                        class="mf-edit w-full rounded border border-gray-300 px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                        value="{{ $raw($colKey, $cellValue) }}"
                                        data-id="{{ $rowId }}"
                                        data-col="{{ $colKey }}"
                                        data-url="{{ $updateUrl }}"
                                        data-extra='@json($updatePayloadExtra)'
                                    />
                                @elseif($type === 'textarea')
                                    <textarea
                                        class="mf-edit w-full rounded border border-gray-300 px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                        rows="2"
                                        data-id="{{ $rowId }}"
                                        data-col="{{ $colKey }}"
                                        data-url="{{ $updateUrl }}"
                                        data-extra='@json($updatePayloadExtra)'
                                    >{{ $raw($colKey, $cellValue) }}</textarea>
                                @else
                                    <input
                                        type="text"
                                        class="mf-edit w-full rounded border border-gray-300 px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                        value="{{ $raw($colKey, $cellValue) }}"
                                        data-id="{{ $rowId }}"
                                        data-col="{{ $colKey }}"
                                        data-url="{{ $updateUrl }}"
                                        data-extra='@json($updatePayloadExtra)'
                                    />
                                @endif
                            @else
                                {{ $fmt($colKey, $cellValue) }}
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

  const debounce = (fn, ms=350) => { let t; return (...a)=>{ clearTimeout(t); t=setTimeout(()=>fn(...a), ms);} };

  const save = async (el) => { /* ... existing autosave code unchanged ... */ };

  // Only bind autosave when NOT in batch mode
  if (!window.mfBatchMode) {
    document.body.addEventListener('change', (e) => {
      if (e.target.classList.contains('mf-edit')) save(e.target);
    });
    document.body.addEventListener('input', debounce((e) => {
      if (e.target.classList.contains('mf-edit')) save(e.target);
    }, 600));
  }
});
</script>
@endpush
