@extends('layouts.app')

@section('content')
<div class="flex items-center justify-between mb-4">
  <h1 class="text-lg font-semibold">Completed Whiteboards</h1>
  <a href="{{ route('outdoor.whiteboard.index') }}"
     class="text-xs px-3 py-1.5 rounded-full border border-neutral-300 hover:bg-neutral-100">
     Back to Active
  </a>
</div>

<table class="w-full text-sm border-separate border-spacing-0">
  <thead>
    <tr class="bg-neutral-50 text-neutral-700">
      <th class="px-3 py-2 text-left">No.</th>
      <th class="px-3 py-2 text-left">Created</th>
      <th class="px-3 py-2 text-left">INV Number</th>
      <th class="px-3 py-2 text-left">Purchase Order</th>
      <th class="px-3 py-2 text-left">Product</th>
      <th class="px-3 py-2 text-left">Company</th>
      <th class="px-3 py-2 text-left">Location</th>
      <th class="px-3 py-2 text-left">Installation</th>
      <th class="px-3 py-2 text-left">Dismantle</th>
      <th class="px-3 py-2 text-left">Supplier</th>
      <th class="px-3 py-2 text-left">Storage</th>
      <th class="px-3 py-2 text-left">Completed At</th>
      <th class="px-3 py-2 text-left">Actions</th>
    </tr>
  </thead>

  <tbody>
    @forelse ($whiteboards as $index => $wb)
      <tr class="border-b">
        <td class="px-3 py-2">{{ $index + $whiteboards->firstItem() }}</td>
        <td class="px-3 py-2">{{ optional($wb->created_at)->format('Y-m-d') }}</td>
        <td class="px-3 py-2">{{ $wb->inv_number ?? '-' }}</td>
        <td class="px-3 py-2">{{ $wb->purchase_order ?? '-' }}</td>
        <td class="px-3 py-2">{{ $wb->product ?? '-' }}</td>
        <td class="px-3 py-2">{{ $wb->company ?? '-' }}</td>
        <td class="px-3 py-2">{{ $wb->location ?? '-' }}</td>
        <td class="px-3 py-2">{{ $wb->installation ?? '-' }}</td>
        <td class="px-3 py-2">{{ $wb->dismantle ?? '-' }}</td>
        <td class="px-3 py-2">{{ $wb->supplier_text ?? '-' }}</td>
        <td class="px-3 py-2">{{ $wb->storage_text ?? '-' }}</td>
        <td class="px-3 py-2">{{ optional($wb->completed_at)->format('Y-m-d H:i') }}</td>

        <td class="px-3 py-2 text-center">
          {{-- example delete action --}}
          <form method="POST" action="{{ route('outdoor.whiteboard.destroy', $wb->id) }}"
                onsubmit="return confirm('Delete this completed record?')">
            @csrf
            @method('DELETE')
            <button class="text-xs px-3 py-1.5 rounded-full bg-red-600 text-white hover:bg-red-700">
              Delete
            </button>
          </form>
        </td>
      </tr>
    @empty
      <tr>
        <td colspan="13" class="px-3 py-4 text-center text-neutral-500">
          No completed whiteboards yet.
        </td>
      </tr>
    @endforelse
  </tbody>
</table>

<div class="mt-4">
  {{ $whiteboards->links() }}
</div>
@endsection
