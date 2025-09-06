{{-- resources/views/dashboard/master/_table.blade.php --}}
@php
    use Illuminate\Support\Str;
    use Carbon\Carbon;

    // Small formatter helper (safe against bad/missing values)
    $fmt = function (string $col, $val) {
        if ($val === null || $val === '') return '—';

        if (in_array($col, ['amount'], true)) {
            return number_format((float) $val, 2);
        }

        if (in_array($col, ['date','invoice_date','created_at','updated_at','date_finish'], true)) {
            try {
                return Carbon::parse($val)->format('M d, Y');
            } catch (\Throwable $e) {
                return $val; // leave as-is if it can't be parsed
            }
        }

        return $val;
    };
@endphp

<div class="overflow-x-auto rounded-xl border border-gray-200">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50">
            <tr>
                @foreach($columns as $c)
                    <th class="px-4 py-3 text-left font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">
                        {{ Str::headline($c) }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white">
            @forelse($rows as $row)
                <tr class="hover:bg-gray-50">
                    @foreach($columns as $c)
                        <td class="px-4 py-3 whitespace-nowrap text-gray-800">
                            {{ $fmt($c, data_get($row, $c)) }}
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

<div class="mt-4">
    {{ $rows->onEachSide(1)->links() }}
</div>
