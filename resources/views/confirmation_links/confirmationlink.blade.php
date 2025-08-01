@extends('layouts.app')

@section('content')
<div class="p-6">
    <h2 class="text-2xl font-bold mb-4">Master File List</h2>

    <form method="GET" class="mb-4 flex gap-4">
        <input type="text" name="search" placeholder="Search by company or product" class="border px-3 py-2 rounded w-64" value="{{ request('search') }}">
        <select name="month" class="border px-3 py-2 rounded">
            <option value="">All Months</option>
            @foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $m)
                <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>{{ $m }}</option>
            @endforeach
        </select>
        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Filter</button>
    </form>

    <table class="min-w-full bg-white border">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 border">Date</th>
                <th class="px-4 py-2 border">Company</th>
                <th class="px-4 py-2 border">Product</th>
                <th class="px-4 py-2 border">Status</th>
                <th class="px-4 py-2 border">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($masterFiles as $file)
                <tr class="border-t hover:bg-gray-50">
                    <td class="px-4 py-2 border">{{ $file->date }}</td>
                    <td class="px-4 py-2 border">{{ $file->company }}</td>
                    <td class="px-4 py-2 border">{{ $file->product }}</td>
                    <td class="px-4 py-2 border">{{ ucfirst($file->status) }}</td>
                    <td class="px-4 py-2 border">
                        <a href="{{ route('masterfile.show', $file->id) }}" class="text-blue-600 hover:underline">View</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        {{ $masterFiles->withQueryString()->links() }}
    </div>
</div>
@endsection
