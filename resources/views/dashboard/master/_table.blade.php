{{-- resources/views/dashboard/master/_table.blade.php --}}
@php
    use Illuminate\Support\Str;
    use Carbon\Carbon;

    /** @var array|null $column_labels */
    /** @var array|null $dateColumns */

    // Default date columns (can be overridden from parent include)
    $dateCols = $dateColumns ?? ['created_at', 'updated_at', 'date', 'date_finish', 'start_date', 'end_date', 'invoice_date'];

    // Small formatter helper (safe against bad/missing values)
    $fmt = function (string $col, $val) use ($dateCols) {
        // Normalize "empty"
        if ($val === null || $val === '' || (is_array($val) && count(array_filter($val, fn($v) => $v !== null && $v !== '')) === 0)) {
            return '—';
        }

        // Arrays (e.g., multi locations)
        if (is_array($val)) {
            return implode(', ', array_filter($val, fn($v) => $v !== null && $v !== ''));
        }

        // Amount formatting
        if ($col === 'amount') {
            $num = is_numeric($val) ? (float) $val : 0;
            return number_format($num, 2);
        }

        // Date formatting => n/j/y (e.g., 9/8/25)
        if (in_array($col, $dateCols, true)) {
            try {
                // Accept Carbon, DateTime, or string
                $dt = $val instanceof \DateTimeInterface ? $val : Carbon::parse($val);
                return $dt->format('n/j/y');
            } catch (\Throwable $e) {
                // If parse fails, return raw
                return (string) $val;
            }
        }

        // Everything else
        return is_scalar($val) ? (string) $val : '—';
    };
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
                            $colKey = is_array($c) ? ($c['key'] ?? '') : $c;
                            $cellValue = data_get($row, $colKey);
                        @endphp
                        <td class="px-4 py-3 whitespace-nowrap text-gray-800">
                            {{ $fmt($colKey, $cellValue) }}
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
