<form method="GET" action="{{ route('information.booth.index') }}" class="bg-white rounded-2xl border border-neutral-200/70 shadow-sm">
  <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
    <div>
      <label class="block text-xs font-medium text-neutral-600 mb-1">Search Client</label>
      <input type="text" name="q" value="{{ request('q') }}" placeholder="Enter client name..."
             class="w-full rounded-xl border-neutral-300 focus:border-neutral-400 focus:ring-0">
    </div>
    <div>
      <label class="block text-xs font-medium text-neutral-600 mb-1">Status</label>
      <select name="status" class="w-full rounded-xl border-neutral-300 focus:border-neutral-400 focus:ring-0">
        <option value="">All Status</option>
        @foreach(['Pending','In Progress','Completed','Cancelled'] as $s)
          <option value="{{ $s }}" @selected(request('status')===$s)>{{ $s }}</option>
        @endforeach
      </select>
    </div>
    <div class="flex items-end">
      <button class="rounded-xl border border-neutral-300 bg-white px-4 py-2 text-sm font-medium text-neutral-800 hover:bg-neutral-50 shadow-sm">
        Filter
      </button>
    </div>
  </div>
</form>
