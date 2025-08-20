<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            📘 KLTG Matrix Vertical View for {{ $file->company }}
        </h2>
    </x-slot>

    <div class="py-10 px-6">
        <div class="bg-white rounded shadow p-6 w-full max-w-4xl mx-auto">
            <table class="w-full border text-sm text-left">
                <thead>
                    <tr>
                        <th class="px-4 py-2 bg-gray-100">Period</th>
                        <th class="px-4 py-2 bg-gray-100 text-center">{{ $file->company }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($months as $month)
                        @foreach($types as $type)
                            @php
                                $value = optional($grouped[$month] ?? collect())->firstWhere('type', $type)?->status;
                                $bg = str_contains(strtolower($value), 'tbd') ? 'bg-red-200' :
                                      (str_contains(strtolower($value), 'x') ? 'bg-green-200' :
                                      (str_contains(strtolower($value), 'free') ? 'bg-red-100' : ''));
                            @endphp
                            <tr class="border-t">
                                <td class="px-4 py-2 font-medium text-gray-700">{{ $month }} - {{ $type }}</td>
                                <td class="px-4 py-2 text-center {{ $bg }}">{{ $value ?? '-' }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>

            <div class="mt-6">
                <a href="{{ route('dashboard') }}" class="text-indigo-600 hover:underline">← Back to Dashboard</a>
            </div>
        </div>
    </div>
</x-app-layout>
