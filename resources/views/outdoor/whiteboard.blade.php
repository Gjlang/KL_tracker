@extends('layouts.app')

@section('content')
<style>
/* Custom styles for classic elegant design */
.hairline { border-color: #EAEAEA; }
.ink { color: #1C1E26; }
.paper { background-color: #F7F7F9; }
.surface { background-color: #FFFFFF; }
.primary-btn {
  @apply bg-[#22255b] text-white hover:opacity-90 focus:ring-2 focus:ring-[#4bbbed] rounded-full px-4 py-2 transition-all duration-150;
}
.ghost-btn {
  @apply border border-neutral-300 text-neutral-700 hover:bg-neutral-50 rounded-full px-4 py-2 transition-all duration-150;
}
.elegant-input {
  @apply h-11 rounded-xl border-neutral-300 focus:ring-2 focus:ring-[#4bbbed] focus:border-transparent transition-all duration-150;
}
.header-label {
  @apply tracking-[0.06em] uppercase text-[11px] text-neutral-600 font-medium;
}
.elegant-card {
  @apply bg-white rounded-2xl border border-neutral-200/70 shadow-sm;
}
.stacked-input {
  @apply border rounded-lg px-3 py-2 w-44 elegant-input text-sm;
}
.table-cell {
  @apply px-4 py-3 text-sm;
}
</style>

<div class="min-h-screen paper">
  <div class="max-w-[1400px] mx-auto">

    <!-- Top Navigation Bar -->
    <header class="surface border-b hairline">
      <div class="px-8 py-6">
        <div class="flex items-center justify-between">
          <!-- Left: Title Section -->
          <div>
            <h1 class="font-serif text-3xl font-medium ink">OUTDOOR Whiteboard</h1>
            <p class="text-neutral-500 text-sm mt-1 font-sans">Project tracking & management dashboard</p>
          </div>
        </div>
      </div>
    </header>

    <!-- Search Panel -->
    <div class="px-8 py-6">
      <div class="elegant-card p-6">
        <form method="get" class="flex items-center gap-4">
          <div class="flex-1">
            <label class="header-label block mb-2">Search Projects</label>
            <input
              type="text"
              name="q"
              value="{{ $search }}"
              placeholder="Search by company, product, or location..."
              class="elegant-input w-full font-sans"
            >
          </div>
          <div class="pt-7">
            <button type="submit" class="primary-btn">
              <span class="text-sm font-medium">Search</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Status Messages -->
    @if (session('status'))
      <div class="px-8 mb-6">
        <div class="elegant-card border-l-4 border-l-green-400 p-4">
          <div class="text-green-700 text-sm font-medium">{{ session('status') }}</div>
        </div>
      </div>
    @endif

    <!-- Main Data Table -->
    <div class="px-8 pb-8">
      <div class="elegant-card overflow-hidden">
        <!-- Table Header -->
        <div class="surface border-b hairline px-6 py-4">
          <h2 class="font-serif text-xl ink">Project Overview</h2>
          <p class="text-neutral-500 text-sm mt-1">Track and manage outdoor advertising projects</p>
        </div>

        <!-- Table Content -->
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="surface border-b hairline">
              <tr>
                <th class="table-cell text-left">
                  <span class="header-label">Created</span>
                </th>
                <th class="table-cell text-left">
                  <span class="header-label">Company</span>
                </th>
                <th class="table-cell text-left">
                  <span class="header-label">Product</span>
                </th>
                <th class="table-cell text-left">
                  <span class="header-label">Location</span>
                </th>
                <th class="table-cell text-left">
                  <span class="header-label">Duration</span>
                </th>
                <th class="table-cell text-left">
                  <span class="header-label">Purchase Order</span>
                </th>
                <th class="table-cell text-left">
                  <span class="header-label">Client</span>
                </th>
                <th class="table-cell text-left">
                  <span class="header-label">Supplier</span>
                </th>
                <th class="table-cell text-left">
                  <span class="header-label">Storage</span>
                </th>
                <th class="table-cell text-left">
                  <span class="header-label">Notes</span>
                </th>
                <th class="table-cell text-center">
                  <span class="header-label">Actions</span>
                </th>
              </tr>
            </thead>
            <tbody class="divide-y hairline">
              @foreach ($masterFiles as $mf)
                @foreach ($mf->outdoorItems as $item)
                @php
                  $wb = $existing[$mf->id] ?? null;
                  // Get site from outdoor_items
                  $site = $mf->outdoorItems->first()?->site;
                @endphp

                <tr class="hover:bg-neutral-50/50 transition-colors duration-150">
                  <form action="{{ route('outdoor.whiteboard.upsert') }}" method="post">
                    @csrf
                    <input type="hidden" name="master_file_id" value="{{ $mf->id }}">

                    <!-- Created Date -->
                    <td class="table-cell">
                      <div class="ink font-medium">{{ $mf->created_at?->format('M d, Y') }}</div>
                      <div class="text-neutral-400 text-xs">{{ $mf->created_at?->format('D') }}</div>
                    </td>

                    <!-- Company -->
                    <td class="table-cell">
                      <div class="ink font-medium truncate max-w-[120px]" title="{{ $mf->company }}">
                        {{ $mf->company }}
                      </div>
                    </td>

                    <!-- Product -->
                    <td class="table-cell">
                      <div class="ink truncate max-w-[120px]" title="{{ $mf->product }}">
                        {{ $mf->product }}
                      </div>
                    </td>

                    <!-- Location -->
                    <td class="table-cell">
                      <div class="ink truncate max-w-[120px]" title="{{ $site }}">
                        {{ $site }}
                      </div>
                    </td>

                    <!-- Duration -->
                    <td class="table-cell">
                      <div class="space-y-1">
                        <div class="text-xs text-neutral-500">Start: {{ $mf->start_date?->format('M d') }}</div>
                        <div class="text-xs text-neutral-500">End: {{ $mf->end_date?->format('M d') }}</div>
                      </div>
                    </td>

                    <!-- PO (Stacked) -->
                    <td class="table-cell">
                      <div class="space-y-2">
                        <input
                          type="text"
                          name="po_text"
                          class="stacked-input"
                          placeholder="PO note..."
                          value="{{ old('po_text', $wb?->po_text) }}"
                        >
                        <input
                          type="date"
                          name="po_date"
                          class="stacked-input"
                          value="{{ old('po_date', $wb?->po_date?->format('Y-m-d')) }}"
                        >
                      </div>
                    </td>

                    <!-- Client (Stacked) -->
                    <td class="table-cell">
                      <div class="space-y-2">
                        <input
                          type="text"
                          name="client_text"
                          class="stacked-input"
                          placeholder="Client note..."
                          value="{{ old('client_text', $wb?->client_text) }}"
                        >
                        <input
                          type="date"
                          name="client_date"
                          class="stacked-input"
                          value="{{ old('client_date', $wb?->client_date?->format('Y-m-d')) }}"
                        >
                      </div>
                    </td>

                    <!-- Supplier (Stacked) -->
                    <td class="table-cell">
                      <div class="space-y-2">
                        <input
                          type="text"
                          name="supplier_text"
                          class="stacked-input"
                          placeholder="Supplier note..."
                          value="{{ old('supplier_text', $wb?->supplier_text) }}"
                        >
                        <input
                          type="date"
                          name="supplier_date"
                          class="stacked-input"
                          value="{{ old('supplier_date', $wb?->supplier_date?->format('Y-m-d')) }}"
                        >
                      </div>
                    </td>

                    <!-- Storage (Stacked) -->
                    <td class="table-cell">
                      <div class="space-y-2">
                        <input
                          type="text"
                          name="storage_text"
                          class="stacked-input"
                          placeholder="Storage note..."
                          value="{{ old('storage_text', $wb?->storage_text) }}"
                        >
                        <input
                          type="date"
                          name="storage_date"
                          class="stacked-input"
                          value="{{ old('storage_date', $wb?->storage_date?->format('Y-m-d')) }}"
                        >
                      </div>
                    </td>

                    <!-- Notes -->
                    <td class="table-cell">
                      <input
                        type="text"
                        name="notes"
                        class="border rounded-lg px-3 py-2 w-56 elegant-input text-sm"
                        placeholder="Additional notes..."
                        value="{{ old('notes', $wb?->notes) }}"
                      >
                    </td>

                    <!-- Save Button -->
                    <td class="table-cell text-center">
                      <button type="submit" class="primary-btn text-xs px-3 py-1.5">
                        Save
                      </button>
                    </td>
                  </form>
                </tr>
                @endforeach
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
// Optional: Add subtle interactions
document.addEventListener('DOMContentLoaded', function() {
  // Focus ring improvements
  const inputs = document.querySelectorAll('input, button');
  inputs.forEach(input => {
    input.addEventListener('focus', function() {
      this.style.outline = '2px solid #4bbbed';
      this.style.outlineOffset = '2px';
    });
    input.addEventListener('blur', function() {
      this.style.outline = 'none';
    });
  });

  // Form validation feedback
  const forms = document.querySelectorAll('form');
  forms.forEach(form => {
    form.addEventListener('submit', function(e) {
      const button = this.querySelector('button[type="submit"]');
      if (button) {
        button.textContent = 'Saving...';
        button.disabled = true;
        setTimeout(() => {
          button.textContent = 'Save';
          button.disabled = false;
        }, 2000);
      }
    });
  });
});
</script>
@endsection
